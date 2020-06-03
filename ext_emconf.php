<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'groupmailer',
    'description' => '',
    'category' => 'services',
    'state' => 'stable',
    'uploadfolder' => true,
    'clearCacheOnLoad' => true,
    'author' => 'Sebastian Stein',
    'author_email' => 'sebastian.stein@in2code.de',
    'author_company' => 'in2code GmbH',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
