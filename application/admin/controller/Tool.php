<?php

namespace app\admin\controller;
use think\Db;
use think\Controller;
use think\Request;
use think\facade\App;

class Tool extends Controller {
    
    public function main() {
        $today = strtotime(date("Y-m-d"),time());
        $count = [
            'admin' => Db::name('admin')->count(),
            'actLog' => Db::name('adminLog')->where('act_time', '>', $today)->count(),
            'ThinkPHP版本'=>App::version()
        ];

        $info = array(
            ['name' => '操作系统', 'value'=> PHP_OS],
            ['name' => '运行环境', 'value'=> $_SERVER["SERVER_SOFTWARE"]],
            ['name' => 'PHP运行方式', 'value'=> php_sapi_name()],
            ['name' => '上传附件限制', 'value'=> ini_get('upload_max_filesize')],
            ['name' => '执行时间限制', 'value'=> ini_get('max_execution_time').'秒'],
            ['name' => '服务器时间', 'value'=> date("Y年n月j日 H:i:s")],
            ['name' => '北京时间', 'value'=> gmdate("Y年n月j日 H:i:s",time()+8*3600)],
            ['name' => '服务器域名/IP', 'value'=> $_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]'],
            ['name' => '剩余空间', 'value'=> round((disk_free_space(".")/(1024*1024)),2).'M'],
            ['name' => 'register_globals', 'value'=> get_cfg_var("register_globals")=="1" ? "ON" : "OFF"],
            ['name' => 'magic_quotes_gpc', 'value'=> (1===get_magic_quotes_gpc())?'YES':'NO'],
            ['name' => 'magic_quotes_runtime', 'value'=> (1===get_magic_quotes_runtime())?'YES':'NO'],
		);
    
        success('ok!',[
            'info' => $info,
            'count' => $count
        ]);
    }

}