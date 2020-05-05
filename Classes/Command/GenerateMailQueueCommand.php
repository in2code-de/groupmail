<?php
declare(strict_types=1);

namespace In2code\In2bemail\Command;

use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Repository\MailingRepository;
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
     * @var MailingRepository
     */
    protected $mailingRepository;

    /**
     * @var QueueService
     */
    protected $queueService;

    public function __construct(MailingRepository $mailingRepository, QueueService $queueService)
    {
        $this->mailingRepository = $mailingRepository;
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
        $mailings = $this->mailingRepository->findByMailQueueGenerated(0);

        /** @var Mailing $mailing */
        foreach ($mailings as $mailing) {
            $this->queueService->generateQueueForMailing($mailing);

            $this->logger->info(
                'The mail queue for mailing: ' . $mailing->getUid() . ' was created.',
                [
                    'mailing' => $mailing->getUid()
                ]
            );
            $mailing->setMailQueueGenerated(true);
            $this->mailingRepository->update($mailing);
        }
    }
}
