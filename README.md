微信公众平台第三方接口sdk
=========================
详细接口调用方法请查阅wiki文档：
mp.weixin.qq.com/wiki

使用示例
========
### 创建自定义菜单

$sdk = new WxSDK('YOUR APPID', 'YOUR SECRET');
$resp = $sdk->create_menu(
	array(
		array(
			"type" => "click",
			"name" => "今日歌曲",
			"key" => "V1001_TODAY_MUSIC"
		),
		array(
			"type" => "view",
			"name" => "跳转",
			"url" => "http://www.qq.com",
		)
	)
);
