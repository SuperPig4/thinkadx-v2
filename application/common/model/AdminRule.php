<?php
namespace app\common\model;

use think\facade\Cache;
use think\Model;

class AdminRule extends Model {
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

    public function setRuleAttr($value) {
        return strtolower($value);
    }
}
