
CREATE TABLE `agent` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) NOT NULL DEFAULT '' COMMENT '加密id',
  `code` int(10) NOT NULL COMMENT '编号',
  `enterprise_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '企业id',
  `type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '服务类型，关联agent_type',
  `service_item` varchar(1000) NOT NULL DEFAULT '' COMMENT '服务事项',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面url',
  `service_detail` text NOT NULL COMMENT '服务详情',
  `province_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `city_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `district_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '区',
  `address` varchar(50) NOT NULL DEFAULT '' COMMENT '地址',
  `contact_name` varchar(20) NOT NULL DEFAULT '' COMMENT '联系人',
  `contact_phone` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否上下架，0：下架；1：上架',
  `remark` varchar(500) NOT NULL DEFAULT '' COMMENT '备注',
  `composite_stars` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '综合评分',
  `department_stars` decimal(10,1) NOT NULL DEFAULT '0.0' COMMENT '部门评分',
  `credit_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '信用行为 0-正常 1-一般失信 2-严重失信',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介';

CREATE TABLE `agent_comment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中介id',
  `stars` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '评价星级  1,2,3,4,5',
  `content` varchar(300) NOT NULL DEFAULT '' COMMENT '评价内容',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否显示评论内容 0-不显示 1-显示',
  `is_calculate` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否计算分值 0-不计算 1-计算',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `user_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '用户类型1网站user2运营staff',
  `department_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门id， 只有use_type 为2 才不为0 ',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介评论';

CREATE TABLE `agent_complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中介id',
  `code` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '编号',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `enterprise_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户企业id',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '处理状态 1-已处理 2-待处理',
  `is_dispose` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户标识 1-staff dispose  员工 0-user 用户',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 1-有效举报 2-无效举报 3-恶意举报',
  `source_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '来源id',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='中介-投诉';

CREATE TABLE `agent_credit` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `agent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '中介id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失信类型 1-一般失信行为-严重失信行为',
  `old_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '历史失信类型 1-一般失信行为-严重失信行为',
  `project_name` varchar(30) NOT NULL DEFAULT '' COMMENT '项目名称',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '原因说明',
  `province_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '省',
  `city_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '市',
  `district_code` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '区',
  `is_audit` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '审核 2-待审核 1-审核通过 3-审核不通过',
  `is_show` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '是否展示 0-不展示 1-展示',
  `department_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '部门id',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='中介--失信行为';

CREATE TABLE `agent_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `agent_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介-附件表';

CREATE TABLE `agent_notify` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) NOT NULL DEFAULT '' COMMENT '加密id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '正文',
  `source_name` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发布，0：未发布；1：已发布',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介--通知公告';

CREATE TABLE `agent_notify_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `agent_notify_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介-通知-附件表';

CREATE TABLE `agent_setup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '正文',
  `source_name` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '类型 1-中介服务简介 2-机构入驻登记方式 3-评价规则 4-监督管理',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发布，0：未发布；1：已发布',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介配置--首页的4个配置';

CREATE TABLE `agent_setup_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `agent_setup_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='中介-配置-附件表';

CREATE TABLE `agent_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '类型名',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='中介类型';

ALTER TABLE `staff_department_bind_department`
ADD COLUMN `department_three_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '指挥部ID' AFTER `department_two_id`,
DROP PRIMARY KEY,
ADD PRIMARY KEY (`department_one_id`, `department_two_id`, `department_three_id`) USING BTREE;

ALTER TABLE `flo_apply`
ADD COLUMN `zip_business_id` varchar(30) NOT NULL DEFAULT '' COMMENT '业务id用于打包文件' AFTER `pdf_url`,
ADD COLUMN `zip_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件打包后的文件路径' AFTER `zip_business_id`;

ALTER TABLE `agent`
ADD COLUMN `enterprise_stars` decimal(10, 1) NOT NULL DEFAULT 0.0 COMMENT '企业评分' AFTER `department_stars`;

INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (1, '创业服务', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (2, '科技创新', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (3, '知识产权', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (4, '市场拓展', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (5, '人力资源', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (6, '投融资服务', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (7, '改制上市', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (8, '管理咨询', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (9, '法律服务', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (10, '商务服务', 1571388891, 1571388891, NULL);
INSERT INTO `agent_type`(`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES (11, '项目建设类', 1571388891, 1571388891, NULL);


INSERT INTO `resource_type`(`id`, `name`, `alias`, `description`, `created_at`, `updated_at`) VALUES (14, '中介服务', 'agent_service', '中介服务', 1571294336, 1571294336);

INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (28, 14, '中介机构管理', 'agent_service_manage', '1000028', '中介机构管理', 1571294336, 1571294336);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (29, 14, '用户评价审核', 'user_evaluation', '1000029', '用户评价审核', 1571294336, 1571294336);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (30, 14, '信用行为审核', 'audit_of_credit', '1000030', '信用行为审核', 1571294336, 1571294336);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (31, 14, '投诉与举报管理', 'complaints_and_reporting', '1000031', '投诉与举报管理', 1571294336, 1571294336);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (32, 14, '通知列表', 'notification_list', '1000032', '通知列表', 1571294336, 1571294336);


INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (23, '中介服务', '', '', 0, 1571282658, 1571282658);

INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (162, 23, '中介服务指南新增', '', '1000150', '中介服务指南新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (163, 23, '中介服务指南详情', '', '1000151', '中介服务指南详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (164, 23, '中介服务指南编辑', '', '1000152', '中介服务指南编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (165, 23, '中介服务通知新增', '', '1000153', '中介服务通知新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (166, 23, '中介服务通知编辑', '', '1000154', '中介服务通知编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (167, 23, '中介服务通知详情', '', '1000155', '中介服务通知详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (168, 23, '中介服务通知删除', '', '1000156', '中介服务通知删除', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (169, 23, '中介服务通知列表', '', '1000157', '中介服务通知列表', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (170, 23, '中介机构新增', '', '1000158', '中介机构新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (171, 23, '中介机构编辑', '', '1000159', '中介机构编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (172, 23, '中介机构详情', '', '1000160', '中介机构详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (173, 23, '中介机构删除', '', '1000161', '中介机构删除', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (174, 23, '中介机构列表', '', '1000162', '中介机构列表', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (175, 23, '中介机构更新发布状态', '', '1000163', '中介机构更新发布状态', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (176, 23, '中介机构清空记录行为', '', '1000164', '中介机构清空记录行为', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (177, 23, '中介机构评论新增', '', '1000165', '中介机构评论新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (178, 23, '中介机构评论编辑', '', '1000166', '中介机构评论编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (179, 23, '中介机构评论详情', '', '1000167', '中介机构评论详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (180, 23, '中介机构评论删除', '', '1000168', '中介机构评论删除', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (181, 23, '中介机构评论列表', '', '1000169', '中介机构评论列表', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (182, 23, '中介机构评论是否展示', '', '1000170', '中介机构评论是否展示', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (183, 23, '中介机构评论是否统计分值', '', '1000171', '中介机构评论是否统计分值', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (184, 23, '中介机构信用行为新增', '', '1000172', '中介机构信用行为新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (185, 23, '中介机构信用行为编辑', '', '1000173', '中介机构信用行为编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (186, 23, '中介机构信用行为详情', '', '1000174', '中介机构信用行为详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (187, 23, '中介机构信用行为删除', '', '1000175', '中介机构信用行为删除', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (188, 23, '中介机构信用行为列表', '', '1000176', '中介机构信用行为列表', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (189, 23, '中介机构信用行为是否展示', '', '1000177', '中介机构信用行为是否展示', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (190, 23, '中介机构信用行为审核', '', '1000178', '中介机构信用行为审核', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (191, 23, '中介机构投诉与举报新增', '', '1000179', '中介机构投诉与举报新增', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (192, 23, '中介机构投诉与举报编辑', '', '1000180', '中介机构投诉与举报编辑', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (193, 23, '中介机构投诉与举报详情', '', '1000181', '中介机构投诉与举报详情', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (194, 23, '中介机构投诉与举报删除', '', '1000182', '中介机构投诉与举报删除', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (195, 23, '中介机构投诉与举报列表', '', '1000183', '中介机构投诉与举报列表', 0, 1571282843, 1571282843);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (196, 23, '中介机构投诉与举报处理', '', '1000184', '中介机构投诉与举报处理', 0, 1571282843, 1571282843);
