DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` int(10) NOT NULL auto_increment,
  `name` varchar(160) default NULL,
  `slug` varchar(160) default NULL,
  `count` int(10) NOT NULL default '0',
  `created` datetime default NULL,
  PRIMARY KEY  (`id`)
);

DROP TABLE IF EXISTS `tagged`;
CREATE TABLE `tagged` (
  `id` int(10) NOT NULL auto_increment,
  `tag_id` int(10) default NULL,
  `model` varchar(100) default NULL,
  `model_id` int(10) default NULL,
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `model` (`model`),
  KEY `model_id` (`model_id`)
);