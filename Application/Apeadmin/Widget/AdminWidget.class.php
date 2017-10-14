<?php
namespace Apeadmin\Widget;

use Think\Controller;
use Common\Controller\ApeadminController;

class AdminWidget extends ApeadminController {
    /**
     * 栏目seometa信息
     */
    public function seoMeta($model) {
        $this->model = $model;
        $this->display('Widget:seoMeta');
    }

    /**
     *头部留言是否有内容显示
     */
    public function headerfeedback() {
        $count = D('Feedback')->where(array('is_read' => 0))->count();
        $this->count = $count;
        $this->display('Widget:headerfeedback');
    }
}
