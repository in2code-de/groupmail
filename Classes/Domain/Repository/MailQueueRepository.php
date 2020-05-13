<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Repository;

use In2code\In2bemail\Domain\Model\Mailing;
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

        $constraints = [
            $query->equals('sent', false),
            $query->equals('error', false)
        ];

        $query->matching($query->logicalAnd($constraints));
        $query->setLimit($limit);
        $query->setOrderings(['tstamp' => QueryInterface::ORDER_ASCENDING]);

        return $query->execute()->toArray();
    }

    /**
     * @param Mailing $mailing
     * @return array
     */
    public function getFailedMessages(Mailing $mailing): array
    {
        $query = $this->createQuery();

        return $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('mailing', $mailing),
                    $query->equals('error', true)
                ]
            )
        )->execute()->toArray();
    }

    /**
     * @param Mailing $mailing
     * @return array
     */
    public function getQueueStatusForMailing(Mailing $mailing): array
    {
        $query = $this->createQuery();

        $status = [
            'count' => 0,
            'sent' => 0,
            'failed' => 0,
            'notSent' => 0
        ];

        // count
        $status['count'] = $query->matching($query->logicalAnd($query->equals('mailing', $mailing)))->count();

        // sent
        $status['sent'] = $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('mailing', $mailing),
                    $query->equals('sent', true)
                ]
            )
        )->count();

        // failed
        $status['failed'] = $query->matching(
            $query->logicalAnd(
                [
                    $query->equals('mailing', $mailing),
                    $query->equals('error', true)
                ]
            )
        )->count();

        $status['notSent'] = $status['count'] - $status['sent'] - $status['failed'];

        return $status;
    }
}
