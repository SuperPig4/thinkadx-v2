<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-06-05 16:07:58
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:42:00
# Description: 
# ============================================================================= */

namespace app\admin\controller;

use think\Controller;
use Thinkadx\Traits\ControllerBaseTrait;

class Base extends Controller {
    
    use ControllerBaseTrait;

    public function initialize() {
        // 验证方法
        $actionIsHave = method_exists($this, $this->request->action());
        if(empty($actionIsHave)) {
            error('illegal action');
        }

        // 验证器中间件
        if(!empty($this->validateName)) {
            $this->middleware = [
                [
                    \app\http\middleware\AutoValidate::class,
                    [
                        \app\admin\middleware\AutoValidate::class,
                        $this->validateName
                    ]
                ]
            ];
        }
    }

}
