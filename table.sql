
-- ----------------------------
-- Table structure for sms_log_tbl
-- ----------------------------
DROP TABLE IF EXISTS `sms_log_tbl`;
CREATE TABLE `sms_log_tbl` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `mobile` varchar(11) NOT NULL COMMENT '手机号码',
  `area_code` varchar(4) NOT NULL COMMENT '电话地区号',
  `content` varchar(255) NOT NULL COMMENT '短信内容',
  `gateway` varchar(20) NOT NULL COMMENT '网关',
  `product_id` tinyint(4) unsigned NOT NULL COMMENT '产品ID：由程序配置',
  `response` varchar(255) NOT NULL COMMENT '网关响应',
  `err_code` smallint(5) unsigned NOT NULL COMMENT '错误码',
  `ip` varchar(11) NOT NULL COMMENT 'IP',
  `create_time` int(10) unsigned NOT NULL COMMENT '创建时间',
  `update_time` int(10) unsigned NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COMMENT='短信日志';