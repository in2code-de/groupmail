<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Model;

use In2code\In2bemail\Context\Context;
use TYPO3\CMS\Extbase\Domain\Model\BackendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
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
     * @var FrontendUser|null
     */
    protected $feUser = null;

    /**
     * @var bool
     */
    protected $sent = false;

    /**
     * @var bool
     */
    protected $error = false;

    /**
     * @var bool
     */
    protected $hidden = false;

    /**
     * @var string
     */
    protected $context = Context::FRONTEND;

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
     * @return FrontendUser|null
     */
    public function getFeUser(): ?FrontendUser
    {
        return $this->feUser;
    }

    /**
     * @param FrontendUser|null $feUser
     * @return MailQueue
     */
    public function setFeUser(?FrontendUser $feUser): MailQueue
    {
        $this->feUser = $feUser;
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

    /**
     * @return string
     */
    public function getContext(): string
    {
        return $this->context;
    }

    /**
     * @param string $context
     * @return MailQueue
     */
    public function setContext(string $context): MailQueue
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param bool $error
     * @return MailQueue
     */
    public function setError(bool $error): MailQueue
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @param bool $hidden
     * @return MailQueue
     */
    public function setHidden(bool $hidden): MailQueue
    {
        $this->hidden = $hidden;
        return $this;
    }
}
