<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminRule extends Migrator
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
        $table = $this->table('admin_rule', ['comment' => '管理员权限规则表']);
        $table->addColumn('rule', 'text', [
            'comment' => '匹配规则'
        ])
        ->addColumn('des', 'string', [
            'comment' => '规则描述'
        ])
        ->addColumn('create_time', 'integer')
        ->create();
    }
}
