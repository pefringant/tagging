CREATE TABLE `tags` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  `slug` varchar(255) collate utf8_unicode_ci NOT NULL,
  `count` int(10) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `tagged` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `tag_id` int(10) unsigned NOT NULL,
  `model` varchar(255) collate utf8_unicode_ci NOT NULL,
  `assoc_key` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `tag_id` (`tag_id`),
  KEY `model` (`model`),
  KEY `assoc_key` (`assoc_key`)
);