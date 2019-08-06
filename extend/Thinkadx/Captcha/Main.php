<?php
namespace Thinkadx\Captcha;

use think\facade\Cache;

class Main {
    
    /**
     * 输出验证码
     * @param string $key 标识符
     * @param string $code 验证码内容 - 只支持数字和字母(大小写)
     * @param int    $expire 过期时间
     * @return 
     */
    public static function show($key, $code = '', $expire = 3600) {
        $img = new Create();
        $img->create($code);
        // 缓存验证码
        Cache::set($key, $img->get_code(), $expire);
        $img->show();
    }


    /**
     * 验证
     * @param string $key 标识符
     * @param string $val 
     * @return string/bool
     */
    public static function check($key, $val) {
        $code = Cache::get($key);
        if(empty($code)) {
            return '验证码过期或不存在,请刷新验证码';
        } else if($val != $code) {
            return '验证码错误';
        }
        Cache::rm($key);
        return true;
    }


}