<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

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
     * @param BackendUserGroup[] $backendGroups
     * @param string $subject
     * @param string $bodytext
     * @param string $senderEmail
     * @param string $senderName
     * @param string $mailFormat valid options are FluidEmail::FORMAT_BOTH,  FluidEmail::FORMAT_HTML or FluidEmail::FORMAT_PLAIN
     * @param array $attachments
     *
     * @api
     */
    public function generateMailing(
        array $backendGroups,
        string $subject,
        string $bodytext,
        string $senderEmail = '',
        string $senderName = '',
        string $mailFormat = FluidEmail::FORMAT_BOTH,
        array $attachments = []
    ) {
        if (empty($senderName)) {
            $senderName = $this->getSenderNameFallback();
        }

        if (empty($senderEmail)) {
            $senderEmail = $this->getSenderEmailFallback();
        }

        $beGroups = new ObjectStorage();
        foreach ($backendGroups as $backendGroup) {
            $beGroups->attach($backendGroup);
        }

        $this->mailingRepository->createRecord(
            [
                'beGroups' => $beGroups,
                'subject' => $subject,
                'bodytext' => $bodytext,
                'senderMail' => $senderEmail,
                'senderName' => $senderName,
                'mailFormat' => $mailFormat,
                'pid' => ConfigurationUtility::getStoragePid()
            ],
            new Mailing()
        );
        $this->uploadAttachments([]);
    }

    protected function uploadAttachments(array $attachments)
    {
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
    public function sendMail(MailQueue $queueEntry): bool
    {
        $status = false;

        if (filter_var($queueEntry->getBeUser()->getEmail(), FILTER_VALIDATE_EMAIL)) {
            $email = GeneralUtility::makeInstance(FluidEmail::class);

            $email
                ->to($queueEntry->getBeUser()->getEmail())
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
        } else {
            $this->logger->error(
                'Email could not be sent. Because the user: ' . $queueEntry->getBeUser()->getUserName() .
                ' has no valid email address',
                ['queueEntry' => $queueEntry->getUid()]
            );
        }

        return $status;
    }
}
