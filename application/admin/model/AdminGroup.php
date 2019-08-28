<?php

namespace app\admin\model;

use think\Model;
use think\facade\Cache;

class AdminGroup extends Model {
    
    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    
    public static function init() {
        $callback = function(){
            // 清空缓存
            Cache::clear('rule_tag_admin_rule');
        };
        self::event('after_write', $callback);
        self::event('after_delete', $callback);
    }
    
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
