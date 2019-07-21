<?php
namespace Thinkadx\RuleAuth\Rule;
/** 
* 提示:这里为了注释 //斜杠表示为/个斜杠
* 提示:这里为了注释 //斜杠表示为/个斜杠
* 提示:这里为了注释 //斜杠表示为/个斜杠
* 提示:这里为了注释 //斜杠表示为/个斜杠
* 提示:这里为了注释 //斜杠表示为/个斜杠
* 提示:这里为了注释 //斜杠表示为/个斜杠
*
*
*
*   表达式解析:
*       *号表示所有
*       *//*:*  *(模块位)//*(控制器位):*(操作位)
*
*       操作位用-分隔如: 
*          admin//user:index-edit 
*          admin模块->user控制器->index、edit可以进行操作
*       ,是某个规则的分隔
*/
class Standard extends Constraint {
    
    public function run($module, $controller, $action) {
        //判断是否超级权限
        if(isset($this->roleInfo['*']) && isset($this->roleInfo['*']['*']) && in_array('*', $this->roleInfo['*']['*'])) {
            return true;
        } else {
            // 模块
            if(isset($this->roleInfo['*']) || isset($this->roleInfo[$module])) {
                // 控制器
                if(isset($this->roleInfo['*']['*']) || isset($this->roleInfo[$module][$controller])) {
                    //操作
                    if(isset($this->roleInfo['*']['*']) && in_array($action, $this->roleInfo['*']['*'])) {
                        return true;
                    } else if(isset($this->roleInfo[$module][$controller]) && in_array($action, $this->roleInfo[$module][$controller])) {
                        return true;
                    } else if(isset($this->roleInfo[$module][$controller]) && in_array('*', $this->roleInfo[$module][$controller])) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}