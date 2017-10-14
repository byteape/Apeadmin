<?php
namespace Apeadmin\Model;
use Think\Model;

class AdminNodeModel extends Model {
    /**
     * 搜索数组
     * @var array
     */
    public $searchFields = array(
        'title' => 'like',
    );

    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('name', 'require', '节点名称必须填写！', 1),
    );

    /**
     * 搜索符合条件的记录
     * @param $conditions 条件数组
     * @param string $order 排序
     * @param int $number 每页条数
     * @param bool $all 是否查找所有
     * @return mixed
     */
    public function search($conditions, $order = 'node_id', $all = false, $number = 0) {
        $dataFields = M($this->name)->getDbFields();
        $condition = D('Dbsearch', 'Service')->get_condition($conditions, $dataFields, $this->searchFields);

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

?>
