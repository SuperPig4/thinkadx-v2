<?php

namespace app\admin\model;

use think\Model;

class Admin extends Model {
    
    public function admin_oauth() {
        return $this->hasMany('AdminOauth', 'admin_id');
    }

}
