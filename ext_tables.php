<?php

use In2code\In2bemail\Controller\AdministrationController;

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'in2bemail',
            'web',
            'administration',
            'bottom',
            [
                AdministrationController::class => 'index, new, create',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:in2bemail/Resources/Public/Icons/module-administration.svg',
                'labels' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_mod.xlf',
                'navigationComponentId' => '',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );
    }
);
