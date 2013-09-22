-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 04, 2008 at 01:19 PM
-- Server version: 5.0.51
-- PHP Version: 5.2.6
-- 
-- Database: `fun`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_comments`
-- 

CREATE TABLE `fun_comments` (
  `comment_id` mediumint(8) unsigned NOT NULL auto_increment,
  `comment_user_id` mediumint(8) unsigned NOT NULL,
  `comment_obj_id` mediumint(8) unsigned NOT NULL,
  `comment_obj_type_id` mediumint(8) unsigned NOT NULL,
  `comment_title` varchar(10) NOT NULL default '',
  `comment_contents` text NOT NULL,
  `comment_date` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`comment_id`),
  KEY `comment_obj_id` USING BTREE (`comment_obj_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_config`
-- 

CREATE TABLE `fun_config` (
  `cfg_key` varchar(50) NOT NULL default '',
  `cfg_val` varchar(255) default NULL,
  PRIMARY KEY  (`cfg_key`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_favourites`
-- 

CREATE TABLE `fun_favourites` (
  `fav_id` int(10) unsigned NOT NULL auto_increment,
  `fav_user_id` int(10) unsigned NOT NULL,
  `fav_object_id` int(10) unsigned NOT NULL,
  `fav_object_type_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`fav_id`),
  KEY `fav_user_id` USING BTREE (`fav_user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fun_favourites`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fun_lang`
-- 

CREATE TABLE `fun_lang` (
  `lang_id` tinyint(3) unsigned NOT NULL auto_increment,
  `lang_abbrev` char(2) NOT NULL default '',
  `lang_name_en` varchar(20) NOT NULL default '',
  `lang_name_local` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`lang_id`),
  UNIQUE KEY `lang_abbrev` (`lang_abbrev`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fun_lang`
-- 

INSERT INTO `fun_lang` (`lang_id`, `lang_abbrev`, `lang_name_en`, `lang_name_local`) VALUES (1, 'en', '', '');

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_lang_data`
-- 

CREATE TABLE `fun_lang_data` (
  `data_id` mediumint(8) unsigned NOT NULL auto_increment,
  `data_lang_id` tinyint(3) unsigned NOT NULL default '0',
  `data_key` varchar(50) default NULL,
  `data_val` tinytext NOT NULL,
  PRIMARY KEY  (`data_id`),
  KEY `data_lang_id` (`data_lang_id`)
) ENGINE=INNODB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8 AUTO_INCREMENT=97 ;

-- 
-- Dumping data for table `fun_lang_data`
-- 

INSERT INTO `fun_lang_data` (`data_id`, `data_lang_id`, `data_key`, `data_val`) VALUES (3, 1, 'form_err_minlen_user_login', 'Username is minimum %2$s characters long.'),
(2, 1, 'form_err_login', 'Invalid username or password'),
(4, 1, 'form_err_maxlen_user_login', 'Username is maximum %2$s characters long.'),
(5, 1, 'form_err_minlen_user_pass', 'Password is minimum %2$s characters long.'),
(6, 1, 'form_err_maxlen_user_pass', 'Password is maximum %2$s characters long.'),
(10, 1, 'form_err_minlen_user_passcheck', 'Confirmed password is minimum %2$s characters long.'),
(9, 1, 'form_err_maxlen_user_passcheck', 'Confirmed password is maximum %2$s characters long.'),
(11, 1, 'form_err_mail_user_email', 'Entered email is not valid.'),
(12, 1, 'form_err_exists_user_login', 'User is already registered.'),
(13, 1, 'form_err_exists_user_email', 'User with such email already exist.'),
(14, 1, 'form_err_code_user_security', 'Entered security code doesn''t match.'),
(15, 1, 'form_err_equals_user_passcheck', 'Confirmed password doesn''t match first entered.'),
(16, 1, 'form_err_required_user_email', 'Email not entered.'),
(17, 1, 'form_err_required_user_login', 'Username not entered.'),
(18, 1, 'form_err_required_user_pass', 'Password not entered.'),
(19, 1, 'form_err_required_user_passcheck', 'Confirmed password not entered.'),
(20, 1, 'form_err_required_user_security', 'Security code not entered.'),
(21, 1, 'form_err_existsnot_user_email', 'User with such email doesn''t exist.'),
(22, 1, 'form_err_existsnot_user_login', 'User doesn''t exist.'),
(23, 1, 'form_err_minlen_user_first_name', 'User First name is minimum %2$s characters long.'),
(24, 1, 'form_err_maxlen_user_first_name', 'User First name is maximum %2$s characters long.'),
(25, 1, 'form_err_maxlen_user_last_name', 'User Last name is maximum %2$s characters long.'),
(26, 1, 'form_err_minlen_user_last_name', 'User Last name is minimum %2$s characters long.'),
(27, 1, 'form_err_file_user_avatar', 'An error occured during uploading avatar.'),
(28, 1, 'form_err_lostpass', 'There is a problem with recovering your password.'),
(29, 1, 'form_err_register', 'Registration did not complete successfully.'),
(30, 1, 'form_label_user_login', 'Username'),
(31, 1, 'form_label_user_pass', 'Password'),
(32, 1, 'form_label_user_passcheck', 'Password check'),
(33, 1, 'form_label_user_email', 'Email'),
(34, 1, 'form_label_user_security', 'Security code'),
(35, 1, 'form_label_user_first_name', 'First name'),
(36, 1, 'form_label_user_last_name', 'Last name'),
(37, 1, 'form_label_user_sex', 'Sex'),
(38, 1, 'form_label_user_interests', 'Interests'),
(39, 1, 'form_label_user_avatar', 'Avatar'),
(40, 1, 'form_label_user_birth_date_day', 'Day'),
(41, 1, 'form_label_user_birth_date_month', 'Month'),
(42, 1, 'form_label_user_birth_date_year', 'Year'),
(43, 1, 'form_label_remember', 'Remember me'),
(45, 1, 'text_security_image', 'Security image'),
(46, 1, 'button_update_profile', 'Update'),
(47, 1, 'button_login', 'Login'),
(48, 1, 'button_submit', 'Submit'),
(49, 1, 'button_confirm', 'Confirm'),
(50, 1, 'button_register', 'Register'),
(51, 1, 'text_undefined', 'Undefined'),
(52, 1, 'text_male', 'Male'),
(53, 1, 'text_female', 'Female'),
(54, 1, 'menu_edit_profile', 'Edit profile'),
(55, 1, 'menu_forget_password', 'Forgotten password'),
(56, 1, 'menu_register', 'Register'),
(57, 1, 'menu_delete_avatar', 'Delete avatar'),
(58, 1, 'menu_profile', 'Profile'),
(59, 1, 'menu_logout', 'Logout'),
(60, 1, 'menu_search', 'Search'),
(61, 1, 'month_january', 'January'),
(62, 1, 'month_february', 'February'),
(63, 1, 'month_march', 'March'),
(64, 1, 'month_april', 'April'),
(65, 1, 'month_may', 'May'),
(66, 1, 'month_june', 'June'),
(67, 1, 'month_july', 'July'),
(68, 1, 'month_august', 'August'),
(69, 1, 'month_september', 'September'),
(70, 1, 'month_october', 'October'),
(71, 1, 'month_november', 'November'),
(72, 1, 'month_december', 'December'),
(73, 1, 'combo_none', 'None'),
(74, 1, 'menu_my_presentations', 'My presentations'),
(75, 1, 'menu_favourites', 'View favourites'),
(76, 1, 'no_presentations', 'No presentations found'),
(77, 1, 'comment', 'Comment this presentation'),
(78, 1, 'comments', 'Comments'),
(79, 1, 'form_err_required_ppt_file', 'Enter powerpoint presentation'),
(80, 1, 'form_err_required_ppt_title', 'Enter powerpoint presentation title'),
(81, 1, 'form_err_required_ppt_description', 'Enter powerpoint presentation description'),
(82, 1, 'form_err_required_ppt_tags', 'Enter powerpoint presentation tags'),
(83, 1, 'msg_reg_successful', 'Thank for registering!Please check your mail to activate your account'),
(84, 1, 'msg_lost_password', 'You have forgotten your password.Please check your email to get a new one'),
(85, 1, 'msg_new_password', 'You have successfully changed your password.'),
(86, 1, 'msg_logged_in', 'You have successfully logged in.'),
(87, 1, 'msg_uploaded_ppt', 'You have successfully uploaded your presentation.'),
(88, 1, 'form_share', 'Share this presentation'),
(89, 1, 'form_label_from', 'From'),
(90, 1, 'form_label_to', 'To(email)'),
(91, 1, 'form_label_comment', 'Comment'),
(92, 1, 'form_err_required_share_from', 'Please enter value for from field'),
(93, 1, 'form_err_required_share_to', 'Please enter value for to field'),
(94, 1, 'menu_most_popular', 'Most popular'),
(96, 1, 'menu_avatar_format', '.jpg or .png format');

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_object_type_ids`
-- 

CREATE TABLE `fun_object_type_ids` (
  `obj_type_id` mediumint(8) unsigned NOT NULL auto_increment,
  `obj_type_name` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`obj_type_id`),
  UNIQUE KEY `obj_type_name` (`obj_type_name`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fun_object_type_ids`
-- 

INSERT INTO `fun_object_type_ids` (`obj_type_id`, `obj_type_name`) VALUES
(2, 'ppt'),
(4, 'user');

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_ppts`
-- 

CREATE TABLE `fun_ppts` (
  `ppt_id` mediumint(8) unsigned NOT NULL auto_increment,
  `ppt_user_id` mediumint(8) unsigned NOT NULL,
  `ppt_title` varchar(255) NOT NULL default '',
  `ppt_description` text NOT NULL,
  `ppt_file` varchar(4) NOT NULL default '',
  `ppt_time` int(10) unsigned NOT NULL default '0',
  `ppt_views` mediumint(8) unsigned default '0',
  `ppt_pic` varchar(4) NOT NULL,
  `ppt_downloads` int(10) unsigned NOT NULL,
  `ppt_converted` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `ppt_content_type` varchar(75) NOT NULL DEFAULT '',
  `ppt_content_name` varchar(255) NOT NULL DEFAULT '',
  `ppt_slides_count` int(4) unsigned NOT NULL default 0,
  PRIMARY KEY  (`ppt_id`),
  KEY `ppt_user_id` USING BTREE (`ppt_user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `fun_ratings`
-- 

CREATE TABLE `fun_ratings` (
  `rating_id` mediumint(8) unsigned NOT NULL auto_increment,
  `rating_user_id` mediumint(8) unsigned NOT NULL,
  `rating_obj_id` mediumint(8) unsigned NOT NULL,
  `rating_obj_type_id` mediumint(8) unsigned NOT NULL,
  `rating_value` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY  (`rating_id`),
  UNIQUE KEY `rating_user_id` (`rating_user_id`,`rating_obj_id`,`rating_obj_type_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 
-- Dumping data for table `fun_ratings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `fun_sessions`
-- 

CREATE TABLE `fun_sessions` (
  `sess_id` char(32) NOT NULL default '',
  `sess_time` int(11) unsigned NOT NULL default '0',
  `sess_admin` tinyint(1) unsigned NOT NULL default '0',
  `sess_vars` varchar(255) NOT NULL default '',
  `sess_user_id` mediumint(8) unsigned NOT NULL default '0',
  `sess_ip` varchar(40) NOT NULL default '',
  `sess_user_agent` varchar(150) NOT NULL default '',
  `sess_page` varchar(30) NOT NULL default '',
  `sess_page_params` varchar(255) NOT NULL default '',
  `sess_view_online` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`sess_id`),
  KEY `sess_time` (`sess_time`),
  KEY `sess_user_id` (`sess_user_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `fun_tags`
-- 

CREATE TABLE `fun_tags` (
  `tag_id` mediumint(8) unsigned NOT NULL auto_increment,
  `tag_word` varchar(50) NOT NULL default '',
  `tag_count` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`tag_id`),
  KEY `tag_word` (`tag_word`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

-- 
-- Table structure for table `fun_tags_refs`
-- 

CREATE TABLE `fun_tags_refs` (
  `ref_id` mediumint(8) unsigned NOT NULL auto_increment,
  `ref_word_id` mediumint(8) unsigned NOT NULL,
  `ref_object_id` mediumint(8) unsigned NOT NULL,
  `ref_object_type_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`ref_id`),
  UNIQUE KEY `ref_word_id` (`ref_word_id`,`ref_object_id`,`ref_object_type_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

-- 
-- Table structure for table `fun_users`
-- 

CREATE TABLE `fun_users` (
  `user_id` mediumint(8) unsigned NOT NULL auto_increment,
  `user_login` varchar(50) NOT NULL default '',
  `user_pass_hash` char(32) NOT NULL default '',
  `user_auto_hash` char(32) NOT NULL default '',
  `user_active` tinyint(1) unsigned NOT NULL default '0',
  `user_reg_time` int(11) unsigned NOT NULL default '0',
  `user_reg_ip` varchar(40) NOT NULL default '',
  `user_admin` tinyint(1) unsigned NOT NULL default '0',
  `user_first_name` varchar(10) default NULL,
  `user_last_name` varchar(10) default NULL,
  `user_email` varchar(100) default NULL,
  `user_birth_date` int(10) unsigned NOT NULL default '0',
  `user_sex` tinyint(2) default NULL,
  `user_interests` text,
  `user_avatar` varchar(5) default NULL,
  PRIMARY KEY  (`user_id`),
  UNIQUE KEY `user_login` (`user_login`),
  UNIQUE KEY `user_email` (`user_email`),
  KEY `user_active` (`user_active`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

