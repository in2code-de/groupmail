<?php

defined('TYPO3_MODE') or die();

call_user_func(
    function () {
        /**
         * Logging
         */
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['In2code']['Groupmailer'] = [
            'writerConfiguration' => [
                TYPO3\CMS\Core\Log\LogLevel::INFO => [
                    TYPO3\CMS\Core\Log\Writer\DatabaseWriter::class => [
                        'logTable' => 'tx_groupmailer_log'
                    ]
                ]
            ]
        ];

        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths'][300] =
            'EXT:groupmailer/Resources/Private/Templates/Email';
        $GLOBALS['TYPO3_CONF_VARS']['MAIL']['layoutRootPaths'][300] =
            'EXT:groupmailer/Resources/Private/Layouts/Email';

        $icons = [
            [
                'identifier' => 'tx-groupmailer-backend-mail',
                'source' => 'EXT:groupmailer/Resources/Public/Icons/mail_backend.svg'
            ],
            [
                'identifier' => 'tx-groupmailer-frontend-mail',
                'source' => 'EXT:groupmailer/Resources/Public/Icons/mail_frontend.svg'
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
