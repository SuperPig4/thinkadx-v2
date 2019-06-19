<?php
namespace app\admin\controller;

class Menu extends Base {

    protected $validateName = 'Menu';
    protected $beforeActionList = [
        'test'
    ];

    public function index() {

    }

    public function test() {
        // $this->request->title = 222;
    }


}