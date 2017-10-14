<?php
namespace Apeadmin\Model;

use Think\Model\AdvModel;

class NewsCategoryModel extends AdvModel {
    /**
     * 搜索数组
     * @var array
     */
    public $searchFields = array(
        'category_name' => 'like',
        'is_show' => 'eq',
        'status' => 'eq',
    );

    /**
     * 自动验证
     * @var array
     */
    protected $_validate = array(
        array('category_name', 'require', '类别名称必须填写！', 3),
        array('order_id', 'require', '顺序编号必须填写！', 3),
        array('is_show', array(0, 1), '请设置是否显示！', 3, 'in')
    );

    /**
     * 序列化字段
     * @var array
     */
    protected $serializeField = array(
        'seo_meta' => array('seo_title', 'seo_keywords', 'seo_description'),
    );

    /**
     * 新增或修改时可调用指定的序列化字段
     * @param $data
     */
    public function serializeSet($data) {
        $serializeField = $this->serializeField;
        foreach ($serializeField as $k => $v) {
            foreach ($v as $i => $m) {
                $this->$m = $data[$m];
            }
        }
    }

    /**
     * 设置当前类别的路径
     * @param $parent_id
     * @return string
     */
    public function setPath($parent_id) {
        $parent_id_arr = array($parent_id ? $parent_id : 0);
        while ($parent_id) {
            $parent = $this->info($parent_id, 'parent_id');
            $parent_id = $parent['parent_id'];
            $parent_id_arr[] = $parent_id;
        }
        krsort($parent_id_arr);
        $this->path = implode(',', $parent_id_arr) . ',';;
    }

    /**
     * 获取分类详细信息
     * @param  milit $category_id 分类ID或标识
     * @param  boolean $field 查询字段
     * @return array     分类信息
     */
    public function info($category_id, $field = true) {
        /* 获取分类信息 */
        $map['category_id'] = $category_id;
        $data = M($this->name)->field($field)->where($map)->find(); //这里要用M，不然在获取路径外部调用时会改变data的值。
        return $data;
    }

    /**
     * 获取分类树，指定分类则返回指定分类极其子分类，不指定则返回所有分类树
     * @param array $conditions 附加搜索条件
     * @param int $category_id 分类ID
     * @param int $max_level 深度层级
     * @param bool $field 查询字段
     * @return array
     */
    public function getTree($conditions = array('is_show' => array('gt', -1)), $category_id = 0, $max_level = 100, $field = true) {
        $dataFields = D($this->name)->getDbFields();
        $where = D('Dbsearch', 'Service')->get_condition($conditions, $dataFields, $this->searchFields);
        /* 获取当前分类信息 */
        $name = true;
        if ($category_id) {
            $info = $this->info($category_id, $name);
            $category_id = $info['category_id'];
        }

        /* 获取所有分类 */
        $map = array('status' => array('gt', 0), 'level' => array('elt', $max_level));
        foreach ($where as $k => $v) {
            $map[$k] = $v;
        }
        $list = $this->field($field)->where($map)->order('order_id')->select();
        $list = list_to_tree($list, $pk = 'category_id', $pid = 'parent_id', $child = '_', $root = $category_id);

        /* 获取返回数据 */
        if (isset($info)) { //指定分类则返回当前分类极其子分类
            $info['_'] = $list;
        } else { //否则返回所有分类
            $info = $list;
        }

        return $info;
    }

    /**
     * 获取指定分类的同级分类
     * @param  integer $category_id 分类ID
     * @param  boolean $field 查询字段
     * @return array
     */
    public function getSameLevel($category_id, $field = true) {
        $name = $this->name;
        $info = $this->info($category_id, $name, 'parent_id');
        $map = array('parent_id' => $info['parent_id'], 'status' => 1, 'name' => $name);
        return $this->field($field)->where($map)->order('order_id')->select();
    }
}