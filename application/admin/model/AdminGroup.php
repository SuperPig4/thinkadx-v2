<?php

namespace app\admin\model;

use think\Model;

class AdminGroup extends Model {
    
    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    
    public function admin() {
        return $this->hasMany('admin', 'group_id');
    }

    // 修改器
    public function setRulesAttr($value) {
        if(is_array($value)) {
            return implode(',',$value);
        } else {
            return $value;
        }
    }

    // 获取器
    public function getStatusTextAttr() {
        if($this->getAttr('status') == 1) {
            return '正常';
        } else {
            return '暂停';
        }
    }




}
