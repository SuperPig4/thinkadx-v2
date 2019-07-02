<?php

namespace app\admin\validate;
use app\admin\model\Config as ConfigModel;
use think\Validate;

class Config extends Validate
{
    protected $rule = [
        // ID
        'id' => 'require|number',
        // 分页
        'p' => 'number',
        // 名
        'name' => 'require|min:4|max:30|check_value:name',
        // 别名
        'alias' => 'require|min:4|max:30|check_value:alias',
        // 类型
        'type' => 'require|in:system,app',
        // 描述
        'description' => 'require|max:200'
    ];


    protected $message = [
        'id' => '参数异常',
        'name' => '请输入配置名',
        'name.min' => '配置名最少4个字',
        'name.max' => '配置名最多30个字',
        'alias' => '请输入配置别名',
        'alias.min' => '配置别名最好4个字',
        'alias.max' => '配置别名最多30个字',
        'type' => '请选择合法类型',
        'description' => '请输入描述',
        'description.max' => '描述最多200个字',
    ];


    protected $scene = [
        'get_system_config' => [''],
        'add_edit' => ['name', 'alias', 'type', 'description'],
        'detail' => ['id'],
        'index' => ['p'],
    ];

    
    protected function check_value($value, $rule, $data) {
        $where = [
            [$rule, '=', $value]
        ];
        
        if(!empty($data['id'])) {
            $where[] = ['id', '<>', $data['id']];
        } 

        $count = ConfigModel::where($where)->count();
        if($count > 0) {
            if($rule == 'name') {
                return '当前配置名已被使用';
            } else if($rule == 'alias') {
                return '当前别名已被使用';
            }
        }

        return true;
    }
}
