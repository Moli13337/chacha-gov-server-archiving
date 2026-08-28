/*
 Navicat Premium Data Transfer

 Source Server         : chachadev
 Source Server Type    : MySQL
 Source Server Version : 50718
 Source Host           : 10.255.159.35:3306
 Source Schema         : wenjiang

 Target Server Type    : MySQL
 Target Server Version : 50718
 File Encoding         : 65001

 Date: 10/07/2019 14:35:13
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for activity_log
-- ----------------------------
DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE `activity_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型 默认0  1-created2-updated 3-deleted',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_id` int(11) NULL DEFAULT NULL,
  `subject_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `subject_type_id` tinyint(1) NOT NULL DEFAULT 0 COMMENT '受影响的类型 1-policy 2-政策解读 3-申报项目',
  `causer_id` int(11) NULL DEFAULT NULL,
  `causer_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '用户名 冗余',
  `causer_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `properties` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  `updated_at` timestamp(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `activity_log_log_name_index`(`log_name`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for attendence_except
-- ----------------------------
DROP TABLE IF EXISTS `attendence_except`;
CREATE TABLE `attendence_except`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '例外id',
  `year` char(4) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '年',
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '例外名',
  `content` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `start_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间',
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '结束时间',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型1工作日放假2周末上班',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '记录工作日放假和周末上班' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for big_data
-- ----------------------------
DROP TABLE IF EXISTS `big_data`;
CREATE TABLE `big_data`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '大数据采集ID',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '编号',
  `name` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `doc_num` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文号',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '正文',
  `file_div` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '附件正文',
  `source` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文章来源',
  `source_web` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源网站',
  `source_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源地址',
  `relation_policy` varchar(3000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '可能相关政策',
  `click_num` int(10) NOT NULL DEFAULT 0 COMMENT '原文点击次数',
  `pub_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '原文发布时间',
  `obj_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属分类，0：未分类，1：宏观；2：扶持政策；3：实施细则；4：申报通知；7：公示 ',
  `is_handle` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否预处理 0：未处理 1：已处理 ',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  `similar_data` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '相似的标题的 id 用逗号隔开',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '大数据采集原始表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for credit_class
-- ----------------------------
DROP TABLE IF EXISTS `credit_class`;
CREATE TABLE `credit_class`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(10) NOT NULL DEFAULT 0 COMMENT '父级id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业征信--信息分类' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for credit_department
-- ----------------------------
DROP TABLE IF EXISTS `credit_department`;
CREATE TABLE `credit_department`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '部门名',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业征信--部门' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise
-- ----------------------------
DROP TABLE IF EXISTS `enterprise`;
CREATE TABLE `enterprise`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业名称',
  `unified_credit_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '统一信用代码',
  `organization_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组织机构代码',
  `tax_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '税号',
  `legal_represent` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '法定代表人',
  `business_license_url` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '营业执照图片 url',
  `regist_capital` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '注册资本 单位万元',
  `regist_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '地址',
  `regist_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时间',
  `business_term` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '营业期限',
  `business_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '办公地址',
  `business_area` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '办公面积',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '创建人',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业认证信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_apply
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_apply`;
CREATE TABLE `enterprise_apply`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `apply_time` int(10) NOT NULL DEFAULT 0 COMMENT '申报时间',
  `apply_user_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申报人姓名',
  `policy_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '政策名',
  `apply_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态1草稿2提交申报',
  `apply_id` int(10) NOT NULL DEFAULT 0 COMMENT '申报id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业申报记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_apply_info
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_apply_info`;
CREATE TABLE `enterprise_apply_info`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `business_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业主营业务介绍',
  `plan_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '项目建设（计划）主要内容',
  `approval_organ` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批复机关',
  `approval_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批文文号',
  `qualifications` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经认证的资格、资质、证书及称呼',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业申报信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_apply_support
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_apply_support`;
CREATE TABLE `enterprise_apply_support`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `apply_time` int(10) NOT NULL DEFAULT 0 COMMENT '申报时间',
  `apply_user_name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申报人姓名',
  `policy_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '政策名',
  `apply_status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '状态1草稿2提交申报',
  `apply_project` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申报项目',
  `apply_id` int(10) NOT NULL DEFAULT 0 COMMENT '申报id',
  `content` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '扶持内容',
  `sup_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '支持时间',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业申报支持情况' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_business
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_business`;
CREATE TABLE `enterprise_business`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NULL DEFAULT 0 COMMENT '企业id',
  `business_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '办公地址',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  `business_area` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '办公面积',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业办公信息--地址--面积' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_credit
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_credit`;
CREATE TABLE `enterprise_credit`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `department_id` int(10) NOT NULL DEFAULT 0 COMMENT '部门id',
  `class_first_id` int(10) NOT NULL DEFAULT 0 COMMENT '一级分类',
  `class_second_id` int(10) NOT NULL DEFAULT 0 COMMENT '二级分类',
  `punish_number` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文号',
  `item` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '信息事项',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `decision_date` bigint(10) NOT NULL DEFAULT 0 COMMENT '做出判定的日期',
  `register_no` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '企业服务中心处罚的主键',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业信用信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_employee_overview
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_employee_overview`;
CREATE TABLE `enterprise_employee_overview`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `employee_number` int(10) NOT NULL DEFAULT 0 COMMENT '员工总数',
  `employee_degree` int(10) NOT NULL DEFAULT 0 COMMENT '本科以上人数',
  `employee_junior` int(10) NOT NULL DEFAULT 0 COMMENT '大专以上人数',
  `employee_other` int(10) NOT NULL DEFAULT 0 COMMENT '其他学历人数',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业员工概览' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_industry
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_industry`;
CREATE TABLE `enterprise_industry`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `first_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业一级',
  `second_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业二级',
  `third_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业三级',
  `fourth_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业四级',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业-行业关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_linkman
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_linkman`;
CREATE TABLE `enterprise_linkman`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `duty` tinyint(1) NOT NULL DEFAULT 0 COMMENT '职责 1 法人 2 单位负责人姓名 3 联系人姓名',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `wechat_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '微信号',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业联系人' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_send_record
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_send_record`;
CREATE TABLE `enterprise_send_record`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '消息标题',
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `enterprise_ids` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '企业id',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '类型 1-站内信 2-短信',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '申报id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业消息发送记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_tax
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_tax`;
CREATE TABLE `enterprise_tax`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '统计口径 1-全口径 2-本地',
  `year` year NULL DEFAULT NULL COMMENT '年度',
  `annual_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '年度税收总计 单位万元',
  `add_value_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '增值税 单位万元',
  `enterprise_income_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '企业所得税 单位万元',
  `business_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '营业税 单位万元',
  `individual_income_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '个人所得税 单位万元',
  `consumption_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '消费税 单位万元',
  `city_planning_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '城市建设税 单位万元',
  `house_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '房产税 单位万元',
  `stamp_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '印花税 单位万元',
  `urban_land_use_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '城镇土地使用税 单位万元',
  `land_increment_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '\r\n\r\n土地增值税 单位万元',
  `vehicle_and_vessel_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '车船税 单位万元',
  `vehicle_purchase_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '车辆购置税 单位万元',
  `farmland_conversion_tax` decimal(10, 2) UNSIGNED NOT NULL DEFAULT 0.00 COMMENT '耕地占用税 单位万元',
  `deed_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '契税',
  `other_tax` decimal(10, 2) NOT NULL DEFAULT 0.00 COMMENT '其他税种',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = ' 企业税收信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for enterprise_tax_import
-- ----------------------------
DROP TABLE IF EXISTS `enterprise_tax_import`;
CREATE TABLE `enterprise_tax_import`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `year` year NULL DEFAULT NULL COMMENT '年度',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '统计口径 1-全口径 2-本地',
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件路径',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '创建者',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = ' 企业税收导入的excel记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_apply
-- ----------------------------
DROP TABLE IF EXISTS `flo_apply`;
CREATE TABLE `flo_apply`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `enterprise_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '企业ID',
  `project_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '项目ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申报人ID',
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申报人姓名',
  `number` int(10) UNSIGNED NOT NULL DEFAULT 1 COMMENT '申报记录编号',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '表名',
  `policy_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '政策类型',
  `project_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支持项目',
  `enterprise_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业名称',
  `regist_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '注册地址',
  `regist_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '注册时间',
  `regist_capital` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '注册资本',
  `business_address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经营（办公）地址',
  `business_area` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经营（办公）面积',
  `unified_credit_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '统一信用代码',
  `organization_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组织机构代码',
  `industry_text` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行业类别名称',
  `industry_id` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行业类别ID',
  `employee_number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '单位员工总数',
  `employee_degree` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '本科以上学历人数',
  `employee_junior` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '大专学历人数',
  `employee_other` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '其他学历人数',
  `legal_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '法定代表人',
  `legal_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '法人手机号',
  `legal_wechat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '法人微信号',
  `charge_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '单位负责人姓名',
  `charge_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '负责人手机号',
  `charge_wechat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '负责人微信',
  `contact_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系人姓名',
  `contact_phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系人手机号',
  `contact_wechat` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '联系人微信',
  `business_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业主营业务介绍',
  `plan_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '项目建设（计划）主要内容',
  `approval_organ` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批复机关',
  `approval_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批文文号',
  `qualifications` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经认证的资格、资质、证书及称呼',
  `provisions` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申报政策条款',
  `apply_criteria` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请扶持资金计算依据（标准）',
  `apply_money` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请扶持资金金额',
  `other_notes` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '其他说明',
  `apply_status` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态1草稿2待系统预处理3待受理4不受理5待主审部门审核6线下会审中7待指挥部审核8待拨款9已拨款10主审部门不通过11线下会审不通过12指挥部不通过',
  `audit_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '系统预处理状态记录1处理中2处理成功',
  `audit_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核时间',
  `config` varchar(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件快照JSON',
  `support_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '获得支持',
  `allocation_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '拨款时间',
  `submit_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '提交时间：用于工作台统计',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '申请表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_apply_economy
-- ----------------------------
DROP TABLE IF EXISTS `flo_apply_economy`;
CREATE TABLE `flo_apply_economy`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `year` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '年度',
  `content` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '类型1销售收入2总产值3营业收入4主营业务收入5净利润6出口总额7纳税额',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_apply`(`apply_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '申请经济指标表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_apply_file
-- ----------------------------
DROP TABLE IF EXISTS `flo_apply_file`;
CREATE TABLE `flo_apply_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件url',
  `file_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '附件类型  0-补充材料 1-其他材料 2-发票 3-身份证 4-营业执照',
  `number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发票号码',
  `money` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发票金额',
  `check_status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '发票检查状态1待检查2异常3正常',
  `project_materials_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '材料清单类型ID',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_apply`(`apply_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '申请表附件' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_apply_file_exception
-- ----------------------------
DROP TABLE IF EXISTS `flo_apply_file_exception`;
CREATE TABLE `flo_apply_file_exception`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `apply_file_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发票附件ID',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型1识别失败2假发票3名称重复检查4其他项目使用重复检查',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'type为4的异常详情',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_apply_file`(`apply_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件发票异常信息' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval`;
CREATE TABLE `flo_approval`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核部门ID',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '审批类型1企业服务部2主审部门3协同部门4指挥部5园区办公室',
  `start_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计开始时间',
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '预计结束时间',
  `audit_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实际审核完成时间',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态:1待处理2已处理',
  `audit_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'type为2的审计操作0不需要1需要审计2审计延时',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门审批表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_accept
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_accept`;
CREATE TABLE `flo_approval_accept`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_message_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息ID',
  `approval_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审批ID',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门ID',
  `department_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '部门名称',
  `content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '记录内容',
  `is_read` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '消息状态 1 未处理 2 已处理',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '受理时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '已读时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_approval_id`(`approval_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '受理申报记录' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_config
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_config`;
CREATE TABLE `flo_approval_config`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `config_item` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置项',
  `config_value` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '配置值',
  `config_type` tinyint(2) UNSIGNED NOT NULL DEFAULT 1 COMMENT '配置类型1园区管委会2非审计类主审部门3审计类主审部门4审计类延长时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '审批配置表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_mark
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_mark`;
CREATE TABLE `flo_approval_mark`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `approval_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审批ID',
  `mark` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1企业服务不受理2园区办公室延时拨款3主审部门补充资料4协同部门补充资料',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '审批理由和补充资料表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_material
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_material`;
CREATE TABLE `flo_approval_material`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `approval_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审批ID',
  `enterprise_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '企业ID',
  `user_id` int(10) UNSIGNED NOT NULL COMMENT '接受者ID',
  `content` varchar(1200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态1待提交补充材料发送一次提醒2发送二次提醒3已提交补充材料',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送时间',
  `update_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `end_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '截止日期',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '主审部门和协同部门补充资料24小时定时消息通知' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_opinion
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_opinion`;
CREATE TABLE `flo_approval_opinion`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `approval_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审批ID',
  `expert_mark` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '专家意见',
  `department_mark` varchar(600) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '部门评审意见',
  `file_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '部门评审意见附件',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '审批意见' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for flo_approval_push
-- ----------------------------
DROP TABLE IF EXISTS `flo_approval_push`;
CREATE TABLE `flo_approval_push`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `apply_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '申请表ID',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '审核部门ID',
  `create_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '企业服务审批推送的其他部门表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for gov_agen
-- ----------------------------
DROP TABLE IF EXISTS `gov_agen`;
CREATE TABLE `gov_agen`  (
  `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '发文体系ID',
  `gov_agen_name` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发文体系名称',
  `parent_id` int(10) NOT NULL DEFAULT 0 COMMENT '发文体系父节点ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '发文体系表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for industry
-- ----------------------------
DROP TABLE IF EXISTS `industry`;
CREATE TABLE `industry`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `b_type` int(10) NULL DEFAULT NULL,
  `m_type` int(10) NULL DEFAULT NULL,
  `s_type` int(10) NULL DEFAULT NULL,
  `type_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for information
-- ----------------------------
DROP TABLE IF EXISTS `information`;
CREATE TABLE `information`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '正文',
  `source_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源',
  `order_num` smallint(6) NOT NULL DEFAULT 0 COMMENT '排序字段',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '员工id',
  `created_at` int(10) NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '资讯表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for login_logs
-- ----------------------------
DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE `login_logs`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '操作记录ID',
  `source_type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '所属对象类型 1- user  2-staff',
  `source_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `ip` int(10) NOT NULL DEFAULT 0 COMMENT 'ip地址',
  `address` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '登录地址',
  `type` int(10) NOT NULL DEFAULT 1 COMMENT '登录方式 1-PC端登录',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户登录记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for mold
-- ----------------------------
DROP TABLE IF EXISTS `mold`;
CREATE TABLE `mold`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '政策分类名称',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '政策类型基础表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for operation_logs
-- ----------------------------
DROP TABLE IF EXISTS `operation_logs`;
CREATE TABLE `operation_logs`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '操作记录ID',
  `obj_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '所属对象分类',
  `obj_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `operation_title` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作标题',
  `operation_content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '操作内容',
  `staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '操作员ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '操作记录表 暂时无用' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for password_resets
-- ----------------------------
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets`  (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp(0) NULL DEFAULT NULL,
  INDEX `password_resets_email_index`(`email`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy
-- ----------------------------
DROP TABLE IF EXISTS `policy`;
CREATE TABLE `policy`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '政策主表ID',
  `enc_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加密id值',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '编号',
  `obj_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '所属分类，1：宏观；2：扶持政策；3：实施细则；4：申报通知；5：扶持条款；6：细则条款；7：公示 10:拨款公示公告',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `doc_num` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文号',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '正文',
  `pub_time` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发文时间',
  `province_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：省',
  `city_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：市',
  `district_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：区',
  `validity_sdate` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期开始时间',
  `validity_edate` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期结束时间',
  `publish_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否发布，0：未发布；1：已发布',
  `source` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文章来源',
  `source_web` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源网站',
  `source_url` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源地址url',
  `is_handel` tinyint(1) NOT NULL DEFAULT -1 COMMENT '\'处理状态(扶持条款、细则条款)，0：未处理；1：已处理，-1：其他\',',
  `big_data_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '关联的原始数据id（big_data）',
  `target_id` int(10) NOT NULL DEFAULT 0 COMMENT '目标id，暂时用于拨款的来源id',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '员工id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_enc_id`(`enc_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_conclusion
-- ----------------------------
DROP TABLE IF EXISTS `policy_conclusion`;
CREATE TABLE `policy_conclusion`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '实施细则扶持政策扩展信息表ID',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的政策主表ID',
  `conclusion` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '结束语',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '实施细则扶持政策扩展信息表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_file
-- ----------------------------
DROP TABLE IF EXISTS `policy_file`;
CREATE TABLE `policy_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `download_times` int(10) NOT NULL DEFAULT 0 COMMENT '附件下载次数',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_policy_id`(`policy_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_gov_agen
-- ----------------------------
DROP TABLE IF EXISTS `policy_gov_agen`;
CREATE TABLE `policy_gov_agen`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '政策发文体系关联ID',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `gov_agen_first` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发文体系第一级ID',
  `gov_agen_second` tinyint(4) NOT NULL DEFAULT 0 COMMENT '发文体系第二级ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策发文体系关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_industry
-- ----------------------------
DROP TABLE IF EXISTS `policy_industry`;
CREATE TABLE `policy_industry`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '政策id',
  `first_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业一级',
  `second_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业二级',
  `third_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业三级',
  `fourth_industry_id` int(10) NOT NULL DEFAULT 0 COMMENT '行业四级',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策-行业关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_item
-- ----------------------------
DROP TABLE IF EXISTS `policy_item`;
CREATE TABLE `policy_item`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '政策条款ID',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的政策主表ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '条款内容',
  `item_id` int(10) NOT NULL DEFAULT 0 COMMENT '条款id,(关联policy表的条款id)',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策条款扩展信息表(扶持政策、实施细则)' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_mold
-- ----------------------------
DROP TABLE IF EXISTS `policy_mold`;
CREATE TABLE `policy_mold`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `mold_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的政策分类ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策类型关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_relation
-- ----------------------------
DROP TABLE IF EXISTS `policy_relation`;
CREATE TABLE `policy_relation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '政策关联关系ID',
  `obj_type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '所属对象，1：宏观；2：扶持政策；3：实施细则；4：申报通知；5：扶持条款；6：细则条款；7：公示',
  `obj_id` int(10) NULL DEFAULT 0 COMMENT '所属对象ID',
  `type` tinyint(2) NOT NULL DEFAULT 0 COMMENT '关联类型，1：宏观；2：扶持政策；3：实施细则；4：申报通知；5：扶持条款；6：细则条款；7：公示；98：历史；99：父级',
  `obj_type_relation_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的所属对象ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策关联关系表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_summarize
-- ----------------------------
DROP TABLE IF EXISTS `policy_summarize`;
CREATE TABLE `policy_summarize`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `direction_id` int(10) NOT NULL DEFAULT 0 COMMENT '概述方向id',
  `title` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策概述' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_summarize_direction
-- ----------------------------
DROP TABLE IF EXISTS `policy_summarize_direction`;
CREATE TABLE `policy_summarize_direction`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '所属对象ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策概述方向' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_unscramble
-- ----------------------------
DROP TABLE IF EXISTS `policy_unscramble`;
CREATE TABLE `policy_unscramble`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加密id',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '编号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名称',
  `content_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件名',
  `content_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容地址 pdf',
  `source_name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源',
  `publish_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否发布，0：未发布；1：已发布',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '员工id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策解读' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for policy_unscramble_relation
-- ----------------------------
DROP TABLE IF EXISTS `policy_unscramble_relation`;
CREATE TABLE `policy_unscramble_relation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `unscramble_id` int(10) NOT NULL DEFAULT 0 COMMENT '解读id',
  `obj_type` int(10) NOT NULL DEFAULT 0 COMMENT '关联的类型',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的id 政策id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '解读和政策的关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project
-- ----------------------------
DROP TABLE IF EXISTS `project`;
CREATE TABLE `project`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `enc_id` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '加密id',
  `policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '关联的政策主表ID',
  `code` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '编号',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '项目申报名称',
  `mold_id` int(10) NOT NULL DEFAULT 0 COMMENT '政策类型',
  `policy_basis` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '政策依据',
  `sup_object` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '扶持对象',
  `sup_content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '支持内容',
  `apply_condition` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '申报条件',
  `policy_advisory` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '政策咨询',
  `province_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：省',
  `city_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：市',
  `district_code` bigint(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '地区：区',
  `validity_sdate` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期开始时间',
  `validity_edate` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '有效期结束时间',
  `publish_status` tinyint(2) NOT NULL DEFAULT 0 COMMENT '是否发布，0：未发布；1：已发布',
  `created_staff_id` int(10) NOT NULL DEFAULT 0 COMMENT '员工id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '政策主表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_file
-- ----------------------------
DROP TABLE IF EXISTS `project_file`;
CREATE TABLE `project_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '附件ID',
  `project_id` int(10) NOT NULL DEFAULT 0 COMMENT '项目ID',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件名称',
  `save_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件存储地址',
  `download_times` int(10) NOT NULL DEFAULT 0 COMMENT '附件下载次数',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_project_id`(`project_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '项目附件表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_materials
-- ----------------------------
DROP TABLE IF EXISTS `project_materials`;
CREATE TABLE `project_materials`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT 0 COMMENT '项目申报id',
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `is_need` tinyint(1) NOT NULL DEFAULT 1 COMMENT '是否必备 1-必备 2-据实际提供',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '附件类型  0-补充材料 1-其他材料 2-发票 3-身份证 4-营业执照',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '项目申报材料表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_materials_other
-- ----------------------------
DROP TABLE IF EXISTS `project_materials_other`;
CREATE TABLE `project_materials_other`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL DEFAULT 0 COMMENT '项目申报id',
  `content` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '说明',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '项目申报材料其他说明' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for project_plate
-- ----------------------------
DROP TABLE IF EXISTS `project_plate`;
CREATE TABLE `project_plate`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` int(10) NOT NULL COMMENT '项目id',
  `title` varchar(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '板块名',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '内容',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '项目板块' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for resource
-- ----------------------------
DROP TABLE IF EXISTS `resource`;
CREATE TABLE `resource`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `resource_type_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '资源分类ID',
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '别名',
  `number` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '编号',
  `description` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '资源表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for resource_type
-- ----------------------------
DROP TABLE IF EXISTS `resource_type`;
CREATE TABLE `resource_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '别名',
  `description` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '资源分类表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `role_type_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色组ID',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `reserved` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否系统保留1否2是',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for role_bind_resource
-- ----------------------------
DROP TABLE IF EXISTS `role_bind_resource`;
CREATE TABLE `role_bind_resource`  (
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `resource_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '权限ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`role_id`, `resource_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色与权限关联' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for role_bind_staff
-- ----------------------------
DROP TABLE IF EXISTS `role_bind_staff`;
CREATE TABLE `role_bind_staff`  (
  `role_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色ID',
  `staff_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  PRIMARY KEY (`role_id`, `staff_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '员工与角色关联' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for role_type
-- ----------------------------
DROP TABLE IF EXISTS `role_type`;
CREATE TABLE `role_type`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `reserved` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否系统保留1否2是',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '角色组表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for staff
-- ----------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `number` smallint(5) UNSIGNED NOT NULL DEFAULT 1 COMMENT '编号',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '手机号',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码md5',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `sex` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '性别0未填写1男2女',
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `photo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '头像',
  `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '备注',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '员工表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for staff_bind_department
-- ----------------------------
DROP TABLE IF EXISTS `staff_bind_department`;
CREATE TABLE `staff_bind_department`  (
  `staff_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
  `department_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门ID',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `opertor_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '操作类型1操作人员2监督人员3普通人员',
  PRIMARY KEY (`staff_id`, `department_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '员工与部门关联' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for staff_code
-- ----------------------------
DROP TABLE IF EXISTS `staff_code`;
CREATE TABLE `staff_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mobile` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `code` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '短信code',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '验证码' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for staff_department
-- ----------------------------
DROP TABLE IF EXISTS `staff_department`;
CREATE TABLE `staff_department`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `name` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称',
  `parent_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父级ID,0一级',
  `parent_list` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '递归层级',
  `description` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '描述',
  `manager_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '部门主管ID',
  `type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '类型1区企业服务中心2普通部门3园区管委会企服中心4指挥部5园区管委会办公室',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '部门表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for staff_token
-- ----------------------------
DROP TABLE IF EXISTS `staff_token`;
CREATE TABLE `staff_token`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `staff_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
  `sign` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'sign随机字符串',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间-登录时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间-登录有效期结束时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_staff_sign`(`staff_id`, `sign`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'token' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `mobile` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '账号',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码md5',
  `name` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '姓名',
  `is_forbidden` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号状态 0-启用 1-禁用',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_code
-- ----------------------------
DROP TABLE IF EXISTS `user_code`;
CREATE TABLE `user_code`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `mobile` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '电话',
  `code` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '短信code',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_mobile`(`mobile`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户验证码' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_enterprise_relation
-- ----------------------------
DROP TABLE IF EXISTS `user_enterprise_relation`;
CREATE TABLE `user_enterprise_relation`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0 COMMENT '用户id',
  `enterprise_id` int(10) NOT NULL DEFAULT 0 COMMENT '企业id',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户-企业的关联表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_feedback
-- ----------------------------
DROP TABLE IF EXISTS `user_feedback`;
CREATE TABLE `user_feedback`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT 0 COMMENT '用户id',
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题',
  `content` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容',
  `type` tinyint(1) NOT NULL DEFAULT 0 COMMENT '反馈类型 1-建议 2-投诉 3-咨询',
  `status` tinyint(1) NOT NULL DEFAULT 2 COMMENT '是否处理 1-处理 2-待处理',
  `is_reply` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1-staff reply, 0-user',
  `source_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_source`(`source_id`) USING BTREE,
  FULLTEXT INDEX `idx_content`(`content`)
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户反馈表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_message
-- ----------------------------
DROP TABLE IF EXISTS `user_message`;
CREATE TABLE `user_message`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '自定义消息头',
  `content` varchar(1200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '消息内容',
  `user_id` int(10) NOT NULL DEFAULT 0 COMMENT '接收消息用户的ID',
  `user_type` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '用户类型1网站user2运营staff',
  `type` tinyint(1) NOT NULL DEFAULT 1 COMMENT '消息类型：1通知消息',
  `is_read` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '消息状态 1 未处理 2 已处理',
  `source_type_id` tinyint(1) NOT NULL DEFAULT 0 COMMENT '来源模块  0 -系统通知 1-反馈 2-申报 3-拨款',
  `target_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '来源id',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  `deleted_at` int(10) NULL DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '站内消息中心表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for user_token
-- ----------------------------
DROP TABLE IF EXISTS `user_token`;
CREATE TABLE `user_token`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '员工ID',
  `sign` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT 'sign随机字符串',
  `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间-登录时间',
  `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '修改时间-登录有效期结束时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_user_sign`(`user_id`, `sign`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = 'user token' ROW_FORMAT = Dynamic;


ALTER TABLE `flo_apply`
MODIFY COLUMN `config`  varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '附件快照JSON' AFTER `audit_time`;

ALTER TABLE `policy`
ADD COLUMN `original_policy_id` int(10) NOT NULL DEFAULT 0 COMMENT '政策迁移原始id' AFTER `target_id`;
ALTER TABLE `policy`
MODIFY COLUMN `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '正文' AFTER `doc_num`;

ALTER TABLE `policy_item`
MODIFY COLUMN `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '条款内容' AFTER `policy_id`;

ALTER TABLE `big_data`
ADD COLUMN `original_big_data_id` int(10) NOT NULL DEFAULT 0 COMMENT '原始big_data_id' AFTER `is_handle`;
ALTER TABLE `big_data`
MODIFY COLUMN `content` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '正文' AFTER `doc_num`;

ALTER TABLE `policy_unscramble`
MODIFY COLUMN `source_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源' AFTER `content_url`;

ALTER TABLE `user_message`
MODIFY COLUMN `source_type_id`  tinyint(1) NOT NULL DEFAULT 0 COMMENT '来源模块  0 -系统通知 1-反馈2申报受理3主审部门审核4协同部门审核5指挥部决策6拨款7审核通知8申报消息9协同部门评审完成10企业补充资料11申报审核12打款公示' AFTER `is_read`;

ALTER TABLE `activity_log`
MODIFY COLUMN `subject_type_id` tinyint(1) NOT NULL DEFAULT 0 COMMENT '受影响的类型 1-政策 2-政策解读 3-申报项目' AFTER `subject_type`,
ADD COLUMN `ip` int(10) NOT NULL DEFAULT 0 COMMENT 'ip地址' AFTER `properties`,
ADD COLUMN `terminal` tinyint(1) NOT NULL DEFAULT 1 COMMENT '终端设备 1-web端' AFTER `ip`,
COMMENT = '操作日志';

ALTER TABLE `flo_apply_file`
CHANGE COLUMN `number` `invoice_number`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发票号码' AFTER `file_type`,
CHANGE COLUMN `money` `invoice_money`  varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '发票金额' AFTER `invoice_number`,
ADD COLUMN `invoice_billing_date`  varchar(20) NOT NULL DEFAULT '' COMMENT '开票日期' AFTER `invoice_money`,
ADD COLUMN `invoice_checkcode`  varchar(30) NOT NULL DEFAULT '' COMMENT '校验码' AFTER `invoice_billing_date`,
ADD COLUMN `invoice_code`  varchar(30) NOT NULL DEFAULT '' COMMENT '发票代码' AFTER `invoice_checkcode`;

ALTER TABLE `enterprise`
MODIFY COLUMN `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业名称' AFTER `id`;


ALTER TABLE `flo_approval_material`
CHANGE COLUMN `content` `mark`  varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容' AFTER `user_id`,
ADD COLUMN `start_time`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '开始时间' AFTER `update_at`;

ALTER TABLE `user_feedback`
MODIFY COLUMN `title` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题 /回复的备注' AFTER `user_id`;


ALTER TABLE `policy`
MODIFY COLUMN `source_url` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '来源地址url' AFTER `source_web`;

ALTER TABLE `flo_apply`
ADD COLUMN `business_id`  varchar(30) NOT NULL DEFAULT '' COMMENT '业务ID' AFTER `submit_time`,
ADD COLUMN `pdf_url`  varchar(255) NOT NULL DEFAULT '' COMMENT 'pdf文件路径' AFTER `business_id`;

DROP TABLE IF EXISTS `enterprise_backup`;
CREATE TABLE `enterprise_backup` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `key_no` varchar(100) NOT NULL DEFAULT '' COMMENT '第三方主键',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '企业名称',
  `content` json DEFAULT NULL COMMENT '第三方返回的数据 备份',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0',
  `updated_at` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='企业第三方的备份';

DROP TABLE IF EXISTS `api`;
CREATE TABLE `api` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `api_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '资源分类ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(50) NOT NULL DEFAULT '' COMMENT '别名',
  `number` varchar(10) NOT NULL DEFAULT '' COMMENT '编号',
  `description` varchar(200) NOT NULL DEFAULT '' COMMENT '描述',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API表';

DROP TABLE IF EXISTS `api_type`;
CREATE TABLE `api_type` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '名称',
  `alias` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '别名',
  `description` varchar(200) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '描述',
  `sort` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='API分类表-按照页面分类';

ALTER TABLE `flo_apply`
CHANGE COLUMN `business_id` `business_id1`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '业务ID用于申请表' AFTER `submit_time`,
CHANGE COLUMN `pdf_url` `apply_pdf_url`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请表pdf文件路径' AFTER `business_id1`,
ADD COLUMN `business_id2`  varchar(30) NOT NULL DEFAULT '' COMMENT '业务ID2用于打印pdf' AFTER `apply_pdf_url`,
ADD COLUMN `print_pdf_url`  varchar(255) NOT NULL DEFAULT '' COMMENT '打印PDF' AFTER `business_id2`;

ALTER TABLE `flo_apply`
DROP COLUMN `business_id2`,
DROP COLUMN `print_pdf_url`,
CHANGE COLUMN `business_id1` `business_id`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '业务ID用于申请表' AFTER `submit_time`,
CHANGE COLUMN `apply_pdf_url` `pdf_url`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请表pdf文件路径' AFTER `business_id`;

ALTER TABLE `flo_approval_opinion`
ADD COLUMN `business_id`  varchar(30) NOT NULL DEFAULT '' COMMENT '业务ID' AFTER `create_at`,
ADD COLUMN `pdf_url`  varchar(255) NOT NULL DEFAULT '' COMMENT 'pdf路径' AFTER `business_id`;

ALTER TABLE `flo_apply`
MODIFY COLUMN `policy_name`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '政策类型' AFTER `title`,
MODIFY COLUMN `project_name`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '支持项目' AFTER `policy_name`,
MODIFY COLUMN `enterprise_name`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '企业名称' AFTER `project_name`;


ALTER TABLE `policy_summarize`
MODIFY COLUMN `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '标题' AFTER `direction_id`,
MODIFY COLUMN `content` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '内容' AFTER `title`;

CREATE TABLE `role_bind_api` (
  `role_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '角色ID',
  `api_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '接口ID',
  PRIMARY KEY (`role_id`,`api_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='角色与接口权限关联';

ALTER TABLE `flo_apply`
MODIFY COLUMN `business_address`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '经营（办公）地址' AFTER `regist_capital`,
MODIFY COLUMN `industry_text`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行业类别名称' AFTER `organization_code`,
MODIFY COLUMN `industry_id`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '行业类别ID' AFTER `industry_text`;

CREATE TABLE `staff_department_bind_department` (
  `department_one_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '园区企服中心部门ID',
  `department_two_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '园区办公室部门ID',
  PRIMARY KEY (`department_one_id`,`department_two_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门与部门关联';

ALTER TABLE `activity_log`
MODIFY COLUMN `ip` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ip地址' AFTER `properties`;
ALTER TABLE `login_logs`
MODIFY COLUMN `ip` bigint(20) NOT NULL DEFAULT 0 COMMENT 'ip地址' AFTER `source_id`;

ALTER TABLE `project_materials`
MODIFY COLUMN `name` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '名称' AFTER `project_id`;

ALTER TABLE `enterprise`
MODIFY COLUMN `legal_represent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '法定代表人' AFTER `tax_number`;


ALTER TABLE `flo_apply`
MODIFY COLUMN `organization_code`  varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组织机构代码' AFTER `unified_credit_code`;
ALTER TABLE `flo_apply`
MODIFY COLUMN `approval_organ`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批复机关' AFTER `plan_content`,
MODIFY COLUMN `approval_number`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '批文文号' AFTER `approval_organ`,
MODIFY COLUMN `apply_criteria`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请扶持资金计算依据（标准）' AFTER `provisions`;

ALTER TABLE `enterprise`
MODIFY COLUMN `organization_code` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组织机构代码' AFTER `unified_credit_code`;


ALTER TABLE `flo_apply`
ADD COLUMN `children_id`  int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '子申请表ID' AFTER `id`;

ALTER TABLE `enterprise`
MODIFY COLUMN `organization_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '组织机构代码' AFTER `unified_credit_code`;

ALTER TABLE `flo_apply`
MODIFY COLUMN `apply_criteria`  varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '申请扶持资金计算依据（标准）' AFTER `provisions`;

ALTER TABLE `enterprise_tax_import`
ADD COLUMN `current_row` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '处理到当前的行数' AFTER `file_url`;

ALTER TABLE `policy`
ADD COLUMN `content_name` varchar(255) NOT NULL DEFAULT '' COMMENT '正文附件名' AFTER `content`,
ADD COLUMN `content_url` varchar(255) NOT NULL DEFAULT '' COMMENT '正文附件地址' AFTER `content_name`;

CREATE TABLE `flo_approval_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `approval_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '审批ID',
  `file_name` varchar(255) NOT NULL DEFAULT '' COMMENT '附件名称',
  `file_url` varchar(255) NOT NULL DEFAULT '' COMMENT '附件url',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_apply` (`approval_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='审批附件';


ALTER TABLE `flo_approval`
ADD COLUMN `remark`  varchar(255) NOT NULL DEFAULT '' COMMENT '备注' AFTER `create_at`;

ALTER TABLE `mold`
MODIFY COLUMN `name` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '政策分类名称' AFTER `id`;

ALTER TABLE `policy`.`user_message`
ADD INDEX `idx_user_id`(`user_id`) USING BTREE;

ALTER TABLE `project`
ADD INDEX `idx_enc_id`(`enc_id`) USING BTREE;

ALTER TABLE `big_data`
ADD INDEX `idx_obj_handle`(`obj_type`, `is_handle`) USING BTREE;

ALTER TABLE `policy_conclusion`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_gov_agen`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_industry`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_item`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_mold`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_summarize_direction`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_summarize`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy_relation`
ADD INDEX `idx_obj_id`(`obj_id`) USING BTREE,
ADD INDEX `idx_obj_type_relation_id`(`obj_type_relation_id`) USING BTREE;

ALTER TABLE `policy_unscramble`
ADD INDEX `idx_enc_id`(`enc_id`) USING BTREE;

ALTER TABLE `policy_unscramble_relation`
ADD INDEX `idx_policy`(`policy_id`) USING BTREE;

ALTER TABLE `policy`
ADD INDEX `idx_obj_type`(`obj_type`) USING BTREE;

ALTER TABLE `project_materials`
ADD INDEX `idx_project`(`project_id`) USING BTREE;

ALTER TABLE `project_materials_other`
ADD INDEX `idx_project`(`project_id`) USING BTREE;

ALTER TABLE `project_plate`
ADD INDEX `idx_project`(`project_id`) USING BTREE;

ALTER TABLE `activity_log`
ADD INDEX `idx_subject_type_id_subject_id`(`subject_type_id`, `subject_id`) USING BTREE;

ALTER TABLE `user`
ADD COLUMN `email` varchar(50) NOT NULL DEFAULT '' COMMENT '邮箱' AFTER `mobile`;

CREATE TABLE `user_unbundling` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `step` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '解绑步骤',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '软删除时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户解绑流程';

ALTER TABLE `flo_apply`
ADD COLUMN `mold_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '政策类型id' AFTER `project_id`;

ALTER TABLE `flo_apply_file_exception`
ADD COLUMN `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `create_at`,
ADD COLUMN `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间' AFTER `updated_at`;
ALTER TABLE `flo_apply_file`
ADD COLUMN `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `create_at`,
ADD COLUMN `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间' AFTER `updated_at`;
ALTER TABLE `flo_apply_file`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `project_materials_id`;
ALTER TABLE `flo_apply_file_exception`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `remark`;
ALTER TABLE `flo_approval`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `audit_type`;
ALTER TABLE `flo_approval_push`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `department_id`;
ALTER TABLE  `flo_approval_opinion`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `file_name`;
ALTER TABLE `flo_approval_material`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送时间' AFTER `status`,
CHANGE COLUMN `update_at` `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `created_at`;
ALTER TABLE `flo_approval_mark`
CHANGE COLUMN `create_at` `created_at` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建时间' AFTER `type`;
ALTER TABLE `flo_approval`
ADD COLUMN `updated_at` int(10) UNSIGNED NOT NULL DEFAULT 0  COMMENT '更新时间' AFTER `created_at`,
ADD COLUMN `deleted_at` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '删除时间' AFTER `updated_at`;
ALTER TABLE `flo_apply_file_exception`
ADD COLUMN `is_year` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否一年内发票 1-是' AFTER `type`,
ADD COLUMN `is_truth` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1-疑似假发票 2-未变真伪 0-默认' AFTER `is_year`,
ADD COLUMN `repeat_apply` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重复申报 1-重复申报' AFTER `is_truth`,
ADD COLUMN `repeat` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否重复 1-重复' AFTER `repeat_apply`;
ALTER TABLE  `flo_apply_file_exception`
MODIFY COLUMN `is_year` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否一年内发票 1-是 2-不是' AFTER `type`,
MODIFY COLUMN `repeat_apply` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '重复申报 1-重复申报 2-否' AFTER `is_truth`,
MODIFY COLUMN `repeat` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否重复 1-重复 2-否' AFTER `repeat_apply`,
ADD COLUMN `ocr` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT 'ocr识别是否成功 1-成功 2-失败' AFTER `type`;
ALTER TABLE `flo_apply_file_exception`
MODIFY COLUMN `is_year` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否一年内发票 1-是 2-不是' AFTER `ocr`;
ALTER TABLE `flo_apply_file_exception`
MODIFY COLUMN `remark` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '异常详情' AFTER `repeat`,
ADD COLUMN `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 3 COMMENT '状态 1-正常 2-异常 3-未检查完' AFTER `repeat`;
CREATE TABLE flo_apply_file_exception_bak  LIKE flo_apply_file_exception;
INSERT INTO flo_apply_file_exception_bak SELECT * FROM flo_apply_file_exception;

SET FOREIGN_KEY_CHECKS = 1;
