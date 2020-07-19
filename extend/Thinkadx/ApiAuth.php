<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-17 02:52:58
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-19 09:12:04
# Descripttion: API鉴权 
                签名流程 -> strtoupper(md5($appid . $timestamp . $appsecret))
                            url拼接->md5->转小写
                注意:目前只支持redis
# errorCode
    1000 : 缺少验证参数
    1001 : 缺少标识符
#============================================================================= */
namespace Thinkadx;

use think\facade\Request;
use think\facade\Cache;

class ApiAuth {
    
    // appid
    private $appid;
    // 密钥
    private $appsecret;
    // 时间戳 - 微秒
    private $timestamp;
    // 签名
    private $sign;
    // 随机数
    private $nonce;
    // url有效期
    private $timeOut = 30;

    // 实例化对象
    private static $e;


    /**
     * 自动设置参数
     */
    protected function autoSetParams() {
        $header = Request::header();
        if(empty($header['timestamp']) || empty($header['nonce']) || empty($header['sign'])) {
            throw new \think\Exception('缺少验证参数', 1000);
        }

        $this->timestamp = $header['timestamp'];
        $this->nonce = $header['nonce'];
        $this->sign = $header['sign'];

        return $this;
    }


    /**
     * 检查
     * 
     * @return bool/String
     */
    protected function check() {
        if(empty($this->appid) || empty($this->appsecret)) {
            throw new \think\Exception('缺少标识符', 1001);
        }

        // 检测时间戳
        $checkTimeResult = $this->checkTimestamp();
        if($checkTimeResult !== true) {
            return $checkTimeResult;
        }
        
        // 判断签名是否通过
        $checkSignResult = $this->checkSign();
        if($checkSignResult !== true) {
            return $checkSignResult;
        }

        // 判断是否重放
        $checkRepeatResult = $this->checkRepeat();
        if($checkRepeatResult !== true) {
            return $checkRepeatResult;
        }
        
        // 缓存
        $name = 'appid=' . $this->appid . '/time_out=' . $this->timeOut . '/nonce='. $this->nonce .'/sign=' . $this->sign;
        Cache::store('redis')->set($name, time(), $this->timeOut);
        
        return true;
    }


    /**
     * 检测签名
     * 
     * @return bool/string
     */
    protected function checkSign() {
        $signData = http_build_query([
            'appid' => $this->appid,
            'timestamp' => $this->timestamp,
            'nonce' => $this->nonce,
            'appsecret' => $this->appsecret,
        ]);
        
        if(strtoupper(md5($signData)) == $this->sign) {
            return true;
        } else {
            return '签名错误';
        }
    }


    /**
     * 检测重放
     * 
     * @return bool/string
     */
    protected function checkRepeat() {
        $name = 'appid=' . $this->appid . '/time_out=' . $this->timeOut . '/nonce=' . $this->nonce . '/sign=' . $this->sign;
        if(Cache::store('redis')->get($name)) {
            return '链接已被使用';
        } else {
            return true;
        }
    }


    /**
     * 检查时间戳是否过期
     */
    protected function checkTimestamp() {
        $timestamp = $this->timestamp;
        if(is_numeric($timestamp) == false) {
            return '链接非法';
        } else if(((int)($timestamp) + $this->timeOut) < time()) {
            return '链接超时';
        }
        return true;
    } 


    /**
     * 设置APPID
     */
    protected function setAppid($value) {
        $this->appid = $value;
    }


    /**
     * 设置appsecret
     */
    protected function setAppsecret($value) {
        $this->appsecret = $value;
    }


    /**
     * 设置时间戳
     */
    protected function setTimestamp($value) {
        $this->timestamp = $value;
    }
    

    /**
     * 设置随机数
     */
    protected function setNonce($value) {
        $this->nonce = $value;
    }

    
    /**
     * 设置签名
     */
    protected function setSign($value) {
        $this->sign = $value;
    }

    
    public function __construct() {
        $this->redisHandler  = Cache::store('redis')->handler();
    }


    public function __call($name, $args) {
        $result = call_user_func_array([$this, $name], $args);
        if(empty($result)) {
            return $this;
        } else {
            return $result;
        }
    }


    public static function __callStatic($name, $args) {
        if(empty(self::$e)) {
            self::$e = new self();
        }

        return call_user_func_array([
            self::$e,
            $name
        ], $args);
    }

}
