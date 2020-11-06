
--
-- 表的结构 `eb_article`
--

CREATE TABLE `eb_article` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文章管理ID',
  `cid` varchar(255) DEFAULT '1' COMMENT '分类id',
  `title` varchar(255) DEFAULT NULL COMMENT '文章标题',
  `author` varchar(255) DEFAULT NULL COMMENT '文章作者',
  `image_input` varchar(255) DEFAULT NULL COMMENT '文章图片',
  `synopsis` varchar(255) DEFAULT NULL COMMENT '文章简介',
  `label` varchar(2000) NOT NULL DEFAULT '' COMMENT '标签',
  `share_title` varchar(255) DEFAULT NULL COMMENT '文章分享标题',
  `share_synopsis` varchar(255) DEFAULT NULL COMMENT '文章分享简介',
  `visit` varchar(255) DEFAULT NULL COMMENT '浏览次数',
  `sort` int(10) UNSIGNED DEFAULT '0' COMMENT '排序',
  `url` varchar(255) DEFAULT NULL COMMENT '原文链接',
  `status` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '状态',
  `add_time` varchar(255) DEFAULT NULL COMMENT '添加时间',
  `hide` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否隐藏',
  `admin_id` int(10) UNSIGNED DEFAULT '0' COMMENT '管理员id',
  `mer_id` int(10) UNSIGNED DEFAULT '0' COMMENT '商户id',
  `is_hot` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否热门(小程序)',
  `is_banner` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否轮播图(小程序)',
  `consult_image` varchar(255) DEFAULT NULL COMMENT '活动咨询图片',
  `consult_type` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '活动咨询类型 1 图片 2视屏',
  `is_consult` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否活动咨询',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_article_category`
--

CREATE TABLE `eb_article_category` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文章分类id',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `title` varchar(255) NOT NULL COMMENT '文章分类标题',
  `intr` varchar(255) DEFAULT NULL COMMENT '文章分类简介',
  `image` varchar(255) NOT NULL COMMENT '文章分类图片',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '状态',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '1删除0未删除',
  `add_time` varchar(255) NOT NULL COMMENT '添加时间',
  `hidden` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_article_content`
--

CREATE TABLE `eb_article_content` (
  `nid` int(10) UNSIGNED NOT NULL COMMENT '文章id',
  `content` text NOT NULL COMMENT '文章内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章内容表';


-- --------------------------------------------------------

--
-- 表的结构 `eb_cache`
--

CREATE TABLE `eb_cache` (
  `key` varchar(32) NOT NULL,
  `result` text COMMENT '缓存数据',
  `add_time` int(10) DEFAULT NULL COMMENT '缓存时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='记录缓存';

-- --------------------------------------------------------

--
-- 表的结构 `eb_express`
--

CREATE TABLE `eb_express` (
  `id` mediumint(11) UNSIGNED NOT NULL COMMENT '快递公司id',
  `code` varchar(50) NOT NULL COMMENT '快递公司简称',
  `name` varchar(50) NOT NULL COMMENT '快递公司全称',
  `sort` int(11) NOT NULL COMMENT '排序',
  `is_show` tinyint(1) NOT NULL COMMENT '是否显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='快递公司表';

--
-- 转存表中的数据 `eb_express`
--

INSERT INTO `eb_express` (`id`, `code`, `name`, `sort`, `is_show`) VALUES
(1, 'LIMINWL', '利民物流', 1, 1),
(2, 'XINTIAN', '鑫天顺物流', 1, 1),
(3, 'henglu', '恒路物流', 1, 1),
(4, 'klwl', '康力物流', 1, 1),
(5, 'meiguo', '美国快递', 1, 1),
(6, 'a2u', 'A2U速递', 1, 1),
(7, 'benteng', '奔腾物流', 1, 1),
(8, 'ahdf', '德方物流', 1, 1),
(9, 'timedg', '万家通', 1, 1),
(10, 'ztong', '智通物流', 1, 1),
(11, 'xindan', '新蛋物流', 1, 1),
(12, 'bgpyghx', '挂号信', 1, 1),
(13, 'XFHONG', '鑫飞鸿物流快递', 1, 1),
(14, 'ALP', '阿里物流', 1, 1),
(15, 'BFWL', '滨发物流', 1, 1),
(16, 'SJWL', '宋军物流', 1, 1),
(17, 'SHUNFAWL', '顺发物流', 1, 1),
(18, 'TIANHEWL', '天河物流', 1, 1),
(19, 'YBWL', '邮联物流', 1, 1),
(20, 'SWHY', '盛旺货运', 1, 1),
(21, 'TSWL', '汤氏物流', 1, 1),
(22, 'YUANYUANWL', '圆圆物流', 1, 1),
(23, 'BALIANGWL', '八梁物流', 1, 1),
(24, 'ZGWL', '振刚物流', 1, 1),
(25, 'JIAYU', '佳宇物流', 1, 1),
(26, 'SHHX', '昊昕物流', 1, 1),
(27, 'ande', '安得物流', 1, 1),
(28, 'ppbyb', '贝邮宝', 1, 1),
(29, 'dida', '递达快递', 1, 1),
(30, 'jppost', '日本邮政', 1, 1),
(31, 'intmail', '中国邮政', 96, 1),
(32, 'HENGCHENGWL', '恒诚物流', 1, 1),
(33, 'HENGFENGWL', '恒丰物流', 1, 1),
(34, 'gdems', '广东ems快递', 1, 1),
(35, 'xlyt', '祥龙运通', 1, 1),
(36, 'gjbg', '国际包裹', 1, 1),
(37, 'uex', 'UEX', 1, 1),
(38, 'singpost', '新加坡邮政', 1, 1),
(39, 'guangdongyouzhengwuliu', '广东邮政', 1, 1),
(40, 'bht', 'BHT', 1, 1),
(41, 'cces', 'CCES快递', 1, 1),
(42, 'cloudexpress', 'CE易欧通国际速递', 1, 1),
(43, 'dasu', '达速物流', 1, 1),
(44, 'pfcexpress', '皇家物流', 1, 1),
(45, 'hjs', '猴急送', 1, 1),
(46, 'huilian', '辉联物流', 1, 1),
(47, 'huanqiu', '环球速运', 1, 1),
(48, 'huada', '华达快运', 1, 1),
(49, 'htwd', '华通务达物流', 1, 1),
(50, 'hipito', '海派通', 1, 1),
(51, 'hqtd', '环球通达', 1, 1),
(52, 'airgtc', '航空快递', 1, 1),
(53, 'haoyoukuai', '好又快物流', 1, 1),
(54, 'hanrun', '韩润物流', 1, 1),
(55, 'ccd', '河南次晨达', 1, 1),
(56, 'hfwuxi', '和丰同城', 1, 1),
(57, 'Sky', '荷兰', 1, 1),
(58, 'hongxun', '鸿讯物流', 1, 1),
(59, 'hongjie', '宏捷国际物流', 1, 1),
(60, 'httx56', '汇通天下物流', 1, 1),
(61, 'lqht', '恒通快递', 1, 1),
(62, 'jinguangsudikuaijian', '京广速递快件', 1, 1),
(63, 'junfengguoji', '骏丰国际速递', 1, 1),
(64, 'jiajiatong56', '佳家通', 1, 1),
(65, 'jrypex', '吉日优派', 1, 1),
(66, 'jinchengwuliu', '锦程国际物流', 1, 1),
(67, 'jgwl', '景光物流', 1, 1),
(68, 'pzhjst', '急顺通', 1, 1),
(69, 'ruexp', '捷网俄全通', 1, 1),
(70, 'jmjss', '金马甲', 1, 1),
(71, 'lanhu', '蓝弧快递', 1, 1),
(72, 'ltexp', '乐天速递', 1, 1),
(73, 'lutong', '鲁通快运', 1, 1),
(74, 'ledii', '乐递供应链', 1, 1),
(75, 'lundao', '论道国际物流', 1, 1),
(76, 'mailikuaidi', '麦力快递', 1, 1),
(77, 'mchy', '木春货运', 1, 1),
(78, 'meiquick', '美快国际物流', 1, 1),
(79, 'valueway', '美通快递', 1, 1),
(80, 'nuoyaao', '偌亚奥国际', 1, 1),
(81, 'euasia', '欧亚专线', 1, 1),
(82, 'pca', '澳大利亚PCA快递', 1, 1),
(83, 'pingandatengfei', '平安达腾飞', 1, 1),
(84, 'pjbest', '品骏快递', 1, 1),
(85, 'qbexpress', '秦邦快运', 1, 1),
(86, 'quanxintong', '全信通快递', 1, 1),
(87, 'quansutong', '全速通国际快递', 1, 1),
(88, 'qinyuan', '秦远物流', 1, 1),
(89, 'qichen', '启辰国际物流', 1, 1),
(90, 'quansu', '全速快运', 1, 1),
(91, 'qzx56', '全之鑫物流', 1, 1),
(92, 'qskdyxgs', '千顺快递', 1, 1),
(93, 'runhengfeng', '全时速运', 1, 1),
(94, 'rytsd', '日益通速递', 1, 1),
(95, 'ruidaex', '瑞达国际速递', 1, 1),
(96, 'shiyun', '世运快递', 1, 1),
(97, 'sfift', '十方通物流', 1, 1),
(98, 'stkd', '顺通快递', 1, 1),
(99, 'bgn', '布谷鸟快递', 1, 1),
(100, 'jiahuier', '佳惠尔快递', 1, 1),
(101, 'pingyou', '小包', 1, 1),
(102, 'yumeijie', '誉美捷快递', 1, 1),
(103, 'meilong', '美龙快递', 1, 1),
(104, 'guangtong', '广通速递', 1, 1),
(105, 'STARS', '星晨急便', 1, 1),
(106, 'NANHANG', '中国南方航空股份有限公司', 1, 1),
(107, 'lanbiao', '蓝镖快递', 1, 1),
(109, 'baotongda', '宝通达物流', 1, 1),
(110, 'dashun', '大顺物流', 1, 1),
(111, 'dada', '大达物流', 1, 1),
(112, 'fangfangda', '方方达物流', 1, 1),
(113, 'hebeijianhua', '河北建华物流', 1, 1),
(114, 'haolaiyun', '好来运快递', 1, 1),
(115, 'jinyue', '晋越快递', 1, 1),
(116, 'kuaitao', '快淘快递', 1, 1),
(117, 'peixing', '陪行物流', 1, 1),
(118, 'hkpost', '香港邮政', 1, 1),
(119, 'ytfh', '一统飞鸿快递', 1, 1),
(120, 'zhongxinda', '中信达快递', 1, 1),
(121, 'zhongtian', '中天快运', 1, 1),
(122, 'zuochuan', '佐川急便', 1, 1),
(123, 'chengguang', '程光快递', 1, 1),
(124, 'cszx', '城市之星', 1, 1),
(125, 'chuanzhi', '传志快递', 1, 1),
(126, 'feibao', '飞豹快递', 1, 1),
(127, 'huiqiang', '汇强快递', 1, 1),
(128, 'lejiedi', '乐捷递', 1, 1),
(129, 'lijisong', '成都立即送快递', 1, 1),
(130, 'minbang', '民邦速递', 1, 1),
(131, 'ocs', 'OCS国际快递', 1, 1),
(132, 'santai', '三态速递', 1, 1),
(133, 'saiaodi', '赛澳递', 1, 1),
(134, 'jingdong', '京东快递', 1, 1),
(135, 'zengyi', '增益快递', 1, 1),
(136, 'fanyu', '凡宇速递', 1, 1),
(137, 'fengda', '丰达快递', 1, 1),
(138, 'coe', '东方快递', 1, 1),
(139, 'ees', '百福东方快递', 1, 1),
(140, 'disifang', '递四方速递', 1, 1),
(141, 'rufeng', '如风达快递', 1, 1),
(142, 'changtong', '长通物流', 1, 1),
(143, 'chengshi100', '城市100快递', 1, 1),
(144, 'feibang', '飞邦物流', 1, 1),
(145, 'haosheng', '昊盛物流', 1, 1),
(146, 'yinsu', '音速速运', 1, 1),
(147, 'kuanrong', '宽容物流', 1, 1),
(148, 'tongcheng', '通成物流', 1, 1),
(149, 'tonghe', '通和天下物流', 1, 1),
(150, 'zhima', '芝麻开门', 1, 1),
(151, 'ririshun', '日日顺物流', 1, 1),
(152, 'anxun', '安迅物流', 1, 1),
(153, 'baiqian', '百千诚国际物流', 1, 1),
(154, 'chukouyi', '出口易', 1, 1),
(155, 'diantong', '店通快递', 1, 1),
(156, 'dajin', '大金物流', 1, 1),
(157, 'feite', '飞特物流', 1, 1),
(159, 'gnxb', '国内小包', 1, 1),
(160, 'huacheng', '华诚物流', 1, 1),
(161, 'huahan', '华翰物流', 1, 1),
(162, 'hengyu', '恒宇运通', 1, 1),
(163, 'huahang', '华航快递', 1, 1),
(164, 'jiuyi', '久易快递', 1, 1),
(165, 'jiete', '捷特快递', 1, 1),
(166, 'jingshi', '京世物流', 1, 1),
(167, 'kuayue', '跨越快递', 1, 1),
(168, 'mengsu', '蒙速快递', 1, 1),
(169, 'nanbei', '南北快递', 1, 1),
(171, 'pinganda', '平安达快递', 1, 1),
(172, 'ruifeng', '瑞丰速递', 1, 1),
(173, 'rongqing', '荣庆物流', 1, 1),
(174, 'suijia', '穗佳物流', 1, 1),
(175, 'simai', '思迈快递', 1, 1),
(176, 'suteng', '速腾快递', 1, 1),
(177, 'shengbang', '晟邦物流', 1, 1),
(178, 'suchengzhaipei', '速呈宅配', 1, 1),
(179, 'wuhuan', '五环速递', 1, 1),
(180, 'xingchengzhaipei', '星程宅配', 1, 1),
(181, 'yinjie', '顺捷丰达', 1, 1),
(183, 'yanwen', '燕文物流', 1, 1),
(184, 'zongxing', '纵行物流', 1, 1),
(185, 'aae', 'AAE快递', 1, 1),
(186, 'dhl', 'DHL快递', 1, 1),
(187, 'feihu', '飞狐快递', 1, 1),
(188, 'shunfeng', '顺丰速运', 92, 1),
(189, 'spring', '春风物流', 1, 1),
(190, 'yidatong', '易达通快递', 1, 1),
(191, 'PEWKEE', '彪记快递', 1, 1),
(192, 'PHOENIXEXP', '凤凰快递', 1, 1),
(193, 'CNGLS', 'GLS快递', 1, 1),
(194, 'BHTEXP', '华慧快递', 1, 1),
(195, 'B2B', '卡行天下', 1, 1),
(196, 'PEISI', '配思货运', 1, 1),
(197, 'SUNDAPOST', '上大物流', 1, 1),
(198, 'SUYUE', '苏粤货运', 1, 1),
(199, 'F5XM', '伍圆速递', 1, 1),
(200, 'GZWENJIE', '文捷航空速递', 1, 1),
(201, 'yuancheng', '远成物流', 1, 1),
(202, 'dpex', 'DPEX快递', 1, 1),
(203, 'anjie', '安捷快递', 1, 1),
(204, 'jldt', '嘉里大通', 1, 1),
(205, 'yousu', '优速快递', 1, 1),
(206, 'wanbo', '万博快递', 1, 1),
(207, 'sure', '速尔物流', 1, 1),
(208, 'sutong', '速通物流', 1, 1),
(209, 'JUNCHUANWL', '骏川物流', 1, 1),
(210, 'guada', '冠达快递', 1, 1),
(211, 'dsu', 'D速快递', 1, 1),
(212, 'LONGSHENWL', '龙胜物流', 1, 1),
(213, 'abc', '爱彼西快递', 1, 1),
(214, 'eyoubao', 'E邮宝', 1, 1),
(215, 'aol', 'AOL快递', 1, 1),
(216, 'jixianda', '急先达物流', 1, 1),
(217, 'haihong', '山东海红快递', 1, 1),
(218, 'feiyang', '飞洋快递', 1, 1),
(219, 'rpx', 'RPX保时达', 1, 1),
(220, 'zhaijisong', '宅急送', 1, 1),
(221, 'tiantian', '天天快递', 99, 1),
(222, 'yunwuliu', '云物流', 1, 1),
(223, 'jiuye', '九曳供应链', 1, 1),
(224, 'bsky', '百世快运', 1, 1),
(225, 'higo', '黑狗物流', 1, 1),
(226, 'arke', '方舟速递', 1, 1),
(227, 'zwsy', '中外速运', 1, 1),
(228, 'jxy', '吉祥邮', 1, 1),
(229, 'aramex', 'Aramex', 1, 1),
(230, 'guotong', '国通快递', 1, 1),
(231, 'jiayi', '佳怡物流', 1, 1),
(232, 'longbang', '龙邦快运', 1, 1),
(233, 'minhang', '民航快递', 1, 1),
(234, 'quanyi', '全一快递', 1, 1),
(235, 'quanchen', '全晨快递', 1, 1),
(236, 'usps', 'USPS快递', 1, 1),
(237, 'xinbang', '新邦物流', 1, 1),
(238, 'yuanzhi', '元智捷诚快递', 1, 1),
(239, 'zhongyou', '中邮物流', 1, 1),
(240, 'yuxin', '宇鑫物流', 1, 1),
(241, 'cnpex', '中环快递', 1, 1),
(242, 'shengfeng', '盛丰物流', 1, 1),
(243, 'yuantong', '圆通速递', 97, 1),
(244, 'jiayunmei', '加运美物流', 1, 1),
(245, 'ywfex', '源伟丰快递', 1, 1),
(246, 'xinfeng', '信丰物流', 1, 1),
(247, 'wanxiang', '万象物流', 1, 1),
(248, 'menduimen', '门对门', 1, 1),
(249, 'mingliang', '明亮物流', 1, 1),
(250, 'fengxingtianxia', '风行天下', 1, 1),
(251, 'gongsuda', '共速达物流', 1, 1),
(252, 'zhongtong', '中通快递', 100, 1),
(253, 'quanritong', '全日通快递', 1, 1),
(254, 'ems', 'EMS', 1, 1),
(255, 'wanjia', '万家物流', 1, 1),
(256, 'yuntong', '运通快递', 1, 1),
(257, 'feikuaida', '飞快达物流', 1, 1),
(258, 'haimeng', '海盟速递', 1, 1),
(259, 'zhongsukuaidi', '中速快件', 1, 1),
(260, 'yuefeng', '越丰快递', 1, 1),
(261, 'shenghui', '盛辉物流', 1, 1),
(262, 'datian', '大田物流', 1, 1),
(263, 'quanjitong', '全际通快递', 1, 1),
(264, 'longlangkuaidi', '隆浪快递', 1, 1),
(265, 'neweggozzo', '新蛋奥硕物流', 1, 1),
(266, 'shentong', '申通快递', 95, 1),
(267, 'haiwaihuanqiu', '海外环球', 1, 1),
(268, 'yad', '源安达快递', 1, 1),
(269, 'jindawuliu', '金大物流', 1, 1),
(270, 'sevendays', '七天连锁', 1, 1),
(271, 'tnt', 'TNT快递', 1, 1),
(272, 'huayu', '天地华宇物流', 1, 1),
(273, 'lianhaotong', '联昊通快递', 1, 1),
(274, 'nengda', '港中能达快递', 1, 1),
(275, 'LBWL', '联邦物流', 1, 1),
(276, 'ontrac', 'onTrac', 1, 1),
(277, 'feihang', '原飞航快递', 1, 1),
(278, 'bangsongwuliu', '邦送物流', 1, 1),
(279, 'huaxialong', '华夏龙物流', 1, 1),
(280, 'ztwy', '中天万运快递', 1, 1),
(281, 'fkd', '飞康达物流', 1, 1),
(282, 'anxinda', '安信达快递', 1, 1),
(283, 'quanfeng', '全峰快递', 1, 1),
(284, 'shengan', '圣安物流', 1, 1),
(285, 'jiaji', '佳吉物流', 1, 1),
(286, 'yunda', '韵达快运', 94, 1),
(287, 'ups', 'UPS快递', 1, 1),
(288, 'debang', '德邦物流', 1, 1),
(289, 'yafeng', '亚风速递', 1, 1),
(290, 'kuaijie', '快捷速递', 98, 1),
(291, 'huitong', '百世快递', 93, 1),
(293, 'aolau', 'AOL澳通速递', 1, 1),
(294, 'anneng', '安能物流', 1, 1),
(295, 'auexpress', '澳邮中国快运', 1, 1),
(296, 'exfresh', '安鲜达', 1, 1),
(297, 'bcwelt', 'BCWELT', 1, 1),
(298, 'youzhengguonei', '挂号信', 1, 1),
(299, 'xiaohongmao', '北青小红帽', 1, 1),
(300, 'lbbk', '宝凯物流', 1, 1),
(301, 'byht', '博源恒通', 1, 1),
(302, 'idada', '百成大达物流', 1, 1),
(303, 'baitengwuliu', '百腾物流', 1, 1),
(304, 'birdex', '笨鸟海淘', 1, 1),
(305, 'bsht', '百事亨通', 1, 1),
(306, 'dayang', '大洋物流快递', 1, 1),
(307, 'dechuangwuliu', '德创物流', 1, 1),
(308, 'donghanwl', '东瀚物流', 1, 1),
(309, 'dfpost', '达方物流', 1, 1),
(310, 'dongjun', '东骏快捷物流', 1, 1),
(311, 'dindon', '叮咚澳洲转运', 1, 1),
(312, 'dazhong', '大众佐川急便', 1, 1),
(313, 'decnlh', '德中快递', 1, 1),
(314, 'dekuncn', '德坤供应链', 1, 1),
(315, 'eshunda', '俄顺达', 1, 1),
(316, 'ewe', 'EWE全球快递', 1, 1),
(317, 'fedexuk', 'FedEx英国', 1, 1),
(318, 'fox', 'FOX国际速递', 1, 1),
(319, 'rufengda', '凡客如风达', 1, 1),
(320, 'fandaguoji', '颿达国际快递', 1, 1),
(321, 'hnfy', '飞鹰物流', 1, 1),
(322, 'flysman', '飞力士物流', 1, 1),
(323, 'sccod', '丰程物流', 1, 1),
(324, 'farlogistis', '泛远国际物流', 1, 1),
(325, 'gsm', 'GSM', 1, 1),
(326, 'gaticn', 'GATI快递', 1, 1),
(327, 'gts', 'GTS快递', 1, 1),
(328, 'gangkuai', '港快速递', 1, 1),
(329, 'gtsd', '高铁速递', 1, 1),
(330, 'tiandihuayu', '华宇物流', 1, 1),
(331, 'huangmajia', '黄马甲快递', 1, 1),
(332, 'ucs', '合众速递', 1, 1),
(333, 'huoban', '伙伴物流', 1, 1),
(334, 'nedahm', '红马速递', 1, 1),
(335, 'huiwen', '汇文配送', 1, 1),
(336, 'nmhuahe', '华赫物流', 1, 1),
(337, 'hangyu', '航宇快递', 1, 1),
(338, 'minsheng', '闽盛物流', 1, 1),
(339, 'riyu', '日昱物流', 1, 1),
(340, 'sxhongmajia', '山西红马甲', 1, 1),
(341, 'syjiahuier', '沈阳佳惠尔', 1, 1),
(342, 'shlindao', '上海林道货运', 1, 1),
(343, 'shunjiefengda', '顺捷丰达', 1, 1),
(344, 'subida', '速必达物流', 1, 1),
(345, 'bphchina', '速方国际物流', 1, 1),
(346, 'sendtochina', '速递中国', 1, 1),
(347, 'suning', '苏宁快递', 1, 1),
(348, 'sihaiet', '四海快递', 1, 1),
(349, 'tianzong', '天纵物流', 1, 1),
(350, 'chinatzx', '同舟行物流', 1, 1),
(351, 'nntengda', '腾达速递', 1, 1),
(352, 'sd138', '泰国138', 1, 1),
(353, 'tongdaxing', '通达兴物流', 1, 1),
(354, 'tlky', '天联快运', 1, 1),
(355, 'youshuwuliu', 'UC优速快递', 1, 1),
(356, 'ueq', 'UEQ快递', 1, 1),
(357, 'weitepai', '微特派快递', 1, 1),
(358, 'wtdchina', '威时沛运', 1, 1),
(359, 'wzhaunyun', '微转运', 1, 1),
(360, 'gswtkd', '万通快递', 1, 1),
(361, 'wotu', '渥途国际速运', 1, 1),
(362, 'xiyoute', '希优特快递', 1, 1),
(363, 'xilaikd', '喜来快递', 1, 1),
(364, 'xsrd', '鑫世锐达', 1, 1),
(365, 'xtb', '鑫通宝物流', 1, 1),
(366, 'xintianjie', '信天捷快递', 1, 1),
(367, 'xaetc', '西安胜峰', 1, 1),
(368, 'xianfeng', '先锋快递', 1, 1),
(369, 'sunspeedy', '新速航', 1, 1),
(370, 'xipost', '西邮寄', 1, 1),
(371, 'sinatone', '信联通', 1, 1),
(372, 'sunjex', '新杰物流', 1, 1),
(373, 'yundaexus', '韵达美国件', 1, 1),
(374, 'yxwl', '宇鑫物流', 1, 1),
(375, 'yitongda', '易通达', 1, 1),
(376, 'yiqiguojiwuliu', '一柒物流', 1, 1),
(377, 'yilingsuyun', '亿领速运', 1, 1),
(378, 'yujiawuliu', '煜嘉物流', 1, 1),
(379, 'gml', '英脉物流', 1, 1),
(380, 'leopard', '云豹国际货运', 1, 1),
(381, 'czwlyn', '云南中诚', 1, 1),
(382, 'sdyoupei', '优配速运', 1, 1),
(383, 'yongchang', '永昌物流', 1, 1),
(384, 'yufeng', '御风速运', 1, 1),
(385, 'yamaxunwuliu', '亚马逊物流', 1, 1),
(386, 'yousutongda', '优速通达', 1, 1),
(387, 'yishunhang', '亿顺航', 1, 1),
(388, 'yongwangda', '永旺达快递', 1, 1),
(389, 'ecmscn', '易满客', 1, 1),
(390, 'yingchao', '英超物流', 1, 1),
(391, 'edlogistics', '益递物流', 1, 1),
(392, 'yyexpress', '远洋国际', 1, 1),
(393, 'onehcang', '一号仓', 1, 1),
(394, 'ycgky', '远成快运', 1, 1),
(395, 'lineone', '一号线', 1, 1),
(396, 'ypsd', '壹品速递', 1, 1),
(397, 'vipexpress', '鹰运国际速递', 1, 1),
(398, 'el56', '易联通达物流', 1, 1),
(399, 'yyqc56', '一运全成物流', 1, 1),
(400, 'zhongtie', '中铁快运', 1, 1),
(401, 'ZTKY', '中铁物流', 1, 1),
(402, 'zzjh', '郑州建华快递', 1, 1),
(403, 'zhongruisudi', '中睿速递', 1, 1),
(404, 'zhongwaiyun', '中外运速递', 1, 1),
(405, 'zengyisudi', '增益速递', 1, 1),
(406, 'sujievip', '郑州速捷', 1, 1),
(407, 'zhichengtongda', '至诚通达快递', 1, 1),
(408, 'zhdwl', '众辉达物流', 1, 1),
(409, 'kuachangwuliu', '直邮易', 1, 1),
(410, 'topspeedex', '中运全速', 1, 1),
(411, 'otobv', '中欧快运', 1, 1),
(412, 'zsky123', '准实快运', 1, 1),
(413, 'donghong', '东红物流', 1, 1),
(414, 'kuaiyouda', '快优达速递', 1, 1),
(415, 'balunzhi', '巴伦支快递', 1, 1),
(416, 'hutongwuliu', '户通物流', 1, 1),
(417, 'xianchenglian', '西安城联速递', 1, 1),
(418, 'youbijia', '邮必佳', 1, 1),
(419, 'feiyuan', '飞远物流', 1, 1),
(420, 'chengji', '城际速递', 1, 1),
(421, 'huaqi', '华企快运', 1, 1),
(422, 'yibang', '一邦快递', 1, 1),
(423, 'citylink', 'CityLink快递', 1, 1),
(424, 'meixi', '美西快递', 1, 1),
(425, 'acs', 'ACS', 1, 1);

-- --------------------------------------------------------

--
-- 表的结构 `eb_grade`
--

CREATE TABLE `eb_grade` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL COMMENT '年级名称',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='年级列表';

--
-- 转存表中的数据 `eb_grade`
--

INSERT INTO `eb_grade` (`id`, `name`, `sort`, `add_time`) VALUES
(1, '少儿编程', 5, 1547625712),
(2, '高中', 6, 1547625985),
(3, '初中', 7, 1547625992),
(4, '小学', 8, 1547626003),
(5, '绘画', 4, 1581414927),
(6, '公考', 3, 1581414950),
(7, '小语种', 2, 1581414960),
(8, '考研', 1, 1581414969);

-- --------------------------------------------------------

--
-- 表的结构 `eb_live_barrage`
--

CREATE TABLE `eb_live_barrage` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `to_uid` int(11) NOT NULL COMMENT '给谁发送',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '消息类型，1=文本，2=图片，3=语音',
  `barrage` varchar(3000) NOT NULL DEFAULT '' COMMENT '评论内容',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '发送评论时间',
  `is_show` int(11) NOT NULL DEFAULT '1' COMMENT '是否显示 1= 显示，0=隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播间弹幕';

--
-- 转存表中的数据 `eb_live_barrage`
--

INSERT INTO `eb_live_barrage` (`id`, `uid`, `to_uid`, `type`, `barrage`, `live_id`, `add_time`, `is_show`) VALUES
(1, 209, 0, 1, '你好', 10, 1582029381, 1),
(2, 198, 0, 1, '[qq_12][qq_12][qq_12][qq_12][qq_12]', 10, 1582073756, 1),
(3, 164, 0, 1, '嘿嘿', 10, 1582077353, 1),
(4, 164, 0, 1, '[qq_13]', 10, 1582077358, 1),
(5, 164, 0, 1, '[qq_12][qq_12]', 10, 1582077363, 1),
(6, 276, 0, 1, '[qq_11][qq_12][qq_12][qq_6][qq_6]', 10, 1582093402, 1),
(7, 276, 0, 1, '[qq_2][qq_3][qq_4][qq_4][qq_5]', 10, 1582093406, 1),
(8, 65, 0, 2, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/95d75202002191944598419.png', 10, 1582112699, 1),
(9, 319, 0, 1, '[em_37]', 10, 1582127815, 1),
(10, 65, 0, 1, 'd', 11, 1582166793, 1),
(11, 65, 0, 1, 'asd', 11, 1582166828, 1),
(12, 66, 0, 1, '你是', 11, 1582166839, 1),
(13, 66, 0, 1, '你是', 11, 1582166854, 1),
(14, 65, 0, 1, 'asd', 11, 1582166858, 1),
(15, 65, 0, 1, '咱是', 11, 1582166867, 1),
(16, 65, 0, 1, '实打实打算啊是大', 11, 1582166921, 1),
(17, 65, 0, 1, '打发士大夫二', 11, 1582166930, 1),
(18, 341, 0, 1, '323419323419', 10, 1582205387, 1),
(19, 326, 0, 1, '[em_25]', 10, 1582284966, 1),
(20, 503, 0, 1, '回放呢', 10, 1582285233, 1),
(21, 503, 0, 1, '123', 10, 1582285242, 1),
(22, 524, 0, 1, '？', 10, 1582285336, 1),
(23, 574, 0, 1, '[qq_1]', 10, 1582285636, 1),
(24, 592, 0, 1, '[qq_1]', 10, 1582285835, 1),
(25, 506, 0, 1, '看不到呀', 10, 1582285963, 1),
(26, 608, 0, 1, '好棒', 10, 1582286466, 1),
(27, 680, 0, 1, '直播', 10, 1582287711, 1),
(28, 680, 0, 1, '[qq_1]', 10, 1582287733, 1),
(29, 680, 0, 1, '[qq_2]', 10, 1582287742, 1),
(30, 715, 0, 1, '大家好', 10, 1582289243, 1),
(31, 759, 0, 1, '63+', 10, 1582290626, 1),
(32, 759, 0, 1, '错过就感觉到个低功耗', 10, 1582290630, 1),
(33, 776, 0, 1, '测试', 10, 1582291516, 1),
(34, 807, 0, 1, '[qq_13]', 10, 1582293831, 1),
(35, 832, 0, 1, '那么状况', 10, 1582296232, 1),
(36, 832, 0, 2, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3efd39040238907d69ae.jpg', 10, 1582296253, 1),
(37, 842, 0, 1, '123', 10, 1582297198, 1),
(38, 842, 0, 1, '好', 10, 1582297226, 1),
(39, 842, 0, 2, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/417be91ff00706d68f62.jpg', 10, 1582297288, 1),
(40, 864, 0, 1, '[qq_12]', 10, 1582299228, 1),
(41, 864, 0, 1, '[qq_5]', 10, 1582299232, 1),
(42, 864, 0, 1, '[em_3][em_3]', 10, 1582299237, 1),
(43, 620, 0, 1, '人生的奔跑，不在于瞬间的爆发，而取决于途中的坚持。', 10, 1582306748, 1),
(44, 918, 0, 1, '测试', 10, 1582329784, 1),
(45, 182, 0, 1, '测试', 10, 1582330164, 1),
(46, 835, 0, 1, '？？？？', 10, 1582337896, 1),
(47, 262, 0, 1, '哈哈', 10, 1582344568, 1),
(48, 969, 0, 1, '?', 10, 1582349253, 1),
(49, 969, 0, 2, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/edf24f679abab08d7ee1.jpg', 10, 1582349281, 1),
(50, 972, 0, 1, '[qq_1]', 10, 1582350217, 1),
(51, 972, 0, 1, '111', 10, 1582350254, 1),
(52, 848, 0, 1, '开下', 13, 1582350596, 1),
(53, 952, 0, 1, '[qq_6]', 10, 1582360627, 1),
(54, 1060, 0, 1, '111', 12, 1582389437, 1),
(55, 1060, 0, 1, '2222', 12, 1582389439, 1),
(56, 1060, 0, 1, '3333', 12, 1582389441, 1),
(57, 1060, 0, 1, '5555555', 12, 1582389443, 1),
(58, 1060, 0, 1, '11111111111', 12, 1582389448, 1),
(59, 1060, 0, 1, '[qq_11]', 12, 1582389476, 1),
(60, 1060, 0, 1, '[qq_12][qq_12][qq_12][qq_12][qq_12]', 12, 1582389479, 1),
(61, 1060, 0, 1, '[qq_20][qq_20]', 12, 1582389496, 1),
(62, 1060, 0, 1, '111', 12, 1582389514, 1),
(63, 1060, 0, 1, '33333333333', 12, 1582389524, 1),
(64, 1060, 0, 1, '11111111', 12, 1582389575, 1),
(65, 65, 0, 1, 'wer', 10, 1582433799, 1),
(66, 65, 0, 1, '1', 10, 1582434761, 1);

-- --------------------------------------------------------

--
-- 表的结构 `eb_live_honoured_guest`
--

CREATE TABLE `eb_live_honoured_guest` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '嘉宾用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '嘉宾类型 0=助教，1=讲师',
  `wechat_name` varchar(64) DEFAULT NULL COMMENT '微信昵称',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '嘉宾别昵称',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加事件'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播间嘉宾';

-- --------------------------------------------------------

--
-- 表的结构 `eb_live_studio`
--

CREATE TABLE `eb_live_studio` (
  `id` int(11) NOT NULL,
  `stream_name` varchar(32) NOT NULL DEFAULT '' COMMENT '直播间号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `live_title` varchar(255) NOT NULL DEFAULT '' COMMENT '直播间标题',
  `live_image` varchar(255) NOT NULL DEFAULT '' COMMENT '直播间封面',
  `start_play_time` varchar(30) NOT NULL DEFAULT '0' COMMENT '每日开播时间',
  `stop_play_time` varchar(30) NOT NULL DEFAULT '0' COMMENT '每日停播时间',
  `live_introduction` varchar(3000) NOT NULL DEFAULT '' COMMENT '直播间简介',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联专题id',
  `online_user_num` int(11) NOT NULL DEFAULT '0' COMMENT '在线人数',
  `online_num` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟在线人数',
  `studio_pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '直播间密码',
  `is_remind` tinyint(1) NOT NULL DEFAULT '0' COMMENT '开播提醒',
  `remind_time` int(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '开播前提醒时间（分钟）',
  `auto_phrase` varchar(255) NOT NULL DEFAULT '' COMMENT '进入直播间自动回复',
  `add_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_recording` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否录制直播间',
  `is_play` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否正在开播',
  `recording` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否录制进行中',
  `record_time` int(11) NOT NULL DEFAULT '0' COMMENT '录制开始时间',
  `is_playback` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否回放',
  `playback_record_id` varchar(1000) NOT NULL DEFAULT '' COMMENT '回放RecordId',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播间';

--
-- 转存表中的数据 `eb_live_studio`
--

INSERT INTO `eb_live_studio`(`id`, `stream_name`, `uid`, `live_title`, `live_image`, `start_play_time`, `stop_play_time`, `live_introduction`, `special_id`, `online_user_num`, `online_num`, `studio_pwd`, `is_remind`, `remind_time`, `auto_phrase`, `add_time`, `is_del`, `is_recording`, `is_play`, `recording`, `record_time`, `is_playback`, `playback_record_id`, `sort`) VALUES
(14, '66396889', 0, '直播国学大讲堂', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '2020-04-28 11:22:57', '2020-04-28 12:22:57', '直播国学大讲堂', 1, 0, 0, '', 0, 0, '国学大讲堂', 1588044359, 0, 0, 0, 0, 0, 0, '', 0);


-- --------------------------------------------------------

--
-- 表的结构 `eb_live_user`
--

CREATE TABLE `eb_live_user` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播间id',
  `add_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '首次浏览ip',
  `last_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '历史访问id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '首次访问时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '历史访问时间',
  `visit_num` int(11) NOT NULL DEFAULT '0' COMMENT '访问次数',
  `is_online` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在线 1=在线，0=下线',
  `is_ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁言，1=禁言，0=自由',
  `is_open_ben` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁止进入直播间 1=禁止，0=自由',
  `ban_time` int(11) NOT NULL DEFAULT '0' COMMENT '禁言时间',
  `open_ben_time` int(11) NOT NULL DEFAULT '0' COMMENT '禁止进入直播间时间',
  `is_follow` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否关注'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播间用户管理';

-- --------------------------------------------------------

--
-- 表的结构 `eb_phone_user`
--

CREATE TABLE `eb_phone_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `phone` varchar(11) NOT NULL DEFAULT '' COMMENT '手机号码',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户头像',
  `nickname` varchar(255) NOT NULL DEFAULT '' COMMENT '用户昵称',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT 'user用户id,不关联为0',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级id',
  `new_money` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `level` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户等级',
  `add_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '添加ip',
  `pwd` varchar(32) NOT NULL DEFAULT '' COMMENT '登录密码',
  `spread_uid` int(11) NOT NULL DEFAULT '0' COMMENT '推广员id',
  `is_promoter` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为推广员',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `last_time` int(11) NOT NULL DEFAULT '0' COMMENT '登录时间',
  `last_ip` varchar(16) NOT NULL DEFAULT '' COMMENT '登录id',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否禁止,0=禁止,1=开启'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='手机号码注册用户表';


--
-- 表的结构 `eb_recommend`
--

CREATE TABLE `eb_recommend` (
  `id` int(11) NOT NULL,
  `typesetting` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1=大图,2=宫图,3=小图,4=纯图',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '自定义跳转路径',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型0=专题,1=新闻,2=直播,3=自定义跳转',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `relation_ids` varchar(1000) NOT NULL DEFAULT '' COMMENT '关联id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `icon` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_fixed` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为固定排版',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否上架',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级id',
  `show_count` int(11) NOT NULL DEFAULT '0' COMMENT '展示多少个'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页推荐';

--
-- 转存表中的数据 `eb_recommend`
--

INSERT INTO `eb_recommend` (`id`, `typesetting`, `title`, `link`, `type`, `number`, `relation_ids`, `add_time`, `icon`, `image`, `sort`, `is_fixed`, `is_show`, `grade_id`, `show_count`) VALUES
(18, 0, '小学', '', 0, 0, '', 1581415908, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/a3248202002111757415453.png', '', 10, 1, 1, 4, 0),
(19, 1, '初中', '', 0, 0, '', 1581415944, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/12017202002111757416996.png', '', 9, 1, 1, 3, 0),
(20, 4, '高中', '', 0, 0, '', 1581416083, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/55bd0202002111757421468.png', '', 8, 1, 1, 2, 0),
(21, 2, '少儿编程', '', 0, 0, '', 1581416143, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/022b1202002111757413298.png', '', 7, 1, 1, 1, 0),
(22, 3, '绘画', '', 0, 0, '', 1581416164, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/09bd3202002111757426798.png', '', 6, 1, 1, 5, 0),
(23, 1, '公考', '', 0, 0, '', 1581416198, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/fb05a202002111757415258.png', '', 5, 1, 1, 6, 0),
(24, 3, '小语种', '', 0, 0, '', 1581416219, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/e20b0202002111757424191.png', '', 4, 1, 1, 7, 0),
(25, 3, '考研', '', 0, 0, '', 1581416246, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8cf7c202002111757418051.png', '', 3, 1, 1, 8, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_recommend_banner`
--

CREATE TABLE `eb_recommend_banner` (
  `id` int(11) NOT NULL,
  `recommend_id` int(11) NOT NULL DEFAULT '0' COMMENT '推荐id',
  `pic` varchar(300) NOT NULL DEFAULT '' COMMENT '图片链接',
  `url` varchar(300) NOT NULL DEFAULT '' COMMENT '跳转地址',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='首页推荐banner';

--
-- 转存表中的数据 `eb_recommend_banner`
--

INSERT INTO `eb_recommend_banner` (`id`, `recommend_id`, `pic`, `url`, `sort`, `is_show`, `add_time`) VALUES
(6, 26, 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', '#', 0, 1, 1581924394);

-- --------------------------------------------------------

--
-- 表的结构 `eb_recommend_relation`
--

CREATE TABLE `eb_recommend_relation` (
  `id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型,0=专题,1=新闻，2=直播，3=活动',
  `recommend_id` int(11) NOT NULL COMMENT '推荐id',
  `link_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='主页推荐关联表';

--
-- 转存表中的数据 `eb_recommend_relation`
--

INSERT INTO `eb_recommend_relation` (`id`, `type`, `recommend_id`, `link_id`, `sort`, `add_time`) VALUES
(111, 0, 31, 1, 0, 1588047108),
(112, 0, 26, 2, 0, 1588047166),
(113, 0, 17, 3, 0, 1588047184),
(114, 0, 26, 3, 0, 1588047194),
(115, 0, 17, 4, 0, 1588047207),
(116, 0, 26, 5, 0, 1588047333),
(117, 0, 17, 5, 0, 1588047341);

-- --------------------------------------------------------

--
-- 表的结构 `eb_routine_access_token`
--

CREATE TABLE `eb_routine_access_token` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '小程序access_token表ID',
  `access_token` varchar(256) NOT NULL COMMENT 'openid',
  `stop_time` int(11) UNSIGNED NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序access_token表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_routine_form_id`
--

CREATE TABLE `eb_routine_form_id` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '表单ID表ID',
  `uid` int(11) DEFAULT '0' COMMENT '用户uid',
  `form_id` varchar(32) NOT NULL COMMENT '表单ID',
  `stop_time` int(11) UNSIGNED DEFAULT NULL COMMENT '表单ID失效时间',
  `status` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '状态1 未使用 2不能使用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='表单id表记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_routine_template`
--

CREATE TABLE `eb_routine_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '模板id',
  `tempkey` char(50) NOT NULL COMMENT '模板编号',
  `name` char(100) NOT NULL COMMENT '模板名',
  `content` varchar(1000) NOT NULL COMMENT '回复内容',
  `tempid` char(100) DEFAULT NULL COMMENT '模板ID',
  `add_time` varchar(15) NOT NULL COMMENT '添加时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信模板';

--
-- 转存表中的数据 `eb_routine_template`
--

INSERT INTO `eb_routine_template` (`id`, `tempkey`, `name`, `content`, `tempid`, `add_time`, `status`) VALUES
(12, 'AT0177', '订单配送通知', '订单编号{{keyword1.DATA}}\n配送员{{keyword2.DATA}}\n联系电话{{keyword3.DATA}}\n配送时间{{keyword4.DATA}}\n备注{{keyword5.DATA}}', 'PPDexF5trJG2H9XuEcfrcV6V0Nk1X9S0mWDva_bEjF8', '1534469109', 1),
(13, 'AT0007', '订单发货提醒', '订单号{{keyword1.DATA}}\n快递公司{{keyword2.DATA}}\n快递单号{{keyword3.DATA}}\n发货时间{{keyword4.DATA}}\n备注{{keyword5.DATA}}', 'rgb_ATfivS_vx7u6AnYXpQH-BxIABq8yJpMnGwhAzaE', '1534469928', 0),
(14, 'AT0787', '退款成功通知', '订单号{{keyword1.DATA}}\n退款时间{{keyword2.DATA}}\n退款金额{{keyword3.DATA}}\n退款方式{{keyword4.DATA}}\n备注{{keyword5.DATA}}', 'g3Fu-ljOUwtNHo7ISRNovemB5LIDsPfgiYUukwadymA', '1534469993', 1),
(15, 'AT0009', '订单支付成功通知', '单号{{keyword1.DATA}}\n下单时间{{keyword2.DATA}}\n订单状态{{keyword3.DATA}}\n支付金额{{keyword4.DATA}}\n支付方式{{keyword5.DATA}}', '6HxCNpsDXqhzGd_7QioQgWCJlIgzmLbcoE2d8uluEaQ', '1534470043', 1),
(16, 'AT1173', '砍价成功通知', '商品名称{{keyword1.DATA}}\n砍价金额{{keyword2.DATA}}\n底价{{keyword3.DATA}}\n砍掉价格{{keyword4.DATA}}\n支付金额{{keyword5.DATA}}\n备注{{keyword6.DATA}}', 'DCt_nV9uo2bJdGMg19zr6ao_x8MGo3lRX6BqsVFY2zw', '1534470085', 0),
(17, 'AT0036', '退款通知', '订单编号{{keyword1.DATA}}\n退款原因{{keyword2.DATA}}\n退款时间{{keyword3.DATA}}\n退款金额{{keyword4.DATA}}\n退款方式{{keyword5.DATA}}', 'kJ5jTVzV4ecNUDucKUATjBOIKJVzrPAm9peXYD13BxY', '1534470134', 1),
(18, 'AT0035', '收益到账通知', '订单号{{keyword1.DATA}}\n商品名称{{keyword2.DATA}}\n金额{{keyword3.DATA}}\n收益来源{{keyword4.DATA}}', 'OaCSnkkFi7GFomOCTCDe5w9qaNAWS2REAVZ3epWlxA0', '1541814979', 1),
(19, 'AT0146', '审核结果通知', '审核结果{{keyword1.DATA}}\r\n审核人员{{keyword2.DATA}}\r\n联系电话{{keyword3.DATA}}\r\n审核时间{{keyword4.DATA}}\r\n备注{{keyword5.DATA}}\r\n申请用户{{keyword6.DATA}}\r\n未通过原因{{keyword7.DATA}}', 'wzc5lXn4llgOgX7zH7l90rh4szlPZdjYPRTRo5OnzTs', '1543545827', 1);

-- --------------------------------------------------------

--
-- 表的结构 `eb_search`
--

CREATE TABLE `eb_search` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '关键词名',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='关键词搜索设置';

--
-- 转存表中的数据 `eb_search`
--

INSERT INTO `eb_search` (`id`, `name`, `add_time`) VALUES
(29, '语文', 1581476333),
(30, '数学', 1581476338),
(31, '英语', 1581476343);

-- --------------------------------------------------------

--
-- 表的结构 `eb_sms_code`
--

CREATE TABLE `eb_sms_code` (
  `id` int(5) NOT NULL COMMENT 'id',
  `tel` varchar(20) NOT NULL COMMENT '电话号码',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '谁发送的',
  `code` int(8) UNSIGNED NOT NULL COMMENT '验证码',
  `last_time` int(11) UNSIGNED NOT NULL COMMENT '过期时间',
  `is_use` int(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否已使用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `eb_special`
--

CREATE TABLE `eb_special` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
  `subject_id` int(11) NOT NULL DEFAULT '0' COMMENT '科目id',
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '所属管理者id',
   `type` tinyint(2) NOT NULL DEFAULT '6' COMMENT '专题类型1：图文专题；2：音频专题；3：视频专题；4：直播专题；5：专栏；6：其他专题',
  `abstract` varchar(1000) NOT NULL DEFAULT '' COMMENT '简介',
  `phrase` varchar(2000) NOT NULL DEFAULT '' COMMENT '短语',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `label` varchar(1000) NOT NULL DEFAULT '' COMMENT '标签',
  `banner` varchar(2000) NOT NULL DEFAULT '' COMMENT 'banner图',
  `poster_image` varchar(255) NOT NULL DEFAULT '' COMMENT '海报',
  `service_code` varchar(255) NOT NULL DEFAULT '' COMMENT '客服二维码',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否显示',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_live` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为直播',
  `money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `pink_money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '拼团价格',
  `is_pink` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否开启拼团',
  `pink_number` int(11) NOT NULL DEFAULT '0' COMMENT '拼团人数',
  `pink_strar_time` int(11) NOT NULL DEFAULT '0' COMMENT '拼团开始时间',
  `pink_end_time` int(11) NOT NULL DEFAULT '0' COMMENT '拼团结束时间',
  `pink_time` int(11) NOT NULL DEFAULT '0' COMMENT '拼团时间',
  `is_fake_pink` tinyint(1) NOT NULL DEFAULT '0' COMMENT '虚拟拼团是否开启',
  `fake_pink_number` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟拼团人数',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` int(11) NOT NULL DEFAULT '0' COMMENT '实际销量',
  `fake_sales` int(11) NOT NULL DEFAULT '0' COMMENT '虚拟销量',
  `browse_count` int(11) NOT NULL DEFAULT '0' COMMENT '浏览量',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `pay_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支付类型',
  `member_pay_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员支付类型0：免费，1付费',
  `member_money` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `link` varchar(512) NOT NULL DEFAULT '' COMMENT '音频视频专题链接'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专题';

--
-- 转存表中的数据 `eb_special`
--
INSERT INTO `eb_special`(`id`, `title`, `subject_id`, `admin_id`, `type`, `abstract`, `phrase`, `image`, `label`, `banner`, `poster_image`, `service_code`, `is_show`, `is_del`, `is_live`, `money`, `pink_money`, `is_pink`, `pink_number`, `pink_strar_time`, `pink_end_time`, `pink_time`, `is_fake_pink`, `fake_pink_number`, `sort`, `sales`, `fake_sales`, `browse_count`, `add_time`, `pay_type`, `member_pay_type`, `member_money`, `link`) VALUES
(1, '直播国学大讲堂', 21, 0, 4, '直播国学大讲堂', '直播国学大讲堂', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '[\"\\u56fd\\u5b66\\u8bb2\\u5802\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/ce99d202002201232432554.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 1, 0, 0, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1588044359, 0, 0, 0.00, ''),
(2, '图文专题', 21, 0, 1, '图文专题简介', '图文专题短语', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', '[\"\\u56fe\\u6587\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/f4313202002201232435004.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 1, 0, 0, 0.50, 0.00, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 13, 1588045424, 1, 1, 0.20, ''),
(3, '音频专题', 22, 0, 2, '音频专题简介', '音频专题', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', '[\"\\u97f3\\u9891\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/8ab46202002201232152644.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 1, 0, 0, 0.20, 0.00, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1588046472, 1, 0, 0.00, ''),
(4, '视频专题', 23, 0, 3, '视频专题', '视频专题', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', '[\"\\u89c6\\u9891\\u4e13\\u9898\"]', '[\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/9ec16202002121807113099.png\"]', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', 1, 0, 0, 0.00, 0.00, 0, 0, 0, 0, 0, 0, 0, 3, 0, 0, 1, 1588046648, 0, 0, 0.00, ''),
(5, '我是专栏', 21, 0, 5, '我是专栏（我里面包含专题）', '我是专栏', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '[\"\\u4e13\\u680f\\u4e13\\u9898\"]', '[\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/32379202002201232449232.jpg\"]', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 1, 0, 0, 1.00, 0.30, 1, 2, 1588003200, 1588176000, 100, 1, 50, 0, 0, 0, 2, 1588046788, 1, 1, 0.50, '');

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_barrage`
--

CREATE TABLE `eb_special_barrage` (
  `id` int(11) NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id 0=全部可用',
  `nickname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '用户昵称',
  `avatar` varchar(255) NOT NULL DEFAULT '' COMMENT '用户图像',
  `action` tinyint(1) NOT NULL DEFAULT '0' COMMENT '动作类型 1=开团，2=参团',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示，默认显示',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='专题弹幕';

--
-- 转存表中的数据 `eb_special_barrage`
--

INSERT INTO `eb_special_barrage` (`id`, `special_id`, `nickname`, `avatar`, `action`, `sort`, `is_show`, `add_time`) VALUES
(24, 0, '666', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 1, 0, 1, 1582011509),
(25, 0, '666', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 1, 0, 1, 1582011520),
(26, 0, 'yy', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', 1, 0, 1, 1582018672);

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_buy`
--

CREATE TABLE `eb_special_buy` (
  `id` int(11) NOT NULL,
  `order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '订单id',
  `special_id` int(11) NOT NULL COMMENT '专题id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_del` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '获得方式:0=支付获得,1=拼团获得,2=领取礼物获得,3=赠送'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专题获得记录';

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_content`
--

CREATE TABLE `eb_special_content` (
  `id` int(11) NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专栏id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `content` text NOT NULL COMMENT '内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专栏内容关联表';

--
-- 转存表中的数据 `eb_special_content`
--

INSERT INTO `eb_special_content` (`id`, `special_id`, `add_time`, `content`) VALUES
(1, 1, 1588044359, '&lt;p&gt;&lt;br/&gt;&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;直播简介&lt;br/&gt;&lt;/p&gt;'),
(2, 2, 1588045424, '&lt;p&gt;专题内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;'),
(3, 3, 1588046472, '&lt;p&gt;内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;'),
(4, 4, 1588046648, '&lt;p&gt;内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;'),
(5, 5, 1588046788, '&lt;p&gt;专栏内容&lt;/p&gt;&lt;div&gt;&lt;video style=&quot;width: 100%&quot; src=&quot;http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4&quot; class=&quot;video-ue&quot; controls=&quot;controls&quot;&gt;&lt;/video&gt;&lt;/div&gt;&lt;p&gt;&lt;br/&gt;&lt;/p&gt;');


-- --------------------------------------------------------

--
-- 表的结构 `eb_special_course`
--

CREATE TABLE `eb_special_course` (
  `id` int(11) NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `course_name` varchar(100) NOT NULL DEFAULT '' COMMENT '课程名称',
  `is_show` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否上架',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '数量',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程(1.1版本后废弃)';

-- --------------------------------------------------------
--
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

-- --------------------------------------------------------
--
-- 表的结构 `eb_special_record`
--

CREATE TABLE `eb_special_record` (
  `id` int(11) NOT NULL,
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '浏览次数',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(11) NOT NULL DEFAULT '0' COMMENT '再次访问时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学习记录';
-- --------------------------------------------------------
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
  `update_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡批次表';
-- --------------------------------------------------------
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
  `update_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员卡表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_relation`
--

CREATE TABLE `eb_special_relation` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=专题,1=图文',
  `link_id` int(11) NOT NULL DEFAULT '0' COMMENT '关联id',
  `category` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型,1=收藏,2=点赞',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图文专题点赞收藏';

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_subject`
--

CREATE TABLE `eb_special_subject` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `pic` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_show` int(11) NOT NULL DEFAULT '0' COMMENT '是否显示,1=显示,0=隐藏',
  `grade_id` int(11) NOT NULL DEFAULT '0' COMMENT '年级部id',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='科目列表';

--
-- 转存表中的数据 `eb_special_subject`
--

INSERT INTO `eb_special_subject` (`id`, `name`, `pic`, `sort`, `is_show`, `grade_id`, `add_time`) VALUES
(21, '语文', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', 10, 1, 4, 1581401495),
(22, '数学', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', 9, 1, 4, 1581415097),
(23, '英语', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', 8, 1, 3, 1581415148),
(24, '音乐启蒙', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', 0, 1, 4, 1581476723),
(25, '体育训练', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', 0, 1, 4, 1581476740),
(26, '课外辅导', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', 0, 1, 4, 1581476755),
(27, '野外拓展', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', 0, 1, 4, 1581476777),
(28, '精品提升', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', 0, 1, 4, 1581476806),
(30, '二级测试', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/ccb10202002111757423753.jpg', 0, 1, 10, 1581674369);

-- --------------------------------------------------------

--
-- 表的结构 `eb_special_task`
--

CREATE TABLE `eb_special_task` (
  `id` int(11) NOT NULL,
  `coures_id` int(11) NOT NULL DEFAULT '0' COMMENT '课程id（1.1版本后废弃）',
  `special_id` int(11) NOT NULL DEFAULT '0' COMMENT '专题id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '素材标题',
  `content` text NOT NULL COMMENT '内容',
  `detail` text NOT NULL COMMENT '简介',
  `type` tinyint(1) NOT NULL DEFAULT '6' COMMENT '素材类型',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否付费,0=免费,1=付费',
  `link` varchar(255) NOT NULL DEFAULT '' COMMENT '链接',
  `image` varchar(200) NOT NULL DEFAULT '0' COMMENT '封面图',
  `abstract` varchar(2000) NOT NULL DEFAULT '' COMMENT '摘要',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `play_count` int(11) NOT NULL DEFAULT '0' COMMENT '播放量',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=下架,1=上架',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `edit_time` int(11) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `live_id` int(11) NOT NULL DEFAULT '0' COMMENT '直播ID，0=普通（1.1版本后废弃）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专题-素材';

--
-- 转存表中的数据 `eb_special_task`
--
INSERT INTO `eb_special_task`(`id`, `coures_id`, `special_id`, `title`, `content`, `detail`, `type`, `is_pay`, `link`, `image`, `abstract`, `sort`, `play_count`, `is_show`, `add_time`, `edit_time`, `live_id`) VALUES
(1, 0, 0, '图文素材1', '<p>我是图文素材1内容</p>', '<p>我是图文素材1简介</p>', 1, 0, '', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '', 1, 0, 1, 1588042168, 0, 0),
(2, 0, 0, '图文素材2', '<p>我是图文素材2内容</p>', '<p>我是图文素材2简介</p>', 1, 0, '', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '', 2, 0, 1, 1588042210, 0, 0),
(3, 0, 0, '音频素材一', '<p><br/></p><div><audio style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></audio></div><p>音频</p>', '<p>我是音频素材1简介</p>', 2, 0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', '', 1, 0, 1, 1588043711, 0, 0),
(4, 0, 0, '音频素材二', '<p><br/></p><audio style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></audio><p>我是音频素材内容</p>', '<p>我是音频素材二简介</p>', 2, 0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', '', 2, 0, 1, 1588043762, 0, 0),
(5, 0, 0, '视频素材一', '<p><br/></p><div><video style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></video></div><p>我是视频素材一内容</p>', '<p>我是视频素材一简介</p>', 3, 0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', '', 1, 0, 1, 1588043857, 0, 0),
(6, 0, 0, '视频素材二', '<p><br/></p><div><video style=\"width: 100%\" src=\"http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4\" class=\"video-ue\" controls=\"controls\"></video></div><p>我是视频素材2内容</p>', '<p>我是视频素材2简介</p>', 3, 0, 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/W8rykksS4m6ZtYp27E.mp4', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', '', 2, 0, 1, 1588043914, 0, 0);

--
-- 转存表中的数据 `eb_special_source`
--
INSERT INTO `eb_special_source` (`id`, `special_id`, `source_id`, `pay_status`, `play_count`, `add_time`) VALUES
(1, 2, 2, 1, 0, 1588045424),
(2, 2, 1, 0, 0, 1588045424),
(3, 3, 4, 0, 0, 1588046472),
(4, 3, 3, 0, 0, 1588046472),
(5, 4, 6, 1, 0, 1588046648),
(6, 4, 5, 0, 0, 1588046648),
(7, 5, 4, 0, 0, 1588046788),
(8, 5, 2, 0, 0, 1588046788),
(9, 5, 3, 0, 0, 1588046788);

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_bargain`
--

CREATE TABLE `eb_store_bargain` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '砍价产品ID',
  `product_id` int(11) UNSIGNED NOT NULL COMMENT '关联产品ID',
  `title` varchar(255) NOT NULL COMMENT '砍价活动名称',
  `image` varchar(150) NOT NULL COMMENT '砍价活动图片',
  `unit_name` varchar(16) DEFAULT NULL COMMENT '单位名称',
  `stock` int(11) UNSIGNED DEFAULT NULL COMMENT '库存',
  `sales` int(11) UNSIGNED DEFAULT NULL COMMENT '销量',
  `images` varchar(1000) NOT NULL COMMENT '砍价产品轮播图',
  `start_time` int(11) UNSIGNED NOT NULL COMMENT '砍价开启时间',
  `stop_time` int(11) UNSIGNED NOT NULL COMMENT '砍价结束时间',
  `store_name` varchar(255) DEFAULT NULL COMMENT '砍价产品名称',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价金额',
  `min_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价商品最低价',
  `num` int(11) UNSIGNED DEFAULT NULL COMMENT '每次购买的砍价产品数量',
  `bargain_max_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '用户每次砍价的最大金额',
  `bargain_min_price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '用户每次砍价的最小金额',
  `bargain_num` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '用户每次砍价的次数',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '砍价状态 0(到砍价时间不自动开启)  1(到砍价时间自动开启时间)',
  `description` text COMMENT '砍价详情',
  `give_integral` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '反多少积分',
  `info` varchar(255) DEFAULT NULL COMMENT '砍价活动简介',
  `cost` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '成本价',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `is_hot` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否推荐0不推荐1推荐',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除 0未删除 1删除',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '添加时间',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否包邮 0不包邮 1包邮',
  `postage` decimal(10,2) UNSIGNED DEFAULT NULL COMMENT '邮费',
  `rule` text COMMENT '砍价规则',
  `look` int(11) UNSIGNED DEFAULT '0' COMMENT '砍价产品浏览量',
  `share` int(11) UNSIGNED DEFAULT '0' COMMENT '砍价产品分享量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_bargain_user`
--

CREATE TABLE `eb_store_bargain_user` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '用户参与砍价表ID',
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '用户ID',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品id',
  `bargain_price_min` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍价的最低价',
  `bargain_price` decimal(8,2) DEFAULT NULL COMMENT '砍价金额',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '砍掉的价格',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态 1参与中 2 活动结束参与失败 3活动结束参与成功',
  `add_time` int(11) UNSIGNED DEFAULT NULL COMMENT '参与时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户参与砍价表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_bargain_user_help`
--

CREATE TABLE `eb_store_bargain_user_help` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '砍价用户帮助表ID',
  `uid` int(11) UNSIGNED DEFAULT NULL COMMENT '帮助的用户id',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品ID',
  `bargain_user_id` int(11) UNSIGNED DEFAULT NULL COMMENT '用户参与砍价表id',
  `price` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '帮助砍价多少金额',
  `add_time` int(11) UNSIGNED DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='砍价用户帮助表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_cart`
--

CREATE TABLE `eb_store_cart` (
  `id` bigint(8) UNSIGNED NOT NULL COMMENT '购物车表ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `type` varchar(32) NOT NULL COMMENT '类型',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `product_attr_unique` varchar(16) NOT NULL DEFAULT '' COMMENT '商品属性',
  `cart_num` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品数量',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间',
  `is_pay` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = 未购买 1 = 已购买',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为立即购买',
  `combination_id` int(11) UNSIGNED NOT NULL COMMENT '拼团id',
  `seckill_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '秒杀产品ID',
  `bargain_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '砍价id',
  `integral_id` int(11) DEFAULT '0' COMMENT '积分id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物车表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_category`
--

CREATE TABLE `eb_store_category` (
  `id` mediumint(11) NOT NULL COMMENT '商品分类表ID',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户id',
  `pid` mediumint(11) NOT NULL COMMENT '父id',
  `cate_name` varchar(100) NOT NULL COMMENT '分类名称',
  `sort` mediumint(11) NOT NULL COMMENT '排序',
  `pic` varchar(128) NOT NULL DEFAULT '' COMMENT '图标',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否推荐',
  `add_time` int(11) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品分类表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination`
--

CREATE TABLE `eb_store_combination` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `mer_id` int(10) UNSIGNED DEFAULT '0' COMMENT '商户id',
  `image` varchar(255) NOT NULL COMMENT '推荐图',
  `images` varchar(1000) NOT NULL COMMENT '轮播图',
  `title` varchar(255) NOT NULL COMMENT '活动标题',
  `attr` varchar(255) NOT NULL COMMENT '活动属性',
  `people` int(2) UNSIGNED NOT NULL COMMENT '参团人数',
  `info` varchar(255) NOT NULL COMMENT '简介',
  `price` decimal(10,2) UNSIGNED NOT NULL COMMENT '价格',
  `sort` int(10) UNSIGNED NOT NULL COMMENT '排序',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `stock` int(10) UNSIGNED NOT NULL COMMENT '库存',
  `add_time` varchar(128) NOT NULL COMMENT '添加时间',
  `is_host` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '推荐',
  `is_show` tinyint(1) UNSIGNED NOT NULL COMMENT '产品状态',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `combination` tinyint(1) UNSIGNED NOT NULL DEFAULT '1',
  `mer_use` tinyint(1) UNSIGNED NOT NULL COMMENT '商户是否可用1可用0不可用',
  `is_postage` tinyint(1) UNSIGNED NOT NULL COMMENT '是否包邮1是0否',
  `postage` decimal(10,2) UNSIGNED NOT NULL COMMENT '邮费',
  `description` text NOT NULL COMMENT '拼团内容',
  `start_time` int(11) UNSIGNED NOT NULL COMMENT '拼团开始时间',
  `stop_time` int(11) UNSIGNED NOT NULL COMMENT '拼团结束时间',
  `cost` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '拼图产品成本',
  `browse` int(11) DEFAULT '0' COMMENT '浏览量'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼团产品表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr`
--

CREATE TABLE `eb_store_combination_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr_result`
--

CREATE TABLE `eb_store_combination_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `result` text NOT NULL COMMENT '商品属性参数',
  `change_time` int(10) UNSIGNED NOT NULL COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_combination_attr_value`
--

CREATE TABLE `eb_store_combination_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `suk` varchar(128) NOT NULL COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL COMMENT '属性金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon`
--

CREATE TABLE `eb_store_coupon` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '优惠券表ID',
  `title` varchar(64) NOT NULL COMMENT '优惠券名称',
  `integral` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换消耗积分值',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '兑换的优惠券面值',
  `use_min_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低消费多少金额可用优惠券',
  `coupon_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券有效期限（单位：天）',
  `sort` int(11) UNSIGNED NOT NULL DEFAULT '1' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '状态（0：关闭，1：开启）',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '兑换项目添加时间',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_issue`
--

CREATE TABLE `eb_store_coupon_issue` (
  `id` int(10) UNSIGNED NOT NULL,
  `cid` int(10) DEFAULT NULL COMMENT '优惠券ID',
  `start_time` int(10) DEFAULT NULL COMMENT '优惠券领取开启时间',
  `end_time` int(10) DEFAULT NULL COMMENT '优惠券领取结束时间',
  `total_count` int(10) DEFAULT NULL COMMENT '优惠券领取数量',
  `remain_count` int(10) DEFAULT NULL COMMENT '优惠券剩余领取数量',
  `is_permanent` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否无限张数',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1 正常 0 未开启 -1 已无效',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `add_time` int(10) DEFAULT NULL COMMENT '优惠券添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券前台领取表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_issue_user`
--

CREATE TABLE `eb_store_coupon_issue_user` (
  `uid` int(10) DEFAULT NULL COMMENT '领取优惠券用户ID',
  `issue_coupon_id` int(10) DEFAULT NULL COMMENT '优惠券前台领取ID',
  `add_time` int(10) DEFAULT NULL COMMENT '领取时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券前台用户领取记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_coupon_user`
--

CREATE TABLE `eb_store_coupon_user` (
  `id` int(11) NOT NULL COMMENT '优惠券发放记录id',
  `cid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '兑换的项目id',
  `uid` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券所属用户',
  `coupon_title` varchar(32) NOT NULL COMMENT '优惠券名称',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '优惠券的面值',
  `use_min_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '最低消费多少金额可用优惠券',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '优惠券创建时间',
  `end_time` int(11) UNSIGNED NOT NULL COMMENT '优惠券结束时间',
  `use_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '使用时间',
  `type` varchar(32) NOT NULL DEFAULT 'send' COMMENT '获取方式',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态（0：未使用，1：已使用, 2:已过期）',
  `is_fail` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否有效'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券发放记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order`
--

CREATE TABLE `eb_store_order` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '订单ID',
  `trade_no` varchar(50) NOT NULL DEFAULT '' COMMENT '支付宝支付,支付宝交易订单号',
  `order_id` varchar(32) NOT NULL COMMENT '订单号',
  `uid` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `real_name` varchar(32) NOT NULL COMMENT '用户姓名',
  `user_phone` varchar(18) NOT NULL COMMENT '用户电话',
  `user_address` varchar(100) NOT NULL COMMENT '详细地址',
  `cart_id` varchar(256) NOT NULL DEFAULT '[]' COMMENT '购物车id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单类别 （0:课程订单 1:会员订单）',
  `member_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员期限ID',
  `total_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '订单商品总数',
  `total_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '订单总价',
  `total_postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `pay_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '实际支付金额',
  `deduction_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '抵扣金额',
  `coupon_id` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '优惠券id',
  `coupon_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '优惠券金额',
  `paid` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '支付状态',
  `pay_time` int(11) UNSIGNED DEFAULT '0' COMMENT '支付时间',
  `pay_type` varchar(32) NOT NULL COMMENT '支付方式',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '创建时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单状态（-1 : 申请退款 -2 : 退货成功 0：待取货；1：配送中；2：已收货；3：待评价；-1：已退款）',
  `refund_status` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 未退款 1 申请中 2 已退款',
  `refund_reason_wap_img` varchar(255) DEFAULT NULL COMMENT '退款图片',
  `refund_reason_wap_explain` varchar(255) DEFAULT NULL COMMENT '退款用户说明',
  `refund_reason_time` int(11) UNSIGNED DEFAULT NULL COMMENT '退款时间',
  `refund_reason_wap` varchar(255) DEFAULT NULL COMMENT '前台退款原因',
  `refund_reason` varchar(255) DEFAULT NULL COMMENT '不退款的理由',
  `refund_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `mark` varchar(512) NOT NULL COMMENT '备注',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `unique` char(32) NOT NULL COMMENT '唯一id(md5加密)类似id',
  `remark` varchar(512) DEFAULT NULL COMMENT '管理员备注',
  `mer_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户ID',
  `cost` decimal(8,2) UNSIGNED NOT NULL COMMENT '成本价',
  `is_channel` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '支付渠道(0微信公众号1微信小程序)',
  `pink_time` int(11) NOT NULL DEFAULT '0' COMMENT '拼团有效时间',
  `combination_id` int(11) UNSIGNED DEFAULT '0' COMMENT '拼团产品id',
  `pink_id` int(11) UNSIGNED DEFAULT '0' COMMENT '开团id',
  `seckill_id` int(11) UNSIGNED DEFAULT NULL COMMENT '秒杀产品id',
  `bargain_id` int(11) UNSIGNED DEFAULT NULL COMMENT '砍价产品id',
  `integral_id` int(11) UNSIGNED DEFAULT NULL,
  `pay_postage` decimal(8,2) DEFAULT '0.00' COMMENT '邮费',
  `delivery_name` varchar(255) DEFAULT NULL COMMENT '快递公司',
  `delivery_type` varchar(255) DEFAULT NULL COMMENT '物流类型',
  `delivery_id` varchar(64) DEFAULT '' COMMENT '快递单号',
  `gain_integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '消费赚取积分',
  `use_integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '使用积分',
  `back_integral` decimal(8,2) UNSIGNED DEFAULT NULL COMMENT '给用户退了多少积分',
  `is_gift` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为礼物订单',
  `gift_count` int(11) NOT NULL DEFAULT '0' COMMENT '领取次数',
  `is_receive_gift` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为领取的礼物订单',
  `gift_order_id` varchar(32) NOT NULL DEFAULT '' COMMENT '购买礼物order_id',
  `link_pay_uid` int(11) NOT NULL DEFAULT '0' COMMENT '通过连接购买所属用户',
  `spread_uid` int(11) NOT NULL DEFAULT '0' COMMENT '推广人uid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order_cart_info`
--

CREATE TABLE `eb_store_order_cart_info` (
  `oid` int(11) UNSIGNED NOT NULL COMMENT '订单id',
  `cart_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '购物车id',
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `cart_info` text NOT NULL COMMENT '购买东西的详细信息',
  `unique` char(32) NOT NULL COMMENT '唯一id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单购物详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_order_status`
--

CREATE TABLE `eb_store_order_status` (
  `oid` int(10) UNSIGNED NOT NULL COMMENT '订单id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=外面订单,1=跑腿订单',
  `change_type` varchar(32) NOT NULL COMMENT '操作类型',
  `change_message` varchar(256) NOT NULL COMMENT '操作备注',
  `change_time` int(10) UNSIGNED NOT NULL COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_pink`
--

CREATE TABLE `eb_store_pink` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '拼团产品类型0=商品,1=专题',
  `order_id` varchar(32) NOT NULL COMMENT '订单id 生成',
  `order_id_key` int(10) UNSIGNED NOT NULL COMMENT '订单id  数据库',
  `total_num` int(10) UNSIGNED NOT NULL COMMENT '购买商品个数',
  `total_price` decimal(10,2) UNSIGNED NOT NULL COMMENT '购买总金额',
  `cid` int(10) UNSIGNED NOT NULL COMMENT '拼团产品id',
  `pid` int(10) UNSIGNED NOT NULL COMMENT '产品id',
  `people` int(10) UNSIGNED NOT NULL COMMENT '拼图总人数',
  `price` decimal(10,2) UNSIGNED NOT NULL COMMENT '拼团产品单价',
  `add_time` varchar(24) NOT NULL COMMENT '开始时间',
  `stop_time` varchar(24) NOT NULL,
  `k_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '团长id 0为团长',
  `is_tpl` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否发送模板消息0未发送1已发送',
  `is_refund` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否退款 0未退款 1已退款',
  `is_false` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为虚拟拼团',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态1进行中2已完成3未完成'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='拼团表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_pink_false`
--

CREATE TABLE `eb_store_pink_false` (
  `id` int(11) NOT NULL,
  `pink_id` int(11) NOT NULL DEFAULT '0' COMMENT '拼团id',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `avatar` varchar(225) NOT NULL DEFAULT '' COMMENT '头像',
  `add_time` int(11) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='虚拟参团人员';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product`
--

CREATE TABLE `eb_store_product` (
  `id` mediumint(11) NOT NULL COMMENT '商品id',
  `mer_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户Id(0为总后台管理员创建,不为0的时候是商户后台创建)',
  `image` varchar(128) NOT NULL COMMENT '商品图片',
  `slider_image` varchar(512) NOT NULL COMMENT '轮播图',
  `store_name` varchar(128) NOT NULL COMMENT '商品名称',
  `store_info` varchar(256) NOT NULL COMMENT '商品简介',
  `keyword` varchar(256) NOT NULL COMMENT '关键字',
  `cate_id` varchar(64) NOT NULL COMMENT '分类id',
  `price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '商品价格',
  `vip_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '会员价格',
  `ot_price` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '市场价',
  `postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `unit_name` varchar(32) NOT NULL COMMENT '单位名',
  `sort` smallint(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `sales` mediumint(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `stock` mediumint(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '库存',
  `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（0：未上架，1：上架）',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否热卖',
  `is_benefit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否优惠',
  `is_best` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否精品',
  `is_new` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否新品',
  `description` text NOT NULL COMMENT '产品描述',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '添加时间',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否包邮',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `mer_use` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户是否代理 0不可代理1可代理',
  `give_integral` decimal(8,2) UNSIGNED NOT NULL COMMENT '获得积分',
  `cost` decimal(8,2) UNSIGNED NOT NULL COMMENT '成本价',
  `is_seckill` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '秒杀状态 0 未开启 1已开启',
  `is_bargain` tinyint(1) UNSIGNED NOT NULL COMMENT '砍价状态 0未开启 1开启',
  `ficti` mediumint(11) DEFAULT '100' COMMENT '虚拟销量',
  `browse` int(11) DEFAULT '0' COMMENT '浏览量',
  `code_path` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr`
--

CREATE TABLE `eb_store_product_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr_result`
--

CREATE TABLE `eb_store_product_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `result` text NOT NULL COMMENT '商品属性参数',
  `change_time` int(10) UNSIGNED NOT NULL COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_attr_value`
--

CREATE TABLE `eb_store_product_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `suk` varchar(128) NOT NULL COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL COMMENT '属性金额',
  `vip_price` decimal(9,2) NOT NULL DEFAULT '0.00' COMMENT '会员金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_relation`
--

CREATE TABLE `eb_store_product_relation` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `type` varchar(32) NOT NULL COMMENT '类型(收藏(collect）、点赞(like))',
  `category` varchar(32) NOT NULL COMMENT '某种类型的商品(普通商品、秒杀商品)',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品点赞和收藏表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_product_reply`
--

CREATE TABLE `eb_store_product_reply` (
  `id` int(11) NOT NULL COMMENT '评论ID',
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `oid` int(11) NOT NULL COMMENT '订单ID',
  `unique` char(32) NOT NULL COMMENT '唯一id',
  `product_id` int(11) NOT NULL COMMENT '产品id',
  `reply_type` varchar(32) NOT NULL DEFAULT 'product' COMMENT '某种商品类型(普通商品、秒杀商品）',
  `product_score` tinyint(1) NOT NULL COMMENT '商品分数',
  `service_score` tinyint(1) NOT NULL COMMENT '服务分数',
  `packing_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '包装分数',
  `mer_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '商家评价',
  `delivery_score` tinyint(1) NOT NULL DEFAULT '0' COMMENT '配送速度分数',
  `comment` varchar(512) NOT NULL COMMENT '评论内容',
  `pics` text NOT NULL COMMENT '评论图片',
  `add_time` int(11) NOT NULL COMMENT '评论时间',
  `merchant_reply_content` varchar(300) NOT NULL COMMENT '管理员回复内容',
  `merchant_reply_time` int(11) NOT NULL COMMENT '管理员回复时间',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0未删除1已删除',
  `is_reply` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未回复1已回复'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='评论表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill`
--

CREATE TABLE `eb_store_seckill` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '商品秒杀产品表id',
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品id',
  `image` varchar(255) NOT NULL COMMENT '推荐图',
  `images` varchar(1000) NOT NULL COMMENT '轮播图',
  `title` varchar(255) NOT NULL COMMENT '活动标题',
  `info` varchar(255) NOT NULL COMMENT '简介',
  `price` decimal(10,2) UNSIGNED NOT NULL COMMENT '价格',
  `cost` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '成本',
  `ot_price` decimal(10,2) UNSIGNED NOT NULL COMMENT '原价',
  `give_integral` decimal(10,2) UNSIGNED NOT NULL COMMENT '返多少积分',
  `sort` int(10) UNSIGNED NOT NULL COMMENT '排序',
  `stock` int(10) UNSIGNED NOT NULL COMMENT '库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `unit_name` varchar(16) NOT NULL COMMENT '单位名',
  `postage` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '邮费',
  `description` text NOT NULL COMMENT '内容',
  `start_time` varchar(128) NOT NULL COMMENT '开始时间',
  `stop_time` varchar(128) NOT NULL COMMENT '结束时间',
  `add_time` varchar(128) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '产品状态',
  `is_postage` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否包邮',
  `is_hot` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '热门推荐',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '删除 0未删除1已删除',
  `num` int(11) UNSIGNED NOT NULL COMMENT '最多秒杀几个',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品秒杀产品表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr`
--

CREATE TABLE `eb_store_seckill_attr` (
  `product_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商品ID',
  `attr_name` varchar(32) NOT NULL COMMENT '属性名',
  `attr_values` varchar(256) NOT NULL COMMENT '属性值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='秒杀商品属性表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr_result`
--

CREATE TABLE `eb_store_seckill_attr_result` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `result` text NOT NULL COMMENT '商品属性参数',
  `change_time` int(10) UNSIGNED NOT NULL COMMENT '上次修改时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='秒杀商品属性详情表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_seckill_attr_value`
--

CREATE TABLE `eb_store_seckill_attr_value` (
  `product_id` int(10) UNSIGNED NOT NULL COMMENT '商品ID',
  `suk` varchar(128) NOT NULL COMMENT '商品属性索引值 (attr_value|attr_value[|....])',
  `stock` int(10) UNSIGNED NOT NULL COMMENT '属性对应的库存',
  `sales` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '销量',
  `price` decimal(8,2) UNSIGNED NOT NULL COMMENT '属性金额',
  `image` varchar(128) DEFAULT NULL COMMENT '图片',
  `unique` char(8) NOT NULL DEFAULT '' COMMENT '唯一值',
  `cost` decimal(8,2) UNSIGNED NOT NULL COMMENT '成本价'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='秒杀商品属性值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_service`
--

CREATE TABLE `eb_store_service` (
  `id` int(11) NOT NULL COMMENT '客服id',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户id',
  `uid` int(11) NOT NULL COMMENT '客服uid',
  `avatar` varchar(250) NOT NULL COMMENT '客服头像',
  `notify` int(11) NOT NULL DEFAULT '0',
  `nickname` varchar(50) NOT NULL COMMENT '代理名称',
  `add_time` int(11) NOT NULL COMMENT '添加时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0隐藏1显示'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_service_log`
--

CREATE TABLE `eb_store_service_log` (
  `id` int(11) NOT NULL COMMENT '客服用户对话记录表ID',
  `mer_id` int(11) NOT NULL DEFAULT '0' COMMENT '商户id',
  `msn` text NOT NULL COMMENT '消息内容',
  `uid` int(11) NOT NULL COMMENT '发送人uid',
  `to_uid` int(11) NOT NULL COMMENT '接收人uid',
  `add_time` int(11) NOT NULL COMMENT '发送时间',
  `type` tinyint(1) DEFAULT '0' COMMENT '是否已读（0：否；1：是；）',
  `remind` tinyint(1) DEFAULT '0' COMMENT '是否提醒过'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客服用户对话记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_store_visit`
--

CREATE TABLE `eb_store_visit` (
  `id` int(10) NOT NULL,
  `product_id` int(11) DEFAULT NULL COMMENT '产品ID',
  `product_type` varchar(32) DEFAULT NULL COMMENT '产品类型',
  `cate_id` int(11) DEFAULT NULL COMMENT '产品分类ID',
  `type` char(50) DEFAULT NULL COMMENT '产品类型',
  `uid` int(11) DEFAULT NULL COMMENT '用户ID',
  `count` int(11) DEFAULT NULL COMMENT '访问次数',
  `content` varchar(255) DEFAULT NULL COMMENT '备注描述',
  `add_time` int(11) DEFAULT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='产品浏览分析表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_admin`
--

CREATE TABLE `eb_system_admin` (
  `id` smallint(5) UNSIGNED NOT NULL COMMENT '后台管理员表ID',
  `account` varchar(32) NOT NULL COMMENT '后台管理员账号',
  `pwd` char(32) NOT NULL COMMENT '后台管理员密码',
  `real_name` varchar(16) NOT NULL COMMENT '后台管理员姓名',
  `roles` varchar(128) NOT NULL COMMENT '后台管理员权限(menus_id)',
  `phone` varchar(11) NOT NULL COMMENT '前端登录电话号码',
  `last_ip` varchar(16) DEFAULT NULL COMMENT '后台管理员最后一次登录ip',
  `last_time` int(10) UNSIGNED DEFAULT NULL COMMENT '后台管理员最后一次登录时间',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '后台管理员添加时间',
  `login_count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '登录次数',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '1' COMMENT '后台管理员级别',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '后台管理员状态 1有效0无效',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台管理员表';

--
-- 转存表中的数据 `eb_system_admin`
--

INSERT INTO `eb_system_admin` (`id`, `account`, `pwd`, `real_name`, `roles`, `last_ip`, `last_time`, `add_time`, `login_count`, `level`, `status`, `is_del`) VALUES
(1, 'admin', '31f9abf61ad21a39fe44b0790e1a4f6d', 'admin', '1', '111.18.84.139', 1582432279, 1581341252, 0, 0, 1, 0),
(2, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', 'demo', '23', '106.44.7.186', 1582437709, 1581585623, 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_attachment`
--

CREATE TABLE `eb_system_attachment` (
  `att_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '附件名称',
  `att_dir` varchar(200) NOT NULL COMMENT '附件路径',
  `satt_dir` varchar(200) DEFAULT NULL COMMENT '压缩图片路径',
  `att_size` char(30) NOT NULL COMMENT '附件大小',
  `att_type` char(30) NOT NULL COMMENT '附件类型',
  `pid` int(10) NOT NULL COMMENT '分类id',
  `time` int(11) NOT NULL COMMENT '上传时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件管理表';

--
-- 转存表中的数据 `eb_system_attachment`
--

INSERT INTO `eb_system_attachment` (`att_id`, `name`, `att_dir`, `satt_dir`, `att_size`, `att_type`, `pid`, `time`) VALUES
(23, 'dff8b202002111344407490.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/dff8b202002111344407490.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/dff8b202002111344407490.png', '0', 'image/jpg', 4, 1581399880),
(24, '4e0a9202002111413415852.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/4e0a9202002111413415852.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/4e0a9202002111413415852.jpg', '0', 'image/jpg', 5, 1581401621),
(25, '5ab3f202002111454479193.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ab3f202002111454479193.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ab3f202002111454479193.jpg', '0', 'image/jpg', 0, 1581404087),
(26, 'bd2ef202002111519022660.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/bd2ef202002111519022660.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/bd2ef202002111519022660.jpg', '0', 'image/jpg', 6, 1581405542),
(27, '12017202002111757416996.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/12017202002111757416996.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/12017202002111757416996.png', '0', 'image/jpg', 0, 1581415061),
(28, 'fb05a202002111757415258.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/fb05a202002111757415258.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/fb05a202002111757415258.png', '0', 'image/jpg', 0, 1581415061),
(29, '022b1202002111757413298.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/022b1202002111757413298.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/022b1202002111757413298.png', '0', 'image/jpg', 0, 1581415061),
(30, '8cf7c202002111757418051.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8cf7c202002111757418051.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8cf7c202002111757418051.png', '0', 'image/jpg', 0, 1581415061),
(31, 'a3248202002111757415453.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/a3248202002111757415453.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/a3248202002111757415453.png', '0', 'image/jpg', 0, 1581415061),
(32, '37e3a202002111757417776.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/37e3a202002111757417776.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/37e3a202002111757417776.png', '0', 'image/jpg', 0, 1581415062),
(33, 'ccb10202002111757423753.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/ccb10202002111757423753.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/ccb10202002111757423753.jpg', '0', 'image/jpg', 0, 1581415062),
(34, 'abbe8202002111757429842.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/abbe8202002111757429842.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/abbe8202002111757429842.png', '0', 'image/jpg', 0, 1581415062),
(35, '10f3d202002111757429313.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/10f3d202002111757429313.jpg', '0', 'image/jpg', 0, 1581415062),
(36, 'e20b0202002111757424191.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/e20b0202002111757424191.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/e20b0202002111757424191.png', '0', 'image/jpg', 0, 1581415062),
(37, '55bd0202002111757421468.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/55bd0202002111757421468.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/55bd0202002111757421468.png', '0', 'image/jpg', 0, 1581415062),
(38, '6b85d202002111757421971.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/6b85d202002111757421971.jpg', '0', 'image/jpg', 0, 1581415062),
(39, '7d68920200211175742320.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7d68920200211175742320.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7d68920200211175742320.png', '0', 'image/jpg', 0, 1581415062),
(40, '09bd3202002111757426798.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/09bd3202002111757426798.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/09bd3202002111757426798.png', '0', 'image/jpg', 0, 1581415063),
(41, '7f0bd202002111757435432.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7f0bd202002111757435432.jpg', '0', 'image/jpg', 0, 1581415063),
(42, '99cb7202002111757434673.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/99cb7202002111757434673.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/99cb7202002111757434673.jpg', '0', 'image/jpg', 0, 1581415063),
(43, '7c54d202002111757589056.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7c54d202002111757589056.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7c54d202002111757589056.png', '0', 'image/jpg', 0, 1581415078),
(44, '8b0be202002111757582367.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/8b0be202002111757582367.jpg', '0', 'image/jpg', 0, 1581415078),
(45, '5ae6f202002111757583267.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/5ae6f202002111757583267.jpg', '0', 'image/jpg', 0, 1581415078),
(46, '67c04202002111757584709.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/67c04202002111757584709.jpg', '0', 'image/jpg', 0, 1581415079),
(47, '7809f202002111801111378.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7809f202002111801111378.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/7809f202002111801111378.jpg', '0', 'image/jpg', 0, 1581415271),
(48, '32095202002111804187328.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/32095202002111804187328.jpg', '0', 'image/jpg', 0, 1581415458),
(49, '9ec16202002121807113099.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9ec16202002121807113099.png', '0', 'image/jpg', 0, 1581502031),
(50, '9d76920200212180711743.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9d76920200212180711743.png', 'http://testcrmeb.oss-cn-beijing.aliyuncs.com/9d76920200212180711743.png', '0', 'image/jpg', 0, 1581502031),
(51, '34285202002171340371181.gif', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/34285202002171340371181.gif', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/34285202002171340371181.gif', '0', 'image/jpg', 6, 1581918037),
(52, 'd6c57202002171744271971.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d6c57202002171744271971.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d6c57202002171744271971.png', '0', 'image/jpg', 6, 1581932667),
(53, '68244202002171745063469.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68244202002171745063469.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/68244202002171745063469.jpg', '0', 'image/jpg', 6, 1581932714),
(54, 'f939b202002201114298263.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f939b202002201114298263.png', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f939b202002201114298263.png', '0', 'image/jpg', 0, 1582168469),
(74, '267f3202002201232144167.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/267f3202002201232144167.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/267f3202002201232144167.jpg', '0', 'image/jpg', 0, 1582173134),
(75, '1b0cb202002201232145135.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1b0cb202002201232145135.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/1b0cb202002201232145135.jpg', '0', 'image/jpg', 0, 1582173134),
(76, '9f41f202002201232146791.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9f41f202002201232146791.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9f41f202002201232146791.jpg', '0', 'image/jpg', 0, 1582173134),
(77, '44910202002201232149124.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/44910202002201232149124.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/44910202002201232149124.jpg', '0', 'image/jpg', 0, 1582173135),
(78, '3e9f4202002201232159295.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3e9f4202002201232159295.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3e9f4202002201232159295.jpg', '0', 'image/jpg', 0, 1582173135),
(79, '8ab46202002201232152644.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/8ab46202002201232152644.jpg', '0', 'image/jpg', 0, 1582173135),
(80, '887d9202002201232157812.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/887d9202002201232157812.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/887d9202002201232157812.jpg', '0', 'image/jpg', 0, 1582173135),
(81, '9547920200220123215506.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9547920200220123215506.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9547920200220123215506.jpg', '0', 'image/jpg', 0, 1582173135),
(82, '85d12202002201232152250.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/85d12202002201232152250.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/85d12202002201232152250.jpg', '0', 'image/jpg', 0, 1582173136),
(83, '9a0d1202002201232324402.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9a0d1202002201232324402.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/9a0d1202002201232324402.jpg', '0', 'image/jpg', 0, 1582173154),
(84, 'd41c7202002201232431520.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d41c7202002201232431520.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/d41c7202002201232431520.jpg', '0', 'image/jpg', 0, 1582173163),
(85, 'f4313202002201232435004.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/f4313202002201232435004.jpg', '0', 'image/jpg', 0, 1582173163),
(86, 'ce99d202002201232432554.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/ce99d202002201232432554.jpg', '0', 'image/jpg', 0, 1582173164),
(87, '78cdd20200220123244525.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/78cdd20200220123244525.jpg', '0', 'image/jpg', 0, 1582173164),
(88, '3ec76202002201232442683.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/3ec76202002201232442683.jpg', '0', 'image/jpg', 0, 1582173164),
(89, 'e56f5202002201232449146.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/e56f5202002201232449146.jpg', '0', 'image/jpg', 0, 1582173164),
(90, '0620e202002201232446695.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/0620e202002201232446695.jpg', '0', 'image/jpg', 0, 1582173164),
(91, '32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/32379202002201232449232.jpg', '0', 'image/jpg', 0, 1582173165),
(92, '744f3202002201232457195.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/744f3202002201232457195.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/744f3202002201232457195.jpg', '0', 'image/jpg', 0, 1582173165),
(94, '20362202002201412303972.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', 'http://cremb-zsff.oss-cn-beijing.aliyuncs.com/20362202002201412303972.jpg', '0', 'image/jpg', 0, 1582179150);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_attachment_category`
--

CREATE TABLE `eb_system_attachment_category` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT '0' COMMENT '父级ID',
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `enname` varchar(50) NOT NULL COMMENT '分类目录'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件分类表';

--
-- 转存表中的数据 `eb_system_attachment_category`
--

INSERT INTO `eb_system_attachment_category` (`id`, `pid`, `name`, `enname`) VALUES
(4, 0, '导航图标', ''),
(5, 0, '专题主图', ''),
(6, 0, '公众号图', '');

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_config`
--

CREATE TABLE `eb_system_config` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '配置id',
  `menu_name` varchar(255) NOT NULL COMMENT '字段名称',
  `type` varchar(255) NOT NULL COMMENT '类型(文本框,单选按钮...)',
  `input_type` varchar(20) DEFAULT 'input' COMMENT '表单类型',
  `config_tab_id` int(10) UNSIGNED NOT NULL COMMENT '配置分类id',
  `parameter` varchar(255) DEFAULT NULL COMMENT '规则 单选框和多选框',
  `upload_type` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '上传文件格式1单图2多图3文件',
  `required` varchar(255) DEFAULT NULL COMMENT '规则',
  `width` int(10) UNSIGNED DEFAULT NULL COMMENT '多行文本框的宽度',
  `high` int(10) UNSIGNED DEFAULT NULL COMMENT '多行文框的高度',
  `value` varchar(5000) DEFAULT NULL COMMENT '默认值',
  `info` varchar(255) NOT NULL COMMENT '配置名称',
  `desc` varchar(255) DEFAULT NULL COMMENT '配置简介',
  `sort` int(10) UNSIGNED NOT NULL COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置表';

--
-- 转存表中的数据 `eb_system_config`
--

INSERT INTO `eb_system_config` (`id`, `menu_name`, `type`, `input_type`, `config_tab_id`, `parameter`, `upload_type`, `required`, `width`, `high`, `value`, `info`, `desc`, `sort`, `status`) VALUES
(1, 'site_name', 'text', 'input', 1, '', 0, 'required:true', 100, 0, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '网站名称', '网站名称', 0, 1),
(2, 'site_url', 'text', 'input', 1, '', 0, 'required:true,url:true', 100, 0, '\"\"', '网站地址', '网站地址', 0, 1),
(3, 'site_logo', 'upload', 'input', 1, '', 1, '', 0, 0, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/d6c57202002171744271971.png\"', '后台LOGO', '左上角logo,建议尺寸[170*50]', 0, 1),
(4, 'site_phone', 'text', 'input', 1, '', 0, '', 100, 0, '\"\"', '联系电话', '联系电话', 0, 1),
(5, 'seo_title', 'text', 'input', 1, '', 0, 'required:true', 100, 0, '\"\"', 'SEO标题', 'SEO标题', 0, 1),
(6, 'site_email', 'text', 'input', 1, '', 0, 'email:true', 100, 0, '\"\"', '联系邮箱', '联系邮箱', 0, 1),
(7, 'site_qq', 'text', 'input', 1, '', 0, 'qq:true', 100, 0, '\"\"', '联系QQ', '联系QQ', 0, 1),
(9, 'close_system', 'radio', 'input', 1, '0=开启\n1=关闭', 0, '', 0, 0, '\"0\"', '关闭后台', '关闭后台', 0, 2),
(10, 'wechat_name', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', '公众号名称', '公众号的名称', 0, 1),
(11, 'wechat_id', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', '微信号', '微信号', 0, 1),
(12, 'wechat_sourceid', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', '公众号原始id', '公众号原始id', 0, 1),
(13, 'wechat_appid', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'AppID', 'AppID', 0, 1),
(14, 'wechat_appsecret', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'AppSecret', 'AppSecret', 0, 1),
(15, 'wechat_token', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', '微信验证TOKEN', '微信验证TOKEN', 0, 1),
(16, 'wechat_encode', 'radio', 'input', 2, '0=明文模式\n1=兼容模式\n2=安全模式', 0, '', 0, 0, '\"0\"', '消息加解密方式', '如需使用安全模式请在管理中心修改，仅限服务号和认证订阅号', 0, 1),
(17, 'wechat_encodingaeskey', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"\"', 'EncodingAESKey', '公众号消息加解密Key,在使用安全模式情况下要填写该值，请先在管理中心修改，然后填写该值，仅限服务号和认证订阅号', 0, 1),
(18, 'wechat_share_img', 'upload', 'input', 3, '', 1, '', 0, 0, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/68244202002171745063469.jpg\"', '微信分享图片', '若填写此图片地址，则分享网页出去时会分享此图片。可有效防止分享图片变形', 0, 1),
(19, 'wechat_qrcode', 'upload', 'input', 2, '', 1, '', 0, 0, '\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/bd2ef202002111519022660.jpg\"', '公众号二维码', '您的公众号二维码', 0, 1),
(20, 'wechat_type', 'radio', 'input', 2, '0=服务号\n1=订阅号', 0, '', 0, 0, '\"0\"', '公众号类型', '公众号的类型', 0, 1),
(21, 'wechat_share_title', 'text', 'input', 3, '', 0, 'required:true', 100, 0, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '微信分享标题', '微信分享标题', 0, 1),
(22, 'wechat_share_synopsis', 'textarea', 'input', 3, '', 0, '', 100, 5, '\"\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '微信分享简介', '微信分享简介', 0, 1),
(23, 'pay_weixin_appid', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Appid', '微信公众号身份的唯一标识。审核通过后，在微信发送的邮件中查看。', 0, 1),
(24, 'pay_weixin_appsecret', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Appsecret', 'JSAPI接口中获取openid，审核后在公众平台开启开发模式后可查看。', 0, 1),
(25, 'pay_weixin_mchid', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Mchid', '受理商ID，身份标识', 0, 1),
(26, 'pay_weixin_client_cert', 'upload', 'input', 4, '', 3, '', 0, 0, '\"\"', '微信支付证书', '微信支付证书，在微信商家平台中可以下载！文件名一般为apiclient_cert.pem', 0, 1),
(27, 'pay_weixin_client_key', 'upload', 'input', 4, '', 3, '', 0, 0, '\"\"', '微信支付证书密钥', '微信支付证书密钥，在微信商家平台中可以下载！文件名一般为apiclient_key.pem', 0, 1),
(28, 'pay_weixin_key', 'text', 'input', 4, '', 0, '', 100, 0, '\"\"', 'Key', '商户支付密钥Key。审核通过后，在微信发送的邮件中查看。', 0, 1),
(29, 'pay_weixin_open', 'radio', 'input', 4, '1=开启\n0=关闭', NULL, NULL, 0, 0, '1', '开启', '是否启用微信支付', 0, 2),
(31, 'store_postage', 'text', 'input', 10, '', 0, 'number:true,min:0', 100, 0, '\"12\"', '邮费基础价', '商品邮费基础价格,最终金额为(基础价 + 商品1邮费 + 商品2邮费)', 0, 1),
(32, 'store_free_postage', 'text', 'input', 5, '', 0, 'number:true,min:-1', 100, 0, '\"200\"', '满额包邮', '商城商品满多少金额即可包邮', 0, 1),
(33, 'offline_postage', 'radio', 'input', 10, '0=不包邮\n1=包邮', 0, '', 0, 0, '\"1\"', '线下支付是否包邮', '用户选择线下支付时是否包邮', 0, 1),
(34, 'integral_ratio', 'text', 'input', 11, '', 0, 'number:true', 100, 0, '\"0.01\"', '积分抵用比例', '积分抵用比例(1积分抵多少金额)', 0, 1),
(35, 'site_service_phone', 'text', 'input', 1, '', 0, '', 100, 0, '\"\"', '客服电话', '客服联系电话', 0, 1),
/*(44, 'store_user_min_recharge', 'text', 'input', 23, '', 0, 'required:true,number:true,min:0', 100, 0, '\"0.01\"', '用户最低充值金额', '用户单次最低充值金额', 0, 0),*/
(45, 'site_store_admin_uids', 'text', 'input', 5, '', 0, '', 100, 0, '\"4\"', '管理员用户ID', '管理员用户ID,用于接收商城订单提醒，到微信用户中查找编号，多个英文‘,’隔开', 0, 1),
(46, 'system_express_app_code', 'text', 'input', 10, '', 0, '', 100, 0, '\"dbc3c1426efe44fdb6c0aa04c52a4ca9\"', '快递查询密钥', '阿里云快递查询接口密钥购买地址：https://market.aliyun.com/products/57126001/cmapi011120.html', 0, 1),
(47, 'main_business', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\" IT\\u79d1\\u6280 \\u4e92\\u8054\\u7f51|\\u7535\\u5b50\\u5546\\u52a1\"', '微信模板消息_主营行业', '微信公众号模板消息中选择开通的主营行业', 0, 0),
(48, 'vice_business', 'text', 'input', 2, '', 0, 'required:true', 100, 0, '\"IT\\u79d1\\u6280 IT\\u8f6f\\u4ef6\\u4e0e\\u670d\\u52a1 \"', '微信模板消息_副营行业', '微信公众号模板消息中选择开通的副营行业', 0, 0),
(49, 'store_brokerage_ratio', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, 0, '\"60\"', '一级推广人返佣比例', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 反订单金额的5%', 9, 1),
(50, 'wechat_first_sub_give_coupon', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"\"', '首次关注赠送优惠券ID', '首次关注赠送优惠券ID,0为不赠送', 0, 1),
(51, 'store_give_con_min_price', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"0.01\"', '消费满多少赠送优惠券', '消费满多少赠送优惠券,0为不赠送', 0, 1),
(52, 'store_order_give_coupon', 'text', 'input', 12, '', 0, 'requred:true,digits:true,min:0', 100, 0, '\"\"', '消费赠送优惠劵ID', '消费赠送优惠劵ID,0为不赠送', 0, 1),
(54, 'sx_sign_min_int', 'text', 'input', 11, '', 0, 'required:true,number:true,min:0', 100, 0, '\"1\"', '签到奖励最低积分', '签到奖励最低积分', 0, 1),
(55, 'sx_sign_max_int', 'text', 'input', 11, '', 0, 'required:true,number:true,min:0', 100, 0, '\"5\"', '签到奖励最高积分', '签到奖励最高积分', 0, 1),
(57, 'about_us', 'upload', 'input', 1, '', 1, '', 0, 0, '\"\"', '关于我们', '系统的标识', 0, 1),
(58, 'replenishment_num', 'text', 'input', 5, '', 0, 'required:true,number:true,min:0', 100, 0, '\"20\"', '待补货数量', '产品待补货数量低于多少时，提示补货', 0, 1),
(59, 'routine_appId', 'text', 'input', 7, '', 0, '', 100, 0, '\"\"', 'appId', '小程序appID', 0, 1),
(60, 'routine_appsecret', 'text', 'input', 7, '', 0, '', 100, 0, '\"\"', 'AppSecret', '小程序AppSecret', 0, 1),
(61, 'api', 'text', 'input', 2, '', 0, '', 100, 0, '\"\\/wap\\/wechat\\/serve\"', '接口地址', '微信接口例如：http://www.abc.com/wap/wechat/serve', 0, 1),
(62, 'paydir', 'textarea', 'input', 4, '', 0, '', 100, 5, '\"\\/wap\\/special\\/details\\n\"', '配置目录', '支付目录配置系统不调用提示作用', 0, 1),
(73, 'routine_logo', 'upload', 'input', 7, '', 1, '', 0, 0, '\"\"', '小程序logo', '小程序logo', 0, 1),
(74, 'routine_name', 'text', 'input', 7, '', 0, '', 100, 0, '\"\\u8f66\\u8054\\u76df\\u65b0\\u80fd\\u6e90\"', '小程序名称', '小程序名称', 0, 1),
(76, 'routine_style', 'text', 'input', 7, '', 0, '', 100, 0, '\"#FFFFFF\"', '小程序风格', '小程序颜色', 0, 1),
(77, 'store_stock', 'text', 'input', 5, '', 0, '', 100, 0, '\"1\"', '警戒库存', '警戒库存提醒值', 0, 1),
(85, 'stor_reason', 'textarea', 'input', 5, '', 0, '', 100, 8, '\"\\u6536\\u8d27\\u5730\\u5740\\u586b\\u9519\\u4e86=\\u4e0e\\u63cf\\u8ff0\\u4e0d\\u7b26=\\u4fe1\\u606f\\u586b\\u9519\\u4e86\\uff0c\\u91cd\\u65b0\\u62cd=\\u6536\\u5230\\u5546\\u54c1\\u635f\\u574f\\u4e86=\\u672a\\u6309\\u9884\\u5b9a\\u65f6\\u95f4\\u53d1\\u8d27=\\u5176\\u5b83\\u539f\\u56e0\"', '退货理由', '配置退货理由，一行一个理由', 0, 1),
(87, 'store_brokerage_two', 'text', 'input', 9, NULL, NULL, 'required:true,min:0,max:100,number:true', 100, 0, '\"30\"', '二级推广人返佣比例', '订单交易成功后给上级返佣的比例0 - 100,例:5 = 反订单金额的5%', 8, 1),
(88, 'store_brokerage_statu', 'radio', 'input', 9, '1=指定分销\n2=人人分销', 0, '', 0, 0, '\"2\"', '分销模式', '人人分销默认每个人都可以分销，制定人分销后台制定人开启分销', 10, 1),
(89, 'pay_routine_appid', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Appid', '小程序Appid', 0, 1),
(90, 'pay_routine_appsecret', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Appsecret', '小程序Appsecret', 0, 1),
(91, 'pay_routine_mchid', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Mchid', '商户号', 0, 1),
(92, 'pay_routine_key', 'text', 'input', 14, '', 0, 'required:true', 100, 0, '\"\"', 'Key', '商户key', 0, 1),
(93, 'pay_routine_client_cert', 'upload', 'input', 14, '', 3, '', 0, 0, '[\"\\/public\\/uploads\\/config\\/file\\/5c12fb7b9ebc5.pem\"]', '小程序支付证书', '小程序支付证书', 0, 1),
(94, 'pay_routine_client_key', 'upload', 'input', 14, '', 3, '', 0, 0, '[\"\\/public\\/uploads\\/config\\/file\\/5c12fb7eb8897.pem\"]', '小程序支付证书密钥', '小程序支付证书密钥', 0, 1),
(95, 'log_cycle', 'text', 'input', 1, '', 0, '', 100, 0, '\"\"', '系统日志', '系统日志保存时间单位天', 0, 1),
(96, 'sign_in_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '签到送积分', '签到送积分显示开关', 0, 1),
(97, 'give_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '邀请好友赠送积分', '邀请好友赠送积分显示开关', 0, 1),
(98, 'share_integral', 'radio', 'input', 11, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"0\"', '分享商品送积分', '分享商品送积分开关', 0, 1),
(114, 'user_extract_bank', 'textarea', 'input', 5, NULL, NULL, NULL, 100, 6, '\"\\u62db\\u5546\\u94f6\\u884c=\\u5efa\\u8bbe\\u94f6\\u884c=\\u519c\\u4e1a\\u94f6\\u884c\"', '提现银行', '提现银行', 0, 1),
(116, 'index_recommend_url', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '[\"\\/public\\/uploads\\/config\\/image\\/5c1ef9f2594ea.png\"]', '主页推荐图', '主页推荐图', 0, 1),
(117, 'hot_food', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '\"\"', '热销菜品', '热销菜品', 0, 1),
(118, 'welcome_url', 'upload', 'input', 15, NULL, 1, NULL, NULL, NULL, '[\"\\/public\\/uploads\\/config\\/image\\/5c29e37db77e8.jpg\"]', '最受欢迎', '热销菜品', 0, 1),
(119, 'praise_part', 'text', 'input', 7, NULL, NULL, '', 100, NULL, '\"5\"', '用户给商户好评加分', '用户给商户好评加分', 0, 1),
(120, 'praise_bad_part', 'text', 'input', 7, NULL, NULL, '', 100, NULL, '\"8\"', '用户给商户差评减分分', '用户给商户好评加分', 0, 1),
(130, 'extract_min_money', 'text', 'input', 9, NULL, NULL, NULL, 100, NULL, '\"1\"', '提现最低金额', '提现最低金额', 0, 1),
(132, 'smsSignName', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '\"CEMEB\\u77e5\\u8bc6\\u4ed8\\u8d39\"', '阿里云短信签名', '阿里云短信签名', 0, 1),
(133, 'accessKeyId', 'text', 'input', 18, NULL, NULL, NULL, 100, NULL, '\"\"', '阿里云AccessKeyId', '阿里云AccessKeyId', 0, 1),
(134, 'accessKeySecret', 'text', 'input', 18, NULL, NULL, NULL, 100, NULL, '\"\"', '阿里云AccessKeySecret', '阿里云AccessKeySecret', 0, 1),
(135, 'smsTemplateCode', 'text', 'input', 17, NULL, NULL, '', 100, NULL, '\"\"', '阿里云短信模板ID', '阿里云短信模板ID', 0, 1),
(140, 'spread_poster_url', 'upload', 'input', 9, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/20362202002201412303972.jpg\"', '推广人海报', '推广人海报', 0, 1),
(141, 'special_gift_banner', 'upload', 'input', 22, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/9547920200220123215506.jpg\"', '赠送礼物广告图', '赠送礼物广告图', 0, 1),
(143, 'alipay_app_id', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服appid', '蚂蚁金服appid', 0, 1),
(144, 'alipay_private_key', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服key', '蚂蚁金服key', 0, 1),
(145, 'alipay_public_key', 'text', 'input', 16, NULL, NULL, '', 100, NULL, '\"\"', '蚂蚁金服公钥', '蚂蚁金服公钥', 0, 1),
(146, 'promoter_content', 'textarea', 'input', 9, NULL, NULL, NULL, 100, 8, '\"\"', '推广细则', '推广细则', 0, 1),
(147, 'promoter_guide', 'textarea', 'input', 9, NULL, NULL, NULL, 100, 8, '\"\"', '推荐指引', '推荐指引', 0, 1),
(154, 'open_barrage', 'radio', 'input', 22, '1=开启\n0=关闭', NULL, NULL, 100, NULL, '\"1\"', '虚拟用户专题弹幕开关', '虚拟用户专题弹幕开关', 0, 1),
(155, 'barrage_show_time', 'text', 'input', 22, NULL, NULL, NULL, 100, NULL, '\"5\"', '专题弹幕停留时间', '专题弹幕停留时间 单位秒计算', 0, 1),
(164, 'uploadUrl', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"\"', '空间域名 Domain', '空间域名 Domain', 0, 1),
(165, 'OssBucket', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"\"', '存储空间名称', '存储空间名称,又称桶名', 0, 1),
(166, 'end_point', 'text', 'input', 19, NULL, NULL, '', 100, NULL, '\"oss-cn-beijing.aliyuncs.com\"', 'EndPoint（地域节点）', 'EndPoint（地域节点）', 0, 1),
(168, 'home_logo', 'upload', 'input', 22, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/34285202002171340371181.gif\"', '首页图标', '首页图标', 0, 1),
(169, 'aliyun_live_rtmpLink', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '推流地址', '推流地址', 0, 1),
(170, 'aliyun_live_playLike', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '播放地址', '播放地址', 0, 1),
(171, 'aliyun_live_push_key', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '推流主key', '推流主key', 0, 1),
(172, 'aliyun_live_play_key', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '播放主key', '播放主key', 0, 1),
(173, 'aliyun_live_appName', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '直播应用名', '直播应用名(只能为英文数字组合)', 0, 1),
(174, 'aliyun_live_oss_bucket', 'text', 'input', 21, NULL, NULL, '', 100, NULL, '\"\"', '直播录制OSS桶名', '直播录制OSS桶名', 0, 1),
(175, 'aliyun_live_end_point', 'text', 'input', 21, NULL, NULL, NULL, 100, NULL, '\"oss-cn-shenzhen.aliyuncs.com\"', '直播录制OssEndpoint', '直播录制OssEndpoint地域节点', 0, 1),
(176, 'gold_rate', 'text', 'input', 23, NULL, NULL, 'number:rue', 100, NULL, '\"10\"', '人民币与金币换算率', '充值人民币和金币的换算概率，默认：1元人民币=10系统虚拟金币', 0, 1),
(177, 'gold_name', 'text', 'input', 23, NULL, NULL, NULL, 100, NULL, '\"\\u91d1\\u5e01\"', '虚拟货币名称', '虚拟货币名称（如，金币，水滴，鲜花等）', 0, 1),
(178, 'gold_image', 'upload', 'input', 23, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/61d9d202006181439471781.png\"', '虚拟货币图标', '虚拟货币图标', 0, 1),
(179, 'single_gold_coin', 'text', 'input', 22, NULL, NULL, '', 100, NULL, '1', '单次签到虚拟币数', '每次签到用户可以获得的虚拟币数（默认为金币）', 0, 1),
(180, 'sign_default_poster', 'upload', 'input', 22, NULL, 1, NULL, NULL, NULL, '\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/20362202002201412303972.jpg\"', '签到海报', '签到默认海报（没有签到海报时默认显示此图）', 0, 1),
(181, 'balance_switch', 'radio', 'input', 22, '1=开启\n0=关闭', NULL, NULL, NULL, NULL, '\"1\"', '余额开关', '余额开关', 0, 1);


-- --------------------------------------------------------

--
-- 表的结构 `eb_system_config_content`
--

CREATE TABLE `eb_system_config_content` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '中文标题',
  `config_name` varchar(100) NOT NULL DEFAULT '' COMMENT '配置名',
  `content` text NOT NULL COMMENT '内容',
  `add_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `is_show` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏',
  `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置文章表' ROW_FORMAT=COMPACT;

--
-- 转存表中的数据 `eb_system_config_content`
--

INSERT INTO `eb_system_config_content` (`id`, `title`, `config_name`, `content`, `add_time`, `is_show`, `is_del`) VALUES
(1, '关于我们', 'about_us', '&lt;p&gt;关于公司的信息介绍&lt;/p&gt;', 0, 1, 0),
(2, '用户付费协议', 'user_agreement', '', 0, 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_config_tab`
--

CREATE TABLE `eb_system_config_tab` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '配置分类id',
  `title` varchar(255) NOT NULL COMMENT '配置分类名称',
  `eng_title` varchar(255) NOT NULL COMMENT '配置分类英文名称',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '配置分类状态',
  `info` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '配置分类是否显示',
  `icon` varchar(30) DEFAULT NULL COMMENT '图标',
  `type` int(2) DEFAULT '0' COMMENT '配置类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='配置分类表';

--
-- 转存表中的数据 `eb_system_config_tab`
--

INSERT INTO `eb_system_config_tab` (`id`, `title`, `eng_title`, `status`, `info`, `icon`, `type`) VALUES
(1, '基础配置', 'basics', 1, 0, 'cog', 0),
(2, '公众号配置', 'wechat', 1, 0, 'weixin', 1),
(3, '公众号分享配置', 'wechat_share', 1, 0, 'whatsapp', 1),
(4, '公众号支付配置', 'pay', 1, 0, 'jpy', 4),
(5, '商城配置(暂弃)', 'store', 2, 0, 'shopping-cart', 0),
(7, '小程序配置', 'routine', 1, 0, 'weixin', 2),
(9, '分销配置', 'fenxiao', 1, 0, 'sitemap', 3),
(10, '物流配置', 'express', 2, 0, 'motorcycle', 0),
(11, '积分配置', 'point', 1, 0, 'powerpoint-o', 3),
(12, '优惠券配置', 'coupon', 1, 0, 'heartbeat', 3),
(15, '小程序图片配置', 'routine_image_url', 1, 0, 'cc', 2),
(16, '支付宝支付配置', 'pay_system', 1, 0, 'yen', 4),
(17, '短信配置', 'sms_system', 1, 0, 'paper-plane', 5),
(18, '阿里云key配置', 'aliyun_system', 1, 0, 'sun-o', 5),
(19, 'OSS上传配置', 'oss_sytem', 1, 0, 'cloud-upload', 5),
(21, '阿里云直播', 'aliyun_live', 1, 0, 'arrow-circle-o-left', 5),
(22, '其他配置', 'shop_home', 1, 0, '', 6),
(23, '充值金币', 'recharge', 1, 0, 'jpy', 7);



-- --------------------------------------------------------

--
-- 表的结构 `eb_system_file`
--

CREATE TABLE `eb_system_file` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文件对比ID',
  `cthash` char(32) NOT NULL COMMENT '文件内容',
  `filename` varchar(255) NOT NULL COMMENT '文价名称',
  `atime` char(12) NOT NULL COMMENT '上次访问时间',
  `mtime` char(12) NOT NULL COMMENT '上次修改时间',
  `ctime` char(12) NOT NULL COMMENT '上次改变时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文件对比表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_group`
--

CREATE TABLE `eb_system_group` (
  `id` int(11) NOT NULL COMMENT '组合数据ID',
  `name` varchar(50) NOT NULL COMMENT '数据组名称',
  `info` varchar(256) NOT NULL COMMENT '数据提示',
  `config_name` varchar(50) NOT NULL COMMENT '数据字段',
  `fields` text COMMENT '数据组字段以及类型（json数据）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合数据表';

--
-- 转存表中的数据 `eb_system_group`
--

INSERT INTO `eb_system_group` (`id`, `name`, `info`, `config_name`, `fields`) VALUES
(34, '商城首页banner', '商城首页banner设置', 'store_home_banner', '[{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u94fe\\u63a5\",\"title\":\"url\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u7247\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(52, '提现银行', '提现银行', 'extract_bank', '[{\"name\":\"\\u94f6\\u884c\\u540d\\u79f0\",\"title\":\"bank_name\",\"type\":\"input\",\"param\":\"\"}]'),
(53, '提现规则', '提现规则', 'extract_rule', '[{\"name\":\"\\u89c4\\u5219\",\"title\":\"content\",\"type\":\"textarea\",\"param\":\"\"}]'),
(54, '分享页面背景图', '分享页面背景图', 'share_background', '[{\"name\":\"\\u80cc\\u666f\\u56fe\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"}]'),
(56, '首页活动区域图片', '首页活动区域图片', 'home_activity', '[{\"name\":\"\\u56fe\\u7247(260*260\\/416*214)\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u6807\\u9898\",\"title\":\"title\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u7b80\\u4ecb\",\"title\":\"info\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u516c\\u4f17\\u53f7\\u94fe\\u63a5\",\"title\":\"wap_link\",\"type\":\"select\",\"param\":\"\\/activity\\/goods_seckill=>\\u79d2\\u6740\\u5217\\u8868\\n\\/activity\\/bargain=>\\u780d\\u4ef7\\u5217\\u8868\\n\\/activity\\/group=>\\u62fc\\u56e2\\u5217\\u8868\"}]'),
(57, '会员权益', '会员权益', 'membership_interests', '[{\"name\":\"\\u6743\\u76ca\\u540d\\u79f0\",\"title\":\"name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u56fe\\u6807\",\"title\":\"pic\",\"type\":\"upload\",\"param\":\"\"},{\"name\":\"\\u8bf4\\u660e\",\"title\":\"explain\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]'),
(58, '会员说明', '会员说明', 'member_description', '[{\"name\":\"\\u5185\\u5bb9\",\"title\":\"text\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u6392\\u5e8f\",\"title\":\"sort\",\"type\":\"input\",\"param\":\"\"}]'),
(59, '直播间礼物列表', '直播间礼物列表', 'live_gift', '[{\"name\":\"\\u793c\\u7269\\u540d\\u79f0\",\"title\":\"live_gift_name\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u793c\\u7269\\u4ef7\\u683c\\uff08\\u865a\\u62df\\u8d27\\u5e01\\uff09\",\"title\":\"live_gift_price\",\"type\":\"input\",\"param\":\"\"},{\"name\":\"\\u8d60\\u9001\\u6570\\u91cf\\u5217\\u8868\",\"title\":\"live_gift_num\",\"type\":\"checkbox\",\"param\":\"1=1\\n5=5\\n10=10\\n20=20\\n66=66\\n99=99\\n520=520\\n999=999\\n1314=1314\"},{\"name\":\"\\u793c\\u7269\\u56fe\\u6807\",\"title\":\"live_gift_show_img\",\"type\":\"upload\",\"param\":\"\"}]');



-- --------------------------------------------------------

--
-- 表的结构 `eb_system_group_data`
--

CREATE TABLE `eb_system_group_data` (
  `id` int(11) NOT NULL COMMENT '组合数据详情ID',
  `gid` int(11) NOT NULL COMMENT '对应的数据组id',
  `value` text NOT NULL COMMENT '数据组对应的数据值（json数据）',
  `add_time` int(10) NOT NULL COMMENT '添加数据时间',
  `sort` int(11) NOT NULL COMMENT '数据排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态（1：开启；2：关闭；）'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组合数据详情表';

--
-- 转存表中的数据 `eb_system_group_data`
--

INSERT INTO `eb_system_group_data` (`id`, `gid`, `value`, `add_time`, `sort`, `status`) VALUES
(89, 38, '{\"banner\":{\"type\":\"upload\",\"value\":\"http:\\/\\/shop.crmeb.net\\/public\\/uploads\\/editor\\/20180601\\/5b10b2436876e.jpg\"}}', 1527153599, 1, 1),
(125, 52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u62db\\u5546\\u94f6\\u884c\"}}', 1545116190, 1, 1),
(126, 52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u5efa\\u8bbe\\u94f6\\u884c\"}}', 1545116196, 1, 1),
(127, 52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u519c\\u4e1a\\u94f6\\u884c\"}}', 1545116201, 1, 1),
(128, 52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u4e2d\\u56fd\\u94f6\\u884c\"}}', 1545116213, 1, 1),
(130, 52, '{\"bank_name\":{\"type\":\"input\",\"value\":\"\\u5de5\\u5546\\u94f6\\u884c\"}}', 1545116248, 1, 1),
(131, 53, '{\"content\":{\"type\":\"textarea\",\"value\":\"\\u5230\\u8d26\\u65f6\\u95f4T+1\"}}', 1545117378, 0, 1),
(132, 53, '{\"content\":{\"type\":\"textarea\",\"value\":\"\\u63d0\\u73b0\\u624b\\u7eed\\u8d39\\u6263\\u966410%\\uff08\\u542b\\u4e2a\\u4eba\\u6240\\u5f97\\u7a0e\\uff09\"}}', 1545117383, 1, 1),
(138, 54, '{\"pic\":{\"type\":\"upload\",\"value\":\"https:\\/\\/huochemeng.crmeb.net\\/public\\/uploads\\/25\\/20181224\\/s_5c2055fb368ab.jpg\"}}', 1545623151, 1, 1),
(147, 34, '{\"title\":{\"type\":\"input\",\"value\":\"1\"},\"url\":{\"type\":\"input\",\"value\":\"http:\\/\\/zhishifufei.crmeb.net\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/7d68920200211175742320.png\"}}', 1548224930, 1, 1),
(155, 34, '{\"title\":{\"type\":\"input\",\"value\":\"2\"},\"url\":{\"type\":\"input\",\"value\":\"http:\\/\\/zhishifufei.crmeb.net\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/9d76920200212180711743.png\"}}', 1581416351, 1, 1),
(156, 56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/ccb10202002111757423753.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u96f6\\u5143\\u8bfe\\u7a0b\\u514d\\u8d39\\u9886\"},\"info\":{\"type\":\"input\",\"value\":\"\\u673a\\u4f1a\\u4e0d\\u80fd\\u9519\\u8fc7\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/details?id=48\"},\"select_id\":{\"value\":\"48\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"0\"}}', 1581416634, 0, 1),
(158, 56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/7f0bd202002111757435432.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u96c5\\u601d\\u6258\\u798f\\u7ebf\\u4e0a\\u57f9\\u8bad\"},\"info\":{\"type\":\"input\",\"value\":\"\\u51fa\\u56fd\\u6df1\\u9020\\u5fc5\\u5907\\u8bfe\\u7a0b\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/details?id=49\"},\"select_id\":{\"value\":\"49\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"0\"}}', 1581416745, 0, 1),
(159, 56, '{\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/6b85d202002111757421971.jpg\"},\"title\":{\"type\":\"input\",\"value\":\"\\u8003\\u7814\\u5347\\u5b66\\u6b63\\u5f53\\u65f6\"},\"info\":{\"type\":\"input\",\"value\":\"\\u8003\\u7814\\u5f3a\\u52a9\\u529b\"},\"wap_link\":{\"type\":\"select\",\"value\":\"\\/wap\\/special\\/special_cate?cate_id=8\"},\"select_id\":{\"value\":\"8\",\"type\":\"select\"},\"type\":{\"type\":\"radio\",\"value\":\"1\"}}', 1581416759, 0, 1),
(160, 34, '{\"title\":{\"type\":\"input\",\"value\":\"3\"},\"url\":{\"type\":\"input\",\"value\":\"http:\\/\\/zhishifufei.crmeb.net\\/wap\\/special\\/special_cate.html\"},\"pic\":{\"type\":\"upload\",\"value\":\"http:\\/\\/testcrmeb.oss-cn-beijing.aliyuncs.com\\/9ec16202002121807113099.png\"}}', 1581502059, 1, 1),
(161, 57, '{"name":{"type":"input","value":"\\u4f1a\\u5458\\u4f18\\u60e0\\u4ef7"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/39472202004291128217988.png"},"explain":{"type":"input","value":"\\u8d2d\\u4e70\\u4e13\\u9898\\u4eab\\u4f1a\\u5458\\u4ef7"},"sort":{"type":"input","value":"1"}}', '1588130969', '1', '1'),
(162, 57, '{"name":{"type":"input","value":"\\u514d\\u8d39\\u8bfe\\u7a0b"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/59285202004291128212590.png"},"explain":{"type":"input","value":"\\u90e8\\u5206\\u8bfe\\u7a0b\\u4f1a\\u5458\\u514d\\u8d39"},"sort":{"type":"input","value":"2"}}', '1588130996', '2', '1'),
(163, 57, '{"name":{"type":"input","value":"\\u66f4\\u591a\\u6743\\u76ca"},"pic":{"type":"upload","value":"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/11009202004291128212348.png"},"explain":{"type":"input","value":"\\u66f4\\u591a\\u6743\\u76ca\\u589e\\u52a0\\u4e2d"},"sort":{"type":"input","value":"3"}}', '1588131020', '3', '1'),
(164, 58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u8d2d\\u4e70\\u90e8\\u5206\\u8bfe\\u7a0b\\u53ef\\u4eab\\u53d7\\u4f18\\u60e0\\u4ef7"},"sort":{"type":"input","value":"1"}}', '1588131048', '1', '1'),
(165, 58, '{"text":{"type":"input","value":"\\u4f1a\\u5458\\u5230\\u671f\\u540e\\u6743\\u76ca\\u5373\\u5931\\u6548\\uff0c\\u9700\\u7ee7\\u7eed\\u4eab\\u53d7\\u6743\\u76ca\\u8bf7\\u53ca\\u65f6\\u7eed\\u8d39"},"sort":{"type":"input","value":"2"}}', '1588131059', '2', '1'),
(166, 58, '{"text":{"type":"input","value":"\\u62fc\\u56e2\\u6d3b\\u52a8\\u4ef7\\u65e0\\u4f1a\\u5458\\u4f18\\u60e0"},"sort":{"type":"input","value":"3"}}', '1588131073', '3', '1'),
(170, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u9c9c\\u82b1\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"2\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"66\",\"520\",\"999\",\"1314\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/da616202007011009436251.png\"}}', 1590739724, 9, 1),
(171, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7231\\u5fc3\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"1\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/5a2d520200701101025960.png\"}}', 1590740368, 10, 1),
(172, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u6c14\\u7403\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"6\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\",\"999\",\"1314\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/0c648202007011012055342.png\"}}', 1593569535, 7, 1),
(173, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7687\\u51a0\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"20\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/82d4c202007011013025359.png\"}}', 1593569588, 5, 1),
(174, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u5956\\u676f\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"15\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/1caae202007011013442033.png\"}}', 1593569629, 6, 1),
(175, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u9526\\u9ca4\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"30\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/226e5202007011015051008.png\"}}', 1593569720, 4, 1),
(176, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u5609\\u5e74\\u534e\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"50\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/22c8e202007011017537720.png\"}}', 1593569880, 3, 1),
(177, 59, '{\"live_gift_name\":{\"type\":\"input\",\"value\":\"\\u7c89\\u7b14\"},\"live_gift_price\":{\"type\":\"input\",\"value\":\"5\"},\"live_gift_num\":{\"type\":\"checkbox\",\"value\":[\"1\",\"5\",\"10\",\"20\",\"66\",\"99\",\"520\"]},\"live_gift_show_img\":{\"type\":\"upload\",\"value\":\"http:\\/\\/cremb-zsff.oss-cn-beijing.aliyuncs.com\\/96ea420200701101917287.png\"}}', 1593569968, 8, 1);

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_log`
--

CREATE TABLE `eb_system_log` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '管理员操作记录ID',
  `admin_id` int(10) UNSIGNED NOT NULL COMMENT '管理员id',
  `admin_name` varchar(64) NOT NULL COMMENT '管理员姓名',
  `path` varchar(128) NOT NULL COMMENT '链接',
  `page` varchar(64) NOT NULL COMMENT '行为',
  `method` varchar(12) NOT NULL COMMENT '访问类型',
  `ip` varchar(16) NOT NULL COMMENT '登录IP',
  `type` varchar(32) NOT NULL COMMENT '类型',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '操作时间',
  `merchant_id` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '商户id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员操作记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_menus`
--

CREATE TABLE `eb_system_menus` (
  `id` smallint(5) UNSIGNED NOT NULL COMMENT '菜单ID',
  `pid` smallint(5) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父级id',
  `icon` varchar(16) NOT NULL COMMENT '图标',
  `menu_name` varchar(32) NOT NULL DEFAULT '' COMMENT '按钮名',
  `module` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '模块名',
  `controller` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '控制器',
  `action` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '' COMMENT '方法名',
  `params` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '[]' COMMENT '参数',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序',
  `is_show` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '是否显示',
  `access` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '子管理员是否可用'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='菜单表';

--
-- 转存表中的数据 `eb_system_menus`
--

INSERT INTO `eb_system_menus` (`id`, `pid`, `icon`, `menu_name`, `module`, `controller`, `action`, `params`, `sort`, `is_show`, `access`) VALUES
(1, 289, '', '系统设置', 'admin', 'setting.systemConfig', 'index', '[]', 265, 1, 1),
(2, 153, '', '权限规则', 'admin', 'setting.systemMenus', 'index', '{\"cate\":\"12\"}', 7, 1, 1),
(4, 153, '', '管理员列表', 'admin', 'setting.systemAdmin', 'index', '[]', 9, 1, 1),
(6, 1, '', '基础配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"1\",\"type\":\"999\"}', 270, 1, 1),
(7, 497, '', '配置分类', 'admin', 'setting.systemConfigTab', 'index', '[]', 277, 1, 1),
(8, 153, '', '身份管理', 'admin', 'setting.systemRole', 'index', '[]', 10, 1, 1),
(9, 497, '', '组合数据', 'admin', 'setting.systemGroup', 'index', '[]', 276, 1, 1),
(11, 0, 'wechat', '公众号', 'admin', 'wechat.wechat', 'index', '[]', 91, 1, 1),
(12, 354, '', '微信关注回复', 'admin', 'wechat.reply', 'index', '{\"key\":\"subscribe\",\"title\":\"\\u7f16\\u8f91\\u65e0\\u914d\\u7f6e\\u9ed8\\u8ba4\\u56de\\u590d\"}', 86, 1, 1),
(17, 360, '', '微信菜单', 'admin', 'wechat.menus', 'index', '[]', 95, 1, 1),
(19, 11, '', '图文管理', 'admin', 'wechat.wechatNewsCategory', 'index', '[]', 60, 1, 1),
(21, 0, 'magic', '维护', 'admin', 'system.system', '', '[]', 1, 1, 1),
(30, 354, '', '关键字回复', 'admin', 'wechat.reply', 'keyword', '[]', 85, 1, 1),
(31, 354, '', '无效关键词回复', 'admin', 'wechat.reply', 'index', '{\"key\":\"default\",\"title\":\"\\u7f16\\u8f91\\u65e0\\u6548\\u5173\\u952e\\u5b57\\u9ed8\\u8ba4\\u56de\\u590d\"}', 84, 1, 1),
(37, 31, '', '附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(42, 19, '', '附加权限', 'admin', 'wechat.wechatNewsCategory', '', '[]', 0, 0, 1),
(43, 42, '', '添加图文消息', 'admin', 'wechat.wechatNewsCategory', 'create', '[]', 0, 0, 1),
(44, 42, '', '编辑图文消息', 'admin', 'wechat.wechatNewsCategory', 'edit', '[]', 0, 0, 1),
(45, 42, '', '删除图文消息', 'admin', 'wechat.wechatNewsCategory', 'delete', '[]', 0, 0, 1),
(46, 7, '', '配置分类附加权限', 'admin', 'setting.systemConfigTab', '', '[]', 0, 0, 1),
(47, 46, '', '添加配置分类', 'admin', 'setting.systemConfigTab', 'create', '[]', 0, 0, 1),
(48, 46, '', '添加配置', 'admin', 'setting.systemConfig', 'create', '[]', 0, 0, 1),
(49, 46, '', '编辑配置分类', 'admin', 'setting.systemConfigTab', 'edit', '[]', 0, 0, 1),
(50, 46, '', '删除配置分类', 'admin', 'setting.systemConfigTab', 'delete', '[]', 0, 0, 1),
(51, 46, '', '查看子字段', 'admin', 'system.systemConfigTab', 'sonConfigTab', '[]', 0, 0, 1),
(52, 9, '', '组合数据附加权限', 'admin', 'system.systemGroup', '', '[]', 0, 0, 1),
(53, 52, '', '添加数据', 'admin', 'system.systemGroupData', 'create', '[]', 0, 0, 1),
(54, 52, '', '编辑数据', 'admin', 'system.systemGroupData', 'edit', '[]', 0, 0, 1),
(55, 52, '', '删除数据', 'admin', 'system.systemGroupData', 'delete', '[]', 0, 0, 1),
(56, 52, '', '数据列表', 'admin', 'system.systemGroupData', 'index', '[]', 0, 0, 1),
(57, 52, '', '添加数据组', 'admin', 'system.systemGroup', 'create', '[]', 0, 0, 1),
(58, 52, '', '删除数据组', 'admin', 'system.systemGroup', 'delete', '[]', 0, 0, 1),
(59, 4, '', '管理员列表附加权限', 'admin', 'system.systemAdmin', '', '[]', 0, 0, 1),
(60, 59, '', '添加管理员', 'admin', 'system.systemAdmin', 'create', '[]', 0, 0, 1),
(61, 59, '', '编辑管理员', 'admin', 'system.systemAdmin', 'edit', '[]', 0, 0, 1),
(62, 59, '', '删除管理员', 'admin', 'system.systemAdmin', 'delete', '[]', 0, 0, 1),
(63, 8, '', '身份管理附加权限', 'admin', 'system.systemRole', '', '[]', 0, 0, 1),
(64, 63, '', '添加身份', 'admin', 'system.systemRole', 'create', '[]', 0, 0, 1),
(65, 63, '', '修改身份', 'admin', 'system.systemRole', 'edit', '[]', 0, 0, 1),
(66, 63, '', '删除身份', 'admin', 'system.systemRole', 'delete', '[]', 0, 0, 1),
(67, 8, '', '身份管理展示页', 'admin', 'system.systemRole', 'index', '[]', 0, 0, 1),
(68, 4, '', '管理员列表展示页', 'admin', 'system.systemAdmin', 'index', '[]', 0, 0, 1),
(69, 7, '', '配置分类展示页', 'admin', 'setting.systemConfigTab', 'index', '[]', 0, 0, 1),
(70, 9, '', '组合数据展示页', 'admin', 'system.systemGroup', 'index', '[]', 0, 0, 1),
(73, 19, '', '图文消息展示页', 'admin', 'wechat.wechatNewsCategory', 'index', '[]', 0, 0, 1),
(74, 2, '', '菜单管理附加权限', 'admin', 'system.systemMenus', '', '[]', 0, 0, 1),
(75, 74, '', '添加菜单', 'admin', 'system.systemMenus', 'create', '[]', 0, 0, 1),
(76, 74, '', '编辑菜单', 'admin', 'system.systemMenus', 'edit', '[]', 0, 0, 1),
(77, 74, '', '删除菜单', 'admin', 'system.systemMenus', 'delete', '[]', 0, 0, 1),
(78, 2, '', '菜单管理展示页', 'admin', 'system.systemMenus', 'index', '[]', 0, 0, 1),
(89, 30, '', '关键字回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(90, 89, '', '添加关键字', 'admin', 'wechat.reply', 'add_keyword', '[]', 0, 0, 1),
(91, 89, '', '修改关键字', 'admin', 'wechat.reply', 'info_keyword', '[]', 0, 0, 1),
(92, 89, '', '删除关键字', 'admin', 'wechat.reply', 'delete', '[]', 0, 0, 1),
(93, 30, '', '关键字回复展示页', 'admin', 'wechat.reply', 'keyword', '[]', 0, 0, 1),
(94, 31, '', '无效关键词回复展示页', 'admin', 'wechat.reply', 'index', '[]', 0, 0, 1),
(95, 31, '', '无效关键词回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(96, 95, '', '无效关键词回复提交按钮', 'admin', 'wechat.reply', 'save', '{\"key\":\"default\",\"title\":\"编辑无效关键字默认回复\"}', 0, 0, 1),
(97, 12, '', '微信关注回复展示页', 'admin', 'wechat.reply', 'index', '[]', 0, 0, 1),
(98, 12, '', '微信关注回复附加权限', 'admin', 'wechat.reply', '', '[]', 0, 0, 1),
(99, 98, '', '微信关注回复提交按钮', 'admin', 'wechat.reply', 'save', '{\"key\":\"subscribe\",\"title\":\"编辑无配置默认回复\"}', 0, 0, 1),
(100, 74, '', '添加提交菜单', 'admin', 'system.systemMenus', 'save', '[]', 0, 0, 1),
(101, 74, '', '编辑提交菜单', 'admin', 'system.systemMenus', 'update', '[]', 0, 1, 1),
(102, 59, '', '提交添加管理员', 'admin', 'system.systemAdmin', 'save', '[]', 0, 0, 1),
(103, 59, '', '提交修改管理员', 'admin', 'system.systemAdmin', 'update', '[]', 0, 0, 1),
(104, 63, '', '提交添加身份', 'admin', 'system.systemRole', 'save', '[]', 0, 0, 1),
(105, 63, '', '提交修改身份', 'admin', 'system.systemRole', 'update', '[]', 0, 0, 1),
(106, 46, '', '提交添加配置分类', 'admin', 'setting.systemConfigTab', 'save', '[]', 0, 0, 1),
(107, 46, '', '提交修改配置分类', 'admin', 'setting.systemConfigTab', 'update', '[]', 0, 0, 1),
(108, 46, '', '提交添加配置列表', 'admin', 'setting.systemConfig', 'save', '[]', 0, 0, 1),
(109, 52, '', '提交添加数据组', 'admin', 'system.systemGroup', 'save', '[]', 0, 0, 1),
(110, 52, '', '提交修改数据组', 'admin', 'system.systemGroup', 'update', '[]', 0, 0, 1),
(111, 52, '', '提交添加数据', 'admin', 'system.systemGroupData', 'save', '[]', 0, 0, 1),
(112, 52, '', '提交修改数据', 'admin', 'system.systemGroupData', 'update', '[]', 0, 0, 1),
(115, 42, '', '提交添加图文消息', 'admin', 'wechat.wechatNewsCategory', 'save', '[]', 0, 0, 1),
(116, 42, '', '提交编辑图文消息', 'admin', 'wechat.wechatNewsCategory', 'update', '[]', 0, 0, 1),
(117, 6, '', '配置列表附加权限', 'admin', 'setting.systemConfig', '', '[]', 0, 0, 1),
(118, 6, '', '配置列表展示页', 'admin', 'setting.systemConfig', 'index', '[]', 0, 0, 1),
(119, 117, '', '提交保存配置列表', 'admin', 'setting.systemConfig', 'save_basics', '[]', 0, 0, 1),
(123, 89, '', '提交添加关键字', 'admin', 'wechat.reply', 'save_keyword', '{\"dis\":\"1\"}', 0, 0, 1),
(124, 89, '', '提交修改关键字', 'admin', 'wechat.reply', 'save_keyword', '{\"dis\":\"2\"}', 0, 0, 1),
(126, 17, '', '微信菜单展示页', 'admin', 'wechat.menus', 'index', '[]', 0, 0, 1),
(127, 17, '', '微信菜单附加权限', 'admin', 'wechat.menus', '', '[]', 0, 0, 1),
(128, 127, '', '提交微信菜单按钮', 'admin', 'wechat.menus', 'save', '{\"dis\":\"1\"}', 0, 0, 1),
(130, 498, '', '系统日志', 'admin', 'system.systemLog', 'index', '[]', 274, 1, 1),
(131, 130, '', '管理员操作记录展示页', 'admin', 'system.systemLog', 'index', '[]', 0, 0, 1),
(137, 135, '', '添加通知模板', 'admin', 'system.systemNotice', 'create', '[]', 0, 0, 1),
(138, 135, '', '编辑通知模板', 'admin', 'system.systemNotice', 'edit', '[]', 0, 0, 1),
(139, 135, '', '删除辑通知模板', 'admin', 'system.systemNotice', 'delete', '[]', 0, 0, 1),
(140, 135, '', '提交编辑辑通知模板', 'admin', 'system.systemNotice', 'update', '[]', 0, 0, 1),
(141, 135, '', '提交添加辑通知模板', 'admin', 'system.systemNotice', 'save', '[]', 0, 0, 1),
(144, 117, '', '获取配置列表上传文件的名称', 'admin', 'setting.systemConfig', 'getimagename', '[]', 0, 0, 1),
(145, 117, '', '配置列表上传文件', 'admin', 'setting.systemConfig', 'view_upload', '[]', 0, 0, 1),
(148, 286, '', '优惠券', '', '', '', '[]', 10, 0, 1),
(149, 148, '', '优惠券制作', 'admin', 'ump.storeCoupon', 'index', '[]', 5, 1, 1),
(150, 148, '', '会员领取记录', 'admin', 'ump.storeCouponUser', 'index', '[]', 1, 1, 1),
(151, 0, 'user', '用户', 'admin', 'user.userList', 'list', '[]', 286, 1, 1),
(153, 289, '', '权限管理', 'admin', 'setting.systemAdmin', '', '[]', 256, 1, 1),
(155, 154, '', '商户产品展示页', 'admin', 'store.storeMerchant', 'index', '[]', 0, 0, 1),
(156, 154, '', '商户产品附加权限', 'admin', 'store.storeMerchant', '', '[]', 0, 0, 1),
(158, 157, '', '商户文章管理展示页', 'admin', 'wechat.wechatNews', 'merchantIndex', '[]', 0, 0, 1),
(159, 157, '', '商户文章管理附加权限', 'admin', 'wechat.wechatNews', '', '[]', 0, 0, 1),
(173, 498, '', '文件校验', 'admin', 'system.system_file', 'index', '[]', 273, 1, 1),
(174, 360, '', '微信模板消息', 'admin', 'wechat.wechatTemplate', 'index', '[]', 1, 1, 1),
(175, 289, '', '客服管理', 'admin', 'wechat.storeService', 'index', '[]', 257, 1, 1),
(177, 151, '', '用户列表', 'admin', 'user.user', 'index', '[]', 285, 1, 1),
(179, 307, '', '充值记录', 'admin', 'finance.userRecharge', 'index', '[]', 1, 1, 1),
(209, 175, '', '客服管理展示页', 'admin', 'store.storeService', 'index', '[]', 0, 0, 1),
(210, 175, '', '客服管理附加权限', 'admin', 'store.storeService', '', '[]', 0, 0, 1),
(211, 210, '', '客服管理添加', 'admin', 'store.storeService', 'create', '[]', 0, 0, 1),
(212, 210, '', '客服管理添加提交', 'admin', 'store.storeService', 'save', '[]', 0, 0, 1),
(213, 210, '', '客服管理编辑', 'admin', 'store.storeService', 'edit', '[]', 0, 0, 1),
(214, 210, '', '客服管理编辑提交', 'admin', 'store.storeService', 'update', '[]', 0, 0, 1),
(215, 210, '', '客服管理删除', 'admin', 'store.storeService', 'delete', '[]', 0, 0, 1),
(216, 179, '', '用户充值记录展示页', 'admin', 'user.userRecharge', 'index', '[]', 0, 0, 1),
(217, 179, '', '用户充值记录附加权限', 'admin', 'user.userRecharge', '', '[]', 0, 0, 1),
(218, 217, '', '用户充值记录退款', 'admin', 'user.userRecharge', 'edit', '[]', 0, 0, 1),
(219, 217, '', '用户充值记录退款提交', 'admin', 'user.userRecharge', 'updaterefundy', '[]', 0, 0, 1),
(220, 180, '', '预售卡管理批量修改预售卡金额', 'admin', 'presell.presellCard', 'batch_price', '[]', 0, 0, 1),
(221, 180, '', '预售卡管理批量修改预售卡金额提交', 'admin', 'presell.presellCard', 'savebatch', '[]', 0, 0, 1),
(222, 210, '', '客服管理聊天记录查询', 'admin', 'store.storeService', 'chat_user', '[]', 0, 0, 1),
(223, 210, '', '客服管理聊天记录查询详情', 'admin', 'store.storeService', 'chat_list', '[]', 0, 0, 1),
(229, 149, '', '优惠券管理展示页', 'admin', 'store.storeCoupon', 'index', '[]', 0, 0, 1),
(230, 149, '', '优惠券管理附加权限', 'admin', 'store.storeCoupon', '', '[]', 0, 0, 1),
(231, 230, '', '优惠券管理添加', 'admin', 'store.storeCoupon', 'create', '[]', 0, 0, 1),
(232, 230, '', '优惠券管理添加提交', 'admin', 'store.storeCoupon', 'save', '[]', 0, 0, 1),
(233, 230, '', '优惠券管理删除', 'admin', 'store.storeCoupon', 'delete', '[]', 0, 0, 1),
(234, 230, '', '优惠券管理立即失效', 'admin', 'store.storeCoupon', 'status', '[]', 0, 0, 1),
(235, 148, '', '已发布管理', 'admin', 'ump.storeCouponIssue', 'index', '[]', 3, 1, 1),
(237, 21, '', '刷新缓存', 'admin', 'system.clear', 'index', '[]', 0, 1, 1),
(239, 306, '', '提现申请', 'admin', 'finance.user_extract', 'index', '[]', 0, 1, 1),
(241, 273, '', '限时秒杀', 'admin', 'ump.storeSeckill', 'index', '[]', 0, 1, 1),
(244, 294, '', '财务报表', 'admin', 'record.storeStatistics', 'index', '[]', 0, 1, 1),
(245, 293, '', '商品统计', 'admin', 'store.storeProduct', 'statistics', '[]', 0, 0, 1),
(246, 295, '', '用户统计', 'admin', 'user.user', 'user_analysis', '[]', 0, 1, 1),
(247, 153, '', '个人资料', 'admin', 'setting.systemAdmin', 'admininfo', '[]', 0, 0, 1),
(248, 247, '', '个人资料附加权限', 'admin', 'system.systemAdmin', '', '[]', 0, 0, 1),
(249, 248, '', '个人资料提交保存', 'admin', 'system.systemAdmin', 'setAdminInfo', '[]', 0, 0, 1),
(250, 247, '', '个人资料展示页', 'admin', 'system.systemAdmin', 'admininfo', '[]', 0, 0, 1),
(251, 293, '', '订单统计', 'admin', 'order.storeOrder', 'orderchart', '[]', 0, 1, 1),
/*(252, 21, '', '在线更新', 'admin', 'system.system_upgradeclient', 'index', '[]', 0, 1, 1),*/
/*(267, 360, '', '公众号接口配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"1\",\"tab_id\":\"2\"}', 100, 1, 1),*/
(269, 0, 'cubes', '小程序', 'admin', 'setting.system', '', '[]', 92, 0, 1),
(270, 269, '', '小程序配置', 'admin', 'setting.systemConfig', 'index_alone', '{\"type\":\"2\",\"tab_id\":\"7\"}', 0, 1, 1),
(273, 286, '', '秒杀管理', 'admin', '', '', '[]', 0, 0, 1),
(276, 21, '', '附件管理', 'admin', 'widget.images', 'index', '[]', 0, 0, 1),
(278, 498, '', '清除数据', 'admin', 'system.system_cleardata', 'index', '[]', 271, 1, 1),
(286, 0, 'paper-plane', '营销', 'admin', '', '', '[]', 105, 1, 1),
(287, 0, 'money', '财务', 'admin', '', '', '[]', 103, 1, 1),
(289, 0, 'gear', '设置', 'admin', '', '', '[]', 90, 1, 1),
(306, 287, '', '财务操作', 'admin', '', '', '[]', 100, 1, 1),
(307, 287, '', '财务记录', 'admin', '', '', '[]', 50, 1, 1),
(308, 287, '', '佣金记录', 'admin', '', '', '[]', 1, 1, 1),
(312, 307, '', '资金监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"now_money\"}', 0, 1, 1),
(313, 308, '', '佣金记录', 'admin', 'finance.finance', 'commission_list', '[]', 0, 1, 1),
(337, 0, 'users', '分销', 'admin', 'user', 'user', '[]', 106, 1, 1),
(349, 286, '', '积分', 'admin', 'userPoint', 'index', '[]', 0, 0, 1),
(350, 349, '', '积分配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"3\",\"tab_id\":\"11\"}', 0, 1, 1),
(351, 349, '', '积分日志', 'admin', 'ump.userPoint', 'index', '[]', 0, 1, 1),
(352, 148, '', '优惠券配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"3\",\"tab_id\":\"12\"}', 0, 1, 1),
(353, 337, '', '分销配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"3\",\"tab_id\":\"9\"}', 282, 1, 1),
(354, 11, '', '自动回复', '', '', '', '[]', 80, 1, 1),
/*(355, 11, '', '页面设置', '', '', '', '[]', 90, 1, 1),*/
(357, 507, '', '首页轮播图', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"34\"}', 463, 1, 1),
(360, 11, '', '公众号配置', '', '', '', '[]', 100, 1, 1),
/*(361, 360, '', '公众号支付配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"1\",\"tab_id\":\"4\"}', 0, 1, 1),*/
(362, 276, '', '附加权限', '', '', '', '[]', 0, 1, 1),
(363, 362, '', '上传图片', 'admin', 'widget.images', 'upload', '[]', 0, 1, 1),
(364, 362, '', '删除图片', 'admin', 'widget.images', 'delete', '[]', 0, 1, 1),
(365, 362, '', '附件管理', 'admin', 'widget.images', 'index', '[]', 0, 1, 1),
(372, 269, '', '首页幻灯片', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"48\"}', 0, 1, 1),
(373, 269, '', '分享背景', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"54\"}', 0, 1, 1),
(374, 295, '', '分销会员业务', 'admin', 'record.record', 'user_distribution_chart', '[]', 0, 1, 1),
(375, 269, '', '小程序支付配置', 'admin', 'setting.systemConfig', 'index_alone', '{\"type\":\"2\",\"tab_id\":\"14\"}', 0, 1, 1),
(376, 269, '', '小程序模板消息', 'admin', 'routine.routineTemplate', 'index', '[]', 0, 1, 1),
(377, 498, '', '数据库维护', 'admin', 'system.system_databackup', 'index', '[]', 272, 1, 1),
(378, 286, '', '砍价管理', 'admin', '', '', '[]', 0, 0, 1),
(379, 286, '', '拼团列表', 'admin', 'ump.store_combination', 'combina_list', '[]', 279, 1, 1),
(380, 378, '', '砍价产品', 'admin', 'ump.storeBargain', 'index', '[]', 0, 1, 1),
(391, 269, '', '配送时效', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"49\"}', 0, 1, 1),
(392, 269, '', '小程序图片配置', 'admin', 'setting.system_config', 'index_alone', '{\"type\":\"2\",\"tab_id\":\"15.html\"}', 0, 1, 1),
(399, 0, 'th-list', '订单', 'admin', '', '', '[]', 298, 1, 1),
(400, 399, '', '订单管理', 'admin', 'order.store_order', 'index', '[]', 100, 1, 1),
(409, 507, '', '推荐设置', 'admin', 'setting.system_group_data', 'recommend', '[]', 261, 1, 1),
(411, 0, 'th', '专题', 'admin', '', '', '[]', 297, 1, 1),
(417, 0, 'picture-o', '新闻', 'admin', '', '', '[]', 290, 1, 1),
(418, 417, '', '新闻列表', 'admin', 'article.article_v1', 'index', '[]', 289, 1, 1),
(419, 417, '', '搜索关键词', 'admin', 'article.search', 'index', '[]', 0, 0, 1),
(420, 502, '', '关键词搜索', 'admin', 'article.search', 'index', '[]', 258, 1, 1),
(421, 337, '', '分销员列表', 'admin', 'user.user_spread', 'index', '[]', 287, 1, 1),
(423, 482, '', '课程弹幕', 'admin', 'special.special_type', 'special_barrage', '{\"type\":\"3\"}', 291, 1, 1),
(428, 0, 'leaf', '分类', 'admin', '', '', '[]', 300, 1, 1),
(429, 428, '', '分类管理', 'admin', 'special.grade', 'index', '[]', 299, 1, 1),
(430, 428, '', '二级分类', 'admin', 'special.subject', 'index', '[]', 298, 0, 1),
(431, 507, '', '导航配置', 'admin', 'setting.system_group_data', 'navigation', '[]', 264, 1, 1),
(432, 507, '', '活动区域', 'admin', 'setting.system_group_data', 'index_v1', '{\"gid\":\"56\"}', 262, 1, 1),
(433, 337, '', '提现银行配置', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"52\"}', 281, 1, 1),
(434, 502, '', '关于我们', 'admin', 'setting.system_config_content', 'index', '{\"id\":\"1\"}', 259, 1, 1),
(435, 502, '', '用户付费协议', 'admin', 'setting.system_config_content', 'index', '{\"id\":\"2\"}', 259, 1, 1),
(454, 429, '', '删除分类', 'admin', 'special.grade', 'delete', '[]', 0, 0, 1),
(455, 429, '', '保存分类', 'admin', 'special.grade', 'save', '[]', 0, 0, 1),
(456, 429, '', '快速编辑分类', 'admin', 'special.grade', 'set_value', '[]', 0, 0, 1),
(457, 429, '', '创建分类', 'admin', 'special.grade', 'create', '[]', 0, 0, 1),
(458, 430, '', '删除分类', 'admin', 'special.subject', 'delete', '[]', 0, 0, 1),
(459, 430, '', '快速编辑分类', 'admin', 'special.subject', 'set_value', '[]', 0, 0, 1),
(460, 430, '', '创建分类页面', 'admin', 'special.subject', 'create', '[]', 0, 0, 1),
(461, 400, '', '订单展示页面', 'admin', 'order.store_order', 'order_list', '[]', 0, 0, 1),
(462, 400, '', '修改订单金额', 'admin', 'order.store_order', 'edit', '[]', 0, 0, 1),
(463, 400, '', '保存订单金额', 'admin', 'order.store_order', 'update', '[]', 0, 0, 1),
(464, 400, '', '送货', 'admin', 'order.store_order', 'delivery', '[]', 0, 0, 1),
(465, 400, '', '发货', 'admin', 'order.store_order', 'deliver_goods', '[]', 0, 0, 1),
(466, 400, '', '发货保存', 'admin', 'order.store_order', 'updateDeliveryGoods', '[]', 0, 0, 1),
(467, 400, '', '确认收货', 'admin', 'order.store_order', 'take_delivery', '[]', 0, 0, 1),
(468, 400, '', '退款页面', 'admin', 'order.store_order', 'refund_y', '[]', 0, 0, 1),
(469, 400, '', '退款处理', 'admin', 'order.store_order', 'updateRefundY', '[]', 0, 0, 1),
(470, 400, '', '订单详情', 'admin', 'order.store_order', 'order_info', '[]', 0, 0, 1),
(471, 400, '', '物流查询', 'admin', 'order.store_order', 'express', '[]', 0, 0, 1),
(472, 400, '', '修改配送信息', 'admin', 'order.store_order', 'distribution', '[]', 0, 0, 1),
(473, 400, '', '保存配送信息', 'admin', 'order.store_order', 'updateDistribution', '[]', 0, 0, 1),
(474, 400, '', '不退款保存页面', 'admin', 'order.store_order', 'refund_n', '[]', 0, 0, 1),
(475, 400, '', '保存不退款信息', 'admin', 'order.store_order', 'updateRefundN', '[]', 0, 0, 1),
(476, 400, '', '立即支付', 'admin', 'order.store_order', 'offline', '[]', 0, 0, 1),
(477, 400, '', '退积分', 'admin', 'order.store_order', 'integral_back', '[]', 0, 0, 1),
(478, 400, '', '退积分保存', 'admin', 'order.store_order', 'updateIntegralBack', '[]', 0, 0, 1),
(482, 0, 'book', '课程', 'admin', '', '', '[]', 299, 1, 1),
(483, 482, '', '图文专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"1\"}', 294, 1, 1),
(484, 482, '', '音频专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"2\"}', 295, 1, 1),
(485, 482, '', '视频专题', 'admin', 'special.special_type', 'index', '{\"special_type\":\"3\"}', 296, 1, 1),
(487, 513, '', '直播列表', 'admin', 'live.aliyun_live', 'special_live', '{\"special_type\":\"4\"}', 292, 1, 1),
(488, 482, '', '专栏列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"5\"}', 293, 1, 1),
(490, 493, '', '卡密会员', 'admin', 'user.member_card', 'batch_index', '[]', 252, 1, 1),
(491, 493, '', '会员等级', 'admin', 'user.member_ship', 'index', '[]', 254, 1, 1),
(492, 493, '', '会员记录', 'admin', 'user.member_record', 'index', '[]', 251, 1, 1),
(493, 151, '', '会员管理', 'admin', '', '', '[]', 283, 1, 1),
(496, 337, '', '提现银行规则', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"53\"}', 280, 1, 1),
(497, 21, '', '开发配置', 'admin', '', '', '[]', 278, 1, 1),
(498, 21, '', '安全维护', 'admin', '', '', '[]', 275, 1, 1),
(499, 1, '', '公众号配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"1\",\"tab_id\":\"2\"}', 269, 1, 1),
(500, 1, '', '支付配置', 'admin', 'setting.systemConfig', 'index', '{\"type\":\"4\",\"tab_id\":\"4\"}', 266, 1, 1),
(501, 1, '', '阿里云配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"17\",\"type\":\"5\"}', 265, 1, 1),
(502, 289, '', '商城设置', '', '', '', '[]', 264, 1, 1),
(505, 493, '', '会员权益', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"57\"}', 249, 1, 1),
(506, 493, '', '会员说明', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"58\"}', 248, 1, 1),
(507, 502, '', '首页配置', 'admin', '', '', '[]', 261, 1, 1),
(508, 502, '', '其他配置', 'admin', 'setting.systemConfig', 'index', '{\"tab_id\":\"22\",\"type\":\"6\"}', 260, 1, 1),
(512, 287, '', '充值配置', 'admin', 'setting.system_config', 'index', '{\"tab_id\":\"23\",\"type\":\"7\"}', 0, 1, 1),
(513, 0, 'video-camera', '直播', 'admin', '', '', '[]', 298, 1, 1),
/*(514, 513, '', '推荐课程', 'admin', 'live.aliyun_live', 'live_goods', '[]', 0, 1, 1),*/
(515, 513, '', '直播间管理', 'admin', 'live.aliyun_live', 'index', '{\"special_type\":\"4\",\"type\":\"2\"}', 290, 1, 1),
(516, 513, '', '礼物管理', 'admin', 'setting.system_group_data', 'index', '{\"gid\":\"59\"}', 289, 1, 1),
(517, 513, '', '直播贡献', 'admin', 'live.aliyun_live', 'live_reward', '[]', 288, 1, 1),
(518, 307, '', '虚拟币监控', 'admin', 'finance.finance', 'bill', '{\"category\":\"gold_num\"}', 0, 1, 1),
(519, 286, '', '活动管理', 'admin', 'ump.eventRegistration', 'index', '[]', 0, 1, 1),
(520, 485, '', '专题列表', 'admin', 'special.SpecialType', 'index', '{\"special_type\":\"3\"}', 1, 1, 1),
(521, 485, '', '素材列表', 'admin', 'special.SpecialType', 'source_index', '{\"special_type\":\"3\"}', 0, 1, 1),
(522, 484, '', '专题列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"2\"}', 1, 1, 1),
(523, 484, '', '素材列表', 'admin', 'special.special_type', 'source_index', '{\"special_type\":\"2\"}', 0, 1, 1),
(524, 483, '', '专题列表', 'admin', 'special.special_type', 'index', '{\"special_type\":\"1\"}', 1, 1, 1),
(525, 483, '', '素材列表', 'admin', 'special.special_type', 'source_index', '{\"special_type\":\"1\"}', 0, 1, 1),
(526, 417, '', '新闻分类', 'admin', 'article.article_category', 'index', '[]', 0, 1, 1),
(527, 286, '', '签到管理', 'admin', '', '', '[]', 0, 1, 1),
(528, 527, '', '签到海报', 'admin', 'user.signPoster', 'index', '[]', 0, 1, 1),
(529, 527, '', '签到记录', 'admin', 'user.userSign', 'index', '[]', 0, 1, 1);







-- --------------------------------------------------------

--
-- 表的结构 `eb_system_notice`
--

CREATE TABLE `eb_system_notice` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '通知模板id',
  `title` varchar(64) NOT NULL COMMENT '通知标题',
  `type` varchar(64) NOT NULL COMMENT '通知类型',
  `icon` varchar(16) NOT NULL COMMENT '图标',
  `url` varchar(64) NOT NULL COMMENT '链接',
  `table_title` varchar(256) NOT NULL COMMENT '通知数据',
  `template` varchar(64) NOT NULL COMMENT '通知模板',
  `push_admin` varchar(128) NOT NULL COMMENT '通知管理员id',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知模板表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_notice_admin`
--

CREATE TABLE `eb_system_notice_admin` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '通知记录ID',
  `notice_type` varchar(64) NOT NULL COMMENT '通知类型',
  `admin_id` smallint(5) UNSIGNED NOT NULL COMMENT '通知的管理员',
  `link_id` int(10) UNSIGNED NOT NULL COMMENT '关联ID',
  `table_data` text NOT NULL COMMENT '通知的数据',
  `is_click` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '点击次数',
  `is_visit` tinyint(3) UNSIGNED NOT NULL DEFAULT '0' COMMENT '访问次数',
  `visit_time` int(11) NOT NULL COMMENT '访问时间',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '通知时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='通知记录表' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `eb_system_role`
--

CREATE TABLE `eb_system_role` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '身份管理id',
  `role_name` varchar(32) NOT NULL COMMENT '身份管理名称',
  `sign` varchar(50) NOT NULL COMMENT '身份标识',
  `rules` text NOT NULL COMMENT '身份管理权限(menus_id)',
  `level` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='身份管理表';

--
-- 转存表中的数据 `eb_system_role`
--

INSERT INTO `eb_system_role` (`id`, `role_name`, `sign`, `rules`, `level`, `status`) VALUES
(1, '超级管理员','admin', '37,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61,62,63,64,65,66,67,68,69,70,73,74,75,76,77,78,89,90,91,92,93,94,95,96,97,98,99,100,102,103,104,105,106,107,108,109,110,111,112,115,116,117,118,119,123,124,126,127,128,131,137,138,139,140,141,144,145,148,155,156,158,159,209,210,211,212,213,214,215,216,217,218,219,220,221,222,223,229,230,231,232,233,234,245,247,248,249,250,269,273,276,288,349,378,419,436,437,438,439,440,441,442,443,444,445,446,447,448,449,450,451,452,453,454,455,456,457,458,459,460,461,462,463,464,465,466,467,468,469,470,471,472,473,474,475,476,477,478,1,2,4,6,7,8,9,11,12,17,19,21,30,31,101,130,149,150,151,153,173,174,175,177,179,235,237,239,241,244,246,251,252,267,270,278,286,287,289,293,306,307,308,312,313,337,350,351,352,353,354,355,357,360,361,362,363,364,365,372,373,374,375,376,377,379,380,391,392,399,400,409,411,412,415,417,418,420,421,423,428,429,430,431,432,433,434,435,479,480,481', 0, 1),
(2, '主播', 'anchor','', 1, 1),
(3, '核销员', 'verification', '518,286', 1, 1);


-- --------------------------------------------------------

--
-- 表的结构 `eb_user`
--

CREATE TABLE `eb_user` (
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `account` varchar(32) NOT NULL COMMENT '用户账号',
  `pwd` varchar(32) NOT NULL COMMENT '用户密码',
  `nickname` varchar(16) NOT NULL COMMENT '用户昵称',
  `avatar` varchar(256) NOT NULL COMMENT '用户头像',
  `name` varchar(255) DEFAULT NULL COMMENT '姓名',
  `phone` char(15) NOT NULL COMMENT '手机号码',
  `grade_id` int(11) NOT NULL COMMENT '年级id',
  `full_name` varchar(35) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `bank_card` varchar(50) NOT NULL DEFAULT '' COMMENT '银行卡号码',
  `add_time` int(11) UNSIGNED NOT NULL COMMENT '添加时间',
  `add_ip` varchar(16) NOT NULL COMMENT '添加ip',
  `last_time` int(11) UNSIGNED NOT NULL COMMENT '最后一次登录时间',
  `last_ip` varchar(16) NOT NULL COMMENT '最后一次登录ip',
  `now_money` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '用户余额',
  `gold_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '虚拟币余额',
  `brokerage_price` decimal(8,2) NOT NULL DEFAULT '0.00' COMMENT '可提现金额',
  `integral` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '用户剩余积分',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1为正常，0为禁止',
  `level` tinyint(2) UNSIGNED NOT NULL DEFAULT '0' COMMENT '等级',
  `spread_uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '推广元id',
  `spread_time` int(11) NOT NULL,
  `valid_time` int(11) NOT NULL DEFAULT '0' COMMENT '客户关系解除时间',
  `user_type` varchar(32) NOT NULL COMMENT '用户类型',
  `is_promoter` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否为推广员',
  `pay_count` int(11) UNSIGNED DEFAULT '0' COMMENT '用户购买次数',
  `is_binding` int(11) NOT NULL,
  `is_senior` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否为高级推广人',
  `is_h5user` tinyint(1) DEFAULT '0' COMMENT '是否为H5虚拟用户',
  `is_permanent` tinyint(2) DEFAULT '0' COMMENT '会员是否永久',
  `overdue_time` int(11) UNSIGNED DEFAULT '0' COMMENT '会员过期时间',
  `client_id` varchar(50) NOT NULL COMMENT 'client_id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_address`
--

CREATE TABLE `eb_user_address` (
  `id` mediumint(8) UNSIGNED NOT NULL COMMENT '用户地址id',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `real_name` varchar(32) NOT NULL COMMENT '收货人姓名',
  `phone` varchar(16) NOT NULL COMMENT '收货人电话',
  `province` varchar(64) NOT NULL COMMENT '收货人所在省',
  `city` varchar(64) NOT NULL COMMENT '收货人所在市',
  `district` varchar(64) NOT NULL COMMENT '收货人所在区',
  `detail` varchar(256) NOT NULL COMMENT '收货人详细地址',
  `post_code` int(10) UNSIGNED NOT NULL COMMENT '邮编',
  `longitude` varchar(16) NOT NULL DEFAULT '0' COMMENT '经度',
  `latitude` varchar(16) NOT NULL DEFAULT '0' COMMENT '纬度',
  `is_default` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否默认',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户地址表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_bill`
--

CREATE TABLE `eb_user_bill` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '用户账单id',
  `uid` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户uid',
  `get_uid` int(11) NOT NULL DEFAULT '0' COMMENT '由谁获得',
  `link_id` varchar(32) NOT NULL DEFAULT '0' COMMENT '关联id',
  `pm` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = 支出 1 = 获得',
  `title` varchar(64) NOT NULL COMMENT '账单标题',
  `category` varchar(64) NOT NULL COMMENT '明细种类',
  `type` varchar(64) NOT NULL DEFAULT '' COMMENT '明细类型',
  `number` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '明细数字',
  `balance` decimal(8,2) UNSIGNED NOT NULL DEFAULT '0.00' COMMENT '剩余',
  `mark` varchar(512) NOT NULL COMMENT '备注',
  `add_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '添加时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0 = 带确定 1 = 有效 -1 = 无效'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户账单表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_enter`
--

CREATE TABLE `eb_user_enter` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '商户申请ID',
  `uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
  `province` varchar(32) NOT NULL COMMENT '商户所在省',
  `city` varchar(32) NOT NULL COMMENT '商户所在市',
  `district` varchar(32) NOT NULL COMMENT '商户所在区',
  `address` varchar(256) NOT NULL COMMENT '商户详细地址',
  `merchant_name` varchar(256) NOT NULL COMMENT '商户名称',
  `link_user` varchar(32) NOT NULL,
  `link_tel` varchar(16) NOT NULL COMMENT '商户电话',
  `charter` varchar(512) NOT NULL COMMENT '商户证书',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间',
  `apply_time` int(10) UNSIGNED NOT NULL COMMENT '审核时间',
  `success_time` int(11) NOT NULL COMMENT '通过时间',
  `fail_message` varchar(256) NOT NULL COMMENT '未通过原因',
  `fail_time` int(10) UNSIGNED NOT NULL COMMENT '未通过时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '-1 审核未通过 0未审核 1审核通过',
  `is_lock` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '0 = 开启 1= 关闭',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商户申请表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_extract`
--

CREATE TABLE `eb_user_extract` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED DEFAULT NULL,
  `mer_id` int(11) NOT NULL COMMENT '商户id',
  `real_name` varchar(64) DEFAULT NULL COMMENT '名称',
  `partner_trade_no` varchar(32) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `extract_type` varchar(32) DEFAULT 'bank' COMMENT 'bank = 银行卡 alipay = 支付宝wx=微信',
  `bank_code` varchar(32) DEFAULT '0' COMMENT '银行卡',
  `bank_address` varchar(256) DEFAULT '' COMMENT '开户地址',
  `alipay_code` varchar(64) DEFAULT '' COMMENT '支付宝账号',
  `extract_price` decimal(8,2) UNSIGNED DEFAULT '0.00' COMMENT '提现金额',
  `mark` varchar(512) DEFAULT NULL,
  `balance` decimal(8,2) UNSIGNED DEFAULT '0.00',
  `fail_msg` varchar(128) DEFAULT NULL COMMENT '无效原因',
  `fail_time` int(10) UNSIGNED DEFAULT NULL,
  `add_time` int(10) UNSIGNED DEFAULT NULL COMMENT '添加时间',
  `status` tinyint(2) DEFAULT '0' COMMENT '-1 未通过 0 审核中 1 已提现',
  `wechat` varchar(15) DEFAULT NULL COMMENT '微信号'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户提现表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_group`
--

CREATE TABLE `eb_user_group` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `group_name` varchar(64) DEFAULT NULL COMMENT '用户分组名称'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户分组表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_notice`
--

CREATE TABLE `eb_user_notice` (
  `id` int(11) NOT NULL,
  `uid` text NOT NULL COMMENT '接收消息的用户id（类型：json数据）',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '消息通知类型（1：系统消息；2：用户通知）',
  `user` varchar(20) NOT NULL DEFAULT '' COMMENT '发送人',
  `title` varchar(20) NOT NULL COMMENT '通知消息的标题信息',
  `content` varchar(500) NOT NULL COMMENT '通知消息的内容',
  `add_time` int(11) NOT NULL COMMENT '通知消息发送的时间',
  `is_send` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发送（0：未发送；1：已发送）',
  `send_time` int(11) NOT NULL COMMENT '发送时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户通知表';

--
-- 转存表中的数据 `eb_user_notice`
--

INSERT INTO `eb_user_notice` (`id`, `uid`, `type`, `user`, `title`, `content`, `add_time`, `is_send`, `send_time`) VALUES
(1, ',6,', 2, '系统管理员', '请开通会员但未提供收货地址的会员联系客服', '请开通会员但未提供收货地址的会员联系客服', 1541742986, 1, 1541743080);

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_notice_see`
--

CREATE TABLE `eb_user_notice_see` (
  `id` int(11) NOT NULL,
  `nid` int(11) NOT NULL COMMENT '查看的通知id',
  `uid` int(11) NOT NULL COMMENT '查看通知的用户id',
  `add_time` int(11) NOT NULL COMMENT '查看通知的时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户通知发送记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_user_recharge`
--

CREATE TABLE `eb_user_recharge` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) DEFAULT NULL COMMENT '充值用户UID',
  `vip_id` int(11) NOT NULL DEFAULT '0' COMMENT '0为充值金额,',
  `order_id` varchar(32) DEFAULT NULL COMMENT '订单号',
  `price` decimal(8,2) DEFAULT NULL COMMENT '充值金额',
  `gold_num` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '虚拟币余额',
  `recharge_type` varchar(32) DEFAULT NULL COMMENT '充值类型',
  `paid` tinyint(1) DEFAULT NULL COMMENT '是否充值',
  `pay_time` int(10) DEFAULT NULL COMMENT '充值支付时间',
  `add_time` int(12) DEFAULT NULL COMMENT '充值时间',
  `refund_price` decimal(10,2) UNSIGNED NOT NULL COMMENT '退款金额'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户充值表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_media`
--

CREATE TABLE `eb_wechat_media` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '微信视频音频id',
  `type` varchar(16) NOT NULL COMMENT '回复类型',
  `path` varchar(128) NOT NULL COMMENT '文件路径',
  `media_id` varchar(64) NOT NULL COMMENT '微信服务器返回的id',
  `url` varchar(256) NOT NULL COMMENT '地址',
  `temporary` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否永久或者临时 0永久1临时',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信回复表';

--
-- 转存表中的数据 `eb_wechat_media`
--

INSERT INTO `eb_wechat_media` (`id`, `type`, `path`, `media_id`, `url`, `temporary`, `add_time`) VALUES
(12, 'image', '/public/uploads/wechat/image/5b042ca618139.jpg', '6sFx6PzPF2v_Lv4FGOMzzwcwmM2wuoA63ZMSxiN-7DY', 'http://mmbiz.qpic.cn/mmbiz_jpg/xVkDhuiaGm78WOdUXuPE1oYLnU4J0LCEiaSuLhwwSrfdyINspibXsllaj8rOMSs5estAv0qhGuGniaqhb6HftecPuw/0?wx_fmt=jpeg', 0, 1527000231);

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_message`
--

CREATE TABLE `eb_wechat_message` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '用户行为记录id',
  `openid` varchar(32) NOT NULL COMMENT '用户openid',
  `type` varchar(32) NOT NULL COMMENT '操作类型',
  `result` varchar(512) NOT NULL COMMENT '操作详细记录',
  `add_time` int(10) UNSIGNED NOT NULL COMMENT '操作时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户行为记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_news_category`
--

CREATE TABLE `eb_wechat_news_category` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '图文消息管理ID',
  `cate_name` varchar(255) NOT NULL COMMENT '图文名称',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `new_id` varchar(255) NOT NULL COMMENT '文章id',
  `add_time` varchar(255) NOT NULL COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='图文消息管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_qrcode`
--

CREATE TABLE `eb_wechat_qrcode` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '微信二维码ID',
  `third_type` varchar(32) NOT NULL COMMENT '二维码类型',
  `third_id` int(11) UNSIGNED NOT NULL COMMENT '用户id',
  `scan_id` int(10) NOT NULL COMMENT '扫描用户uid',
  `ticket` varchar(255) NOT NULL COMMENT '二维码参数',
  `expire_seconds` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '二维码有效时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态',
  `add_time` varchar(255) NOT NULL COMMENT '添加时间',
  `url` varchar(255) NOT NULL COMMENT '微信访问url',
  `qrcode_url` varchar(255) NOT NULL COMMENT '微信二维码url',
  `scan` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '被扫的次数'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信二维码管理表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_reply`
--

CREATE TABLE `eb_wechat_reply` (
  `id` mediumint(8) UNSIGNED NOT NULL COMMENT '微信关键字回复id',
  `key` varchar(64) NOT NULL COMMENT '关键字',
  `type` varchar(32) NOT NULL COMMENT '回复类型',
  `data` text NOT NULL COMMENT '回复数据',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '0=不可用  1 =可用',
  `hide` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否隐藏'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信关键字回复表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_template`
--

CREATE TABLE `eb_wechat_template` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '模板id',
  `tempkey` char(50) NOT NULL COMMENT '模板编号',
  `name` char(100) NOT NULL COMMENT '模板名',
  `content` varchar(1000) NOT NULL COMMENT '回复内容',
  `tempid` char(100) DEFAULT NULL COMMENT '模板ID',
  `add_time` varchar(15) NOT NULL COMMENT '添加时间',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信模板';

--
-- 转存表中的数据 `eb_wechat_template`
--

INSERT INTO `eb_wechat_template` (`id`, `tempkey`, `name`, `content`, `tempid`, `add_time`, `status`) VALUES
(3, 'OPENTM200565259', '订单发货提醒', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n物流公司：{{keyword2.DATA}}\n物流单号：{{keyword3.DATA}}\n{{remark.DATA}}', 'RRsyuuWpCo81xCtfG-5qYnXXoeSQHY4mTVav0zzaZsM', '1515052638', 1),
(4, 'OPENTM413386489', '订单收货通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n订单状态：{{keyword2.DATA}}\n收货时间：{{keyword3.DATA}}\n商品详情：{{keyword4.DATA}}\n{{remark.DATA}}', 'caAhoWioDb2A8Ew1bTr4GTe6mdsDoM4kjp9XV5BC8hg', '1515052765', 1),
(5, 'OPENTM410119152', '退款进度通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n订单金额：{{keyword2.DATA}}\n下单时间：{{keyword3.DATA}}\n{{remark.DATA}}', '-WH6gUzezKnX9OTam9VrQEVyNWfr1bUhT6FRuBMotZw', '1515053049', 1),
(6, 'OPENTM405847076', '帐户资金变动提醒', '{{first.DATA}}\n变动类型：{{keyword1.DATA}}\n变动时间：{{keyword2.DATA}}\n变动金额：{{keyword3.DATA}}\n{{remark.DATA}}', 'qYMdIRYj3EoNYjFljxxgcEnzWP5TCN8yRb553HeX-yE', '1515053127', 1),
(7, 'OPENTM207707249', '订单发货提醒', '\n{{first.DATA}}\n商品明细：{{keyword1.DATA}}\n下单时间：{{keyword2.DATA}}\n配送地址：{{keyword3.DATA}}\n配送人：{{keyword4.DATA}}\n联系电话：{{keyword5.DATA}}\n{{remark.DATA}}', 'hC9PFuxOKq6u5kNZyl6VdHGgAuA6h5I3ztpuDk1ioAk', '1515053313', 1),
(10, 'OPENTM407456411', '拼团成功通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n团购商品：{{keyword2.DATA}}\n{{remark.DATA}}', '0vOEi8iBqKXYsqvVep9Nn-CDaqPfqG8ajDpib2eY1S0', '1520063823', 1),
(11, 'OPENTM401113750', '拼团失败通知', '{{first.DATA}}\n拼团商品：{{keyword1.DATA}}\n商品金额：{{keyword2.DATA}}\n退款金额：{{keyword3.DATA}}\n{{remark.DATA}}', 'zFFr8ENLdKvVqA7Clo5ZB0WC6eARlYClCVDck-RIoSM', '1520064059', 1),
(12, 'OPENTM205213550', '订单生成通知', '{{first.DATA}}\n时间：{{keyword1.DATA}}\n商品名称：{{keyword2.DATA}}\n订单号：{{keyword3.DATA}}\n{{remark.DATA}}', 'pUMM82fuyNAeomd4mpzNz9_FdIxEehGTPR2l6q8GuCQ', '1528966701', 1),
(13, 'OPENTM207791277', '订单支付成功通知', '{{first.DATA}}\n订单编号：{{keyword1.DATA}}\n支付金额：{{keyword2.DATA}}\n{{remark.DATA}}', 'zQ0HVGaYM_zZoCiUG9E8IZiOD4X54G9e9ldUWkyuPvo', '1528966759', 1),
(15, 'OPENTM406772650', '拼团成功通知', '{first.DATA}}\n订单编号：{{keyword1.DATA}}\n团购商品：{{keyword2.DATA}}\n{{remark.DATA}}', '0vOEi8iBqKXYsqvVep9Nn-CDaqPfqG8ajDpib2eY1S0', '1551749779', 1),
(16, 'OPENTM411478702', '开团成功通知', '{{first.DATA}}\n商品名称：{{keyword1.DATA}}\n商品价格：{{keyword2.DATA}}\n组团人数：{{keyword3.DATA}}\n组团时间：{{keyword4.DATA}}\n{{remark.DATA}}', 'PDLbtKYeoWFjqwmvEb49s3zaqe1ufdXGEfZ5rt1XQLk', '1552034788', 1),
(17, 'OPENTM415198906', '拼单进度提醒', '\n{{first.DATA}}\n商品名称：{{keyword1.DATA}}\n拼单进度：{{keyword2.DATA}}\n{{remark.DATA}}', 'USG9thsiUOjyT9UISNiThdBgkq2Tl29cE6J39_vWdTo', '1562752076', 1),
(18, 'OPENTM405456204', '开播提醒', '{{first.DATA}}\r\n课程名称：{{keyword1.DATA}}\r\n开始时间：{{keyword2.DATA}}\r\n{{remark.DATA}}', 'MCfo_7rP8vFGssGE78JdgXycghSwCqmeFmFnYYIbPb8', '1585197241', '1');
-- --------------------------------------------------------

--
-- 表的结构 `eb_wechat_user`
--

CREATE TABLE `eb_wechat_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL COMMENT '微信用户id',
  `unionid` varchar(30) DEFAULT NULL COMMENT '只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段',
  `openid` varchar(30) DEFAULT NULL COMMENT '用户的标识，对当前公众号唯一',
  `routine_openid` varchar(32) DEFAULT NULL COMMENT '小程序唯一身份ID',
  `nickname` varchar(64) NOT NULL COMMENT '用户的昵称',
  `headimgurl` varchar(256) NOT NULL COMMENT '用户头像',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户的性别，值为1时是男性，值为2时是女性，值为0时是未知',
  `city` varchar(64) NOT NULL COMMENT '用户所在城市',
  `language` varchar(64) NOT NULL COMMENT '用户的语言，简体中文为zh_CN',
  `province` varchar(64) NOT NULL COMMENT '用户所在省份',
  `country` varchar(64) NOT NULL COMMENT '用户所在国家',
  `remark` varchar(256) DEFAULT NULL COMMENT '公众号运营者对粉丝的备注，公众号运营者可在微信公众平台用户管理界面对粉丝添加备注',
  `groupid` smallint(5) UNSIGNED DEFAULT '0' COMMENT '用户所在的分组ID（兼容旧的用户分组接口）',
  `tagid_list` varchar(256) DEFAULT NULL COMMENT '用户被打上的标签ID列表',
  `subscribe` tinyint(3) UNSIGNED DEFAULT '1' COMMENT '用户是否订阅该公众号标识',
  `subscribe_time` int(10) UNSIGNED DEFAULT NULL COMMENT '关注公众号时间',
  `add_time` int(10) UNSIGNED DEFAULT NULL COMMENT '添加时间',
  `stair` int(11) UNSIGNED DEFAULT NULL COMMENT '一级推荐人',
  `second` int(11) UNSIGNED DEFAULT NULL COMMENT '二级推荐人',
  `order_stair` int(11) DEFAULT NULL COMMENT '一级推荐人订单',
  `order_second` int(11) UNSIGNED DEFAULT NULL COMMENT '二级推荐人订单',
  `now_money` int(11) UNSIGNED DEFAULT NULL COMMENT '佣金',
  `session_key` varchar(32) DEFAULT NULL COMMENT '小程序用户会话密匙',
  `user_type` varchar(32) DEFAULT 'wechat' COMMENT '用户类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信用户表';
-- --------------------------------------------------------

--
-- 表的结构 `eb_member_ship`
--
CREATE TABLE `eb_member_ship` (
  `id` int(11) NOT NULL,
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
  `add_time` int(50) NOT NULL COMMENT '添加时间',
   PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员管理表';
-- --------------------------------------------------------

--
-- 表数据 `eb_member_ship`
--
INSERT INTO `eb_member_ship` (`id`, `type`, `title`, `vip_day`, `original_price`, `price`, `is_permanent`, `is_publish`, `is_free`, `sort`, `is_del`, `add_time`) VALUES
 (1, '1', '月卡', '30', '30.00', '20.00', '0', '1', '0', '4', '0', '1588129765'),
 (2, '1', '季卡', '90', '90.00', '80.00', '0', '1', '0', '3', '0', '1588129794'),
 (3, '1', '年卡', '365', '360.00', '300.00', '0', '1', '0', '2', '0', '1588129818'),
 (4, '1', '永久', '-1', '1200.00', '1000.00', '1', '1', '0', '1', '0', '1588129856'),
 (5, '1', '免费', '7', '0.00', '0.00', '0', '1', '1', '0', '0', '1588130680');



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
  `validity` varchar(10) DEFAULT '0' COMMENT '有效期',
  `add_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员购买记录表';

-- --------------------------------------------------------

--
-- 表的结构 `eb_live_reward`
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

--
-- 表的结构 `eb_live_goods`
--

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

--
-- 表的结构 `eb_event_registration`
--
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
--
-- 表的结构 `eb_event_sign_up`
--
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

--
-- 表的结构 `eb_user_sign`
--
CREATE TABLE `eb_user_sign` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '签到说明',
  `number` int(11) NOT NULL DEFAULT '0' COMMENT '获得金币',
  `balance` int(11) NOT NULL DEFAULT '0' COMMENT '剩余金币',
  `add_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到记录表';
--
-- 表的结构 `eb_sign_poster`
--
CREATE TABLE `eb_sign_poster` (
  `id` int(10) NOT NULL,
  `sign_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '签到时间',
  `poster` varchar(255) DEFAULT NULL COMMENT '分享海报',
  `sort` int(5) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `add_time` int(50) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到海报';
--
-- 表的结构 `eb_search_history`
--
CREATE TABLE `eb_search_history` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL DEFAULT '0',
  `search` varchar(255) NOT NULL COMMENT '搜索内容',
  `add_time` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='历史搜索';
--
-- 表的结构 `eb_special_watch`
--
CREATE TABLE `eb_special_watch` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL COMMENT 'uid',
  `special_id` int(11) NOT NULL COMMENT '专题ID',
  `task_id` int(11) NOT NULL COMMENT '素材ID',
  `viewing_time` int(50) DEFAULT '0' COMMENT '观看时间',
  `add_time` int(50) DEFAULT '0' COMMENT '时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户观看素材时间';
--
-- 转储表的索引
--

--
-- 表的索引 `eb_article`
--
ALTER TABLE `eb_article`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_article_category`
--
ALTER TABLE `eb_article_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_article_content`
--
ALTER TABLE `eb_article_content`
  ADD UNIQUE KEY `nid` (`nid`) USING BTREE;

--
-- 表的索引 `eb_cache`
--
ALTER TABLE `eb_cache`
  ADD PRIMARY KEY (`key`);

--
-- 表的索引 `eb_express`
--
ALTER TABLE `eb_express`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE;

--
-- 表的索引 `eb_grade`
--
ALTER TABLE `eb_grade`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_live_barrage`
--
ALTER TABLE `eb_live_barrage`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `live_id` (`live_id`) USING BTREE,
  ADD KEY `to_uid` (`to_uid`) USING BTREE;

--
-- 表的索引 `eb_live_honoured_guest`
--
ALTER TABLE `eb_live_honoured_guest`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `live_id` (`live_id`);

--
-- 表的索引 `eb_live_studio`
--
ALTER TABLE `eb_live_studio`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `special_id` (`special_id`),
  ADD KEY `stream_name` (`stream_name`);

--
-- 表的索引 `eb_live_user`
--
ALTER TABLE `eb_live_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `live_id` (`live_id`);

--
-- 表的索引 `eb_phone_user`
--
ALTER TABLE `eb_phone_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_recommend`
--
ALTER TABLE `eb_recommend`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_recommend_banner`
--
ALTER TABLE `eb_recommend_banner`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_recommend_relation`
--
ALTER TABLE `eb_recommend_relation`
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- 表的索引 `eb_routine_access_token`
--
ALTER TABLE `eb_routine_access_token`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_routine_form_id`
--
ALTER TABLE `eb_routine_form_id`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_routine_template`
--
ALTER TABLE `eb_routine_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tempkey` (`tempkey`) USING BTREE;

--
-- 表的索引 `eb_search`
--
ALTER TABLE `eb_search`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_sms_code`
--
ALTER TABLE `eb_sms_code`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `id_2` (`id`);

--
-- 表的索引 `eb_special`
--
ALTER TABLE `eb_special`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_barrage`
--
ALTER TABLE `eb_special_barrage`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_buy`
--
ALTER TABLE `eb_special_buy`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_content`
--
ALTER TABLE `eb_special_content`
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_special_course`
--
ALTER TABLE `eb_special_course`
  ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_special_source`
--
ALTER TABLE `eb_special_source`
  ADD PRIMARY KEY (`id`),
  ADD KEY `special_ids` (`special_id`) USING BTREE,
  ADD KEY `source_ids` (`source_id`) USING BTREE;

--
-- 表的索引 `eb_special_record`
--
ALTER TABLE `eb_special_record`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_relation`
--
ALTER TABLE `eb_special_relation`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_subject`
--
ALTER TABLE `eb_special_subject`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_special_task`
--
ALTER TABLE `eb_special_task`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_store_bargain`
--
ALTER TABLE `eb_store_bargain`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_bargain_user`
--
ALTER TABLE `eb_store_bargain_user`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_bargain_user_help`
--
ALTER TABLE `eb_store_bargain_user_help`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_cart`
--
ALTER TABLE `eb_store_cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`uid`) USING BTREE,
  ADD KEY `goods_id` (`product_id`) USING BTREE,
  ADD KEY `uid` (`uid`,`is_pay`) USING BTREE,
  ADD KEY `uid_2` (`uid`,`is_del`) USING BTREE,
  ADD KEY `uid_3` (`uid`,`is_new`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;

--
-- 表的索引 `eb_store_category`
--
ALTER TABLE `eb_store_category`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `is_base` (`is_show`) USING BTREE,
  ADD KEY `sort` (`sort`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `eb_store_combination`
--
ALTER TABLE `eb_store_combination`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_combination_attr`
--
ALTER TABLE `eb_store_combination_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_combination_attr_result`
--
ALTER TABLE `eb_store_combination_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_combination_attr_value`
--
ALTER TABLE `eb_store_combination_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_coupon`
--
ALTER TABLE `eb_store_coupon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `state` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `coupon_time` (`coupon_time`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_issue`
--
ALTER TABLE `eb_store_coupon_issue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`) USING BTREE,
  ADD KEY `start_time` (`start_time`,`end_time`) USING BTREE,
  ADD KEY `remain_count` (`remain_count`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_issue_user`
--
ALTER TABLE `eb_store_coupon_issue_user`
  ADD UNIQUE KEY `uid` (`uid`,`issue_coupon_id`) USING BTREE;

--
-- 表的索引 `eb_store_coupon_user`
--
ALTER TABLE `eb_store_coupon_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cid` (`cid`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `end_time` (`end_time`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_fail` (`is_fail`) USING BTREE;

--
-- 表的索引 `eb_store_order`
--
ALTER TABLE `eb_store_order`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id_2` (`order_id`,`uid`) USING BTREE,
  ADD UNIQUE KEY `unique` (`unique`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `pay_price` (`pay_price`) USING BTREE,
  ADD KEY `paid` (`paid`) USING BTREE,
  ADD KEY `pay_time` (`pay_time`) USING BTREE,
  ADD KEY `pay_type` (`pay_type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `coupon_id` (`coupon_id`) USING BTREE;

--
-- 表的索引 `eb_store_order_cart_info`
--
ALTER TABLE `eb_store_order_cart_info`
  ADD UNIQUE KEY `oid` (`oid`,`unique`) USING BTREE,
  ADD KEY `cart_id` (`cart_id`) USING BTREE,
  ADD KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_order_status`
--
ALTER TABLE `eb_store_order_status`
  ADD KEY `oid` (`oid`) USING BTREE,
  ADD KEY `change_type` (`change_type`) USING BTREE;

--
-- 表的索引 `eb_store_pink`
--
ALTER TABLE `eb_store_pink`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_pink_false`
--
ALTER TABLE `eb_store_pink_false`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_product`
--
ALTER TABLE `eb_store_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cate_id` (`cate_id`) USING BTREE,
  ADD KEY `is_hot` (`is_hot`) USING BTREE,
  ADD KEY `is_benefit` (`is_benefit`) USING BTREE,
  ADD KEY `is_best` (`is_best`) USING BTREE,
  ADD KEY `is_new` (`is_new`) USING BTREE,
  ADD KEY `toggle_on_sale, is_del` (`is_del`) USING BTREE,
  ADD KEY `price` (`price`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE,
  ADD KEY `sort` (`sort`) USING BTREE,
  ADD KEY `sales` (`sales`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `is_postage` (`is_postage`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr`
--
ALTER TABLE `eb_store_product_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr_result`
--
ALTER TABLE `eb_store_product_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_product_attr_value`
--
ALTER TABLE `eb_store_product_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_product_relation`
--
ALTER TABLE `eb_store_product_relation`
  ADD UNIQUE KEY `uid` (`uid`,`product_id`,`type`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `category` (`category`) USING BTREE;

--
-- 表的索引 `eb_store_product_reply`
--
ALTER TABLE `eb_store_product_reply`
  ADD PRIMARY KEY (`id`) USING BTREE,
  ADD UNIQUE KEY `order_id_2` (`oid`,`unique`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `parent_id` (`reply_type`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `product_score` (`product_score`) USING BTREE,
  ADD KEY `service_score` (`service_score`) USING BTREE;

--
-- 表的索引 `eb_store_seckill`
--
ALTER TABLE `eb_store_seckill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `start_time` (`start_time`,`stop_time`),
  ADD KEY `is_del` (`is_del`),
  ADD KEY `is_hot` (`is_hot`),
  ADD KEY `is_show` (`status`),
  ADD KEY `add_time` (`add_time`),
  ADD KEY `sort` (`sort`),
  ADD KEY `is_postage` (`is_postage`);

--
-- 表的索引 `eb_store_seckill_attr`
--
ALTER TABLE `eb_store_seckill_attr`
  ADD KEY `store_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_seckill_attr_result`
--
ALTER TABLE `eb_store_seckill_attr_result`
  ADD UNIQUE KEY `product_id` (`product_id`) USING BTREE;

--
-- 表的索引 `eb_store_seckill_attr_value`
--
ALTER TABLE `eb_store_seckill_attr_value`
  ADD UNIQUE KEY `unique` (`unique`,`suk`) USING BTREE,
  ADD KEY `store_id` (`product_id`,`suk`) USING BTREE;

--
-- 表的索引 `eb_store_service`
--
ALTER TABLE `eb_store_service`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_service_log`
--
ALTER TABLE `eb_store_service_log`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_store_visit`
--
ALTER TABLE `eb_store_visit`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_admin`
--
ALTER TABLE `eb_system_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `account` (`account`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_system_attachment`
--
ALTER TABLE `eb_system_attachment`
  ADD PRIMARY KEY (`att_id`);

--
-- 表的索引 `eb_system_attachment_category`
--
ALTER TABLE `eb_system_attachment_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- 表的索引 `eb_system_config`
--
ALTER TABLE `eb_system_config`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_config_content`
--
ALTER TABLE `eb_system_config_content`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_config_tab`
--
ALTER TABLE `eb_system_config_tab`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_file`
--
ALTER TABLE `eb_system_file`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_group`
--
ALTER TABLE `eb_system_group`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `config_name` (`config_name`) USING BTREE;

--
-- 表的索引 `eb_system_group_data`
--
ALTER TABLE `eb_system_group_data`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_system_log`
--
ALTER TABLE `eb_system_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE;

--
-- 表的索引 `eb_system_menus`
--
ALTER TABLE `eb_system_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pid` (`pid`) USING BTREE,
  ADD KEY `is_show` (`is_show`) USING BTREE,
  ADD KEY `access` (`access`) USING BTREE;

--
-- 表的索引 `eb_system_notice`
--
ALTER TABLE `eb_system_notice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_system_notice_admin`
--
ALTER TABLE `eb_system_notice_admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`,`notice_type`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `is_visit` (`is_visit`) USING BTREE,
  ADD KEY `is_click` (`is_click`) USING BTREE;

--
-- 表的索引 `eb_system_role`
--
ALTER TABLE `eb_system_role`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status` (`status`) USING BTREE,
  ADD UNIQUE KEY `sign` (`sign`) USING BTREE;

--
-- 表的索引 `eb_user`
--
ALTER TABLE `eb_user`
  ADD PRIMARY KEY (`uid`),
  ADD KEY `account` (`account`) USING BTREE,
  ADD KEY `spreaduid` (`spread_uid`) USING BTREE,
  ADD KEY `level` (`level`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `is_promoter` (`is_promoter`) USING BTREE;

--
-- 表的索引 `eb_user_address`
--
ALTER TABLE `eb_user_address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `is_default` (`is_default`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE;

--
-- 表的索引 `eb_user_bill`
--
ALTER TABLE `eb_user_bill`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`uid`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `pm` (`pm`) USING BTREE,
  ADD KEY `type` (`category`,`type`,`link_id`) USING BTREE;

--
-- 表的索引 `eb_user_enter`
--
ALTER TABLE `eb_user_enter`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uid` (`uid`) USING BTREE,
  ADD KEY `province` (`province`,`city`,`district`) USING BTREE,
  ADD KEY `is_lock` (`is_lock`) USING BTREE,
  ADD KEY `is_del` (`is_del`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE;

--
-- 表的索引 `eb_user_extract`
--
ALTER TABLE `eb_user_extract`
  ADD PRIMARY KEY (`id`),
  ADD KEY `extract_type` (`extract_type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `openid` (`uid`) USING BTREE,
  ADD KEY `fail_time` (`fail_time`);

--
-- 表的索引 `eb_user_group`
--
ALTER TABLE `eb_user_group`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_notice`
--
ALTER TABLE `eb_user_notice`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_notice_see`
--
ALTER TABLE `eb_user_notice_see`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_user_recharge`
--
ALTER TABLE `eb_user_recharge`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_id` (`order_id`) USING BTREE,
  ADD KEY `uid` (`uid`) USING BTREE,
  ADD KEY `recharge_type` (`recharge_type`) USING BTREE,
  ADD KEY `paid` (`paid`) USING BTREE;

--
-- 表的索引 `eb_wechat_media`
--
ALTER TABLE `eb_wechat_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type` (`type`,`media_id`) USING BTREE,
  ADD KEY `type_2` (`type`) USING BTREE;

--
-- 表的索引 `eb_wechat_message`
--
ALTER TABLE `eb_wechat_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `openid` (`openid`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE;

--
-- 表的索引 `eb_wechat_news_category`
--
ALTER TABLE `eb_wechat_news_category`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_wechat_qrcode`
--
ALTER TABLE `eb_wechat_qrcode`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `third_type` (`third_type`,`third_id`) USING BTREE,
  ADD KEY `ticket` (`ticket`) USING BTREE;

--
-- 表的索引 `eb_wechat_reply`
--
ALTER TABLE `eb_wechat_reply`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`) USING BTREE,
  ADD KEY `type` (`type`) USING BTREE,
  ADD KEY `status` (`status`) USING BTREE,
  ADD KEY `hide` (`hide`) USING BTREE;

--
-- 表的索引 `eb_wechat_template`
--
ALTER TABLE `eb_wechat_template`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tempkey` (`tempkey`) USING BTREE;

--
-- 表的索引 `eb_wechat_user`
--
ALTER TABLE `eb_wechat_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `openid` (`openid`) USING BTREE,
  ADD KEY `groupid` (`groupid`) USING BTREE,
  ADD KEY `subscribe_time` (`subscribe_time`) USING BTREE,
  ADD KEY `add_time` (`add_time`) USING BTREE,
  ADD KEY `subscribe` (`subscribe`) USING BTREE,
  ADD KEY `unionid` (`unionid`) USING BTREE,
  ADD KEY `uid` (`uid`);

--
-- 表的索引 `eb_member_card_batch`
--
ALTER TABLE `eb_member_card_batch`
 ADD PRIMARY KEY (`id`);

--
-- 表的索引 `eb_member_card`
--
ALTER TABLE `eb_member_card`
 ADD PRIMARY KEY (`id`),
 ADD KEY `card_batch_id` (`card_batch_id`) USING BTREE;
--
-- 表的索引 `eb_member_ship`
--
/*ALTER TABLE `eb_member_ship`
 ADD PRIMARY KEY (`id`);*/

--
-- 表的索引 `eb_member_record`
--
ALTER TABLE `eb_member_record`
 ADD PRIMARY KEY (`id`);
 --
-- 表的索引 `eb_live_reward`
--
ALTER TABLE `eb_live_reward`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE;
--
-- 表的索引 `eb_live_goods`
--
ALTER TABLE `eb_live_goods`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_event_registration`
--
ALTER TABLE `eb_event_registration`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_event_sign_up`
--
ALTER TABLE `eb_event_sign_up`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_user_sign`
--
ALTER TABLE `eb_user_sign`
 ADD PRIMARY KEY (`id`),
 ADD KEY `uid` (`uid`) USING BTREE;
--
-- 表的索引 `eb_sign_poster`
--
ALTER TABLE `eb_sign_poster`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_search_history`
--
ALTER TABLE `eb_search_history`
 ADD PRIMARY KEY (`id`);
--
-- 表的索引 `eb_special_watch`
--
ALTER TABLE `eb_special_watch`
 ADD PRIMARY KEY (`id`);
--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `eb_article`
--
ALTER TABLE `eb_article`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章管理ID', AUTO_INCREMENT=15;

--
-- 使用表AUTO_INCREMENT `eb_article_category`
--
ALTER TABLE `eb_article_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文章分类id', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `eb_express`
--
ALTER TABLE `eb_express`
  MODIFY `id` mediumint(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '快递公司id', AUTO_INCREMENT=426;

--
-- 使用表AUTO_INCREMENT `eb_grade`
--
ALTER TABLE `eb_grade`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `eb_live_barrage`
--
ALTER TABLE `eb_live_barrage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- 使用表AUTO_INCREMENT `eb_live_honoured_guest`
--
ALTER TABLE `eb_live_honoured_guest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- 使用表AUTO_INCREMENT `eb_live_studio`
--
ALTER TABLE `eb_live_studio`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- 使用表AUTO_INCREMENT `eb_live_user`
--
ALTER TABLE `eb_live_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=167;

--
-- 使用表AUTO_INCREMENT `eb_phone_user`
--
ALTER TABLE `eb_phone_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用表AUTO_INCREMENT `eb_recommend`
--
ALTER TABLE `eb_recommend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `eb_recommend_banner`
--
ALTER TABLE `eb_recommend_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `eb_recommend_relation`
--
ALTER TABLE `eb_recommend_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- 使用表AUTO_INCREMENT `eb_routine_access_token`
--
ALTER TABLE `eb_routine_access_token`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '小程序access_token表ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `eb_routine_form_id`
--
ALTER TABLE `eb_routine_form_id`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '表单ID表ID';

--
-- 使用表AUTO_INCREMENT `eb_routine_template`
--
ALTER TABLE `eb_routine_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '模板id', AUTO_INCREMENT=20;

--
-- 使用表AUTO_INCREMENT `eb_search`
--
ALTER TABLE `eb_search`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- 使用表AUTO_INCREMENT `eb_sms_code`
--
ALTER TABLE `eb_sms_code`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT COMMENT 'id', AUTO_INCREMENT=256;

--
-- 使用表AUTO_INCREMENT `eb_special`
--
ALTER TABLE `eb_special`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT/*, AUTO_INCREMENT=56*/;
--
-- 使用表AUTO_INCREMENT `eb_special_source`
--
ALTER TABLE `eb_special_source`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- 使用表AUTO_INCREMENT `eb_special_barrage`
--
ALTER TABLE `eb_special_barrage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- 使用表AUTO_INCREMENT `eb_special_buy`
--
ALTER TABLE `eb_special_buy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- 使用表AUTO_INCREMENT `eb_special_content`
--
ALTER TABLE `eb_special_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_special_course`
--
ALTER TABLE `eb_special_course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `eb_special_record`
--
ALTER TABLE `eb_special_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1670;

--
-- 使用表AUTO_INCREMENT `eb_special_relation`
--
ALTER TABLE `eb_special_relation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- 使用表AUTO_INCREMENT `eb_special_subject`
--
ALTER TABLE `eb_special_subject`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- 使用表AUTO_INCREMENT `eb_special_task`
--
ALTER TABLE `eb_special_task`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT/*, AUTO_INCREMENT=22*/;

--
-- 使用表AUTO_INCREMENT `eb_store_bargain`
--
ALTER TABLE `eb_store_bargain`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '砍价产品ID', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `eb_store_bargain_user`
--
ALTER TABLE `eb_store_bargain_user`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户参与砍价表ID', AUTO_INCREMENT=34;

--
-- 使用表AUTO_INCREMENT `eb_store_bargain_user_help`
--
ALTER TABLE `eb_store_bargain_user_help`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '砍价用户帮助表ID', AUTO_INCREMENT=46;

--
-- 使用表AUTO_INCREMENT `eb_store_cart`
--
ALTER TABLE `eb_store_cart`
  MODIFY `id` bigint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '购物车表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_category`
--
ALTER TABLE `eb_store_category`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT COMMENT '商品分类表ID', AUTO_INCREMENT=71;

--
-- 使用表AUTO_INCREMENT `eb_store_combination`
--
ALTER TABLE `eb_store_combination`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_coupon`
--
ALTER TABLE `eb_store_coupon`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '优惠券表ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `eb_store_coupon_issue`
--
ALTER TABLE `eb_store_coupon_issue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_coupon_user`
--
ALTER TABLE `eb_store_coupon_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '优惠券发放记录id', AUTO_INCREMENT=6;

--
-- 使用表AUTO_INCREMENT `eb_store_order`
--
ALTER TABLE `eb_store_order`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '订单ID';

--
-- 使用表AUTO_INCREMENT `eb_store_pink`
--
ALTER TABLE `eb_store_pink`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_pink_false`
--
ALTER TABLE `eb_store_pink_false`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_store_product`
--
ALTER TABLE `eb_store_product`
  MODIFY `id` mediumint(11) NOT NULL AUTO_INCREMENT COMMENT '商品id', AUTO_INCREMENT=170;

--
-- 使用表AUTO_INCREMENT `eb_store_product_reply`
--
ALTER TABLE `eb_store_product_reply`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '评论ID', AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `eb_store_seckill`
--
ALTER TABLE `eb_store_seckill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商品秒杀产品表id';

--
-- 使用表AUTO_INCREMENT `eb_store_service`
--
ALTER TABLE `eb_store_service`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客服id';

--
-- 使用表AUTO_INCREMENT `eb_store_service_log`
--
ALTER TABLE `eb_store_service_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客服用户对话记录表ID';

--
-- 使用表AUTO_INCREMENT `eb_store_visit`
--
ALTER TABLE `eb_store_visit`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=971;

--
-- 使用表AUTO_INCREMENT `eb_system_admin`
--
ALTER TABLE `eb_system_admin`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '后台管理员表ID', AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `eb_system_attachment`
--
ALTER TABLE `eb_system_attachment`
  MODIFY `att_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- 使用表AUTO_INCREMENT `eb_system_attachment_category`
--
ALTER TABLE `eb_system_attachment_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- 使用表AUTO_INCREMENT `eb_system_config`
--
ALTER TABLE `eb_system_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置id', AUTO_INCREMENT=176;

--
-- 使用表AUTO_INCREMENT `eb_system_config_content`
--
ALTER TABLE `eb_system_config_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- 使用表AUTO_INCREMENT `eb_system_config_tab`
--
ALTER TABLE `eb_system_config_tab`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '配置分类id', AUTO_INCREMENT=22;

--
-- 使用表AUTO_INCREMENT `eb_system_file`
--
ALTER TABLE `eb_system_file`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文件对比ID', AUTO_INCREMENT=2696;

--
-- 使用表AUTO_INCREMENT `eb_system_group`
--
ALTER TABLE `eb_system_group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '组合数据ID', AUTO_INCREMENT=57;

--
-- 使用表AUTO_INCREMENT `eb_system_group_data`
--
ALTER TABLE `eb_system_group_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '组合数据详情ID', AUTO_INCREMENT=161;

--
-- 使用表AUTO_INCREMENT `eb_system_log`
--
ALTER TABLE `eb_system_log`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员操作记录ID';

--
-- 使用表AUTO_INCREMENT `eb_system_menus`
--
ALTER TABLE `eb_system_menus`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '菜单ID', AUTO_INCREMENT=482;

--
-- 使用表AUTO_INCREMENT `eb_system_notice`
--
ALTER TABLE `eb_system_notice`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知模板id', AUTO_INCREMENT=10;

--
-- 使用表AUTO_INCREMENT `eb_system_notice_admin`
--
ALTER TABLE `eb_system_notice_admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '通知记录ID', AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `eb_system_role`
--
ALTER TABLE `eb_system_role`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '身份管理id', AUTO_INCREMENT=24;

--
-- 使用表AUTO_INCREMENT `eb_user`
--
ALTER TABLE `eb_user`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id';

--
-- 使用表AUTO_INCREMENT `eb_user_address`
--
ALTER TABLE `eb_user_address`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户地址id', AUTO_INCREMENT=37;

--
-- 使用表AUTO_INCREMENT `eb_user_bill`
--
ALTER TABLE `eb_user_bill`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户账单id';

--
-- 使用表AUTO_INCREMENT `eb_user_enter`
--
ALTER TABLE `eb_user_enter`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '商户申请ID';

--
-- 使用表AUTO_INCREMENT `eb_user_extract`
--
ALTER TABLE `eb_user_extract`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_group`
--
ALTER TABLE `eb_user_group`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_notice`
--
ALTER TABLE `eb_user_notice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- 使用表AUTO_INCREMENT `eb_user_notice_see`
--
ALTER TABLE `eb_user_notice_see`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_user_recharge`
--
ALTER TABLE `eb_user_recharge`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_wechat_media`
--
ALTER TABLE `eb_wechat_media`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信视频音频id', AUTO_INCREMENT=13;

--
-- 使用表AUTO_INCREMENT `eb_wechat_message`
--
ALTER TABLE `eb_wechat_message`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户行为记录id';

--
-- 使用表AUTO_INCREMENT `eb_wechat_news_category`
--
ALTER TABLE `eb_wechat_news_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '图文消息管理ID';

--
-- 使用表AUTO_INCREMENT `eb_wechat_qrcode`
--
ALTER TABLE `eb_wechat_qrcode`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信二维码ID';

--
-- 使用表AUTO_INCREMENT `eb_wechat_reply`
--
ALTER TABLE `eb_wechat_reply`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '微信关键字回复id';

--
-- 使用表AUTO_INCREMENT `eb_wechat_template`
--
ALTER TABLE `eb_wechat_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '模板id', AUTO_INCREMENT=18;

--
-- 使用表AUTO_INCREMENT `eb_member_card_batch`
--
ALTER TABLE `eb_member_card_batch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_card`
--
ALTER TABLE `eb_member_card`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_ship`
--
ALTER TABLE `eb_member_ship`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `eb_member_record`
--
ALTER TABLE `eb_member_record`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_live_reward`
--
ALTER TABLE `eb_live_reward`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  --
-- 使用表AUTO_INCREMENT `eb_live_goods`
--
ALTER TABLE `eb_live_goods`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_event_registration`
--
ALTER TABLE `eb_event_registration`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_event_sign_up`
--
ALTER TABLE `eb_event_sign_up`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_user_sign`
--
ALTER TABLE `eb_user_sign`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_sign_poster`
--
ALTER TABLE `eb_sign_poster`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_wechat_user`
--
ALTER TABLE `eb_wechat_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_search_history`
--
ALTER TABLE `eb_search_history`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- 使用表AUTO_INCREMENT `eb_special_watch`
--
ALTER TABLE `eb_special_watch`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

