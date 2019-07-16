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
 * 删除过期的file缓存
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


/**
 * 发送请求
 * @param  char  $sendType 发送类型 post/get
 * @param  char  $url      发送地址
 * @param  char/array  $param    发送参数
 * @return array   
 */
function send_http($url, $sendType, $param = '') {
	//初始化 curl
	$curl = curl_init(); 
    
	//判断发送请求地址是否为https
    $urlParams = parse_url($url);
    //关闭https证书验证
	if($urlParams['scheme'] == 'https') {
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}

	//设置网站
	curl_setopt($curl, CURLOPT_URL, $url); 
	//不输出包头
	curl_setopt($curl,CURLOPT_HEADER,0);
	//不输出包头
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); 

	//判断请求是否为post
	if($sendType == 'post') {
		curl_setopt($curl,CURLOPT_POST,1);
	}

    //传输参数
	if(!empty($param)) {
		curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
		curl_setopt($curl,CURLOPT_POSTFIELDS,$param); 
	}
	
	$data = curl_exec($curl);
	curl_close($curl);
	return $data;
}


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
    if(empty($path)) return '';

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