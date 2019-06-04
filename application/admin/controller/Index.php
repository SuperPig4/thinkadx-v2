<?php
namespace app\admin\controller;
use think\Controller;
use \ApiAuth\ParamsChcke;
class Index extends Base {

    public function index()
    {
        $test = new ParamsChcke();
        exit();
        return success('ces');
    }
}
