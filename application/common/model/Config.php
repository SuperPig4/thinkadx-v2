<?php

namespace app\common\model;

use think\facade\Cache;
use think\Model;

class Config extends Model {
    
    public static function init() {
        $callback = function(){
            // 清空配置缓存
            Cache::clear('system_config');
            Cache::rm('controller_get_all_config');
        };
        self::event('after_write', $callback);
        self::event('after_delete', $callback);
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
