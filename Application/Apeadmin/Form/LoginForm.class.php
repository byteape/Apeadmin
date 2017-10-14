<?php
namespace Apeadmin\Form;

use Common\Form\ApeadminActiveForm;

/**
 * 表单验证器
 * ------------------------------------------------
 * 同时还需要如下操作
 * 1、在表单模板输出的控制器中
 * $LoginForm= new Form\LoginForm();
 * $this->LoginForm=$LoginForm;
 * 别记了引入空间use Apeadmin\Form;
 * 2、在表单所在的模板页面中
 * <?php $LoginForm->run();?>
 */
class LoginForm extends ApeadminActiveForm {
    /*表单id，如果不定义，自动套用类名Feedback*/
    public $formId = '';
    /*表单验证规则*/
    public $rules = array(
        'admin_name' => array('required' => true),
        'password' => array('required' => true),
        'code' => array('required' => true),
    );
    /*表单验证信息*/
    public $messages = array(
        'admin_name' => array('required' => "{%please_enter_username}"),
        'password' => array('required' => "{%please_enter_password}"),
        'code' => array('required' => "{%please_enter_code}"),
    );
    public $ajaxAction = 'Login/index'; //ajax提交地址
    public $successJs = "window.location.href = data.url"; //提交成功执行的js代码
    public $errorJs = "$('#verify').click()"; //提交失败执行的js代码

    /**
     * 执行输出
     */
    public function run() {
        $vars = get_class_vars(get_class($this));
        $vars['formId'] = $vars['formId'] ? $vars['formId'] : $this->formId;
        $vars['messages'] = parent::langExchange($vars['messages']); //多语言转换
        $this->assign('vars', $vars);
        $this->display('Public/validate');
    }
}