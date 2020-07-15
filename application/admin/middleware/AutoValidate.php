<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:40:16
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:40:51
# Description: 验证器中间件配置类
# ============================================================================= */

namespace app\admin\middleware;

use think\Response;
use think\exception\HttpResponseException;

class AutoValidate {

    /**
     * 验证不通过回调
     */
    static public function fail($error = '', $e = null) {
        error($error);
    } 

    // 验证类型
    // static public function getMethod() {
    //     return 'POST';
    // }

}