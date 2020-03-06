-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 21, 2019 at 04:53 PM
-- Server version: 10.4.8-MariaDB
-- PHP Version: 7.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dino_local`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `code` text DEFAULT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ads`
--

INSERT INTO `ads` (`id`, `type`, `code`, `active`) VALUES
(1, 'header', NULL, '0'),
(2, 'footer', NULL, '0'),
(3, 'sidebar', NULL, '0'),
(4, 'home_latest_news', NULL, '0'),
(5, 'home_latest_lists', NULL, '0'),
(6, 'home_latest_videos', NULL, '0'),
(7, 'home_latest_music', NULL, '0'),
(8, 'between', NULL, '0');

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `text` text DEFAULT NULL,
  `time` int(32) NOT NULL DEFAULT 0,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `announcement_views`
--

CREATE TABLE `announcement_views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `announcement_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `banned_ip`
--

CREATE TABLE `banned_ip` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(32) NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `breaking_news`
--

CREATE TABLE `breaking_news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `expire` varchar(50) NOT NULL DEFAULT '0',
  `url` varchar(3000) NOT NULL DEFAULT '',
  `text` varchar(1000) NOT NULL DEFAULT '',
  `active` enum('0','1') NOT NULL DEFAULT '0',
  `time` varchar(50) NOT NULL DEFAULT '0',
  `posted` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `news_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `likes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `dislikes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `time` varchar(30) NOT NULL DEFAULT '0',
  `page` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `news_id`, `text`, `likes`, `dislikes`, `time`, `page`) VALUES
(1, 1, 26, 'This is a comment', 'a:0:{}', 'a:0:{}', '1544805735', 'news');

-- --------------------------------------------------------

--
-- Table structure for table `comm_replies`
--

CREATE TABLE `comm_replies` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `news_id` int(11) NOT NULL DEFAULT 0,
  `comment` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `likes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `dislikes` varchar(10000) NOT NULL DEFAULT 'a:0:{}',
  `time` varchar(30) NOT NULL DEFAULT '0',
  `page` varchar(10) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

CREATE TABLE `config` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`id`, `name`, `value`) VALUES
(1, 'name', 'Yozax'),
(2, 'theme', 'default'),
(3, 'title', 'Yozax'),
(4, 'validation', '2'),
(5, 'registration', '1'),
(6, 'language', 'english'),
(7, 'smtp_or_mail', 'smtp'),
(8, 'smtp_host', 'smtp.mailgun.org'),
(9, 'smtp_username', 'postmaster@mg.jmbls.com'),
(10, 'smtp_password', '1e4ea5a70e04a0f678cb898e18e77514-bd350f28-695b5e49'),
(11, 'smtp_encryption', 'tls'),
(12, 'smtp_port', '587'),
(13, 'email', 'roar@thedumbestdinosaur.com'),
(14, 'reCaptcha', '2'),
(15, 'google_app_ID', ''),
(16, 'google_app_key', ''),
(17, 'facebook_app_ID', 'asdasd'),
(18, 'facebook_app_key', 'asd'),
(19, 'twitter_app_ID', ''),
(20, 'twitter_app_key', ''),
(21, 'maintenance', '1'),
(22, 'delete_account', '1'),
(23, 'keywords', 'Yozax,viral,media,social media,videos,content'),
(24, 'description', 'All the things you love, all in one place'),
(26, 'censored', ''),
(27, 'reCaptcha_key', ''),
(28, 'analytics', ''),
(29, 'upload', '1000000000'),
(33, 'facebook', '1'),
(34, 'twitter', '1'),
(35, 'google', '1'),
(36, 'fb_page', 'https://www.facebook.com/'),
(42, 'vkontakte_app_key', ''),
(43, 'vkontakte', '1'),
(44, 'vkontakte_app_ID', ''),
(45, 'vkontakte_app_key', ''),
(46, 'rss_feed', ''),
(47, 'rss_feed_limit', '10'),
(48, 'theme', 'default'),
(49, 'logo_extension', 'png'),
(50, 'icon_extension', 'png'),
(51, 'logo', 'themes/default/img/logo.png'),
(52, 'favicon', 'themes/default/img/icon.png'),
(53, 'news', '1'),
(54, 'lists', '1'),
(55, 'polls', '1'),
(56, 'music', '1'),
(57, 'quizzes', '1'),
(58, 'videos', '1'),
(59, 'last_backup', '17-12-2018'),
(60, 'can_post', '1'),
(61, 'header_ccx', '/* Add here your JavaScript Code. Note. the code entered here will be added in <head> tag Example: var x, y, z; x = 5; y = 6; z = x + y; */'),
(62, 'footer_ccx', ' /* The code entered here will be added in <footer> tag */'),
(63, 'styles_ccx', '/* Add here your custom css styles Example: p { text-align: center; color: red; } */ '),
(64, 'amazone_s3', '0'),
(65, 'bucket_name', ''),
(66, 'amazone_s3_key', ''),
(67, 'amazone_s3_s_key', ''),
(68, 'region', ''),
(69, 'apps_api_id', '1ffa3c7d5195d13dc00e88d9ed68336d'),
(70, 'comment_system', 'default'),
(71, 'apps_api_key', '500e47f2fdc2b9ee3260e4d49e3ebe94'),
(72, 'google_code', ''),
(73, 'review_posts', '0'),
(74, 'pro_pkg_price', '23'),
(75, 'go_pro', '1'),
(76, 'user_max_posts', '10'),
(77, 'paypal_mode', 'live'),
(78, 'paypal_id', 'ASFxDwZsRC8s2CLL6TY7KRy8md_IFoEDcCE7lRyBvEOS2va2uk9kYCVFV_6tS0AY2Qz9arH67JoPTjc0'),
(79, 'paypal_secret', 'EKku-64-c73_cpYW0mW_sztqUkt0QPwKdbc7zIi7HK7iQ3AYoidnsncI45a-Mu6fXYzYpXtCADLdI3GM'),
(80, 'ad_c_price', '0.4'),
(81, 'usr_ads', '1'),
(82, 'show_subscribe_box', '0'),
(83, 'subscribe_box_username', 'thedumbestdinosaur'),
(84, 'stripe_publisher_key', 'pk_test_rGLP0fyX98wMgL2gc2KejfMA00qDHvighE'),
(85, 'stripe_secret_key', 'sk_test_n0yDSEP6cnBnXmDzYqiZgRC400Z8Ud5ywL');

-- --------------------------------------------------------

--
-- Table structure for table `entries`
--

CREATE TABLE `entries` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `index_id` int(11) NOT NULL DEFAULT 0,
  `entry_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `text` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `image` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `facebook_post` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `tweet` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `tweet_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `soundcloud_id` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `instagram` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `title` text CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `video` varchar(120) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `video_type` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `video_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `entry_page` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'news',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE `follow` (
  `id` int(11) NOT NULL,
  `user_id` int(191) NOT NULL,
  `relation_arr` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `follow`
--

INSERT INTO `follow` (`id`, `user_id`, `relation_arr`) VALUES
(3, 4, 'a:1:{i:0;s:1:\"5\";}'),
(4, 5, 'a:1:{i:0;s:1:\"4\";}');

-- --------------------------------------------------------

--
-- Table structure for table `lists`
--

CREATE TABLE `lists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `from_id` int(191) NOT NULL,
  `to_id` int(191) NOT NULL,
  `msg` longtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `from_id`, `to_id`, `msg`, `date`) VALUES
(4, 5, 4, 'hello', '2019-11-12 11:37:28'),
(5, 4, 5, 'test response', '2019-11-12 11:37:45');

-- --------------------------------------------------------

--
-- Table structure for table `music`
--

CREATE TABLE `music` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `type` varchar(200) NOT NULL DEFAULT '',
  `amount` int(11) NOT NULL DEFAULT 0,
  `date` varchar(100) NOT NULL DEFAULT '',
  `expire` varchar(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `type`, `amount`, `date`, `expire`) VALUES
(1, 1, 'pro', 23, '12/2018', '1547401315'),
(2, 4, 'pro', 23, '11/2019', '1576916316');

-- --------------------------------------------------------

--
-- Table structure for table `polls`
--

CREATE TABLE `polls` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `text` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `image` varchar(120) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0,
  `type` varchar(10) CHARACTER SET utf8 NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poll_pages`
--

CREATE TABLE `poll_pages` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(11) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profile_fields`
--

CREATE TABLE `profile_fields` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `length` int(11) NOT NULL DEFAULT 0,
  `placement` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'profile',
  `registration_page` int(11) NOT NULL DEFAULT 0,
  `profile_page` int(11) NOT NULL DEFAULT 0,
  `select_type` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'none',
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_answers`
--

CREATE TABLE `quiz_answers` (
  `id` int(11) NOT NULL,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `result_index` int(11) NOT NULL DEFAULT 0,
  `text` varchar(3000) NOT NULL DEFAULT '',
  `image` varchar(5000) NOT NULL DEFAULT '',
  `time` varchar(50) NOT NULL DEFAULT '0',
  `type` varchar(30) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `profile_id` int(11) NOT NULL DEFAULT 0,
  `page_id` int(15) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `text` text DEFAULT NULL,
  `type` varchar(30) NOT NULL DEFAULT '',
  `seen` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `session_id` varchar(140) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `platform` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'web',
  `platform_info` varchar(500) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `session_id`, `platform`, `time`) VALUES
(16, 1, '2f010786710817bd82b5a2ce260209adacc1100e2c78670b703a1effd6d9322f0cdcaea773918687cebd648f9146a6345d604ab093b02c73', 'web', 1545182865),
(8, 1, '8c9d7a4a1e4213a167143ad0ff3f36b99199ce7c10b119d69aa437c4bc3b4d94675bd371497271965d3145e1226fd39ee3b3039bfa90c95d', 'web', 1545011157),
(9, 1, '7807ea46f4ee4108768be08a06022898ebafbcc7483a54b744499127ad9d3e236eeb2bce25778831eca336bc46296c1aced239fbfb803b5c', 'web', 1545057920),
(10, 1, '67c2ebd71958eb4f17b5dc87fb58b98729f1f568eb3f8a4aaa854be78361fe4eea6f7b02192686586933b5648c59d618bbb30986c84080fe', 'web', 1545071824),
(11, 1, '58ab1868ac980cf8e8559b3adec02591e4f4da687af1d03c62471ed5d2d95655cfd8f9da1731075397d0e0329055e6ddaaaf2335a2509231', 'web', 1545079162),
(12, 1, '63d5c47587adb9478e7a3668563ca09ca75a4c05717f3a3bed9125316ff9ee8290509e4435275472586ee5cb5f17541372cdd7d54b6414d7', 'web', 1545079827),
(13, 1, '267f9b77b392d557ce0d7178a32ecce1b75029b9490e0041b75134ba982ed8a8cdea89f631142066ac10ec1ace51b2d973cd87973a98d3ab', 'web', 1545079923),
(15, 1, 'abf3f1e953eb761cf55934de465d165d3e0e23b7f947073c613c8b183ceff593e5bd14f313930714ac53fab47b547a0d47b77e424cf119ba', 'web', 1545100929),
(19, 4, '7b1f9f5adf8b6d393cd2183a065f1145b00f22368f6b4f215e8d894af02035f86f42998b574891999a0684d9dad4967ddd09594511de2c52', 'web', 1573742516),
(18, 5, 'ecb45ccf8b39cf3c326d61772da1a334be5d98491bbf83fe70b6c57b6d4528fc1ce7dd4c65137057dc116c922217ede2210c57237bd1c1ee', 'web', 1573558511),
(20, 4, '9b4e49f9e226defe5638e3826ca9bfc42aaa524c78724d779345616a73a002f34b448962530222906de59d960d3bb8a6346c058930f3cd28', 'web', 1573743239);

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

CREATE TABLE `terms` (
  `id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL DEFAULT '',
  `text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `terms`
--

INSERT INTO `terms` (`id`, `type`, `text`) VALUES
(1, 'terms_of_use', '<h4>Meet Yozax</h4>    \nYozax (written as TDD, “us” or “we”) and its associates provide their services to you subject to the following conditions. Please read them carefully.\n<br><br>\n<h4>Electronic Communications</h4>\nWhen you use our site or services or send e-mails to us, you are communicating with us electronically. You consent to receive communications from us electronically. We will communicate with you by e-mail or by posting notices on this site. You agree that all agreements, notices, disclosures and other communications that we provide to you electronically satisfy any legal requirement that such communications be in writing.\n<br><br>\n<h4>Copyright</h4>\nAll content included on this site, such as text, graphics, logos, button icons, images, audio clips, digital downloads, data compilations, and software, is the property of TDD or its content suppliers and protected by international copyright laws. The compilation of all content on this site is the exclusive property of TDD, with copyright authorship for this collection by TDD, and protected by international copyright laws.\n<br>\nNOTE: This does NOT include “USER GENERATED CONTENT” and such content is protected under the section REVIEWS, COMMENTS, EMAILS, AND OTHER CONTENT listed below.\n<br><br>\n<h4>Trademarks</h4>\nTDD\'s trademarks and trade dress may not be used in connection with any product or service that is not TDD\'s, in any manner that is likely to cause confusion among customers, or in any manner that disparages or discredits TDD. All other trademarks not owned by TDD or its subsidiaries that appear on this site are the property of their respective owners, who may or may not be affiliated with, connected to, or sponsored by TDD or its subsidiaries.\n<br><br>\n<h4>License and Site Access</h4>\nTDD grants you a limited license to access and make personal use of this site and not to download (other than page caching) or modify it, or any portion of it, except with express written consent of TDD. This license does not include any resale or commercial use of this site or its contents: any collection and use of any product listings, descriptions, or prices: any derivative use of this site or its contents: any downloading or copying of account information for the benefit of another merchant: or any use of data mining, robots, or similar data gathering and extraction tools. This site or any portion of this site may not be reproduced, duplicated, copied, sold, resold, visited, or otherwise exploited for any commercial purpose without express written consent of TDD. You may not frame or utilize framing techniques to enclose any trademark, logo, or other proprietary information (including images, text, page layout, or form) of TDD and our associates without express written consent. You may not use any meta tags or any other \"hidden text\" utilizing TDD\'s name or trademarks without the express written consent of TDD. Any unauthorized use terminates the permission or license granted by TDD. You are granted a limited, revocable, and nonexclusive right to create a hyperlink to the home page of TDD so long as the link does not portray TDD, its associates, or their products or services in a false, misleading, derogatory, or otherwise offensive matter. You may not use any TDD logo or other proprietary graphic or trademark as part of the link without express written permission. Users may display a hyperlink to their user profiles.\n<br><br>\n<h4>Your Membership Account</h4>\nIf you use this site, you are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer, and you agree to accept responsibility for all activities that occur under your account or password. If you are under 18, you may use our website only with involvement of a parent or guardian. TDD and its associates reserve the right to refuse service, terminate accounts, remove or edit content, or cancel orders in their sole discretion. You must be 18 or over to purchase the ‘Pro Package Subscription’ or have a parent or guardian purchase the subscription for you.\n<br><br>\n<h4>Reviews, Comments, Emails and Other Content</h4>\nVisitors may post reviews, comments, and other content: and submit suggestions, ideas, comments, questions, or other information, so long as the content is not illegal, obscene, threatening, defamatory, invasive of privacy, infringing of intellectual property rights, or otherwise injurious to third parties or objectionable and does not consist of or contain software viruses, political campaigning, commercial solicitation, chain letters, mass mailings, or any form of \"spam.\" You may not use a false e-mail address, impersonate any person or entity, or otherwise mislead as to the origin of a card or other content. TDD reserves the right (but not the obligation) to remove or edit such content, but does not regularly review posted content. If you do post content or submit material, and unless we indicate otherwise, you grant TDD and its associates a nonexclusive, royalty-free, perpetual, irrevocable, and fully sublicensable right to use, reproduce, modify, adapt, publish, translate, create derivative works from, distribute, and display such content throughout the world in any media. You grant TDD and its associates and sublicensees the right to use the name that you submit in connection with such content, if they choose. You represent and warrant that you own or otherwise control all of the rights to the content that you post: that the content is accurate: that use of the content you supply does not violate this policy and will not cause injury to any person or entity: and that you will indemnify TDD or its associates for all claims resulting from content you supply. TDD has the right but not the obligation to monitor and edit or remove any activity or content. TDD takes no responsibility and assumes no liability for any content posted by you or any third party. You can ‘report’ content for a site moderator to review at any time under the post functions menu.\n<br><br>\n<h4>Risk of Loss</h4>\nAll items purchased from our upgrades page AND/OR the TDD marketplace are made pursuant to a shipment contract. This basically means that the risk of loss and title for such items pass to you upon our delivery to the carrier. If we do not handle the goods (such as in the classified marketplace) all liability lies with the seller and we will do our best to provide a safe and secure platform.\n<br><br>\n<h4>Product Descriptions</h4>\nTDD and its associates attempt to be as accurate as possible. However, TDD does not warrant that product descriptions or other content of this site is accurate, complete, reliable, current, or error-free. If a product offered by TDD itself is not as described, your sole remedy is to return it in unused condition.\n<br><br>\n<h4>Disclaimer of Warranties and Limitation of Liability</h4>\nTHIS SITE IS PROVIDED BY TDD ON AN \"AS IS\" AND \"AS AVAILABLE\" BASIS. TDD MAKES NO REPRESENTATIONS OR WARRANTIES OF ANY KIND, EXPRESS OR IMPLIED, AS TO THE OPERATION OF THIS SITE OR THE INFORMATION, CONTENT, MATERIALS, OR PRODUCTS INCLUDED ON THIS SITE. YOU EXPRESSLY AGREE THAT YOUR USE OF THIS SITE IS AT YOUR SOLE RISK. TO THE FULL EXTENT PERMISSIBLE BY APPLICABLE LAW, TDD DISCLAIMS ALL WARRANTIES, EXPRESS OR IMPLIED, INCLUDING, BUT NOT LIMITED TO, IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE. TDD DOES NOT WARRANT THAT THIS SITE, ITS SERVERS, OR E-MAIL SENT FROM TDD ARE FREE OF VIRUSES OR OTHER HARMFUL COMPONENTS. TDD WILL NOT BE LIABLE FOR ANY DAMAGES OF ANY KIND ARISING FROM THE USE OF THIS SITE, INCLUDING, BUT NOT LIMITED TO DIRECT, INDIRECT, INCIDENTAL, PUNITIVE, AND CONSEQUENTIAL DAMAGES. CERTAIN STATE LAWS DO NOT ALLOW LIMITATIONS ON IMPLIED WARRANTIES OR THE EXCLUSION OR LIMITATION OF CERTAIN DAMAGES. IF THESE LAWS APPLY TO YOU, SOME OR ALL OF THE ABOVE DISCLAIMERS, EXCLUSIONS, OR LIMITATIONS MAY NOT APPLY TO YOU, AND YOU MIGHT HAVE ADDITIONAL RIGHTS.\n<br><br>\n<h4>Applicable Law</h4>\nBy visiting TDD, you agree that the laws of the United Kingdom, without regard to principles of conflict of laws, will govern these Conditions of Use and any dispute of any sort that might arise between you and TDD or its associates.\n<br><br>\n<h4>Disputes</h4>\nAny dispute relating in any way to your visit to TDD or to products you purchase through TDD shall be submitted to confidential arbitration in the courts within the United Kingdom, except that, to the extent you have in any manner violated or threatened to violate TDD\'s intellectual property rights, TDD may seek injunctive or other appropriate relief in any court within the jurisdiction of the United Kingdom, and you consent to exclusive jurisdiction and venue in such courts. The arbitrators award shall be binding and may be entered as a judgment in any court of competent jurisdiction. To the fullest extent permitted by applicable law, no arbitration under this Agreement shall be joined to an arbitration involving any other party subject to this Agreement, whether through class arbitration proceedings or otherwise. We may initiate or defend ourselves from international legal action through one of our subsidiaries or legal partners at our discretion.\n<br><br>\n<h4>Site Policies, Modification and Severability</h4>\nPlease review our other policies, such as our Shipping and Returns policy, posted on this site. These policies also govern your visit to TDD. We reserve the right to make changes to our site, policies, and these Conditions of Use at any time. If any of these conditions shall be deemed invalid, void, or for any reason unenforceable, that condition shall be deemed severable and shall not affect the validity and enforceability of any remaining condition.\n<br><br>\n<h4>Questions</h4>\nQuestions regarding our Conditions of Usage, Privacy Policy, or other policy related material can be directed to our support staff by messaging a verified member of staff. or you can email us at: Roar@TheDumbestDinosaur.com and we will get back to you within 24hours.'),
(2, 'privacy_policy', 'Any information that is collected via our Services is covered by the Privacy Policy in effect at the time such information is collected. We may revise this Privacy Policy from time to time without prior notice.\n<br>\nWe may collect Personal Information from you such as email address, phone number or mailing address when you choose to request information about our Services, register for Yozax newsletter or a program that we may offer from time to time, request to receive customer or technical support, or otherwise communicate with us.\n<br>\nIn some cases we may collect and store information about where you are located, such as by converting your IP address into a rough geolocation or by accessing your mobile devices GPS coordinates or coarse location if you enable location services on your device. We may use location information to improve and personalize our Services for you or to secure your account from external threats.\n<br>\nIf you choose to supply additional information on your profile (such as your high school or place of work) we of course will also have access to that information.\n<br>\nWe will not share any Personal Information that we have collected from or regarding you with any third-party company or business partners. \n<br><br>\n<h4>Modifying Your Information</h4>\nYou can access and modify the Personal Information associated with your Account through your Account settings or by contacting us at roar@thedumbestdinosaur.com.\n<br>\nIf you want us to delete your Personal Information and your Account, you can do so by navigating to contacting us at roar@thedumbestdinosaur.com with your request. \n<br>\nWe’ll take steps to delete your information as soon we can, but some information may remain in archived/backup copies for our records (such as financial transactions) or as otherwise required by law. \n<br><br>\n<h4>Rights of access, Rectification, Erasure and Restriction</h4>\nYou may inquire as to whether Yozax is Processing Personal Information about you, request access to Personal Information, and ask that we correct, amend or delete your Personal Information where it is inaccurate. Where otherwise permitted by applicable law, you may contact us at roar@thedumbestdinosaur.com to request access to, receive (port), seek rectification, or request erasure of Personal Information held about you by us. Please include your full name, email address associated with your Account, and a detailed description of your data request. Such requests will be processed in line with local laws. \n<br><br>\nAlthough Yozax makes good faith efforts to provide Individuals with access to their Personal Information, there may be circumstances in which we are unable to provide access, including but not limited to: where the information contains legal privilege, would compromise others privacy or other legitimate rights, where the burden or expense of providing access would be disproportionate to the risks to the Individual’s privacy in the case in question or where it is commercially proprietary. If we determine that access should be restricted in any particular instance, we will provide you with an explanation of why that determination has been made and a contact point for any further inquiries. To protect your privacy, we will take commercially reasonable steps to verify your identity before granting access to or making any changes to your Personal Information.\n<br><br>\n<h4>Protection of your Information</h4>\nWe take reasonable administrative, physical and electronic measures designed to protect the information that we collect from or about you (including your Personal Information) from unauthorized access, use or disclosure. When you enter sensitive information on our forms, we encrypt this data using SSL or other technologies. Please be aware, however, that no method of transmitting information over the Internet or storing information is completely secure. Accordingly, we cannot guarantee the absolute security of any information. We do not accept liability for unintentional disclosure.\n<br><br>\nBy using our services or otherwise providing Personal Information to us, you agree that we may communicate with you electronically regarding security, privacy, and administrative issues relating to your use of the Site. If we learn of a security system’s breach, we may attempt to notify you electronically by posting a notice on the Site or sending an e-mail to you. You may have a legal right to receive this notice in writing (and agree that contacting you via e-mail satisifes the legal definition of \'in writing\'. \n<br><br>\n<h4>Our Policy Towards Children</h4>\nOur Services are not directed to children under 13 and we do not knowingly collect Personal Information from children under 13. If we learn that we have collected Personal Information of a child under 13 we will take steps to delete such information from our files as soon as possible. If you are under the age of 18, you must have your parent’s permission to access the Services. \n<br><br>\n<h4>Questions?</h4>\nPlease contact us at roar@thedumbestdinosaur.com if you have any questions about our practices or this Terms of Use and/or Privacy Policy. \nAlternatively, you can DM a member of staff.\n'),
(3, 'about', '<p><strong>What and who exactly is Yozax?</strong></p>\n\n<p>Yozax is a global social media and social networking company. </p>\n\n<p>The name Yozax comes from the founder - Connor Gibson\'s personality. It was important to build a company thats different, disruptive and to be honest, one that just steps away from the unapproachable and closed off online businesses of today.</p>\n\n<p>Yozax is a follower based social networking service with Movies, Music, Location based events and polls, lists, quizzes, a community forum, File manager, Photo albums and an e-wallet system thrown into the mix. We want to create an environment where your whole library of online entertainment can be found in one single place. </p>\n\n<p>It’s worth noting all the above features are aimed at being fully integrated into the social networking site itself. The actors in the movies you watch can setup public profiles linked to the credits, you can mention a friend in the comments or ask friends for recommendations. You can send your friends money through the wallet in one click or ask to be paid back for the drink you bought last weekend. The free file manager allows you to quickly collaborate and share files up-to 5GB in size straight from within the chat or on your timeline with no storage limits or useless caps...</p>\n\n<p>You can also post pictures, videos, gifs, audio, emojis, files, products to sell, polls, live recordings or tell us how you’re feeling, what your doing or where you’re going. </p>\n\n<p>Our live chat allows you to instantly message friends as well as form group chats. You can video chat or voice chat too if you want (coming soon).</p>\n\n<p>We are open and transparent; users can contact us at anytime via our contact form, via one of the dedicated support pages, support groups or post in the community forum or even reach out to a member of the team directly via our personal* pages on the site.</p>\n\n<p>Our tech savvy users can use our development tools to integrate our plugins into their sites for functions such as auto posting and sharing or integrate our oauth functionality to allow their users to login with their Dumb Dinosaur account and securely access certain profile data.</p>\n\n<p>Users can increase visibility of their posts through hashtags or pay to promote them via our advertisement and boost systems. Currently our ad service supports image based adverts in the sidebar or in between posts or skippable video ads that show before other videos.</p>\n\n<p>Users can also upgrade their account to a premium subscription with perks including more advanced profile customisation, post boosts and a snazzy verified badge.</p>\n\n<p>Users can create public groups and “brand accounts” to showcase their brands or projects in a more public facing manner than through their personal profile. </p>\n\n<p>We also currently support 10 languages with more coming soon.</p>\n\n<p>\n	<br>\n</p>\n\n<p>*If the staff opt-in of course.</p>\n');

-- --------------------------------------------------------

--
-- Table structure for table `uc_fields`
--

CREATE TABLE `uc_fields` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `fid_3` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fid_1` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fid_2` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email` varchar(52) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `first_name` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(52) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `email_code` varchar(35) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `avatar` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/avatar.jpg',
  `cover` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT 'upload/photos/cover.jpg',
  `country_id` int(11) NOT NULL DEFAULT 0,
  `gender` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT 'male',
  `about` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `instagram` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `facebook` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `twitter` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip_address` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0.0.0.0',
  `timezone` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'UTC',
  `language` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'english',
  `device_id` varchar(100) CHARACTER SET utf8 NOT NULL DEFAULT '',
  `last_active` int(11) NOT NULL DEFAULT 0,
  `src` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `verified` int(11) NOT NULL DEFAULT 0,
  `admin` enum('0','1') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `registered` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '00/0000',
  `active` enum('0','1','2') CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `is_pro` int(15) NOT NULL DEFAULT 0,
  `posts` int(11) NOT NULL DEFAULT 0,
  `fav_post` TEXT NULL DEFAULT NULL,  
  `wallet` decimal(13,2) NOT NULL DEFAULT 0.00
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `first_name`, `last_name`, `password`, `email_code`, `avatar`, `cover`, `country_id`, `gender`, `about`, `instagram`, `facebook`, `twitter`, `ip_address`, `timezone`, `language`, `device_id`, `last_active`, `src`, `verified`, `admin`, `registered`, `active`, `is_pro`, `posts`, `wallet`) VALUES
(4, 'connor', 'connorpaulgibson@gmail.com', 'Connor', 'Gibson', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', '469c37a45a1e422385f338b1e15cf086', 'upload/photos/avatar.jpg', 'upload/photos/cover.jpg', 0, 'male', 'Yozax', '', '', '', '::1', 'UTC', 'english', '', 1574326123, 'site', 1, '1', '11/2019', '1', 0, 0, '1000.00'),
(5, 'dinosupport', 'support@thedumbestdinosaur.com', '', '', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', '9a00d47a2580343b850be9ae02a20df2', 'upload/photos/avatar.jpg', 'upload/photos/cover.jpg', 0, 'male', NULL, '', '', '', '::1', 'UTC', 'english', '', 1573558715, 'site', 0, '0', '11/2019', '1', 0, 0, '0.00');

-- --------------------------------------------------------

--
-- Table structure for table `user_ads`
--

CREATE TABLE `user_ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `title` varchar(500) NOT NULL DEFAULT '',
  `url` varchar(3000) NOT NULL DEFAULT '',
  `placement` varchar(150) NOT NULL DEFAULT '',
  `status` int(5) NOT NULL DEFAULT 1,
  `spent` decimal(13,2) NOT NULL DEFAULT 0.00,
  `results` int(11) NOT NULL DEFAULT 0,
  `media_file` varchar(3000) NOT NULL DEFAULT '',
  `time` varchar(100) NOT NULL DEFAULT '0',
  `type` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user_reactions`
--

CREATE TABLE `user_reactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `post_id` int(11) NOT NULL DEFAULT 0,
  `page` varchar(10) NOT NULL DEFAULT '',
  `ip_address` varchar(100) NOT NULL DEFAULT '',
  `option_id` int(11) NOT NULL DEFAULT 0,
  `time` varchar(50) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_reactions`
--

INSERT INTO `user_reactions` (`id`, `user_id`, `post_id`, `page`, `ip_address`, `option_id`, `time`) VALUES
(1, 1, 26, 'news', '', 5, '1544805611'),
(2, 1, 26, 'news', '', 4, '1544805614'),
(3, 1, 26, 'news', '', 3, '1544805615'),
(4, 1, 26, 'news', '', 2, '1544805616'),
(5, 1, 26, 'news', '', 1, '1544805616'),
(6, 1, 26, 'news', '', 6, '1544805620'),
(7, 1, 26, 'news', '', 7, '1544805621'),
(8, 1, 26, 'news', '', 8, '1544805622'),
(9, 1, 26, 'news', '', 9, '1544805622');

-- --------------------------------------------------------

--
-- Table structure for table `verification_requests`
--

CREATE TABLE `verification_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `name` varchar(300) NOT NULL DEFAULT '',
  `message` text DEFAULT NULL,
  `passport` varchar(3000) NOT NULL DEFAULT '',
  `photo` varchar(3000) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT 'user',
  `time` varchar(100) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `short_title` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `tags` text COLLATE utf8_unicode_ci DEFAULT NULL,
  `category` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `viewable` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `shares` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `last_update` int(11) NOT NULL DEFAULT 0,
  `entries_per_page` int(32) NOT NULL DEFAULT 0,
  `views` int(11) NOT NULL DEFAULT 0,
  `featured` int(11) NOT NULL DEFAULT 0,
  `registered` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0/0000',
  `hd` int(11) NOT NULL DEFAULT 0,
  `active` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `option_id` int(11) NOT NULL DEFAULT 0,
  `entry_id` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(32) NOT NULL DEFAULT '0.0.0.0',
  `user_id` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `active` (`active`);

--
-- Indexes for table `announcement_views`
--
ALTER TABLE `announcement_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `announcement_id` (`announcement_id`);

--
-- Indexes for table `banned_ip`
--
ALTER TABLE `banned_ip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ip_address` (`ip_address`);

--
-- Indexes for table `breaking_news`
--
ALTER TABLE `breaking_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `news_id` (`news_id`);

--
-- Indexes for table `comm_replies`
--
ALTER TABLE `comm_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `config`
--
ALTER TABLE `config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `value` (`value`(767)),
  ADD KEY `name` (`name`);

--
-- Indexes for table `entries`
--
ALTER TABLE `entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `entry_type` (`entry_type`),
  ADD KEY `entry_page` (`entry_page`),
  ADD KEY `index_id` (`index_id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `id_2` (`id`);

--
-- Indexes for table `lists`
--
ALTER TABLE `lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `viewable` (`viewable`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `lists` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `lists` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `music`
--
ALTER TABLE `music`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `music` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `music` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `music` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `news` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `news` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `news` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expire` (`expire`);

--
-- Indexes for table `polls`
--
ALTER TABLE `polls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`);

--
-- Indexes for table `poll_pages`
--
ALTER TABLE `poll_pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `viewable` (`viewable`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `poll_pages` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `poll_pages` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `profile_fields`
--
ALTER TABLE `profile_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `registration_page` (`registration_page`),
  ADD KEY `active` (`active`),
  ADD KEY `profile_page` (`profile_page`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `quizzes` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `quizzes` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `quizzes` ADD FULLTEXT KEY `title_2` (`title`,`description`);

--
-- Indexes for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `entry_id` (`entry_id`),
  ADD KEY `result_index` (`result_index`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `seen` (`seen`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `page_id` (`page_id`),
  ADD KEY `type` (`type`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `platform` (`platform`),
  ADD KEY `time` (`time`);

--
-- Indexes for table `terms`
--
ALTER TABLE `terms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uc_fields`
--
ALTER TABLE `uc_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `active` (`active`),
  ADD KEY `admin` (`admin`),
  ADD KEY `last_active` (`last_active`),
  ADD KEY `is_pro` (`is_pro`),
  ADD KEY `wallet` (`wallet`);

--
-- Indexes for table `user_ads`
--
ALTER TABLE `user_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `user_reactions`
--
ALTER TABLE `user_reactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `verification_requests`
--
ALTER TABLE `verification_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category` (`category`),
  ADD KEY `active` (`active`),
  ADD KEY `featured` (`featured`),
  ADD KEY `hd` (`hd`);
ALTER TABLE `videos` ADD FULLTEXT KEY `slug` (`slug`);
ALTER TABLE `videos` ADD FULLTEXT KEY `short_title` (`short_title`);
ALTER TABLE `videos` ADD FULLTEXT KEY `title` (`title`,`description`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `option_id` (`option_id`),
  ADD KEY `ip_address` (`ip_address`),
  ADD KEY `entry_id` (`entry_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `announcement_views`
--
ALTER TABLE `announcement_views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banned_ip`
--
ALTER TABLE `banned_ip`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `breaking_news`
--
ALTER TABLE `breaking_news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `comm_replies`
--
ALTER TABLE `comm_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `config`
--
ALTER TABLE `config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `entries`
--
ALTER TABLE `entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lists`
--
ALTER TABLE `lists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `music`
--
ALTER TABLE `music`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `polls`
--
ALTER TABLE `polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `poll_pages`
--
ALTER TABLE `poll_pages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profile_fields`
--
ALTER TABLE `profile_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quiz_answers`
--
ALTER TABLE `quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessions`
--
ALTER TABLE `sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `terms`
--
ALTER TABLE `terms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `uc_fields`
--
ALTER TABLE `uc_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_ads`
--
ALTER TABLE `user_ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_reactions`
--
ALTER TABLE `user_reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `verification_requests`
--
ALTER TABLE `verification_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
