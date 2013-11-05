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
	调用成功返回结果：
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
/* 
	调用成功返回结果：
	$resp => Array (
	    [groups] => Array (
            [0] => Array (
                [id] => 0
                [name] => 未分组
                [count] => 132
            ),
            [1] => Array (
                [id] => 1
                [name] => 黑名单
                [count] => 0
            ),
            ...
        )
    )
*/
```

### 创建分组
```php
$resp = $sdk->create_group($group_name);
/* 
	调用成功返回结果：
	$resp => array (
  		'group' => array (
      		'id' => int xxx
      		'name' => string 'xxx' 
		)
	)
*/

```

### 修改分组名
```php
$resp = $sdk->update_group($group_id, $group_name);
/* 
	调用成功返回结果：
	$resp => array (
  		'errcode' => int 0
  		'errmsg' => string 'ok'
  	)
*/
```

### 移动用户分组
```php
$resp = $sdk->update_group_of_user($openid, $to_groupid);
/* 
	调用成功返回结果：
	$resp => array (
		'errcode' => int 0
		'errmsg' => string 'ok' 
	)
*/
```

### 获取用户基本信息
```php
$resp = $sdk->get_user_info($openid);
/* 
	调用成功返回结果：
	$resp => array (
		'subscribe' => int 1
		'openid' => string 'xxx' 
		'nickname' => string 'xxx' 
		'sex' => int xxx
		'language' => string 'xxx' 
		'city' => string '' 
		'province' => string 'xxx'
		'country' => string 'xxx' 
		'headimgurl' => string 'xxx' 
		'subscribe_time' => int xxx
	)
*/
```

### 获取关注者列表
```php
$resp = $sdk->get_user_list();
/* 
	调用成功返回结果：
	$resp => Array (
	    'total' => int 151
	    'count' => int 151
	    'data' => array (
            'openid' => Array (
                0 => string omN7ljkwaz2AdN9bSU0Vz9XsxGY0
                1 => string omN7ljkj3bk0VJ03UxQBkLmNIX-U
                ...
            )
        )
    )
*/

```

### 创建自定义菜单
```php
$resp = $sdk->create_menu(
	array(
		array(				// 以数字为键值表示该菜单为普通的一级菜单
			"type" => "click",
			"name" => "今日歌曲",
			"key" => "V1001_TODAY_MUSIC"
		),
		array(
			"type" => "view",
			"name" => "跳转",
			"url" => "http://www.qq.com",
		),
		"菜单" => array(	// 有声明键值则表示该菜单下带有二级菜单
			array(
				"type" => "view",
	            "name" => "搜索",
	            "url" => "http://www.soso.com/"
			),
			array(
				"type" => "view",
               	"name" => "视频",
               	"url" => "http://v.qq.com/"
			),
			array(
				"type" => "click",
               	"name" => "赞一下我们",
               	"key" => "V1001_GOOD"
			)
		)
	)
);
/* 
	调用成功返回结果：
	$resp => array (
  		'errcode' => int 0
  		'errmsg' => string 'ok'
  	)
*/

```

### 删除自定义菜单
```php
$resp = $sdk->delete_menu();
/* 
	调用成功返回结果：
	$resp => array (
  		'errcode' => int 0
  		'errmsg' => string 'ok' 
  	)
*/
```

### 获取自定义菜单
```php	
$resp = $sdk->get_menu();
/* 
	调用成功返回结果：
	$resp => Array (
	    [menu] => Array (
	        [button] => Array (
                [0] => Array (
                    [type] => click
                    [name] => 今日歌曲
                    [key] => V1001_TODAY_MUSIC
                    [sub_button] => Array () // 注意这里会多出一个空的sub_button数组
                )
                [1] => Array (
                    [type] => view
                    [name] => 跳转
                    [url] => http://www.qq.com
                    [sub_button] => Array ()
                )
                [2] => Array (
	                [name] => 菜单
	                [sub_button] => Array (
                        [0] => Array (
                            [type] => view
                            [name] => 搜索
                            [url] => http://www.soso.com/
                            [sub_button] => Array ()
                        )
                        [1] => Array (
                            [type] => view
                            [name] => 视频
                            [url] => http://v.qq.com/
                            [sub_button] => Array ()
                        )
                        [2] => Array (
                            [type] => click
                            [name] => 赞一下我们
                            [key] => V1001_GOOD
                            [sub_button] => Array ()
						)
	                )
	            )
	        )
	    )
	)
*/
```

### 获取带参数永久二维码图片的ticket
```php
$resp = $sdk->get_qr_code_forever($scene_id);
/* 
	调用成功返回结果：
	$resp => array(
	  	'ticket' => string 'xxx' 
	)
*/
```

### 获取带参数临时二维码图片的ticket
```php
$resp = $sdk->get_qr_code_temporary($scene_id, $expire_seconds);
/* 
	调用成功返回结果：
	$resp => array(
	  	'ticket' => string 'xxx' 
	  	'expire_seconds' => int xxx
	)
*/
```

