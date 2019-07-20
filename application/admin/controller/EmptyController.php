<?php
namespace app\admin\controller;
use think\Controller;

class EmptyController extends Controller {

    protected $middleware = ['CheckAdmin', 'AdminAfter'];
    
    public function _empty() {
        try {
            $controller = controller($this->request->controller(), 'thinkadx');
            call_user_func(array($controller,$this->request->action()));
        } catch(\think\exception\ClassNotFoundException $e) {
            error('系统异常-1');
        }
    }

}
