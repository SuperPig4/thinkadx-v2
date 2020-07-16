<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminLog extends Migrator
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
        $table = $this->table('admin_log', ['engine' => 'MyISAM', 'comment' => '管理员操作表']);
        $table->addColumn('admin_id', 'integer', [
            'default' => '0',
            'comment' => '管理员ID'
        ])
        ->addColumn('des', 'string', [
            'default' => '',
            'comment' => '描述'
        ])
        ->addColumn('ip', 'string', [
            'default' => '',
            'limit'   => 24,
            'comment' => '描述'
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
        ->addColumn('other_info', 'string', [
            'default' => '',
            'comment' => '其他信息'
        ])
        ->addColumn('act_time', 'integer', [
            'comment' => '操作时间'
        ])
        ->addIndex('des', ['type'=>'fulltext'])
        ->create();
    }
}
