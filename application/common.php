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

// 应用公共文件


/**
 * 返回成功
 * @param string $msg 返回提示
 * @param array $data 数据
 */
function success($msg = '', $data = []) {
    return [
        'code' => 1,
        'msg' => $msg,
        'data' => $data
    ];
}


/**
 * 返回失败
 * @param string $msg 返回提示
 * @param array $data 数据
 */
function error($msg = '', $data = []) {
    return [
        'code' => 0,
        'msg' => $msg,
        'data' => $data
    ];
}