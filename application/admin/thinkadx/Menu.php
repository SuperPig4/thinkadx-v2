<?php
namespace app\admin\thinkadx;
use think\facade\Request;
use app\admin\model\AdminMenu;
use Thinkadx\RuleAuth\Main;

class Menu extends Base {

    protected $validateName = 'Menu';
    protected $modelName = 'AdminMenu';
    protected $logs = [
        'add' => '新增了栏目',
        'edit' => '编辑了栏目'
    ];

    public function index() {
        $list = AdminMenu::all();
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
        AdminMenu::where('id', $id)->whereOr('father_id', $id)->delete();
        $this->request->act_log = '删除了菜单分类';
        success('ok!');
    }


    // 获得列表
    public function get_list() {
        $list = AdminMenu::where('status', 1)->select();
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
            foreach($list as $key=>&$val) {
                if(empty($val['children'])) {
                    unset($list[$key]);
                }
            }
            success('ok!',$list);
        }
    }

}