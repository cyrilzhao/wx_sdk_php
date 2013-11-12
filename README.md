微信公众平台第三方接口SDK
=========================
详细接口调用方法请查阅wiki文档：
mp.weixin.qq.com/wiki

使用示例
========
##### 注意：所有接口调用失败均返回false

### 与公众平台校验token参数
```php
define('TOKEN', 'hello');	
WxSDK::valid_token(TOKEN);
```

### 实例化SDK对象
```php
// 这里的TOKEN由用户自行决定并填写，且应与在公众平台开发者页面填写的token保持一致
$sdk = new WxSDK($appid, $secret);
```

## 基础支持
### 上传媒体文件
```php
/**
 * 可调用本接口来上传图片、语音、视频等文件到微信服务器
 *
 * @param 	absolute_file_path 	一定要是文件的绝对路径
 * @param 	type 				文件类型type的可选项有image,voice,video和thumb
 */
$resp = $sdk->upload_media($absolute_file_path, $type);
/** 
 *	调用成功返回结果：
 *	$resp => array(
 *	  	'type' => string 'image' 
 *	  	'media_id' => string 'xxx' 
 *	  	'created_at' => int xxx
 *	)
 */
```

### 下载媒体文件
```php
/**
 * 公众号可调用本接口来获取多媒体文件。请注意，调用该接口需http协议。
 * 下载得到的文件将保存在参数指定的目录中，如果目录不存在将被创建
 *
 * @param   media_id            通过上传多媒体文件，得到的id
 * @param   dir                 下载文件的存放目录的相对路径
 */
 $resp = $sdk->get_media($media_id, $dir);
 /** 
 *  调用成功返回结果：
 *  $resp => true
 */
```

## 发送消息
### 被动回复文字消息
```php
/**
 * 通过此接口可以被动向用户回复文本消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	content 			回复的消息内容（换行：在content中能够换行，微信客户端就支持换行显示）
 */
$sdk->send_callback_text_message($fromuser_openid, $touser_openid, $content);
```

### 被动回复图片消息
```php
/**
 * 通过此接口可以被动向用户回复图片消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	media_id 			通过上传多媒体文件，得到的id
 */
$sdk->send_callback_image_message($fromuser_openid, $touser_openid, $media_id);
```

### 被动回复语音消息
```php
/**
 * 通过此接口可以被动向用户回复语音消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	media_id 			通过上传多媒体文件，得到的id
 */
$sdk->send_callback_voice_message($touser_openid, $fromuser_openid, $media_id);
```

### 被动回复视频消息
```php
/**
 * 通过此接口可以被动向用户回复视频消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	media_id 			通过上传多媒体文件，得到的id
 * @param 	thumb_media_id 		缩略图的媒体id，通过上传多媒体文件，得到的id
 */
$sdk->send_callback_video_message($touser_openid, $fromuser_openid, $media_id, $thumb_media_id);
```

### 被动回复音乐消息
```php
/**
 * 通过此接口可以被动向用户回复音乐消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	music_title 		音乐标题
 * @param 	description 		音乐描述
 * @param 	music_url 			音乐链接
 * @param 	hqmusicurl 			高质量音乐链接，WIFI环境优先使用该链接播放音乐
 * @param 	thumb_media_id 		缩略图的媒体id，通过上传多媒体文件，得到的id
 */
$sdk->send_callback_music_message($touser_openid, $fromuser_openid, $music_title, $description, $music_url, $hqmusicurl, $thumb_media_id);
```

### 被动回复图文消息
```php
/**
 *  参数$articles的形式如下：
 * 	$articles = array(
 * 		array(
 * 			"Title" => "title1",
 * 			"Description" => "description1",
 * 			"PicUrl" => "1.jpg",
 * 			"Url" => "www.google.com"
 * 		),
 * 		array(
 * 			"Title" => "title1",
 * 			"Description" => "description1",
 * 			"PicUrl" => "2.jpg",
 * 			"Url" => "www.qq.com"
 * 		)
 * 	);
 */
/**
 * 通过此接口可以被动向用户回复图文消息
 *
 * @param 	fromuser_openid		开发者微信号
 * @param 	touser_openid		接收方帐号（收到的OpenID）
 * @param 	articleCount 		图文消息个数，限制为10条以内
 * @param 	articles 			保存图文消息的数组，详细结构见上方示例
 */
$sdk->send_callback_news_message($touser_openid, $fromuser_openid, $articleCount, $articles);
```

### 发送客服文字消息
```php
/**
 * 通过此接口可以向用户发送客服文字消息，在公众号收到用户消息的24小时内不限制发送次数
 *
 * @param 	touser_openid		接收客服消息用户的openid
 * @param 	content 			文本消息内容
 */
$sdk->send_custom_text_message($touser_openid, $content);
```

### 发送客服图片消息
```php
/**
 * 通过此接口可以向用户发送客服图片消息，在公众号收到用户消息的24小时内不限制发送次数
 *
 * @param 	touser_openid		接收客服消息用户的openid
 * @param 	media_id 			发送的图片的媒体ID
 */
$sdk->send_custom_image_message($touser_openid, $media_id);
```

### 发送客服语音消息
```php
/**
 * 通过此接口可以向用户发送客服语音消息，在公众号收到用户消息的24小时内不限制发送次数
 *
 * @param 	touser_openid		接收客服消息用户的openid
 * @param 	media_id 			发送的语音的媒体ID
 */
$sdk->send_custom_voice_message($touser_openid, $media_id);
```

### 发送客服视频消息
```php
/**
 * 通过此接口可以向用户发送客服视频消息，在公众号收到用户消息的24小时内不限制发送次数
 *
 * @param 	touser_openid		接收客服消息用户的openid
 * @param 	media_id 			发送的视频的媒体ID
 * @param 	thumb_media_id 		视频缩略图的媒体ID
 */
$sdk->send_custom_video_message($touser_openid, $media_id, $thumb_media_id);
```

### 发送客服音乐消息
```php
/**
 * 通过此接口可以向用户发送客服音乐消息，在公众号收到用户消息的24小时内不限制发送次数
 *
 * @param 	touser_openid		接收客服消息用户的openid
 * @param 	music_title 		音乐标题
 * @param 	description 		音乐描述
 * @param 	music_url 			音乐链接
 * @param 	hqmusicurl 			高质量音乐链接，WIFI环境优先使用该链接播放音乐
 * @param 	thumb_media_id 		视频缩略图的媒体ID
 */
$sdk->send_custom_music_message($touser_openid, $music_title, $music_url, $description, $hqmusicurl, $thumb_media_id);
```

### 发送客服图文消息
```php
/**
 * 参数$articlesArray的形式如下：
 * 
 *  array(
 *  	"title" => "Happy Day",
 *   	"description" => "Is Really A Happy Day",
 *   	"url" => "URL",
 *   	"picurl" => "PIC_URL"
 *  ),
 *  array(
 *      "title" => "Happy Day",
 *   	"description" => "Is Really A Happy Day",
 *   	"url" => "URL",
 *   	"picurl" => "PIC_URL"
 *  )
 */
/**
 * 通过此接口可以向用户发送客服图文消息，
 * 在公众号收到用户消息的24小时内可发送的客服图文消息条数限制在10条以内。
 *
 * @param 	articlesArray 		保存图文消息的数组，详细结构见上方示例
 */
$sdk->send_custom_news_message($articlesArray);
```

## 用户管理
### 查询分组
```php
/**
 * 通过此接口可以查询当前公众号的所有分组信息
 *
 * @return	groups 				包含所有分组信息的数组，详细结构见下方示例
 */
$groups = $sdk->get_groups();
/** 
 *	调用成功返回结果：
 *	$resp => Array (
 *	    [groups] => Array (
 *          [0] => Array (
 *              [id] => 0
 *              [name] => 未分组
 *              [count] => 132
 *          ),
 *          [1] => Array (
 *              [id] => 1
 *              [name] => 黑名单
 *              [count] => 0
 *          ),
 *          ...
 *      )
 *  )
 */
```

### 创建分组
```php
/**
 * 通过此接口可以为当前公众号创建新的分组
 *
 * @param 	group_name 			要创建的分组的名称
 * 
 * @return	group 				包含新创建的分组信息的数组，详细结构见下方示例
 */
$group = $sdk->create_group($group_name);
/** 
 *	调用成功返回结果：
 *	$resp => array (
 *		'group' => array (
 *    		'id' => int xxx				新增数组的id，由微信分配
 *    		'name' => string 'xxx' 		分组名字，UTF8编码
 *		) 
 *	)
 */
```

### 修改分组名
```php
/**
 * 通过此接口可以修改当前公众号中某个分组的名称
 *
 * @param 	group_id			要修改名称的分组的id，由微信分配
 * @param 	group_name 			要修改的分组名称（30个字符以内）
 *
 * @return	group 				包含系统返回信息的数组，详细结构见下方示例
 */
$resp = $sdk->update_group($group_id, $group_name);
/**
 *	调用成功返回结果：
 *	$resp => array (
 *		'errcode' => int 0			// 系统返回码
 *		'errmsg' => string 'ok'		// 返回码对应的显示信息
 *	)
 */
```

### 移动用户分组
```php
/**
 * 通过此接口可以移动当前公众号中某个用户到特定的组中
 *
 * @param 	openid				普通用户的标识符，对当前公众号唯一
 * @param 	to_groupid			要移动的目标分组id		
 *
 * @return	resp 				包含系统返回信息的数组，详细结构见下方示例
 */
$resp = $sdk->update_group_of_user($openid, $to_groupid);
/**
 *	调用成功返回结果：
 *	$resp => array ( 
 *		'errcode' => int 0
 *		'errmsg' => string 'ok' 
 *	)
 */
```

### 获取用户基本信息
```php
/**
 * 通过此接口可以根据OpenID来获取用户基本信息
 *
 * @param 	openid				普通用户的标识符，对当前公众号唯一	
 *
 * @return	resp 				包含用户详细信息的数组，详细结构见下方示例
 */
$resp = $sdk->get_user_info($openid);
/** 
 *	调用成功返回结果： 
 *	$resp => array (
 *		'subscribe' => int 1
 *		'openid' => string 'xxx' 
 *		'nickname' => string 'xxx' 
 *		'sex' => int xxx
 *		'language' => string 'xxx' 
 *		'city' => string '' 
 *		'province' => string 'xxx'
 *		'country' => string 'xxx' 
 *		'headimgurl' => string 'xxx' 
 *		'subscribe_time' => int xxx
 *	)
 */
```

### 获取关注者列表
```php
/**
 * 通过此接口可以获取当前公众号的关注者列表
 *
 * @return	resp 				包含关注当前公众号的所有用户openid的数组，详细结构见下方示例
 */
$resp = $sdk->get_user_list();
/** 
 *	调用成功返回结果：
 *	$resp => Array ( 
 *	    'total' => int 151
 *	    'count' => int 151
 *	    'data' => array (
 *          'openid' => Array (
 *              0 => string omN7ljkwaz2AdN9bSU0Vz9XsxGY0
 *              1 => string omN7ljkj3bk0VJ03UxQBkLmNIX-U
 *              ...
 *          )
 *      )
 *  )
 */
```

## 网页授权获取用户基本信息
### 通过code换取网页授权access_token
```php
/**
 * 这里通过code换取的网页授权access_token,与基础支持中的access_token不同。
 * 公众号可通过下述接口来获取网页授权access_token。
 * 如果网页授权的作用域为snsapi_base，则本步骤中获取到网页授权access_token的同时，
 * 也获取到了openid，snsapi_base式的网页授权流程即到此为止。
 *
 * @param   code                第一步用户同意授权后获取的code参数
 *
 * @return  resp                包含OAuth_access_token信息的数组，详细结构见下方示例：
 */
$resp = $sdk->get_OAuth_access_token($code);
/** 
 *  调用成功返回结果：
 *  $resp => Array ( 
 *      'access_token' => string ACCESS_TOKEN       // 网页授权接口调用凭证。此access_token与
 *													   基础支持的access_token不同
 *      'expires_in' => int 7200                    // access_token接口调用凭证超时时间，单位（秒）
 *      'refresh_token' => string REFRESH_TOKEN     // 用户刷新access_token
 *      'openid' => string OPENID                   // 用户唯一标识，请注意，在未关注公众号时，
 *													   用户访问公众号的网页，
 *                                                     也会产生一个用户和公众号唯一的OpenID 
 *      'scope' => string SCOPE                     // 用户授权的作用域，使用逗号（,）分隔
 *  )
 */
```
### 刷新access_token
```php
/**
 * 通过此接口可以获取刷新OAuth_access_token（如果需要）。由于access_token拥有较短的有效期，
 * 当access_token超时后，可以使用refresh_token进行刷新，
 * refresh_token拥有较长的有效期（7天、30天、60天、90天），
 * 当refresh_token失效的后，需要用户重新授权。
 *
 * @param   $refresh_token      第二步中通过access_token和code获取到的refresh_token参数
 *
 * @return  resp                包含OAuth_access_token信息的数组，详细结构见下方示例：
 */
$resp = $sdk->refresh_OAuth_access_token($refresh_token);
/** 
 *  调用成功返回结果：
 *  $resp => Array ( 
 *      'access_token' => string ACCESS_TOKEN       // 网页授权接口调用凭证。此access_token与基础支持的access_token不同
 *      'expires_in' => int 7200                    // access_token接口调用凭证超时时间，单位（秒）
 *      'refresh_token' => string REFRESH_TOKEN     // 用户刷新access_token
 *      'openid' => string OPENID                   // 用户唯一标识，请注意，在未关注公众号时，
 *													   用户访问公众号的网页，
 *                                                     也会产生一个用户和公众号唯一的OpenID
 *      'scope' => string SCOPE                     // 用户授权的作用域，使用逗号（,）分隔
 *  )
 */
```
### 拉取用户信息
```php
/**
 * 通过此接口可以拉取用户信息(需scope为 snsapi_userinfo)
 *
 * @param   $openid             用户的唯一标识
 *
 * @return  resp                包含用户详细身份信息的数组，详细结构见下方示例
 */
$resp = $sdk->get_snsapi_userinfo($openid);
/** 
 *  调用成功返回结果：
 *  $resp => Array ( 
 *      'openid' => string OPENID                   // 用户唯一标识
 *      'nickname' => string NICKNAME               // 用户昵称
 *      'sex' => string "1"                         // 用户的性别，值为1时是男性，
 *													   值为2时是女性，值为0时是未知
 *      'province' => string PROVINCE               // 用户个人资料填写的省份
 *      'city' => string CITY                       // 普通用户个人资料填写的城市
 *      'country' => string COUNTRY                 // 国家，如中国为CN
 *      'headimgurl' => string URL                  // 用户头像，最后一个数值代表正方形头像大小
 *                                                    （有0、46、64、96、132数值可选，
 *													   0代表640*640正方形头像），
 *                                                     用户没有头像时该项为空 
 *      'privilege' =>  array=> (                   // 用户特权信息，json 数组，
 *													   如微信沃卡用户为（chinaunicom）
 *          "PRIVILEGE1", 
 *          "PRIVILEGE2"
 *      )
 *  )
 */
```

## 自定义菜单
### 创建自定义菜单
```php
/**
 * $menuArray = array(
 *		array(				// 以默认数字为键值表示该菜单为普通的一级菜单
 *			"type" => "click",
 *			"name" => "今日歌曲",
 *			"key" => "V1001_TODAY_MUSIC"
 *		),
 *		array(
 *			"type" => "view",
 *			"name" => "跳转",
 *			"url" => "http://www.qq.com",
 *		),
 *		"菜单" => array(	// 有声明键值则表示该菜单下带有二级菜单
 *			array(
 *				"type" => "view",
 *	            "name" => "搜索",
 *	            "url" => "http://www.soso.com/"
 *			),
 *			array(
 *				"type" => "view",
 *             	"name" => "视频",
 *             	"url" => "http://v.qq.com/"
 *			),
 *			array(
 *				"type" => "click",
 *             	"name" => "赞一下我们",
 *             	"key" => "V1001_GOOD"
 *			)
 *		)
 *	)
 */
/**
 * 通过此接口可以为当前公众号创建自定义菜单（当前公众号必须具有相应权限）
 *
 * @param 	menuArray			包含要创建的自定义菜单内容的数组，详细结构见上方示例
 *
 * @return	resp 				包含有系统返回码和请求结果信息的数组，详细结构见下方示例
 */
$resp = $sdk->create_menu($menuArray);
/**
 *	调用成功返回结果：
 *	$resp => array (
 *		'errcode' => int 0
 *		'errmsg' => string 'ok'
 *	)
 */
```

### 删除自定义菜单
```php
/**
 * 通过此接口可以删除当前公众号的自定义菜单（当前公众号必须具有相应权限）
 *
 * @return	resp 				包含有系统返回码和请求结果信息的数组，详细结构见下方示例
 */
$resp = $sdk->delete_menu();
/** 
 *	调用成功返回结果：
 *	$resp => array (
 *		'errcode' => int 0
 *		'errmsg' => string 'ok' 
 *	)
 */
```

### 获取自定义菜单
```php	
/**
 * 通过此接口可以获取当前公众号的自定义菜单（当前公众号必须具有相应权限）
 *
 * @return	resp 				包含有自定义菜单内容的数组，详细结构见下方示例
 */
$resp = $sdk->get_menu();
/**
 *	调用成功返回结果：
 *	$resp => Array (
 *	    [menu] => Array (
 *	        [button] => Array (
 *              [0] => Array (
 *                  [type] => click
 *                  [name] => 今日歌曲
 *                  [key] => V1001_TODAY_MUSIC
 *                  [sub_button] => Array () // 注意这里会多出一个空的sub_button数组
 *              )
 *              [1] => Array (
 *                  [type] => view
 *                  [name] => 跳转
 *                  [url] => http://www.qq.com
 *                  [sub_button] => Array ()
 *              )
 *              [2] => Array (
 *	                [name] => 菜单
 *	                [sub_button] => Array (
 *                      [0] => Array (
 *                          [type] => view
 *                          [name] => 搜索
 *                          [url] => http://www.soso.com/
 *                          [sub_button] => Array ()
 *                      )
 *                      [1] => Array (
 *                          [type] => view
 *                          [name] => 视频
 *                          [url] => http://v.qq.com/
 *                          [sub_button] => Array ()
 *                      )
 *                      [2] => Array (
 *                          [type] => click
 *                          [name] => 赞一下我们
 *                          [key] => V1001_GOOD
 *                          [sub_button] => Array ()
 *						)
 *	                )
 *	            )
 *	        )
 *	    )
 *	)
 */
```

## 推广支持
### 获取带参数永久二维码图片的ticket
```php
/**
 * 通过此接口可以获取带参数永久二维码图片的ticket
 *
 * @param 	scene_id 			场景值ID，永久二维码时最大值为1000
 *
 * @return	resp 				包含有ticket字符串的数组，详细结构见下方示例
 */
$resp = $sdk->get_qr_code_forever($scene_id);
/** 
 *	调用成功返回结果：
 *	$resp => array(
 *	  	'ticket' => string 'xxx' // 获取的二维码ticket，凭借此ticket可以换取二维码。
 *	)
 */
```

### 获取带参数临时二维码图片的ticket
```php
/**
 * 通过此接口可以获取带参数临时二维码图片的ticket
 *
 * @param 	scene_id 			场景值ID，临时二维码时为32位整型
 *
 * @return	resp 				包含有ticket字符串和有效时间的数组，详细结构见下方示例
 */
$resp = $sdk->get_qr_code_temporary($scene_id, $expire_seconds);
/** 
 *	调用成功返回结果：
 *	$resp => array(
 *	  	'ticket' => string 'xxx' 		// 获取的二维码ticket，凭借此ticket可以在有效时间内换取二维码。
 *	  	'expire_seconds' => int xxx 	// 二维码的有效时间，以秒为单位。最大不超过1800。
 *	)
 */
```

### 通过ticket换取二维码图片url
```php
/**
 * 通过此接口可以获取带参数临时二维码图片的ticket
 *
 * @param 	ticket 				获取的二维码ticket字符串
 *
 * @return	url 				可获取指定二维码图片的url字符串
 */
$url = $sdk->get_qr_code_url($ticket);
/** 
 *	调用成功返回结果：
 *	$url => https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=XXX;
 */
```
