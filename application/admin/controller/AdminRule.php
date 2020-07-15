<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:24:54
# Description: 管理员规则
# ============================================================================= */

namespace app\admin\controller;

use app\common\model\AdminRule as AdminRuleModel;
use app\admin\validate\AdminRule as AdminRuleValidate;

class AdminRule extends Base {

    protected $validateName = AdminRuleValidate::class;
    protected $modelName    = AdminRuleModel::class;
    protected $logs = [
        'add' => [
            '新增了规则',
            '新增规则失败'
        ],
        'edit' => [
            '编辑了规则',
            '编辑规则失败'
        ],
        'delete' => [
            '删除了规则',
            '删除规则失败'
        ]
    ];

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|des', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC');
    }

}
