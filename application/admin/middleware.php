<?php 
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 08:29:27
# Descripttion: 
#============================================================================= */

return [

    // 常规session
    // [
    //     \app\http\middleware\auth\Session::class, 
    //     \app\admin\middleware\auth\Session::class,
    // ], 

    // API鉴权
    [
        \app\http\middleware\ApiAuth::class, 
        \app\admin\middleware\ApiAuth::class
    ], 

    //  token验证
    [
        \app\http\middleware\auth\AdxToken::class, 
        \app\admin\middleware\auth\AdxToken::class,
    ], 

    // 操作后的日志写入
    [
        \app\http\middleware\ActionLog::class,
        \app\admin\middleware\ActionLog::class
    ]
];