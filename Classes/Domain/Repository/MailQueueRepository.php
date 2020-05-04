<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class MailQueueRepository extends AbstractRepository
{
    /**
     * @param int $limit
     * @return array
     */
    public function getEntriesToProcess(int $limit): array
    {
        $query = $this->createQuery();

        $query->matching($query->equals('sent', false));
        $query->setLimit($limit);
        $query->setOrderings(['tstamp' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute()->toArray();
    }
}
