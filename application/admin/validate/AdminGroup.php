<?php

namespace app\admin\validate;
use app\admin\model\Admin as AdminModel;
use think\Validate;

class AdminGroup extends Validate {
    protected $rule = [
        // ID
        'id' => 'require|number',
        // 分页
        'p' => 'number',
        // 分组名
        'name' => 'require|min:4|max:20|check_name'
    ];
    
    protected $message = [
        'id' => '参数异常',
        'name' => '请输入分组名',
        'name.min' => '分组名最少4个字',
        'name.max' => '分组名最多20个字',
        'name.check_name' => '当前分组名已被使用',
    ];

    protected $scene = [
        'add_edit' => ['name'],
        'detail' => ['id'],
        'index' => ['p'],
    ];

    // 自定义场景
    protected function sceneDelete() {
        $id = request()->param('id/a');
        
        return $this->only(['id'])
        ->append('id', 'check_id_use')
        ->remove('id', 'number');
    }

    
    // 自定义规则
    protected function check_id_use($value, $rule, $data) {
        if((!empty($value)) && (is_numeric($value) || is_array($value))) {
            if(is_numeric($value)) {
                $ids[] = $value;
            } else {
                $ids = $value;
            }

            foreach($ids as $item) {
                if($item == 1) {
                    return '1号分组禁止删除';
                }
            }

            $useCount = AdminModel::where('group_id', 'in', $ids)->count();
            if($useCount > 0) {
                return '无法删除被使用中的分组';
            }
            return true;
        }
        return '参数类型异常';
    }


    protected function check_name($value, $rule, $data) {
        if(empty($data['id'])) {
            if(db('admin_group')->where('name',$value)->count() <= 0) {
                return true;
            }
        } else {
            return true;
        }
        return false;
    }
    
}
