ALTER TABLE `flo_apply`
MODIFY COLUMN `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '附件快照JSON' AFTER `audit_time`;

ALTER TABLE `policy`.`flo_apply`
MODIFY COLUMN `config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '附件快照JSON' AFTER `audit_time`;