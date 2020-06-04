<?php

use think\migration\Migrator;
use think\migration\db\Column;
use \Phinx\Db\Adapter\MysqlAdapter;

class AdminGroup extends Migrator
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
        $table = $this->table('admin_group', ['comment' => '管理员权限分组表']);
        $table->addColumn('name', 'string', [
            'limit'   => 24,
            'comment' => '姓名'
        ])
        ->addColumn('rules', 'text', [
            'comment' => '规则ID 用,分隔'
        ])
        ->addColumn('status', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => '0',
            'comment' => '状态 0:暂停 1:正常'
        ])
        ->addColumn('create_time', 'integer')
        ->create();
    }
}
