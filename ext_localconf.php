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
    }
);
