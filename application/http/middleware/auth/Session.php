<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-17 08:32:08
# Descripttion: session权限检测
#============================================================================= */
namespace app\http\middleware\auth;

use think\facade\Session as ThinkSession;

class Session extends Constraint {

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
    public function handle($request, \Closure $next, $logic) {
        // 判断是否忽略
        $this->logic      = $logic;
        // 判断是否忽略
        $this->ignoreList = $logic::getgetIgnores();
        
        if(isset($params[1])) $this->ignoreList = $params[1];
        if(!$this->ignore_check()) {
            ThinkSession::init();
            
            if(!ThinkSession::has($this->logic::getAuthDataName())) {
                return $this->logic::fail();
            }

            // 装载数据
            $data = ThinkSession::get($this->logic::getAuthDataName());
            $this->loadData($request, $data, $this->logic);
        }
        return $next($request);
    }

    /**
     * 缓存逻辑
     */
    public function storage($data) {
        return ThinkSession::set($this->logic::getAuthDataName(), $data);
    }

}
