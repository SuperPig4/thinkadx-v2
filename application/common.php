<?php

if (!function_exists('del_expired_file_cache')) {
    /**
     * 删除过期的file缓存 - x  
     *
     * @param String $filename 文件名字
     * @return void
     */
    function del_expired_file_cache($filename) {
        $content      = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int) substr($content, 8, 12);
            if (0 != $expire && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                is_file($filename) && unlink($filename);
            }
        }
    }
}


if (!function_exists('validate_file_url')) {
    /**
     * 验证文件URL是否为本站
     *
     * @param String $url
     * @return void
     */
    function validate_file_url($url) {
        $param = parse_url($url);
        if(isset($param['path']) && is_file('./'.$param['path'])) {
            return $param['path'];
        } else {
            return false;
        }
    }
}


if (!function_exists('local_path_turn_url')) {
    /**
     * 本地路径转换成
     * 
     * @param string $path 
     * @param array $host 域名 默认自动获取
     * @return string
     */
    function local_path_turn_url($path, $host = '') {
        if(empty($path)) return '';

        $path = str_replace('\\','/',$path);
        if(empty($hots)) {
            $request = request();
            $host = $request->domain();
        }

        return $host . $path;
    }
}


if (!function_exists('system_config')) {
    /**
     * 获得系统配置信息
     * 
     * @param string $name 配置名 app:应用类型的配置信息 system:系统配置信息
     * @return string
     */
    function system_config($name) {
        $nameSplit = explode('.', $name);
        if(count($nameSplit) < 2) {
            return '';
        } else {
            $data = Cache::get($name);
            if(empty($data)) {
                $data = db('config', [], false)->where([
                    'alias' => $nameSplit[1],
                    'type' => $nameSplit[0]
                ])->value('value');
                if($data) {
                    Cache::tag('system_config')->set($name, $data);
                }
            }
            return $data;
        }
    }
}


if(!function_exists('success')) {
    /**
     * 返回成功 - thinkadx依赖
     * @param string $msg 返回提示
     * @param array $data 数据
     * @param array $code 状态码
     * @param array $header 包头
     */
    function success($msg = '', $data = [], $code = 200, $header = []) {
        throw new \think\exception\HttpResponseException(response([
            'msg' => $msg,
            'data' => $data,
            'code' => 1
        ], $code, $header, 'json'));
    }
}


if(!function_exists('error')) {
    /**
     * 返回失败 - thinkadx依赖
     * @param string $msg 返回提示
     * @param array $data 数据
     * @param array $code 状态码
     * @param array $header 包头
     */
    function error($msg = '', $data = [], $code = 200, $header = []) {
        throw new \think\exception\HttpResponseException(response([
            'msg' => $msg,
            'data' => $data,
            'code' => 0
        ], $code, $header, 'json'));
    }
}