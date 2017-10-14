<?php
namespace Apeadmin\Controller;

use Think\Controller;
use Common\Controller\ApeadminController;

/**
 * 单页面 类型：单页面
 */
class SinglepageController extends ApeadminController {
    /**
     * 初始化
     */
    function __construct() {
        parent::__construct();
        $ModelObj = D('Singlepage');
        $this->ModelObj = $ModelObj;
        //注册所有单页,id号和数据库一一对应
        $singlePage = array(
            array('id' => 1, 'name' => '服务中心'),
        );
        $this->singlePage = $singlePage;
    }

    /**
     * 编辑记录
     * @param $id 主键值
     */
    public function edit($id) {
        $this->sid = $id;
        $ModelObj = $this->ModelObj;
        if (IS_POST) {
            $data = I('post.');
            import("Org.Util.FileTemp");
            $FileTemp = new \FileTemp();
            $data = $FileTemp->run($data);
            if ($ModelObj->create($data)) {
                $ModelObj->serializeSet($data); //序列化值
                $flag = $ModelObj->save();
                if ($flag !== false) {
                    $this->success('修改成功', U('edit', array('id' => $id)));
                } else {
                    $this->error($ModelObj->getError());
                }
            } else {
                $this->error($ModelObj->getError());
            }
        } else {
            $model = $ModelObj->find($id);
            if (!$model) {
                $ModelObj->add(array('id' => $id));
                $model = $ModelObj->find($id);
            }
            $this->model = $model;
            //获取注册的单页栏目名称
            $singlePage = $this->singlePage;
            for ($i = 0; $i < count($singlePage); $i++) {
                if ($singlePage[$i]['id'] == $id) {
                    $this->title = $singlePage[$i]['name'];
                    break;
                }
            }
            //如果以id号命名的模板存在则输出，若不存在则输出通用模板
            $tpl = CONTROLLER_NAME . '/' . $id;
            if (file_exists(T($tpl))) {
                $this->display($id);
            } else {
                $this->display('default');
            }
        }
    }

}