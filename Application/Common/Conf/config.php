<?php
return array(
	//'配置项'=>'配置值'
    'URL_MODEL'=>2,
    'DEFAULT_MODULE'=>'Home',
    'MODULE_ALLOW_LIST'=>array('Home','Admin',),
    'TMPL_PARSE_STRING'=>array(
        '__PUBLIC_ADMIN__'=>'/Public/Admin',
        '__PUBLIC_HOME__'=>'/Public/Home',
    ),

    'DB_TYPE'               =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  '127.0.0.1', // 服务器地址
    'DB_NAME'               =>  'jxshop',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'root',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    'DB_PREFIX'             =>  'jx_',
);