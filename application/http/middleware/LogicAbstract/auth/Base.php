<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-06 16:31:09
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-06 18:02:20
# Description: 认证抽象基类
# ============================================================================= */

namespace app\http\middleware\LogicAbstract\auth;

abstract class Base {

    /**
     * 获得缓存数据标识符名(类似 session.admin)
     * 
     * @return String
     */
    abstract static public function getAuthDataName();

    /**
     * 标识符相关联的模型
     * 
     * @return Class/String
     */
    abstract static public function getModel();

    /**
     * 验证不通过回调
     */
    abstract static public function fail($data = '');

    /**
     * 缓存数据中的主键
     */
    static public function getPk() {
        return 'id';
    }

    // 忽略URL
    static public function getIgnores() {
        return [];
    }

}