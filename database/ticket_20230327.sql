# ************************************************************
# Database: ets
# Generation Time: 2023-03-27 01:19:49 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table d_posts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `d_posts`;

CREATE TABLE `d_posts` (
                           `post_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息ID',
                           `parent_id` int(11) DEFAULT '0' COMMENT '回复消息ID',
                           `ticket_id` int(11) NOT NULL,
                           `content` longtext NOT NULL COMMENT '消息内容',
                           `attachment` varchar(5000) DEFAULT '',
                           `created_by` varchar(50) NOT NULL DEFAULT '' COMMENT '回复人',
                           `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           PRIMARY KEY (`post_id`),
                           KEY `parent_id` (`parent_id`),
                           KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='消息回复记录';



# Dump of table d_ticket_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `d_ticket_log`;

CREATE TABLE `d_ticket_log` (
                                `log_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                `ticket_id` int(11) NOT NULL,
                                `op_type` varchar(20) NOT NULL DEFAULT '' COMMENT '操作类型',
                                `op_by` varchar(50) NOT NULL DEFAULT '' COMMENT '操作人',
                                `assign_to_dept` int(11) DEFAULT '0',
                                `assign_to` varchar(50) DEFAULT '' COMMENT '指派时记录',
                                `description` varchar(500) DEFAULT '',
                                `result` longtext,
                                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '操作时间',
                                PRIMARY KEY (`log_id`),
                                KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工单操作日志';



# Dump of table d_tickets
# ------------------------------------------------------------

DROP TABLE IF EXISTS `d_tickets`;

CREATE TABLE `d_tickets` (
                             `ticket_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                             `title` varchar(255) NOT NULL DEFAULT '' COMMENT '标题',
                             `content` longtext NOT NULL COMMENT '内容',
                             `expect_finish_at` date DEFAULT NULL COMMENT '期望完成时间',
                             `created_by` varchar(50) NOT NULL DEFAULT '' COMMENT '创建人',
                             `created_by_dept` int(11) NOT NULL,
                             `assign_to` varchar(50) DEFAULT '' COMMENT '被指派人',
                             `assign_to_dept` int(11) DEFAULT '0',
                             `final_handler` varchar(50) DEFAULT '0' COMMENT '最后处理人',
                             `final_handler_dept` int(11) DEFAULT '0' COMMENT '最后处理人部门',
                             `attachment` varchar(5000) DEFAULT '',
                             `status` tinyint(4) DEFAULT '1' COMMENT '状态',
                             `finish_at` timestamp NULL DEFAULT NULL COMMENT '完成时间(完成|取消)',
                             `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='工单';



# Dump of table sys_depts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_depts`;

CREATE TABLE `sys_depts` (
                             `dept_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                             `dept_name` varchar(255) NOT NULL DEFAULT '',
                             `parent_id` int(11) NOT NULL DEFAULT '0',
                             `l_num` int(11) NOT NULL DEFAULT '0' COMMENT '左值',
                             `r_num` int(11) NOT NULL DEFAULT '0' COMMENT '右值',
                             `lv` int(11) NOT NULL DEFAULT '0' COMMENT '层级',
                             `super_admin` tinyint(4) DEFAULT '0',
                             `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='部门表';



# Dump of table sys_user_dept
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_user_dept`;

CREATE TABLE `sys_user_dept` (
                                 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                                 `uid` varchar(50) NOT NULL DEFAULT '',
                                 `dept_id` int(11) NOT NULL,
                                 `is_leader` int(11) DEFAULT '0',
                                 PRIMARY KEY (`id`),
                                 UNIQUE KEY `uniq-uid-dept_id` (`uid`,`dept_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户部门表';



# Dump of table sys_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_users`;

CREATE TABLE `sys_users` (
                             `_id` int(11) NOT NULL AUTO_INCREMENT,
                             `uid` varchar(50) NOT NULL DEFAULT '' COMMENT '钉钉UID',
                             `username` varchar(255) NOT NULL DEFAULT '',
                             `is_admin` tinyint(4) DEFAULT '0' COMMENT '是否管理员',
                             `position` varchar(255) DEFAULT '' COMMENT '职位',
                             `jobnumber` varchar(255) NOT NULL DEFAULT '' COMMENT '工号',
                             `avatar` varchar(255) NOT NULL DEFAULT '',
                             `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (`_id`),
                             UNIQUE KEY `uniq-uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
