<?php
namespace Thinkadx\ApiAuth;
use think\facade\Cache;

/**
 * 接口鉴权加密
 */
class ParamsChcke {

    private static $header = [];
    private static $post = [];
    private static $files = [];
    private static $apiTimeOut = 30;
    private static $nonce;
    private static $appsecret = '';

    public static function setAppSecret($appsecret) {
        self::$appsecret = $appsecret;
    }

    public static function setHeader($data = []) {
        if(is_array($data)) {
            self::$header = $data;   
        }
    }

    public static function setPost($data = []) {
        if(is_array($data)) {
            self::$post = $data;
        }
    }

    public static function setFiles($data = []) {
        if(is_array($data)) {
            self::$files = $data;    
        }
    }

    /**
     * 检测header参数是否合法
     * return bool/string
     */
    public static function checkHeader() {
        $header = self::$header;
        if(empty($header['nonce']) || empty($header['timestamp']) || empty($header['sign'])) {
            return '接口鉴权异常';
        } else {
            // 时间戳
            $timestampIsTimeOut = self::checkTimestamp();
            if(is_string($timestampIsTimeOut)) {
                return $timestampIsTimeOut;
            } 

            // 判断链接是否重放
            $nonceIsUse = self::checkNonce();
            if(is_string($nonceIsUse)) {
                return $nonceIsUse;
            }

            // 签名检测
            $signCheckRes = self::checkSign();
            if(is_string($signCheckRes)) {
                return $signCheckRes;
            }

            self::useNonce();
        }
        return true;
    }


    /**
     * 使用重放字符串
     */
    private static function useNonce() {
        Cache::set(self::$nonce, '5', self::$apiTimeOut);
    }


    /**
     * 检测签名是否合法
     */
    private static function checkSign() {
        $header = self::$header;
        $params = self::$post;
        $files = self::$files;

        $data = array_merge($params, [
            'timestamp' => $header['timestamp'],
            'nonce' => $header['nonce'],
        ]);

        if(isset($header['token'])) {
            $data['token'] = $header['token'];
        }

        // 过滤在files里面的数组
        foreach($files as $key=>$val) {
            if(isset($data[$key])) {
                unset($data[$key]);
            }
        }

        try {
            array_walk($data, function(&$val, $key) {
                if(is_array($val)) {
                    $val = urldecode(json_encode($val));
                } else {
                    $val = urldecode($val);
                }
            });
        } catch (\think\exception\ErrorException $e) {
            // return '提交数据只支持数字(整数、浮点)、字符串';
            return '检测到非法数据类型';
        }
        
        ksort($data);
        $signData = http_build_query($data);
        if(self::$appsecret) {
            $signData .= '&appsecret='.self::$appsecret;
        }
        // var_dump($signData);
        // exit();
        if(strtoupper(md5($signData)) != $header['sign']) {
            return '签名错误';
        }

        return true;
    }


    /**
     * 检测防止重放随机字符串
     */
    private static function checkNonce() {
        $header = self::$header;
        $salt = $header['timestamp'];
        if(!empty($header['token'])) {
            $salt .= $header['token'];
        }
        self::$nonce = MD5($header['nonce'].$salt);
        if(Cache::get(self::$nonce)) {
            return '链接已被使用';
        }
        return true;
    }


    /**
     * 检测链接时间戳
     */
    private static function checkTimestamp() {
        $timestamp = self::$header['timestamp'];
        if(is_numeric($timestamp) == false) {
            return '链接非法';
        } else if(((int)($timestamp) + self::$apiTimeOut) < time()) {
            return '链接超时';
        }
        return true;
    } 

}