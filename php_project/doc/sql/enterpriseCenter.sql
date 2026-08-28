CREATE TABLE `migrate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `register_no` varchar(100) NOT NULL DEFAULT '' COMMENT '注册号',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型  对应相应的处罚表 1-ajj 2-dsj 3-fgw 4-gaj 5-gsj 6-hbj 7-jw 8-jxw 9-lyj 10-mzj 11-sfj 12-sjw 13-wsj',
  `created_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `deleted_at` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`id`),
  KEY `idx_no` (`register_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='处罚--已经迁移表记录表';