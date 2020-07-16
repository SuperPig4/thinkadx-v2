<?php 
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 09:57:47
# Descripttion: 
#============================================================================= */

return [

    // 常规session
    // [
    //     \app\http\middleware\auth\Session::class, 
    //     [
    //         \app\admin\middleware\auth\Session::class,
    //         [
    //             'Tool' => ['get_verify_img','get_verify_key'],
    //             // 'Index' => ['index'],
    //             'AdminUser' => ['login', 'rese_token'],
    //             'Upload' => ['index'],
    //             'Config' => ['get_system_config'],
    //         ]
    //     ]
    // ], 

    // API鉴权 + token验证
    [
        \app\http\middleware\ApiAuth::class, 
        [
            'Tool' => ['get_verify_img']
        ]
    ], 
    [
        \app\http\middleware\auth\AdxToken::class, 
        [
            \app\admin\middleware\auth\AdxToken::class,
            [
                'Tool' => ['get_verify_img','get_verify_key'],
                'Index' => ['index'],
                'AdminUser' => ['login', 'rese_token'],
                'Upload' => ['index'],
                'Config' => ['get_system_config', 'get_system_config2'],
            ]
        ]
    ], 


    // 操作后的日志写入
    [
        \app\http\middleware\ActionLog::class,
        \app\admin\middleware\ActionLog::class
    ]
];