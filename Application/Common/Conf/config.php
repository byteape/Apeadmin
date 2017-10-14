<?php
return array(
    /*模块相关配置*/
    'MODULE_ALLOW_LIST' => array('Home', 'Apeadmin'), //模块列表
    'DEFAULT_MODULE' => 'Home', //默认模块

    /*URL配置*/
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => '2',
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '/', //PATHINFO URL分割符

    /*数据库配置*/
    'DB_TYPE' => 'mysql',
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'apeadmin',
    'DB_USER' => 'root',
    'DB_PWD' => '',
    'DB_PORT' => '3306',
    'DB_PREFIX' => 'ape_',

    /*其他配置[不用修改]*/
    'WEB_OPEN_FILE' => './Application/Common/Conf/web_open.php', //网站开关设置配置文件
    'WEB_LOCK_FILE' => './Application/Common/Conf/web_lock.php', //富文本图片图片根路径替换字符锁

    /*其他配置[根据需要设置]*/
    'POWER_ROUTE' => false, //是否开启权限控制，开启权限控制后，还需要去添加节点
    /*需要忽略的路由(不需要在权限节点中定义的，谁都可以访问。管理员权限管理节点也不必定义。)[不用修改]*/
    'APEADMIN_IGNORE_ROUTE' => array(
        'Index/index', //后台首页
        'Index/exportDatabase', //备份数据表
        'Ajax/webcontroll', //后台首页网站开关控制
        'Ajax/editPassword', //后台管理员修改自身密码
        'Ajax/delRuntime', //清除缓存
        'Upload/uploadFile', //上传文件
        'Upload/delFile', //删除文件
        'Upload/kingEditorUpload', //编辑器上传文件
    ),
);