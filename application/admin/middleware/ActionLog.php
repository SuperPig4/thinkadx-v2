<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 09:06:34
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 10:12:20
# Descripttion: 日志写入中间件配置参数
#============================================================================= */
namespace app\admin\middleware;

use app\http\middleware\LogicAbstract\ActionLog as ActionLogAbstract; 
use app\admin\model\AdminLog as AdminLogModel;

class ActionLog extends ActionLogAbstract {

    public static function getModel() {
        return AdminLogModel::class;
    }

} 