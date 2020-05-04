<?php

use In2code\In2bemail\Domain\Model\Mailing;
use In2code\In2bemail\Domain\Model\MailQueue;

return [
    'ctrl' => [
        'title' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . MailQueue::TABLE,
        'label' => 'mailing',
        'label_alt' => 'be_user',
        'label_alt_force' => 1,
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
        'iconfile' => 'EXT:in2bemail/Resources/Public/Icons/' . MailQueue::TABLE . '.svg',
        'rootLevel' => -1
    ],
    'interface' => [
        'showRecordFieldList' => 'mailing,be_user,sent',
    ],
    'types' => [
        '1' => ['showitem' => 'mailing,be_user,sent'],
    ],
    'columns' => [
        'mailing' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . MailQueue::TABLE . '.mailing',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => Mailing::TABLE,
                'foreign_table_where' => 'AND sys_language_uid in (0,-1)',
                'default' => 0,
            ]
        ],
        'be_user' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . MailQueue::TABLE . '.be_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'be_users',
                'default' => 0,
            ]
        ],
        'sent' => [
            'exclude' => true,
            'label' => 'LLL:EXT:in2bemail/Resources/Private/Language/locallang_db.xlf:' . MailQueue::TABLE . '.sent',
            'config' => [
                'type' => 'check',
                'readOnly' => true
            ]
        ],
    ]
];
