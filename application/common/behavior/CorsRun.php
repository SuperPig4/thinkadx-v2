<?php
namespace app\common\behavior;
use think\Facade\Request;
use think\Facade\Response;

class CorsRun {
    public function appInit(){
        
        Response::header([
            'Access-Control-Allow-Headers' => '*',
            'Access-Control-Max-Age' => '600',
            'Access-Control-Allow-Origin' => '*'
        ])->send();
        if(Request::isOptions()){
            exit();
        }

        // 检测来源域名
        // $info = request::header();
        // if($info['host'] != 'www.thinkadx-v2.cn') {
        //     throw new \think\exception\HttpException(404, 'header域名不匹配');
        // }
    }
}