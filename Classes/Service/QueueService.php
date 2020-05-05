<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Model\MailQueue;
use In2code\In2bemail\Domain\Repository\MailingRepository;
use In2code\In2bemail\Domain\Repository\MailQueueRepository;
use In2code\In2bemail\Utility\BackendUserUtility;
use In2code\In2bemail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

class QueueService extends AbstractService
{
    /**
     * @var BackendUserGroupService
     */
    protected $backendUserGroupService;

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var MailQueueRepository
     */
    protected $mailQueueRepository;

    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    /**
     * QueueService constructor.
     *
     * @param BackendUserGroupService $backendUserGroupService
     * @param MailQueueRepository $mailQueueRepository
     * @param MailService $mailService
     * @param MailingRepository $mailingRepository
     */
    public function __construct(
        BackendUserGroupService $backendUserGroupService,
        MailQueueRepository $mailQueueRepository,
        MailService $mailService,
        MailingRepository $mailingRepository
    ) {
        $this->backendUserGroupService = $backendUserGroupService;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->mailService = $mailService;
        $this->mailingRepository = $mailingRepository;
    }

    /**
     * @param Mailing $mailing
     */
    protected function generateQueueForMailing(Mailing $mailing)
    {
        $backendUsers = $this->getBackendUsersForMailing($mailing);

        foreach ($backendUsers as $backendUser) {
            $this->mailQueueRepository->createRecord(
                [
                    'pid' => ConfigurationUtility::getStoragePid(),
                    'beUser' => $backendUser['uid'],
                    'mailing' => $mailing
                ],
                new MailQueue()
            );
        }
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function generateQueue() {
        $mailings = $this->mailingRepository->findByMailQueueGenerated(0);

        /** @var Mailing $mailing */
        foreach ($mailings as $mailing) {
            $this->generateQueueForMailing($mailing);

            $this->logger->info(
                'The mail queue for mailing: ' . $mailing->getUid() . ' was created.',
                [
                    'mailing' => $mailing->getUid()
                ]
            );
            $mailing->setMailQueueGenerated(true);
            $this->mailingRepository->update($mailing);
        }
    }

    /**
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function processQueue()
    {
        $recordsToProcess =
            $this->mailQueueRepository->getEntriesToProcess(ConfigurationUtility::getEmailProcessCount());

        /** @var MailQueue $recordToProcess */
        foreach ($recordsToProcess as $recordToProcess) {
            if ($this->mailService->sendMail($recordToProcess)) {
                $recordToProcess->setSent(true);
                $this->mailQueueRepository->update($recordToProcess);
            }
        }
    }

    /**
     * @param Mailing $mailing
     * @return array
     */
    protected function getBackendUsersForMailing(Mailing $mailing): array
    {
        $groups = [];
        $backendUsers = [];

        foreach ($mailing->getBeGroups() as $beGroup) {
            $groups =
                array_unique(
                    array_merge(
                        $groups,
                        $this->backendUserGroupService->getBackendGroups(
                            $beGroup,
                            ConfigurationUtility::getRecursionLevel()
                        )
                    )
                );
        }

        foreach ($groups as $group) {
            $backendUser = BackendUserUtility::getAllBackendUserForUserGroup($group);
            if (!empty($backendUser)) {
                ArrayUtility::mergeRecursiveWithOverrule($backendUsers, $backendUser);
            }
        }

        return $backendUsers;
    }
}
