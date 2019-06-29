<?php

namespace app\admin\controller;
use think\Controller;
use think\Response;
use think\Facade\Cache;
use think\Db;

class Base extends Controller {
    
    protected $middleware = ['CheckAdmin', 'AdminAfter'];
   
    // 可通过子类定义的属性
    
    // 当该属性被定义时,中间件将会执行对用的控制器,操作方法就是场景值
    // protected $validateName = String
    
    // 当该属性被定义时基类将不会接受外部参数,而是通过该属性获取
    // protected $baseParam = Array;

    // 当该属性被定义时,基类的操作方法需要用到的数据库操作,将是直接使用tp的DB而不是模型
    // protected $table = String

    /**
     * 范围:add_eidt方法
     * 当该属性被定义时,操作后会写入日志
     * 只有执行成功后才会被写入
     * 注意: 
     *  1.因编辑和新增是同一个方法,所以定义了两个特别的key
     *      add : 表示增加
     *      edit : 表示编辑
     * 格式:
     *  基类操作方法 => 日志内容(Array|String)
     */
    // protected $logs = Array | String;
    

    public function __construct() {
        parent::__construct();
      
        if($this->request->param('test')) {
            // var_dump(Cache::get('temp_token_admin_access_afdaf07662eada68dc66a48dccf702a7'));
            $key = 'd49c668c4b8696cfd9e05be062d463ea';
            echo Cache::rm('admin_access_'.$key);
            exit();
        }
        
        //验证方法是否存在
        $actionIsHave = method_exists($this, $this->request->action());
        if(empty($actionIsHave)) {
            error('illegal action');
        }
       
        if(!empty($this->validateName)) {
            $name = $this->validateName === true ? $this->request->controller() : $this->validateName;
            define('Validate_Name', $name);
        }

    }


    //公共列表
    public function index() {
        $params = $this->get_params();

        if(empty($this->table)) {
            $db = $this->get_model();
        }
        
        if(empty($db)) {
            $table = empty($this->table) ? $this->request->controller(true) : $this->table;
            $db = Db::name($table);
        }

        // 检测是否有条件回调
        if(method_exists($this, 'index_where_callback')) {
            $db = $this->index_where_callback($db, $params);
        }
        

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

    
    // 公共详情
    public function detail() {
        $params = $this->get_params();

        if(empty($this->table)) {
            $model = $this->get_model();
            if(!empty($model)) {
                // 模型写入、编辑
                $info = $model->get($params['id']);
            }
        }
        
        if(empty($model)) {
            $table = empty($this->table) ? $this->request->controller(true) : $this->table;
            $info = Db::name($table)->where('id', $params['id'])->find();
        }

        success('ok!', $info);
    }


    // 公共编辑写
    public function add_edit() {
        $params = $this->get_params();
      
        if(empty($this->table)) {
            $model = $this->get_model();
            if(!empty($model)) {
                // 模型写入、编辑
                $model->allowField(true);
                if(!empty($params['id'])) {
                    $actStatus = $model->save($params,['id' => $params['id']]);
                } else {
                    $actStatus = $model->save($params);
                }
            }
        }
        
        if(empty($model)) {
            $table = empty($this->table) ? $this->request->controller(true) : $this->table;
            $db = Db::name($table)->strict(false);
            if(!empty($params['id'])) {
                $actStatus = $db->where('id', $params['id'])->update($params);
            } else {
                $actStatus = $db->insert($params);
            }   
        }

        if($actStatus !== false) {
            if(isset($this->logs)) {
                if(!empty($params['id'])) {
                    $log = $this->logs['edit'];
                } else {
                    $log = $this->logs['add'];
                }
                if(!empty($log)) {
                    $this->request->act_log = $log;
                }
            }
            success('操作成功!');
        } else {
            error('操作失败');
        }
    }


    // 公共删除
    public function delete() {
        $params = $this->get_params();
        if(empty($this->table)) {
            $db = $this->get_model();
        }
        
        if(empty($db)) {
            $table = empty($this->table) ? $this->request->controller(true) : $this->table;
            $db = Db::name($table);
        }
        $db->where('id', 'in', $params['id'])->delete();
        
        if(isset($this->logs)) {
            if(isset($params['id'])) {
                $log = $this->logs['edit'];
            } else {
                $log = $this->logs['add'];
            }
            if(!empty($log)) {
                $this->request->act_log = $log;
            }
        }
        success('操作成功');
    }


    /**
     * 获得model
     * @return bool | class
     */
    private function get_model() {
        if(isset($this->modelName)) {
            $modelPath = strtolower($this->modelName.'.php');
        } else {
            $modelPath = strtolower($this->request->controller(true).'.php');
        }
        $dirList = scandir('../application/admin/model');
        foreach($dirList as $val) {
            if(strtolower($val) == $modelPath) {
                $modelNameAr = explode('.', $val);
                $modelName = '\\app\\admin\\model\\'.$modelNameAr[0];
                return new $modelName();
                break;
            }
        }

        return false;
    }

    // 获得参数
    private function get_params() {
        // 判断是否自己定义了参数
        if(isset($this->baseParam)) {
            return $this->baseParam;
        } else {
            return $this->request->param();
        }
    }
    
}
