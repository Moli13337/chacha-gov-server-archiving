ALTER TABLE `flo_apply`
ADD COLUMN `is_supplement` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否补录 默认 0 ， 1 补录' AFTER `pdf_url`;

ALTER TABLE `flo_apply`
ADD COLUMN `created_staff_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者' AFTER `is_supplement`;

ALTER TABLE `flo_apply_file_exception`
MODIFY COLUMN `is_truth` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '1-真发票 2-疑似假发票 3-未变真伪 0-默认' AFTER `is_year`;

INSERT INTO `api_type`(`id`, `name`, `alias`, `description`, `sort`, `created_at`, `updated_at`) VALUES (22, '发票补录管理', '', '', 0, 1569485223, 1569485223);

INSERT INTO `resource`(`id`, `resource_type_id`, `name`, `alias`, `number`, `description`, `created_at`, `updated_at`) VALUES (33, 6, '发票补录管理', 'invoice_supplement', '1000033', '发票补录管理', 1569485223, 1569485223);

INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (152, 16, '项目选择列表', '', '1000140', '项目选择列表', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (153, 18, '企业选择列表', '', '1000141', '企业选择列表', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (154, 22, '发票补录新增', '', '1000142', '发票补录新增', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (155, 22, '发票补录更新', '', '1000143', '发票补录更新', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (156, 22, '发票补录删除', '', '1000144', '发票补录删除', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (157, 22, '发票补录列表', '', '1000145', '发票补录列表', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (158, 22, '发票补录新增发票', '', '1000146', '发票补录新增发票', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (159, 22, '发票补录发票列表', '', '1000147', '发票补录发票列表', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (160, 22, '发票补录发票更新', '', '1000148', '发票补录发票更新', 0, 1569486334, 1569486334);
INSERT INTO `api`(`id`, `api_type_id`, `name`, `alias`, `number`, `description`, `sort`, `created_at`, `updated_at`) VALUES (161, 22, '发票补录发票删除', '', '1000149', '发票补录发票删除', 0, 1569486334, 1569486334);
