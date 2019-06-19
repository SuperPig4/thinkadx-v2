<?php

namespace app\admin\controller;

class Upload extends Base {

    protected $validateName = 'Upload';

    public function index() {
        $file = request()->file('image');
        $saveFileName = $this->request->param('path');
        $info = $file->rule('md5')->validate(['size'=>1048576,'ext'=>'jpg,png'])->move('./uploads/'.$saveFileName);
        if($info){
            $locaPath = str_replace("\\","/",'uploads/'.$saveFileName.'/'.$info->getSaveName());
            $url = $this->request->scheme().'://'.$this->request->host().'/'.$locaPath;
            success('ok!',[
                'locaPath' => $locaPath,
                'url' => $url
            ]);

        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }    
    }

}
