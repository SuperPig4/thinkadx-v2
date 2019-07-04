<?php

namespace app\admin\validate;

use think\Validate;

class Login extends Validate {

	protected $rule = [
        'password' => 'require|max:34',
    ];
    
    protected $message = [
        'password' => '请输入密码',
        'password.max' => '请输入正确的密码',
    ];

    protected $scene = [
        'api_pwd' => 'password'
    ];
}
