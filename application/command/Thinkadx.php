<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\input\Argument;
use think\console\Output;
use think\facade\Env;
use think\Db;
use app\admin\model\Admin;
use app\admin\model\AdminOauth;
use app\admin\model\AdminGroup;
use app\admin\model\AdminRule;

class Thinkadx extends Command
{
    protected function configure() {
        // 指令配置
        $this->setName('thinkadx')
        ->addOption('init', null, Option::VALUE_REQUIRED, 'init action')
        ->setDescription('thinkadx');
    }

    protected function execute(Input $input, Output $output)
    {
        $result = '';
        if($input->hasOption('init')) {
            $initType = $input->getOption('init');
            switch($initType) {
                case 'database' : 
                        $myfile = fopen('mysql.sql', 'r');
                        $sqlString = fread($myfile,filesize("mysql.sql"));
                        fclose($myfile);
                        $sqlArr = explode(';', $sqlString);
                        //执行sql语句
                        array_pop($sqlArr);
                        foreach ($sqlArr as $_value) {
                            Db::execute($_value.';');
                        }
                        $output->writeln('database write success');
                    break;
                case 'data' :
                        if(Admin::count() > 0) {
                            throw new \think\Exception('admin init fail', 10006);
                        }

                        //创建系统默认图标
                        $this->createDefaultIcon($output);

                        //创建菜单

                        //创建配置项
                        // Db::name('config')

                        //创建权限相关
                        $adminRule = AdminRule::create(['rule'=>'*/*:*', 'des'=>'所有权限']);
                        $adminGroup = AdminGroup::create(['name'=>'所有权限', 'status'=>1, 'rules'=> $adminRule->id]);
                        $output->writeln('permission data write success');

                        //创建用户信息
                        $adminUser = Admin::create([
                            'nickname' => '超级管理员',
                            'access' => 'admin',
                            'group_id' => $adminGroup->id
                        ])->adminOauth()
                        ->save(AdminOauth::getPasswordBaseConfig('admin'));
                        $output->writeln('admin data write success');

                    break;
            }
            // $result = 'init'.$input->getOption('init');;
        }

    	// 指令输出
    }

    protected function createDefaultIcon($output) {
        $systemDefaultIconDir = Env::get('root_path') . 'public/uploads/system_default_icon/';
        if(!is_dir($systemDefaultIconDir)) {
            mkdir($systemDefaultIconDir, '0744', true);
        }

        //默认头像
        $defaultUserAvatarbase = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAADtklEQVRoQ9WZ320TQRDGv1QAeeSJIPEOqQCoIFABUAFQAVABpIJABUAFkAqACoAOQBQA+lk71nA5383M3llkpMh2vLf7ffN/xwdaXu5LuiXpqP1xwhdJPyV9aO8XO/VgoZ2uSnolCfC8n5Lvkt5IernE2UsQeCHpyQD4D0kA9XJb0hX3D75/Jul9D5EeAqb1Rw7AW0mvJ9wECz2VdMc9w+fTKokeAp8ckHNJEBlqfReuu82NrrcFuBOWTEuVAD78sJ2G1r0VoiCwIEog4JHHjVT0+c26CgG097GdQlbBLariSZClbrRsFd6vQoCUiNYIVAKTg3uEPT63DYgFYiIsWQJo+12PyXcg8y55mFFKloAdhPYpVEsJe32rKCZLAHchl6dNHWBqrpmKqyyBPw3Ig94CNEKI+kFBJCWTKEKSIeCzz72WAkOHBBdRB563tWFc4YVNK5Y+LyUBn+7WJPAr0BBujZqxAA9ZDJSq5owrWYZbLQY4n16H/qXaPkxxKO2dtYBlilLZn0DvE0Qqw2UJ+Dgod5AjRKyzTfk/+2QJ8Iz5KlYgmClAPUIne1apwlUClH1AU5F5hUS1ocOipGa60q+tOUwpo2IBDvBagwR+G73MGED8nsYQ8LgOn9PWrBIAhAU077EAbTDZaU4ATMvgb2CpwPUH9BAYWsLSLMTI5UNtouGTZj2bXJQ1byR6CbAPfgxof1GfswLfYy2sVo2fzRm9BNAkWgUIRDLCOIWMRvtclioB82OADwdZXHbQKkFt3/2WdE3SzcFsyNyOeIjEzwWiFQIWgB44Po82KUhz2QhLEQ+Qt7GKEaHHYo+wZAiQ/yk4/rKB1tDeHOhdgNiL5338EE9U+VBsRAn4nA2Y7CBrTqPDQVe4tkQI+KIFEOaZaGlpwSXZ1wZmoVZljoAH352zg4z9mbMkpgj4jehTMHPIL4NAp5YR6AQz/dYkiV0E/ABr3+CN2JDE8ViyGCNAtmHUZ00Wn/el+aFV/EWHwIbEPzJGAPCw35fPz3mcd+ULA7UhAYoLPxWtmW3mAI99T9tBy4JghW2j6Al410lNBiqIks/gzhRLgprg5hK1EU/AT4iZ01eraxJbePno1dMI+OnwGiOTMMqZhTZ64RUlby3gff9/1L7x8lbYxIJZgNk8VkiNtpdSa3IfG/FvPAUCftZTvpsmQfQs98O1Qwh490n9vNODouNZ3yUcQ6A0VO0AsMSj2yEzBKzyrvGz0RJgx/awn6NOIWBslpx1rgXc9rVZ6vmlJ/AXpLrZ0ULE0uYAAAAASUVORK5CYII=';
        $file = $this->createBaseFile('avatar', $defaultUserAvatarbase, $systemDefaultIconDir);

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

}
