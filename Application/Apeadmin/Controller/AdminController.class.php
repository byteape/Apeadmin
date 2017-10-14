<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;
use Apeadmin\Form;

/**
 * 类型：信息类别
 */
class AdminController extends ApeadminController {
    /**
     * 本控制器参数配置
     * @var array
     */
    public $controllerConfig = array(
        'lmodelName' => 'Admin', //信息列表模型名称
        'cmodelName' => 'AdminGroup', //类别模型名称
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
        $cmodelName = $controllerConfig['cmodelName'];
        //读取管理组别
        $groupList = D($cmodelName)->search('', 'group_id', true);
        $this->groupList = $groupList;
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
    public function edit($admin_id) {
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data, 2)) {
                $ModelObj->password = $ModelObj->password ? md5($ModelObj->password) : $ModelObj->getFieldByAdminId($admin_id, 'password');
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
            $model = $ModelObj->find($admin_id);
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
        $admin_id = I('get.admin_id');
        S('pefererPage', $_SERVER["HTTP_REFERER"], 1000);
        $flag = $ModelObj->delete($admin_id);
        if ($flag > 0) {
            $this->success('删除成功', S('pefererPage') ? S('pefererPage') : U('index'));
        } else {
            $this->error($ModelObj->getError());
        }
    }

    /**
     * 修改密码
     */
    public function editpassword() {
        $admin_id = session('admin_id');
        $EditPasswordForm = new Form\EditPasswordForm();
        $this->EditPasswordForm = $EditPasswordForm;
        $this->display();
    }
}