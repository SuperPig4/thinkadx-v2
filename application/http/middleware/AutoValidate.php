<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-06 16:31:09
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:10:56
# Description: 自动验证器中间件
# ============================================================================= */

namespace app\http\middleware;

use think\facade\Validate;

class AutoValidate
{
    /**
     * @param object  $request
     * @param Closure $next
     * @param array   $params
     *  desc
     *      string 参数一 逻辑类
     *      string 参数二 验证器类型名
     */
    public function handle($request, \Closure $next, $params)
    {
        $logic        = $params[0];
        $validateName = $params[1];
        $actionName   = $request->action(true);

        // 判断是否有限定请求类型
        $method = false;
        if(method_exists($logic, 'getMethod')) {
            $method = $logic::getMethod();
        }

        if(empty($method) || $method == $request->method()) {
            try {
                $validateInstance = new $validateName();
                if(!$validateInstance->scene($actionName)->check($request->param())) {
                    return $logic::fail($validateInstance->getError(), $validateInstance);
                }
            } catch(\think\exception\ClassNotFoundException $e) {
                return $logic::fail('验证器执行异常');
            }
        }

        return $next($request);
    }
}
