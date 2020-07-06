<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-06 17:49:39
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-06 17:59:43
# Description: session 抽象类
# ============================================================================= */


namespace app\http\middleware\LogicAbstract\auth;

abstract class Session extends Base {

    // 缓存配置
    abstract static public function getStorageConfig();

    // oauth模型
    abstract static public function getOauthModel();

    // oauth模型的用户ID字段
    abstract static public function getOauthUserPk();
    
    
}