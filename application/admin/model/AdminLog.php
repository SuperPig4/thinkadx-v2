<?php
/* =============================================================================#
# Author: 奔跑猪
# Date: 2020-07-16 07:00:03
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-19 11:04:20
# Descripttion: 
#============================================================================= */
namespace app\admin\model;

use app\common\model\Admin;
use think\Model;

class AdminLog extends Model {

    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    protected $createTime = 'act_time';

    protected $insert = ['admin_id', 'ip', 'module', 'controller', 'action'];  

    public function admin() {
        return $this->belongsTo(Admin::class);
    } 


    protected function setAdminIdAttr($value) {
        try {
            if(empty($value)) {
                return app('admin')->id;
            } else {
                return $value;
            }
        } catch(\Exception $e) {
            return '';
        }
    }

    protected function setIpAttr() {
        return request()->ip();
    }

    protected function setModuleAttr() {
        return request()->module(true);
    }

    protected function setControllerAttr() {
        return request()->controller(true);
    }

    protected function setActionAttr() {
        return request()->action(true);
    }


}
