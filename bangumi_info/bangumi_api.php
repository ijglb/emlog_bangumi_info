<?php
require_once 'bangumi_info_config.php';

function curl_get($url){
    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	
	$data = curl_exec($curl);
    if(!$data){
		$data = null;
	}
    curl_close($curl);
	$json = json_decode($data);
	if(!$json || isset($json->code)){
		$json = null;
	}
    return $json;
}

$subject_arr = array(
'book'=>1,
'anime'=>2,
'music'=>3,
'game'=>4,
'real'=>5
);

//获取用户收藏列表 在看的动画三次元与书籍条目 book/anime/real
function bangumi_user_do_collection($subjecttype){
	global $config,$subject_arr;
	$url = 'https://api.bgm.tv/user/'.$config["user"].'/collection?cat=all_watching';
	$result = curl_get($url);
	if (isset($result)) {
		foreach ($result as $item) {
			if($item->subject->type == $subject_arr[$subjecttype]){
				$res_arr[] = array(
					'name'=>$item->subject->name,
					'name_cn'=>$item->subject->name_cn,
					'image'=>$item->subject->images->common,
					'url'=>$item->subject->url,
					'ep_status'=>$item->ep_status,
					'eps_count'=>$item->subject->eps_count
				);
			}
		}
		$output = array(
			'code' => '0', 
			'data' => $res_arr
		);
	} else {
		$output = array(
			'code'=>'-1',
			'msg'=>'查询失败'
		);
	}
	return json_encode($output);
}
//获取用户指定类型的收藏概览，固定返回最近更新的收藏，不支持翻页 book/anime/music/game/real
/* function bangumi_user_collections_recently($subjecttype){
	$url = 'https://api.bgm.tv/user/'.$config["user"].'/collections/'.$subjecttype.'?app_id='.$config["app_id"].'&max_results=25';
	return curl_get($url);
} */

if(!isset($_GET['action'])) exit('null');
$action = addslashes($_GET['action']);
switch ($action) {
	case 'do':
		if(!isset($_GET['type'])) exit('null');
		$subjecttype = addslashes($_GET['type']);
		print_r(bangumi_user_do_collection($subjecttype));
		break;
	default:
		exit('null');
		break;
}