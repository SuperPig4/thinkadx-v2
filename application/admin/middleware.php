<?php 
/*
 * @Descripttion: 
 * @version: 
 * @Author: 奔跑猪
 * @Date: 2020-06-14 19:58:10
 * @LastEditTime: 2020-07-15 08:48:34
 */ 
return [

    // 常规session
    // [
    //     \app\http\middleware\auth\Session::class, 
    //     [
    //         \app\admin\logic\middleware\auth\Session::class,
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
            \app\admin\logic\middleware\auth\AdxToken::class,
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
        \app\admin\model\AdminLog::class
    ]
];