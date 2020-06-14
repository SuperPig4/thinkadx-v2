<?php

use think\migration\Migrator;
use think\migration\db\Column;
use \Phinx\Db\Adapter\MysqlAdapter;

class AdminMenu extends Migrator
{
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
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('admin_menu', ['comment' => '管理员菜单表']);
        $table->addColumn('icon', 'string')
        ->addColumn('title', 'string', [
            'default' => '',
            'limit'   => 34
        ])
        ->addColumn('module', 'string', [
            'default' => '',
            'limit'   => 34
        ])
        ->addColumn('controller', 'string', [
            'default' => '',
            'limit'   => 34
        ])
        ->addColumn('action', 'string', [
            'default' => '',
            'limit'   => 34
        ])
        ->addColumn('status', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => 1,
            'comment' => '是否显示 0:不显示 1:显示'
        ])
        ->addColumn('father_id', 'integer', [
            'default' => 0,
            'comment' => '上级id'
        ])
        ->addColumn('sort', 'integer', [
            'default' => 0,
            'comment' => '排序 越大越前'
        ])
        ->addColumn('create_time', 'integer', [
            'comment' => '操作时间'
        ])
        ->create();
    }
}
