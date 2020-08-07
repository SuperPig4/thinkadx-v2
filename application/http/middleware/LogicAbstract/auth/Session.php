<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-15 07:36:09
# LastEditors: 奔跑猪
# LastEditTime: 2020-08-08 00:58:48
# Descripttion: 
#============================================================================= */

namespace app\http\middleware\LogicAbstract\auth;

abstract class Session extends Base {

    /**
     * 获得缓存数据标识符名(类似 session.admin)
     * 
     * @return String
     */
    abstract static public function getAuthDataName();
    
}