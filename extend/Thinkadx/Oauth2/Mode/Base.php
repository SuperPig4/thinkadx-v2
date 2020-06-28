<?php

namespace Thinkadx\Oauth2\Mode;

abstract class Base {
    
    // 参数
    protected $params;
    // 模式名
    protected $modeName;

    // 局部唯一标识符
    protected $id;
    // 全局唯一标识符
    protected $uniqueId;

    // main
    protected $main;

    public function __construct(\Thinkadx\Oauth2\Main $e) {
        // 默认自动获取
        $this->params = request()->param();

        $this->main = $e;
    }

    
    abstract public function login();

    // 创建 访问令牌
    abstract public function create_access_token($options = []);

    // 创建 令牌刷新
    abstract public function create_refresh_token($options = []);

    // 置参数
    final public function setParams($params) {
        $this->params = $params;
    }

    // 修改模式名
    final public function setModeName($name) {
        $this->modeName = $name;
    }


    // 模型查询
    final public function query($where) {
        return $this->main->baseQuery(array_merge([
            'oauth_type' => $this->modeName
        ], $where));
    }

    final public function setId($value) {
        $this->id = $value;
    }

    final public function setUniqueId($value) {
        $this->uniqueId = $value;
    }

    final public function getId() {
        return $this->id;
    }

    final public function getUniqueId() {
        return $this->uniqueId;
    }

    final public function getModeName() {
        return $this->modeName;
    }

}
