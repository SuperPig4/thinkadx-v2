<?php

use think\migration\Migrator;
use think\migration\db\Column;
use \Phinx\Db\Adapter\MysqlAdapter;

class AdminApi extends Migrator
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
        $table = $this->table('admin_api', ['comment' => 'api权限表']);
        $table->addColumn('appid', 'string')
        ->addColumn('appsecret', 'string')
        ->addColumn('status', 'integer', [
            'limit' => MysqlAdapter::INT_TINY,
            'default' => 0,
            'comment' => '状态 0:暂停 1:正常'
        ])
        ->addColumn('update_time', 'integer')
        ->addColumn('create_time', 'integer')
        ->create();
    }
}
