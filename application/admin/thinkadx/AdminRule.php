<?php

namespace app\admin\thinkadx;

class AdminRule extends Base {

    protected $validateName = true;
    protected $logs = [
        'add' => '新增了规则',
        'edit' => '编辑了规则',
        'delete' => '删除了规则'
    ];

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|des', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC');
    }

}
