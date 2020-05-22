<?php
declare(strict_types=1);

namespace In2code\In2bemail\Controller;

use In2code\In2bemail\Context\Context;
use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Repository\MailingRepository;
use TYPO3\CMS\Backend\Template\Components\Menu\Menu;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Domain\Repository\BackendUserGroupRepository;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserGroupRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class AdministrationController extends ActionController
{
    /**
     * Backend Template Container
     *
     * @var string
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * @var BackendUserGroupRepository
     */
    protected $backendUserGroupRepository;

    /**
     * @var FrontendUserGroupRepository
     */
    protected $frontendUserGroupRepository;

    /**
     * @var MailingRepository
     */
    protected $mailingRepository;

    public function __construct(
        MailingRepository $mailingRepository,
        BackendUserGroupRepository $backendUserGroupRepository,
        FrontendUserGroupRepository $frontendUserGroupRepository
    ) {
        $this->mailingRepository = $mailingRepository;
        $this->backendUserGroupRepository = $backendUserGroupRepository;
        $this->frontendUserGroupRepository = $frontendUserGroupRepository;

    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     */
    protected function initializeView(ViewInterface $view)
    {
        parent::initializeView($view);

        $this->createMenu();
    }

    public function indexAction()
    {
        $this->view->assignMultiple(
            [
                'activeMailings' => $this->mailingRepository->findActiveMailings(),
                'lockedMailings' => $this->mailingRepository->findLockedMailings()
            ]
        );
    }

    public function newAction()
    {
        /** @var Typo3QuerySettings $defaultQuerySettings */
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        $defaultQuerySettings->setRespectStoragePage(false);
        $this->frontendUserGroupRepository->setDefaultQuerySettings($defaultQuerySettings);

        $this->view->assignMultiple(
            [
                'beGroups' => $this->backendUserGroupRepository->findAll(),
                'feGroups' => $this->frontendUserGroupRepository->findAll(),
                'mailing' => new Mailing(),
            ]
        );
    }

    public function createAction(Mailing $mailing)
    {
        $this->removeContextForeignGroups($mailing);

        
    }

    /**
     * @param Mailing $mailing
     */
    protected function removeContextForeignGroups(Mailing $mailing) {
        if ($mailing->getContext() === Context::FRONTEND) {
            $mailing->setBeGroups(new ObjectStorage());
        }

        if ($mailing->getContext() === Context::BACKEND) {
            $mailing->setFeGroups(new ObjectStorage());
        }
        
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($mailing, __CLASS__ . ' in der Zeile ' . __LINE__);
        die();
    }

    /**
     * Create menu
     *
     */
    protected function createMenu()
    {
        $uriBuilder = $this->objectManager->get(UriBuilder::class);
        $uriBuilder->setRequest($this->request);

        $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
        $menu->setIdentifier('in2bemail');

        $actions = [
            ['action' => 'index', 'label' => 'administration'],
            ['action' => 'new', 'label' => 'new']
        ];

        foreach ($actions as $action) {
            $item = $menu->makeMenuItem()
                ->setTitle(
                    LocalizationUtility::translate(
                        'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:module.' . $action['label']
                    )
                )
                ->setHref($uriBuilder->reset()->uriFor($action['action'], [], 'Administration'))
                ->setActive($this->request->getControllerActionName() === $action['action']);
            $menu->addMenuItem($item);
        }

        if ($menu instanceof Menu) {
            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }
}
