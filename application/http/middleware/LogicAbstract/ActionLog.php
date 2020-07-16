<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 08:25:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 10:00:01
# Descripttion: 
#============================================================================= */
namespace app\http\middleware\LogicAbstract;

abstract class ActionLog {

    // 存储字段
    static public function getField() {
        return 'des';
    }
    
    // 存储模型
    abstract static public function getModel();

}

