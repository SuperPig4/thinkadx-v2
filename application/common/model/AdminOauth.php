<?php

namespace app\common\model;

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
            'oauth_type' => 'password',
            'port_type' => 'api'
        ];
    }



}
