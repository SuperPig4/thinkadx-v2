<?php
/**
 * 密码模式
 * @desc
 *  1.常见的密码登录
 *
 */
namespace Thinkadx\Oauth2\Mode;

use think\facade\Validate;

class Password extends Base{

    public $modeName = 'password';


    /**
     * 登录
     * 
     * 条件
     *  - 相对标识符 - 未加密密码
     *  - 绝对标识符 - 盐
     *  - 缓存数据
     *  - 模型实例
     * 
     * @return bool/array
     */
    public function login() {
        $password = md5($this->id . $this->uniqueId);

        if($this->main->userOauthModel->identifier == $password) {
            // 刷新全部令牌
            $access = $this->create_access_token();
            $refresh = $this->create_refresh_token([
                'token' => $access['token']
            ]);
            return [ $access, $refresh ];
        } else {
            return false;
        }
    }

    public function create_access_token($options = []) {
        $accessValue  = $this->main->createToken($this->main->userOauthModel->id . 'access');
        return $this->main->resetToken('access', $accessValue);
    }

    public function create_refresh_token($options = []) {
        $refreshValue = $this->main->createToken($this->main->userOauthModel->id . 'refresh');
        return $this->main->resetToken('refresh', $refreshValue, [
            'access_token' => $options['token']
        ]);
    }

}

