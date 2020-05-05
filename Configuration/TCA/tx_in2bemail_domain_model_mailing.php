<?php

use In2code\In2bemail\Domain\Model\Mailing;
use TYPO3\CMS\Core\Mail\FluidEmail;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE,
        'label' => 'subject',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'default_sortby' => 'ORDER BY tstamp ASC',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'iconfile' => 'EXT:in2bemail/Resources/Public/Icons/' . Mailing::TABLE . '.svg',
        'rootLevel' => -1
    ],
    'interface' => [
        'showRecordFieldList' => 'be_groups,subject,bodytext,mail_format,sender_mail,sender_name,mail_queue_generated',
    ],
    'types' => [
        '1' => ['showitem' => 'be_groups,subject,bodytext,mail_format,--palette--;LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.sender;sender,mail_queue_generated'],
    ],
    'palettes' => [
        'sender' => [
            'showitem' => 'sender_mail,--linebreak--,sender_name,',
            'canNotCollapse' => 1,
        ],
    ],
    'columns' => [
        'be_groups' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.be_groups',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'be_groups',
                'MM' => 'tx_in2bemail_mailing_be_groups_mm',
                'foreign_table_where' => 'ORDER BY title ASC',
                'size' => 5,
                'minitems' => 1,
            ]
        ],
        'subject' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.subject',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim,required',
                'max' => 255,
            ],
        ],
        'bodytext' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.bodytext',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim,required',
            ],
        ],
        'mail_format' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.mail_format',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.mail_format.' . FluidEmail::FORMAT_BOTH,
                        FluidEmail::FORMAT_BOTH
                    ],
                    [
                        'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.mail_format.' . FluidEmail::FORMAT_HTML,
                        FluidEmail::FORMAT_HTML
                    ],
                    [
                        'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.showinviews.' . FluidEmail::FORMAT_PLAIN,
                        FluidEmail::FORMAT_PLAIN
                    ],
                ],
                'default' => FluidEmail::FORMAT_BOTH,
            ]
        ],
        'sender_mail' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.sender_mail',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'sender_name' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.sender_name',
            'config' => [
                'type' => 'input',
                'size' => 40,
                'eval' => 'trim',
                'max' => 255,
            ],
        ],
        'mail_queue_generated' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . Mailing::TABLE . '.mail_queue_generated',
            'config' => [
                'type' => 'check',
                'readOnly' => true
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ]
        ]
    ]
];
