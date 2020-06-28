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
        'oauth_type' => 'require|in:password',
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
        'p' => 'number',
        // 头像 
        'avatar' => 'check_avatar',
        // 权限
        'group_id' => 'require|number',
        // 昵称
        'nickname' => 'require|min:4|max:30',
        // 状态
        'status' => 'check_status',
        // 新增时的密码
        'add_data_password' => 'check_add_data_password',
        // 删除ID
        'delete_id' => 'require|check_delete_id'
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
        'id' => 'id 只能为数字',
        'avatar' => '请上传图片',
        'group_id' => '请选择分组',
        'nickname' => '请输入昵称',
        'nickname.min' => '昵称最少5个字',
        'nickname.max' => '昵称最多30个字',
        'delete_id' => '请选择删除数据'
    ];

    protected $scene = [
        'delete' => ['delete_id'],
        'add_edit' => ['avatar', 'group_id', 'nickname', 'status', 'access'],
        'detail' => ['id'],
        'index' => ['p'],
        'login' => ['access', 'oauth_type', 'port_type'],
        'logout' => [''],
        'info' => [''],
        'rese_token' => ['refresh_token', 'old_token'],
        'set_password' => ['oauth_type', 'port_type', 'new_password','id']
    ];

    protected function check_avatar($value) {
        $validateRes = validate_file_url($value);
        if(is_string($validateRes)) {
            request()->avatar = $validateRes;
            return true;
        } else {
            return false;
        }
    }

    protected function check_status($value, $rule, $data) {
        if((isset($data['id']) && $data['id'] == 1) && $value == 0) {
            return '超级管理员状态不能切换为关闭';
        } else {
            return true;
        }
    }

    protected function check_add_data_password($value, $rule, $data) {
        if(empty($data['id']) && empty($value)) {
            return '请输入密码';
        } else {
            return true;
        }
    }

    protected function check_delete_id($value) {
        if(!empty($value)) {
            if(!is_numeric($value)) {
                if(is_array($value)) {
                    foreach($value as $val) {
                        if(!is_numeric($val)) {
                            return false;
                        }
                    }
                    return true;
                }
            } else {
                return true;
            }
        }
        return false;
    }

}
