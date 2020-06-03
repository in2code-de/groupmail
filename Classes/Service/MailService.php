<?php

declare(strict_types=1);

namespace In2code\Groupmailer\Service;

use In2code\Groupmailer\Context\Context;
use In2code\Groupmailer\Domain\Model\Mailing;
use In2code\Groupmailer\Domain\Model\MailQueue;
use In2code\Groupmailer\Domain\Repository\MailingRepository;
use In2code\Groupmailer\Utility\ConfigurationUtility;
use In2code\Groupmailer\Workflow\Workflow;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Resource\FileInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class MailService extends AbstractService
{
    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var AttachmentService
     */
    protected $attachmentService;

    /**
     * MailService constructor.
     *
     * @param MailingRepository $mailingRepository
     * @param AttachmentService $attachmentService
     */
    public function __construct(MailingRepository $mailingRepository, AttachmentService $attachmentService)
    {
        $this->mailingRepository = $mailingRepository;
        $this->queryBuilder =
            GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(Mailing::TABLE);
        $this->attachmentService = $attachmentService;
    }

    /**
     * @param array $groups
     * @param string $subject
     * @param string $bodytext
     * @param string $senderEmail
     * @param string $senderName
     * @param string $mailFormat valid options are FluidEmail::FORMAT_BOTH,  FluidEmail::FORMAT_HTML or
     *     FluidEmail::FORMAT_PLAIN
     * @param string $context valid options are Context::FRONTEND or Context::BACKEND
     * @param int $workflowState valid options are Workflow::STATE_DRAFT, Workflow::STATE_REVIEW,
     *     Workflow::STATE_APPROVED or Workflow::STATE_REJECTED
     * @param FileInterface[] $attachments
     *
     * @api
     */
    public function generateMailing(
        array $groups,
        string $subject,
        string $bodytext,
        string $senderEmail = '',
        string $senderName = '',
        string $mailFormat = FluidEmail::FORMAT_BOTH,
        string $context = Context::FRONTEND,
        int $workflowState = Workflow::STATE_DRAFT,
        array $attachments = []
    ) {
        if (empty($senderName)) {
            $senderName = $this->getSenderNameFallback();
        }

        if (empty($senderEmail)) {
            $senderEmail = $this->getSenderEmailFallback();
        }

        if (!$this->validateArguments(
            $groups,
            $senderEmail,
            $mailFormat,
            $context,
            $workflowState,
            $attachments
        )) {
            throw new \InvalidArgumentException(
                'The argument validation of generateMailing failed. For more Information take a look in the logs',
                1588836726
            );
        }

        $groupStorage = new ObjectStorage();
        foreach ($groups as $group) {
            $groupStorage->attach($group);
        }

        $mailingUid = $this->mailingRepository->createRecord(
            [
                $context . 'Groups' => $groupStorage,
                'subject' => $subject,
                'bodytext' => $bodytext,
                'senderMail' => $senderEmail,
                'senderName' => $senderName,
                'mailFormat' => $mailFormat,
                'context' => $context,
                'pid' => ConfigurationUtility::getStoragePid(),
            ],
            new Mailing()
        );

        if (!is_null($mailingUid)) {
            $this->attachmentService->createFileReferences($attachments, (int)$mailingUid);
        }
    }

    /**
     * @param array $groups
     * @param string $senderEmail
     * @param string $mailFormat
     * @param string $context
     * @param int $workflowState
     * @param FileInterface[] $attachments
     * @return bool
     */
    protected function validateArguments(
        array $groups,
        string $senderEmail,
        string $mailFormat,
        string $context,
        int $workflowState,
        array $attachments
    ): bool {
        $valid = true;

        if (!Context::isContextValid($context)) {
            $this->logger->critical(
                'No valid context provided. Allowed are only: ' . Context::FRONTEND . ' or ' . Context::BACKEND,
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                    'providedContext' => $context
                ]
            );
            $valid = false;
        }

        if ($mailFormat !== FluidEmail::FORMAT_BOTH &&
            $mailFormat !== FluidEmail::FORMAT_PLAIN &&
            $mailFormat !== FluidEmail::FORMAT_HTML) {
            $this->logger->critical(
                'No valid mail format provided. Allowed are only: ' . FluidEmail::FORMAT_BOTH . ', ' . FluidEmail::FORMAT_PLAIN . ' or ' . FluidEmail::FORMAT_HTML,
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
            $valid = false;
        }

        if (empty($senderEmail)) {
            $this->logger->critical(
                'No valid sender email',
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
            $valid = false;
        }

        if (!Workflow::isValidWorkflowState($workflowState)) {
            $this->logger->critical(
                'No valid workflow state',
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
            $valid = false;
        }

        if ($context === Context::FRONTEND) {
            foreach ($groups as $key => $group) {
                if (!$group instanceof FrontendUserGroup) {
                    $this->logger->error(
                        'An not valid frontend group was removed.',
                        [
                            'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                            'invalidGroup' => json_encode($group)
                        ]
                    );
                    unset($groups[$key]);
                }
            }
        }

        if ($context === Context::BACKEND) {
            foreach ($groups as $key => $group) {
                if (!$group instanceof BackendUserGroup) {
                    $this->logger->error(
                        'An not valid backend group was removed.',
                        [
                            'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                            'invalidGroup' => json_encode($group)
                        ]
                    );
                    unset($groups[$key]);
                }
            }
        }

        foreach ($attachments as $key => $attachment) {
            if (!$attachment instanceof FileInterface) {
                $this->logger->error(
                    'An not valid attachment was removed.',
                    [
                        'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                        'invalidGroup' => json_encode($attachment)
                    ]
                );
                unset($attachments[$key]);
            }
        }

        return $valid;
    }

    /**
     * @return string
     */
    protected function getSenderEmailFallback(): string
    {
        $this->logger->debug('set fallback for sender email');
        return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
    }

    /**
     * @return string
     */
    protected function getSenderNameFallback(): string
    {
        $this->logger->debug('set fallback for sender name');
        return $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
    }

    /**
     * @param MailQueue $queueEntry
     * @return boolean
     */
    public function sendMail(
        MailQueue $queueEntry
    ): bool {
        $status = false;
        $email = GeneralUtility::makeInstance(FluidEmail::class);

        switch ($queueEntry->getContext()) {
            case Context::FRONTEND:
                if (!empty($queueEntry->getFeUser()) &&
                    filter_var($queueEntry->getFeUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $to = $queueEntry->getFeUser()->getEmail();
                }
                break;
            case Context::BACKEND:
                if (!empty($queueEntry->getBeUser()) &&
                    filter_var($queueEntry->getBeUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $to = $queueEntry->getBeUser()->getEmail();
                }
                break;
            default:
                $this->logger->error(
                    'Email could not be sent. Because the target user of queue #' . $queueEntry->getUid() .
                    ' has no valid email address',
                    ['queueEntry' => $queueEntry->getUid()]
                );
                break;
        }

        if (!empty($to)) {
            $email
                ->to($to)
                ->from(
                    new Address($queueEntry->getMailing()->getSenderMail(), $queueEntry->getMailing()->getSenderName())
                )
                ->subject($queueEntry->getMailing()->getSubject())
                ->setTemplate('Mailing')
                ->format($queueEntry->getMailing()->getMailFormat())
                ->assign('content', $queueEntry->getMailing()->getBodytext());

            if ($queueEntry->getMailing()->getAttachments()->count() > 0) {
                /** @var FileReference $attachment */
                foreach ($queueEntry->getMailing()->getAttachments() as $attachment) {
                    $publicPath = Environment::getPublicPath() . '/fileadmin';
                    $combinedIdenitifer = $publicPath . $attachment->getOriginalResource()->getIdentifier();
                    $email->attachFromPath($combinedIdenitifer, $attachment->getOriginalResource()->getName());
                }
            }

            try {
                GeneralUtility::makeInstance(Mailer::class)->send($email);
                $status = true;
            } catch (TransportExceptionInterface $e) {
                $this->logger->error(
                    'Email could not be sent. See exception message for more detail.',
                    ['exception' => $e->getMessage()]
                );
            }
        }

        return $status;
    }

    /**
     * This function updates mailings with the workflow status "rejected".
     * So this mailings will ignored for the queue generation
     */
    public function updateRejectedMailings()
    {
        $this->queryBuilder
            ->update(Mailing::TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('mail_queue_generated', $this->queryBuilder->createNamedParameter(0)),
                $this->queryBuilder->expr()->eq(
                    'workflow_state',
                    $this->queryBuilder->createNamedParameter(Workflow::STATE_REJECTED)
                )
            )
            ->set('rejected', 1)
            ->execute();
    }

}
