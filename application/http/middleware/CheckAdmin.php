<?php

namespace app\http\middleware;
use think\facade\Cache;
use ApiAuth\ParamsChcke;

class CheckAdmin {

    public function handle($request, \Closure $next) {
        ParamsChcke::setHeader($request->header());
        ParamsChcke::setPost($request->post(false));
        ParamsChcke::setFiles($request->file());
        $apiHeadrCheckRes = ParamsChcke::checkHeader();
        if(is_string($apiHeadrCheckRes)) {
            return response(error($apiHeadrCheckRes), 200, [], 'json');
        }
        
        $tokenCheckRes = $this->tokenCheck($request);
        if(!$tokenCheckRes['code']) {
            return response($tokenCheckRes, 200, [], 'json');
        }
        $request->user_id = $tokenCheckRes['data']['user_id'];
        return $next($request);
    }


    //检测token
    protected function tokenCheck($request) {
        $ignoreList = [];
        if(!in_array($request, $ignoreList)) {
            $token = $request->header('token');
            if(empty($token)) {
                return error('非法请求');
            } else {
                $userId = Cache::get(($token.'_access_token'));
                if(empty($userId)) {
                    return error('token不存在',['errorCode'=>-1000]);
                }
            }
        }
        return success('ok!',['user_id'=>$userId]);
    }

}
