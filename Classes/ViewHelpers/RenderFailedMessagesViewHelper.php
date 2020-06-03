<?php
declare(strict_types=1);

namespace In2code\Groupmailer\ViewHelpers;

use In2code\Groupmailer\Context\Context;
use In2code\Groupmailer\Domain\Model\Mailing;
use In2code\Groupmailer\Domain\Model\MailQueue;
use In2code\Groupmailer\Domain\Repository\MailQueueRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class RenderFailedMessagesViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var MailQueueRepository
     */
    protected $mailQueueRepository;

    public function __construct(MailQueueRepository $mailQueueRepository)
    {
        $this->mailQueueRepository = $mailQueueRepository;
    }

    public function initializeArguments()
    {
        $this->registerArgument('mailing', Mailing::class, 'the mailing', true);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $mailing = $this->arguments['mailing'];
        $content = '';
        $errorMessage = LocalizationUtility::translate('LLL:EXT:groupmailer/Resources/Private/Language/locallang_db.xlf:modal.error.message');
        $additionalInformation = LocalizationUtility::translate('LLL:EXT:groupmailer/Resources/Private/Language/locallang_db.xlf:modal.error.message.additional-information');

        if ($mailing instanceof Mailing) {
            $failed = $this->mailQueueRepository->getFailedMessages($mailing);
            $content = '<strong>' . $errorMessage . ':</strong><br/><br/><ul>';

            if (!empty($failed)) {
                /** @var MailQueue $failedItem */
                foreach ($failed as $failedItem) {
                    switch ($failedItem->getContext()) {
                        case Context::BACKEND:
                            $content .= '<li>' . $failedItem->getBeUser()->getUserName() . ' (Uid: ' . $failedItem->getBeUser()->getUid() . ')</li>';
                            break;
                        case Context::FRONTEND:
                            $content .= '<li>' . $failedItem->getFeUser()->getUserName() . ' (Uid: ' . $failedItem->getFeUser()->getUid() . ')</li>';
                            break;
                    }
                }
            }
            $content .= '</ul><br/><p>' . $additionalInformation . '</p>';
        }

        return $content;
    }
}
