<?php

namespace app\http\middleware;

class CheckHeader
{
    public function handle($request, \Closure $next) {
        // 检测来源域名
        $info = $request->header();
        if($info['host'] != '118.24.221.147:9487') {
            throw new \think\exception\HttpException(404, 'header域名不匹配');
        }
        return $next($request);
    }
}
