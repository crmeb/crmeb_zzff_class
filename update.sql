
--
-- 配置分类充值配置
--
INSERT INTO `eb_system_config_tab`(`id`, `title`, `eng_title`, `status`, `info`, `icon`, `type`) VALUES (23, '充值金币', 'recharge', 1, 0, 'jpy', 7);
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (176, 'gold_rate', 'text', 'input', 23, NULL, NULL, 'number:rue', 100, NULL, '\"10\"', '人民币与金币换算率', '充值人民币和金币的换算概率，默认：1元人民币=10系统虚拟金币', 0, 1);
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (177, 'gold_name', 'text', 'input', 23, NULL, NULL, NULL, 100, NULL, '\"\\u91d1\\u5e01\"', '虚拟货币名称', '虚拟货币名称（如，金币，水滴，鲜花等）', 0, 1);
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (178, 'gold_image', 'upload', 'input', 23, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/61d9d202006181439471781.png\"', '虚拟货币图标', '虚拟货币图标', 0, 1);
/*UPDATE `eb_system_config` SET `menu_name` = 'store_user_min_recharge', `type` = 'text', `input_type` = 'input', `config_tab_id` = 23, `parameter` = '', `upload_type` = 0, `required` = 'required:true,number:true,min:0', `width` = 100, `high` = 0, `value` = '\"0.01\"', `info` = '用户最低充值金额', `desc` = '用户单次最低充值金额', `sort` = 0, `status` = 1 WHERE `id` = 44;*/
ALTER TABLE `eb_user` ADD COLUMN `gold_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '虚拟币余额' AFTER `now_money`;
ALTER TABLE `eb_user_recharge` ADD COLUMN `gold_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '虚拟币余额' AFTER `price`;
ALTER TABLE `eb_system_admin` ADD COLUMN `phone` varchar(11) NOT NULL COMMENT '前端登录电话号码'AFTER `roles`;
UPDATE `eb_system_config` SET `config_tab_id` = 23 WHERE `id` = 44;
ALTER TABLE `eb_recommend_relation` MODIFY COLUMN `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型,0=专题,1=新闻，2=直播，3=活动';
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (179, 'single_gold_coin', 'text', 'input', 22, NULL, NULL, '', 100, NULL, '1', '单次签到虚拟币数', '每次签到用户可以获得的虚拟币数（默认为金币）', 0, 1);
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (180, 'sign_default_poster', 'upload', 'input', 22, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/20362202002201412303972.jpg\"', '签到海报', '签到默认海报（没有签到海报时默认显示此图）', 0, 1);
INSERT INTO `eb_system_config`(`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES (181, 'balance_switch', 'radio', 'input', 22, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '余额开关', '余额开关', 0, 1);
INSERT INTO `eb_system_role`(`id`, `role_name`, `sign`, `rules`, `level`, `status`) VALUES (3, '核销员', 'verification', '518,286', 1, 1);



--
-- 菜单
--
UPDATE `eb_system_menus` SET `params` = '{\"tab_id\":\"1\",\"type\":\"7\"}' WHERE `id` = 6;
UPDATE `eb_system_menus` SET `params` = '{\"tab_id\":\"22\",\"type\":\"6\"}' WHERE `id` = 508;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (512, 287, '', '充值配置', 'admin', 'setting.system_config', 'index', '{\"tab_id\":\"23\",\"type\":\"7\"}', 0, 1, 1);
UPDATE `eb_system_menus` SET `params` = '{\"tab_id\":\"1\",\"type\":\"999\"}' WHERE `id` = 6;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (513, 0, 'video-camera', '直播', 'admin', '', '', '[]', 298, 1, 1);
UPDATE `eb_system_menus` SET `pid` = 513, `menu_name` = '直播列表', `sort` = 292 WHERE `id` = 487;
/*INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (514, 513, '', '推荐课程', 'admin', 'live.aliyun_live', 'live_goods', '[]', 0, 1, 1);*/
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (515, 513, '', '直播间管理', 'admin', 'live.aliyun_live', 'index', '{\"special_type\":\"4\",\"type\":\"2\"}', 290, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (516, 513, '', '礼物管理', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"59\"}', 289, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (517, 513, '', '直播贡献', 'admin', 'live.aliyun_live', 'live_reward', '[]', 288, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (518, 307, '', '虚拟币监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"gold_num\"}', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (519, 286, '', '活动管理', 'admin', 'ump.eventRegistration', 'index', '[]', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (520, 485, '', '专题列表', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"3\"}', 1, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (521, 485, '', '素材列表', 'admin', 'special.SpecialType', 'source_index', '{\"special_type\":\"3\"}', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (522, 484, '', '专题列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"2\"}', 1, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (523, 484, '', '素材列表', 'admin', 'special.special_type', 'source_index', '{\"special_type\":\"2\"}', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (524, 483, '', '专题列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"1\"}', 1, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (525, 483, '', '素材列表', 'admin', 'special.special_type', 'source_index', '{\"special_type\":\"1\"}', 0, 1, 1);
INSERT INTO `eb_system_group`(`id`, `name`, `info`, `config_name`, `fields`) VALUES (59, '直播间礼物列表', '直播间礼物列表', 'live_gift', '[{\"name\":\"\\u793c\\u7269\\u540d\\u79f0\",\"title\":\"live_gift_name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u793c\\u7269\\u4ef7\\u683c\\uff08\\u865a\\u62df\\u8d27\\u5e01\\uff09\",\"title\":\"live_gift_price\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u8d60\\u9001\\u6570\\u91cf\\u5217\\u8868\",\"title\":\"live_gift_num\",\"type\":\"checkbox\",\"param\":\"1=1\\n5=5\\n10=10\\n20=20\\n66=66\\n99=99\\n520=520\\n999=999\\n1314=1314\"},{\"name\":\"\\u793c\\u7269\\u56fe\\u6807\",\"title\":\"live_gift_show_img\",\"type\":\"upload\",\"param\":\"\"}]');
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (170, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u9c9c\\u82b1\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"2\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"66\",\"520\",\"999\",\"1314\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/da616202007011009436251.png\"}}', 1590739724, 9, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (171, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7231\\u5fc3\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"1\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/5a2d520200701101025960.png\"}}', 1590740368, 10, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (172, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u6c14\\u7403\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"6\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\",\"999\",\"1314\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/0c648202007011012055342.png\"}}', 1593569535, 7, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (173, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7687\\u51a0\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"20\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/82d4c202007011013025359.png\"}}', 1593569588, 5, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (174, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u5956\\u676f\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"15\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/1caae202007011013442033.png\"}}', 1593569629, 6, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (175, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u9526\\u9ca4\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"30\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/226e5202007011015051008.png\"}}', 1593569720, 4, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (176, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u5609\\u5e74\\u534e\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"50\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/22c8e202007011017537720.png\"}}', 1593569880, 3, 1);
INSERT INTO `eb_system_group_data`(`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES (177, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7c89\\u7b14\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"5\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/96ea420200701101917287.png\"}}', 1593569968, 8, 1);
UPDATE `eb_system_menus` SET `pid` = 307, `icon` = '', `menu_name` = '资金监控', `module` = 'admin', `controller` = 'finance.finance', `action` = 'bill', `params` = '{\"category\":\"now_money\"}', `sort` = 0, `is_show` = 1, `access` = 1 WHERE `id` = 312;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (526, 417, '', '新闻分类', 'admin', 'article.article_category', 'index', '[]', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (527, 286, '', '签到管理', 'admin', '', '', '[]', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (528, 527, '', '签到海报', 'admin', 'user.signPoster', 'index', '[]', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (529, 527, '', '签到记录', 'admin', 'user.userSign', 'index', '[]', 0, 1, 1);

--
-- 新增表
--

CREATE TABLE `eb_live_reward` (
  `id` int(11) NOT NULL,
  `live_id` int(11) unsigned NOT NULL COMMENT '直播间id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `nickname` varchar(100) CHARACTER SET utf8mb4 NOT NULL COMMENT '昵称',
  `gift_id` int(11) NOT NULL COMMENT '礼物id',
  `gift_name` varchar(100) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '礼物名称',
  `gift_price` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '虚拟货币价格',
  `gift_num` int(11) NOT NULL DEFAULT '0' COMMENT '礼物数量',
  `total_price` int(11) NOT NULL DEFAULT '0' COMMENT '总虚拟货币价格',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '打赏时间',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示 1= 显示，0=隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播间礼物打赏表';

ALTER TABLE `eb_live_reward`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE;

ALTER TABLE `eb_live_reward`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_live_goods` (
  `id` int(11) NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `special_name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` int(10) NOT NULL DEFAULT '0' COMMENT '销量',
  `fake_sales` int(10) NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(11) unsigned NOT NULL COMMENT '添加时间',
  `update_time` int(11) NOT NULL COMMENT '修改时间'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='直播间带货表';

ALTER TABLE `eb_live_goods`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_live_goods`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_event_registration` (
  `id` int(10) NOT NULL,
  `title` varchar(255) NOT NULL COMMENT '标题',
  `phone` varchar(12) NOT NULL COMMENT '电话',
  `image` varchar(255) NOT NULL COMMENT '封面图',
  `signup_start_time` int(50) NOT NULL COMMENT '报名开始时间',
  `signup_end_time` int(50) NOT NULL COMMENT '报名结束时间',
  `start_time` int(50) NOT NULL COMMENT '活动开始时间',
  `end_time` int(50) NOT NULL COMMENT '活动结束时间',
  `province` varchar(255) NOT NULL COMMENT '省',
  `city` varchar(255) NOT NULL COMMENT '市',
  `district` varchar(255) NOT NULL COMMENT '区',
  `detail` varchar(255) NOT NULL COMMENT '详细地址',
  `latitude` varchar(255) NOT NULL COMMENT '纬度',
  `longitude` varchar(255) NOT NULL COMMENT '经度',
  `number` int(10) NOT NULL DEFAULT '0' COMMENT '人数',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序',
  `activity_rules` varchar(3000) DEFAULT NULL COMMENT '活动规则',
  `content` varchar(5000) DEFAULT NULL COMMENT '活动详情',
  `pay_type` int(2) NOT NULL DEFAULT '0' COMMENT '是否免费',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '价格',
  `member_pay_type` tinyint(2) NOT NULL COMMENT '会员支付状态（0:免费1:付费）',
  `member_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `write_off_code` varchar(255) NOT NULL COMMENT '核销码',
  `restrictions` tinyint(2) NOT NULL DEFAULT '0' COMMENT '限购',
  `is_fill` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否填写资料',
  `qrcode_img` varchar(255) DEFAULT NULL COMMENT '群聊二维码',
  `is_show` tinyint(2) NOT NULL COMMENT '是否显示',
  `is_del` int(2) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(50) NOT NULL DEFAULT '1' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='活动报名表';

ALTER TABLE `eb_event_registration`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_event_registration`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_event_sign_up` (
  `id` int(10) NOT NULL,
  `order_id` varchar(64) NOT NULL COMMENT '订单号',
  `trade_no` varchar(64) NOT NULL COMMENT '支付宝支付,支付宝交易订单号',
  `uid` int(10) NOT NULL,
  `user_info` varchar(3072) NOT NULL COMMENT '报名信息',
  `activity_id` int(10) NOT NULL DEFAULT '0' COMMENT '活动ID',
  `pay_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '实际支付价格',
  `paid` int(2) NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(50) DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(20) NOT NULL DEFAULT '0' COMMENT '支付方式',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '状态（0:未核销1:已核销）',
  `write_off_code` varchar(255) DEFAULT NULL COMMENT '核销二维码',
  `is_del` tinyint(2) NOT NULL,
  `add_time` int(50) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户报名表';

ALTER TABLE `eb_event_sign_up`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_event_sign_up`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_user_sign` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '签到说明',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '获得金币',
  `balance` int(11) NOT NULL DEFAULT '0' COMMENT '剩余金币',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到记录表';

ALTER TABLE `eb_user_sign`
 ADD PRIMARY KEY (`id`),
ADD KEY `uid` (`uid`) USING BTREE;

ALTER TABLE `eb_user_sign`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_sign_poster` (
  `id` int(10) NOT NULL,
  `sign_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `poster` varchar(255) DEFAULT NULL COMMENT '分享海报',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到海报';

ALTER TABLE `eb_sign_poster`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_sign_poster`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_search_history` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL COMMENT '搜索内容',
  `add_time` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历史搜索';

ALTER TABLE `eb_search_history`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_search_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

CREATE TABLE `eb_special_watch` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT 'uid',
  `special_id` int(11) NOT NULL COMMENT '专题ID',
  `task_id` int(11) NOT NULL COMMENT '素材ID',
  `viewing_time` int(50) DEFAULT '0' COMMENT '观看时间',
  `add_time` int(50) DEFAULT '0' COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户观看素材时间';

ALTER TABLE `eb_special_watch`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `eb_special_watch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;