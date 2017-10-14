<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 网站配置 类型：配置
 */
class ConfigController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'lmodelName' => 'Config', //信息列表模型名称
    );

    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        $controllerConfi = $this->controllerConfig;
        $ModelObj = D($controllerConfi['lmodelName']);
        $this->ModelObj = $ModelObj;
    }

    /**
     * 网站基本配置
     */
    public function index() {
        $ModelObj = $this->ModelObj;
        //配置需要处理的关键字
        $nameArray = array(
            'index_title',
            'index_keywords',
            'index_description',
            'logo',
            'copyright',
            'othercode',
        );
        //配置需要保留原html内容的关键字[是上面的子集]
        $noStripsArray = array(
            'othercode'
        );
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            foreach ($nameArray as $k => $v) {
                if (!in_array($v, $noStripsArray)) {
                    $ModelObj->set($v, stripslashes($data[$v]));
                } else {
                    $ModelObj->set($v, htmlspecialchars_decode($data[$v]));
                }
            }
            S('config', null);
            $this->success('保存成功');
        } else {
            foreach ($nameArray as $k => $v) {
                $this->assign($v, $ModelObj->get($v));
            }
            $this->display();
        }
    }

    /**
     * 网站其他配置
     */
    public function other() {
        $ModelObj = $this->ModelObj;
        //配置需要处理的关键字
        $nameArray = array(
            'otherbanner',
        );
        //配置需要保留原html内容的关键字[是上面的子集]
        $noStripsArray = array(
            ''
        );
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            foreach ($nameArray as $k => $v) {
                if (!in_array($v, $noStripsArray)) {
                    $ModelObj->set($v, stripslashes($data[$v]));
                } else {
                    $ModelObj->set($v, htmlspecialchars_decode($data[$v]));
                }
            }
            S('config', null);
            $this->success('保存成功');
        } else {
            foreach ($nameArray as $k => $v) {
                $this->assign($v, $ModelObj->get($v));
            }
            $this->display();
        }
    }

}
