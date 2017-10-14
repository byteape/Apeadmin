<?php
/*需要添加一个发送邮件的代理在config配置文件中*/
'MAIL_ADDRESS'          =>  '****@sina.com',        // 初始化邮箱地址
'MAIL_SMTP'             =>  'smtp.sina.com',        // 初始化邮箱SMTP服务器
'MAIL_LOGINNAME'        =>  '****@sina.com',        // 初始化邮箱登录帐号
'MAIL_PASSWORD'         =>  '****',                 // 初始化邮箱密码
'MAIL_CHARSET'          =>  'UTF-8',                //编码
'MAIL_AUTH'             =>  true,                   //邮箱认证
'MAIL_HTML'             =>  true,                   //true HTML格式 false TXT格式

/*以下是控制器中的使用*/
import('Org.Util.Mail');//导入邮件类
$sendTitle="邮件标题";//邮件标题
$sendData="邮件内容，可以使用html代码";//发送内容
$toMail="918618594@qq.com";//收件人
$theme="留言反馈邮件";//邮件主题
SendMail($toMail,$sendTitle,$sendData,$theme);