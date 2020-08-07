<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-06-14 19:58:10
# LastEditors: 奔跑猪
# LastEditTime: 2020-08-08 04:10:18
# Descripttion: adx token验证
#============================================================================= */
namespace app\http\middleware\auth;

use app\admin\model\AdminOauth;
use Exception;
use think\facade\Cache;
use think\facade\Request;
use think\facade\Response;
use Thinkadx\Oauth2\Main as OauthMain;

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
    public function handle($request, \Closure $next, $logic) {
        // 设置响应头
        header('Access-Control-Expose-Headers: access-token, access-token-expire-time');
        
        // 逻辑
        $this->logic      = $logic;
        // 判断是否忽略
        $this->ignoreList = $logic::getIgnores();
        
        if(!$this->ignore_check()) {
            $refreshToken = $request->header('refresh-token');
            $accessToken  = $request->header('token');
            $oauthType    = $request->header('oauth-type');
            $portType     = $request->header('port-type');
            $oauthTypeMap = $logic::getOauthTypeMap();
            $checkResult  = false;
            
            try {
                // 初始化 Oauth
                if(empty($oauthType) || empty($portType)) {
                    return response()->code(401);
                }

                if(is_array($oauthTypeMap) && isset($oauthTypeMap[$oauthType])) {
                    $oauthType = $oauthTypeMap[$oauthType];
                } else if($oauthTypeMap !== true) {
                    throw new \think\Exception('oauth error');    
                }

                $oauthMain = OauthMain::init($this->logic::getOauthModel(), $oauthType)->setPortType($portType)->setModeName(strtoupper($oauthType));
            } catch (\Exception $e) {
                return response()->data(json_encode(['msg'=>'oauth error']))->code(404);
            }

            if(empty($refreshToken) == false) {
                // 令牌验证刷新
                $refreshResult = $oauthMain->setTableUserPk($this->logic::getOauthUserPk())->refresh($refreshToken);
                if($refreshResult === false) {
                    return $this->logic::fail(401);
                } else {
                    $checkResult = $oauthMain->check($refreshResult['token']);
                    if($checkResult !== false) {
                        // 返回新令牌
                        Response::header('access-token', $refreshResult['token'])->send();
                        Response::header('access-token-expire-time', $refreshResult['expire_time'])->send();
                    }
                }
            } else if(empty($accessToken) === false) {
                // 访问令牌刷新
                $checkResult = $oauthMain->check($accessToken);
            } else {
                return $this->logic::fail(401);
            }

            if($checkResult === false) {
                return $this->logic::fail(401);
            } else {
                // 装载数据
                $this->loadData($request, $checkResult, $this->logic);
            }
        }

        return $next($request, false);
    }

    /**
     * 缓存逻辑
     */
    public function storage($data) {
        $config = $this->logic::getStorageConfig();
        OauthMain::init($config['modelClass'], $config['model']->oauth_type)
        ->setPortType($config['model']->port_type)
        ->setCacheData($data)
        ->setOauthModel($config['model'])
        ->updateCacheData();
    }

}
