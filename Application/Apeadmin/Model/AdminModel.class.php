<?php
namespace Apeadmin\Model;

use Think\Model\AdvModel;

class AdminModel extends AdvModel {
    /**
     * 搜索数组
     * @var array
     */
    public $searchFields = array(
        'admin_name' => 'like',
    );

    /**
     * 自动完成
     * @var array
     */
    protected $_auto = array(
        array('password', 'md5', 1, 'function'), // 对password字段在新增的时候使md5函数处理
        array('login_time', 'time', 1, 'function'), // 对login_time字段在新增的时候写入当前时间戳
    );

    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('admin_name', 'require', '用户名必须填写！'),
        array('admin_name', '', '帐号名称已经存在！', 0, 'unique', 3),
        array('password', 'require', '密码必须填写！', 0, 'regex', 1),
    );

    /**
     * 搜索符合条件的记录
     * @param $conditions 条件数组
     * @param string $order 排序
     * @param int $number 每页条数
     * @param bool $all 是否查找所有
     * @return mixed
     */
    public function search($conditions, $order = 'group_id', $all = false, $number = 0) {
        $dataFields = M($this->name)->getDbFields();
        $condition = D('Dbsearch', 'Service')->get_condition($conditions, $dataFields, $this->searchFields);

        $superId = '1,2'; //超级管理员及总管理员帐号
        if (is_array($condition)) {
            $condition['admin_id'] = array('not in', $superId);
        } else {
            $condition = array('admin_id' => array('not in', $superId));
        }
        if (!$all) {
            $count = $this->where($condition)->count();
            $number = $number ? $number : C('PAGE_NUM');
            Vendor('Page.Page');
            $Page = new \Page($count, $number);
            $Page->setConfig('theme', '%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
            $show = $Page->show();
            $list = $this->where($condition)->order($order)->limit($Page->firstRow . ',' . $Page->listRows)->select();
            $array['list'] = $list;
            $array['page'] = $show;
            return $array;
        } else {
            return $this->where($condition)->order($order)->select();
        }
    }
}