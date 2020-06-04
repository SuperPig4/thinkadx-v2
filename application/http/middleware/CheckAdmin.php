<?php

namespace app\http\middleware;
use think\facade\Cache;
use Thinkadx\ApiAuth\ParamsChcke;
use Thinkadx\RuleAuth\Main;
use think\Controller;

class CheckAdmin extends Controller {

    public function handle($request, \Closure $next) {
        
        // 忽略不进行鉴权的接口
        $ignoreList = [
            'Tool' => ['get_verify_img'],
        ];
        
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

        if(defined("Validate_Name")) {
            try {
                $validateInstance = validate("\\app\\admin\\validate\\".Validate_Name);
                if(!$validateInstance->scene($request->action(true))->check($request->param())) {
                    error($validateInstance->getError());
                }
            } catch(\think\exception\ClassNotFoundException $e) {
                error('执行异常');
            }
        }
        
        if(!$isIgnore) {
            $tokenCheckRes = $this->tokenCheck($request);
            if($tokenCheckRes['code'] == 0) {
                error($tokenCheckRes['msg'], $tokenCheckRes['data']);
            } else {
                if(array_key_exists('user_id', $tokenCheckRes['data'])) {
                    define('USER_ID', $tokenCheckRes['data']['user_id']);
                    // 进行权限检测
                    $ruleAuth = new Main('admin_rule', 'admin_group', 'admin', USER_ID);
                    $check = $ruleAuth->check();
                    if(!$check) {
                        error('暂无权限');
                    }
                }
            }
        }
        
        return $next($request);
    }


    //检测token
    protected function tokenCheck($request) {
        
        // 忽略不进行token检测的接口
        $ignoreList = [
            'Index' => ['index'],
            'AdminUser' => ['login', 'rese_token'],
            'Upload' => ['index'],
            'Config' => ['get_system_config'],
            'Tool' => ['get_verify_key']
        ];
        $result = ['code'=>0, 'msg'=>'未知错误', 'data'=>['errorCode'=>0]];
        
        if(!$this->ignore_check($ignoreList, $request->controller(), $request->action(true))) {
            $token = $request->header('token');
            if(empty($token)) {
                $result['msg'] = '非法请求';
                $result['data']['errorCode'] = -1000;
            } else {
                $userId = Cache::get(('admin_access_'.$token));
                if(empty($userId)) {
                    $result['msg'] = 'token不存在';
                    $result['data']['errorCode'] = -1001;
                } else {
                    $result['msg'] = 'token不存在';
                    $result['data']['user_id'] = $userId;
                    $result['code'] = 1;
                }
            }
        } else {
            $result['code'] = 1;
            $result['msg'] = '对该请求不验证token';
        }

        return $result;
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
