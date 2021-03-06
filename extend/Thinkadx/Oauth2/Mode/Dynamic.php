<?php
/**
 * 动态模式
 * @desc
 *  1.可用于自定义模式,适用场景 微信(小程序/公众号)授权登录、
 *
 */
namespace Thinkadx\Oauth2\Mode;

use think\facade\Validate;

class Dynamic extends Base{

    public $modeName = 'dynamic';


    /**
     * 登录
     * 
     * 条件
     *  - 相对标识符
     *  - 绝对标识符
     *  - 缓存数据
     *  - 模型实例
     * 
     * @return bool/array
     */
    public function login() {
        if(
            $this->main->userOauthModel->identifier == $this->id 
            && 
            (is_null($this->uniqueId) === false || $this->main->userOauthModel->unique_identifier == $this->uniqueId)
           ) {

            // 刷新全部令牌
            $access = $this->create_access_token();
            $refresh = $this->create_refresh_token([
                'token' => $access['token']
            ]);

            return [ 
                'access'  => $access, 
                'refresh' => $refresh 
            ];
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

