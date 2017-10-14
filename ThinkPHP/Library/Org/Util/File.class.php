<?php

/**
 * 文件处理操作类
 * +------------------------------------说明------------------------------------+
 * 可以用来获取文件的后缀、大小、创建、修改时间等
 * +------------------------------------说明------------------------------------+
 * +------------------------------------使用------------------------------------+
 * $filename="/Public/Upload/Temp/banner/20161215/1.jpg";//数据库里储的文件路径
 * $filename="http://www.baidu.com/logo.jpg";//远程文件路径
 * $file= new File();
 * $result=$file->getExtName($filename);//获取文件后缀
 * $result=$file->getSize($filename);//获取文件大小
 * $result=$file->getRealFile($filename);//获取文件的正确路径
 * +------------------------------------使用------------------------------------+
 * Class FileTemp
 */
class  File {
    public $fileName = ''; //输入的文件路径
    public $realFiel = ''; //当前正确的路径
    public $prevSpot = ''; //正确路径中的前面的点
    /**
     * 构造函数
     * @param string $fileName 文件路径
     */
    public function __construct($fileName = '') {
        $this->fileName = $fileName;
    }

    /**
     * 获取正确的路径
     * @param $file
     * @return string
     */
    public function getRealFile($file = '') {
        $file = $file ? $file : $this->fileName;
        $error = '';
        if (substr($file, 0, 4) == 'http') {
            $realfile = $file;
        } elseif (!$file) {
            $error = '请指定文件路径';
        } elseif (file_exists($file)) {
            $realfile = $file;
            $this->prevSpot = '';
        } elseif (file_exists('.' . $file)) {
            $realfile = '.' . $file;
            $this->prevSpot = '.';
        } elseif (file_exists('..' . $file)) {
            $realfile = '..' . $file;
            $this->prevSpot = '..';
        } else {
            $error = 0; //您输入的文件不存在
        }
        if ($error != '') {
            try {
                throw new Exception($error);
            } catch (Exception $e) {
                return $e->getMessage();
            }
        } else {
            $this->realFiel = $realfile;
            return $realfile;
        }
    }

    /**
     * 获取文件扩展名
     * @param string $file
     * @return string
     */
    public function getExtName($file = '') {
        $file = $this->getRealFile($file);
        return substr($file, strrpos($file, '.') + 1);
    }

    /**
     * 获取文件的大小
     * @param bool $is_formater 是否返回格式化的串
     * @param string $file
     * @return int|string
     */
    public function getSize($is_formater = true, $file = '') {
        $file = $this->getRealFile($file);
        if (substr($file, 0, 4) == 'http') {
            $header_array = get_headers($file, true);
            $size = $header_array['Content-Length'];
            //如果外部链接
        } else {
            $size = filesize($file);
        }
        if ($size < 1024 * 1024) {
            $rsize = ceil($size / 1024) . 'kb';
        } else {
            $rsize = ceil($size / (1024 * 1024)) . 'M';
        }
        return $is_formater ? $rsize : $size;
    }

    /**
     * 获取文件MiME类型
     * @param string $file
     * @return string
     */
    public function getMimeType($file = '') {
        $file = $this->getRealFile($file);
        $fi = new finfo(FILEINFO_MIME_TYPE);
        return $fi->file($file);
    }

    /**
     * 返回文件最后被修改的时间
     * @param string $file
     * @return int
     */
    public function getFileTime($file = '') {
        $file = $this->getRealFile($file);
        return filemtime($file);
    }

    /**
     * 获取文件名称
     * @param string $file
     * @return string
     */
    public function getBaseName($file = '') {
        $file = $this->getRealFile($file);
        return basename($file);
    }
}