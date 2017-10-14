<?php
namespace Common\Form;

use Think\Controller;

/**
 * 表单验证类
 * Class ApeadminActiveForm
 */
class ApeadminActiveForm extends Controller {
    public $validatejsUrl = "/Public/Apeadmin/js/jquery.validate-1.13.1.js"; //validate.js路径
    public $formId; //表单id默认为去掉Form的类名
    public $rules; //表单验证规则
    public $messages; //表单验证信息
    public $ajaxAction; //ajax提交地址
    public $successJs; //成功后执行的代码
    public $errorJs; //失改后执行的代码
    function __construct($formId = '') {
        parent::__construct();
        $childClassArr = explode('\\', get_class($this));
        $this->formId = $formId ? $formId : str_replace('Form', '', $childClassArr[count($childClassArr) - 1]);
    }

    /**
     * 多语言转换
     * @param $messagesArr
     * @return mixed
     */
    public function langExchange($messagesArr) {
        $pattern = '/{%(\S+?)}/i';
        foreach ($messagesArr as $k => $v) {
            foreach ($v as $m => $n) {
                preg_match($pattern, $n, $result);
                if ($result[1]) {
                    $messagesArr[$k][$m] = L($result[1]);
                }
            }
        }
        return $messagesArr;
    }

    /**
     * 如果需要动态修改成功或失败的js代码，可以在子类中重载run方法
     */
    public function run() {
        $vars = get_class_vars(get_class($this));
        $vars['formId'] = $vars['formId'] ? $vars['formId'] : $this->formId;
        $this->assign('vars', $vars);
        $this->display('Public/validate');
    }
}