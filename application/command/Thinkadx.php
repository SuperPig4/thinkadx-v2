<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\input\Argument;
use think\console\Output;
use think\facade\Env;
use think\Db;
use app\common\model\Admin;
use app\common\model\AdminOauth;
use app\common\model\AdminGroup;
use app\common\model\AdminRule;
use think\Container;

class Thinkadx extends Command
{
    protected function configure() {
        // 指令配置
        $this->setName('thinkadx')
        ->addOption('init', null, Option::VALUE_REQUIRED, 'init action')
        ->addOption('action', null, Option::VALUE_REQUIRED, 'action name')
        ->setDescription('thinkadx');
    }

    protected function execute(Input $input, Output $output)
    {
        $result = '';
        if($input->hasOption('init')) {
            $initType = $input->getOption('init');
            switch($initType) {
                case 'mysql-data' :
                        // 检测是否可以导入
                        try {
                            if(Admin::count() > 0) {
                                throw new \think\Exception('init fail, data exists', 10006);
                            }
                        } catch(\think\exception\PDOException $e) {
                            throw new \think\Exception('init fail, not table exists', 10006);
                        }   

                        // 创建数据
                        $this->writeDatebaseData();
                        $output->writeln('data write success...');
                        // 创建系统资源
                        $output->writeln('create resources ...');
                        $this->createDefaultIcon($output);

                        $output->writeln('init success!!!');
                    break;
            }
        } else if($input->hasOption('action')) {
            // 操作
            $initType = $input->getOption('action');
            switch($initType) {
                case 'empty_expired_cache' : 
                        // 清空过期缓存
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
                        $output->writeln('action success');
                    break;
            }
        }

    }

    protected function createDefaultIcon($output) {
        $systemDefaultIconDir = Env::get('root_path') . 'public/uploads/system_default_icon/';
        if(!is_dir($systemDefaultIconDir)) {
            mkdir($systemDefaultIconDir, '0744', true);
        }

        // 默认头像
        $defaultUserAvatarbase = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKTWlDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVN3WJP3Fj7f92UPVkLY8LGXbIEAIiOsCMgQWaIQkgBhhBASQMWFiApWFBURnEhVxILVCkidiOKgKLhnQYqIWotVXDjuH9yntX167+3t+9f7vOec5/zOec8PgBESJpHmomoAOVKFPDrYH49PSMTJvYACFUjgBCAQ5svCZwXFAADwA3l4fnSwP/wBr28AAgBw1S4kEsfh/4O6UCZXACCRAOAiEucLAZBSAMguVMgUAMgYALBTs2QKAJQAAGx5fEIiAKoNAOz0ST4FANipk9wXANiiHKkIAI0BAJkoRyQCQLsAYFWBUiwCwMIAoKxAIi4EwK4BgFm2MkcCgL0FAHaOWJAPQGAAgJlCLMwAIDgCAEMeE80DIEwDoDDSv+CpX3CFuEgBAMDLlc2XS9IzFLiV0Bp38vDg4iHiwmyxQmEXKRBmCeQinJebIxNI5wNMzgwAABr50cH+OD+Q5+bk4eZm52zv9MWi/mvwbyI+IfHf/ryMAgQAEE7P79pf5eXWA3DHAbB1v2upWwDaVgBo3/ldM9sJoFoK0Hr5i3k4/EAenqFQyDwdHAoLC+0lYqG9MOOLPv8z4W/gi372/EAe/tt68ABxmkCZrcCjg/1xYW52rlKO58sEQjFu9+cj/seFf/2OKdHiNLFcLBWK8ViJuFAiTcd5uVKRRCHJleIS6X8y8R+W/QmTdw0ArIZPwE62B7XLbMB+7gECiw5Y0nYAQH7zLYwaC5EAEGc0Mnn3AACTv/mPQCsBAM2XpOMAALzoGFyolBdMxggAAESggSqwQQcMwRSswA6cwR28wBcCYQZEQAwkwDwQQgbkgBwKoRiWQRlUwDrYBLWwAxqgEZrhELTBMTgN5+ASXIHrcBcGYBiewhi8hgkEQcgIE2EhOogRYo7YIs4IF5mOBCJhSDSSgKQg6YgUUSLFyHKkAqlCapFdSCPyLXIUOY1cQPqQ28ggMor8irxHMZSBslED1AJ1QLmoHxqKxqBz0XQ0D12AlqJr0Rq0Hj2AtqKn0UvodXQAfYqOY4DRMQ5mjNlhXIyHRWCJWBomxxZj5Vg1Vo81Yx1YN3YVG8CeYe8IJAKLgBPsCF6EEMJsgpCQR1hMWEOoJewjtBK6CFcJg4Qxwicik6hPtCV6EvnEeGI6sZBYRqwm7iEeIZ4lXicOE1+TSCQOyZLkTgohJZAySQtJa0jbSC2kU6Q+0hBpnEwm65Btyd7kCLKArCCXkbeQD5BPkvvJw+S3FDrFiOJMCaIkUqSUEko1ZT/lBKWfMkKZoKpRzame1AiqiDqfWkltoHZQL1OHqRM0dZolzZsWQ8ukLaPV0JppZ2n3aC/pdLoJ3YMeRZfQl9Jr6Afp5+mD9HcMDYYNg8dIYigZaxl7GacYtxkvmUymBdOXmchUMNcyG5lnmA+Yb1VYKvYqfBWRyhKVOpVWlX6V56pUVXNVP9V5qgtUq1UPq15WfaZGVbNQ46kJ1Bar1akdVbupNq7OUndSj1DPUV+jvl/9gvpjDbKGhUaghkijVGO3xhmNIRbGMmXxWELWclYD6yxrmE1iW7L57Ex2Bfsbdi97TFNDc6pmrGaRZp3mcc0BDsax4PA52ZxKziHODc57LQMtPy2x1mqtZq1+rTfaetq+2mLtcu0W7eva73VwnUCdLJ31Om0693UJuja6UbqFutt1z+o+02PreekJ9cr1Dund0Uf1bfSj9Rfq79bv0R83MDQINpAZbDE4Y/DMkGPoa5hpuNHwhOGoEctoupHEaKPRSaMnuCbuh2fjNXgXPmasbxxirDTeZdxrPGFiaTLbpMSkxeS+Kc2Ua5pmutG003TMzMgs3KzYrMnsjjnVnGueYb7ZvNv8jYWlRZzFSos2i8eW2pZ8ywWWTZb3rJhWPlZ5VvVW16xJ1lzrLOtt1ldsUBtXmwybOpvLtqitm63Edptt3xTiFI8p0in1U27aMez87ArsmuwG7Tn2YfYl9m32zx3MHBId1jt0O3xydHXMdmxwvOuk4TTDqcSpw+lXZxtnoXOd8zUXpkuQyxKXdpcXU22niqdun3rLleUa7rrStdP1o5u7m9yt2W3U3cw9xX2r+00umxvJXcM970H08PdY4nHM452nm6fC85DnL152Xlle+70eT7OcJp7WMG3I28Rb4L3Le2A6Pj1l+s7pAz7GPgKfep+Hvqa+It89viN+1n6Zfgf8nvs7+sv9j/i/4XnyFvFOBWABwQHlAb2BGoGzA2sDHwSZBKUHNQWNBbsGLww+FUIMCQ1ZH3KTb8AX8hv5YzPcZyya0RXKCJ0VWhv6MMwmTB7WEY6GzwjfEH5vpvlM6cy2CIjgR2yIuB9pGZkX+X0UKSoyqi7qUbRTdHF09yzWrORZ+2e9jvGPqYy5O9tqtnJ2Z6xqbFJsY+ybuIC4qriBeIf4RfGXEnQTJAntieTE2MQ9ieNzAudsmjOc5JpUlnRjruXcorkX5unOy553PFk1WZB8OIWYEpeyP+WDIEJQLxhP5aduTR0T8oSbhU9FvqKNolGxt7hKPJLmnVaV9jjdO31D+miGT0Z1xjMJT1IreZEZkrkj801WRNberM/ZcdktOZSclJyjUg1plrQr1zC3KLdPZisrkw3keeZtyhuTh8r35CP5c/PbFWyFTNGjtFKuUA4WTC+oK3hbGFt4uEi9SFrUM99m/ur5IwuCFny9kLBQuLCz2Lh4WfHgIr9FuxYji1MXdy4xXVK6ZHhp8NJ9y2jLspb9UOJYUlXyannc8o5Sg9KlpUMrglc0lamUycturvRauWMVYZVkVe9ql9VbVn8qF5VfrHCsqK74sEa45uJXTl/VfPV5bdra3kq3yu3rSOuk626s91m/r0q9akHV0IbwDa0b8Y3lG19tSt50oXpq9Y7NtM3KzQM1YTXtW8y2rNvyoTaj9nqdf13LVv2tq7e+2Sba1r/dd3vzDoMdFTve75TsvLUreFdrvUV99W7S7oLdjxpiG7q/5n7duEd3T8Wej3ulewf2Re/ranRvbNyvv7+yCW1SNo0eSDpw5ZuAb9qb7Zp3tXBaKg7CQeXBJ9+mfHvjUOihzsPcw83fmX+39QjrSHkr0jq/dawto22gPaG97+iMo50dXh1Hvrf/fu8x42N1xzWPV56gnSg98fnkgpPjp2Snnp1OPz3Umdx590z8mWtdUV29Z0PPnj8XdO5Mt1/3yfPe549d8Lxw9CL3Ytslt0utPa49R35w/eFIr1tv62X3y+1XPK509E3rO9Hv03/6asDVc9f41y5dn3m978bsG7duJt0cuCW69fh29u0XdwruTNxdeo94r/y+2v3qB/oP6n+0/rFlwG3g+GDAYM/DWQ/vDgmHnv6U/9OH4dJHzEfVI0YjjY+dHx8bDRq98mTOk+GnsqcTz8p+Vv9563Or59/94vtLz1j82PAL+YvPv655qfNy76uprzrHI8cfvM55PfGm/K3O233vuO+638e9H5ko/ED+UPPR+mPHp9BP9z7nfP78L/eE8/sl0p8zAAAAIGNIUk0AAHolAACAgwAA+f8AAIDpAAB1MAAA6mAAADqYAAAXb5JfxUYAAAQESURBVHja5JnflbI6FMU3BcySDsAKjBWAFagVgBUYK5CpwFiBTAViBWQqIL7PWsaneTRUwH2CG0f9JPxxvrvueVSX5Hdy9snJxiqKokCHkSQJjscjpJSQUgIACCGwbRvT6RSEkC4fB6sLAKUUVqsVkiSBUuqPv3VdF2EYYr1e/x0AURRhu91eLdxxHLiue/U7IQTyPL8C2Ww2mM1mvwNQZj2O4+qzIAhAKX1YJkmSgDGGz8/P6jPGGJbL5esBfN+vFuJ5HuI4vsn6o+CcIwxDnM9nAMB6vUYURa8DCMMQHx8fVdb1XTDZQd/3cTweAQC73Q5hGPYPwDnHZDIBAEynUyRJ0kr8JYRt2zidTrBtu18AQgiOxyMcx4EQwviBP0MIgfF4DABYLpdgjPUHkCQJ5vN5qy1/VpKXy8UoKUYA5YMcx6kOqS5CSonhcNgoMUYAtm0jz/NGW123NE11ZQRgWRYAYL/ftz6AfgalFNvtFp7ngXPePYDefdI0he/7nQJEUYT393cAgElf+f8A6O2uT4DBYPB0IGytgS5b6M8O15sGygnyfD43Hh/6+G8jgLJTND326+jLtMMZAeg6aDNBPppsTeu/0SxU1qpt20jTtPUVMY5jLBaLxtoyBpBSghCCPM9BCEGapo1LSQiByWQCpRRGoxGEEK+5D+hZI4Rgv9/XvszodT+fz6GUwmAwAOe80W42vpGVgi5nJMYYgiCodQfYbrdX+mkzmrS61Os7UbZCSik8z7vJJucch8MBcRxXQm2T+c5cCSEEKKVXF/U6EQQBGGOtW3ErAKUUDocDGGPGApzNZgjDENPp9PUAZR0zxm76tuM4sG0brutW3729veH7+xtfX19X3lBZdlEU1dJPJwClAPWFe56HMAzh+/7TbiSEAOccjLHKVilBdrud8ZBYG0BKicVicTVoBUGAKIqMW6gu7CiKrvRDKcV6va6tjVoAes9uYmSZGl0mZ8tTgJ+tcrPZgFKKrkMpBUpp5U7UHVX+CKAvvouebXq21IF4CKD/0Wg0Aue8s/G5ztni+z7yPH8KcRdAN7BevfhHEFmW3dXEDYCUEuPxuBqypJQvX/y9iw4hBFmWPQcYj8cQQrys5k00cc9QuwJgjGG1WvXabZrEbDbD4XAAAGRZdpXUCkAvHVNnoO9QSsF1XeR5Dt/3kabpLYDuEJ9Op84OqT5KSb96WkVRFLo73Idl0rX14rouTqfTvwB67f+N2b+3C6UWrKIoiuFwCCll61dGr4jS4i8rxcqyrCi9nj5s865DN9culwuszWZTlOVj+nrnN0KfErIsgxUEQdHEVP3N0E1mixBSCCF6eW3UV5Svo5bLJSwABdCt19l3lF6q53n/fYB/BgD4kyq+P0OONgAAAABJRU5ErkJggg==';
        $file = $this->createBaseFile('avatar', $defaultUserAvatarbase, $systemDefaultIconDir);

        // 菜单-系统设置
        $defaultUserAvatarbase = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABUElEQVQ4T6WTMVLCUBCGdzMBKfEGcgLhBtwAbSUzWCcWsZDYSWliYSh4Y4kzwbHEE+gNhBtwA2MHQvI7LwjmQUAYU2X2f/vl3+z/mDY8davZZeJGIoPagXDtrKOcLtatph1FWr9QGIez6cHHr4ZQz32VxuNCUddQC4TbXmhLQN10+sxUmwsIibi4yR0B/UB4p1JPAIbZbBHzzcaGDAGEy17H8+eAi+sqAa/7AGYRl54fbkcJ4Ny2i+rMS9Tw5+14Fa7nJodd3w/ZMB2fGI3VmWPElSdxN5CNZ+ZVWWPtXYUgBFGXDctBhvVh0HHL6bphORK25iQTAGDQE15lJ4ASmFTH3yPIfOFx60+UTpJdMyvjyJqyhX+vUUaYie/3yYESJNmoRnk7CqCXnnBPllFeHJdOALzl89NROlgAfUYxl3WdjiiOq4HwWmuXafWbu17nb8BYpTIiRWX4AAAAAElFTkSuQmCC';
        $file = $this->createBaseFile('system_setup', $defaultUserAvatarbase, $systemDefaultIconDir);

        // 菜单-管理员管理
        $defaultUserAvatarbase = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABnElEQVQ4T52TPVLCUBSFz33hr2QHsgNxB3EHlA6koHSCRRqBkpKfQpzRYMmMwbHMEuIOsgPZgdgphHedGycYYoQZUyW595137vfOI+Q8F5f9WpG2J1vC+5M7CfN6kn+ULrYdp7rZlKbEtAJzAEIdgBlp1X5+GC7zhPYELLs31dDz9K6x6Lo8X7ijxlGBVqc7X9yP29nGpt1vK4WldzcMsrWdA+uqb0Jr03PHg2zTodpOIJn/3w5k178YROuS77lj8yiDBBiIXxkUKOY6g862mhzD0ANi1AAE6TH3TkFgEbhB4BCMMGIVFpQWqAyAZKG4BPgNoJXnjm5/IEqBOfBmYz+xGtNnvRKhSuVjtf0snj7OJi8StFhYqSAWsOzuQD6yx9S0r+tE5AjYlt3zxRkTavJt2d2gUFo36FuNHc8dOVlIwiRalwdSkwWSSGGRCEq4qNXpOswc5GU+nleRL86SLGiwL73JeCRNebuLG3FnKJ7KOzHPhU8MmnR8pOKERDkvor/S2OmFDL4BcJ4O294xHrq2AlQpoxpFWKZv5hd5pPdsMdrFwAAAAABJRU5ErkJggg==';
        $file = $this->createBaseFile('admin_manage', $defaultUserAvatarbase, $systemDefaultIconDir);

        $output->writeln($systemDefaultIconDir);
    }


    /**
     * 创建文件
     * @param string $name 文件名
     * @param string $content 文件内容
     * @param string $path 文件路径
     * @return void
     */
    protected function createBaseFile($name, $content, $path) {
        //正则匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $content, $result)) {
            $type = $result[2];  //获取图片格式
            $new_file = $path . $name . ".{$type}";
            file_put_contents($new_file, base64_decode(str_replace($result[1], '', $content)));
        }
    }


    // 往数据库写数据
    protected function writeDatebaseData() {
        $timeStr = time();

        // admin
        Db::name('admin')->insert([
            'avatar'        => '',
            'group_id'      => 1,
            'nickname'      => '超级管理员',
            'access'        => 'admin',
            'create_time'   => $timeStr,
        ]);

        // admin_group
        Db::name('admin_group')->insert([
            'name'          => '所有权限',
            'status'        => '1',
            'rules'         => '1',
            'create_time'   => $timeStr,
        ]);

        // admin_menu
        Db::name('admin_menu')->insertAll([
            [
                'icon'        => '/uploads/system_default_icon/system_setup.png',
                'title'       => '系统设置',
                'module'      => '',
                'controller'  => '',
                'action'      => '',
                'status'      => 1,
                'father_id'   => 0,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '菜单设置',
                'module'      => 'admin',
                'controller'  => 'menu',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 1,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '/uploads/system_default_icon/admin_manage.png',
                'title'       => '管理员管理',
                'module'      => '',
                'controller'  => '',
                'action'      => '',
                'status'      => 1,
                'father_id'   => 0,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '管理员列表',
                'module'      => 'admin',
                'controller'  => 'admin_user',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 3,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '管理员分组',
                'module'      => 'admin',
                'controller'  => 'admin_group',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 3,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '分组规则',
                'module'      => 'admin',
                'controller'  => 'admin_rule',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 3,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '系统配置',
                'module'      => 'admin',
                'controller'  => 'config',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 1,
                'create_time' => $timeStr,
            ],
            [
                'icon'        => '',
                'title'       => '管理员操作日志',
                'module'      => 'admin',
                'controller'  => 'admin_log',
                'action'      => 'index',
                'status'      => 1,
                'father_id'   => 1,
                'create_time' => $timeStr,
            ]
        ]);

        // admin_oauth
        Db::name('admin_oauth')->insert([
            'admin_id'          =>  1,
            'identifier'        => '3b1f1f4eafccab421abac7b9bfe056b6',
            'unique_identifier' => '738607423',
            'oauth_type'        => 'password',
            'port_type'         => 'api',
            'access_token'      => '',
            'refresh_token'              => '',
            'last_use_access_token'      => '',
            'refresh_token_create_time'  => '',
            'access_token_create_time'   => '',
            'create_time'                => $timeStr,
        ]);

        // admin_rule
        Db::name('admin_rule')->insert([
            'rule'          =>  '*/*:*',
            'des'           => '所有权限',
            'create_time'   => $timeStr,
        ]);

        // admin_rule
        Db::name('config')->insertAll([
            [
                'name'            =>  '管理员访问令牌周期',
                'alias'           => 'admin_access_token_time_out',
                'type'            => 'system',
                'value'           => '7200',
                'description'     => '秒位单位',
            ],
            [
                'name'            =>  '管理员刷新令牌周期',
                'alias'           => 'admin_refresh_token_time_out',
                'type'            => 'system',
                'value'           => '604800',
                'description'     => '秒位单位',
            ],
            [
                'name'            =>  '后台系统名称',
                'alias'           => 'admin_system_name',
                'type'            => 'system',
                'value'           => '某某管理系统',
                'description'     => '后台系统名称',
            ],
        ]);

        // admin_api
        Db::name('admin_api')->insert([
            'appid'               => 'xb4nBB6ZfEN7njAnlJHMRuR8',
            'appsecret'           => 'u6ucLCg9RWXRDm6TiEeYBhHK',
            'status'              => '1',
            'update_time'         => $timeStr,
            'create_time'         => $timeStr
        ]);
    }

}
