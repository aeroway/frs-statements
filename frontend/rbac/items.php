<?php
return [
    'editMfc' => [
        'type' => 2,
        'description' => 'Can do actions as MFC user',
    ],
    'editZkp' => [
        'type' => 2,
        'description' => 'Can do actions as ZKP user',
    ],
    'editRosreestr' => [
        'type' => 2,
        'description' => 'Can do actions as Rosreestr user',
    ],
    'addAudit' => [
        'type' => 2,
        'description' => 'Can view more info',
    ],
    'confirmExtDocs' => [
        'type' => 2,
        'description' => 'Can confirm extraterritorial documents',
    ],
    'mfc' => [
        'type' => 1,
        'children' => [
            'editMfc',
        ],
    ],
    'zkp' => [
        'type' => 1,
        'children' => [
            'editZkp',
        ],
    ],
    'rosreestr' => [
        'type' => 1,
        'children' => [
            'editRosreestr',
        ],
    ],
    'audit' => [
        'type' => 1,
        'children' => [
            'addAudit',
        ],
    ],
    'extdocs' => [
        'type' => 1,
        'children' => [
            'confirmExtDocs',
        ],
    ],
];
