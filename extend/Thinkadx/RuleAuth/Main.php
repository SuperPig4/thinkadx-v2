<?php
namespace Thinkadx\RuleAuth;
use think\Db;
use think\facade\Cache;
use think\facade\Request;

/**
 * 注意
 *  1、如需要使用该插件,请严格按照admin相关表进行设计
 *      设计:
 *          1、tx_admin_group 该表的所有字段都要有
 *          2、tx_admin_rule  该表的所有字段都需要有
 *          3、tx_admin  该表只需要有group_id字段即可
 */
class Main {

    protected $ruleTable;
    protected $groupTable;
    protected $userTable;
    protected $userId;
    protected $ruleClassName;

    protected $isInit;
    protected $userInfo;
    protected $groupInfo;
    protected $roleInfo;


    public function __construct($rule, $group, $user, $userId, $ruleClassName = 'Standard') {
        $this->ruleTable = $rule;
        $this->groupTable = $group;
        $this->userTable = $user;
        $this->ruleClassName = $ruleClassName;
        $this->userId = $userId;
    }
    

    /**
     * 检测权限
     * @param string/bool $module
     * @param string/bool $controller
     * @param string/bool $action
     * @return bool
     */
    public function chcke($module = true, $controller = true, $action = true) {
        $this->getInfoInit();
        if(empty($this->roleInfo)) {
            return false;
        }

        $module === true && $module = Request::module();
        $controller === true && $controller = Request::controller(true);
        $action === true && $action = Request::action();

        // $reflection = new \ReflectionClass('\ThinkAdx\RuleAuth\Rule\Admin');
        // var_dump($reflection->getDocComment());

        $className = "\ThinkAdx\RuleAuth\Rule\\{$this->ruleClassName}";
        $model = new $className($this->userInfo, $this->groupInfo, $this->roleInfo);
        return $model->run($module, $controller, $action);
    }


    /**
     * 获得当前用户规则
     */
    private function getInfoInit() {
        if(!$this->isInit) {
            $this->isInit = true;
            $key = $this->ruleTable.'_rule_'.$this->userId;
            $data = Cache::get($key);
            if(empty($data)) {
                $data['userInfo'] = Db::name($this->userTable)->where('id', $this->userId)->findOrFail();
                $data['groupInfo'] = Db::name($this->groupTable)->where('id', $data['userInfo']['group_id'])->find();
                if(isset($data['groupInfo']['rules'])) {
                    $data['roleInfo'] = Db::name($this->ruleTable)->where('id', 'in', $data['groupInfo']['rules'])->column('rule');
                }
                
                if(empty($data['roleInfo'])) {
                    $data['roleInfo'] = [];
                } else {
                    $newRoleInfo = [];
                    foreach($data['roleInfo'] as $value) {
                        $tempA = explode(',', $value);
                        foreach($tempA as $valueA) {
                            $tempB = explode(':',$valueA);
                            $tempC = explode('/', $tempB[0]);
                            if(empty($newRoleInfo[$tempC[0]][$tempC[1]])) {
                                $newRoleInfo[$tempC[0]][$tempC[1]] = explode('-', $tempB[1]);
                            } else {
                                $newRoleInfo[$tempC[0]][$tempC[1]] = array_unique(array_merge($newRoleInfo[$tempC[0]][$tempC[1]], explode('-', $tempB[1])));
                            }
                            
                        }
                    }
                    $data['roleInfo'] = $newRoleInfo;
                }
                echo '进来了';
                //设置缓存
                Cache::tag($this->ruleClassName.'_rule_auth')->set($key, $data);
            }
            $this->userInfo = $data['userInfo'];
            $this->groupInfo = $data['groupInfo'];
            $this->roleInfo = $data['roleInfo'];
        }
    }
}