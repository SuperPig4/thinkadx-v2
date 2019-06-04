<?php

namespace app\admin\model;

use think\Model;

class AdminGroup extends Model {
    
    public function admin() {
        return $this->hasMany('admin', 'group_id');
    }

}
