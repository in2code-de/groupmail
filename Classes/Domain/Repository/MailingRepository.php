<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;

class MailingRepository extends AbstractRepository
{
    /**
     * @param object $modifiedObject
     * @throws IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function update($modifiedObject)
    {
        parent::update($modifiedObject);
        $this->persistenceManager->persistAll();
    }
}
