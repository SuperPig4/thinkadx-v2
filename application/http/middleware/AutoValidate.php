<?php
/**
 * 自动验证器
 */

namespace app\http\middleware;

class AutoValidate
{
    public function handle($request, \Closure $next)
    {
        if(defined("Validate_Name")) {
            try {
                $validateInstance = validate("\\app\\".$request->module(true)."\\validate\\".Validate_Name);
                if(!$validateInstance->scene($request->action(true))->check($request->param())) {
                    error($validateInstance->getError());
                }
            } catch(\think\exception\ClassNotFoundException $e) {
                error('执行异常');
            }
        }
        return $next($request);
    }
}
