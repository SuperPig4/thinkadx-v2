<?php

namespace app\admin\model;
use think\Model;

class AdminLog extends Model {

    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    protected $createTime = 'act_time';

    protected $insert = ['admin_id', 'ip', 'module', 'controller', 'action'];  

    public function admin() {
        return $this->belongsTo('admin');
    } 


    protected function setAdminIdAttr() {
        return USER_ID;
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
