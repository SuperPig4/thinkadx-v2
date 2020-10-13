<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-06 10:01:21
# LastEditors: 奔跑猪
# LastEditTime: 2020-09-29 00:10:51
# Descripttion: 
#============================================================================= */
namespace app\common\behavior;
use think\facade\Request;
use think\facade\Response;
use think\route\Rule;

class CorsRun {

    protected $headers = [
        'Access-Control-Allow-Origin' => '*',
        'Access-Control-Allow-Methods' => '*',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Headers' => '*',
    ];

    public function appInit(){
        // 判断是否开启调试模式
        if(config('app_debug')) {
            // 直接设置响应头 方便调试看错误
            header('Access-Control-Allow-Origin:*');
            header('Access-Control-Allow-Methods:*');
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Allow-Headers:*');
        } 

        if(Request::isOptions()){
            Response::header($this->headers)->code(200)->send();
            exit;
        }
    }

    public function responseSend($response) {
        $response->header($this->headers);
    }
}