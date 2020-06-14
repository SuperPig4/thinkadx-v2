<?php

namespace app\admin\logic\middleware;

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