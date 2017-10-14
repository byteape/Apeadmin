<?php

/**
 * 上传临时文件转移类
 * +------------------------------------说明------------------------------------+
 * 1、默认临时文件根目录为'Temp/',也可以在初始化类时指定。
 * 2、可以对多个图片路径和单个图片路径进行操作，可以指定多图路径时的分隔符，默认为','。
 * 3、返回处理的结果数组，包含来源和新地址以处理结果。
 * +------------------------------------说明------------------------------------+
 * +------------------------------------使用------------------------------------+
 * $files="/Public/Upload/Temp/banner/20161215/1.jpg,/Public/Upload/Temp/banner/20161215/2.png,";
 * $fileTemp= new FileTemp();
 * $result=$fileTemp->copy($files);
 * +------------------------------------使用------------------------------------+
 * Class FileTemp
 */
class  FileTemp {
    public $postData; //pst接收的数组
    public $temp_root = 'Temp/'; //临时文件的根目录
    public $fileArr; //待处理的路径数组
    public $explodeStr = ','; //多个路径的分割字符
    public $lastisdh = false; //末尾是否有逗号(上面定义的符号)
    public $normalFileArr; //待复制的正式文件路径新数组
    public $delOld = true; //当成功复制时是否删除旧文件
    public $frontStr = '.'; //当打开文件路径前面加的字符
    public $extArr = array(
        '.jpg', '.jpeg', '.gif', '.png', '.bmp'
    );//允许操作的扩展文件名

    /**
     * 构造函数
     * @param string $temp_root
     * @param string $explodeStr
     * @param string $delOld
     */
    public function __construct($temp_root = '', $explodeStr = '', $delOld = '') {
        $this->temp_root = $temp_root ? $temp_root : $this->temp_root;
        $this->explodeStr = $explodeStr ? $explodeStr : $this->explodeStr;
        $this->delOld = $delOld ? $delOld : $this->delOld;
    }

    /**
     * 生成待处理路径数组
     * @param $files
     */
    public function setFiles($files) {
        //生成新的数组
        $fileArr = $this->explodeArr($this->explodeStr, $files, true);
        foreach ($fileArr as $k => $v) {
            $fileArr[$k] = $this->frontStr . $v;
        }
        $this->fileArr = $fileArr;
    }

    /**
     * 将字符串以分割符分割成数组
     * @param $str
     * @param $dataStr
     * @param bool $isstart
     * @return array
     */
    public function explodeArr($str, $dataStr, $isstart = false) {
        $arr = explode($str, $dataStr);
        foreach ($arr as $k => $v) {
            if (!trim($v)) {
                if ($isstart) $this->lastisdh = true;
                unset($arr[$k]);
            } else {
                if ($isstart) $this->lastisdh = false;
            }
        }
        return $arr;
    }

    /**
     * 生成待复制的正式文件路径新数组
     */
    public function getNormalFileArr() {
        $fileArr = $this->fileArr;
        foreach ($fileArr as $k => $v) {
            $point = strpos($v, $this->temp_root);
            $behind = str_replace($this->temp_root, '', substr($v, $point));
            $front = substr($v, 0, $point);
            $normalFileArr[$k] = $front . $behind;
        }
        $this->normalFileArr = $normalFileArr;
    }

    /**
     * 开始复制文件
     * @param $files
     * @return mixed
     */
    public function copy($files) {
        $this->setFiles($files);
        $this->getNormalFileArr();

        $fileArr = $this->fileArr;
        $normalFileArr = $this->normalFileArr;
        foreach ($normalFileArr as $k => $v) {
            $arr = $this->explodeArr('/', $v);
            $extName = '.' . substr(strrchr($arr[count($arr) - 1], '.'), 1);
            if (in_array($extName, $this->extArr)) {
                unset($arr[count($arr) - 1]);
                $dirname = "";
                foreach ($arr as $m => $n) {
                    $dirname .= $n . '/';
                    if (!is_dir($dirname)) {
                        mkdir($dirname, 0777, true);
                    }
                }
                $result = @copy($fileArr[$k], $normalFileArr[$k]);
                if ($result && $this->delOld) {
                    unlink($fileArr[$k]);
                }
            } else {
                $result = 0;
            }
            if ($result) {
                $redata[$k] = $normalFileArr[$k];
            } else {
                if (file_exists(str_replace($this->temp_root, '', $fileArr[$k]))) {
                    $redata[$k] = str_replace($this->temp_root, '', $fileArr[$k]);
                } else {
                    $redata[$k] = $fileArr[$k];
                }
            }
            //$redata[$k]=$result?$normalFileArr[$k]:$fileArr[$k];
            $redata[$k] = substr($redata[$k], strlen($this->frontStr));
        }
        return $this->lastisdh ? implode($this->explodeStr, $redata) . $this->explodeStr : implode($this->explodeStr, $redata);
    }

    /**
     * 接收post数据进行处理
     * @param $postData
     * @return mixed
     */
    public function run($postData) {
        $extArr = $this->extArr;
        foreach ($postData as $k => $v) {
            //不包含html字符才执行
            if ($v == strip_tags($v)) {
                foreach ($extArr as $m => $n) {
                    if (stristr($v, $n) != false) {
                        $postData[$k] = $this->copy($v);
                    }
                }
            }
        }
        $this->postData = $postData;
        return $postData;
    }
}