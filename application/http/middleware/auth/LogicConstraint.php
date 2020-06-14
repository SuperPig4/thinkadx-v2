<?php

namespace app\http\middleware\auth;

abstract class LogicConstraint {

    /**
     * 操作对象名
     */
    // abstract static public function getActionName();

    /**
     * 获得缓存数据标识符名(类似 session.admin)
     * 
     * @return String
     */
    abstract static public function getAuthDataName();


    /**
     * 标识符相关联的模型
     * 
     * @return Class/String
     */
    abstract static public function getModel();


    /**
     * 验证成功
     */
    // abstract static public function success();


    /**
     * 验证不通过回调
     */
    abstract static public function fail($data = '');

    // 绑定到容器的名字 - 空则为loadData
    // abstract static public function containerName();
    

}