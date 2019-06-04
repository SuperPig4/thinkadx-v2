<?php

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\input\Argument;
use think\console\Output;
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

                        //创建菜单

                        //创建权限相关
                        $adminRule = AdminRule::create(['rule'=>'*/*:*', 'des'=>'所有权限']);
                        $adminGroup = AdminGroup::create(['name'=>'所有权限', 'status'=>1, 'rules'=> $adminRule->id]);
                        $output->writeln('permission data write success');

                        //创建用户信息
                        $adminUser = Admin::create([
                            'nickname' => '超级管理员',
                            'access' => 'admin',
                            'group_id' => $adminGroup->id
                        ])->admin_oauth()
                        ->save(AdminOauth::getPasswordBaseConfig('admin'));
                        $output->writeln('admin data write success');

                    break;
            }
            // $result = 'init'.$input->getOption('init');;
        }

    	// 指令输出
    	
    }
}
