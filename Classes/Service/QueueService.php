<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

use In2code\In2bemail\Context\Context;
use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Model\MailQueue;
use In2code\In2bemail\Domain\Repository\MailingRepository;
use In2code\In2bemail\Domain\Repository\MailQueueRepository;
use In2code\In2bemail\Utility\ConfigurationUtility;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

class QueueService extends AbstractService
{
    /**
     * @var UserGroupService
     */
    protected $userGroupService;

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
     * @var UserService
     */
    protected $userService;

    /**
     * QueueService constructor.
     *
     * @param UserGroupService $backendUserGroupService
     * @param MailQueueRepository $mailQueueRepository
     * @param MailService $mailService
     * @param MailingRepository $mailingRepository
     * @param UserService $userService
     */
    public function __construct(
        UserGroupService $backendUserGroupService,
        MailQueueRepository $mailQueueRepository,
        MailService $mailService,
        MailingRepository $mailingRepository,
        UserService $userService
    ) {
        $this->userGroupService = $backendUserGroupService;
        $this->mailQueueRepository = $mailQueueRepository;
        $this->mailService = $mailService;
        $this->mailingRepository = $mailingRepository;
        $this->userService = $userService;
    }

    /**
     * @param Mailing $mailing
     */
    protected function generateQueueForMailing(Mailing $mailing)
    {
        $groups = [];
        switch ($mailing->getContext()) {
            case Context::FRONTEND:
                $groups = $mailing->getFeGroups()->toArray();
                break;
            case Context::BACKEND:
                $groups = $mailing->getBeGroups()->toArray();
                break;
        }

        $users = $this->getUsersForMailing($groups, $mailing->getContext());

        foreach ($users as $user) {
            $this->mailQueueRepository->createRecord(
                [
                    'pid' => ConfigurationUtility::getStoragePid(),
                    $mailing->getContext() . 'User' => $user['uid'],
                    'context' => $mailing->getContext(),
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
    public function generateQueue()
    {
        $mailings = $this->mailingRepository->getAvailableMailingsToGenerate();

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
            } else {
                $recordToProcess->setError(true);
            }
            $this->mailQueueRepository->update($recordToProcess);
        }
    }

    /**
     * @param array $groups
     * @param string $context
     * @return array
     */
    protected function getUsersForMailing(array $groups, string $context): array
    {
        $groupsWithSubgroups = [];
        foreach ($groups as $group) {
            $groupsWithSubgroups[] = $group->getUid();
            $groupsWithSubgroups =
                array_unique(
                    array_merge(
                        $groupsWithSubgroups,
                        $this->userGroupService->getSubgroups(
                            $group->getUid(),
                            ConfigurationUtility::getRecursionLevel(),
                            $context
                        )
                    )
                );
        }

        return $this->userService->getUserByGroups($groupsWithSubgroups, $context);
    }
}
