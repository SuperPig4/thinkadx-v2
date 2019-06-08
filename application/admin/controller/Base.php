<?php

namespace app\admin\controller;
use think\Controller;
use think\Response;
class Base extends Controller {
    
    protected $middleware = ['CheckAdmin'];

    public function __construct() {
        parent::__construct();
        
        //验证方法是否存在
        $actionIsHave = method_exists($this, $this->request->action());
        if(empty($actionIsHave)) {
            error('illegal action');
        }
        
        if(!empty($this->validateName)) {
            $this->request->validateName = $this->validateName;
        }
    }

    // public function index() {
    //     echo 'test';
    // }


    
}
