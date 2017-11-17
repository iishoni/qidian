-- MySQL database dump
-- 主机: 
-- 生成日期: 2017 年  10 月 24 日 18:19
-- MySQL版本: 
-- PHP 版本: 5.5.38
-- 数据库: `huzhu_db`
-- ---------------------------------------------------------
-- 表的结构ysk_activate_num
--
DROP TABLE IF EXISTS `ysk_activate_num`;
CREATE TABLE `ysk_activate_num` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '激活码',
  `num` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `to_id` int(11) DEFAULT '0' COMMENT '对方ID',
  `to_username` varchar(20) DEFAULT NULL,
  `to_mobile` varchar(20) DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0-获得 1出售',
  `create_time` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_activate_num

--
-- 表的结构ysk_admin
--
DROP TABLE IF EXISTS `ysk_admin`;
CREATE TABLE `ysk_admin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'UID',
  `auth_id` int(11) NOT NULL DEFAULT '1' COMMENT '角色ID',
  `nickname` varchar(63) DEFAULT NULL COMMENT '昵称',
  `username` varchar(31) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(63) NOT NULL DEFAULT '' COMMENT '密码',
  `mobile` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号',
  `reg_ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '注册IP',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  `reg_type` varchar(20) DEFAULT NULL COMMENT '注册人',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='用户账号表';

--
-- 转存表中的数据 ysk_admin

INSERT INTO `ysk_admin` VALUES('1','1','超级管理员','admin','8f3bd6b4d00391c9d09cc14e32fee28c','','0','1438651748','1504694771','1','');
--
-- 表的结构ysk_admin_zhuangz
--
DROP TABLE IF EXISTS `ysk_admin_zhuangz`;
CREATE TABLE `ysk_admin_zhuangz` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '管理员给用户拨发果子明细表id',
  `manage_id` int(11) NOT NULL COMMENT '管理员id',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `num` decimal(15,0) NOT NULL COMMENT '转给用户的果子数量',
  `create_time` int(11) NOT NULL COMMENT '转果子时间',
  `ip` varchar(20) NOT NULL COMMENT '转果子时使用的电脑ip',
  `before_num` decimal(11,0) NOT NULL DEFAULT '0',
  `after_num` decimal(11,0) NOT NULL,
  `type` char(20) NOT NULL,
  `content` varchar(255) NOT NULL,
  `username` varchar(20) DEFAULT NULL,
  `account` varchar(20) NOT NULL,
  `manage_name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_admin_zhuangz

--
-- 表的结构ysk_banner
--
DROP TABLE IF EXISTS `ysk_banner`;
CREATE TABLE `ysk_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管告图',
  `banner_name` varchar(255) DEFAULT NULL,
  `banner_type` varchar(4) NOT NULL DEFAULT '' COMMENT '广告图类型',
  `banner_link` varchar(255) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `sort` char(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `banner_img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_banner

INSERT INTO `ysk_banner` VALUES('3','3434','1','','1','','2','14');
INSERT INTO `ysk_banner` VALUES('7','rere','1','','1','1501155469','10','15');
--
-- 表的结构ysk_buy_num_detail
--
DROP TABLE IF EXISTS `ysk_buy_num_detail`;
CREATE TABLE `ysk_buy_num_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(20) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '转账数量',
  `to_id` int(11) NOT NULL DEFAULT '0',
  `to_mobile` varchar(20) NOT NULL,
  `datetime` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0-获得 1-转出 2-抢单',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_buy_num_detail

--
-- 表的结构ysk_config
--
DROP TABLE IF EXISTS `ysk_config`;
CREATE TABLE `ysk_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID',
  `title` varchar(32) NOT NULL DEFAULT '' COMMENT '配置标题',
  `name` varchar(32) DEFAULT NULL COMMENT '配置名称',
  `value` text NOT NULL COMMENT '配置值',
  `group` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '配置分组',
  `type` varchar(16) NOT NULL DEFAULT '' COMMENT '配置类型',
  `options` varchar(255) NOT NULL DEFAULT '' COMMENT '配置额外值',
  `tip` varchar(100) NOT NULL DEFAULT '' COMMENT '配置说明',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `sort` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='系统配置表';

--
-- 转存表中的数据 ysk_config

INSERT INTO `ysk_config` VALUES('1','站点开关','TOGGLE_WEB_SITE','1','3','0','0:关闭\r\n1:开启','系统升级，暂时关闭网站','1378898976','1406992386','1','1');
INSERT INTO `ysk_config` VALUES('2','网站标题','WEB_SITE_TITLE','','1','0','','网站标题前台显示标题','1378898976','1379235274','2','1');
INSERT INTO `ysk_config` VALUES('3','网站LOGO','WEB_SITE_LOGO','0','1','0','','网站LOGO','1407003397','1407004692','3','1');
INSERT INTO `ysk_config` VALUES('4','网站描述','WEB_SITE_DESCRIPTION','','1','0','','网站搜索引擎描述','1378898976','1379235841','4','1');
INSERT INTO `ysk_config` VALUES('5','网站关键字','WEB_SITE_KEYWORD','','1','0','','网站搜索引擎关键字','1378898976','1381390100','5','1');
INSERT INTO `ysk_config` VALUES('6','版权信息','WEB_SITE_COPYRIGHT','','1','0','','设置在网站底部显示的版权信息，如“版权所有 © 2014-2015 科斯克网络科技”','1406991855','1406992583','6','1');
INSERT INTO `ysk_config` VALUES('7','网站备案号','WEB_SITE_ICP','','1','0','','设置在网站底部显示的备案号，如“苏ICP备1502009号\"','1378900335','1415983236','9','1');
INSERT INTO `ysk_config` VALUES('8','普通矿车拆分','CAR_COMMON_FEE','5','2','0','','','0','0','20','1');
INSERT INTO `ysk_config` VALUES('9','银矿车拆分','CAR_SILVER_FEE','6','2','0','','','0','0','21','1');
INSERT INTO `ysk_config` VALUES('10','普通矿车费用','CAR_COMMON_NUM','500','2','0','','','0','0','21','1');
INSERT INTO `ysk_config` VALUES('11','定向交易手续费','TARGET_FEE','5','4','0','','','0','0','30','1');
INSERT INTO `ysk_config` VALUES('12','自由交易手续费','FREE_FEE','10','4','0','','','0','0','31','1');
INSERT INTO `ysk_config` VALUES('13','平台回收手续费','BACK_FEE','20','4','0','','','0','0','31','1');
INSERT INTO `ysk_config` VALUES('14','银矿车费用','CAR_SILVER_NUM','1000','2','','','','0','0','23','1');
INSERT INTO `ysk_config` VALUES('15','购买手续费','REG_FEE','25','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('16','平台回收限制','BACK_LIMIT','30','4','','','','0','0','33','1');
INSERT INTO `ysk_config` VALUES('17','普通监工/直推10人','LEVEL1','0.1','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('18','中级监工/直推20人','LEVEL2','0.2','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('19','高级监工/直推40人','LEVEL3','0.4','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('20','金牌监工/直推70人','LEVEL4','0.6','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('22','金矿车费用','CAR_GOLD_NUM','2000','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('23','金矿车拆分','CAR_GOLD_FEE','8','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('24','一级好友采矿拆分','FRIENDS_ONE','8','2','','','','0','0','0','1');
INSERT INTO `ysk_config` VALUES('25','二级好友采矿拆分','FRIENDS_TWO','5','2','','','','0','0','0','1');
--
-- 表的结构ysk_daywork
--
DROP TABLE IF EXISTS `ysk_daywork`;
CREATE TABLE `ysk_daywork` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `datestr` varchar(20) NOT NULL DEFAULT '' COMMENT '一年中的第几周',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '当天已抢数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_daywork

INSERT INTO `ysk_daywork` VALUES('1','1000','20171024','25');
INSERT INTO `ysk_daywork` VALUES('2','2000','20171024','25');
INSERT INTO `ysk_daywork` VALUES('3','5000','20171024','32');
INSERT INTO `ysk_daywork` VALUES('4','10000','20170906','24');
--
-- 表的结构ysk_group
--
DROP TABLE IF EXISTS `ysk_group`;
CREATE TABLE `ysk_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '部门ID',
  `pid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上级部门ID',
  `title` varchar(31) NOT NULL DEFAULT '' COMMENT '部门名称',
  `icon` varchar(31) NOT NULL DEFAULT '' COMMENT '图标',
  `menu_auth` text NOT NULL COMMENT '权限列表',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  `auth_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='部门信息表';

--
-- 转存表中的数据 ysk_group

INSERT INTO `ysk_group` VALUES('1','0','超级管理员','','','1426881003','1427552428','0','1','1');
INSERT INTO `ysk_group` VALUES('7','0','放单员','','4,327,330,8,9,320,321,323,325,326','1504944415','1505101619','0','1','0');
--
-- 表的结构ysk_menu
--
DROP TABLE IF EXISTS `ysk_menu`;
CREATE TABLE `ysk_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '菜单名称',
  `pid` int(11) NOT NULL COMMENT '父级id',
  `gid` int(11) NOT NULL DEFAULT '0' COMMENT '爷爷ID、',
  `col` varchar(30) NOT NULL COMMENT '控制器',
  `act` varchar(30) NOT NULL COMMENT '方法',
  `patch` varchar(50) DEFAULT NULL COMMENT '全路径',
  `level` int(11) NOT NULL COMMENT '级别',
  `icon` varchar(50) DEFAULT NULL,
  `sort` char(4) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=332 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_menu

INSERT INTO `ysk_menu` VALUES('1','系统','0','0','','','','0','fa-cog','0','1');
INSERT INTO `ysk_menu` VALUES('3','统统功能','1','1','','','','1','fa-folder-open-o','1','1');
INSERT INTO `ysk_menu` VALUES('4','系统配置','3','1','Config','group','','2','fa-wrench','11','1');
INSERT INTO `ysk_menu` VALUES('5','角色管理','3','1','Group','index','','2','fa-sitemap','12','1');
INSERT INTO `ysk_menu` VALUES('6','管理员管理','3','1','Manage','index','','2','fa fa-lock','13','1');
INSERT INTO `ysk_menu` VALUES('7','会员管理','1','1','','','','1','fa-folder-open-o','2','1');
INSERT INTO `ysk_menu` VALUES('8','会员列表','7','1','User','index','','2','fa-user','21','1');
INSERT INTO `ysk_menu` VALUES('9','推荐结构','7','1','Tree','index','','2','fa-th-large','22','1');
INSERT INTO `ysk_menu` VALUES('10','商城','0','0','','','','0','fa-tasks','0','1');
INSERT INTO `ysk_menu` VALUES('11','商品管理','10','10','','','','1','fa-folder-open-o','3','1');
INSERT INTO `ysk_menu` VALUES('315','商品列表','11','10','Good','index','','2','','31','1');
INSERT INTO `ysk_menu` VALUES('316','帮助管理','1','1','','','','1','fa-folder-open-o','3','1');
INSERT INTO `ysk_menu` VALUES('317','提供帮助','316','1','Help','GiveHelp','','2','fa-file','31','1');
INSERT INTO `ysk_menu` VALUES('318','接受帮助','316','1','Help','ReceiptHelp','','2','fa-file-text','32','1');
INSERT INTO `ysk_menu` VALUES('319','财务管理','1','1','','','','1','fa-folder-open-o','4','1');
INSERT INTO `ysk_menu` VALUES('320','排单码','319','1','BuyNum','index','','2','fa-jpy','41','1');
INSERT INTO `ysk_menu` VALUES('321','激活码','319','1','ActivateNum','index','','2','fa-list','42','1');
INSERT INTO `ysk_menu` VALUES('322','系统公告','1','1','','','','1','fa-folder-open-o','5','1');
INSERT INTO `ysk_menu` VALUES('323','公告管理','322','1','News','index','','2','fa-twitter-square','51','1');
INSERT INTO `ysk_menu` VALUES('324','交易管理','1','1','','','','1','fa-folder-open-o','6','1');
INSERT INTO `ysk_menu` VALUES('325','交易明细','324','1','Traing','index','','2','fa-list','61','1');
INSERT INTO `ysk_menu` VALUES('326','冻结明细','324','1','Traing','freezedetail','','2','fa-list','62','1');
INSERT INTO `ysk_menu` VALUES('327','数据库管理','3','1','Database','index','','2','fa fa-lock','14','1');
INSERT INTO `ysk_menu` VALUES('328','转盘记录','324','1','Traing','turntable','','2','fa-list','64','0');
INSERT INTO `ysk_menu` VALUES('330','广告图','3','1','Banner','index','','2','fa-image','16','1');
INSERT INTO `ysk_menu` VALUES('331','订单列表','316','1','Order','index','','2','fa-file-text','33','1');
--
-- 表的结构ysk_money_detail
--
DROP TABLE IF EXISTS `ysk_money_detail`;
CREATE TABLE `ysk_money_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '明细类型',
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `account` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户',
  `username` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `content` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '明细说明',
  `money` text COLLATE utf8_unicode_ci NOT NULL COMMENT '金额明细',
  `datetime` int(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=196 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 ysk_money_detail

--
-- 表的结构ysk_money_freeze
--
DROP TABLE IF EXISTS `ysk_money_freeze`;
CREATE TABLE `ysk_money_freeze` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `account` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户',
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '类型',
  `count_money` decimal(10,2) NOT NULL COMMENT '总金额',
  `money` decimal(10,2) NOT NULL COMMENT '获得金额',
  `content` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT '说明',
  `a_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联提供ID',
  `r_id` int(10) NOT NULL DEFAULT '0' COMMENT '关联接受ID',
  `datetime` int(10) NOT NULL DEFAULT '0' COMMENT '时间',
  `endtime` int(10) NOT NULL DEFAULT '0' COMMENT '发放时间',
  `isok` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发放 0-未发放 1-已发放',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=136 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 ysk_money_freeze

--
-- 表的结构ysk_news
--
DROP TABLE IF EXISTS `ysk_news`;
CREATE TABLE `ysk_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `uid_str` text COMMENT '已查看的用户ID',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_news

INSERT INTO `ysk_news` VALUES('4','周周囍上线公告','&lt;p&gt;\r\n	机缘，在合作中生根，情谊，在合作中加深；事业在合作中壮大；梦想，在合作中腾飞，\r\n	&lt;p class=&quot;MsoNormal&quot;&gt;\r\n		&lt;span&gt;周周&lt;/span&gt;&lt;span&gt;囍&lt;/span&gt;\r\n	&lt;/p&gt;\r\n于九月八日正式扬帆启航\r\n&lt;/p&gt;','1500346692','','1');
--
-- 表的结构ysk_nzbill
--
DROP TABLE IF EXISTS `ysk_nzbill`;
CREATE TABLE `ysk_nzbill` (
  `bill_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '明细id',
  `bill_uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `bill_num` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '财富币',
  `bill_reason` char(20) NOT NULL COMMENT '生成的原因',
  `bill_time` int(11) NOT NULL DEFAULT '0' COMMENT '生成时间',
  `bill_name` varchar(50) DEFAULT NULL,
  `bill_type` char(1) NOT NULL COMMENT '0-扣除 1-获得',
  `bill_username` varchar(20) DEFAULT NULL,
  `bill_account` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`bill_id`),
  KEY `bill_userid` (`bill_uid`)
) ENGINE=MyISAM AUTO_INCREMENT=169 DEFAULT CHARSET=utf8 COMMENT='转盘抽奖';

--
-- 转存表中的数据 ysk_nzbill

--
-- 表的结构ysk_order
--
DROP TABLE IF EXISTS `ysk_order`;
CREATE TABLE `ysk_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `a_id` int(10) NOT NULL DEFAULT '0' COMMENT '提供ID',
  `r_id` int(10) NOT NULL DEFAULT '0' COMMENT '接受ID',
  `a_uid` mediumint(8) NOT NULL COMMENT '提供用户ID',
  `a_account` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '提供用户',
  `a_push_username` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '提供用户的推荐人姓名',
  `a_push_mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '提供用户的推荐人手机',
  `a_username` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '提供用户姓名',
  `r_uid` mediumint(8) NOT NULL COMMENT '接受用户ID',
  `r_account` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '接受用户',
  `r_username` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '接受用户姓名',
  `r_push_username` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '接受用户的推荐人姓名',
  `r_push_mobile` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '接受用户的推荐人手机',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `play_pic` varchar(60) COLLATE utf8_unicode_ci NOT NULL COMMENT '上传凭证',
  `play_datetime` int(10) NOT NULL DEFAULT '0' COMMENT '打款时间',
  `receipt_datetime` int(10) NOT NULL DEFAULT '0' COMMENT '收米时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-匹配成功 1-确认打米 2-确认收米',
  `datetime` int(10) NOT NULL DEFAULT '0' COMMENT '匹配时间',
  `is_push` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否甩单',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 ysk_order

--
-- 表的结构ysk_orderbuy
--
DROP TABLE IF EXISTS `ysk_orderbuy`;
CREATE TABLE `ysk_orderbuy` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `account` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `pay_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '支付类型',
  `username` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `datetime` int(10) NOT NULL COMMENT '时间',
  `is_push` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否甩单',
  `match_datetime` int(10) NOT NULL DEFAULT '0' COMMENT '匹配时间',
  `datestr` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提供帮助';

--
-- 转存表中的数据 ysk_orderbuy

--
-- 表的结构ysk_orderreceipt
--
DROP TABLE IF EXISTS `ysk_orderreceipt`;
CREATE TABLE `ysk_orderreceipt` (
  `id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `account` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户',
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL COMMENT '姓名',
  `money` decimal(10,2) NOT NULL COMMENT '金额',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '提现类型，1一般积分，2收益积分，3推广积分',
  `receipt_type` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0' COMMENT '收款类型',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态',
  `datetime` int(10) NOT NULL COMMENT '时间',
  `datastr` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '每天抢单时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- 转存表中的数据 ysk_orderreceipt

--
-- 表的结构ysk_store
--
DROP TABLE IF EXISTS `ysk_store`;
CREATE TABLE `ysk_store` (
  `uid` int(11) NOT NULL COMMENT '用户id',
  `common_num` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '一般积分',
  `income_num` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '收益积分',
  `recommen_num` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '推广积分',
  `fee_num` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '消费积分',
  `hashiqi_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '狗的数量',
  `wealth_num` decimal(11,1) unsigned NOT NULL DEFAULT '0.0' COMMENT '财富值',
  `recommen_total` int(13) unsigned NOT NULL DEFAULT '0' COMMENT '直推业绩',
  `total_num` decimal(13,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '总水果数',
  `sign_time` varchar(20) NOT NULL DEFAULT '' COMMENT '每天签到时间，用于好友采矿判断',
  `caimi_fids` varchar(255) NOT NULL DEFAULT '' COMMENT '已采蜜的好友ID',
  `activate_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '激活码',
  `buy_num` int(11) unsigned NOT NULL COMMENT '抢单币',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 ysk_store

INSERT INTO `ysk_store` VALUES('1','0.00','0.00','0.00','0.00','0','0.0','0','0.00','','','0','0');
--
-- 表的结构ysk_upload
--
DROP TABLE IF EXISTS `ysk_upload`;
CREATE TABLE `ysk_upload` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'UID',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '文件名',
  `path` varchar(255) NOT NULL DEFAULT '' COMMENT '文件路径',
  `url` varchar(255) DEFAULT NULL COMMENT '文件链接',
  `ext` char(4) NOT NULL DEFAULT '' COMMENT '文件类型',
  `size` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '文件大小',
  `md5` char(32) DEFAULT NULL COMMENT '文件md5',
  `sha1` char(40) DEFAULT NULL COMMENT '文件sha1编码',
  `location` varchar(15) NOT NULL DEFAULT '' COMMENT '文件存储位置',
  `download` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '修改时间',
  `sort` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='文件上传表';

--
-- 转存表中的数据 ysk_upload

INSERT INTO `ysk_upload` VALUES('1','1','timg.jpg','/Uploads/2017-06-26/59512f0caefc4.jpg','','jpg','243539','005b06b776163bbf8531f59ed88dbc2d','2f899e35ab1ec68ffda01941f66657641759e158','Local','0','1498492684','1498492684','0','1');
INSERT INTO `ysk_upload` VALUES('2','1','Screenshot_2010-01-01-08-10-02.png','/Uploads/2017-06-29/595521e77182b.png','','png','55985','526705376a40401857ec39e65ed5c781','b4f4f7516da58301483361977f5fd362a28b249a','Local','0','1498751463','1498751463','0','1');
INSERT INTO `ysk_upload` VALUES('3','1','Screenshot_2010-01-01-08-09-40.png','/Uploads/2017-06-30/59564d3a4b7a6.png','','png','57524','ef4357fe5a5ca800afc985cf3f2edb85','72686f4ded9ddf9cce5edc3ee3cb7d06dcb1ce2e','Local','0','1498828090','1498828090','0','1');
INSERT INTO `ysk_upload` VALUES('4','1','good3.jpg','/Uploads/2017-06-30/59564e3d3940c.jpg','','jpg','38499','a4b068c8bee0f4efcec0f84e29f92105','832b50db5a2daccb7d6e93d7c5d03aecc0a6a644','Local','0','1498828349','1498828349','0','1');
INSERT INTO `ysk_upload` VALUES('5','1','good2.jpg','/Uploads/2017-06-30/59564e5a85064.jpg','','jpg','56354','dd634eee875ab5b7e30d5e5259fc3f9a','4f6c762fc91ff97da32319429def7a0f01e60b6c','Local','0','1498828378','1498828378','0','1');
INSERT INTO `ysk_upload` VALUES('6','1','good4.jpg','/Uploads/2017-06-30/59564f0709300.jpg','','jpg','30787','385347f152e498ca15e299f3b063f806','3adf7efb943984b1048e9aa0e46501be89b87842','Local','0','1498828551','1498828551','0','1');
INSERT INTO `ysk_upload` VALUES('7','1','good1.jpg','/Uploads/2017-07-01/59577eb20a07c.jpg','','jpg','20766','209a5f3913858efc57ed21c14e9210a3','9f3e6f1383058238a628eca9992ebf4524595d7b','Local','0','1498906290','1498906290','0','1');
INSERT INTO `ysk_upload` VALUES('8','1','87696982296211330.png','/Uploads/2017-07-03/595a0cf55eafd.png','','png','164957','9343f5b8f9ab66659bbd3fd01d21981b','ef6ff3a487e259edef841849c06029997c1aeb83','Local','0','1499073781','1499073781','0','1');
INSERT INTO `ysk_upload` VALUES('9','1','upload.jpg','/Uploads/2017-07-18/596ce161e1113.jpg','','jpg','4332','f21dc4228033bd96b8db3a2b62c6e3f9','6b8d09afa3d6361d0351cdd21d2c8f5dab3eeba3','Local','0','1500307809','1500307809','0','1');
INSERT INTO `ysk_upload` VALUES('10','1','che1.png','/Uploads/2017-07-27/5979c924002e2.png','','png','79287','719c645ffc3345ebe975657aed2906e9','419c8c1833831eec8462523fe2956ec2d7d6db57','Local','0','1501153572','1501153572','0','1');
INSERT INTO `ysk_upload` VALUES('11','1','banner.jpg','/Uploads/2017-07-27/5979cfa66d24d.jpg','','jpg','123417','2f8d188ba10bd00a1ef0ba17c87e5894','93f9e792af3fbe37de12f487687ff89ef211f7d4','Local','0','1501155238','1501155238','0','1');
INSERT INTO `ysk_upload` VALUES('12','1','banner01.jpg','/Uploads/2017-07-27/5979d0ba8022c.jpg','','jpg','148019','07db0acce770c294808cb24b5ab64f50','9d9b35078277acbf64e7193ee7b4a59b9c4cf4a2','Local','0','1501155514','1501155514','0','1');
INSERT INTO `ysk_upload` VALUES('13','1','p_big2.jpg','/Uploads/2017-08-29/59a4f7b550143.jpg','','jpg','451228','22508d7389277290584be5a09c16853e','98bc7daafb617f6bab17925dd3a03776ad0674d6','Local','0','1503983541','1503983541','0','1');
INSERT INTO `ysk_upload` VALUES('14','1','banner01.jpg','/Uploads/2017-09-01/59a8fa5962658.jpg','','jpg','167512','044e81e9e3e99e23c984ed38e20794f3','272a1c4ec1c9f6c519fff151121e609525df827d','Local','0','1504246361','1504246361','0','1');
INSERT INTO `ysk_upload` VALUES('15','1','banner02.jpg','/Uploads/2017-09-01/59a8fa674f420.jpg','','jpg','105110','03a8a85a9ff0374d6b48434cfe69296c','1134de42665657264342e05ada6f9f4d69963e1e','Local','0','1504246375','1504246375','0','1');
--
-- 表的结构ysk_user
--
DROP TABLE IF EXISTS `ysk_user`;
CREATE TABLE `ysk_user` (
  `userid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) unsigned NOT NULL COMMENT '上级ID',
  `account` char(20) CHARACTER SET utf8 NOT NULL DEFAULT '0' COMMENT '用户账号',
  `mobile` char(20) CHARACTER SET utf8 NOT NULL COMMENT '用户手机号',
  `username` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `safety_pwd` char(32) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL DEFAULT '' COMMENT '安全密码',
  `safety_salt` char(5) NOT NULL,
  `login_pwd` varchar(32) NOT NULL DEFAULT '',
  `login_salt` char(3) NOT NULL DEFAULT '',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-女 1-男',
  `reg_date` int(11) NOT NULL COMMENT '注册时间',
  `reg_ip` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT '注册IP',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '用户锁定  1 不锁  0拉黑  -1 删除',
  `activate` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否激活 1-已激活 0-未激活 2-账号已完善',
  `path` text CHARACTER SET utf8 NOT NULL COMMENT 'id拼接路径',
  `deep` int(11) NOT NULL DEFAULT '0' COMMENT '深度',
  `head_img` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '头像',
  `session_id` varchar(225) DEFAULT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户等级 0- 普通用户 1-普通监工 2-中级监工 3-高级监工 4-金牌监工',
  `sign_week` int(11) NOT NULL DEFAULT '0' COMMENT '用于每周签到满7天显示提示',
  `idcard` varchar(20) CHARACTER SET utf8 DEFAULT NULL COMMENT '身份证',
  `zhifubao` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `weixin` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `bank_name` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `bank_no` varchar(20) DEFAULT NULL,
  `bank_username` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `order_time` int(11) NOT NULL COMMENT '最后一次排单时间',
  `paidang_time` int(11) NOT NULL DEFAULT '0' COMMENT '拍单时间',
  PRIMARY KEY (`userid`),
  UNIQUE KEY `mobile` (`mobile`),
  UNIQUE KEY `account` (`account`) USING BTREE,
  KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=769 DEFAULT CHARSET=latin1;

--
-- 转存表中的数据 ysk_user

INSERT INTO `ysk_user` VALUES('1','0','13611111111','13611111111','总账号','','382428f9c444367774286359b869bc73','b19','382428f9c444367774286359b869bc73','b19','0','1502170787','127.0.0.1','1','1','','1','/Public/home/images/tx.jpg','408d6u0adgts4j5f26fhimj753','0','0','','','','','','','1507840694','1507840694');
