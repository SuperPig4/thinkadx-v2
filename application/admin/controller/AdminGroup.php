<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:25:33
# Description: 管理员分组
# ============================================================================= */

namespace app\admin\controller;

use app\common\model\AdminGroup as AdminGroupModel;
use app\admin\validate\AdminGroup as AdminGroupValidate;

class AdminGroup extends Base {
    
    protected $validateName = AdminGroupValidate::class;
    protected $modelName    = AdminGroupModel::class;
    protected $logs         = [
        'add' => [
            '新增了分组',
            '新增分组失败'
        ],
        'edit' => [
            '编辑了分组',
            '编辑分组失败'
        ],
        'delete' => [
            '删除了分组',
            '删除分组失败'
        ]
    ];

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|name', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC')->append(['status_text']);
    }

}
