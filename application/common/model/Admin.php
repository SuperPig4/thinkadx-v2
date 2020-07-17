<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 07:00:03
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 07:17:27
# Descripttion: 
#============================================================================= */
namespace app\common\model;

use think\Model;

class Admin extends Model {
    
    protected $autoWriteTimestamp = true;
    protected $updateTime  = false;
    protected $readonly = ['access', 'create_time'];

    // 关联
    public function adminOauth() {
        return $this->hasMany('AdminOauth', 'admin_id');
    }


    public function adminLog() {
        return $this->hasMany('AmindLog', 'admin_id');
    }


    // 获取器
    public function getAvatarAttr($value, $data) {
        if(empty($value)) {
            $value = '/uploads/system_default_icon/avatar.png';
        }
        return [
            'url' => local_path_turn_url($value),
            'path' => $value
        ];
    }

    public function getStatusTextAttr() {
        if($this->getAttr('status') == 1) {
            return '正常';
        } else {
            return '暂停';
        }
    }




}
