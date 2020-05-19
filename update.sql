
--
-- 新增表字段
--

ALTER TABLE `eb_special` ADD COLUMN `type` tinyint(2) NOT NULL DEFAULT '6' COMMENT '专题类型1：图文专题；2：音频专题；3：视频专题；4：直播专题；5：专栏；6：其他专题' AFTER `abstract`;
ALTER TABLE `eb_special` ADD COLUMN `member_pay_type` tinyint(2) NOT NULL DEFAULT '1' COMMENT '会员支付类型0：免费，1付费' AFTER `pay_type`;
ALTER TABLE `eb_special` ADD COLUMN `member_money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '会员价格' AFTER `member_pay_type`;
ALTER TABLE `eb_special` ADD COLUMN `admin_id` int(10) UNSIGNED DEFAULT '0' COMMENT '管理员id' AFTER `subject_id`;
ALTER TABLE `eb_special` ADD COLUMN `link` varchar(512) NOT NULL DEFAULT '' COMMENT '音频视频专题链接' AFTER `member_money`;
ALTER TABLE `eb_special_task` modify COLUMN `title` varchar(255) NOT NULL DEFAULT '' COMMENT '素材标题';
ALTER TABLE `eb_special_task` modify COLUMN `content` text NOT NULL COMMENT '内容';
ALTER TABLE `eb_special_task` ADD COLUMN `detail` text NOT NULL COMMENT '简介' AFTER `content`;
ALTER TABLE `eb_special_task` ADD COLUMN `edit_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间' AFTER `add_time`;
ALTER TABLE `eb_special_task` ADD COLUMN `type` tinyint(2) NOT NULL DEFAULT '6' COMMENT '素材类型1：图文专题；2：音频专题；3：视频专题；4：直播专题；5：专栏；6：其他专题' AFTER `detail`;
ALTER TABLE `eb_system_role` ADD COLUMN `sign` varchar(50) NOT NULL COMMENT '身份标识' AFTER `role_name`;
ALTER TABLE `eb_user` ADD COLUMN `overdue_time` int(11) UNSIGNED DEFAULT '0' COMMENT '会员过期时间' AFTER `is_h5user`;
ALTER TABLE `eb_user` ADD COLUMN `is_permanent` tinyint(2) DEFAULT '0' COMMENT '会员是否永久' AFTER `overdue_time`;
ALTER TABLE `eb_store_order` ADD COLUMN `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单类别 （0:课程订单 1:会员订单）' AFTER `cart_id`;
ALTER TABLE `eb_store_order` ADD COLUMN `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员期限ID';
--
-- 导航菜单调整
--
UPDATE `eb_system_menus` SET `sort`= 299 WHERE `id` = 429;
UPDATE `eb_system_menus` SET `sort`= 298 WHERE `id` = 430;
UPDATE `eb_system_menus` SET `sort`= 300 WHERE `id` = 428;
UPDATE `eb_system_menus` SET `sort`= 298 WHERE `id` = 399;

--
-- 专题
--
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (485, 0, 'book', '课程', 'admin', '', '', '[]', 299, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (486, 485, '', '图文专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"1\"}', 294, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (487, 485, '', '音频专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"2\"}', 295, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (488, 485, '', '视频专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"3\"}', 296, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (489, 485, '', '直播列表', 'admin', 'live.aliyun_live', 'special_live', '{\"special_type\":\"4\"}', 292, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (490, 485, '', '专栏列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"5\"}', 293, 1, 1);
UPDATE `eb_system_menus` SET `pid` = 485, `icon` = '', `menu_name` = '课程弹幕', `module` = 'admin', `controller` = 'special.special_type', `action` = 'special_barrage', `params` = '{\"type\":\"3\"}', `sort` = 291, `is_show` = 1, `access` = 1 WHERE `id` = 423;
--
-- 新闻
--
UPDATE `eb_system_menus` SET `menu_name` = '新闻', `sort` = 297 WHERE `id` = 417;
UPDATE `eb_system_menus` SET `menu_name` = '新闻列表', `sort` = 289 WHERE `id` = 418;
--
-- 用户
--
UPDATE `eb_system_menus` SET `sort` = 296 WHERE `id` = 151;
UPDATE `eb_system_menus` SET `menu_name` = '用户列表', `sort` = 285 WHERE `id` = 177;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (493, 151, '', '会员管理', 'admin', '', '', '[]', 283, 1, 1);
--
-- 分销
--
UPDATE `eb_system_menus` SET `sort` = 288 WHERE `id` = 337;
UPDATE `eb_system_menus` SET `sort` = 287, `pid` = 337, `menu_name` = '分销员列表' WHERE `id` = 421;
UPDATE `eb_system_menus` SET `pid` = 337, `menu_name` = '提现银行配置', `sort` = 281 WHERE `id` = 433;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (496, 337, '', '提现银行规则', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"53\"}', 280, 1, 1);
--
-- 营销
--
UPDATE `eb_system_menus` SET `menu_name` = '拼团列表', `sort` = 279 WHERE `id` = 379;
--
-- 维护
--
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (497, 21, '', '开发配置', 'admin', '', '', '[]', 278, 1, 1);
UPDATE `eb_system_menus` SET `pid` = 497, `menu_name` = '配置分类', `sort` = 277 WHERE `id` = 7;
UPDATE `eb_system_menus` SET `pid` = 497, `menu_name` = '组合数据', `sort` = 276 WHERE `id` = 9;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (498, 21, '', '安全维护', 'admin', '', '', '[]', 275, 1, 1);
UPDATE `eb_system_menus` SET `pid` = 498, `menu_name` = '系统日志', `sort` = 274 WHERE `id` = 130;
UPDATE `eb_system_menus` SET `pid` = 498, `menu_name` = '文件校验', `sort` = 273 WHERE `id` = 173;
UPDATE `eb_system_menus` SET `pid` = 498, `menu_name` = '数据库维护', `sort` = 272 WHERE `id` = 377;
UPDATE `eb_system_menus` SET `pid` = 498, `menu_name` = '清除数据', `sort` = 271 WHERE `id` = 278;
--
-- 设置
--
UPDATE `eb_system_menus` SET `menu_name` = '基础配置', `sort` = 270 WHERE `id` = 6;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (499, 1, '', '公众号配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"1\",\"tab_id\":\"2\"}', 269, 1, 1);
--
-- 删除原公众号里面的公众号接口配置和公众号支付配置
--
DELETE FROM `eb_system_menus` WHERE `id` = 267;
DELETE FROM `eb_system_menus` WHERE `id` = 361;
UPDATE `eb_system_config_tab` SET `title` = '公众号支付配置', `type` = 4 WHERE `id` = 4;
UPDATE `eb_system_config_tab` SET `title` = '支付宝支付配置', `type` = 4 WHERE `id` = 16;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (501, 1, '', '阿里云配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"17\",\"type\":\"5\"}', 265, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (502, 289, '', '商城设置', '', '', '', '[]', 264, 1, 1);


INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (500, 1, '', '支付配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"4\",\"tab_id\":\"4\"}', 266, 1, 1);
UPDATE `eb_system_config_tab` SET `title` = '短信配置', `eng_title` = 'sms_system', `status` = 1, `info` = 0, `icon` = 'paper-plane', `type` = 5 WHERE `id` = 17;
UPDATE `eb_system_config_tab` SET `title` = '阿里云key配置', `type` = 5 WHERE `id` = 18;
UPDATE `eb_system_config_tab` SET `title` = 'OSS上传配置', `type` = 5 WHERE `id` = 19;
UPDATE `eb_system_config_tab` SET `title` = '阿里云直播', `type` = 5 WHERE `id` = 21;
UPDATE `eb_system_menus` SET `pid` = 507, `menu_name` = '导航配置', `sort` = 264 WHERE `id` = 431;
UPDATE `eb_system_menus` SET `pid` = 507, `menu_name` = '首页轮播图', `sort` = 263 WHERE `id` = 357;
UPDATE `eb_system_menus` SET `pid` = 507, `menu_name` = '活动区域', `sort` = 262 WHERE `id` = 432;
UPDATE `eb_system_menus` SET `pid` = 507, `menu_name` = '推荐设置', `sort` = 261 WHERE `id` = 409;
UPDATE `eb_system_menus` SET `pid` = 502, `menu_name` = '关于我们', `sort` = 259 WHERE `id` = 434;
UPDATE `eb_system_menus` SET `pid` = 502, `menu_name` = '用户付费协议', `sort` = 259 WHERE `id` = 435;
DELETE FROM `eb_system_menus` WHERE `id` = 355;
UPDATE `eb_system_menus` SET `pid` = 502, `menu_name` = '关键词搜索', `sort` = 258 WHERE `id` = 420;
UPDATE `eb_system_menus` SET `menu_name` = '系统设置', `sort` = 265 WHERE `id` = 1;
UPDATE `eb_system_menus` SET `pid` = 289, `menu_name` = '客服管理', `sort` = 257 WHERE `id` = 175;
UPDATE `eb_system_menus` SET `pid` = 289, `menu_name` = '权限管理', `sort` = 256 WHERE `id` = 153;
/*UPDATE `eb_system_menus` SET `pid` = 493, `menu_name` = '会员等级', `sort` = 254 WHERE `id` = 491;
UPDATE `eb_system_menus` SET `pid` = 493, `menu_name` = '卡密会员', `sort` = 252 WHERE `id` = 490;*/
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 493, '', '会员权益', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"57\"}', 249, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 493, '', '会员说明', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"58\"}', 248, 1, 1);
UPDATE `eb_system_config` SET `config_tab_id` = 9 WHERE `id` = 140;
UPDATE `eb_system_config` SET `config_tab_id` = 9 WHERE `id` = 146;
UPDATE `eb_system_config` SET `config_tab_id` = 9 WHERE `id` = 147;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (507, 502, '', '首页配置', 'admin', '', '', '[]', 261, 1, 1);
UPDATE `eb_system_role` SET `sign`= 'admin' WHERE `id` = 1;
INSERT INTO `eb_system_role`(`id`, `role_name`, `sign`, `rules`, `level`, `status`) VALUES (NULL, '主播', 'anchor', '', 1, 1);

INSERT INTO `eb_system_group`(`id`, `name`, `info`, `config_name`, `fields`) VALUES (null, '会员权益', '会员权益', 'membership_interests', '[{\"name\":\"\\u6743\\u76ca\\u540d\\u79f0\",\"title\":\"name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u8bf4\\u660e\",\"title\":\"explain\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]');
INSERT INTO `eb_system_group`(`id`, `name`, `info`, `config_name`, `fields`) VALUES (null, '会员说明', '会员说明', 'member_description', '[{\"name\":\"\\u5185\\u5bb9\",\"title\":\"text\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]');
/*INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 151, '', '会员规则', 'admin', 'user.member_ship', 'index', '[]', 0, 1, 1);*/
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 493, '', '会员等级', 'admin', 'user.member_ship', 'index', '[]', 254, 1, 1);

/*INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 151, '', '会员记录', 'admin', 'user.member_record', 'index', '[]', 0, 1, 1);*/
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 493, '', '会员记录', 'admin', 'user.member_record', 'index', '[]', 251, 1, 1);

/*INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 151, '', '会员卡管理', 'admin', 'user.member_card', 'batch_index', '[]', 0, 1, 1);*/
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 493, '', '卡密会员', 'admin', 'user.member_card', 'batch_index', '[]', 252, 1, 1);

DELETE FROM `eb_system_menus` WHERE `id` IN (412,436,437,438,439,440,441,442,443,444,445,446,447);
DELETE FROM `eb_system_menus` WHERE `id` IN (415,453,452,451,450,449,448);
DELETE FROM `eb_system_menus` WHERE `id` IN (411);
DELETE FROM `eb_system_menus` WHERE `id` IN (481,480,479);
/*UPDATE `eb_system_menus` SET `sort`= 127, WHERE `id` = 482;*/

INSERT INTO `eb_system_group_data` (`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES
(161, 57, '{"name":{"type":"input","value":"\\u4f1a\\u5458\\u4f18\\u60e0\\u4ef7"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/39472202004291128217988.png"},"explain":{"type":"input","value":"\\u8d2d\\u4e70\\u4e13\\u9898\\u4eab\\u4f1a\\u5458\\u4ef7"},"sort":{"type":"input","value":"1"}}', '1588130969', '1', '1'),
(162, 57, '{"name":{"type":"input","value":"\\u514d\\u8d39\\u8bfe\\u7a0b"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/59285202004291128212590.png"},"explain":{"type":"input","value":"\\u90e8\\u5206\\u8bfe\\u7a0b\\u4f1a\\u5458\\u514d\\u8d39"},"sort":{"type":"input","value":"2"}}', '1588130996', '2', '1'),
(163, 57, '{"name":{"type":"input","value":"\\u66f4\\u591a\\u6743\\u76ca"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/11009202004291128212348.png"},"explain":{"type":"input","value":"\\u66f4\\u591a\\u6743\\u76ca\\u589e\\u52a0\\u4e2d"},"sort":{"type":"input","value":"3"}}', '1588131020', '3', '1'),
(164, 58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u8d2d\\u4e70\\u90e8\\u5206\\u8bfe\\u7a0b\\u53ef\\u4eab\\u53d7\\u4f18\\u60e0\\u4ef7"},"sort":{"type":"input","value":"1"}}', '1588131048', '1', '1'),
(165, 58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u5230\\u671f\\u540e\\u6743\\u76ca\\u5373\\u5931\\u6548\\uff0c\\u9700\\u7ee7\\u7eed\\u4eab\\u53d7\\u6743\\u76ca\\u8bf7\\u53ca\\u65f6\\u7eed\\u8d39"},"sort":{"type":"input","value":"2"}}', '1588131059', '2', '1'),
(166, 58, '{"text":{"type":"input","value":"\\u62fc\\u56e2\\u6d3b\\u52a8\\u4ef7\\u65e0\\u4f1a\\u5458\\u4f18\\u60e0"},"sort":{"type":"input","value":"3"}}', '1588131073', '3', '1');

/*INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (492, 485, '', '会员规则', 'admin', 'user.member_ship', 'index', '[]', 0, 1, 1);
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (492, 151, '', '会员记录', 'admin', 'user.member_record', 'index', '[]', 0, 1, 1);*/
INSERT INTO `eb_system_config_tab`(`id`, `title`, `eng_title`, `status`, `info`, `icon`, `type`) VALUES (22, '其他配置', 'shop_home', 1, 0, '', 6);
UPDATE `eb_system_config` SET `config_tab_id` = 22, `info` = '赠送礼物广告图', `desc` = '赠送礼物广告图' WHERE `id` = 141;
UPDATE `eb_system_config` SET `config_tab_id` = 22, `info` = '首页图标', `desc` = '首页图标' WHERE `id` = 168;
UPDATE `eb_system_config` SET `config_tab_id` = 22,  `info` = '虚拟用户专题弹幕开关', `desc` = '虚拟用户专题弹幕开关' WHERE `id` = 154;
UPDATE `eb_system_config` SET  `config_tab_id` = 22, `info` = '专题弹幕停留时间', `desc` = '专题弹幕停留时间 单位秒计算' WHERE `id` = 155;
INSERT INTO `eb_system_menus`(`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES (null, 502, '', '其他配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"22\"}', 260, 1, 1);

--
-- 新增表
-- 表的结构 `eb_special_source`
--

CREATE TABLE `eb_special_source` (
    `id` int(11) NOT NULL,
    `special_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '专题id',
    `source_id` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '素材id(task表id)',
    `pay_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0:免费，1：付费',
    `play_count` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '课程光看次数',
    `add_time` int(11) NOT NULL DEFAULT 0 COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='素材专题关联表';

ALTER TABLE `eb_special_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `special_ids` (`special_id`) USING BTREE,
  ADD KEY `source_ids` (`source_id`) USING BTREE;

ALTER TABLE `eb_special_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- 表的结构 `eb_member_card_batch`
--
CREATE TABLE `eb_member_card_batch` (
  `id` int(10) NOT NULL,
  `title` varchar(100) NOT NULL COMMENT '批次名称',
  `total_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '生成卡数量',
  `use_day` int(11) unsigned NOT NULL DEFAULT '7' COMMENT '体验天数',
  `use_num` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '使用',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否生效,控制此批次所有卡0：不生效；1：生效',
  `qrcode` varchar(255) NOT NULL COMMENT '二维码图路径',
  `remark` varchar(502) CHARACTER SET utf8mb4 NOT NULL COMMENT '备注',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡批次表';
 ALTER TABLE `eb_member_card_batch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 表的结构 `eb_member_card`
--
CREATE TABLE `eb_member_card` (
  `id` int(11) NOT NULL,
  `card_batch_id` int(11) unsigned NOT NULL COMMENT '卡批次id',
  `card_number` varchar(200) CHARACTER SET utf8mb4 NOT NULL DEFAULT '' COMMENT '卡号',
  `card_password` char(12) CHARACTER SET utf8mb4 NOT NULL COMMENT '密码',
  `use_uid` int(11) NOT NULL COMMENT '使用用户',
  `use_time` int(11) NOT NULL COMMENT '使用时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '卡状态：0：冻结；1：激活',
  `create_time` int(11) NOT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`,`card_batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡表';

 ALTER TABLE `eb_member_card`
 MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
 ADD KEY `card_batch_id` (`card_batch_id`) USING BTREE;

--
-- 表的结构 `eb_member_ship`
--
CREATE TABLE `eb_member_ship` (
  `id` int(10) NOT NULL,
  `type` int(2) NOT NULL DEFAULT '1' COMMENT '会员类别(1:普通会员)',
  `title` varchar(200) NOT NULL COMMENT '会员名称',
  `vip_day` int(10) NOT NULL COMMENT '会员时间(天)',
  `original_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '原价',
  `price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '优惠后价格',
  `is_permanent` int(2) NOT NULL DEFAULT '0' COMMENT '是否永久',
  `is_publish` int(2) NOT NULL COMMENT '是否发布',
  `is_free` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费',
  `sort` int(10) NOT NULL COMMENT '排序倒序',
  `is_del` int(2) NOT NULL COMMENT '删除',
  `add_time` int(50) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员管理表';
 ALTER TABLE `eb_member_ship`
  ADD PRIMARY KEY (`id`),
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
INSERT INTO `eb_member_ship` (`id`, `type`, `title`, `vip_day`, `original_price`, `price`, `is_permanent`, `is_publish`, `is_free`, `sort`, `is_del`, `add_time`) VALUES
 (NULL, '1', '月卡', '30', '30.00', '20.00', '0', '1', '0', '4', '0', '1588129765'),
 (NULL, '1', '季卡', '90', '90.00', '80.00', '0', '1', '0', '3', '0', '1588129794'),
 (NULL, '1', '年卡', '365', '360.00', '300.00', '0', '1', '0', '2', '0', '1588129818'),
 (NULL, '1', '永久', '-1', '1200.00', '1000.00', '1', '1', '0', '1', '0', '1588129856'),
 (NULL, '1', '免费', '7', '0.00', '0.00', '0', '1', '1', '0', '0', '1588130680');

--
-- 表的结构 `eb_member_record`
--
CREATE TABLE `eb_member_record` (
  `id` int(10) NOT NULL,
  `oid` int(10) unsigned NOT NULL COMMENT '订单ID',
  `uid` int(10) unsigned NOT NULL COMMENT '用户uid',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '区别 0:购买 1:会员卡',
  `code` varchar(255) DEFAULT NULL COMMENT '卡号',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '支付金额',
  `purchase_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '会员购买时间',
  `is_free` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否免费',
  `is_permanent` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否永久',
  `overdue_time` int(50) unsigned NOT NULL COMMENT '会员过期时间',
  `add_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员购买记录表';

ALTER TABLE `eb_member_record`
MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `eb_member_record` ADD COLUMN `validity` varchar(10) DEFAULT '0' COMMENT '有效期' AFTER `overdue_time`;