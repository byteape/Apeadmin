<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 类别管理 类型：类别
 */
class NewsCategoryController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'cmodelName' => 'NewsCategory', //信息列表模型名称
        'maxLevel' => 2, //内容列表允许选择的最深级别,类别新增时会减1(最小值为1)
        'isPhysicsDel' => false, //是否物理删除，默认为逻辑删除
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
        $ModelObj = D($controllerConfig['cmodelName']);
        $this->ModelObj = $ModelObj;
        $this->cmodelName = $controllerConfig['cmodelName'];
        $this->maxLevel = $controllerConfig['maxLevel'];
        //获取类别列表选择
        $map = array('is_show' => 1, 'status' => 1);
        $categoryList = $ModelObj->getTree($map, 0, $this->maxLevel);
        $this->categoryList = $categoryList;
        $this->isPhysicsDel = $controllerConfig['isPhysicsDel'];
    }

    /**
     * 类别列表
     */
    public function index() {
        $controllerConfig = $this->controllerConfig;
        $this->listPldelAction = $controllerConfig['listPldelActionArray'][ACTION_NAME]; //指定批量删除控制器
        $ModelObj = $this->ModelObj;
        if (IS_AJAX) {
            $t = I('get.t');
            $v = I('get.v');
            $id = I('get.i');
            echo $ModelObj->where($ModelObj->getPk() . '=' . $id)->setField($t, $v);
            exit();
        }
        //获取所有的类别数组
        $conditions = I('get.');
        $this->conditions = $conditions;
        $allList = $ModelObj->getTree($conditions);
        $this->allList = $allList;
        $this->display();
    }

    /**
     * 增加类别
     */
    public function add() {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            $parentLevel = $ModelObj->info($data['parent_id'], 'level');
            if ($ModelObj->create($data, 1)) {
                $ModelObj->level = $parentLevel['level'] + 1; //设置层次
                $ModelObj->setPath($data['parent_id']); //设置路径
                $ModelObj->serializeSet($data); //序列化值
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
            //获取类别列表
            $maxLevel = $this->maxLevel;
            $map = array();
            $parentList = $ModelObj->getTree($map, 0, $maxLevel - 1);
            $this->parentList = $parentList; //类别选择的父级类别数组
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $this->display();
        }
    }

    /**
     * 修改类别
     * @param $category_id 类别主键值
     */
    public function edit($category_id) {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            $parentLevel = $ModelObj->info($data['parent_id'], 'level');
            if ($ModelObj->create($data)) {
                $ModelObj->level = $parentLevel['level'] + 1; //设置层次
                $ModelObj->setPath($data['parent_id']); //设置路径
                $ModelObj->serializeSet($data); //序列化值
                $flag = $ModelObj->save();
                if ($flag !== false) {
                    $this->success('修改成功', S('pefererPage') ? S('pefererPage') : U('index'));
                } else {
                    $this->error($ModelObj->getError());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            $model = $ModelObj->where(array('category_id' => $category_id))->find();
            if (!$model) {
                $this->error('非法操作!');
            }
            $this->model = $model;

            //获取类别列表
            $maxLevel = $this->maxLevel;
            $map = array();
            $parentList = $ModelObj->getTree($map, 0, $maxLevel - 1);
            $this->parentList = $parentList; //类别选择的父级类别数组
            S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
            $this->display();
        }
    }

    /**
     * 删除类别
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
        $category_id = I('get.category_id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        if ($this->isPhysicsDel) {
            $flag = $ModelObj->delete($category_id);
        } else {
            $flag = $ModelObj->where(array($ModelObj->getPk() => $category_id))->setField(array('status' => 0));
        }
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('cindex'));
        } else {
            $this->error($ModelObj->getError() ? $ModelObj->getError() : '非法操作!');
        }
    }

    /**
     * 显示所有的类别在index中使用
     * @param null $allList
     */
    public function allList($allList = null) {
        $this->assign('allList', $allList);
        $this->display($this->cmodelName . '/allList');
    }

    /**
     * 增加和修改类别所属父类选择add和edit中使用
     * @param null $parentList
     */
    public function parentList($parentList = null) {
        $this->assign('parentList', $parentList);
        $this->display($this->cmodelName . '/parentList');
    }

    /**
     * 类别列表输出
     * @param null $categoryList
     */
    public function categoryList($categoryList = null) {
        $this->assign('categoryList', $categoryList);
        $this->display($this->cmodelName . '/categoryList');
    }

}