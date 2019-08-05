<?php

namespace app\admin\thinkadx;
use think\Db;
use think\Controller;
use think\Request;
use think\facade\App;
use think\Container;
use Thinkadx\Captcha\Main as CaptchaMain;

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


    // 清空过期缓存
    public function empty_expired_cache() {
        $path = Container::get('app')->getRuntimePath() . 'cache' . DIRECTORY_SEPARATOR;
        $files = (array) glob($path . (config('cache.default.prefix') ? config('cache.default.prefix') . DIRECTORY_SEPARATOR : '') . '*');
        foreach ($files as $path) {
            if (is_dir($path)) {
                $matches = glob($path . DIRECTORY_SEPARATOR . '*.php');
                array_map('del_expired_file_cache', $matches);
                // 文件夹为空则删除
                if(empty(array_diff(scandir($path),array('..','.')))) {
                    rmdir($path);
                }
            } else {
                del_expired_file_cache($path);
            }
        }
        $this->request->act_log = '清空过期缓存'; 
        success('ok!');
    }


    // 验证码
    public function get_verify() {
        $key = $this->request->param('key/s');
        if(empty($key)) {
            error('参数错误');
        }
        CaptchaMain::show($key);
    }


}
