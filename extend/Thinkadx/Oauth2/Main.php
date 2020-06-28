<?php
/**
 * 表设计请遵循thinkadx tx_admin_oauth表的结构
 * 注意
 *  目前该版本只支持redis
 * 
 */
namespace Thinkadx\Oauth2;

use think\facade\Cache;
use think\facade\Config;

class Main {

    // 模型实例
    public $userOauthModel;
    // 模型
    public $model;
    // 模式
    public $mode;
    // 终端名
    protected $portType;
    // 缓存的数据
    protected $cacheData;
    // 令牌配置
    protected $tokenConfig = [
        'access' => [
            // 期限
            'expire_time' => 8000,
        ],
        'refresh' => [
            // 期限
            'expire_time' => 8000,
        ],
    ];

    // 授权表的用户主键
    protected $tableUserPk;


    // redis句柄
    // private $redis;


    // 模式列表
    const ModeList = [
        'DYNAMIC'  => Mode\Dynamic::class,
        'PASSWORD' => Mode\Password::class
    ];

    /**
     * 模型
     * 
     * 数据库模型
     */
    public function __construct($model = null, $mode = null) {
        if($model)  $this->setModel($model);
        if($mode)  $this->setMode($mode);
    }

    public function __call($name, $args){
        if(method_exists($this->mode, $name)) {
            $value = call_user_func_array([$this->mode, $name], $args);
        } else {
            $value = call_user_func_array([$this, $name], $args);
        }

        if(isset($value)) {
            return $value;
        } else {
            return $this;
        }
    }

    // 选择模式
    protected function setMode($mode) {

        if(in_array($mode, self::ModeList) === false) {
            if(isset(self::ModeList[strtoupper($mode)]) === false) {
                throw new \think\Exception('授权模式异常');
            } else {
                $mode = self::ModeList[strtoupper($mode)];
            }
        }
        
        $this->mode = new $mode($this);
    }

    // 设置模型
    protected function setModel($model) {
        $this->model = $model;
    }

    // 设置模型实例
    protected function setOauthModel($oauthModel) {
        $this->userOauthModel = $oauthModel;
    }

    // 设置终端类型
    protected function setPortType($name) {
        $this->portType = $name;
    }

    // 设置缓存内容
    protected function setCacheData($data) {
        $this->cacheData = $data;
    }

    // 设置授权表用户主键
    protected function setTableUserPk($value) {
        $this->tableUserPk = $value;
    }

    // 设置访问令牌过期时间
    protected function setAccessTokenExpireTime($time) {
        $this->tokenConfig['access']['expire_time'] = $time;
    }

    // 设置刷新令牌过期时间
    protected function setRefreshTokenExpireTime($time) {
        $this->tokenConfig['refresh']['expire_time'] = $time;
    }
    
    /**
     * 刷新令牌
     * 
     * 条件
     *  - 授权表和用户表的关联字段 setTableUserPk
     * 
     * @param string $token 刷新令牌
     * 
     * @return bool/string
     */
    protected function refresh($token) {
        // 判断相关令牌是否存在
        $cacheDataPrefix       = $this->getCacheDataPrefix();
        $cachePrefix           = Config::get('cache.redis.prefix');
        $refreshToken          = $cacheDataPrefix . '_refresh_token_' . $token;
        $lockKey               = $cachePrefix . 'lock_' . $refreshToken ;
        $redisHandler          = Cache::store('redis')->handler();
        
        if(Cache::store('redis')->has($refreshToken)) {
            // 缓存内容
            $refreshContent  = unserialize(Cache::store('redis')->get($refreshToken));
            $whiteListKey = $cacheDataPrefix . '_access_token_whitelist_' . $refreshContent['access_token'];
            if(time() < $refreshContent['access_expire_time']) {
                return $refreshContent['access_token'];
            }

            // 上锁
            if($redisHandler->set($lockKey, time(), ['NX', 'EX' => 30])) {
                // 将过期令牌加入白名单 
                Cache::store('redis')->set($whiteListKey, $refreshContent['access_content'], 300);
                
                $oauth = $this->baseQuery([
                    $this->tableUserPk => $refreshContent['access_content']['id'],
                    'oauth_type' => strtolower($this->mode->getModeName()),
                    'port_type' => strtolower($this->portType),
                ]);

                if($oauth->isEmpty()) {
                    return false;
                }

                // 刷新令牌
                $this->setOauthModel($oauth);
                $this->setCacheData($refreshContent['access_content']);
                $newToken = $this->create_access_token();
                $refreshContent['access_token'] = $newToken['token'];
                $refreshContent['access_expire_time'] = time() + $newToken['expire_time'];
                $redisHandler->setRange($cachePrefix . $refreshToken, 0, serialize($refreshContent));

                // 释放
                $redisHandler->del($lockKey);
                return $newToken['token'];
            } else {
                // 并发一般都会在这里 hold住 请求
                sleep(3);
                $refreshContent  = unserialize(Cache::store('redis')->get($refreshToken));
                if($this->check($refreshContent['access_token']) !== false || Cache::store('redis')->has($whiteListKey)) {
                    return $refreshContent['access_token'];
                } 
            }
        }

        return false;
    }

    /**
     * 模型查询 - 内部使用
     * @param array $where 条件
     */
    protected function baseQuery($where) {
        $e = '\\'.$this->model;
        return $e::where([
            'port_type' => $this->portType
        ])->where($where)->find();
    }

    /**
     * 创建授权数据
     * @param array $mergeData 需要额外写入的数据
     * @return object
     */
    protected function create($mergeData) {
        $data = array_merge([
            'oauth_type' => $this->mode->getModeName(),
            'port_type' => $this->portType,
            'identifier' => $this->mode->getId(),
            'unique_identifier' => $this->mode->getUniqueId(),
        ], $mergeData);

        $e = '\\'.$this->model;
        return $e::create($data);
    }

    /**
     * 检测令牌
     * 
     * 条件
     *  - 相对标识符 setId
     *  - 绝对标识符 setUniqueId
     * 
     * @param string $accessToken 访问令牌
     * @return bool
     */
    protected function check($accessToken) {
        $cacheDataPrefix    = $this->getCacheDataPrefix();
        $cacheKey           = $cacheDataPrefix . '_access_token_' . $accessToken;
        $cacheData          = Cache::store('redis')->get($cacheKey);
        if(empty($cacheData)) {
            // 白名单
            $whiteListKey = $cacheDataPrefix . '_access_token_whitelist_' . $accessToken;
            if(Cache::store('redis')->has($whiteListKey)) {
                return Cache::store('redis')->get($whiteListKey);
            }

            return false;
        } else {
            return $cacheData;
        }
    }

    /**
     * 让相同令牌失效
     * 
     * 条件
     *  - 模型实例
     * 
     * @param bool isAll 删除所有登录方式的令牌
     */
    protected function logout($isAll = false) {
        $cacheDataPrefix       = $this->getCacheDataPrefix();
        $cachePrefix           = Config::get('cache.redis.prefix');
        $userSortedSetKey      = $cachePrefix . $cacheDataPrefix . '_user_token_' . $this->userOauthModel->id;
        $allToken              = Cache::store('redis')->handler()->zRange($userSortedSetKey, 0, -1);
        
        if($isAll === false) {
            foreach($allToken as $val) {
                Cache::store('redis')->rm($val);
                Cache::store('redis')->handler()->zrem($userSortedSetKey, $val);
            }
        } else {
            // 暂不支持
        }
    }

    /**
     * 创建令牌 - 内部使用
     * @param string $baseToken 基础令牌
     * @return string
     */
    protected function createToken($baseToken) {
        return md5(get_class($this->mode).$this->portType.$baseToken.mt_rand(11111,99999));
    }

    /**
     * 生成令牌 - 内部调用
     * 
     * @param string $tokenType 令牌类型 
     *  desc
     *      access:访问令牌
     *      refresh:刷新令牌
     * @param string $newToken 新令牌
     * @param array  $option   额外参数
     *  desc
     *      access_token : 当重写令牌为刷新令牌时需要传入
     * 
     * @return array
     */
    protected function resetToken($tokenType, $newToken, $option = []){
        $tokenName = $tokenType.'_token';

        // 入库数据
        $updateData = [
            $tokenName => $newToken,
            $tokenName.'_create_time' => time()    
        ];
        $this->userOauthModel->save($updateData);
        
        // 写入缓存
        $cacheDataPrefix       = $this->getCacheDataPrefix();
        $cachePrefix           = Config::get('cache.redis.prefix');
        $userSortedSetKey      = $cachePrefix . $cacheDataPrefix . '_user_token_' . $this->userOauthModel->id;
        $userSetKey            = $cachePrefix . 'user_sorted_set_key_tokens';
        $tokenKey              = $cacheDataPrefix . '_' . $tokenName . '_' . $newToken;
        $expireTime            = $this->tokenConfig[$tokenType]['expire_time'];

        // 根据令牌类型决定缓存内容
        $content = '';
        if($tokenType == 'access') {
            $content = $this->cacheData;
        } else if($tokenType == 'refresh') {
            $accessExpireTime = time() + $this->tokenConfig['access']['expire_time'];
            $content = serialize([
                'access_content'     => empty($option['access_content']) ? $this->cacheData : $option['access_content'],
                'access_token'       => $option['access_token'],
                'access_expire_time' => $accessExpireTime,
            ]);
            // $content = isset($option['access_token']) ? $option['access_token'] . '_' . $accessExpireTime : '';
        }

        Cache::store('redis')->set($tokenKey, $content, $expireTime);
        Cache::store('redis')->handler()->zAdd($userSortedSetKey, time() + $expireTime, $tokenKey);
        Cache::store('redis')->handler()->sAdd($userSetKey, $userSortedSetKey);

        return [
            'expire_time' => $expireTime,
            'token'       => $newToken
        ];
    }

    /**
     * 获得缓存前缀
     */
    private function getCacheDataPrefix() {
        $modelName = explode('\\',$this->model);
        return strtolower(end($modelName)) . '_' . strtolower($this->mode->getModeName()) . '_' . strtolower($this->portType);
    }
    
    /**
     * - 任务计划 - 杀死无效token
     * desc
     *  一个时间周期执行一次
     */
    static public function taskKillNullToken() {

        $redisHandler          = Cache::store('redis')->handler();
        $cachePrefix           = Config::get('cache.redis.prefix');
        $userSetKey            = $cachePrefix . 'user_sorted_set_key_tokens';
        $tokenTags             = $redisHandler->sMembers($userSetKey);
        $time                  = time();

        foreach($tokenTags as $val) {
            // 查找sorted set
            if($redisHandler->exists($val)) {
                $tokens = $redisHandler->zRange($val, 0, -1, true);
                if(count($tokens) > 0) {
                    $killMin = 0;
                    $killMax = 0;
                    foreach($tokens as $tokenVal => $tokenTime) {
                        if($time >= $tokenTime) {
                            if(empty($killMin)) {
                                $killMin = $tokenTime;
                            } else {
                                $killMax = $tokenTime;
                            }
                        }
                    }
                    if($killMin > 0 && $killMax > 0) {
                        $redisHandler->zRemRangeByScore($val, $killMin, $killMax);
                    }
                }
            } else {
                $redisHandler->sRem($userSetKey, $val);
            }
        }
    }

    /**
     * 初始化
     * @param string $model 模型
     * @param string $name           列表实例
     */
    static public function init($model, $mode) {
        return new self($model, $mode);
    }

    



}
