<?php

namespace app\admin\model;
use think\Model;

class Admin extends Model {
    
    protected $autoWriteTimestamp = true;
    protected $updateTime  = false;

    // 关联
    public function adminOauth() {
        return $this->hasMany('AdminOauth', 'admin_id');
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




}
