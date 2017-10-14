<?php
namespace Common\Controller;

use Think\Controller;

class ApeadminController extends Controller {

    function __construct() {
        parent::__construct();
        $admin_id = session('admin_id');
        if (!$admin_id) {
            $cookie_admin_id = I('cookie.admin_id', 0);
            $cookie_key_login_code = I('cookie.key_login_code', '');
            if ($cookie_admin_id > 0 && $cookie_key_login_code != '') {
                $arr = D('Admin')->where(Array('admin_id' => $cookie_admin_id, 'key_login_code' => $cookie_key_login_code))->find();
                if (is_array($arr)) {
                    session('admin_id', $arr['admin_id']);
                    session('admin_name', $arr['admin_name']);
                    session('admin_group_id', $arr['group_id']);
                }
            } else {
                header("location:" . U('Login/index'));
            }
        } else {
            //如果管理员已经登录要判定权限
            if (!in_array(session('admin_id'), array('1', '2')) && C('POWER_ROUTE')) {
                $this->nodesCheck(session('admin_group_id'));
            }
            $this->setSiderbar();
        }
    }

    /**
     * 管理员权限检查
     * @param $admin_group_id 组别id号
     */
    public function nodesCheck($admin_group_id) {
        $ignoreRoute = C('APEADMIN_IGNORE_ROUTE');
        foreach ($ignoreRoute as $k => $v) {
            $ignoreRoute[$k] = strtolower($v);
        }
        $nodes = S('nodes');
        if (!$nodes) {
            $group = D('AdminGroup')->field('nodes')->find($admin_group_id);
            S('nodes', $group['nodes']);
        } else {
            $nowName = strtolower(CONTROLLER_NAME . '/' . ACTION_NAME);
            if (!in_array($nowName, $ignoreRoute)) {
                $map = array(
                    'node_id' => array('in', $nodes),
                    'name' => $nowName,
                );
                $is_check = D('AdminNode')->where($map)->find();
                if (!$is_check) {
                    $this->error('您没有管理权限');
                }
            }
        }
    }

    /**
     * 设置左侧菜单导航
     */
    public function setSiderbar() {
        $siderbarArray = require(C('WEB_ADMIN_SIDEBAR'));
        $this->siderbarArray = $siderbarArray;
    }
}