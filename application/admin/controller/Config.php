<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:32:49
# Description: 配置
# ============================================================================= */

namespace app\admin\controller;

use think\facade\Cache;
use app\admin\model\Config as ConfigModel;
use app\admin\validate\Config as ConfigValidate;

class Config extends Base {   

    protected $modelName    = ConfigModel::class;
    protected $validateName = ConfigValidate::class;
    protected $logs = [
        'add' => [
            '新增了系统配置',
            '新增系统配置失败'
        ],
        'edit' => [
            '编辑了系统配置',
            '编辑系统配置失败'
        ]
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
