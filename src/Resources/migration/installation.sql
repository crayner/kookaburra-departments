CREATE TABLE `__prefix__Department` (
  `id` int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Learning Area',
  `name` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `nameShort` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `subjectListing` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `blurb` longtext COLLATE utf8_unicode_ci,
  `logo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `nameShort` (`nameShort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;
CREATE TABLE `__prefix__DepartmentResource` (
    id INT(8) UNSIGNED AUTO_INCREMENT,
    type VARCHAR(16) NOT NULL,
    name VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    department INT(4) UNSIGNED,
    INDEX department (department), PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB AUTO_INCREMENT = 1;
CREATE TABLE `__prefix__DepartmentStaff` (
  `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role` varchar(24) COLLATE utf8_unicode_ci NOT NULL,
  `department` int(4) UNSIGNED DEFAULT NULL,
  `person` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departmentPerson` (`department`,`person`),
  KEY `department` (`department`),
  KEY `person` (`person`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT = 1;
