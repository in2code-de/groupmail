<?php
declare(strict_types=1);

namespace In2code\Groupmailer\ViewHelpers;

use In2code\Groupmailer\Domain\Model\Mailing;
use In2code\Groupmailer\Domain\Repository\MailQueueRepository;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class GetStatusViewHelper extends AbstractViewHelper
{
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
     * @return array
     */
    public function render(): array
    {
        $mailing = $this->arguments['mailing'];

        if ($mailing instanceof Mailing && !$mailing->isRejected()) {
            $status = $this->mailQueueRepository->getQueueStatusForMailing($mailing);
            $status['percentageDone'] = ($status['count'] - $status['notSent']) / ($status['count'] / 100);

            return $status;
        }

        return [];
    }
}
