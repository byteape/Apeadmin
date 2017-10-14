<?php

/**
 * 多表合并查询类
 * +------------------------------------说明------------------------------------+
 * 将多个表的数据组装成一个数据集，常用于全表搜索
 * +------------------------------------说明------------------------------------+
 * +------------------------------------使用------------------------------------+
 * $data=array(
     * 'field'=>array('stitle','spec'),//需要输出的公共字段名称和下面的field一一对应
     * 'data'=>array(
     *      'about'=>array(
     *          'field'=>array('title','detail'),
     *          'conditions'=>array(
     *              array('title|detail'=>array('like','%'.$q.'%')),
     *              array('is_show'=>1)
     *          ),
     *          'limit'=>'',
     *          'order'=>'',
     *          'url'=>"U('About/index',array('id'=>\$list['id']))",
     *      ),
     *      'coach'=>array(
     *          'field'=>array('title','detail'),
     *          'conditions'=>array(
     *              array('title|detail'=>array('like','%'.$q.'%')),
     *              array('is_show'=>1)
     *          ),
     *          'limit'=>'',
     *          'order'=>'',
     *          'url'=>"U('Coach/info',array('id'=>\$list['id']))",
     *      ),
     * )
 * );
 * +------------------------------------使用------------------------------------+
 * Class MysqlMerge
 */
class  MysqlMerge {
    public $data = ''; //输入的数据数组
    public $field = array(); //需要返回的字段数组
    public $allList = array(); //返回的新数组
    public $keywords = ''; //搜索关键词

    /**
     * 构造函数
     * @param $data
     * @param string $keywords
     */
    public function __construct($data, $keywords = '') {
        $this->field = $data['field'];
        $this->data = $data['data'];
        $this->keywords = $keywords;
    }

    /**
     * 对传入的data进行拆分获取数据
     * @return array
     */
    public function getAll() {
        $data = $this->data;
        $field = $this->field;
        foreach ($data as $k => $v) {
            $content = M($k)->where($v['conditions'])->limit($v['limit'])->order($v['order'])->select();
            if ($content) {
                foreach ($content as $m => $n) {
                    foreach ($v['field'] as $p => $q) {
                        $contents[$m][$field[$p]] = str_ireplace($this->keywords, '<span style="color:red">' . $this->keywords . '</span>', $content[$m][$q]);
                        $list = $content[$m];
                        $contents[$m]['url'] = eval("return " . $v['url'] . ";");
                    }
                    array_push($this->allList, $contents[$m]);
                }
            }
            unset($content);
            unset($contents);
        }
        return $this->allList;
    }
}