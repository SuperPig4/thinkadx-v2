<?php

namespace app\admin\controller;
use think\Controller;
use think\facade\Cache;
use think\Db;
use app\admin\model\Admin;
use app\admin\model\AdminOauth;



class AdminUser extends Base {
    
    protected $validateName = 'AdminUser';


    // 修改密码
    public function set_password() {
        $data = $this->request->param();
        //目前只有 pwd && api 才能够修改密码
        if($data['port_type'] != 'api' || $data['oauth_type'] != 'pwd') {
            error('非法操作');
        }
     
        if(USER_ID != 1) {
            if(empty($data['old_password'])) {
                error('请输入旧密码');
            } else if(!empty($data['id'])) {
                error('非法操作');
            }
        }

        $user = Admin::get(empty($data['id']) ? USER_ID : $data['id']);
        $oauthInfo = $user->adminOauth()->where([
            'oauth_type' => $data['oauth_type'],
            'port_type' => $data['port_type']
        ])->find();

        if(empty($user) || empty($oauthInfo)) {
            error('用户不存在');
        }

        if(empty($data['old_password']) == false && (MD5($data['old_password'] . $oauthInfo->unique_identifier)) != $oauthInfo->identifier) {
            error('旧密码错误');
        } else {
            $newPassword = MD5($data['new_password'] . $oauthInfo->unique_identifier);
            //不同的话就修改
            if($newPassword != $oauthInfo->identifier) {
                $oauthInfo->identifier = $newPassword;
                $oauthInfo->save();
            }
            $this->request->act_log = '修改了密码';
            success('修改成功');
        }
    }


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
                            }
                        }
                    }
                    Db::commit();
                }
            }
        } 
        error('重新登陆', ['errorCode'=>-1000]);
    }


    // 获得用户信息
    public function info() {
        $userInfo = Admin::get(USER_ID);
        success('ok!',$userInfo);
    }


    // 登陆
    public function login() {
        $data = $this->request->param();
        $user = admin::where('access', $data['access'])->find();
        if(empty($user)) {
            error('请输入正确的账号');
        } else {

            $oauth = $user->adminOauth()->where([
                'port_type' => $data['port_type'],
                'oauth_type' => $data['oauth_type']
            ])->find();

            if(is_null($oauth)) {
                error('登陆失败');
            } else {
                $loginResult = $oauth->login();
            }
            
            if(is_array($loginResult)) {
                define('USER_ID', $oauth->admin_id);
                $this->request->act_log = '登陆成功';
                success('ok!', $loginResult);
            } else {
                error($loginResult);
            }

        }
    }

}
