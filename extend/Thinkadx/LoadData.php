<?php
namespace Thinkadx;

/**
 * 加载数据
 * 
 * 将缓存数据通过关联模型的方式获取或CRUD操作
 * 
 * @param array $data
 * @param string $model
 */

class LoadData {

    // 数据
    private $data;
    // 模型
    private $model;
    // 主键
    private $pk;
    // 存储类型
    private $storageClass;

    public function __construct($data = [], $model = '', $storageClass = '', $pk = 'id') {
        if($data)   $this->set_data($data);
        if($model)  $this->set_model($model);
        if($pk)     $this->set_pk($pk);
        if($storageClass)  $this->set_storage_class($storageClass);
    }

    public function __get($name) {
        // 判断是否为操作模型
        if($name == 'model' && !empty($this->model)) {
            return $this->get_model();
        } else {
            if(property_exists($this->data, $name)) {
                return $this->data->$name;
            } 
            return null;
        }
    }

    public function __set($name, $value) {
        $this->data->$name = $value;
    }

    public function __call($name, $args){
        $this->value = call_user_func_array([$this, $name], $args);
        return $this;
    }


    /**
     * 初始化模型
     */
    protected function get_model() {
        if(gettype($this->model) == 'string') {
            $pk = $this->pk;

            // 检测是否有主键
            if(!property_exists($this->data, $pk)) {
                throw new \Exception('No primary key data found');
            }

            $this->model = $this->model::get($this->data->$pk);
        }

        return $this->model;
    }


    /**
     * 同步缓存数据
     * 
     * @param bool $isUpdateCache 是否更新缓存
     */
    protected function sync($isUpdateCache = false) {
        // 初始化
        $this->get_model();
        // 同步
        foreach(json_decode(json_encode($this->model),true) as $key=>$val) {
            if(property_exists($this->data, $key) && $this->data->$key != $val) {
                $this->data->$key = $val;
            }
        }

        // 执行相应的缓存逻辑
        if($isUpdateCache) {
            return $this->storageClass->storage($this->toArray());
        }
        return true;
    }

    /**
     * 导出为数组
     */
    public function toArray() {
        return json_decode(json_encode($this->data), true);
    }

    /**
     * 置存储类
     * 
     */
    protected function set_storage_class($storageClass) {
        $this->storageClass = $storageClass;
    }

    /**
     * 置主键
     * 
     */
    protected function set_pk($pk) {
        $this->pk = $pk;
    }

    /**
     * 置数据
     * 
     */
    protected function set_data($data) {
        $this->data = json_decode(json_encode($data));
    }

    /**
     * 置模型
     * 
     */
    protected function set_model($model) {
        $this->model = '\\'.$model;
    }


    

}