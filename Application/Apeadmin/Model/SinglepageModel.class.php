<?php
namespace Apeadmin\Model;

use Think\Model\AdvModel;

class SinglepageModel extends AdvModel {
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
}