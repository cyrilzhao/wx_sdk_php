<?php

require('../src/wx.sdk.php');

define('TOKEN', 'test');
$sdk = new WxSDK('wx1bc509d4d039b25a', '9d513ee452109a3b03630334aea45006');

// 查询分组
// $groups = $sdk->get_groups();
// print_r($groups);

// 创建分组
// $resp = $sdk->create_group("cycy");
// print_r($resp);

// 修改分组名
// $resp = $sdk->update_group(116, "cyrilzhao");
// print_r($resp);

// 移动用户分组
// $resp = $sdk->move_user_in_group();
// print_r($resp);

// 获取用户基本信息
// $resp = $sdk->get_user_info("omN7ljkwaz2AdN9bSU0Vz9XsxGY0");
// print_r($resp);

// 获取关注者列表
// $resp = $sdk->get_user_list();
// print_r($resp);

// $access_token = $sdk->get_access_token();
// print_r($access_token);

/*
$resp = $sdk->create_menu(
	array(
			array(
					"type" => "click",
					"name" => "今aa日歌曲1",
					"key" => "V1001_TODAY_MUSIC",
				),

			array(
					"type" => "view",
					"name" => "跳v1转2",
					"url" => "http://www.qq.com",
				)
		)
	);

var_dump($resp);

$resp = $sdk->get_menu();
var_dump($resp);
*/

// $resp = $sdk->get_qr_code(array(
// 		"action_name"=>"QR_LIMIT_SCENE",
// 		"action_info" => array(
// 				"scene"=>array(
// 						"scene_id"=>123
// 					)
// 			)
// 	));
// $resp = $resp['url'];
// echo ("<img src='$resp' />");


