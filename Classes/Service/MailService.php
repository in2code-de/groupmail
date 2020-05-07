<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

use In2code\In2bemail\Context\Context;
use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Model\MailQueue;
use In2code\In2bemail\Domain\Repository\MailingRepository;
use In2code\In2bemail\Utility\ConfigurationUtility;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class MailService extends AbstractService
{
    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    /**
     * MailService constructor.
     *
     * @param MailingRepository $mailingRepository
     */
    public function __construct(MailingRepository $mailingRepository)
    {
        $this->mailingRepository = $mailingRepository;
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
     * @param array $attachments
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
            $context
        )) {
            throw new \InvalidArgumentException(
                'The argument validation of generateMailing failed. More information are in the logs',
                '1588836726'
            );
        }

        $groupStorage = new ObjectStorage();
        foreach ($groups as $group) {
            $groupStorage->attach($group);
        }

        $this->mailingRepository->createRecord(
            [
                $context . 'Groups' => $groupStorage,
                'subject' => $subject,
                'bodytext' => $bodytext,
                'senderMail' => $senderEmail,
                'senderName' => $senderName,
                'mailFormat' => $mailFormat,
                'context' => $context,
                'pid' => ConfigurationUtility::getStoragePid()
            ],
            new Mailing()
        );
        $this->uploadAttachments([]);
    }

    /**
     * @param array $groups
     * @param string $senderEmail
     * @param string $mailFormat
     * @param string $context
     * @return bool
     */
    protected function validateArguments(
        array $groups,
        string $senderEmail,
        string $mailFormat,
        string $context
    ): bool {
        $valid = true;

        if (Context::validateContext($context)) {
            $this->logger->critical(
                'No valid context provided. Allowed are only: ' . Context::FRONTEND . ' or ' . Context::BACKEND,
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
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

        return $valid;
    }

    protected function uploadAttachments(
        array $attachments
    ) {
        // @todo implement function
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
}
