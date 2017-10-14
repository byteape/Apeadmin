表单验证
1、在Home\Form文件夹中建立相应的类文件
如FeedbackForm.class.php定义相关信息和规则
<?php
namespace Home\Form;
use Common\Form\HomeActiveForm;
class FeedbackForm extends HomeActiveForm{
    /*表单id，如果不定义，自动套用类名Feedback*/
    public $formId='';
    /*表单验证规则*/
    public $rules=array(
        'realname'=>array('required'=>true),
        'phone'=>array('required'=>true,'isMobile'=>true),
        'email'=>array('required'=>true,'email'=>true),
        'detail'=>array('required'=>true,'maxlength'=>300),
        'code'=>array('required'=>true),
    );
    /*表单验证信息*/
    public $messages=array(
        'realname'=>array('required'=>"请输入您的姓名"),
        'phone'=>array('required'=>"请输入您的电话号码",'isMobile'=>"电话号码格式错误"),
        'email'=>array('required'=>"请输入您的邮箱",'email'=>"邮箱格式错误"),
        'detail'=>array('required'=>"请输入详情内容",'maxlength'=>"详情内容最长为300个字符"),
        'code'=>array('required'=>"请输入验证码"),
    );
    public $ajaxAction='Ajax/feedback';//ajax提交地址
    public $successJs="window.location.reload();";//提交成功执行的js代码
    public $errorJs="";//提交失败执行的js代码
}
?>

2、在表单输出的控制器中
$FeedbackForm= new Form\FeedbackForm();
$this->FeedbackForm=$FeedbackForm;
别记了引入空间use Home\Form;

3、在模板页面中
<?php $FeedbackForm->run();?>

4、接收处理
<?php 
$data=array(
            'type'=>I('type'),
            'realname'=>I('realname'),
            'tel'=>I('tel'),
            'email'=>I('email'),
            'detail'=>I('detail'),
            'create_time'=>time(),
            'create_ip'=>get_client_ip()
        );
$old=M('feedback')->where(array('create_ip'=>$data['create_ip']))->order('create_time DESC')->find();
if($old & time()-$old['create_time']<30*60){
	$this->error('您操作太频繁了');
}else{
	$re=M('feedback')->data($data)->add();
	$re>-1?$this->success('留言成功'):$this->error('留言失败');
}