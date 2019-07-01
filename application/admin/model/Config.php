<?php

namespace app\admin\model;

use think\facade\Cache;
use think\Model;

class Config extends Model {
    
    public static function init() {
        self::event('after_write', function($e){
            // 清空配置缓存
            Cache::clear('system_config');
        });
    }


    public function getTypeTextAttr($value) {
        if($this->getAttr('type') == 'system') {
            return '系统';
        } else if($this->getAttr('type') == 'app') {
            return '应用';
        } else {
            return '未知';
        }
    }

}
