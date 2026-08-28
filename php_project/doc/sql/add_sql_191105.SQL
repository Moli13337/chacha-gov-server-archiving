

ALTER TABLE `industry`
ADD COLUMN `is_bank` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '禁用 0-正常 1-禁用' AFTER `type_name`;

CREATE TABLE `steward_opinion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) NOT NULL DEFAULT '' COMMENT '加密id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '正文',
  `link` varchar(50) NOT NULL COMMENT '问卷链接 外链',
  `source_name` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '意见征集类型 1-调查问卷 2-其他',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发布，0：未发布；1：已发布',
  `publish_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布人',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='管家服务--意见征集';

CREATE TABLE `steward_opinion_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `steward_opinion_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家-意见征集-附件表';

CREATE TABLE `steward_push` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `obj_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象id',
  `obj_enc_id` varchar(30) NOT NULL DEFAULT '' COMMENT '所属对象加密id',
  `obj_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '所属对象type 1-项目 2-行业动态 3-会议通知',
  `obj_title` varchar(20) NOT NULL DEFAULT '' COMMENT '标题 冗余的标题',
  `title` varchar(20) NOT NULL DEFAULT '' COMMENT '推送的标题',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '推送类型 1-根据行业推送 2-推送给全部认证企业 3-推送给全部注册用户',
  `first_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业一级',
  `second_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业二级',
  `third_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业三级',
  `fourth_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业四级',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '人数',
  `sms_content` varchar(255) NOT NULL DEFAULT '' COMMENT '短信通知内容',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='管家推送';

CREATE TABLE `steward_push_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `steward_push_id` int(10) NOT NULL DEFAULT '0' COMMENT '推送id',
  `enterprise_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `enterprise_name` varchar(255) NOT NULL DEFAULT '' COMMENT '企业名称',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `content` varchar(255) NOT NULL DEFAULT '' COMMENT '通知内容',
  `is_send` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发送',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家推送名单';

CREATE TABLE `steward_information` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) NOT NULL DEFAULT '' COMMENT '加密id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '正文',
  `source_name` varchar(50) NOT NULL DEFAULT '' COMMENT '来源',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '信息类型 1-行业动态 2-会议通知',
  `link` varchar(50) NOT NULL COMMENT '关联链接',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发布，0：未发布；1：已发布',
  `publish_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布人',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家服务--信息动态';

CREATE TABLE `steward_information_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `steward_info_id` int(10) NOT NULL DEFAULT '0' COMMENT '所属对象ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家-信息动态-附件表';

CREATE TABLE `steward_user` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '员工id',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家服务 --企业管家';

CREATE TABLE `steward_user_opinion` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `steward_opinion_id` int(10) NOT NULL DEFAULT '0' COMMENT '推送id',
  `enterprise_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `enterprise_name` varchar(255) NOT NULL DEFAULT '' COMMENT '企业名称',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '用户意见',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家服务--用户意见列表';

CREATE TABLE `steward_user_opinion_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `steward_user_opinion_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户反馈id',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管家-意见征集-用户反馈-附件表';

CREATE TABLE `user_follow_industry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `first_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业一级',
  `second_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业二级',
  `third_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业三级',
  `fourth_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业四级',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '3' COMMENT '行业属性 1-主行业 2-副行业 3-关注的行业',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户关注的行业';

CREATE TABLE `share_activity` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) NOT NULL DEFAULT '' COMMENT '加密id',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text NOT NULL COMMENT '正文',
  `number` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '活动规模人数',
  `province_code` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区：省',
  `city_code` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区：市',
  `district_code` bigint(11) unsigned NOT NULL DEFAULT '0' COMMENT '地区：区',
  `address` varchar(100) NOT NULL COMMENT '活动地点',
  `sponsor` varchar(50) NOT NULL COMMENT '举办方',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '类型 1-普通活动 2 在线培训 3-企业沙龙 2-论坛展会',
  `validity_sdate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期开始时间',
  `validity_edate` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '有效期结束时间',
  `status` tinyint(1) unsigned DEFAULT '0' COMMENT '活动状态 1-报名中 2-报名已截止 3-已结束',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图名称',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '封面图url',
  `publish_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否发布，0：未发布；1：已发布',
  `publish_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布人',
  `publish_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发布时间',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='共享空间--活动';

CREATE TABLE `share_activity_apply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `activity_id` int(10) NOT NULL DEFAULT '0' COMMENT '活动id',
  `enterprise_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '企业id',
  `enterprise_name` varchar(255) NOT NULL DEFAULT '' COMMENT '企业名称',
  `user_name` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名称',
  `mobile` varchar(20) NOT NULL DEFAULT '' COMMENT '手机号码',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COMMENT='共享活空间-活动报名表';

CREATE TABLE `project_industry` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT '0' COMMENT '项目id',
  `first_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业一级',
  `second_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业二级',
  `third_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业三级',
  `fourth_industry_id` int(10) NOT NULL DEFAULT '0' COMMENT '行业四级',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_project` (`project_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='项目-行业关联表';

ALTER TABLE `steward_push`
MODIFY COLUMN `obj_title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题 冗余的标题' AFTER `obj_type`;

ALTER TABLE `share_activity` 
ADD INDEX `idx_enc`(`enc_id`) USING BTREE;

ALTER TABLE `steward_opinion`
ADD INDEX `idx_enc`(`enc_id`) USING BTREE;

ALTER TABLE `steward_information` 
ADD INDEX `idx_enc`(`enc_id`) USING BTREE;

ALTER TABLE `steward_opinion`
MODIFY COLUMN `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '问卷链接 外链' AFTER `content`;

ALTER TABLE `steward_information`
MODIFY COLUMN `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '关联链接' AFTER `type`;

ALTER TABLE `steward_push`
MODIFY COLUMN `obj_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属对象type 8-项目 14-行业动态 15-会议通知' AFTER `obj_enc_id`;

INSERT INTO `resource_type`(`id`, `name`, `alias`, `description`, `created_at`, `updated_at`) VALUES (15, '管家服务管理', 'steward_service', '管家服务管理', 1573264485, 1573264485);
INSERT INTO `resource_type`(`id`, `name`, `alias`, `description`, `created_at`, `updated_at`) VALUES (16, '共享空间管理', 'share_space_service_management', '共享空间管理', 1573265040, 1573265040);

INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (34, 15, '意见征集管理', 'opinion_management', '1000034', '意见征集管理', 1573265161, 1573265161);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (35, 15, '信息动态列表', 'info_action_list', '1000035', '信息动态列表', 1573265161, 1573265161);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (36, 15, '推送记录', 'push_record', '1000036', '推送记录', 1573265161, 1573265161);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (37, 16, '活动列表', 'activity_list', '1000037', '活动列表', 1573265161, 1573265161);


INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (24, '管家服务管理', '', '', 0, 1573265272, 1573265272);
INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (25, '共享空间管理', '', '', 0, 1573265272, 1573265272);

INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (197, 24, '意见征集新增', '', '1000185', '意见征集新增', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (198, 24, '意见征集编辑', '', '1000186', '意见征集编辑', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (199, 24, '意见征集详情', '', '1000187', '意见征集详情', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (200, 24, '意见征集列表', '', '1000188', '意见征集列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (201, 24, '意见征集删除', '', '1000189', '意见征集删除', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (202, 24, '用户意见列表', '', '1000190', '用户意见列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (203, 24, '用户意见详情', '', '1000191', '用户意见详情', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (204, 24, '信息动态新增', '', '1000192', '信息动态新增', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (205, 24, '信息动态编辑', '', '1000193', '信息动态编辑', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (206, 24, '信息动态详情', '', '1000194', '信息动态详情', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (207, 24, '信息动态列表', '', '1000195', '信息动态列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (208, 24, '信息动态删除', '', '1000196', '信息动态删除', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (209, 24, '推送新增', '', '1000197', '推送新增', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (210, 24, '推送记录列表', '', '1000198', '推送记录列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (211, 24, '推送详情', '', '1000199', '推送详情', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (212, 24, '推送名单', '', '1000200', '推送名单', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (213, 25, '活动新增', '', '1000201', '活动新增', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (214, 25, '活动编辑', '', '1000202', '活动编辑', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (215, 25, '活动详情', '', '1000203', '活动详情', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (216, 25, '活动列表', '', '1000204', '活动列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (217, 25, '活动删除', '', '1000205', '活动删除', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (218, 25, '活动报名列表', '', '1000206', '活动报名列表', 0, 1573269451, 1573269451);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (219, 19, '绑定管家', '', '1000207', '绑定管家', 0, 1573269451, 1573269451);
