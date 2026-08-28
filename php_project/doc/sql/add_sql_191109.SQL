
ALTER TABLE `staff_department`
ADD COLUMN `phone` varchar(20) NOT NULL DEFAULT '' COMMENT '部门电话' AFTER `name`;


ALTER TABLE `flo_apply`
ADD COLUMN `able_revocation` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否能撤销 0-不能 1-能' AFTER `is_supplement`;

CREATE TABLE `user_collection` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `obj_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '目标ID',
  `obj_enc_id` varchar(30) NOT NULL DEFAULT '' COMMENT '加密id',
  `obj_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '来源:1：宏观；2：扶持政策；3：实施细则；4：申报通知；5：扶持条款；6：细则条款；7：公示；9: 申报项目 16-中介机构',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收藏表';

CREATE TABLE `flo_apply_correct` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申报id',
  `approval_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审批id',
  `department_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '发起部门id',
  `mark` varchar(500) NOT NULL DEFAULT '' COMMENT '订正原因',
  `invalid_mark` varchar(500) NOT NULL DEFAULT '' COMMENT '无效原因',
  `origin_content` text COMMENT '订正时的详情',
  `changes` text COMMENT '变更内容',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1-待批准 2-不批准 3-待订正 4-待审核 5-订正无效 6-重新订正（这里需要再次生成新的记录） 7-订正完成 8-订正作废',
  `source_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '源修正id',
  `has_material` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否有补充资料',
  `audit_department_id` tinyint(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核部门id',
  `audit_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核人',
  `audit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审核时间',
  `submit_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修正时间',
  `is_check` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0-待检查 1-已经检查',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='修正资料记录';

CREATE TABLE `flo_apply_correct_content` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申报id',
  `correct_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订正id',
  `setup` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '对应 申报提交的 12345个步骤',
  `content` text COMMENT '修改的内容',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='修正资料详情';

CREATE TABLE `flo_apply_correct_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '申报id',
  `correct_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订正id',
  `file_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应的文件id',
  `correct_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '订正类型 1-新增 2-删除',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '附件名称',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件url',
  `file_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '附件类型  0-补充材料 1-其他材料 2-发票 3-身份证 4-营业执照',
  `invoice_number` varchar(30) NOT NULL DEFAULT '' COMMENT '发票号码',
  `invoice_money` varchar(20) NOT NULL DEFAULT '' COMMENT '发票金额',
  `invoice_billing_date` varchar(20) NOT NULL DEFAULT '' COMMENT '开票日期',
  `invoice_checkcode` varchar(30) NOT NULL DEFAULT '' COMMENT '校验码',
  `invoice_code` varchar(30) NOT NULL DEFAULT '' COMMENT '发票代码',
  `check_status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '发票检查状态1待检查2异常3正常',
  `project_materials_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '材料清单类型ID',
  `content` text COMMENT '附件内容',
  `created_at` int(1) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='修正资料附件信息';

ALTER TABLE `flo_approval_material` 
ADD COLUMN `material` text NOT NULL COMMENT '提交的资料' AFTER `status`,
ADD COLUMN `submit_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提交时间' AFTER `material`;

ALTER TABLE `flo_approval_material`
MODIFY COLUMN `material` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '提交的资料' AFTER `status`;

ALTER TABLE `flo_approval_material`
MODIFY COLUMN `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '接受者ID' AFTER `enterprise_id`;

ALTER TABLE `flo_approval_material`
ADD COLUMN `created_staff_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人' AFTER `submit_time`;

CREATE TABLE `flo_approval_coordinate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0',
  `approval_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_staff_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建人',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='选择协同部门的记录';

CREATE TABLE `flo_approval_coordinate_relation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '选择协同记录的id',
  `approval_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '对应协同部门的id',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='选择协同部门记录 和 协同部门审批的 关系';

CREATE TABLE `flo_apply_pdf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `apply_id` int(10) unsigned NOT NULL DEFAULT '0',
  `business_id` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT 'url',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='申报pdf';

ALTER TABLE `flo_apply_correct`
MODIFY COLUMN `is_check` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT ' 0-不需要订正 1-待检查 2-检查完毕' AFTER `submit_time`;

ALTER TABLE `flo_approval`
MODIFY COLUMN `remark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注' AFTER `deleted_at`;

ALTER TABLE `flo_apply_correct`
ADD COLUMN `agree_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '批准时间' AFTER `audit_department_id`;

INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (38, 6, '资料订正', 'data_correct', '1000038', '资料订正', 1574249023, 1574249023);
INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (39, 6, '资料补充', 'data_supplement', '1000039', '资料补充', 1574249023, 1574249023);

INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (26, '补充资料记录', '', '', 0, 1574753830, 1574753830);
INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (27, '申报资料订正记录', '', '', 0, 1574753830, 1574753830);


INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (220, 26, '补充资料列表', '', '1000208', '补充资料列表', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (221, 26, '补充资料详情', '', '1000209', '补充资料详情', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (222, 27, '资料订正申请', '', '1000210', '资料订正申请', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (223, 27, '资料订正列表', '', '1000211', '资料订正列表', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (224, 27, '资料订正详情', '', '1000212', '资料订正详情', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (225, 27, '资料订正批准', '', '1000213', '资料订正批准', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (226, 27, '资料订不批准', '', '1000214', '资料订不批准', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (227, 27, '资料订正通过', '', '1000215', '资料订正通过', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (228, 27, '资料订正重新订正', '', '1000216', '资料订正重新订正', 0, 1574754922, 1574754922);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (229, 27, '资料订正作废', '', '1000217', '资料订正作废', 0, 1574754922, 1574754922);

INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (230, 10, '选择协同部门日志', '', '1000218', '选择协同部门日志', 0, 1574755656, 1574755656);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (231, 10, '已选协同部门列表', '', '1000219', '已选协同部门列表', 0, 1574755656, 1574755656);

INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (232, 18, '更新营业执照', '', '1000220', '更新营业执照', 0, 1574755656, 1574755656);

