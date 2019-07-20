<?php

namespace app\Admin\controller;
use think\facade\Cache;
use app\admin\model\Config as ConfigModel;

class Config extends Base {   

    protected $validateName = true;
    protected $logs = [
        'add' => '新增了系统配置',
        'edit' => '编辑了系统配置'
    ];

    
    public function get_system_config() {
        $data = Cache::remember('controller_get_all_config',function(){
            $data = ConfigModel::all([3]);
            $runData = [];
            foreach($data as $item) {
                $runData[$item['alias']] = $item['value'];
            }
            return $runData;
        });
        success('ok!',$data);
    }


    protected function index_where_callback($db, $data) {
        if(!empty($data['search'])) {
            $db = $db->where('id|name', 'like', '%'.$data['search'].'%');
        }
        return $db->order('id DESC')->append(['type_text']);
    }
}
