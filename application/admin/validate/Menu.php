<?php
namespace app\admin\validate;
use think\Validate;

class Menu extends Validate {
    
	protected $rule = [
        // icon 
        'icon' => 'require|check_icon',
        // 标题
        'title' => 'require|max:34',
        // 模块
        'module' => 'require|max:34',
        // 控制器
        'controller' => 'require|max:34',
        // 操作
        'action' => 'require|max:34',
        // 上级ID
        'father_id' => 'require|check_father_id'
    ];
    
    protected $message = [
        'icon' => 'icon 字段必须存在',
        'title' => '请输入标题',
        'title.max' => '标题最长30个字符串',
        'module' => '请输入模块',
        'module.max' => '模块最长30个字符串',
        'controller' => '请输入控制器',
        'controller.max' => '控制器最长30个字符串',
        'action' => '请输入方法',
        'action.max' => '方法最长30个字符串',
        'father_id' => 'father_id 字段必须存在'
    ];

    protected $scene = [
        'add_edit' => ['icon', 'title', 'module', 'controller', 'action', 'father_id']
    ]
}