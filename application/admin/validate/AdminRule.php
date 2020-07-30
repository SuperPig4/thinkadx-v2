<?php

namespace app\admin\validate;

use app\common\model\AdminGroup as AdminGroupModel;
use think\Validate;
use think\Db;

class AdminRule extends Validate {
    
    protected $rule = [
        // ID
        'id' => 'require|number',
        // 分页
        'p' => 'number',
        // 描述
        'des' => 'require|min:4|max:60'
    ];


    protected $message = [
        'id' => '参数异常',
        'des' => '请输入分组名',
        'des.min' => '描述最少4个字',
        'des.max' => '描述最多120个字'
    ];


    protected $scene = [
        'add' => ['des'],
        'edit' => ['id', 'des'],
        'detail' => ['id'],
        'index' => ['p'],
    ];


    // 自定义场景
    protected function sceneDelete() {
        return $this->only(['id'])
        ->append('id', 'check_id')
        ->remove('id', 'number');
    }


    protected function check_id($value, $rule, $data) {
        if((!empty($value)) && (is_numeric($value) || is_array($value))) {
            if(is_numeric($value)) {
                $ids[] = $value;
            } else {
                $ids = $value;
            }

            $db = Db::name('admin_group');
            foreach($ids as $item) {
                if($item == 1) {
                    return 'id为1的规则禁止删除';
                } else if(!is_numeric($item)) {
                    return '参数非法';
                } else {
                    $db->whereOr("FIND_IN_SET('{$item}', rules)");
                }
            }
            $useCount = $db->count();
            if($useCount > 0) {
                return '无法删除被使用中的规则';
            }
            return true;
        }
        return '参数非法';
    }

}
