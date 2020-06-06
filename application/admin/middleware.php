<?php 
return [
    [
        \app\http\middleware\ApiAuth::class, 
        [
            'Tool' => ['get_verify_img']
        ]
    ], 
    [
        \app\http\middleware\AutoValidate::class,
        []
    ],
    [
        \app\http\middleware\auth\AdxToken::class, 
        [
            \app\admin\logic\middleware\auth\AdxToken::class,
            [
                'Tool' => ['get_verify_img','get_verify_key'],
                'Index' => ['index'],
                'AdminUser' => ['login', 'rese_token'],
                'Upload' => ['index'],
                'Config' => ['get_system_config'],
            ]
        ]
    ], 
    [
        \app\http\middleware\ActionLog::class,
        \app\admin\model\AdminLog::class
    ]
];