<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:31:24
# Description: 管理员
# ============================================================================= */

namespace app\admin\controller;

use think\facade\Cache;
use think\Db;
use app\common\model\Admin as AdminModel;
use app\common\model\AdminOauth as AdminOauthModel;
use app\admin\validate\AdminUser as AdminUserValidate;
use Thinkadx\Oauth2\Main;
use Thinkadx\LoadData;
use app\admin\middleware\auth\AdxToken;

class AdminUser extends Base {
    
    protected $validateName = AdminUserValidate::class;
    protected $modelName = AdminModel::class;
    protected $beforeActionList = [
        'check_access' =>  ['only' => 'add_edit'],
    ];

    protected $logs = [
        'add' => [
            '新增了管理员信息',
            '新增管理员信息失败'
        ],
        'edit' => [
            '编辑了管理员信息',
            '编辑管理员信息失败'
        ]
    ];


    // 重写删除
    public function delete() {
        $data = $this->request->param();
        if(!is_numeric($data['delete_id'])) {
            foreach($data['delete_id'] as $val) {
                if($val == 1) {
                    error('超级管理员无法删除');
                }
            }
        } else {
            if($data['delete_id'] == 1) {
                error('超级管理员无法删除');
            }
        }

        Db::startTrans();
        $info = AdminModel::all($data['delete_id']);
        foreach ($info as $key => $value) {
            if($value->adminOauth()->where('admin_id',$value['id'])->delete()) {
                if($value->delete() === false) {
                    $isError = true;
                    break;
                }
            }
        }
        if(!isset($isError)) {
            $this->request->act_log = '删除了管理员';
            Db::commit();
            success('操作成功');
        } else {
            Db::rollback();
            error('操作失败');
        }
    }


    // 重写编辑增加
    public function add_edit() {
        $data = $this->request->param();
        if(empty($data['id'])) {
            // 新增
            $status = AdminModel::create($data)
            ->adminOauth()
            ->save(AdminOauthModel::getPasswordBaseConfig($data['add_data_password']));
        } else {
            // 编辑
            $model = new Admin();
            $status = $model->save($data,['id' => $data['id']]);
        }

        if($status !== false) {
            if(!empty($data['id'])) {
                $log = $this->logs['edit'];
            } else {
                $log = $this->logs['add'];
            }

            if(!empty($log)) {
                $this->request->act_log = $log;
            }
            success('操作成功!');
        } else {
            error('操作失败');
        }
    }


    // 详情
    public function detail() {
        $userInfo = AdminModel::get($this->request->param('id/d'));
        success('ok!',$userInfo);
    }


    // 修改密码
    public function set_password() {
        $data = $this->request->param();
        //目前只有 pwd && api 才能够修改密码
        if($data['port_type'] != 'api' || $data['oauth_type'] != 'pwd') {
            error('非法操作');
        }
     
        if(app('adminData')->id != 1) {
            if(empty($data['old_password'])) {
                error('请输入旧密码');
            } else if(!empty($data['id'])) {
                error('非法操作');
            }
        }

        $user = AdminModel::get(empty($data['id']) ? app('adminData')->id : $data['id']);
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


    // 获得用户信息
    public function info() {
        $userInfo = AdminModel::get(app('adminData')->id);
        success('ok!',$userInfo);
    }


    // 登陆
    public function login() {
        $data = $this->request->param();
        $result = $this->validate($data, 'app\admin\validate\Login.'. $data['port_type'] . '_' . $data['oauth_type']);
        if($result !== true) {
            error($result);
        }

        $user = AdminModel::where('access', $data['access'])->find();
        if(empty($user)) {
            $this->request->act_log = '尝试登陆';
            error('请输入正确的账号');
        } else {
            $oauth = $user->adminOauth()->where([
                'port_type' => $data['port_type'],
                'oauth_type' => $data['oauth_type']
            ])->find();
            if(is_null($oauth)) {
                $this->request->act_log = [
                    [
                        'admin_id' => $user->id,
                        'des' => '未找到该登录方式'
                    ]
                ];
                error('未找到该登录方式');
            } else {
                // $loginResult = Main::init(AdminOauthModel::class, Main::ModeList['PASSWORD'])
                // ->setPortType('api')
                // ->setId($data['password'])
                // ->setUniqueId($oauth->unique_identifier)
                // ->setCacheData($user->toArray())
                // ->setOauthModel($oauth)
                // ->logout()
                // ->login();

                $oauth = Main::init(AdminOauthModel::class, Main::ModeList['PASSWORD'])
                ->setPortType('api')
                ->setId($data['password'])
                ->setUniqueId($oauth->unique_identifier)
                ->setCacheData($user->toArray())
                ->setOauthModel($oauth);

                $loginResult = $oauth->login();
            }

            if(is_array($loginResult)) {
                // 下线其他令牌
                $oauth->logout();

                // 默认token登录,如果是session的话这里也要改
                // 绑定容器
                $name = method_exists(AdxToken::class, 'containerName') ?  AdxToken::containerName() : 'loadData' ;
                bind($name, new LoadData(
                    $user->toArray(), 
                    AdxToken::getModel(), 
                    $this, 
                    AdxToken::getPk()
                ));

                $this->request->act_log = '登陆成功';
                success('ok!', $loginResult);
            } else {
                $this->request->act_log = [
                    [
                        'admin_id' => $user->id,
                        'des' => '密码错误或其他异常'
                    ]
                ];
                error('密码错误或其他异常');
            }
        }
    }

    
    // 登出
    public function logout() {
        Main::init(AdminOauthModel::class, Main::ModeList['PASSWORD'])
        ->setPortType('api')
        ->setOauthModel(app('adminData')->model->AdminOauth()->where([
            'port_type' => 'api',
            'oauth_type' => 'password'
        ])->find())
        ->logout();
        success('ok!');
    }


    protected function check_access() {
        $data = $this->request->param();
        if(empty($data['id']) && !empty($data['access'])) {
            $isUse = Db::name('admin')->where('access', $data['access'])->count();
            if($isUse) {
                error('当前账号已被使用');
            }
        }
    }

    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|nickname', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC')->append(['status_text']);
    }

}
