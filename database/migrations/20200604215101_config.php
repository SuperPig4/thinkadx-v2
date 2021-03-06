<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Config extends Migrator
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
        $table = $this->table('config', ['comment' => '系统配置']);
        $table->addColumn('name', 'string', [
            'limit'   => 34,
            'comment' => '匹配规则'
        ])
        ->addColumn('alias', 'string', [
            'limit'   => 34,
            'comment' => '别名'
        ])
        ->addColumn('type', 'string', [
            'limit'   => 34,
            'comment' => '配置分类'
        ])
        ->addColumn('value', 'text', [
            'comment' => '内容'
        ])
        ->addColumn('description', 'string', [
            'default' => '',
            'comment' => '描述'
        ])
        ->create();
    }
}
