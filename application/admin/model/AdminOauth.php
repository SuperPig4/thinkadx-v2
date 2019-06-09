<?php

namespace app\admin\model;
use think\Model;
use think\facade\Request;
use think\facade\Cache;

class AdminOauth extends Model {
    
    protected $autoWriteTimestamp = true;
    protected $updateTime  = false;

    public function admin() {
        return $this->belongsTo('admin', 'admin_id');
    }

    /**
     * 密码方式授权数据基本配置
     * @param string $pwd 密码
     */
    public static function getPasswordBaseConfig($pwd) {
        $salt = mt_rand(111111111, 999999999);
        return [
            'identifier' => md5($pwd.$salt),
            'unique_identifier' => $salt,
            'oauth_type' => 'pwd',
            'port_type' => 'api'
        ];
    }


    /**
     * 登陆 - 调用了request
     * @return string/array
     */
    public function login() {
        $data = Request::param();
        $mapping = ['api_pwd' => 'loginPassword'];
        $loginType = $data['port_type'] . '_' . $data['oauth_type'];
        $loginFunRes = call_user_func(array($this, $mapping[$loginType]), $data);
        
        if(is_string($loginFunRes)) {
            return $loginFunRes;
        }
        
        $runData = [];
        $runData['access_token'] = $this->resetToken('access');
        $runData['refresh_token'] = $this->resetToken('refresh');
        return $runData;
    }

    /**
     * 密码登陆
     * @return bool/string
     */
    protected function loginPassword($data) {
        $inputPasswordMd5 = md5($data['password'] . $this->getAttr('unique_identifier'));
        if($inputPasswordMd5 != $this->getAttr('identifier')) {
            return '密码错误';
        } else {
            return true;
        }   
    }


    /**
     * 刷新令牌
     * @param string $tokenType 令牌类型 access:访问 refresh:刷新令牌
     */
    public function resetToken($tokenType, $isDelOld = true) {
        $token = $this->getAttr($tokenType.'_token');
        $tokenTimeOut = system_config('system.admin_'.$tokenType.'_token_time_out');
        $newToken = $this->getNewToken($tokenType);
        $updateData = [
            $tokenType.'_token' => $newToken,
            $tokenType.'_token_create_time' => time()    
        ];

        if($token && $isDelOld === true) {
            // 如果是访问令牌,则记录本次刷新
            if($tokenType == 'access') {
                $updateData['last_use_access_token'] = $this->getAttr('access_token');
            }
            Cache::rm('admin_'.$tokenType.'_'.$token); 
        }
        $this->save($updateData);
        Cache::tag('admin_token')->set('admin_'.$tokenType.'_'.$newToken, $this->getAttr('admin_id'), $tokenTimeOut);
        return [
            'expired' => $tokenTimeOut,
            'token' => $newToken
        ];
    }


    /**
     * 获得新的令牌
     * @param string $tokenType 令牌类型 access:访问 refresh:刷新令牌
     */
    private function getNewToken($tokenType) {
        $str = md5($tokenType.$this->getAttr('admin_id').$this->getAttr('identifier').mt_rand(11111,99999));
        return $str;
    }


}
