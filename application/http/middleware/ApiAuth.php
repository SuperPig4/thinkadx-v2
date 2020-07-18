<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-18 07:18:22
# Descripttion: 接口鉴权中间件
#============================================================================= */
namespace app\http\middleware;

use think\Controller;
use Thinkadx\ApiAuth as AdxApiAuth;

class ApiAuth extends Controller {

    public function handle($request, \Closure $next, $logic) {
        $isIgnore = $this->ignore_check($logic::getIgnores(), $request->controller(), $request->action(true));
        if(!$isIgnore) {
            $appid = $request->header('appid');
            $model = $logic::getModel();
            if(empty($appid)) {
                error('appid不能为空', [], 401);
            }

            $api = $model::where('appid', $appid)->cache(true)->find();
            if(empty($api)) {
                error('appid错误', [], 404);
            } else if($api->status != 1) {
                error('appid被暂停使用', [], 404);
            }

            try {
                $result = AdxApiAuth::autoSetParams()
                ->setAppid($api->appid)
                ->setAppsecret($api->appsecret)
                ->check();

                if($result !== true) {
                    error($result, [], 404);
                }
            } catch (\think\Exception $e) {
                error($e->getMessage(), [], 404);
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