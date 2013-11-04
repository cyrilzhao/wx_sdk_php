微信公众平台第三方接口sdk
=========================
详细接口调用方法请查阅wiki文档：
mp.weixin.qq.com/wiki

使用示例
========
### 查询分组
	
	$sdk = new WxSDK($appid, $secret);
	$groups = $sdk->get_groups();

### 创建分组
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->create_group($group_name);

### 修改分组名
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->update_group($group_id, $group_name);

### 移动用户分组
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->update_group_of_user($openid, $to_groupid);

### 获取用户基本信息
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->get_user_info($openid);

### 获取关注者列表
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->get_user_list();

### 创建自定义菜单

	$sdk = new WxSDK($appid, $secret);
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

### 删除自定义菜单

	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->delete_menu();

### 获取自定义菜单
	
	$sdk = new WxSDK($appid, $secret);
	$resp = $sdk->get_menu();
