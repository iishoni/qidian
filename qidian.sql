-- phpMyAdmin SQL Dump
-- version 4.0.6-rc1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2017-11-11 18:29:49
-- 服务器版本: 5.6.14
-- PHP 版本: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `hzxx1109_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `ysk_activate_num`
--

CREATE TABLE IF NOT EXISTS `ysk_activate_num` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_admin`
--

CREATE TABLE IF NOT EXISTS `ysk_admin` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户账号表' AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `ysk_admin`
--

INSERT INTO `ysk_admin` (`id`, `auth_id`, `nickname`, `username`, `password`, `mobile`, `reg_ip`, `create_time`, `update_time`, `status`, `reg_type`) VALUES
(1, 1, '超级管理员', 'admin', '8f3bd6b4d00391c9d09cc14e32fee28c', ' ', 0, 1438651748, 1510286077, 1, '');

-- --------------------------------------------------------

--
-- 表的结构 `ysk_admin_zhuangz`
--

CREATE TABLE IF NOT EXISTS `ysk_admin_zhuangz` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_banner`
--

CREATE TABLE IF NOT EXISTS `ysk_banner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '管告图',
  `banner_name` varchar(255) DEFAULT NULL,
  `banner_type` varchar(4) NOT NULL DEFAULT '' COMMENT '广告图类型',
  `banner_link` varchar(255) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `create_time` int(11) DEFAULT NULL,
  `sort` char(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `banner_img` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `ysk_banner`
--

INSERT INTO `ysk_banner` (`id`, `banner_name`, `banner_type`, `banner_link`, `status`, `create_time`, `sort`, `banner_img`) VALUES
(3, '3434', '1', '', 1, NULL, '2', '14'),
(7, 'rere', '1', '', 1, 1501155469, '10', '15');

-- --------------------------------------------------------

--
-- 表的结构 `ysk_buy_num_detail`
--

CREATE TABLE IF NOT EXISTS `ysk_buy_num_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `mobile` varchar(20) NOT NULL,
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '转账数量',
  `to_id` int(11) NOT NULL DEFAULT '0',
  `to_mobile` varchar(20) NOT NULL,
  `datetime` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL COMMENT '0-获得 1-转出 2-抢单',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_config`
--

CREATE TABLE IF NOT EXISTS `ysk_config` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='系统配置表' AUTO_INCREMENT=26 ;

--
-- 转存表中的数据 `ysk_config`
--

INSERT INTO `ysk_config` (`id`, `title`, `name`, `value`, `group`, `type`, `options`, `tip`, `create_time`, `update_time`, `sort`, `status`) VALUES
(1, '站点开关', 'TOGGLE_WEB_SITE', '1', 3, '0', '0:关闭\r\n1:开启', '系统升级，暂时关闭网站', 1378898976, 1406992386, 1, 1),
(2, '网站标题', 'WEB_SITE_TITLE', '', 1, '0', '', '网站标题前台显示标题', 1378898976, 1379235274, 2, 1),
(3, '网站LOGO', 'WEB_SITE_LOGO', '0', 1, '0', '', '网站LOGO', 1407003397, 1407004692, 3, 1),
(4, '网站描述', 'WEB_SITE_DESCRIPTION', '', 1, '0', '', '网站搜索引擎描述', 1378898976, 1379235841, 4, 1),
(5, '网站关键字', 'WEB_SITE_KEYWORD', '', 1, '0', '', '网站搜索引擎关键字', 1378898976, 1381390100, 5, 1),
(6, '版权信息', 'WEB_SITE_COPYRIGHT', '', 1, '0', '', '设置在网站底部显示的版权信息，如“版权所有 © 2014-2015 科斯克网络科技”', 1406991855, 1406992583, 6, 1),
(7, '网站备案号', 'WEB_SITE_ICP', '', 1, '0', '', '设置在网站底部显示的备案号，如“苏ICP备1502009号"', 1378900335, 1415983236, 9, 1),
(8, '普通矿车拆分', 'CAR_COMMON_FEE', '5', 2, '0', '', '', 0, 0, 20, 1),
(9, '银矿车拆分', 'CAR_SILVER_FEE', '6', 2, '0', '', '', 0, 0, 21, 1),
(10, '普通矿车费用', 'CAR_COMMON_NUM', '500', 2, '0', '', '', 0, 0, 21, 1),
(11, '定向交易手续费', 'TARGET_FEE', '5', 4, '0', '', '', 0, 0, 30, 1),
(12, '自由交易手续费', 'FREE_FEE', '10', 4, '0', '', '', 0, 0, 31, 1),
(13, '平台回收手续费', 'BACK_FEE', '20', 4, '0', '', '', 0, 0, 31, 1),
(14, '银矿车费用', 'CAR_SILVER_NUM', '1000', 2, '', '', '', 0, 0, 23, 1),
(15, '购买手续费', 'REG_FEE', '25', 2, '', '', '', 0, 0, 0, 1),
(16, '平台回收限制', 'BACK_LIMIT', '30', 4, '', '', '', 0, 0, 33, 1),
(17, '普通监工/直推10人', 'LEVEL1', '0.1', 2, '', '', '', 0, 0, 0, 1),
(18, '中级监工/直推20人', 'LEVEL2', '0.2', 2, '', '', '', 0, 0, 0, 1),
(19, '高级监工/直推40人', 'LEVEL3', '0.4', 2, '', '', '', 0, 0, 0, 1),
(20, '金牌监工/直推70人', 'LEVEL4', '0.6', 2, '', '', '', 0, 0, 0, 1),
(22, '金矿车费用', 'CAR_GOLD_NUM', '2000', 2, '', '', '', 0, 0, 0, 1),
(23, '金矿车拆分', 'CAR_GOLD_FEE', '8', 2, '', '', '', 0, 0, 0, 1),
(24, '一级好友采矿拆分', 'FRIENDS_ONE', '8', 2, '', '', '', 0, 0, 0, 1),
(25, '二级好友采矿拆分', 'FRIENDS_TWO', '5', 2, '', '', '', 0, 0, 0, 1);

-- --------------------------------------------------------

--
-- 表的结构 `ysk_daywork`
--

CREATE TABLE IF NOT EXISTS `ysk_daywork` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `money` int(11) NOT NULL DEFAULT '0' COMMENT '金额',
  `datestr` varchar(20) NOT NULL DEFAULT '' COMMENT '一年中的第几周',
  `num` int(11) NOT NULL DEFAULT '0' COMMENT '当天已抢数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `ysk_daywork`
--

INSERT INTO `ysk_daywork` (`id`, `money`, `datestr`, `num`) VALUES
(1, 1000, '20171111', 3),
(2, 2000, '20171111', 2),
(3, 3000, '20171111', 0),
(4, 5000, '20171111', 5),
(5, 10000, '20171111', 6);

-- --------------------------------------------------------

--
-- 表的结构 `ysk_group`
--

CREATE TABLE IF NOT EXISTS `ysk_group` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='部门信息表' AUTO_INCREMENT=8 ;

--
-- 转存表中的数据 `ysk_group`
--

INSERT INTO `ysk_group` (`id`, `pid`, `title`, `icon`, `menu_auth`, `create_time`, `update_time`, `sort`, `status`, `auth_id`) VALUES
(1, 0, '超级管理员', '', '', 1426881003, 1427552428, 0, 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `ysk_menu`
--

CREATE TABLE IF NOT EXISTS `ysk_menu` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=332 ;

--
-- 转存表中的数据 `ysk_menu`
--

INSERT INTO `ysk_menu` (`id`, `name`, `pid`, `gid`, `col`, `act`, `patch`, `level`, `icon`, `sort`, `status`) VALUES
(1, '系统', 0, 0, '', '', NULL, 0, 'fa-cog', '0', 1),
(3, '统统功能', 1, 1, '', '', '', 1, 'fa-folder-open-o', '1', 1),
(4, '系统配置', 3, 1, 'Config', 'group', '', 2, 'fa-wrench', '11', 1),
(5, '角色管理', 3, 1, 'Group', 'index', '', 2, 'fa-sitemap', '12', 1),
(6, '管理员管理', 3, 1, 'Manage', 'index', '', 2, 'fa fa-lock', '13', 1),
(7, '会员管理', 1, 1, '', '', '', 1, 'fa-folder-open-o', '2', 1),
(8, '会员列表', 7, 1, 'User', 'index', NULL, 2, 'fa-user', '21', 1),
(9, '推荐结构', 7, 1, 'Tree', 'index', NULL, 2, 'fa-th-large', '22', 1),
(10, '商城', 0, 0, '', '', NULL, 0, 'fa-tasks', '0', 1),
(11, '商品管理', 10, 10, '', '', NULL, 1, 'fa-folder-open-o', '3', 1),
(315, '商品列表', 11, 10, 'Good', 'index', NULL, 2, NULL, '31', 1),
(316, '帮助管理', 1, 1, '', '', NULL, 1, 'fa-folder-open-o', '3', 1),
(317, '提供帮助', 316, 1, 'Help', 'GiveHelp', '', 2, 'fa-file', '31', 1),
(318, '接受帮助', 316, 1, 'Help', 'ReceiptHelp', NULL, 2, 'fa-file-text', '32', 1),
(319, '财务管理', 1, 1, '', '', NULL, 1, 'fa-folder-open-o', '4', 1),
(320, '排单码', 319, 1, 'BuyNum', 'index', NULL, 2, 'fa-jpy', '41', 1),
(321, '激活码', 319, 1, 'ActivateNum', 'index', NULL, 2, 'fa-list', '42', 1),
(322, '系统公告', 1, 1, '', '', NULL, 1, 'fa-folder-open-o', '5', 1),
(323, '公告管理', 322, 1, 'News', 'index', NULL, 2, 'fa-twitter-square', '51', 1),
(324, '交易管理', 1, 1, '', '', '', 1, 'fa-folder-open-o', '6', 1),
(325, '交易明细', 324, 1, 'Traing', 'index', '', 2, 'fa-list', '61', 1),
(326, '冻结明细', 324, 1, 'Traing', 'freezedetail', NULL, 2, 'fa-list', '62', 1),
(327, '数据库管理', 3, 1, 'Database', 'index', NULL, 2, 'fa fa-lock', '14', 1),
(328, '转盘记录', 324, 1, 'Traing', 'turntable', '', 2, 'fa-list', '64', 0),
(330, '广告图', 3, 1, 'Banner', 'index', NULL, 2, 'fa-image', '16', 1),
(331, '订单列表', 316, 1, 'Order', 'index', '', 2, 'fa-file-text', '33', 1);

-- --------------------------------------------------------

--
-- 表的结构 `ysk_money_detail`
--

CREATE TABLE IF NOT EXISTS `ysk_money_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '明细类型',
  `uid` mediumint(8) NOT NULL COMMENT '用户ID',
  `account` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户',
  `username` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '姓名',
  `content` varchar(30) COLLATE utf8_unicode_ci NOT NULL COMMENT '明细说明',
  `money` text COLLATE utf8_unicode_ci NOT NULL COMMENT '金额明细',
  `datetime` int(10) NOT NULL COMMENT '时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_money_freeze`
--

CREATE TABLE IF NOT EXISTS `ysk_money_freeze` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_news`
--

CREATE TABLE IF NOT EXISTS `ysk_news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  `content` text NOT NULL,
  `create_time` int(11) NOT NULL,
  `uid_str` text COMMENT '已查看的用户ID',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `ysk_news`
--

INSERT INTO `ysk_news` (`id`, `title`, `content`, `create_time`, `uid_str`, `status`) VALUES
(4, '起点上线平台公告起点上线平台公告', '&lt;p&gt;\r\n	机缘，在合作中生根，情谊，在合作中加深；事业在合作中壮大；梦想，在合作中腾飞，\r\n&lt;/p&gt;\r\n&lt;p class=&quot;MsoNormal&quot;&gt;\r\n	起点互助\r\n&lt;/p&gt;\r\n于11月1日正式扬帆启航\r\n&lt;p&gt;\r\n	&lt;br /&gt;\r\n&lt;/p&gt;', 1500346692, '', 1);

-- --------------------------------------------------------

--
-- 表的结构 `ysk_nzbill`
--

CREATE TABLE IF NOT EXISTS `ysk_nzbill` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='转盘抽奖' AUTO_INCREMENT=169 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_order`
--

CREATE TABLE IF NOT EXISTS `ysk_order` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_orderbuy`
--

CREATE TABLE IF NOT EXISTS `ysk_orderbuy` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='提供帮助' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_orderreceipt`
--

CREATE TABLE IF NOT EXISTS `ysk_orderreceipt` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_store`
--

CREATE TABLE IF NOT EXISTS `ysk_store` (
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

-- --------------------------------------------------------

--
-- 表的结构 `ysk_upload`
--

CREATE TABLE IF NOT EXISTS `ysk_upload` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文件上传表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `ysk_user`
--

CREATE TABLE IF NOT EXISTS `ysk_user` (
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
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=925 ;

--
-- 转存表中的数据 `ysk_user`
--

INSERT INTO `ysk_user` (`userid`, `pid`, `account`, `mobile`, `username`, `email`, `safety_pwd`, `safety_salt`, `login_pwd`, `login_salt`, `sex`, `reg_date`, `reg_ip`, `status`, `activate`, `path`, `deep`, `head_img`, `session_id`, `level`, `sign_week`, `idcard`, `zhifubao`, `weixin`, `bank_name`, `bank_no`, `bank_username`, `order_time`, `paidang_time`) VALUES
(1, 0, '13611111111', '13611111111', '总账号', '', '382428f9c444367774286359b869bc73', 'b19', '382428f9c444367774286359b869bc73', 'b19', 0, 1502170787, '127.0.0.1', 1, 1, '', 1, '/Public/home/images/tx.jpg', 'a4470tj7v0dknjvs5pu87539g1', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1510305015, 1510305015);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
