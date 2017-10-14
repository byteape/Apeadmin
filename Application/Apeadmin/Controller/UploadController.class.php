<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 上传文件操作
 */
class UploadController extends ApeadminController {
    /**
     * 上传文件
     */
    public function uploadfile($path, $filetype) {
        $Model = D('Upload', 'Service');
        $Model->setconfig('savePath', '/Uploads/Temp/' . $path . '/');
        $info = $Model->upload($filetype);
        if (!$info) {
            $data['Message'] = $Model->getError();
            $data['Success'] = false;
        } else {
            foreach ($info as $file) {
                $filepath = $file['savepath'] . $file['savename'];
                $data['Success'] = true;
                $data['SaveName'] = $filepath;
                $thumbw = I('get.thumbw', '');
                $thumbh = I('get.thumbh', '');
                if ($thumbw != '' || $thumbh != '') {
                    $saveName = $Model->thumb($file, $thumbw, $thumbh);
                    $data['SaveName'] = $saveName;
                }
            }
        }
        $this->ajaxReturn($data);
    }

    /**
     * 删除文件
     */
    public function delfile($filename) {
        $Model = D('Upload', 'Service');
        $result = $Model->del($filename);
        echo 1;
    }

    /**
     * keditor编辑器上传图片处理
     */
    public function kingeditorupload() {
        $return = array('error' => 0, 'info' => '上传成功', 'data' => '');
        session('upload_error', null);
        //上传配置
        $setting = array(
            'mimes' => '', //允许上传的文件MiMe类型
            'maxSize' => 1 * 1024 * 1024, //上传的文件大小限制 (0-不做限制)
            'exts' => 'jpg,gif,png,jpeg,zip,rar,pdf,word,xls', //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => '.', //保存根路径这里必须为点
            'savePath' => '/Uploads/detail/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调函数，如果存在返回文件信息数组
        );
        //上传文件
        $Model = D('Upload', 'Service');
        foreach ($setting as $k => $v) {
            $Model->setconfig($k, $v);
        }
        $info = $Model->upload('all');
        if ($info) {
            $url = $setting['rootPath'] . $info['imgFile']['savepath'] . $info['imgFile']['savename'];
            //判断是否为图片根据传值决定是否生成缩略图
            if (I('get.dir') && I('get.thumbw') && I('get.thumbh') && in_array($info['imgFile']['ext'], array('jpg', 'gif', 'png', 'jpeg'))) {
                $url = $Model->thumb($info['imgFile'], I('get.thumbw'), I('get.thumbh'));
            }
            $url = str_replace('./', '/', $url);
            $info['fullpath'] = __ROOT__ . $url;
        }
        session('upload_error', $Model->getError());
        //返回数据
        if ($info) {
            $return['url'] = $info['fullpath'];
            unset($return['info'], $return['data']);
        } else {
            $return['error'] = 1;
            $return['message'] = session('upload_error');
        }
        //返回JSON数据
        exit(json_encode($return));
    }

}
