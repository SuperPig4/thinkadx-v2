<?php
namespace Thinkadx\RuleAuth\Rule;

abstract class Constraint {

    protected $userInfo;
    protected $groupInfo;
    protected $roleInfo;

    
    // abstract public $des;

    public function __construct($userInfo, $groupInfo, $roleInfo) {
        $this->userInfo = $userInfo;
        $this->groupInfo = $groupInfo;
        $this->roleInfo = $roleInfo;
    }

    // public function initData($userInfo, $groupInfo, $roleInfo) {
    //     $this->userInfo = $userInfo;
    //     $this->groupInfo = $groupInfo;
    //     $this->roleInfo = $roleInfo;
    // }


    /**
     * @return bool 
     */
    abstract public function run($module, $controller, $action);

}