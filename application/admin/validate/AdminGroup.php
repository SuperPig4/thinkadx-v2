<?php

namespace app\admin\validate;

use think\Validate;

class AdminGroup extends Validate {
    protected $rule = [
        // ID
        'id' => 'require|number',
        // 分页
        'p' => 'number'
    ];
    
    protected $message = [
        'id' => '参数异常'
    ];

    protected $scene = [
        'detail' => ['id'],
        'index' => ['p'],
    ];
}
