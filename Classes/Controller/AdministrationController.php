<?php
declare(strict_types=1);

namespace In2code\In2bemail\Controller;

use In2code\In2bemail\Domain\Repository\MailingRepository;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class AdministrationController extends ActionController
{
    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    public function __construct(MailingRepository $mailingRepository)
    {
        $this->mailingRepository = $mailingRepository;
    }

    public function indexAction()
    {

        /*
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->mailingRepository->findActiveMailings(), __CLASS__ . ' in der Zeile ' . __LINE__);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($this->mailingRepository->findLockedMailings(), __CLASS__ . ' in der Zeile ' . __LINE__);
        die();
        */

        $this->view->assignMultiple(
            [
                'activeMailings' => $this->mailingRepository->findActiveMailings(),
                'lockedMailings' => $this->mailingRepository->findLockedMailings()
            ]
        );

        //   \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($mailings, __CLASS__ . ' in der Zeile ' . __LINE__);
        // die();
    }
}
