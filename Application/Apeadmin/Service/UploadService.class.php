<?php
namespace Apeadmin\Service;

class UploadService {
    function __construct() {
        $config = array(
            'exts' => array('jpg', 'bmp', 'gif', 'png', 'jpeg'),
            'maxSize' => 0,
            'rootPath' => '.',
            'savePath' => '/Uploads/Temp/public/',
            'autoSub' => true,
            'subName' => array('date', 'Ymd'),
        );
        //上传文件类型限制[文件类型=>包含的后缀]
        $filetype = array(
            'image' => array('jpg', 'gif', 'png', 'jpeg', 'bmp'), //图片
            'file' => array('doc', 'pdf', 'rar', 'zip', 'xls', 'ppt', 'docx', 'xlsx', 'pptx', 'txt', 'jpg', 'gif', 'png', 'jpeg', 'bmp', 'mp4'), //文件
            'all' => array(), //不限
        );
        $this->filetype = $filetype;
        $this->config = $config;
        $this->model = new \Think\Upload($this->config);

    }

    /**
     * 设置相关配置
     * @param $name
     * @param $value
     */
    public function setconfig($name, $value) {
        $this->config[$name] = $value;
    }

    /**
     * 上传文件
     * @param $type
     * @return array|bool
     */
    public function upload($type) {
        $filetype = $this->filetype;
        $this->setconfig('exts', $filetype[$type]);
        $upload = new \Think\Upload($this->config);
        $this->model = $upload;
        $info = $upload->upload();
        return $info;
    }

    /**
     * 返回错误
     * @return array|bool
     */
    public function getError() {
        $upload = $this->model;
        return $upload->getError();
    }

    /**
     * 缩略图片处理
     * @param $file 上传后的信息数组
     * @param $thumbw 宽
     * @param $thumbh 高
     * @return string
     */
    public function thumb($file, $thumbw, $thumbh) {
        $filepath = $this->config['rootPath'] . $file['savepath'] . $file['savename'];
        $thumbdir = $this->config['rootPath'] . $file['savepath'] . 'thumb/';
        mkdirs($thumbdir);
        $saveName = $file['savepath'] . 'thumb/' . $file['savename'];
        $thumbpath = $this->config['rootPath'] . $saveName;
        $image = new \Think\Image();
        $image->open($filepath);
        $image->thumb($thumbw, $thumbh)->save($thumbpath);
        unlink($filepath);
        return $saveName;
    }

    /**
     * 删除文件
     * @param $filename
     * @return bool|string
     */
    public function del($filename) {
        //防止删除其它文件
        if (strpos($filename, 'Uploads') !== false) {
            if (strpos($filename, 'thumb/') !== false) {
                //如果此图片是经过压缩过的，删除非压缩的图片
                $newfilename = str_replace('thumb/', '', $filename);
                @delfile($newfilename); //删除非裁剪图片
            } else {
                //匹配删除压缩目录的图片
                $point = strripos($filename, "/"); //获取/最后一次出现的位置
                $prev = substr($filename, 0, $point); //截取前半部分
                $nex = substr($filename, $point); //截取后半部分
                $newfilename = $prev . "/thumb" . $nex; //拼接新的文件地址
                @delfile($newfilename); //删除裁剪图片
            }
            return delfile($filename);
        } else {
            return '0';
        }
    }
}
