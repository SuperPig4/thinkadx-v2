<?php

namespace app\admin\model;

use think\Model;

class AdminMenu extends Model {
    protected $autoWriteTimestamp  = true;
    protected $updateTime   = false;
    protected $createTime = 'create_time';
    protected $readonly = ['father_id', 'create_time'];
}
