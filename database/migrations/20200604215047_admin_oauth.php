<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminOauth extends Migrator
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
        $table = $this->table('admin_oauth', ['comment' => '第三方授权表']);
        $table->addColumn('admin_id', 'integer')
        ->addColumn('identifier', 'string', [
            'default' => '',
            'comment' => '局部唯一标识符'
        ])
        ->addColumn('unique_identifier', 'string', [
            'default' => '',
            'comment' => '全局唯一标识符'
        ])
        ->addColumn('oauth_type', 'string', [
            'limit'   => 12,
            'comment' => '类型授权'
        ])
        ->addColumn('port_type', 'string', [
            'limit'   => 12,
            'comment' => '终端类型'
        ])
        ->addColumn('access_token', 'string', [
            'default' => '',
            'comment' => '访问令牌'
        ])
        ->addColumn('refresh_token', 'string', [
            'default' => '',
            'comment' => '刷新令牌'
        ])
        ->addColumn('last_use_access_token', 'string', [
            'default' => '',
            'comment' => '最后一次使用的访问令牌'
        ])
        ->addColumn('last_use_refresh_token', 'string', [
            'default' => '',
            'comment' => '最后一次使用的刷新令牌'
        ])
        ->addColumn('access_token_create_time', 'integer', [
            'default' => 0,
            'comment' => '访问令牌创建时间'
        ])
        ->addColumn('refresh_token_create_time', 'integer', [
            'default' => 0,
            'comment' => '刷新令牌创建时间'
        ])
        ->addColumn('create_time', 'integer')
        ->addIndex(['admin_id'])
        ->create();
    }
}
