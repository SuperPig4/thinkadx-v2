<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 07:00:03
# LastEditors: 奔跑猪
# LastEditTime: 2020-09-16 05:47:30
# Descripttion: 
#============================================================================= */

namespace app\admin\middleware\auth;

use app\common\model\AdminOauth;
use app\http\middleware\LogicAbstract\auth\AdxToken as AdxTokenAbstract; 

class AdxToken extends AdxTokenAbstract {


    /**
     * 获得模型
     */
    static public function getModel() {
        return \app\common\model\Admin::class;
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
            'model'       => AdminOauth::get(app(self::containerName())->admin_oauth[0]->id)
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

    /**
     * 忽略
     */
    static public function getIgnores() {
        return [
            'Tool' => ['get_verify_img','get_verify_key'],
            'Index' => ['index'],
            'AdminUser' => ['login', 'rese_token'],
            'Upload' => ['index'],
            'Config' => ['get_system_config', 'get_system_config2'],
        ];
    }

    static public function getOauthTypeMap() {
        return true;
    }
}