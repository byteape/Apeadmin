<?php
return array(
    /*系统配置*/
    'SHOW_ERROR_MSG' => true, //在部署模式下仍然显示错误信息
    'DB_FIELDS_CACHE' => false, //关闭数据缓存_fields

    /*其他参数*/
    'PAGE_NUM' => 15, //默认每页显示记录数
    'LANG_SWITCH_ON' => true,   // 开启语言包功能
    'LANG_AUTO_DETECT' => true, // 自动侦测语言 开启多语言功能后有效
    'LANG_LIST'        => 'zh-cn', // 允许切换的语言列表 用逗号分隔
    'VAR_LANGUAGE'     => 'l', // 默认语言切换变量

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__LIB__' => __ROOT__ . '/Public/' . MODULE_NAME . '/lib',
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
    ),

    /*SESSION和COOKIE配置*/
    'SESSION_PREFIX' => 'siteape_admin_', //session前缀
    'COOKIE_PREFIX' => 'siteape_admin_', // Cookie前缀 避免冲突

    /*其他配置[根据需要设置]*/
    'DETAIL_PIC_RESET' => true, //是否使用html图片根目录路径输出替换
    'WEB_ADMIN_SIDEBAR' => './Application/' . MODULE_NAME . '/Conf/siderbar.php', //后台导航菜单文件


);