<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 留言 类型：单页栏目
 */
class FeedbackController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'lmodelName' => 'Feedback', //信息列表模型名称
        'listPldelActionArray' => array(
            'index' => 'del',
        ), //指定批量删除控制器列表控制器名称=>删除控制器名称
    );

    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        $controllerConfig = $this->controllerConfig;
        $ModelObj = D($controllerConfig['lmodelName']);
        $this->ModelObj = $ModelObj;
    }

    /**
     * 列表页
     */
    public function index() {
        $controllerConfig = $this->controllerConfig;
        $this->listPldelAction = $controllerConfig['listPldelActionArray'][ACTION_NAME]; //指定批量删除控制器
        $ModelObj = $this->ModelObj;

        //ajax修改数据字段
        if (IS_AJAX) {
            $t = I('get.t');
            $v = I('get.v');
            $id = I('get.i');
            echo $ModelObj->where($ModelObj->getPk() . '=' . $id)->setField($t, $v);
            exit();
        }
        //搜索条件
        $conditions = I('get.');
        $this->conditions = $conditions;
        $results = $ModelObj->search($conditions);
        $lists = $results['list'];
        $this->list = $lists;
        $this->page = $results['page'];
        //将所有的内容标为已读
        $ModelObj->where(array('id' => array('gt', 0), 'is_read' => 0))->data(array('is_read' => 1))->save();
        $this->display();
    }

    /**
     * 查看详情获取数据
     * @param $id
     */
    public function info($id) {
        $ModelObj = $this->ModelObj;
        $content = $ModelObj->find($id);
        $this->content = $content;
        $this->display('info');
    }

    /**
     * 删除记录
     */
    public function del() {
        $ModelObj = $this->ModelObj;
        //ajax删除所选数据
        if (IS_AJAX && I('ajaxedit')) {
            $sel = I('post.selidArr');
            if (is_array($sel)) {
                $flag = 0;
                foreach ($sel as $id) {
                    $flag += $ModelObj->where(array($ModelObj->getPk() => $id))->delete();
                }
                $this->success('删除' . $flag . '条数据成功！');
            }
            exit();
        }
        $id = I('get.id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        $flag = $ModelObj->delete($id);
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('index'));
        } else {
            $this->error('删除失败');
        }
    }

}