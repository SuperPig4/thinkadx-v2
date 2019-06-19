<?php

namespace app\admin\controller;
use think\Controller;
use think\Response;
use think\Facade\Cache;

class Base extends Controller {
    
    protected $middleware = ['CheckAdmin', 'AdminAfter'];

    public function __construct() {
        parent::__construct();
      
        if($this->request->param('test')) {
            // var_dump(Cache::get('temp_token_admin_access_afdaf07662eada68dc66a48dccf702a7'));
            $key = 'd49c668c4b8696cfd9e05be062d463ea';
            echo Cache::rm('admin_access_'.$key);
            exit();
        }
        
        //验证方法是否存在
        $actionIsHave = method_exists($this, $this->request->action());
        if(empty($actionIsHave)) {
            error('illegal action');
        }

        if(!empty($this->validateName)) {
            $this->request->validateName = $this->validateName;
        }

    }
    
    // 公共列表
    

    // 公共编辑写
    public function add_edit() {
        $data = $this->request->param();
        var_dump($data);
        exit();
    }


    
}
