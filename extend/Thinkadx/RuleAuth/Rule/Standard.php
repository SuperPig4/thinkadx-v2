<?php
namespace Thinkadx\RuleAuth\Rule;
/**
 * 使用教程
 * https://gitee.com/first_pig/thinkadx-v2/wikis/Standard%E8%A1%A8%E8%BE%BE%E5%BC%8F?sort_id=1540410
*/
class Standard extends Constraint {
    
    public function run($module, $controller, $action) {
        // 模块
        if(isset($this->roleInfo['*'])) {
            // 控制器
            if(isset($this->roleInfo['*']['*']) || isset($this->roleInfo['*'][$controller])) {
                // 方法
                if(
                    isset($this->roleInfo['*']['*']) && in_array('*', $this->roleInfo['*']['*']) || 
                    isset($this->roleInfo['*']['*']) && in_array($action, $this->roleInfo['*']['*']) || 
                    isset($this->roleInfo['*'][$controller]) && in_array('*', $this->roleInfo['*'][$controller]) || 
                    isset($this->roleInfo['*'][$controller]) && in_array($action, $this->roleInfo['*'][$controller])
                ) {
                    return true;
                }
            }
        } else if(isset($this->roleInfo[$module])) {
            // 控制器
            if(isset($this->roleInfo[$module][$controller]) || isset($this->roleInfo[$module]['*'])) {
                // 方法
                if(
                    isset($this->roleInfo[$module][$controller]) && in_array('*', $this->roleInfo[$module][$controller]) || 
                    isset($this->roleInfo[$module][$controller]) && in_array($action, $this->roleInfo[$module][$controller]) ||
                    isset($this->roleInfo[$module]['*']) && in_array('*', $this->roleInfo[$module]['*']) || 
                    isset($this->roleInfo[$module]['*']) && in_array($action, $this->roleInfo[$module]['*'])
                ) {
                    return true;
                }
            }
        }
        return false;
    }

}