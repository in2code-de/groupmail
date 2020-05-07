<?php
declare(strict_types=1);

namespace In2code\In2bemail\Domain\Model;

use In2code\In2bemail\Context\Context;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Mailing extends AbstractEntity
{
    const TABLE = 'tx_in2bemail_domain_model_mailing';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup>
     */
    protected $beGroups = [];

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup>
     */
    protected $feGroups = [];

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $bodytext = '';

    /**
     * @var string
     */
    protected $mailFormat = FluidEmail::FORMAT_BOTH;

    /**
     * @var string
     */
    protected $senderMail = '';

    /**
     * @var string
     */
    protected $senderName = '';

    /**
     * @var bool
     */
    protected $mailQueueGenerated = false;

    /**
     * @var string
     */
    protected $context = Context::FRONTEND;

    /**
     * @var bool
     */
    protected $hidden = false;

    public function __construct()
    {
        $this->beGroups = new ObjectStorage();
        $this->feGroups = new ObjectStorage();
    }

    /**
     * @return ObjectStorage
     */
    public function getBeGroups(): ObjectStorage
    {
        return $this->beGroups;
    }

    /**
     * @param ObjectStorage $beGroups
     * @return Mailing
     */
    public function setBeGroups(ObjectStorage $beGroups): Mailing
    {
        $this->beGroups = $beGroups;
        return $this;
    }

    /**
     * @param BackendUserGroup $backendUserGroup
     * @return $this
     */
    public function addBeGroup(BackendUserGroup $backendUserGroup)
    {
        $this->beGroups->attach($backendUserGroup);
        return $this;
    }

    /**
     * @param BackendUserGroup $backendUserGroup
     * @return $this
     */
    public function removeBeGroup(BackendUserGroup $backendUserGroup)
    {
        $this->beGroups->detach($backendUserGroup);
        return $this;
    }

    /**
     * @return ObjectStorage
     */
    public function getFeGroups(): ObjectStorage
    {
        return $this->feGroups;
    }

    /**
     * @param ObjectStorage $feGroups
     * @return Mailing
     */
    public function setFeGroups(ObjectStorage $feGroups): Mailing
    {
        $this->feGroups = $feGroups;
        return $this;
    }

    /**
     * @param FrontendUserGroup $frontendUserGroup
     * @return $this
     */
    public function addFeGroup(FrontendUserGroup $frontendUserGroup)
    {
        $this->feGroups->attach($frontendUserGroup);
        return $this;
    }

    /**
     * @param FrontendUserGroup $frontendUserGroup
     * @return $this
     */
    public function removeFeGroup(FrontendUserGroup $frontendUserGroup)
    {
        $this->feGroups->detach($frontendUserGroup);
        return $this;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $subject
     * @return Mailing
     */
    public function setSubject(string $subject): Mailing
    {
        $this->subject = $subject;
        return $this;
    }

    /**
     * @return string
     */
    public function getBodytext(): string
    {
        return $this->bodytext;
    }

    /**
     * @param string $bodytext
     * @return Mailing
     */
    public function setBodytext(string $bodytext): Mailing
    {
        $this->bodytext = $bodytext;
        return $this;
    }

    /**
     * @return string
     */
    public function getMailFormat(): string
    {
        return $this->mailFormat;
    }

    /**
     * @param string $mailFormat
     * @return Mailing
     */
    public function setMailFormat(string $mailFormat): Mailing
    {
        $this->mailFormat = $mailFormat;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderMail(): string
    {
        return $this->senderMail;
    }

    /**
     * @param string $senderMail
     * @return Mailing
     */
    public function setSenderMail(string $senderMail): Mailing
    {
        $this->senderMail = $senderMail;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * @param string $senderName
     * @return Mailing
     */
    public function setSenderName(string $senderName): Mailing
    {
        $this->senderName = $senderName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMailQueueGenerated(): bool
    {
        return $this->mailQueueGenerated;
    }

    /**
     * @param bool $mailQueueGenerated
     * @return Mailing
     */
    public function setMailQueueGenerated(bool $mailQueueGenerated): Mailing
    {
        $this->mailQueueGenerated = $mailQueueGenerated;
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
     * @return Mailing
     */
    public function setContext(string $context): Mailing
    {
        $this->context = $context;
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
     * @return Mailing
     */
    public function setHidden(bool $hidden): Mailing
    {
        $this->hidden = $hidden;
        return $this;
    }
}
