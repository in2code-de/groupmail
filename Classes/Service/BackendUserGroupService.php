<?php
declare(strict_types=1);

namespace In2code\In2bemail\Service;

use In2code\In2bemail\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;

class BackendUserGroupService extends AbstractService
{
    /**
     * @var ConnectionPool
     */
    protected $connectionPool;

    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    public function __construct(ConnectionPool $connectionPool)
    {
        $this->connectionPool = $connectionPool;
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable('be_groups');
    }

    /**
     * @param BackendUserGroup $backendUserGroup
     * @param int $recursiveLevel -1 = infinite
     * @return array
     */
    public function getBackendGroups(BackendUserGroup $backendUserGroup, int $recursiveLevel): array
    {
        $groups[] = $backendUserGroup->getUid();
        $recursiveLevelConfig = ['maxLevel' => $recursiveLevel, 'currentLevel' => 0];

        self::getSubgroups($backendUserGroup->getUid(), $recursiveLevelConfig, $groups);

        return $groups;
    }

    /**
     * @param int $groupUid
     * @param array $recursiveLevelConfig
     * @param array $groups
     */
    protected function getSubgroups(int $groupUid, array &$recursiveLevelConfig, array &$groups)
    {
        $recursiveLevelConfig['currentLevel']++;

        if ($recursiveLevelConfig['currentLevel'] <= $recursiveLevelConfig['maxLevel']) {
            $subGroups = $this->queryBuilder->select('uid')->from('be_groups')->where(
                'FIND_IN_SET(' . $this->queryBuilder->createNamedParameter($groupUid) . ', subgroup)'
            )->execute()->fetchAll();

            if (!empty($subGroups)) {
                foreach ($subGroups as $subGroup) {
                    $groups[] = $subGroup['uid'];
                    self::getSubgroups($subGroup['uid'], $recursiveLevelConfig, $groups);
                }
            }
        }
    }
}
