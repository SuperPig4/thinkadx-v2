<?php

namespace app\admin\controller;
use think\Request;
use app\admin\model\AdminGroup as AdminGroupModel;
use app\admin\model\Admin;

class AdminGroup extends Base {
    
    protected $validateName = true;
    protected $logs = [
        'add' => '新增了分组',
        'edit' => '编辑了分组',
        'delete' => '删除了分组'
    ];

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|name', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC')->append(['status_text']);
    }

}
