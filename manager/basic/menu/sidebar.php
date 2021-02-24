<?php
/**
 * sidebar
 * sidebar.php
 */

return
[
    # Dashboard
    'dashboard'=>[

    ],

    # Operation
    'operation'=>[
        'level'=>80000,
        'character'=>'manager',
    ],

    'operationMedia'=>[
        'level'=>80000,
        'character'=>'manager',
    ],

    'operationUser'=>[
        'level'=>90000,
        'character'=>'manager',
    ],

    'managerArea'=>[
        'level'=>90000,
    ],

    'managerAdmin'=>[
        'level'=>80000,
        'character'=>'manager'
    ],

    'managerEditor'=>[
        'level'=>80000
    ],

    # Setting
    'setting'=>[
        'level'=>90000,
        'character'=>'manager'
    ],

    'settingConfig'=>[
        'level'=>90000,
        'character'=>'super'
    ],

    'settingApiTest'=>[
        'level'=>90000,
        'character'=>'super'
    ],

    'settingDatabase'=>[
        'level'=>90000,
        'character'=>'super'
    ]
];