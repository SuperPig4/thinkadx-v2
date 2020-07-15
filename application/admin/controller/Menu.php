<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:35:10
# Description: 菜单
# ============================================================================= */
namespace app\admin\controller;

use think\facade\Request;
use app\admin\model\AdminMenu as AdminMenuModel;
use app\admin\validate\Menu as MenuValidate;
use Thinkadx\RuleAuth\Main;

class Menu extends Base {

    protected $validateName = MenuValidate::class;
    protected $modelName    = AdminMenuModel::class;
    protected $logs = [
        'add' => [
            '新增了栏目',
            '新增栏目失败'
        ],
        'edit' => [
            '编辑了栏目',
            '编辑栏目失败'
        ]
    ];

    public function index() {
        $list = AdminMenuModel::all();
        if(empty($list)) {
            success('ok!',[]);
        } else {
            $mapping = [];
            $list = $list->toArray();
            $ruleAuth = new Main('admin_rule', 'admin_group', 'admin', USER_ID);
            foreach($list as $key=>&$val) {
                if($val['father_id'] != 0) {
                    $check = $ruleAuth->check($val['module'], $val['controller'], $val['action']);
                    if(!$check) unset($list[$key]);
                }
                $id = $val['id'];
                $fatherId = $val['father_id'];
                $mapping[$fatherId][] = &$val;
                $val['children'] = &$mapping[$id];
                if($val['father_id'] != 0) {
                    unset($list[$key]);
                }
            }
            
            // 过滤一级菜单没有子级的
            $list = array_values($list);
            // foreach($list as $key=>&$val) {
            //     if(empty($val['children'])) {
            //         unset($list[$key]);
            //     }
            // }
            success('ok!',$list);
        }
    }


    // 删除
    public function delete() {
        $id = $this->request->param('id/d');
        AdminMenuModel::where('id', $id)->whereOr('father_id', $id)->delete();
        $this->request->act_log = '删除了菜单分类';
        success('ok!');
    }


    // 获得列表
    public function get_list() {
        $list = AdminMenuModel::where('status', 1)->order('sort DESC')->select();
        if(empty($list)) {
            success('ok!',[]);
        } else {
            $mapping = [];
            $list = $list->toArray();
            $ruleAuth = new Main('admin_rule', 'admin_group', 'admin', USER_ID);
            foreach($list as $key=>&$val) {
                if($val['father_id'] != 0) {
                    $check = $ruleAuth->check($val['module'], $val['controller'], $val['action']);
                    if(!$check) {
                        unset($list[$key]);
                        continue;
                    }
                }
                $id = $val['id'];
                $fatherId = $val['father_id'];
                $mapping[$fatherId][] = $val;
                $val['children'] = &$mapping[$id];
                if($val['father_id'] != 0) {
                    unset($list[$key]);
                }
            }
            
            // 过滤一级菜单没有子级的
            $list = array_values($list);
            foreach($list as $key=>&$val) {
                if(empty($val['children'])) {
                    unset($list[$key]);
                }
            }
            success('ok!',$list);
        }
    }

}