<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 07:00:03
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 10:01:09
# Descripttion: 
#============================================================================= */

namespace app\admin\middleware\auth;

use app\http\middleware\LogicAbstract\auth\Session as SessionAbstract; 

class Session extends SessionAbstract {

 
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
        return \app\common\model\Admin::class;
    }

    /**
     * 验证不通过回调
     */
    static public function fail($data = '') {
        echo url('admin/adminUser/login');
        exit;
        // return redirect('http://www.thinkphp.cn');
    } 


    /**
     * 容器名
     */
    static public function containerName() {
        return 'adminData';
    }

}