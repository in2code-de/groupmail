<?php
declare(strict_types=1);

namespace In2code\Groupmailer\Domain\Repository;

use In2code\Groupmailer\Workflow\Workflow;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

class MailingRepository extends AbstractRepository
{
    /**
     * @param bool $includeHidden
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAll(bool $includeHidden = false)
    {
        $query = $this->createQuery();

        if ($includeHidden) {
            $query->getQuerySettings()->setIgnoreEnableFields(true);
        }

        return $query->execute();
    }

    public function getAvailableMailingsToGenerate() {
        $query = $this->createQuery();

        $constraints = [
            $query->equals('mailQueueGenerated', false),
            $query->equals('rejected', false),
            $query->equals('workflowState', Workflow::STATE_APPROVED)
        ];

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    public function findActiveMailings()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $constraints[] = $query->equals('mailQueueGenerated', false);
        $constraints[] = $query->logicalNot($query->equals('rejected', true));

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    public function findLockedMailings()
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setIgnoreEnableFields(true);

        $constraints = [
            $query->equals('mailQueueGenerated', true),
            $query->equals('rejected', true)
        ];

        $query->matching($query->logicalOr($constraints));

        return $query->execute();
    }
}
