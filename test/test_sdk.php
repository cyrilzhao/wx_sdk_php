<?php

require('wx.sdk.php');

define('TOKEN', 'test');
$sdk = new WxSDK('wx1bc509d4d039b25a', '9d513ee452109a3b03630334aea45006');
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

$resp = $sdk->get_qr_code(array(
		"action_name"=>"QR_LIMIT_SCENE",
		"action_info" => array(
				"scene"=>array(
						"scene_id"=>123
					)
			)
	));
$resp = $resp['url'];
echo ("<img src='$resp' />");
