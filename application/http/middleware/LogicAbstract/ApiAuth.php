<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 08:25:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 07:26:59
# Descripttion: 
#============================================================================= */
namespace app\http\middleware\LogicAbstract;

abstract class ApiAuth {

    // API模型
    abstract static public function getModel();


    // 忽略URL
    static public function getIgnores() {
        return [];
    }

}

