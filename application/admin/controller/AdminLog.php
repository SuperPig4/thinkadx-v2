<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:31:17
# Description: 操作日志
# ============================================================================= */

namespace app\admin\controller;

use app\common\model\AdminLog as AdminLogModel;
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
