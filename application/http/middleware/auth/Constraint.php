<?php

namespace app\http\middleware\auth;

use Thinkadx\LoadData;

abstract class Constraint {

    /**
     * 装载数据
     * 
     * @param array  $data  缓存数据
     * @param string $logic 逻辑类
     */
    public function loadData($request, $data, $logic) {
        $obj = new LoadData(
            $data, 
            $logic::getModel(), 
            $this, 
            $logic::getPk()
        );

        // 绑定容器
        $name = method_exists($logic, 'containerName') ?  $logic::containerName() : 'loadData' ;
        bind($name, $obj);
    }


    /**
     * 忽略检测
     * @param array 关联数组
     * @param string/bool 控制名
     * @param string/bool 操作名
     * @return bool true:忽略 false:不忽略
     */
    public function ignore_check($controller = true, $action = true, $ignoreList = []) {
        if($controller === true) $controller = request()->controller();
        if($action === true) $action = request()->action(true);

        $ignoreList = empty($ignoreList) ? $this->ignoreList : $ignoreList ;
        $ignoreAr = [];
        if(array_key_exists($controller, $ignoreList)) {
            $ignoreAr = $ignoreList[$controller];
        }

        if(in_array($action, $ignoreAr)) {
            return true;
        } else {
            return false;
        }
    }


    /**
     * 缓存逻辑
     * 
     * @param array $data
     * @return bool
     */
    abstract public function storage($data);
    
}