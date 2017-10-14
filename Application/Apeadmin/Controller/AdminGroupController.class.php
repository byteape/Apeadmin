<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;
use Apeadmin\Form;

/**
 * 类型：信息类别
 */
class AdminGroupController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'cmodelName' => 'AdminGroup', //类别模型名称
        'nmodelName' => 'AdminNode', //节点模型名称
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
        $ModelObj_N = D($controllerConfig['nmodelName']);
        $this->ModelObj = $ModelObj;
        $this->ModelObj_N = $ModelObj_N;
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
    public function edit($group_id) {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data, 2)) {
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
            $model = $ModelObj->find($group_id);
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
                    $flag += $ModelObj->where(array($ModelObj->getPk() => $id))->delete();
                }
                $this->success('删除' . $flag . '条数据成功！');
            }
            exit();
        }
        $group_id = I('get.group_id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        $flag = $ModelObj->delete($group_id);
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('index'));
        } else {
            $this->error($ModelObj->getError());
        }
    }

    /**
     * 管理组权限节点配置
     * @param $group_id
     */
    public function accessManage($group_id) {
        $ModelObj = $this->ModelObj;
        if (in_array($group_id, array('1', '2')) && session('admin_id') != 1) {
            $this->error('操作错误');
        }
        if (IS_POST) {
            $nodes = I('node_id');
            $group_id = I('group_id');
            $flag = $ModelObj->where(array('group_id' => $group_id))->data(array('nodes' => implode(',', $nodes)))->save();
            if ($flag !== false)
                $this->success('修改成功', U('index'));
            else
                $this->error('修改失败');
            exit();
        } else {
            $this->group_id = $group_id;
            $thisGroup = $ModelObj->field('nodes')->find($group_id);
            $this->tGroup = explode(',', $thisGroup['nodes']);
            $ModelObj_N = $this->ModelObj_N;
            $nodeList = $ModelObj_N->where(array('is_top' => 1))->select();
            for ($i = 0; $i < count($nodeList); $i++) {
                $point = strrpos($nodeList[$i]['name'], '/');
                $bs = substr($nodeList[$i]['name'], 0, $point + 1);
                $child = $ModelObj_N->where(array('name' => array('like', $bs . '%'), 'node_id' => array('neq', $nodeList[$i]['node_id'])))->select();
                $nodeList[$i]['_'] = $child;
            }
            $this->nodeList = $nodeList;
            $this->display();
        }
    }
}