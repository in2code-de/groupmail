<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Service;

use In2code\Groupmailer\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UserService extends AbstractService
{
    /**
     * @var UserGroupService
     */
    protected $userGroupService;

    public function __construct(UserGroupService $userGroupService)
    {
        $this->userGroupService = $userGroupService;
    }

    /**
     * @param array $groups
     * @param string $context allowed values are Context::FRONTEND or Context::BACKEND
     * @return array
     */
    public function getUserByGroups(array $groups, string $context = Context::FRONTEND): array
    {
        $users = [];
        if (Context::isContextValid($context)) {
            foreach ($groups as $group) {
                $user = $this->getAllUserForUserGroup($group, $context);
                if (!empty($user)) {
                    ArrayUtility::mergeRecursiveWithOverrule($users, $user);
                }
            }
        } else {
            $this->logger->error(
                'Wrong Context was provided. Allowed values are ' . Context::FRONTEND . ' or ' . Context::BACKEND,
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
        }

        return $users;
    }

    /**
     * @param int $userGroup
     * @param string $context
     * @return array
     */
    protected function getAllUserForUserGroup(int $userGroup, string $context): array
    {
        $table = $context . '_users';

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable($table);

        $statement = $queryBuilder->select('*')->from($table)->where(
            'FIND_IN_SET(' . $queryBuilder->createNamedParameter($userGroup) . ', usergroup)'
        )->execute();

        while ($row = $statement->fetch()) {
            $user[$row['uid']] = $row;
        }

        if (!empty($user)) {
            return $user;
        }

        return [];
    }
}
