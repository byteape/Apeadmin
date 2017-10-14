<?php 

public function search(){
	$q=I('q');
	$this->q=$q;
	$data=array(
		'field'=>array('stitle','sdetail'),//需要输出的公共字段名称和下面的field一一对应
		'data'=>array(
			'about'=>array(
				'field'=>array('title','detail'),
				'conditions'=>array(
					array('title|detail'=>array('like','%'.$q.'%')),
					array('is_show'=>1)
				),
				'limit'=>'',
				'order'=>'',
				'url'=>"U('About/index',array('id'=>\$list['id']))",
			),
			'coach'=>array(
				'field'=>array('title','detail'),
				'conditions'=>array(
					array('title|detail'=>array('like','%'.$q.'%')),
					array('is_show'=>1)
				),
				'limit'=>'',
				'order'=>'',
				'url'=>"U('Coach/info',array('id'=>\$list['id']))",
			),
			'industry'=>array(
				'field'=>array('title','detail'),
				'conditions'=>array(
					array('title|detail'=>array('like','%'.$q.'%')),
					array('is_show'=>1)
				),
				'limit'=>'',
				'order'=>'',
				'url'=>"U('Industry/info',array('id'=>\$list['id']))",
			),
			'service'=>array(
				'field'=>array('title','detail'),
				'conditions'=>array(
					array('title|detail'=>array('like','%'.$q.'%')),
					array('is_show'=>1)
				),
				'limit'=>'',
				'order'=>'',
				'url'=>"U('Service/info',array('id'=>\$list['id']))",
			),
		)
	);

	import("Org.Util.MysqlMerge");
	$MysqlMerge=new \MysqlMerge($data,$q);
	$dataList=$MysqlMerge->getAll();

	foreach($dataList as $k=>$v){
		$dataList[$k]['sdetail']=str_cut(htmlspecialchars_decode($dataList[$k]['sdetail']),180);
	}
	$contentAll=data_pages_sys($dataList,10);
	$this->contentList=$contentAll['list'];
	$this->page=$contentAll['page'];
	$this->allnum=count($dataList);
	$this->display();
}



<div class="search_list">
	<div class="search_result">总共搜到{$allnum}条记录</div>
	<ul>
		<volist name="contentList" id="item">
		<li>
			<h2><a href="{$item.url}" target="_blank">{$item.stitle}</a></h2>
			<p>{$item.sdetail|search_replace=$q}</p>
		</li>
		</volist>
	</ul>   
	<div class="page">
		{$page}
		<div class="clear"></div>
	</div>   
</div>