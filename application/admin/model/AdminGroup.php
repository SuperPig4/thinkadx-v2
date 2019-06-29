<?php

namespace app\admin\model;

use think\Model;

class AdminGroup extends Model {
    
    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    protected $createTime = 'create_time';
    
    public function admin() {
        return $this->hasMany('admin', 'group_id');
    }

    // 修改器
    public function setRulesAttr($value) {
        return implode(',',$value);
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
