<?php
/*
 * @Descripttion: 
 * @version: 
 * @Author: 奔跑猪
 * @Date: 2020-06-06 10:01:21
 * @LastEditTime: 2020-07-15 08:35:10
 */ 
namespace app\admin\controller;
use think\Controller;

class EmptyController extends Controller {

    public function __construct() {
        parent::__construct();
        try {
            $this->controller = controller($this->request->controller(), 'thinkadx');
        } catch(\think\exception\ClassNotFoundException $e) {
            error('系统异常-1');
        }
    }

    public function _empty() {
        call_user_func(array($this->controller,$this->request->action()));
    }

}
