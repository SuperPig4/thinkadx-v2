<?php
namespace app\common\behavior;
use think\facade\Request;
use think\facade\Response;

class CorsRun {
    public function appInit(){
        // 允许 $originarr 数组内的 域名跨域访问
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:*');
        // 带 cookie 的跨域访问
        header('Access-Control-Allow-Credentials: true');
        // 响应头设置
        header('Access-Control-Allow-Headers:*');
        
        if(Request::isOptions()){
            Response::header([
                'Access-Control-Allow-Headers' => 'content-type, nonce, sign, timestamp, token',
                'Access-Control-Max-Age' => '600',
                'Access-Control-Allow-Origin' => '*'
            ])->send();
            exit();
        }
        // 检测来源域名
        // $info = request::header();
        // if($info['host'] != 'www.thinkadx-v2.cn') {
        //     throw new \think\exception\HttpException(404, 'header域名不匹配');
        // }
    }


    public function appEnd($response){
        $response->header('Access-Control-Allow-Headers', '*');
        $response->header('Access-Control-Max-Age', '6000');
        $response->header('Access-Control-Allow-Origin', '*');
    }
}