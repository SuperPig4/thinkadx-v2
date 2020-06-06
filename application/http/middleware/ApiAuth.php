<?php
/**
 * 接口鉴权
 */

namespace app\http\middleware;
use Thinkadx\ApiAuth\ParamsChcke;
use think\Controller;

class ApiAuth extends Controller {

    public function handle($request, \Closure $next, $ignoreList= []) {
        $isIgnore = $this->ignore_check($ignoreList, $request->controller(), $request->action(true));
        if(!$isIgnore) {
            ParamsChcke::setHeader($request->header());
            ParamsChcke::setPost($request->post(false));
            ParamsChcke::setFiles($request->file());
            $apiHeadrCheckRes = ParamsChcke::checkHeader();
            if(is_string($apiHeadrCheckRes)) {
                error($apiHeadrCheckRes);
            }
        }
        return $next($request);
    }

    /**
     * 忽略检测
     * @param array 关联数组
     * @param string/bool 控制名
     * @param string/bool 操作名
     * @return bool true:忽略 false:不忽略
     */
    protected function ignore_check($ignoreList, $controller, $action) {
        $ignoreAr = [];
        if(array_key_exists($controller, $ignoreList)) {
            $ignoreAr = $ignoreList[$controller];
        }

        if(in_array($action, $ignoreAr)) {
            return true;
        } else {
            return false;
        }
    }
}