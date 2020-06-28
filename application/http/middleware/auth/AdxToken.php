<?php
/**
 * adx token验证
 */

namespace app\http\middleware\auth;

use app\admin\model\AdminOauth;
use Exception;
use think\facade\Cache;
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
    public function handle($request, \Closure $next, $params) {
        // 判断是否忽略
        if(isset($params[1])) $this->ignoreList = $params[1];
        if(!$this->ignore_check()) {
            $this->logic = '\\'.$params[0];

            $refreshToken = $request->header('refresh-token');
            $oauthType    = $request->header('oauth-type');
            $portType     = $request->header('port_type');

            try {
                if(empty($oauthType) || empty($portType)) {
                    $this->logic::fail(403);
                    return response()->code(403);
                }
                $oauthMain = OauthMain::init(AdminOauth::class, $oauthType);
            } catch (\Exception $e) {
                $this->logic::fail('error');
                return response()->code(404);
            }

            // 判断是否有刷新令牌
            if(empty($refreshToken) == false) {
                $refreshResult = $oauthMain
                ->setPortType($portType)
                ->setTableUserPk('admin_id')
                ->refresh($refreshToken);
                
                if($refreshResult === false) {
                    $this->logic::fail(403);
                    return response()->code(403);
                } else {
                    $checkResult = $oauthMain->check($refreshResult);
                    if($checkResult === false) {
                        $this->logic::fail(403);
                        return response()->code(403);
                    } else {
                        // 装载数据
                        $this->loadData($request, $checkResult, $this->logic);
                        Response::header('access-token', $refreshResult)->send();
                    }
                }
            } else {
                $token = $request->header('token');
                if(empty($token)) {
                    $this->logic::fail(403);
                    return response()->code(403);
                } else {
                    $checkResult = $oauthMain
                    ->setPortType($portType)
                    ->check($token);
                    
                    if($checkResult === false) {
                        return response()->code(403);
                    } else {
                        // 装载数据
                        $this->loadData($request, $checkResult, $this->logic);
                    }
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
