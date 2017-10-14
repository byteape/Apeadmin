控制器
/*
*其他$content是查询出的一条数据
*标题字段可以在HomeController中去设置，默认会识别title、category_name字段
*关于优先级别：seo_meta>模板页中$pageTitle的设置>$content['title|category_name|……']默认调用
*/
parent::seoMsg($content);//seo信息的输出



模板中
1、如果控制器使用的是parent::seoMsg($content);则在模板页不需要加任何内容
2、如果控制器没有使用parent::seoMsg($content);可在模板页中加入
<block name="php"><?php $pageTitle='联系我们';?></block>