<?php
/**
 *密码模式
 * @desc
 *  1.可用于自定义或常规的密码模式
 *
 *  默认为密码模式
 */
namespace Thinkadx\Oauth2\Mode;

use think\facade\Validate;

class Dynamic extends Base{

    public $modeName = 'dynamic';
    public $idAlias  = 'password';
    

    public function logout() {
    }

    public function create() {}

    // public function tokenCheck($idAlias, $uniqueIdAlias = null) {}


    /**
     * 登录检测
     * 
     *  条件
     *   - 模型实例
     *   - 模型
     * 
     * @param string $id 标识符 (密码模式=md5(真实密码+各种盐))
     * @param string $uniqueId 全局唯一标识符(密码模式=盐)
     * @param bool   $isEncrypt 是否加密
     * 
     * @return bool/array
     */
    public function login($id, $uniqueId = null, $isEncrypt = true) {

        if($isEncrypt) {
            $md5Str = $id;
            if(is_null($uniqueId)) {
                $md5Str .= $this->main->userOauthModel->unique_identifier;
            } else {
                $md5Str .= $uniqueId;
            }
            $identifier = md5($md5Str);
            $uniqueIdentifier = $this->main->userOauthModel->unique_identifier;
        } else {
            $identifier = $id;
            $uniqueIdentifier = $uniqueId;
        }

        if($this->main->userOauthModel->identifier == $identifier && $this->main->userOauthModel->unique_identifier == $uniqueIdentifier) {
            // 刷新全部令牌
            $tokenAr = [
                'access' => $this->main->createToken($this->main->userOauthModel->id . 'access'),
                'refresh' => $this->main->createToken($this->main->userOauthModel->id . 'refresh'),
            ];
            
            
            return true;
        } else {
            return false;
        }
        
    }
    

}

