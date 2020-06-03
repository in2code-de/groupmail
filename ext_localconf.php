<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        /**
         * Logging
         */
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['In2bemail'] = [
            'writerConfiguration' => [
                TYPO3\CMS\Core\Log\LogLevel::INFO => [
                    TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class => [
                        'logTable' => 'tx_in2bemail_log'
                    ]
                ]
            ]
        ];

        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][300] =
            'EXT:in2bemail/Resources/Private/Templates/Email';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][300] =
            'EXT:in2bemail/Resources/Private/Layouts/Email';

        $icons = [
            [
                'identifier' => 'tx-in2bemail-backend-mail',
                'source' => 'EXT:in2bemail/Resources/Public/Icons/mail_backend.svg'
            ],
            [
                'identifier' => 'tx-in2bemail-frontend-mail',
                'source' => 'EXT:in2bemail/Resources/Public/Icons/mail_frontend.svg'
            ],
        ];

        $iconRegistry = TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            TYPO3\CMS\Core\Imaging\IconRegistry::class
        );
        foreach ($icons as $icon) {
            $iconRegistry->registerIcon(
                $icon['identifier'],
                TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => $icon['source']]
            );
        }
    }
);
