<?php

namespace app\http\middleware;
use think\facade\Cache;
use Thinkadx\ApiAuth\ParamsChcke;
use think\Controller;

class CheckAdmin extends Controller {

    public function handle($request, \Closure $next) {
        
        ParamsChcke::setHeader($request->header());
        ParamsChcke::setPost($request->post(false));
        ParamsChcke::setFiles($request->file());
        $apiHeadrCheckRes = ParamsChcke::checkHeader();
        if(is_string($apiHeadrCheckRes)) {
            error($apiHeadrCheckRes);
        }

        if(isset($request->validateName)) {
            try {
                $validateInstance = validate("\\app\\admin\\validate\\".$request->validateName);
                if(!$validateInstance->scene($request->action(true))->check($request->param())) {
                    error($validateInstance->getError());
                }
            } catch(\think\exception\ClassNotFoundException $e) {
                error('执行异常');
            }
        }
        
        $tokenCheckRes = $this->tokenCheck($request);
        if($tokenCheckRes['code'] == 0) {
            error($tokenCheckRes['msg'], $tokenCheckRes['data']);
        } else {
            if(array_key_exists('user_id', $tokenCheckRes['data'])) {
                $request->user_id = $tokenCheckRes['data']['user_id'];
            }
        }
        // $request->user_id = 1;
        return $next($request);
    }


    //检测token
    protected function tokenCheck($request) {
        $ignoreList = [
            'Index' => ['index'],
            'AdminUser' => ['login', 'rese_token']
        ];
        $result = ['code'=>0, 'msg'=>'未知错误', 'data'=>['errorCode'=>0]];
        $ignoreAr = [];
        $controller = $request->controller();

        if(array_key_exists($controller, $ignoreList)) {
            $ignoreAr = $ignoreList[$controller];
        }
        
        if(!in_array($request->action(true), $ignoreAr)) {
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

}