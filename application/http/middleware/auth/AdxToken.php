<?php
/**
 * adx token验证
 */

namespace app\http\middleware\auth;

use think\facade\Cache;

class AdxToken extends Constraint {

    // 逻辑类
    public $logic;
    // 忽略列表
    public $ignoreList = [];

    /**
     * @param object  $request
     * @param Closure $next
     * @param array   $params
     *  desc
     *      参数一 逻辑类
     *      参数二 忽略列表
     */
    public function handle($request, \Closure $next, $params) {
        // 判断是否忽略
        if(isset($params[1])) $this->ignoreList = $params[1];
        if(!$this->ignore_check()) {
            $this->logic = '\\'.$params[0];
            $token = $request->header('token');
            $result = ['code'=>0, 'msg'=>'未知错误', 'data'=>['errorCode'=>0]];
            if(empty($token)) {
                $result['msg'] = '非法请求';
                $result['data']['errorCode'] = -1000;
                return $this->logic::fail($result);
            } else {
                $userId = Cache::get(($this->logic::getAuthDataName().'_access_'.$token));
                if(empty($userId)) {
                    $result['msg'] = 'token不存在';
                    $result['data']['errorCode'] = -1001;
                    return $this->logic::fail($result);
                } else {
                    // 装载数据
                    $this->loadData($request, [
                        $this->logic::getPk() => $userId
                    ], $this->logic);
                }
            }
        }

        return $next($request, false);
    }

    /**
     * 缓存逻辑
     */
    public function storage($data) {
        error($data);
    }

}
