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
?>
<?php function bangumi_info_output(){//页面输出函数
 require_once 'bangumi_info_config.php'; ?>
<div class="mdui-typo">
	<p>快速导航：<a class="not-ajaxload" href="#a-do">正在看的动画</a>
	<a class="not-ajaxload" href="#a-collect">看过的动画</a>
	<a class="not-ajaxload" href="#c-do">正在看的漫画/小说</a>
	<a class="not-ajaxload" href="#c-collect">看过的漫画/小说</a>
	<a class="not-ajaxload" href="#g-do">正在玩的游戏</a>
	<a class="not-ajaxload" href="#g-collect">玩过的游戏</a></p>
	<h3 id="a-do">正在看的动画</h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-a-do">
		<div class="mdui-col">加载中...</div>
	</div>
	<h3 id="a-collect">看过的动画<a target="_blank" href="//bgm.tv/anime/list/<?php echo $config["user"]; ?>/collect">More</a></h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-a-collect">
		<div class="mdui-col">加载中...</div>
	</div>
	<h3 id="c-do">正在看的漫画/小说</h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-c-do">
		<div class="mdui-col">加载中...</div>
	</div>
	<h3 id="c-collect">看过的漫画/小说<a target="_blank" href="//bgm.tv/book/list/<?php echo $config["user"]; ?>/collect">More</a></h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-c-collect">
		<div class="mdui-col">加载中...</div>
	</div>
	<h3 id="g-do">正在玩的游戏</h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-g-do">
		<div class="mdui-col">加载中...</div>
	</div>
	<h3 id="g-collect">玩过的游戏<a target="_blank" href="//bgm.tv/game/list/<?php echo $config["user"]; ?>/collect">More</a></h3>
	<div class="mdui-row-sm-1 mdui-row-md-2 i-card" id="bgm-g-collect">
		<div class="mdui-col">加载中...</div>
	</div>
	<script>
		function ajaxGetBgm(type,action,dom){
			$.ajax({
				method: 'GET',
				url: '/content/plugins/bangumi_info/bangumi_api.php',
				data:{
					action: action,
					type: type
				},
				success: function (data) {
					creatbgmdo(eval('('+data+')'),dom);
				},
				error: function (xhr, textStatus) {
					creatbgmdo(null,dom);
				}
			});
		}
		function creatbgmdo(data,dom){
			if(data != null && data.code == '0'){
				dom.empty();
				$.each(data.data, function (i, value) {
					var str = '<div class="mdui-col">';
					str+= '<a href="'+value.url+'" target="_blank">';
					str+= '<div class="mdui-card mdui-hoverable"><div class="mdui-card-content">';
					str+= '<img class="mdui-card-header-avatar" src="'+value.image+'"/>';
					str+= '<div class="mdui-card-header-title bgm-content">'+value.name_cn+'</div>';
					str+= '<div class="mdui-card-header-subtitle bgm-content">'+value.name+'</div>';
					str+= '<div class="mdui-card-header-subtitle bgm-content">&nbsp;</div>';
					if((value.eps_count == 0 && value.ep_status == 0)||value.eps_count == null){
						str+= '<div class="mdui-card-header-subtitle mdui-progress bgm-content"><div class="mdui-progress-determinate" style="width: 100%;">进度：未知</div></div>';
					}
					else{
						var progress = Math.round(value.ep_status / value.eps_count * 10000) / 100.00;
						str+= '<div class="mdui-card-header-subtitle mdui-progress bgm-content"><div class="mdui-progress-determinate" style="width: '+progress+'%;">进度：'+value.ep_status+'/'+value.eps_count+'</div></div>';
					}
					str+= '</div></div></a></div>';
					dom.append(str);
				});
			}
		}
		ajaxGetBgm('anime','do',$('#bgm-a-do'));
		ajaxGetBgm('anime','collect',$('#bgm-a-collect'));
		ajaxGetBgm('book','do',$('#bgm-c-do'));
		ajaxGetBgm('book','collect',$('#bgm-c-collect'));
		ajaxGetBgm('game','do',$('#bgm-g-do'));
		ajaxGetBgm('game','collect',$('#bgm-g-collect'));
	</script>
</div>
<?php }?>