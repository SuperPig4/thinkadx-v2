<?php

use think\migration\db\Column;
use think\migration\Migrator;
use \Phinx\Db\Adapter\MysqlAdapter;

class Admin extends Migrator {
    /**
    * Change Method.
    *
    * Write your reversible migrations using this method.
    *
    * More information on writing migrations is available here:
    * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
    *
    * The following commands can be used in this method and Phinx will
    * automatically reverse them when rolling back:
    *
    *    createTable
    *    renameTable
    *    addColumn
    *    renameColumn
    *    addIndex
    *    addForeignKey
    *
    * Remember to call 'create()' or 'update()' and NOT 'save()' when working
    * with the Table class.
    */

    public function change() {
        $table = $this->table('admin', ['comment' => '管理员列表']);
        $table->addColumn('group_id', 'integer', [
            'default' => '0'
        ])
        ->addColumn('avatar', 'string', [
            'default' => '',
            'comment' => '用户头像 相对路径'
        ])
        ->addColumn('nickname', 'string', [
            'limit'   => 34,
            'comment' => '用户名'
        ])
        ->addColumn('access', 'string', [
            'limit'   => 34,
            'comment' => '账号'
        ])
        ->addColumn('status', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '状态 0:暂停 1:正常'
        ])
        ->addColumn('create_time', 'integer')
        ->addIndex(['group_id'])
        ->create();
    }
}
