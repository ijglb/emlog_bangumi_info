<?php
/*
Plugin Name: bangumi信息获取
Version: 1.0
Plugin URL: http://www.ijglb.com
Description:获取bangumi上自己的收藏等信息在博客中展示
ForEmlog:5.3.x
Author: 极光萝卜
Author URL: http://www.ijglb.com
*/
!defined('EMLOG_ROOT') && exit('access deined!');
function bangumi_info_menu(){
	echo '<div class="sidebarsubmenu"><a href="./plugin.php?plugin=bangumi_info">bangumi_info</a></div>';
}
addAction('adm_sidebar_ext', 'bangumi_info_menu');