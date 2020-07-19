<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 07:00:03
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-19 11:28:44
# Descripttion: 
#============================================================================= */

namespace app\admin\controller;

use app\admin\model\AdminLog as AdminLogModel;
use app\admin\validate\AdminLog as AdminLogValidate;

class AdminLog extends Base {
    
    protected $validateName = AdminLogValidate::class;
    protected $modelName    = AdminLogModel::class;

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|admin_id', 'like', '%'.$data['search'].'%');
        }
        return $db->with('admin');
    }

}
