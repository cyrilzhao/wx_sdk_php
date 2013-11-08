<?php

require('../src/wx.sdk.php');

header("Content-Type:text/html;charset=utf-8");

define('TOKEN', 'test');
$sdk = new WxSDK('appid', 'secret');

// 查询分组
// $groups = $sdk->get_groups();
// print_r($groups);

// 创建分组
// $resp = $sdk->create_group("hahahaha");
// var_dump($resp);

// 修改分组名
// $resp = $sdk->update_group(116, "aaaa");
// var_dump($resp);

// 移动用户分组
// $resp = $sdk->update_group_of_user("omN7ljgRDof7IaHfmudQdhoN9qMQ", 116);
// var_dump($resp);

// 获取用户基本信息
// $resp = $sdk->get_user_info("omN7ljgRDof7IaHfmudQdhoN9qMQ");
// var_dump($resp);

// 获取关注者列表
// $resp = $sdk->get_user_list();
// print_r($resp);

// $access_token = $sdk->get_access_token();
// print_r($access_token);

// 解析xml返回包
// $xmlString = 	"<xml>".
// 					"<ToUserName><![CDATA[cyrilzhao]]></ToUserName>".
// 					"<FromUserName><![CDATA[zhaojian]]></FromUserName>".
// 					"<CreateTime>123456789</CreateTime>".
// 					"<MsgType><![CDATA[event]]></MsgType>".
// 					"<Event><![CDATA[LOCATION]]></Event>".
// 					"<Latitude>23.137466</Latitude>".
// 					"<Longitude>113.352425</Longitude>".
// 					"<Precision>119.385040</Precision>".
// 				"</xml>";
// $xml = $sdk->parseRespXML($xmlString);
// print_r($xml);

// 生成xml响应包
// $xmlString = $sdk->getRespXML(
// 	array(
// 		"University" => "SYSU",
// 		"School of Professional" => "Software Engineering",
// 		"person" => array(
// 			"cyrilzhao" => array(
// 				"name" => "zhaojian",
// 				"age" => 21
// 			),
// 			"xuscan" => array(
// 				"name" => "xushijian",
// 				"age" => 22
// 			)
// 		)
// 	)
// );
// var_dump($xmlString);
// $xml->FromUserName = preg_replace("/\[CDATA\](.*?)\[\/CDATA\]/ies", "base64_decode('$1')", $xml->FromUserName);
// print_r($xml->FromUserName);

// 上传媒体文件
// $resp = $sdk->upload_media("D:/wamp/www/wx_sdk/test/1.jpg", "image");
// var_dump($resp);

// 下载媒体文件
// $resp = $sdk->get_media("jmpBeA3AnEcKE0n8cd8ziYf2OUDh4xxw56tzeik-iJyGJOeTbHuzx0R7YLim7GMj");
// var_dump($resp);


// 创建自定义菜单
// $resp = $sdk->create_menu(
// 	array(
// 		array(
// 			"type" => "click",
// 			"name" => "今日歌曲",
// 			"key" => "V1001_TODAY_MUSIC"
// 		),
// 		array(
// 			"type" => "view",
// 			"name" => "跳转",
// 			"url" => "http://www.qq.com",
// 		),

// 		"菜单" => array(
// 			array(
// 				"type" => "view",
// 	            "name" => "搜索",
// 	            "url" => "http://www.soso.com/"
// 			),
// 			array(
// 				"type" => "view",
//                	"name" => "视频",
//                	"url" => "http://v.qq.com/"
// 			),
// 			array(
// 				"type" => "click",
//                	"name" => "赞一下我们",
//                	"key" => "V1001_GOOD"
// 			)
// 		)
// 	)
// );
// var_dump($resp);

// 获取自定义菜单
// $resp = $sdk->get_menu();
// print_r($resp);

// 删除自定义菜单
// $resp = $sdk->delete_menu();
// var_dump($resp);

// 获取带参数永久二维码ticket
// $resp = $sdk->get_qr_code_forever(123);
// var_dump($resp);

// 获取带参数临时二维码ticket
// $resp = $sdk->get_qr_code_temporary(123, 1800);
// var_dump($resp);

// 通过ticket换取二维码图片url
// $ticket = "gQH47zoAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2UzVjRmanprc21MTGdqTlJqVnRjAAIEjdh4UgMECAcAAA==";
// $resp = $sdk->get_qr_code_url($ticket);
// echo '<img src="' . $resp . '" />';
// var_dump($resp);

// 被动回复文字消息
// $sdk->send_callback_text_message("cyrilzhao", "zhaojian", "hello");

// 被动回复图片消息
// $sdk->send_callback_image_message("cyrilzhao", "zhaojian", "jmpBeA3AnEcKE0n8cd8ziYf2OUDh4xxw56tzeik-iJyGJOeTbHuzx0R7YLim7GMj");

// 被动回复图文消息
// $articles = array(
// 	array(
// 		"Title" => "title1",
// 		"Description" => "description1",
// 		"PicUrl" => "1.jpg",
// 		"Url" => "www.google.com"
// 	),
// 	array(
// 		"Title" => "title1",
// 		"Description" => "description1",
// 		"PicUrl" => "2.jpg",
// 		"Url" => "www.qq.com"
// 	)
// );
// $sdk->send_callback_news_message("cyrilzhao", "zhaojian", 2, $articles);
