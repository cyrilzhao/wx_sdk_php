微信公众平台第三方接口SDK
=========================
详细接口调用方法请查阅wiki文档：
mp.weixin.qq.com/wiki

使用示例
========
### 实例化SDK对象

```php
// 这里的TOKEN由用户自行决定并填写，且应与在公众平台开发者页面填写的token保持一致
define('TOKEN', 'test');	
$sdk = new WxSDK($appid, $secret);
```

### 上传媒体文件
```php
// absolute_file_path一定要是文件的绝对路径，文件类型type的可选项有image,voice,video和thumb
$resp = $sdk->upload_media($absolute_file_path, $type);
/* 
	$resp => array(
	  	'type' => string 'image' 
	  	'media_id' => string 'xxx' 
	  	'created_at' => int xxx
	)
*/
```

### 查询分组
```php
$groups = $sdk->get_groups();
```

### 创建分组
```php
$resp = $sdk->create_group($group_name);
```

### 修改分组名
```php
$resp = $sdk->update_group($group_id, $group_name);
```

### 移动用户分组
```php
$resp = $sdk->update_group_of_user($openid, $to_groupid);
```

### 获取用户基本信息
```php
$resp = $sdk->get_user_info($openid);
```

### 获取关注者列表
```php
$resp = $sdk->get_user_list();
```

### 创建自定义菜单
```php
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
```

### 删除自定义菜单
```php
$resp = $sdk->delete_menu();
```

### 获取自定义菜单
```php	
$resp = $sdk->get_menu();
```

### 获取带参数永久二维码图片的ticket
```php
$resp = $sdk->get_qr_code_forever($scene_id);
/* 
	$resp => array(
	  	'ticket' => string 'xxx' 
	)
*/
```

