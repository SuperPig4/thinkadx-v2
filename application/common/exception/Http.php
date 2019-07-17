<?php
namespace app\common\exception;

use Exception;
use think\exception\Handle;
use think\exception\HttpException;
use think\exception\ValidateException;

class Http extends Handle {
    public function render(Exception $e)
    {
        if(config('app_debug')) {
            // 其他错误交给系统处理
            return parent::render($e);
        } else {
            return response([
                'msg' => '系统异常',
                'data' => [],
                'code' => 0
            ], 404, [], 'json');
        }
    }

}