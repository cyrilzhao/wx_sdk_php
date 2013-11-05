微信公众平台第三方接口SDK
=========================
详细接口调用方法请查阅wiki文档：
mp.weixin.qq.com/wiki

使用示例
========
### 实例化SDK对象

```php
define('TOKEN', 'test');	// 这里的TOKEN由用户自行决定并填写，且应与在公众平台开发者页面填写的token保持一致
$sdk = new WxSDK($appid, $secret);
```

### 查询分组

	$groups = $sdk->get_groups();

### 创建分组
	
	$resp = $sdk->create_group($group_name);

### 修改分组名
	
	$resp = $sdk->update_group($group_id, $group_name);

### 移动用户分组
	
	$resp = $sdk->update_group_of_user($openid, $to_groupid);

### 获取用户基本信息
	
	$resp = $sdk->get_user_info($openid);

### 获取关注者列表
	
	$resp = $sdk->get_user_list();

### 创建自定义菜单

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

	$resp = $sdk->delete_menu();

### 获取自定义菜单
	
	$resp = $sdk->get_menu();
