<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 09:06:34
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 07:27:19
# Descripttion: 日志写入中间件配置参数
#============================================================================= */
namespace app\admin\middleware;

use app\http\middleware\LogicAbstract\ApiAuth as ApiAuthAbstract; 
use app\common\model\AdminApi as AdminApiModel;

class ApiAuth extends ApiAuthAbstract {

    static public function getModel() {
        return AdminApiModel::class;
    }

    
    static public function getIgnores() {
        return [
            'Tool' => ['get_verify_img']
        ];
    }

} 