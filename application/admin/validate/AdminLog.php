<?php

namespace app\admin\validate;

use think\Validate;

class AdminLog extends Validate
{
    protected $rule = [
        // 分页
        'p' => 'number'
    ];
    
    protected $message = [
    ];

    protected $scene = [
        'index' => ['p'],
    ];
}
