<?php
declare(strict_types=1);

namespace In2code\In2bemail\Command;

use In2code\In2bemail\Domain\Repository\MailQueueRepository;
use In2code\In2bemail\Service\QueueService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessMailQueueCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var MailQueueRepository
     */
    protected $mailQueueRepository;

    /**
     * @var QueueService
     */
    protected $queueService;

    public function __construct(MailQueueRepository $mailQueueRepository, QueueService $queueService)
    {
        $this->mailQueueRepository = $mailQueueRepository;
        $this->queueService = $queueService;
        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Process mail queue entries.');
    }

    /**
     * Executes the command for showing sys_log entries
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueService->processQueue();
    }
}
