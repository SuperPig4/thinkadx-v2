<?php

namespace app\admin\controller;
use think\Controller;
use think\Response;
class Base extends Controller {
    
    protected $middleware = ['CheckHeader', 'CheckAdmin'];

    //前置方法
    protected $beforeActionList = ['params_check'];

    public function __construct() {
        header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        header("Content-Type:text/html;charset=utf-8");
        parent::__construct();
       
        //验证方法是否存在
        $actionIsHave = method_exists($this, $this->request->action());
        if(empty($actionIsHave)) {
            error('illegal action');
        }
    }

    // public function index() {
    //     echo 'test';
    // }


    //检查参数
    protected function params_check() {
        if(!empty($this->validateName)) {
            $result = $this->validate($this->request->param(),$this->validateName.'.'.$this->request->action());
            if($result !== true) {
                error($result);
            }
        }
    }
    
}
