<?php

namespace app\admin\validate;

use think\Validate;

class Upload extends Validate {
    
    protected $rule = [
        // 上传文件
        'image' => 'requireCallback:check_image',
        // 文件夹路径
        'path' => 'require|in:menu_icon,admin_avatar'
    ];  
    
    protected $message = [
        'image' => '请上传图片',
        'path' => '参数错误'
    ];

    protected $scene = [
        'index' => ['image', 'path']
    ];

    protected function check_image($value, $data){
        if(empty($_FILES['image'])) {
            return true;
        } else {
            return false;
        }
    }
}
