<?php
declare(strict_types=1);

namespace In2code\In2bemail\Utility;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserUtility
{
    /**
     * @param int $userGroup
     * @return array
     */
    public static function getAllBackendUserForUserGroup(int $userGroup): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('be_users');

        $statement = $queryBuilder->select('*')->from('be_users')->where(
            'FIND_IN_SET(' . $queryBuilder->createNamedParameter($userGroup) . ', usergroup)'
        )->execute();

        while ($row = $statement->fetch()) {
            $backendUser[$row['uid']] = $row;
        }

        if (!empty($backendUser)) {
            return $backendUser;
        }

        return [];
    }
}
