<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-15 09:44:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 04:51:50
# Description: 通用控制器基类
# ============================================================================= */

namespace Thinkadx\Traits;

use think\facade\Request;
use think\facade\Validate;

trait ControllerBaseTrait {


    // Array  当该属性被定义时基类将不会接受外部参数,而是通过该属性获取
    protected $baseParam;
    // String 基类所有数据库操作都用Db.优先级低于model
    protected $tableName;
    // String 直接实例化实例化模型.优先级高于table
    protected $modelName;
    // Array OR String 操作记录
    protected $logs = [];


    /**
     * 公共列表
     *
     * @return void
     */
    public function index() {
        $params = $this->getParams();
        $db = $this->getModelOrDb();

        // 检测是否有条件回调
        if(method_exists($this, 'index_where_callback')) {
            $db = $this->index_where_callback($db, $params);
        }

        // 查询
        if(isset($params['count'])) {
            success('ok!', $db->count());
        } else {
            // 判断是否需要分页
            if(!isset($params['all'])) {
                $db->limit(25)->page(
                    (isset($params['p'])  ? $params['p'] : 1)
                );
            }
            success('ok!', $db->select());
        }
    }


    /**
     * 公共详情
     *
     * @return void
     */
    public function detail() {
        $params = $this->getParams();
        $db = $this->getModelOrDb();
        $validate = Validate::make(['id' => 'require']);

        if($validate->check($params) === false) {
            error($validate->getError());
        }

        $info = $db->where('id', $params['id'])->find();
        success('ok!', $info);
    } 


    /**
     * 新增
     *
     * @return void
     */
    public function add() {
        $this->add_edit();
    }


    /**
     * 编辑
     *
     * @return void
     */
    public function edit() {
        $this->add_edit();
    }


    /**
     * 公共删除
     *
     * @return void
     */
    public function delete() {
        $params   = $this->getParams();
        $db       = $this->getModelOrDb();
        $dbType   = $this->getModelOrDb(true);
        $validate = Validate::make(['id' => 'require']);

        if($validate->check($params) === false) {
            error($validate->getError());
        }
        $id = $params['id'];

        // 删除
        if($dbType == 'model') {
            $actStatus = $db->destroy($id);
        } else {
            $actStatus = $db->whereIn('id', $id)->delete();
        }

        // 写入操作记录
        $log = isset($this->logs['delete']) ? $this->logs['delete'] : '' ;
        if($actStatus !== false) {
            empty($log) === false && ($this->request->act_log = is_array($log) ? $log[0] : $log);
            success('操作成功!');
        } else {
            (empty($log) === false && is_array($log)) && ($this->request->act_log = $log[1]);
            error('操作失败');
        }
    }


    /**
     * 公共新增 OR 编辑
     *
     * @return void
     */
    private function add_edit() {
        $params = $this->getParams();
        $db     = $this->getModelOrDb();
        $dbType = $this->getModelOrDb(true);
        $id     = $params['id'];
        
        // 执行操作
        if($dbType == 'model') {
            $actStatus = $db->allowField(true)->save(
                $params,
                empty($id) ? [] : ['id' => $id]
            );
        } else {
            $db = $db->strict(false);
            $actStatus = empty($id) ? $db->insert($params) : $db->update($params);
        }
        
        // 获得操作记录
        $log = '';
        if(isset($this->logs)) {
            if(empty($id) === false) {
                $log = $this->logs['edit'];
            } else {
                $log = $this->logs['add'];
            }
        }

        // 写入操作记录
        if($actStatus !== false) {
            empty($log) === false && ($this->request->act_log = is_array($log) ? $log[0] : $log);
            success('操作成功!');
        } else {
            (empty($log) === false && is_array($log)) && ($this->request->act_log = $log[1]);
            error('操作失败');
        }
    }


    /**
     * 获得模型或DB
     *
     * @param bool $isReturnType 是否返回类型
     * 
     * @return object
     */
    final private function getModelOrDb($isReturnType = false) {
        $db = null;
        if(is_null($this->modelName) === false) {
            $db = $isReturnType ? 'model' : new $this->modelName;
        } else if(is_null($this->tableName) === false) {
            $db = $isReturnType ? 'table' : Db::name($this->tableName);
        } else {
            throw new \think\Exception('model or db get fail');
        }
        return $db;
    }


    /**
     * 获得参数
     *
     * @return array
     */
    final private function getParams() {
        if(isset($this->baseParam)) {
            return $this->baseParam;
        } else {
            return Request::param();
        }
    }

}