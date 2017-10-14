<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 类型：信息类别
 */
class NewsController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'lmodelName' => 'News', //信息列表模型名称
        'cmodelName' => 'NewsCategory', //类别模型名称
        'maxLevel' => 2, //内容列表允许选择的最深级别,类别新增时会减1(最小值为1)
        'isPhysicsDel' => false, //是否物理删除，默认为逻辑删除，适用于信息表和类别表
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
        $ModelObj_C = D($controllerConfig['cmodelName']);
        $this->ModelObj = $ModelObj;
        $this->maxLevel = $controllerConfig['maxLevel'];
        //获取类别列表选择
        $map = array('is_show' => 1, 'status' => 1);
        $categoryList = $ModelObj_C->getTree($map, 0, $this->maxLevel);
        $this->categoryList = $categoryList;
        $this->cmodelName = $controllerConfig['cmodelName'];
        $this->isPhysicsDel = $controllerConfig['isPhysicsDel'];
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
        //条件搜索
        $conditions = I('get.');
        $this->conditions = $conditions;
        $results = $ModelObj->search($conditions);
        $lists = $results['list'];
        $this->list = $lists;
        $this->page = $results['page'];
        $this->display();
    }

    /**
     * 新增页
     */
    public function add() {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data, 1)) {
                $ModelObj->serializeSet($data);
                $id = $ModelObj->add();
                if ($id > 0) {
                    $this->success('添加成功', S('pefererPage') ? S('pefererPage') : U('index'));
                } else {
                    $this->error($ModelObj->getLastSql());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $this->display();
        }
    }

    /**
     * 修改页
     */
    public function edit($id) {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data, 2)) {
                $ModelObj->serializeSet($data);
                $flag = $ModelObj->save();
                if ($flag !== false) {
                    $this->success('修改成功', S('pefererPage') ? S('pefererPage') : U('index'));
                } else {
                    $this->error($ModelObj->getLastSql());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            $model = $ModelObj->find($id);
            $this->model = $model;
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $this->display();
        }
    }

    /**
     * 删除页
     */
    public function del() {
        $ModelObj = $this->ModelObj;
        //ajax删除所选数据
        if (IS_AJAX && I('ajaxedit')) {
            $sel = I('post.selidArr');
            if (is_array($sel)) {
                $flag = 0;
                foreach ($sel as $id) {
                    if ($this->isPhysicsDel) {
                        $flag += $ModelObj->where(array($ModelObj->getPk() => $id))->delete();
                    } else {
                        $flag += $ModelObj->where(array($ModelObj->getPk() => $id))->setField(array('status' => 0));
                    }
                }
                $this->success('删除' . $flag . '条数据成功！');
            }
            exit();
        }
        $id = I('get.id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        if ($this->isPhysicsDel) {
            $flag = $ModelObj->delete($id);
        } else {
            $flag = $ModelObj->where(array($ModelObj->getPk() => $id))->setField(array('status' => 0));
        }
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('index'));
        } else {
            $this->error($ModelObj->getError());
        }
    }
}