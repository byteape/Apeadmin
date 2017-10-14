信息类别
CREATE TABLE `fw_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT '类别id',
  `title` varchar(300) NOT NULL COMMENT '标题',
  `picture` varchar(300) DEFAULT NULL COMMENT '列表图片',
  `detail` text COMMENT '详细内容',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '显示',
  `create_time` int(11) NOT NULL COMMENT '发布时间',
  `seo_meta` text COMMENT 'seo信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='新闻信息表';


单页栏目
CREATE TABLE `fw_about` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL COMMENT '名称',
  `detail` text NOT NULL COMMENT '详情',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '显示',
  `order_id` int(11) NOT NULL DEFAULT '1000' COMMENT '排序',
  `seo_meta` text NOT NULL COMMENT 'seo信息',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='关于我们表';

banner表
CREATE TABLE `fw_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL COMMENT '标题',
  `picture` varchar(500) NOT NULL COMMENT '图片',
  `url` varchar(300) NOT NULL COMMENT '链接',
  `order_id` int(11) NOT NULL DEFAULT '1000' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '显示',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='轮播图表';