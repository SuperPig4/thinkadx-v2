<?php

namespace app\admin\controller;
use app\admin\model\AdminLog as AdminLogModel;
use think\Controller;
use think\Request;

class AdminLog extends Base {
    
    protected $validateName = true;

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|admin_id', 'like', '%'.$data['search'].'%');
        }
        return $db->with('admin');
    }

}
