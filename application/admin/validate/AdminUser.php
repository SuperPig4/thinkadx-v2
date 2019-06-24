<?php

namespace app\admin\validate;
use think\Validate;

class AdminUser extends Validate
{
    
	protected $rule = [
        // 账号
        'access' => 'require|max:34',
        // 密码
        'password' => 'require|max:34',
        // 授权类型
        'oauth_type' => 'require|in:pwd',
        // 授权终端
        'port_type' => 'require|in:api', 
        // 刷新令牌
        'refresh_token' => 'require',
        // 旧的令牌
        'old_token' => 'require',
        // 新密码
        'new_password' => 'require|min:6',
        // ID
        'id' => 'number',
        // 分页
        'p' => 'number'
    ];
    
    protected $message = [
        'access' => '请输入账号',
        'access.max' => '请输入正确的账号',
        'password' => '请输入密码',
        'password.max' => '请输入正确的密码',
        'oauth_type' => '非法授权类型',
        'port_type' => '非法授权终端',
        'refresh_token' => 'refresh_token abnormal',
        'old_token' => 'old_token abnormal',
        'new_password' => '请输入新密码',
        'new_password.min' => '新密码最少6位数',
        'id' => 'id 只能为数字'
    ];

    protected $scene = [
        'index' => ['p'],
        'login' => ['access', 'oauth_type', 'port_type', 'password'],
        'info' => [''],
        'rese_token' => ['refresh_token', 'old_token'],
        'set_password' => ['oauth_type', 'port_type', 'new_password','id']
    ];

}
