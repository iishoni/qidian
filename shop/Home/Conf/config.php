<?php
return array(
	//'配置项'=>'配置值'
	'VIEW_PATH'		=> './Tpl/', //模板位置单独定义	
	'DEFAULT_THEME' => 'wap',     //定义模板主题
	'TMPL_PARSE_STRING'    => array(
        '__CSS__'       => __ROOT__ . '/Public/home/css1',
        '__JS__'       => __ROOT__ . '/Public/home/js',
        '__IMG__'       => __ROOT__ . '/Public/home/images',
        '__JS1__'       => __ROOT__ . '/Public/home/js1',
        '__ICON__'       => __ROOT__ . '/Public/home/icon'
    )
);