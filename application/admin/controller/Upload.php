<?php
/* ============================================================================= #
# Autor: 奔跑猪
# Date: 2020-07-16 05:15:52
# LastEditors: 奔跑猪
# LastEditTime: 2020-07-16 05:36:41
# Description: 上传控制器
# ============================================================================= */
namespace app\admin\controller;

use app\admin\validate\Upload as UploadValidate;

class Upload extends Base {

    protected $validateName = UploadValidate::class;

    public function index() {
        $file = request()->file('image');
        $saveFileName = $this->request->param('path');
        $info = $file->rule('md5')->validate(['size'=>1048576,'ext'=>'jpg,png'])->move('./uploads/'.$saveFileName);
        if($info){
            $locaPath = str_replace("\\","/",'uploads/'.$saveFileName.'/'.$info->getSaveName());
            chmod($locaPath, 0644);
            $url = $this->request->scheme().'://'.$this->request->host().'/'.$locaPath;
            success('ok!',[
                'locaPath' => $locaPath,
                'url' => $url
            ]);

        }else{
            // 上传失败获取错误信息
            error($file->getError());
        }    
    }

}
