<?php
declare(strict_types=1);

namespace In2code\In2bemail\Command;

use In2code\In2bemail\Service\QueueService;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException;
use TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException;

class GenerateMailQueueCommand extends Command implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var QueueService
     */
    protected $queueService;

    /**
     * GenerateMailQueueCommand constructor.
     *
     * @param QueueService $queueService
     */
    public function __construct(QueueService $queueService)
    {
        $this->queueService = $queueService;
        parent::__construct();
    }

    /**
     * Configure the command by defining the name, options and arguments
     */
    protected function configure()
    {
        $this->setDescription('Generate mail queue entries for mailings.');
    }

    /**
     * Executes the command for showing sys_log entries
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws IllegalObjectTypeException
     * @throws UnknownObjectException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueService->generateQueue();
    }
}
