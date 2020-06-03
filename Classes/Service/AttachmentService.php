<?php

declare(strict_types=1);

namespace In2code\Groupmailer\Service;

use In2code\Groupmailer\Domain\Model\Mailing;
use In2code\Groupmailer\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Resource\Exception\ExistingTargetFolderException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException;
use TYPO3\CMS\Core\Resource\Exception\InsufficientFolderWritePermissionsException;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Resource\FileRepository;
use TYPO3\CMS\Core\Resource\Folder;
use TYPO3\CMS\Core\Resource\FolderInterface;
use TYPO3\CMS\Core\Resource\ResourceFactory;
use TYPO3\CMS\Core\Resource\ResourceStorage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class AttachmentService extends AbstractService
{
    const ATTACHMENT_FOLDER = 'tx_groupmailer';

    /**
     * @var ResourceFactory
     */
    protected $resourceFactory;

    /**
     * @var ResourceStorage
     */
    protected $storage;

    /**
     * @var FileRepository
     */
    protected $fileRepository;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(ResourceFactory $resourceFactory, FileRepository $fileRepository)
    {
        $this->resourceFactory = $resourceFactory;
        $this->storage = $resourceFactory->getDefaultStorage();
        $this->fileRepository = $fileRepository;
        $this->queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(Mailing::TABLE);
    }

    /**
     * @param Mailing $mailing
     * @param array $files
     *
     * @return bool
     *
     * @throws InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     */
    public function addAttachments(Mailing $mailing, array $files)
    {
        $sysFiles = $this->uploadFiles($files, $mailing->getUid());

        return $this->createFileReferences($sysFiles, $mailing->getUid());
    }

    /**
     * @param array $files
     * @param int $mailingUid
     * @return bool
     */
    public function createFileReferences(array $files, int $mailingUid): bool
    {
        $data = [];

        foreach ($files as $key => $file) {
            if ($file instanceof FileInterface) {
                $newId = 'NEW' . $mailingUid . $key;

                $data['sys_file_reference'][$newId] = [
                    'table_local' => 'sys_file',
                    'uid_local' => $file->getUid(),
                    'tablenames' => Mailing::TABLE,
                    'uid_foreign' => $mailingUid,
                    'fieldname' => 'attachments',
                    'pid' => ConfigurationUtility::getStoragePid()
                ];
            }
        }

        /** @var DataHandler $dataHandler */
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        $this->queryBuilder
            ->update(Mailing::TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->queryBuilder->createNamedParameter($mailingUid))
            )
            ->set('attachments', count($files))->execute();

        if (count($dataHandler->errorLog) === 0) {
            return true;
        } else {
            $this->logger->critical(
                'File References for Attachments could not not be created',
                ['dataHandlerErrorLog' => $dataHandler->errorLog]
            );
            return false;
        }
    }

    /**
     * @param array $files
     * @param int $mailingUid
     *
     * @return array
     *
     * @throws InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     */
    protected function uploadFiles(array $files, int $mailingUid): array
    {
        $sysFiles = [];
        $attachmentFolder = $this->getAttachmentFolder();
        if (!is_null($attachmentFolder)) {
            $mailingFolder = $attachmentFolder->createFolder($mailingUid);

            foreach ($files as $file) {
                $sysFiles[] = $this->storage->addFile($file['tmp_name'], $mailingFolder, $file['name']);
            }
        }

        return $sysFiles;
    }

    /**
     * @param int $mailingUid
     * @return ObjectStorage
     * @throws InsufficientFolderAccessPermissionsException
     */
    public function getAttachmentsForMailing(int $mailingUid): ObjectStorage
    {
        $fileStorage = new ObjectStorage();
        $attachmentFolder = $this->getAttachmentFolder();

        if (!is_null($attachmentFolder)) {
            $mailingFolder = $attachmentFolder->getSubfolder($mailingUid);
            $files = $mailingFolder->getFiles();

            foreach ($files as $file) {
                $fileStorage->attach($file);
            }
        }

        return $fileStorage;
    }

    /**
     * @return bool
     */
    protected function existAttachmentFolder(): bool
    {
        return is_dir(Environment::getPublicPath() . '/fileadmin/' . self::ATTACHMENT_FOLDER);
    }

    /**
     * @return Folder
     * @throws InsufficientFolderAccessPermissionsException
     * @throws \Exception
     */
    protected function getAttachmentFolder(): ?Folder
    {
        if (!$this->existAttachmentFolder()) {
            $folder = $this->createAttachmentFolder();
            $this->createAttachmentFolderHtaccess();
        } else {
            $folder = $this->storage->getFolder(self::ATTACHMENT_FOLDER);
            if (!$this->existAttachmentFolderHtaccess()) {
                $this->createAttachmentFolderHtaccess();
            }
        }

        if ($folder instanceof FolderInterface) {
            return $folder;
        }

        return null;
    }

    /**
     * @return bool
     */
    protected function existAttachmentFolderHtaccess(): bool
    {
        return is_file(Environment::getPublicPath() . '/fileadmin/' . self::ATTACHMENT_FOLDER . '/.htaccess');
    }

    protected function createAttachmentFolderHtaccess()
    {
        $templateFilePath =
            Environment::getExtensionsPath(
            ) . '/groupmailer/Resources/Private/FolderStructureTemplateFiles/fileadmin-htaccess';
        $targetFile = Environment::getPublicPath() . '/fileadmin/' . self::ATTACHMENT_FOLDER . '/.htaccess';

        if (!copy($templateFilePath, $targetFile)) {
            $this->logger->error(
                'The fileadmin/' . self::ATTACHMENT_FOLDER . '/.htaccess file could not copied from template file',
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
        }
    }

    /**
     * @return Folder|null
     * @throws \Exception
     */
    protected function createAttachmentFolder(): ?Folder
    {
        try {
            return $this->storage->createFolder(self::ATTACHMENT_FOLDER);
        } catch (ExistingTargetFolderException $e) {
        } catch (InsufficientFolderWritePermissionsException $e) {
        } catch (InsufficientFolderAccessPermissionsException $e) {
        }

        return null;
    }
}
