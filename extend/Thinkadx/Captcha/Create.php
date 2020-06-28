<?php
namespace Thinkadx\Captcha;

class Create {

    // 随机数
    protected $string = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
    // 宽
    protected $width;
    // 高
    protected $height;
    // 验证码字符数
    protected $num;
    
    // 画布资源
    protected $img;
    // 验证码
    protected $code;
    // 字体文件
    protected $ttf;
    

    public function __construct($width = 160, $height = 60, $num = 4) {
        $this->width  = $width;
        $this->height = $height;
        $this->num    = $num;
        
        $this->load_ttf();
        $this->create_canvas();
    }

    
    // 加载所有字体文件
    protected function load_ttf() {
        $ttfAr = scandir(__DIR__ . DIRECTORY_SEPARATOR . 'ttf');
        array_splice($ttfAr, 0, 2);
        $this->ttf = $ttfAr;
    }


    // 创建画布
    protected function create_canvas() {
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$color  = imagecolorallocate($this->img, 200, 200, 200);
		imagefill($this->img, 0, 0, $color);
    }


    // 创建验证码
    protected function create_code() {
        //生成随机字符
        if(empty($this->code)) {
            $str = '';
            for($i = 0; $i < $this->num; $i++) {
                $code = substr($this->string, mt_rand(0 ,strlen($this->string)-1) ,1);
                $this->add_code_str($code, $i);
                $str .= $code;
            }
            $this->code = $str;
        } else {
            for($i = 0; $i < strlen($this->code); $i++) {
                $this->add_code_str($this->code[$i], $i);
            }
        }
    }


    /**
     * 往图片增加字符
     * @param string $str 字符 - 只支持数字、字母
     * @param int $key 序号
     */
    protected function add_code_str($str, $key) {
        // 字体颜色
        $codeColor = imagecolorallocate($this->img, mt_rand(0 ,200), mt_rand(0 ,200), mt_rand(0 ,200));
        // 字体大小
        $fontSize = mt_rand(20 ,25);
        // 字体旋转角度
        $fontAngle = mt_rand(0, 20);
        // 字体位置
        $x = mt_rand(10, 15) + $key * (($this->width) / $this->num);
        $y = mt_rand($fontSize ,$this->height);
        // 字体文件
        $fontFile = __DIR__ . DIRECTORY_SEPARATOR . 'ttf' . DIRECTORY_SEPARATOR . $this->ttf[array_rand($this->ttf, 1)];
        imagettftext($this->img, $fontSize, $fontAngle, $x, $y, $codeColor, $fontFile, $str);
    }


    //渲染干扰元素
	protected function create_disturb() {
        //随机点100个
        for ($i=0; $i<100; $i++) {
            $pixelColor = imagecolorallocate($this->img, mt_rand(50,100), mt_rand(50,100), mt_rand(50,100));
            imagesetpixel($this->img, mt_rand(0 ,$this->width-1), mt_rand(0 ,$this->height-1), $pixelColor);
        }
		
        //生成雪花20个
        for ($i=0; $i<20; $i++) {
            $snowColor = imagecolorallocate($this->img, 255, 255, 255);
            imagestring($this->img, 3, mt_rand(0 ,$this->width-3), mt_rand(0 ,$this->height-3), '*', $snowColor);
        }

        //干扰线10条
        for ($i=0; $i<10; $i++) {
            $lineColor = imagecolorallocate($this->img, mt_rand(50,225), mt_rand(50,225), mt_rand(50,225));		
            imageline($this->img, mt_rand(0 ,$this->width-1), mt_rand(0 ,$this->height-1), mt_rand(0,$this->width-1), mt_rand(0 ,$this->height-1), $lineColor);
        }
    }
    

    // 创建
    public function create($code = '') {
        if(empty($this->code)) {
            if(!empty($code)) {
                $this->code = $code;
            }

            // 渲染干扰元素
            $this->create_disturb();
            // 渲染数字
            $this->create_code();
        }
    }

    // 获得验证码
    public function get_code() {
        return $this->code;
    }


    // 获得图片
    public function show() {
		header('Content-type:image/png');
		imagepng($this->img);
    }
    
    
    public function __destruct () {
		imagedestroy($this->img);
	}

}