<?php
namespace Home\Form;

use Common\Form\HomeActiveForm;

/**
 * 表单验证器
 * Class FeedbackForm
 * @package Home\Form
 * ------------------------------------------------
 * 同时还需要如下操作
 * 1、在表单模板输出的控制器中
 * $FeedbackForm= new Form\FeedbackForm();
 * $this->FeedbackForm=$FeedbackForm;
 * 别记了引入空间use Home\Form;
 * 2、在表单所在的模板页面中
 * <?php $FeedbackForm->run();?>
 */
class FeedbackForm extends HomeActiveForm {
    /*表单id，如果不定义，自动套用类名Feedback*/
    public $formId = '';
    /*表单验证规则*/
    public $rules = array(
        'realname' => array('required' => true),
        'phone' => array('required' => true, 'isMobile' => true),
        'email' => array('required' => true, 'email' => true),
        'detail' => array('required' => true, 'maxlength' => 300),
        'code' => array('required' => true),
    );
    /*表单验证信息*/
    public $messages = array(
        'realname' => array('required' => "请输入您的姓名"),
        'phone' => array('required' => "请输入您的电话号码", 'isMobile' => "电话号码格式错误"),
        'email' => array('required' => "请输入您的邮箱", 'email' => "邮箱格式错误"),
        'detail' => array('required' => "请输入详情内容", 'maxlength' => "详情内容最长为300个字符"),
        'code' => array('required' => "请输入验证码"),
    );
    public $ajaxAction = 'Ajax/feedback'; //ajax提交地址
    public $successJs = "window.location.reload();"; //提交成功执行的js代码
    public $errorJs = ""; //提交失败执行的js代码

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