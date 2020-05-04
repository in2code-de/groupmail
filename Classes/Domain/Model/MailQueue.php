<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

class MailQueue extends AbstractEntity
{
    const TABLE = 'tx_in2bemail_domain_model_mailqueue';

    /**
     * @var Mailing|null
     */
    protected $mailing = null;

    /**
     * @var BackendUser|null
     */
    protected $beUser = null;

    /**
     * @var bool
     */
    protected $sent = false;

    /**
     * @return Mailing|null
     */
    public function getMailing(): ?Mailing
    {
        return $this->mailing;
    }

    /**
     * @param Mailing|null $mailing
     * @return MailQueue
     */
    public function setMailing(?Mailing $mailing): MailQueue
    {
        $this->mailing = $mailing;
        return $this;
    }

    /**
     * @return BackendUser|null
     */
    public function getBeUser(): ?BackendUser
    {
        return $this->beUser;
    }

    /**
     * @param BackendUser $beUser
     * @return MailQueue
     */
    public function setBeUser(BackendUser $beUser): MailQueue
    {
        $this->beUser = $beUser;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->sent;
    }

    /**
     * @param bool $sent
     * @return MailQueue
     */
    public function setSent(bool $sent): MailQueue
    {
        $this->sent = $sent;
        return $this;
    }
}
