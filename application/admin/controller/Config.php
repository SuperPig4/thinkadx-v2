<?php

namespace app\Admin\controller;

class Config extends Base {   

    protected $validateName = true;
    protected $logs = [
        'add' => '新增了系统配置',
        'edit' => '编辑了系统配置'
    ];

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|name', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC')->append(['type_text']);
    }
}
