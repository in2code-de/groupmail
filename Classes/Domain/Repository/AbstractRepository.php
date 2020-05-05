<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Repository;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;
use TYPO3\CMS\Extbase\Persistence\Repository;

class AbstractRepository extends Repository implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param object $modifiedObject
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    public function update($modifiedObject)
    {
        parent::update($modifiedObject);
        $this->persistenceManager->persistAll();
    }

    /**
     * @param array $properties
     * @param AbstractDomainObject $object
     */
    public function createRecord(array $properties, AbstractDomainObject $object): void
    {
        foreach ($properties as $property => $value) {
            if ($object->_hasProperty($property)) {
                $object->_setProperty($property, $value);
            }
        }

        $this->persistenceManager->add($object);
        $this->logger->debug('create ' . get_class($object) . ' record', $properties);
        $this->persistenceManager->persistAll();
    }
}
