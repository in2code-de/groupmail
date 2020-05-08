<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Repository;

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
}
