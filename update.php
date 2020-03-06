<?php
// @author Saad Mirza https://saadmirza.net
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (file_exists('assets/init.php')) {
    require 'assets/init.php';
} else {
    die('Please put this file in the home directory!');
}

//THIS IS A LIST OF NEW DATABASE TABLES THAT NEED ADDING.
$query = mysqli_query($sqlConnect, "ALTER TABLE `music` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "ALTER TABLE `lists` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "ALTER TABLE `news` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "ALTER TABLE `poll_pages` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "ALTER TABLE `quizzes` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "ALTER TABLE `videos` ADD `hd` INT NOT NULL DEFAULT '0' AFTER `registered`, ADD INDEX (`hd`);");
$query = mysqli_query($sqlConnect, "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'show_subscribe_box', '0'), (NULL, 'subscribe_box_username', NULL);");


echo 'The script is successfully updated to {{VERSION}}!';
$name = md5(microtime()) . '_updated.php';
rename('update.php', $name);
exit();
