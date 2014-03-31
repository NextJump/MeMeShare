-- --------------------------------------------------------

--
-- Table structure for table `tb_family`
--

CREATE TABLE IF NOT EXISTS `tb_family` (
  `pk_family_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `student_fname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `student_lname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `cohort_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fk_pk_house_id` int(11) NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_family_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `tb_family_user_map`
--

CREATE TABLE IF NOT EXISTS `tb_family_user_map` (
  `pk_family_user_map_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk_pk_family_id` int(11) NOT NULL,
  `fk_pk_user_id` bigint(20) NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_family_user_map_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `tb_house`
--

CREATE TABLE IF NOT EXISTS `tb_house` (
  `pk_house_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_house_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `tb_interaction`
--

CREATE TABLE IF NOT EXISTS `tb_interaction` (
  `pk_interaction_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk_pk_user_id` bigint(20) NOT NULL,
  `fk_pk_family_id` int(11) NOT NULL,
  `datetime_interaction` datetime NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `duration` int(11) NOT NULL,
  `description` varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  `fk_pk_interaction_type_id` int(11) NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  `is_private` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_interaction_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `tb_interaction_comment`
--

CREATE TABLE IF NOT EXISTS `tb_interaction_comment` (
  `pk_interaction_comment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `fk_pk_interaction_id` bigint(20) NOT NULL,
  `fk_pk_user_id` bigint(20) NOT NULL,
  `is_like` int(11) NOT NULL,
  `comment_text` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_interaction_comment_id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `tb_interaction_type`
--

CREATE TABLE IF NOT EXISTS `tb_interaction_type` (
  `pk_interaction_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `fk_pk_parent_interaction_type_id` int(11) DEFAULT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_interaction_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `tb_user`
--

CREATE TABLE IF NOT EXISTS `tb_user` (
  `pk_user_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `google_id` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `lname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `img_url` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `datetime_created` datetime NOT NULL,
  `datetime_updated` datetime NOT NULL,
  `is_active` int(11) NOT NULL DEFAULT '1',
  `is_deleted` int(11) NOT NULL DEFAULT '0',
  `is_admin` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pk_user_id`)
);
