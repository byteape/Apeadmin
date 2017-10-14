<?php
namespace Apeadmin\Model;

use Think\Model\AdvModel;

class AdminGroupModel extends AdvModel {
    /**
     * 搜索数组
     * @var array
     */
    public $searchFields = array(
        'group_name' => 'like',
    );

    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('group_name', 'require', '组别名称必须填写！'),
        array('group_name', '', '组别名称已经存在！', 0, 'unique', 3),
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
            $condition['group_id'] = array('not in', $superId);
        } else {
            $condition = array('group_id' => array('not in', $superId));
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