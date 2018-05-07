<?php
!defined('EMLOG_ROOT') && exit('出错了！！！');

function plugin_setting_view(){
require_once 'bangumi_info_config.php';
?>
<div class="bangumi_info">
<span style=" font-size:18px; font-weight:bold;">bangumi_info配置</span><?php if(isset($_GET['setting'])){echo "<span class='actived'>设置保存成功!</span>";}?><br />
<br />
<form action="plugin.php?plugin=bangumi_info&action=setting" method="post">
<ul>
<li><h4>用户名</h4>
<div class="one"><input type="text" class="txt" name="user" value="<?php echo $config["user"];?>" size="33"/></div>
<p>填写bangumi用户名，即个人主页链接https://bgm.tv/user/xxxxx里的xxxxx</p></li>
<li><h4>缓存时间(小时)：</h4>
<div class="one"><input type="text" class="txt" name="cache" value="<?php echo $config["cache"];?>" size="33"/></div>
<p>填写数据缓存时间，0为不缓存。</p></li>
<div class="sl">说明：<br />
</div>
</ul>
<input type="submit" class="button" name="submit" value="保存设置" />
</form></div>
<?php }?>
<?php 
function plugin_setting(){
	require_once 'bangumi_info_config.php';
	$cache = isset($_POST["cache"]) ? intval($_POST["cache"]):0;
	$user = isset($_POST["user"]) ? addslashes($_POST["user"]):"";
	$newConfig = '<?php
$config = array(
    "cache" => '.$cache.',
	"user" => "'.$user.'",
);';
	echo $newConfig;
	@file_put_contents(EMLOG_ROOT.'/content/plugins/bangumi_info/bangumi_info_config.php', $newConfig);
}