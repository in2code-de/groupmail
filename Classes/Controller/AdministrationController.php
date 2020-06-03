<?php

declare(strict_types=1);

namespace In2code\Groupmailer\Controller;

use In2code\Groupmailer\Context\Context;
use In2code\Groupmailer\Domain\Model\Mailing;
use In2code\Groupmailer\Domain\Repository\MailingRepository;
use In2code\Groupmailer\Service\AttachmentService;
use In2code\Groupmailer\Utility\ConfigurationUtility;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AdministrationController extends ActionController
{
    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var BackendUserGroupRepository
     */
    protected $backendUserGroupRepository;

    /**
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    /**
     * @var AttachmentService
     */
    protected $attachmentService;

    public function __construct(
        MailingRepository $mailingRepository,
        BackendUserGroupRepository $backendUserGroupRepository,
        FrontendUserGroupRepository $frontendUserGroupRepository,
        AttachmentService $attachmentService

    ) {
        $this->mailingRepository = $mailingRepository;
        $this->backendUserGroupRepository = $backendUserGroupRepository;
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;
        $this->attachmentService = $attachmentService;
    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        $this->createMenu();
    }

    public function indexAction()
    {
        $this->view->assignMultiple(
            [
                'activeMailings' => $this->mailingRepository->findActiveMailings(),
                'lockedMailings' => $this->mailingRepository->findLockedMailings()
            ]
        );
    }

    public function newAction()
    {
        /** @var Typo3QuerySettings $defaultQuerySettings */
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->frontendUserGroupRepository->setDefaultQuerySettings($defaultQuerySettings);
        
        $this->view->assignMultiple(
            [
                'beGroups' => $this->backendUserGroupRepository->findAll(),
                'feGroups' => $this->frontendUserGroupRepository->findAll(),
                'mailing' => new Mailing(),
                'senderData' => $this->getSenderData(),
                'backendLanguage' => $GLOBALS['BE_USER']->uc['lang']
            ]
        );
    }

    /**
     * @param Mailing $mailing
     * @param array $files
     * @throws \TYPO3\CMS\Core\Resource\Exception\ExistingTargetFileNameException
     * @throws \TYPO3\CMS\Core\Resource\Exception\InsufficientFolderAccessPermissionsException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function createAction(Mailing $mailing, array $files)
    {
        $this->removeContextForeignGroups($mailing);
        $this->setFallbackSenderData($mailing);

        $this->mailingRepository->add($mailing);

        if ($files[0]['error'] !== UPLOAD_ERR_NO_FILE) {
            $attachmentsCreated = $this->attachmentService->addAttachments($mailing, $files);
            if (!$attachmentsCreated) {
                $this->addFlashMessage(
                    'while uploading the attachments. Please visit the log for further information',
                    'an error occurred',
                    FlashMessage::ERROR
                );
            }
        }

        $this->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:groupmailer/Resources/Private/Language/locallang_db.xlf:message.mailing-created.message'
            ),
            LocalizationUtility::translate(
                'LLL:EXT:groupmailer/Resources/Private/Language/locallang_db.xlf:message.mailing-created.title'
            ),
            FlashMessage::OK
        );

        $this->redirect('index');
    }

    /**
     * @return array
     */
    protected function getSenderData(): array
    {
        $beUser = $GLOBALS['BE_USER']->user;
        $senderData = [];

        if (!empty($beUser['realName'])) {
            $senderData['senderName'] = $beUser['realName'];
        } else {
            $senderData['senderName'] = ConfigurationUtility::getSenderNameFallback();
        }

        if (!empty($beUser['email'])) {
            $senderData['senderMail'] = $beUser['email'];
        } else {
            $senderData['senderMail'] = ConfigurationUtility::getSenderEmailFallback();
        }

        return $senderData;
    }

    /**
     * @param Mailing $mailing
     */
    protected function setFallbackSenderData(Mailing $mailing)
    {
        if (empty($mailing->getSenderName())) {
            $mailing->setSenderName($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']);
        }

        if (empty($mailing->getSenderMail())) {
            $mailing->setSenderMail($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']);
        }
    }

    /**
     * @param Mailing $mailing
     */
    protected function removeContextForeignGroups(Mailing $mailing)
    {
        if ($mailing->getContext() === Context::FRONTEND) {
            $mailing->setBeGroups(new ObjectStorage());
        }

        if ($mailing->getContext() === Context::BACKEND) {
            $mailing->setFeGroups(new ObjectStorage());
        }
    }

    /**
     * Create menu
     *
     */
    protected function createMenu()
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('groupmailer');

        $actions = [
            ['action' => 'index', 'label' => 'administration'],
            ['action' => 'new', 'label' => 'new']
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle(
                    LocalizationUtility::translate(
                        'LLL:EXT:groupmailer/Resources/Private/Language/locallang_db.xlf:module.' . $action['label']
                    )
                )
                ->setHref($uriBuilder->reset()->uriFor($action['action'], [], 'Administration'))
                ->setActive($this->request->getControllerActionName() === $action['action']);
            $menu->addMenuItem($item);
        }

        if ($menu instanceof Menu) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }
}
