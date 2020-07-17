<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-06 10:01:21
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 08:14:20
# Descripttion: 
#============================================================================= */
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
            exit();
        }
    }

    // public function appEnd() {
    //     echo '结束了';
    // }
}