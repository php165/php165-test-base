
-- ----------------------------
-- Table structure for cool_advertising
-- ----------------------------
DROP TABLE IF EXISTS `cool_advertising`;

CREATE TABLE `cool_advertising` (
  `ad_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告表主键id',
  `position_id` int(5) NOT NULL DEFAULT '0' COMMENT '广告位表主键id',
  `mark_id` tinyint(1) NOT NULL DEFAULT '0' COMMENT '跳转标识id',
  `mark_content` varchar (50) NOT NULL DEFAULT '0' COMMENT '跳转内容，如文章模块，存文章id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '广告标题',
  `subhead` varchar(200) NOT NULL DEFAULT '' COMMENT '广告副标题',
  `slogan` varchar(255) NOT NULL DEFAULT '' COMMENT '广告标语',
  `ad_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '广告类型：1图片，2视频',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用：1启用，2禁用',
  `sort` int(5) NOT NULL DEFAULT '1' COMMENT '排序，值越小越靠前',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点击量',
  `running_time` int(5) NOT NULL DEFAULT '5' COMMENT '图片展示时间，单位为秒',
  `is_validity` tinyint(1) DEFAULT '1' COMMENT '有效期：0 无限期；1根据时间',
  `begin_time` int(10) NOT NULL DEFAULT '0' COMMENT '广告开始时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '广告结束时间',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `mark_id` (`mark_id`),
  KEY `click_count` (`click_count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告表';

-- ----------------------------
-- Table structure for cool_advertising_position
-- ----------------------------
DROP TABLE IF EXISTS `cool_advertising_position`;
CREATE TABLE `cool_advertising_position` (
 `position_id` int(4) NOT NULL AUTO_INCREMENT COMMENT '广告位表自增id',
 `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '客户端: 1 PC端官网 ,2用户端 ,3商户端',
 `position_name` varchar(80) NOT NULL DEFAULT '' COMMENT '广告位置名称',
 `position_desc` varchar(255) NOT NULL  DEFAULT '' COMMENT '广告位描述',
 `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位宽度',
 `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '广告位高度',
 `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否启用: 1 启用; 2 禁用;',
 `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
 `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
 `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
 PRIMARY KEY (`position_id`),
 KEY `type` (`type`),
 KEY `status` (`status`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告位置表';

-- ----------------------------
-- Table structure for cool_advertising_mark
-- ----------------------------
DROP TABLE IF EXISTS `cool_advertising_mark`;
CREATE TABLE `cool_advertising_mark` (
 `mark_id` int(4) NOT NULL AUTO_INCREMENT COMMENT '广告跳转标识表自增id',
 `mark_name` varchar(80) NOT NULL DEFAULT '' COMMENT '模块名称',
 `mark_content` varchar(150) NOT NULL DEFAULT '' COMMENT '模块标识内容',
 `mark` varchar(60) NOT NULL DEFAULT '' COMMENT '标识英文标记，如文章：article',
 `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否开启: 1 启用; 2 禁用;',
 `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
 `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '修改时间',
 `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
 PRIMARY KEY (`mark_id`),
 KEY `status` (`status`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告跳转标识表';

-- ----------------------------
-- Table structure for cool_article
-- ----------------------------
DROP TABLE IF EXISTS `cool_article`;
CREATE TABLE `cool_article` (
  `article_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '资讯表自增id',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '资讯标题',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `user_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '用户类型：1后台管理员，',
  `article_cate_id` int(10) NOT NULL DEFAULT '0' COMMENT '分类id',
  `article_img` varchar(200) NOT NULL DEFAULT '' COMMENT '列表展示图',
  `status` tinyint(1) unsigned DEFAULT '1' COMMENT '启用状态： 1上架中 ，2下架中',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '文章描述',
  `subhead` varchar(255) NOT NULL DEFAULT '' COMMENT '文章副标题',
  `source` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '文章来源：1平台系统，2',
  `is_top` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '是否置顶：1是，2否',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '2' COMMENT '是否热门：1是，2否',
  `copyright` varchar(255) NOT NULL DEFAULT '' COMMENT '版权说明',
  `page_view_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览量',
  `like_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞量',
  `comment_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论总量',
  `sort` int(5) unsigned DEFAULT '1' COMMENT '排序,值越小越靠前',
  `content` longtext NOT NULL COMMENT '资讯详情',
  `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
  `update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
  PRIMARY KEY (`article_id`),
  KEY `user_id` (`user_id`),
  KEY `source` (`source`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章资讯表';

DROP TABLE IF EXISTS `cool_article_cate`;
CREATE TABLE `cool_article_cate` (
`article_cate_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章资讯分类表主键id',
`article_cate_name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名称',
`sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序，值越小越靠前',
`parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级id',
`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '启用状态: 1正常 ,2禁用 ,3删除',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
PRIMARY KEY (`article_cate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章资讯分类表';

-- ----------------------------
-- Table structure for cool_article_comment
-- ----------------------------
DROP TABLE IF EXISTS `cool_article_comment`;
CREATE TABLE `cool_article_comment` (
`comment_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章资讯评论表主键id',
`article_id` int(10) unsigned DEFAULT '0' COMMENT '文章资讯id',
`content` varchar(255) NOT NULL DEFAULT '' COMMENT '评论内容',
`user_id` int(10) NOT NULL DEFAULT '0' COMMENT '评论人id',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评论人类型：1用户、2其他',
`comment_reply_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论回复的数量',
`comment_like_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论点赞的数量',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
`delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
PRIMARY KEY (`comment_id`),
KEY `user_id` (`user_id`),
KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='医生资讯评论表';

-- ----------------------------
-- Table structure for cool_article_comment_like
-- ----------------------------
DROP TABLE IF EXISTS `cool_article_comment_like`;
CREATE TABLE `cool_article_comment_like` (
`comment_like_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章资讯评论点赞表自增id',
`comment_id` varchar(50) NOT NULL DEFAULT '0' COMMENT '资讯评论id',
`user_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞用户id',
`status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1点赞 0取消点赞',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '评论人类型：1用户、2其他',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
`delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
PRIMARY KEY (`comment_like_id`),
KEY `type` (`type`),
KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章资讯评论点赞表';

-- ----------------------------
-- Table structure for cool_article_comment_reply
-- ----------------------------
DROP TABLE IF EXISTS `cool_article_comment_reply`;
CREATE TABLE `cool_article_comment_reply` (
`comment_reply_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '医生资讯评论点赞自增id',
`comment_id` int(10) NOT NULL DEFAULT '0' COMMENT '资讯评论id',
`user_id` int(10) NOT NULL DEFAULT '0' COMMENT '回复用户id',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '回复人类型：1用户、2其他',
`content` varchar(255) NOT NULL DEFAULT '' COMMENT '回复内容',
`parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '父id',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
`delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
PRIMARY KEY (`comment_reply_id`),
KEY `reply_user_id` (`user_id`),
KEY `type` (`type`),
KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='资讯评论回复表';

-- ----------------------------
-- Table structure for cool_article_like
-- ----------------------------
DROP TABLE IF EXISTS `cool_article_like`;
CREATE TABLE `cool_article_like` (
`like_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '文章资讯评论点赞表自增id',
`article_id` varchar(50) NOT NULL DEFAULT '0' COMMENT '文章资讯id',
`user_id` int(10) NOT NULL DEFAULT '0' COMMENT '点赞用户id',
`status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '点赞状态 0未操作 ,1已点赞 ,2已取消点赞',
`type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '点赞用户类型：1用户、2其他',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
`delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
PRIMARY KEY (`like_id`),
KEY `user_id` (`user_id`),
KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章资讯点赞表';

-- ----------------------------
-- 用户行为表
-- ----------------------------
DROP TABLE IF EXISTS `cool_behavior`;
CREATE TABLE `cool_behavior` (
`behavior_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '用户行为表主键id',
`behavior_event_id` int(5) NOT NULL DEFAULT '0' COMMENT '用户行为事件id',
`event_time` int(10) NOT NULL DEFAULT '0' COMMENT '事件发生的时间，由前端上传',
`page` varchar (200) NOT NULL DEFAULT '' COMMENT '事件的路由地址',
`uuid` varchar (64) NOT NULL DEFAULT '' COMMENT '用户浏览时随机生成的唯一用户id',
`param` varchar (255) NOT NULL DEFAULT '' COMMENT '参数json',
`ip` varchar (20) NOT NULL DEFAULT '0' COMMENT 'IP地址',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
PRIMARY KEY (`behavior_id`),
KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户行为表';

-- ----------------------------
-- 用户行为事件表
-- ----------------------------
DROP TABLE IF EXISTS `cool_behavior_event`;
CREATE TABLE `cool_behavior_event` (
`event_id` int(5) NOT NULL AUTO_INCREMENT COMMENT '用户行为事件表主键id',
`scene_id` int(5) NOT NULL DEFAULT '0' COMMENT '行为场景id',
`event_content` varchar (255) NOT NULL DEFAULT '' COMMENT '行为事件内容',
`event_name` varchar (100) NOT NULL DEFAULT '' COMMENT '行为事件名称，如注册',
`event_element` varchar (100) NOT NULL DEFAULT '' COMMENT '行为事件名称英文，如register',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
PRIMARY KEY (`event_id`),
KEY `scene_id` (`scene_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户行为事件表';

-- ----------------------------
-- 用户行为场景表
-- ----------------------------
DROP TABLE IF EXISTS `cool_behavior_scene`;
CREATE TABLE `cool_behavior_scene` (
`scene_id` int(5) NOT NULL AUTO_INCREMENT COMMENT '用户行为场景表主键id',
`scene_name` varchar (100) NOT NULL DEFAULT '' COMMENT '行为场景名称',
`scene_desc` varchar (255) NOT NULL DEFAULT '' COMMENT '行为场景描述',
`status` tinyint (1) NOT NULL DEFAULT '1' COMMENT '启用状态：1启用，2禁用',
`create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
`update_time` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
PRIMARY KEY (`scene_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户行为场景表';