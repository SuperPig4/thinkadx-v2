<?php
namespace app\admin\controller;
use think\Controller;
use think\Request;

use Thinkadx\RuleAuth\Main;
class Index extends Base {

    public function index()
    {
        $test = new Main('admin_rule', 'admin_group', 'admin', 1);
        $test->check();
        // $test = new ParamsChcke();
        exit();
        return success('ces');
    }
}
