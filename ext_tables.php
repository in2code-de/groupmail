<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'groupmailer',
            'web',
            'administration',
            'bottom',
            [
                In2code\Groupmailer\Controller\AdministrationController::class => 'index, new, create',
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:groupmailer/Resources/Public/Icons/module-administration.svg',
                'labels' => 'LLL:EXT:groupmailer/Resources/Private/Language/locallang_mod.xlf',
                'navigationComponentId' => '',
                'inheritNavigationComponentFromMainModule' => false
            ]
        );
    }
);
