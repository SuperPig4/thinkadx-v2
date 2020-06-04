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
        $table = $this->table('admin_oauth', ['comment' => '第三方授权表 - 每条数据都有自己的 access_token和refresh_token 都是相对的']);
        $table->addColumn('admin_id', 'integer')
        ->addColumn('identifier', 'string', [
            'comment' => '第三方标识符 如:微信公众号授权登陆 则保存它的openid，unionid则保存到unique_identifier字段'
        ])
        ->addColumn('unique_identifier', 'string', [
            'comment' => '唯一标识符 主要是用来存储相对于用户全局唯一的标识符'
        ])
        ->addColumn('oauth_type', 'string', [
            'limit'   => 12,
            'comment' => '类型 auth:授权'
        ])
        ->addColumn('port_type', 'string', [
            'limit'   => 12,
            'comment' => '终端类型 wxxcx:微信小程序'
        ])
        ->addColumn('access_token', 'string')
        ->addColumn('refresh_token', 'string')
        ->addColumn('last_use_access_token', 'string', [
            'comment' => '最后一次使用的access_token'
        ])
        ->addColumn('access_token_create_time', 'integer', [
            'comment' => '访问令牌最后一次刷新时间'
        ])
        ->addColumn('refresh_token_create_time', 'integer', [
            'comment' => '刷新令牌最后一次刷新时间'
        ])
        ->addColumn('create_time', 'integer')
        ->addIndex(['admin_id'])
        ->create();
    }
}
