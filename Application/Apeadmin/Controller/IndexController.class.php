<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

class IndexController extends ApeadminController {
    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        //网站开关设置保存文件
        $web_open_file = C('WEB_OPEN_FILE');
        $this->web_open_file = $web_open_file;
    }

    /**
     * 后台首页
     */
    public function index() {
        /*获取网站开关配置*/
        $web_open_file = $this->web_open_file;
        $web_open = include($web_open_file);
        $this->web_open = $web_open;
        $this->display();
    }

    /**
     * 图标库
     */
    public function icons() {
        $this->display();
    }

    /**
     * 数据库备份[只有超级管理员和总管理员才有备份资格]
     */
    public function exportdatabase() {
        $admin_id = session('admin_id');
        if (!in_array($admin_id, array('1', '2'))) {
            $this->error('非法操作');
            exit();
        }
        $root = __ROOT__ ? '..' : '.';
        $path = $root . __ROOT__ . '/Data/';
        //查询所有表
        $Model = M();
        $sql = "show tables";
        $result = $Model->query($sql);
        $info = "-- ----------------------------\r\n";
        $info .= "-- 日期：" . date("Y-m-d H:i:s", time()) . "\r\n";
        $info .= "-- MySQL - 5.1.73 : Database - " . C('DB_NAME') . "\r\n";
        $info .= "-- ----------------------------\r\n\r\n";
        $info .= "CREATE DATAbase IF NOT EXISTS `" . C('DB_NAME') . "` DEFAULT CHARACTER SET utf8 ;\r\n\r\n";
        $info .= "USE `" . C('DB_NAME') . "`;\r\n\r\n";

        $fnum = scandir($path);
        for ($i = 2; $i < count($fnum); $i++) {
            $fname = $path . $fnum[$i];
            unlink($fname);
        }
        $filename = C('DB_NAME') . '-' . date("Y-m-d", time()) . md5(rand(1000, 9999)) . '.sql';
        $file_name = $path . $filename;
        file_put_contents($file_name, $info, FILE_APPEND);
        foreach ($result as $k => $v) {
            //查询表结构
            $val = $v['tables_in_' . C('DB_NAME')];
            $sql_table = "show create table " . $val;
            $res = $Model->query($sql_table);
            //print_r($res);exit;
            $info_table = "-- ----------------------------\r\n";
            $info_table .= "-- Table structure for `" . $val . "`\r\n";
            $info_table .= "-- ----------------------------\r\n\r\n";
            $info_table .= "DROP TABLE IF EXISTS `" . $val . "`;\r\n\r\n";
            $info_table .= $res[0]['create table'] . ";\r\n\r\n";
            //查询表数据
            $info_table .= "-- ----------------------------\r\n";
            $info_table .= "-- Data for the table `" . $val . "`\r\n";
            $info_table .= "-- ----------------------------\r\n\r\n";
            file_put_contents($file_name, $info_table, FILE_APPEND);
            $sql_data = "select * from " . $val;
            $data = $Model->query($sql_data);
            $count = count($data);
            if ($count < 1) continue;
            foreach ($data as $key => $value) {
                $sqlStr = "INSERT INTO `" . $val . "` VALUES (";
                foreach ($value as $v_d) {
                    $v_d = str_replace("'", "\'", $v_d);
                    $sqlStr .= "'" . $v_d . "', ";
                }
                //需要特别注意对数据的单引号进行转义处理
                //去掉最后一个逗号和空格
                $sqlStr = substr($sqlStr, 0, strlen($sqlStr) - 2);
                $sqlStr .= ");\r\n";
                file_put_contents($file_name, $sqlStr, FILE_APPEND);
            }
            $info = "\r\n";
            file_put_contents($file_name, $info, FILE_APPEND);
        }
        header('Content-type: application/sql');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        readfile($file_name);
        exit();
    }
}