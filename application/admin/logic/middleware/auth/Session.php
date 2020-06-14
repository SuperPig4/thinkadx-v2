<?php

namespace app\admin\logic\middleware\auth;

use app\http\middleware\auth\LogicConstraint;

class Session extends LogicConstraint{

    /**
     * 操作对象名
     */
    // static public function getActionName() {
    //     return 'admin';
    // }

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
     * 获得主键
     */
    static public function getPk() {
        return 'id';
    }

    /**
     * 验证不通过回调
     */
    static public function fail($data = '') {
        echo url('admin/adminUser/login');exit;
        // return redirect('http://www.thinkphp.cn');
    } 


    /**
     * 容器名
     */
    static public function containerName() {
        return 'adminData';
    }

}