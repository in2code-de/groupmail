<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Service;

use In2code\Groupmailer\Context\Context;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

class UserGroupService extends AbstractService
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
    }

    /**
     * @param int $userGroupId
     * @param int $recursiveLevel -1 = infinite
     * @param string $context allowed are Context::FRONTEND or Context::BACKEND
     * @return array
     */
    public function getSubgroups(
        int $userGroupId,
        int $recursiveLevel,
        string $context = Context::FRONTEND
    ): array {
        $groups = [];

        if (Context::isContextValid($context)) {
            $recursiveLevelConfig = ['maxLevel' => $recursiveLevel, 'currentLevel' => 0];
            $table = $context . '_groups';
            $this->queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);

            self::getSubgroupsRecursively($userGroupId, $recursiveLevelConfig, $groups, $table);
        } else {
            $this->logger->error(
                'Wrong Context was provided. Allowed values are ' . Context::FRONTEND . ' or ' . Context::BACKEND,
                [
                    'additionalInfo' => ['class' => __CLASS__, 'method' => __METHOD__, 'line' => __LINE__],
                ]
            );
        }


        return $groups;
    }

    /**
     * @param int $groupUid
     * @param array $recursiveLevelConfig
     * @param array $groups
     * @param string $table
     */
    protected function getSubgroupsRecursively(int $groupUid, array &$recursiveLevelConfig, array &$groups, string $table)
    {
        $recursiveLevelConfig['currentLevel']++;

        if ($recursiveLevelConfig['currentLevel'] <= $recursiveLevelConfig['maxLevel']) {
            $subGroups = $this->queryBuilder->select('uid')->from($table)->where(
                'FIND_IN_SET(' . $this->queryBuilder->createNamedParameter($groupUid) . ', subgroup)'
            )->execute()->fetchAll();

            if (!empty($subGroups)) {
                foreach ($subGroups as $subGroup) {
                    $groups[] = $subGroup['uid'];
                    self::getSubgroupsRecursively($subGroup['uid'], $recursiveLevelConfig, $groups, $table);
                }
            }
        }
    }
}
