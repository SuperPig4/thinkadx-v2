<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
/**
 * 验证文件URL是否为本站
 */
function validate_file_url($url) {
    $param = parse_url($url);
    if(isset($param['path']) && is_file('./'.$param['path'])) {
        return $param['path'];
    } else {
        return false;
    }
}


/**
 * 本地路径转换成
 * @param string $path 
 * @param array $host 域名 默认自动获取
 * @return string
 */
function local_path_turn_url($path, $host = '') {
    $path = str_replace('\\','/',$path);
    
    if(empty($hots)) {
        $request = request();
        $host = $request->domain();
    }

    return $host . $path;
}


/**
 * 获得系统配置信息
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


/**
 * 返回成功
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


/**
 * 返回失败
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