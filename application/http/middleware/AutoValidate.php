<?php
/**
 * 自动验证器
 */

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
     *      string/Array 参数二 需要验证的控制器名
     *      Array = [模块名,控制器名,方法名]
     */
    public function handle($request, \Closure $next, $params)
    {
        $logic = '\\'.$params[0];
        $validateName = $params[1];

        // 解析参数
        $moduleName = $request->module(true);
        $controllerName = $request->controller();
        $actionName = $request->action(true);
        $validatePath = '';
        if(is_array($validateName)) {
            if(is_string($validateName[0])) $moduleName = $validateName[0];
            if(is_string($validateName[1])) $controllerName = $validateName[1];
            if(is_string($validateName[2])) $actionName = $validateName[2];
        } else {
            $controllerName = $validateName;
        }

        $validatePath = "\\app\\".$moduleName."\\validate\\".$controllerName;

        // 判断是否有限定请求类型
        $method = false;
        if(method_exists($logic, 'getMethod')) {
            $method = $logic::getMethod();
        }

        if(empty($method) || $method == $request->method()) {
            try {
                $validateInstance = validate($validatePath);
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
