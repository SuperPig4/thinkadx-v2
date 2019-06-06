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
        $key = 'admin_acess_'.$oldToken;
        if(!Cache::get($key)) {
            //检测是否在白名单中
            if(!Cache::get('temp_token_'.$key)) {
                //检测是否在历史字段中
                $oauth = AdminOauth::where('last_use_access_token', $oldToken)->findOrEmpty();
                if(!is_null($oauth)) {
                    error('您的账号在其他地方登陆了', ['errorCode'=>-1002]);
                }
                
                //检测数据库
                AdminOauth::startTrans();
                $oauth = AdminOauth::where('access_token', $oldToken)->lock(true)->findOrEmpty();
                if(empty(Cache::get('temp_token_'.$key)) && (!is_null($oauth))) {
                    Cache::tag('admin_temp_token')->set('temp_token_'.$key, $oauth->admin_id, 60);
                    $newToken = $oauth->resetToken('access');
                    AdminOauth::commit();
                } else if(is_null($oauth)) {
                    AdminOauth::commit();
                    error('重新登陆', ['errorCode'=>-1001]);
                } 
            }
        }
        success('ok!',[
            'expired' => system_config('system.admin_access_token_time_out'),
            'token' => $newToken['newToken']
        ]);
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
