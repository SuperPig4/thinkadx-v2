<?php

namespace app\admin\controller;
use think\Controller;
use think\Request;

class Base extends Controller {
    
    protected $middleware = ['CheckHeader', 'CheckAdmin'];

    public function index() {
        echo 'test';
    }
    
}
