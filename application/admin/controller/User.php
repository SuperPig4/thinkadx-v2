<?php

namespace app\admin\controller;
use think\Controller;
use think\facade\Cache;
use think\Db;
use app\admin\model\Admin;
use app\admin\model\AdminOauth;

class User extends Base {
    
    protected $validateName = 'User';

    // 刷新令牌
    public function rese_token() {
        $oldToken = $this->request->param('old_token/s');
        $refreshToken = $this->request->param('refresh_token/s');

        $accessTokenKey = 'admin_access_'.$oldToken;
        $refreshTokenKey = 'admin_refresh_'.$refreshToken;

        $accessValue = Cache::get($accessTokenKey);
        $refreshValue = Cache::get($refreshTokenKey);

        if($refreshValue) {
            if($accessValue && $accessValue == $refreshValue) {
                success('ok!',[
                    'expired' => system_config('system.admin_access_token_time_out'),
                    'token' => $oldToken
                ]);
            } else if(empty($accessValue)) {
                //检测是否临时白名单中
                $tempTokenInfo = Cache::get('temp_token_'.$accessTokenKey);
                if($tempTokenInfo && $tempTokenInfo['refresh_token'] == $refreshToken) {
                    success('ok!',[
                        'expired' => system_config('system.admin_access_token_time_out'),
                        'token' => $tempTokenInfo['access_token']
                    ]);
                } else if(empty($tempTokenInfo)) {
                    //进行数据库级的查询
                    Db::startTrans();
                    $oauth = AdminOauth::where('refresh_token', $refreshToken)->lock(true)->find();
                    if(!empty($oauth)) {
                        //第二次检测是否在临时缓存中
                        $tempTokenInfo = Cache::get('temp_token_'.$accessTokenKey);
                        if($tempTokenInfo && $tempTokenInfo['refresh_token'] == $refreshToken) {
                            Db::commit();
                            success('ok!',[
                                'expired' => system_config('system.admin_access_token_time_out'),
                                'token' => $tempTokenInfo['access_token']
                            ]);
                        } else if(empty($tempTokenInfo)) {
                            if($oauth->last_use_access_token == $oldToken) {
                                Db::commit();
                                error('您的账号在其他地方登陆了', ['errorCode'=>-1002]);
                            } else if($oauth->access_token == $oldToken) {

                                //正式进入刷新逻辑
                                $newToken = $oauth->resetToken('access');
                                Cache::tag('admin_temp_token')->set('temp_token_'.$accessTokenKey, [
                                    'access_token' => $newToken['token'],
                                    'refresh_token' => $refreshToken,
                                    'user_id' => $oauth->admin_id
                                ], 600);
                                Db::commit();
                                success('ok!',[
                                    'expired' => system_config('system.admin_access_token_time_out'),
                                    'token' => $newToken['token']
                                ]);
                                
                                //第二次检测是否在临时缓存中
                                // $tempTokenInfo = Cache::get('temp_token_'.$accessTokenKey);
                                // if($tempTokenInfo && $tempTokenInfo['refresh_token'] == $refreshToken) {
                                //     Db::commit();
                                //     success('ok!',[
                                //         'expired' => system_config('system.admin_access_token_time_out'),
                                //         'token' => $tempTokenInfo['access_token']
                                //     ]);
                                // } else if(empty($tempTokenInfo)) {
                                //     //正式进入刷新逻辑
                                //     $newToken = $oauth->resetToken('access');
                                //     Cache::tag('admin_temp_token')->set('temp_token_'.$accessTokenKey, [
                                //         'access_token' => $newToken['token'],
                                //         'refresh_token' => $refreshToken,
                                //         'user_id' => $oauth->admin_id
                                //     ], 600);
                                //     Db::commit();
                                //     success('ok!',[
                                //         'expired' => system_config('system.admin_access_token_time_out'),
                                //         'token' => $newToken['token']
                                //     ]);
                                // }
                            }
                        }
                    }
                    Db::commit();
                }
            }
        } 
        error('重新登陆', ['errorCode'=>-1000]);

        // if(($accessValue && $refreshValue) && ($accessValue == $refreshValue)) {
            
        // } else {
        //     //检测是否在白名单中
        //     if(!Cache::get('temp_token_'.$key)) {
        //         //检测是否在历史字段中
        //         $oauth = AdminOauth::where('access_token', $oldToken)->findOrEmpty();
                
        //         // if(is_null($oauth)) {
        //         //     $oauth = AdminOauth::where('last_use_access_token', $oldToken)->findOrEmpty();
        //         //     if(!is_null($oauth)) {
        //         //         error('您的账号在其他地方登陆了', ['errorCode'=>-1002]);
        //         //     }
        //         // } 
                

                
        //         //检测数据库
        //         AdminOauth::startTrans();
        //         $oauth = AdminOauth::where('access_token', $oldToken)->lock(true)->findOrEmpty();
        //         if(empty(Cache::get('temp_token_'.$key)) && (!is_null($oauth))) {
        //             Cache::tag('admin_temp_token')->set('temp_token_'.$key, $oauth->admin_id, 60);
        //             $newToken = $oauth->resetToken('access');
        //             AdminOauth::commit();
        //         } else if(is_null($oauth)) {
        //             AdminOauth::commit();
        //             error('重新登陆', ['errorCode'=>-1001]);
        //         } 
        //     }
        // }
        // success('ok!',[
        //     'expired' => system_config('system.admin_access_token_time_out'),
        //     'token' => $newToken['newToken']
        // ]);
    }


    // 获得用户信息
    public function info() {
        $userInfo = Admin::get($this->request->user_id);
        success('ok!',$userInfo);
    }


    // 登陆
    public function login() {
        $data = $this->request->param();
        $user = admin::where('access', $data['access'])->find();
        if(empty($user)) {
            error('请输入正确的账号');
        } else {

            $oauth = $user->admin_oauth()->where([
                'port_type' => $data['port_type'],
                'oauth_type' => $data['oauth_type']
            ])->find();

            if(is_null($oauth)) {
                error('登陆失败');
            } else {
                $loginResult = $oauth->login();
            }
            
            if(is_array($loginResult)) {
                success('ok!', $loginResult);
            } else {
                error($loginResult);
            }

        }
    }

}
