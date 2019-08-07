<?php

namespace app\admin\validate;

use Thinkadx\Captcha\Main as CaptchaMain;
use think\Validate;

class Login extends Validate {

	protected $rule = [
        'password' => 'require|max:34',
        'verify_key' => 'require',
        'verify_code' => 'require|check_verify',
    ];
    
    protected $message = [
        'password' => '请输入密码',
        'password.max' => '请输入正确的密码',
        'verify_key' => '图片验证码异常',
        'verify_code' => '请输入图片验证码',
    ];

    protected $scene = [
        'api_pwd' => ['password', 'verify_key', 'verify_code']
    ];

    protected function check_verify($value, $rule, $data) {
        $runStr = CaptchaMain::check($data['verify_key'], $value);
        if(is_string($runStr)) {
            return $runStr;
        } else {
            return true;
        }
    }

}
