<?php
require_once 'bangumi_info_config.php';

function curl_get($url){
    $curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	
	$data = curl_exec($curl);
    if(!$data){
		$data = null;
	}
    curl_close($curl);
    return $data;
}
function curl_get_json($url){
    $data = curl_get($url);
	$json = json_decode($data);
	if(!$json || isset($json->code)){
		$json = null;
	}
    return $json;
}
//直接解析网页数据 Bangumi API当前还没有获取全部类型和状态的收藏接口
function ParseCollectionData($html){
	if(isset($html)){
		 if(preg_match('#<ul id="browserItemList" class="browserFull">(.+)</ul>#s',$html,$ulmatch)){
			$res_arr = array();
			 $ul = $ulmatch[1];
			 if(preg_match_all('#<li id=".*?" class=".*?">(.*?)</li>#s',$ul,$limatchArr,PREG_SET_ORDER)){
				 foreach ($limatchArr as $limatch) {
					 $li = $limatch[1];
					 if(preg_match('#<img src="//lain\\.bgm\\.tv/pic/cover/./(.+?)" class="cover"#s',$li,$imgmatch)){
						 $img = 'http://lain.bgm.tv/pic/cover/c/'.$imgmatch[1];
					 }
					 else{
						 $img = 'http://bgm.tv/img/no_icon_subject.png';
					 }
					 if(preg_match('#<h3>.*?<a href="(.+?)" class="l">(.+?)</a>.*?(?:<small class=".+?">(.+?)</small>)?.*?</h3>#s',$li,$infomatch)){
						 $url = 'http://bgm.tv'.$infomatch[1];
						 $name_cn = $infomatch[2];
						 $name = $infomatch[3];

						$res_arr[] = array(
							'name'=> isset($name) ? $name : $name_cn,
							'name_cn'=>$name_cn,
							'image'=>$img,
							'url'=>$url,
							'ep_status'=>0,
							'eps_count'=>0
						);
					 }
				 }
			 }
			 $page = 1;
			 if(preg_match('#<div id="multipage">(.+?)</div>#s',$html,$pagesmatch)){
				 $pages = $pagesmatch[1];
				 if(preg_match_all('#<a href=".+?\?page=(\d+)" class="p">.+?</a>#s',$pages,$pageArr,PREG_SET_ORDER)){
					 foreach ($pageArr as $p) {
						if($p[1] > $page){
							$page = $p[1];
						}
					 }
				 }
			 }
		 }
		 if(isset($res_arr)){
			$output = array(
				'code' => '0', 
				'data' => $res_arr,
				'page' => 1,
				'pages' => (int)$page
			);
		 }
	}
	if(!isset($output)){
		$output = array(
			'code'=>'-1',
			'msg'=>'查询失败'
		);
	}
	return $output;
}

$subject_arr = array(
'book'=>1,
'anime'=>2,
'music'=>3,
'game'=>4,
'real'=>5
);

//获取用户do（在做）列表
function bangumi_user_do_collection($subjecttype,$page){
	global $config,$subject_arr;
	if($subject_arr[$subjecttype] == 1 ||$subject_arr[$subjecttype] == 2 ||$subject_arr[$subjecttype] == 6){
		$url = 'https://api.bgm.tv/user/'.$config["user"].'/collection?cat=all_watching';
		$result = curl_get_json($url);
		if (isset($result)) {
			$res_arr = array();
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
				'data' => $res_arr,
				'page' => 1,
				'pages' => 1
			);
		}
		if(!isset($output)){
			$output = array(
				'code'=>'-1',
				'msg'=>'查询失败'
			);
		}
	}
	else{
		$url = 'https://bgm.tv/'.$subjecttype.'/list/'.$config["user"].'/do?page='.$page;
		$html = curl_get($url);
		if(isset($html)){
			$output = ParseCollectionData($html);
			if($output['code'] == 0){
				$output['page'] = $page;
			}
		}
	}
	return json_encode($output);
}
//获取用户指定类型的收藏概览，固定返回最近更新的收藏，不支持翻页 book/anime/music/game/real
/* function bangumi_user_collections_recently($subjecttype){
	$url = 'https://api.bgm.tv/user/'.$config["user"].'/collections/'.$subjecttype.'?app_id='.$config["app_id"].'&max_results=25';
	return curl_get($url);
} */
function bangumi_user_html_collection($status,$subjecttype,$page){
	global $config;
	$url = 'https://bgm.tv/'.$subjecttype.'/list/'.$config["user"].'/'.$status.'?page='.$page;
	$html = curl_get($url);
	if(isset($html)){
		$output = ParseCollectionData($html);
		if($output['code'] == 0){
			$output['page'] = $page;
		}
	}
	return json_encode($output);
}

//type:book,anime,music,game,real
if(!isset($_GET['action'])) exit('null');
if(!isset($_GET['type'])) exit('null');
$action = addslashes($_GET['action']);
$subjecttype = addslashes($_GET['type']);
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if($page == 0) $page = 1;
switch ($action) {
	case 'do'://在做
		print_r(bangumi_user_do_collection($subjecttype,$page));
		break;
	case 'wish'://想做
	case 'collect'://做过
	case 'on_hold'://搁置
	case 'dropped'://抛弃
		print_r(bangumi_user_html_collection($action,$subjecttype,$page));
		break;
	default:
		exit('null');
		break;
}