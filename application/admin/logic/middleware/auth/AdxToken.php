<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-06 16:31:09
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-06 22:20:13
# Description: oauth中间件配置类
# ============================================================================= */

namespace app\admin\logic\middleware\auth;

use app\admin\model\AdminOauth;
use app\http\middleware\LogicAbstract\auth\AdxToken as AdxTokenAbstract; 

class AdxToken extends AdxTokenAbstract {

    /**
     * 缓存数据名
     */
    static public function getAuthDataName() {
        return 'admin';
    }

    /**
     * 获得模型
     */
    static public function getModel() {
        return \app\admin\model\Admin::class;
    }

 
    /**
     * 验证不通过回调
     */
    static public function fail($data = '') {
        return error('没有权限', [], $data);
    } 

    static public function containerName() {
        return 'adminData';
    }

    // 缓存配置
    static public function getStorageConfig() {
        return [
            'modelClass'  => AdminOauth::class,
            'model'       => app('adminData')->admin_oauth[0]
        ];
    }

    // oauth 模型
    static public function getOauthModel() {
        return AdminOauth::class;
    }

    // oauth模型的用户ID字段
    static public function getOauthUserPk() {
        return 'admin_id';
    }

}