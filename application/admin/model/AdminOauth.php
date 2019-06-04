<?php

namespace app\admin\model;

use think\Model;

class AdminOauth extends Model {
    
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

}
