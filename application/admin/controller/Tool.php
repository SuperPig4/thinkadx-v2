<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:35:40
# Description: 工具类
# ============================================================================= */
namespace app\admin\controller;

use think\Db;
use think\Container;
use Thinkadx\Captcha\Main as CaptchaMain;
use Carbon\Carbon;

class Tool extends Base {

    public function main() {
   
        // 统计
        $count = [
            // 管理员总数
            'admin' => Db::name('admin')->count(),
            // 操作日志总数
            'action_log' => Db::name('adminLog')->count(),
            // 登录成功总数
            'login_success' => Db::name('adminLog')->where('des', '登陆成功')->count(),
            // 登录失败总数
            'login_fail' => Db::name('adminLog')->where('des','<>','登陆成功')->count()
        ];

        // 统计今日操作
        $computeRatio = function($num1, $num2) {
            return round($num1 / $num2 * 100);
        };
        $startTime = Carbon::now()->startOfDay()->timestamp;
        $todayCount = [
            // 管理员总数
            'admin' => $computeRatio(
                Db::name('admin')->where('create_time', '>=', $startTime)->count(), 
                $count['admin']
            ),
            // 操作日志总数
            'action_log' => $computeRatio(
                Db::name('adminLog')->where('act_time', '>=', $startTime)->count(),
                $count['action_log']
            ),
            // 登录成功总数
            'login_success' => $computeRatio(
                Db::name('adminLog')->where('act_time', '>=', $startTime)->where('des', '登陆成功')->count(),
                $count['login_success']
            ),
            // 登录失败总数
            'login_fail' => $computeRatio(
                Db::name('adminLog')->where('act_time', '>=', $startTime)->where('des','<>','登陆成功')->count(),
                $count['login_fail']
            )
        ];

        // 图表1 统计 - 日志操作频率
        $runWeekDate = function($week) {
            return Carbon::now()->weekday($week)->format('Y-m-d');
        };

        $chart1 = [];
        $chart2 = [];

        for($i = 0; $i < 7; $i++) {
            $chart1[$i] = Db::name('adminLog')->whereBetweenTime('act_time', $runWeekDate($i + 1))->count();
            $chart2[$i] = [
                Db::name('adminLog')->whereBetweenTime('act_time', $runWeekDate($i + 1))->where('des', '登陆成功')->count(),
                Db::name('adminLog')->whereBetweenTime('act_time', $runWeekDate($i + 1))->where('des','<>','登陆成功')->count()
            ];
        }

        success('ok!',[
            'chart_data_1' => $chart1,
            'chart_data_2' => $chart2,
            'today_count' => $todayCount,
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


    // 验证码 - 获得key
    public function get_verify_key() {
        $key = md5($this->request->ip().time().mt_rand(11111, 99999));
        cache($key, '1', 3600);
        success('ok!', $key);
    }


    // 验证码
    public function get_verify_img() {
        $key = $this->request->param('key/s');
        if(empty($key)) {
            error('参数错误');
        } else {
            $keyContent = cache($key);
            if($keyContent === '1') {
                CaptchaMain::show($key);
            }
        }
        error('请重新刷新验证码');
    }


}
