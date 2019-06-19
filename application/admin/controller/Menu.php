<?php
namespace app\admin\controller;
use think\Facade\Request;
class Menu extends Base {

    protected $validateName = 'Menu';
    protected $modelName = 'AdminMenu';
    // protected $table = 'AdminMenu';
    protected $logs = [
        'add' => '新增了栏目',
        'edit' => '编辑了栏目'
    ];

    public function index() {

    }

    public function test() {
        var_dump(Request::only(['title']));
        exit();
        // $this->request->tes2t = 222;
    }


}