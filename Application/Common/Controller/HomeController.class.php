<?php
namespace Common\Controller;

use Apeadmin\Model\ConfigModel;
use Think\Controller;

class HomeController extends Controller {
    /**
     * 空方法404页
     */
    public function _empty() {
        $this->display('Public/404');
    }

    /**
     * 前台初始化要处理的
     */
    function __construct() {
        parent::__construct();
        $this->baseConfig(); //加载系统的基本配置
        $this->myConfig(); //加载自宝义的配置
    }

    /**
     * 针对本项目自定义的配置(只需要修改此文件即可)
     */
    public function myConfig() {
        $config = $this->config;

    }

    /**
     * 基本的配置(不用修改)
     */
    public function baseConfig() {
        /*网站开关1为关闭2为开启*/
        $web_open_file = C('WEB_OPEN_FILE');
        $web_open = include($web_open_file);
        if ($web_open['open_web'] == 1) {
            $this->close_web_detail = $web_open['close_web_detail'];
            $this->display('Public/404');
            exit();
        }
        /*配置文件输出*/
        $config = S('config');
        if (empty($config)) {
            $ConfigObj = new ConfigModel;
            $config = $ConfigObj->getAll();
            S('config', $config);
        }
        $this->assign('config', $config);
        $this->config = $config;
        /*富文本上线图片根目录字符替换锁*/
        if (__ROOT__ != '') {
            $lockFile = C('WEB_LOCK_FILE');
            $web_lock = include($lockFile);
            if ($web_open['DETAIL_PIC_ROOT'] != __ROOT__) {
                //写入文件
                $content = "<?php return array('DETAIL_PIC_ROOT'=>'" . __ROOT__ . "');?>";
                file_put_contents($lockFile, $content);
            }
        }
    }

    /**
     * 供子类seo信息的输出调用(不用修改)
     * @param $seoArr 包含seo_meta字段的数组，同时可能包含title或category_name的标题字段
     */
    protected function seoMsg($seoArr) {
        $seo = $seoArr['seo_meta'] ? unserialize($seoArr['seo_meta']) : '';
        $this->seo = $seo;
        $titleArr = array('title', 'category_name'); //title可能的字段，按优先级排列
        foreach ($titleArr as $k => $v) {
            if ($seoArr[$v]) {
                $this->assign('pageTitle', $seoArr[$v]);
                break;
            }
        }
    }
}