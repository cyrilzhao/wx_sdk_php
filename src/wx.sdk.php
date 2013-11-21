<?php

define('OS_WIN', !strncasecmp(PHP_OS, 'win', 3)); // 判断是否为windows系统

class WxSDKException extends Exception {
	public function __construct($msg = 'WxSDKException'){
		$this->msg = $msg;
	}

	public function getErrorMsg() {
		return $this->msg;	
	}
}

class HTTP_Helper {
	private $boundary = '';

	const timeout = 30;
	const connecttimeout = 30;
	const ssl_verifypeer = FALSE;
	const useragent = 'WeiXin SDK 1.0.0';
	const host = 'https://api.weixin.qq.com/cgi-bin/';

	private function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		$this->boundary = $boundary = uniqid('------------------');
		$MPboundary = '--'.$boundary;
		$endMPboundary = $MPboundary. '--';
		$multipartbody = '';

		foreach ($params as $parameter => $value) {

			if( in_array($parameter, array('pic', 'image')) && $value{0} == '@' ) {
				$url = ltrim( $value, '@' );
				$content = file_get_contents( $url );
				$array = explode( '?', basename( $url ) );
				$filename = $array[0];

				$body .= $MPboundary . "\r\n";
				$body .= 'Content-Disposition: form-data; name="' . $parameter . '"; filename="' . $filename . '"'. "\r\n";
				$body .= "Content-Type: image/unknown\r\n\r\n";
				$body .= $content. "\r\n";
			} else {
				$body .= $MPboundary . "\r\n";
				$body .= 'content-disposition: form-data; name="' . $parameter . "\"\r\n\r\n";
				$body .= $value."\r\n";
			}

		}

		$multipartbody .= $endMPboundary;
		return $multipartbody;
	}

	public function http_send($url, $method, $postfields = NULL, $headers = array()) {
		$this->http_info = array();
		$ci = curl_init();

		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, self::useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, self::connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, self::timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, self::ssl_verifypeer);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'get_http_header'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token )
			$headers[] = "Authorization: OAuth2 ".$this->access_token;

		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);

		$this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$this->http_info = array_merge($this->http_info, curl_getinfo($ci));
		$this->url = $url;

		curl_close ($ci);		
		return $response;
	}

	private function do_request($cgi, $method, $params, $multi = false) {
		if (strrpos($cgi, 'https://') !== 0 && strrpos($cgi, 'https://') !== 0) {
			$cgi = self::host . $cgi;
		}

		switch (strtoupper($method)) {
			case 'GET':
				$cgi = $cgi . '?' . http_build_query($params);
				return $this->http_send($cgi, 'GET');
			default:
				$headers = array();
				if (!$multi) {
					$body = $params;
				} else {
					$body = $this->build_http_query_multi($params);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . $this->boundary;
				}
				return $this->http_send($cgi, $method, $body, $headers);
		}
	}

	// 获取http头部信息并保存在http_header属性数组中
	public function get_http_header($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	// 微信不支持\uxxxx的UNICODE模式，所以需要转成中文！
	public static function _replace_unicode($str) {
		return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $str); 
	}

	// 发送get请求的接口
	public function get($url, $parameters = array(), $access_token, $format = 'json') {
		$resp = $this->do_request($url, 'GET', $parameters);

		if ($format === 'json') {
			return json_decode($resp, true);
		}
		return $resp;
	}

	// 发送post请求的接口
	public function post($url, $parameters = array(), $access_token, $multi = false, $format = 'json') {
		$resp = $this->do_request($url, 'POST', $parameters, $multi);

		if ($format === 'json') {
			return json_decode($resp, true);
		}
		return $resp;
	}
}

/**
 * 微信公众平台第三方接口SDK
 *
 * @author raphealguo cyrilzhao
 */
class WxSDK {
	private $OAuth_access_token = null;
	private $access_token = null;
	private $expires_in = 0;
	private $secret = '';
	private $appid = '';
	
	public function __construct($appid = '', $secret = ''){
		$this->appid = $appid;
		$this->secret = $secret;
		if ($appid != '' && $secret != ''){
			$this->get_access_token();
		}
	}

	public function post($url, $parameters = array(), $multi = false, $format = 'json') {
		$http_helper = new HTTP_Helper();

		return $http_helper->post($url, $parameters, $this->access_token, $multi, $format);
	}

	public function get($url, $parameters = array(), $format = 'json') {
		$http_helper = new HTTP_Helper();

		return $http_helper->get($url, $parameters, $this->access_token, $format);
	}	

	// 将xml字符串解析为数组对象
	public function parseRespXML($xmlString) {
		$result = array();

		$xml = new SimpleXMLElement($xmlString);

		$xmlString = preg_replace("/\<\!\[CDATA\[(.*?)\]\]\>/ies", "'[CDATA]'.base64_encode('$1').'[/CDATA]'", $xmlString);
		foreach ($xml->children() as $key => $value) {
			$result[$key] = preg_replace("/\[CDATA\](.*?)\[\/CDATA\]/ies", "base64_decode('$1')", $value);
		}

		return $result;
	}

	private function _getXMlString($array) {
		$resultStr = "";
		foreach ($array as $key => $value) {
			if(is_array($value) || is_object($value)) {
				$value = $this->_getXMlString($value);
			} else if(!is_numeric($value)) {
				$value = "<![CDATA[" . $value . "]]>";
			}

			$resultStr = $resultStr . "<" . $key . ">" . $value . "</" . $key . ">";
		}

		return $resultStr;
	}

	// 将数组对象转换为xml字符串
	public function getRespXML($array) {
		$xmlObj = '';

		foreach ($array as $key => $value) {
			if(is_array($value) || is_object($value)) {
				$value = $this->_getXMlString($value);
			} else if(!is_numeric($value)) {
				$value = "<![CDATA[" . $value . "]]>";
			}
			$xmlObj .= "<{$key}>$value</{$key}>";
		}
		$xmlString = "<xml>{$xmlObj}</xml>";
		//$xmlString = htmlspecialchars_decode($xmlString);
		return $xmlString;
	}

	//用于开发者模式下校验来自公众平台的token参数
	private static function _checkSignature($signature, $timestamp, $nonce, $token){
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode($tmpArr);
		$tmpStr = sha1($tmpStr);

		if($tmpStr == $signature){
			return true;
		}else{
			return false;
		}
	}
	// 验证token是否合法，成功则直接输出echostr
	public static function valid_token($token){
		$echostr = $_GET['echostr'];
		$signature = $_GET['signature'];
		$timestamp = $_GET['timestamp'];
		$nonce = $_GET['nonce'];
		if (self::_checkSignature($signature, $timestamp, $nonce, $token)){
			echo $echostr;
		}
		exit();
	}
	//用于开发者模式下校验来自公众平台的token参数 end

	private static function _check_resp_cb($resp, $excepition_msg){
		try {
			if (is_array($resp) && isset($resp['errcode']) && $resp['errcode'] == 0) {
				return $resp;
			} else {
				throw new WxSDKException($excepition_msg . " msg : " . $resp['errmsg']. ', errorCode :' . $resp['errcode']);
			}
		} catch (WxSDKException $e) {
			echo $e->getErrorMsg();
			return false;
		}
	}
	private static function _check_resp_cb_without_errcode($resp, $excepition_msg){
		try {
			if (is_array($resp) && !isset($resp['errcode'])) {
				return $resp;
			} else {
				throw new WxSDKException($excepition_msg . " => message: " . $resp['errmsg']. ', errorCode :' . $resp['errcode']);
			}
		} catch (WxSDKException $e) {
			echo $e->getErrorMsg();
			return false;
		}
	}

	//获取access token
	public function get_access_token(){
		$params = array();
		$params['grant_type'] = 'client_credential';
		$params['appid'] = $this->appid;
		$params['secret'] = $this->secret;

		$resp = $this->get('token', $params);
		$resp = self::_check_resp_cb_without_errcode($resp, 'get_access_token');

		if($resp === false)
			throw new WxSDKException("Failed to initalize SDK");

		$this->access_token = $resp['access_token'];
		$this->expires_in = $resp['expires_in'];

		return $resp;
	}

	private function _upload_file($url, $params) {
		$http_helper = new HTTP_Helper();
		$resp = $http_helper->http_send($url, "POST", $params);

		$resp = json_decode($resp, true);
		if($resp && !empty($resp)) {
			$resp = $this->_check_resp_cb_without_errcode($resp, 'get_media');
		}

		return $resp;
	}

	// 上传多媒体文件
	public function upload_media($absolute_file_path, $type) {
		if(OS_WIN) { // windows系统的文件名以gb2312进行编码，因此这里要做一次转码
			$absolute_file_path = iconv('utf-8', 'gb2312', $absolute_file_path);
		}
		$params = array();
		$params["file"] = "@" . $absolute_file_path;

		$access_token = $this->access_token;
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token={$access_token}&type={$type}";
		$resp = $this->_upload_file($url, $params);

		return $resp;
	}

	private function _get_remote_file($url, $folder = ".", $media_id)
	{
		$isSuccess = true;
		$http_helper = new HTTP_Helper();
		$resp = $http_helper->http_send($url, "GET");
		
		$res = json_decode($resp, true);
		if(!isset($res)) {
			$destination_folder = $folder . '/'; // 文件下载保存目录，默认为当前文件目录
			if (!is_dir($destination_folder)) { // 判断目录是否存在 
		        $this->_mkdirs($destination_folder); // 如果没有就建立目录 
		    }
			$newfname = $destination_folder . $media_id;
			$newf = fopen ($newfname, "wb"); // 打开本地文件准备写入
			if ($newf) { // 如果文件保存成功 
		        fwrite($newf, $resp); // 没有写完就继续 
		    } else {
		    	$isSuccess = false;
		    }
		    if ($newf) { 
		        fclose($newf); // 关闭本地文件 
	    	} 
		} else {
			$resp = $this->_check_resp_cb_without_errcode($res, 'get_media');
			$isSuccess = false;
		}

		return $isSuccess;
	}

	// 循环创建所需的目录
	private function _mkdirs($path , $mode = "0755") { 
	    if (!is_dir($path)) { // 判断目录是否存在 
	        _mkdirs(dirname($path), $mode); // 循环建立目录   
	        mkdir($path, $mode); // 建立目录 
	    }  
	    return true; 
	}  

	// 下载多媒体文件
	public function get_media($media_id, $dir) {
		$access_token = $this->access_token;
		$url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$access_token}&media_id={$media_id}";

		$resp = $this->_get_remote_file($url, $dir, $media_id);

		return $resp;
	}

	// 被动回复文字消息
	public function send_callback_text_message($fromuser_name, $touser_openid, $content) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "text";
		$params["Content"] = $content;

		$xmlString = $this->getRespXML($params);
		echo $xmlString;
	}

	// 被动回复图片消息
	public function send_callback_image_message($fromuser_name, $touser_openid, $media_id) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "image";
		$params["Image"] = array("MediaId" => $media_id);

		$xmlString = $this->getRespXML($params);
		echo $xmlString;
	}

	// 被动回复语音消息
	public function send_callback_voice_message($fromuser_name, $touser_openid, $media_id) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "voice";
		$params["Voice"] = array("MediaId" => $media_id);

		$xmlString = $this->getRespXML($params);
		echo $xmlString;
	}

	// 被动回复视频消息
	public function send_callback_video_message($fromuser_name, $touser_openid, $media_id, $thumb_media_id) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "video";
		$params["Video"] = array(
			"MediaId" => $media_id,
			"ThumbMediaId" => $thumb_media_id
		);

		$xmlString = $this->getRespXML($params);
		echo $xmlString;
	}

	// 被动回复音乐消息
	public function send_callback_music_message($fromuser_name, $touser_openid, $music_title, $description, $music_url, $hqmusicurl, $thumb_media_id) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "music";
		$params["Music"] = array(
			"Title" => $music_title,
			"Description" => $description,
			"MusicUrl" => $music_url,
			"HQMusicUrl" => $hqmusicurl,
			"ThumbMediaId" => $thumb_media_id
		);

		$xmlString = $this->getRespXML($params);
		echo $xmlString;
	}

	// 被动回复图文消息
	public function send_callback_news_message($fromuser_name, $touser_openid, $articleCount, $articles) {
		$params = array();
		$params["ToUserName"] = $touser_openid;
		$params["FromUserName"] = $fromuser_name;
		$params["CreateTime"] = strtotime("now");
		$params["MsgType"] = "news";
		$params["ArticleCount"] = $articleCount;

		foreach ($articles as $key => $value) {
			$articles[$key] = array("item" => $value);
		}

		$params["Articles"] = $articles;
		$xmlString = $this->getRespXML($params);
		$xmlString = preg_replace("/<\/\d>/ies", "", $xmlString);
		$xmlString = preg_replace("/<\d>/ies", "", $xmlString);

		echo $xmlString;
	}

	// 将客服消息推送到微信服务器
	private function _send_custom_message($params_json_str) {
		$access_token = $this->access_token;
		
		return $this->post("message/custom/send?access_token={$access_token}", $params_json_str);
	}

	// 发送客服文字消息
	public function send_custom_text_message($touser_openid, $content) {
		$params = array();
		$params["msgtype"] = "text";
		$params["touser"] = $touser_openid;
		$params["text"] = array("content" => $content);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 发送客服图片消息
	public function send_custom_image_message($touser_openid, $media_id) {
		$params = array();
		$params["msgtype"] = "image";
		$params["touser"] = $touser_openid;
		$params["image"] = array("media_id" => $media_id);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 发送客服语音消息
	public function send_custom_voice_message($touser_openid, $media_id) {
		$params = array();
		$params["msgtype"] = "voice";
		$params["touser"] = $touser_openid;
		$params["voice"] = array("media_id" => $media_id);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 发送客服视频消息
	public function send_custom_video_message($touser_openid, $media_id, $thumb_media_id) {
		$params = array();
		$params["msgtype"] = "video";
		$params["touser"] = $touser_openid;
		$params["video"] = array("media_id" => $media_id, "thumb_media_id" => $thumb_media_id);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 发送客服音乐消息
	public function send_custom_music_message($touser_openid, $music_title, $music_url, $description, $hqmusicurl, $thumb_media_id) {
		$params = array();
		$params["msgtype"] = "music";
		$params["touser"] = $touser_openid;
		$params["music"] = array(
			"title" => $music_title,
		    "musicurl" => $music_url,
		    "hqmusicurl" => $hqmusicurl,
		    "description" => $description,
		    "thumb_media_id" => $thumb_media_id 
		);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 发送客服图文消息
	public function send_custom_news_message($articlesArray) {
		$params = array();
		$params["msgtype"] = "news";
		$params["touser"] = $touser_openid;
		$params["news"] = array("articles" => $articlesArray);
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$resp = $this->_send_custom_message($params_json_str);
		return $resp;
	}

	// 查询分组
	public function get_groups() {
		$params = array();
		$params["access_token"] = $this->access_token;

		$resp = $this->get("groups/get", $params);
		$resp = self::_check_resp_cb_without_errcode($resp, 'get_groups');

		return $resp;
	}

	// 创建分组
	public function create_group($group_name) {
		$params = array();
		$params["group"] = array();
		$params["group"]["name"] = $group_name;
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$access_token = $this->access_token;
		$resp = $this->post("groups/create?access_token={$access_token}", $params_json_str);
		$resp = self::_check_resp_cb_without_errcode($resp, "create_group");

		return $resp;
	}

	// 修改分组名
	public function update_group($group_id, $group_name) {
		$params = array();
		$params["group"] = array();
		$params["group"]["id"] = $group_id;
		$params["group"]["name"] = $group_name;
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$access_token = $this->access_token;
		$resp = $this->post("groups/update?access_token={$access_token}", $params_json_str);
		$resp = self::_check_resp_cb($resp, "update_group");

		return $resp;
	}
	
	// 移动用户分组
	public function update_group_of_user($openid, $to_groupid) {
		$params = array();
		$params["openid"] = $openid;
		$params["to_groupid"] = $to_groupid;
		$params_json_str = HTTP_Helper::_replace_unicode(json_encode($params));

		$access_token = $this->access_token;
		$resp = $this->post("groups/members/update?access_token={$access_token}", $params_json_str);
		$resp = self::_check_resp_cb($resp, "update_group_of_user");

		return $resp;
	}

	// 获取用户基本信息
	public function get_user_info($openid) {
		$params = array();
		$params["openid"] = $openid;
		$params["access_token"] = $this->access_token;

		$resp = $this->get('user/info', $params);
		$resp = self::_check_resp_cb_without_errcode($resp, 'get_user_info');

		return $resp;
	}

	// 获取关注者列表
	public function get_user_list($next_openid = NULL) {
		$params = array();
		$params["next_openid"] = $next_openid;
		$params["access_token"] = $this->access_token;

		$resp = $this->get("user/get", $params);
		$resp = self::_check_resp_cb_without_errcode($resp, 'get_user_list');

		return $resp;
	}

	// 网页授权获取用户基本信息
	// 通过code换取网页授权access_token
	public function get_OAuth_access_token($code) {
		$params = array();
		$params["code"] = $code;
		$params["appid"] = $this->appid;
		$params["secret"] = $this->secret;
		$params["grant_type"] = "authorization_code";

		$http_helper = new HTTP_Helper();
		$url = "https://api.weixin.qq.com/sns/oauth2/access_token";
		$resp = $http_helper->http_send($url, "GET", $params);
		$resp = json_decode($resp, true);
		$resp = $this->_check_resp_cb_without_errcode($resp, "get_OAuth_access_token");

		return $resp;
	}
	// 刷新OAuth_access_token
	public function refresh_OAuth_access_token($refresh_token) {
		$params = array();
		$params["appid"] = $this->appid;
		$params["grant_type"] = "refresh_token";
		$params["refresh_token"] = $refresh_token;

		$http_helper = new HTTP_Helper();
		$url = "https://api.weixin.qq.com/sns/oauth2/refresh_token";
		$resp = $http_helper->http_send($url, "GET", $params);
		$resp = json_decode($resp, true);
		$resp = $this->_check_resp_cb_without_errcode($resp, "refresh_OAuth_access_token");

		return $resp;
	}
	// 拉取用户信息(需scope为 snsapi_userinfo)，使用https协议
	public function get_snsapi_userinfo($openid) {
		$params = array();
		$params["openid"] = $openid;
		$params["access_token"] = $this->access_token;

		$http_helper = new HTTP_Helper();
		$url = "https://api.weixin.qq.com/sns/oauth2/userinfo";
		$resp = $http_helper->http_send($url, "GET", $params);
		$resp = json_decode($resp, true);
		$resp = $this->_check_resp_cb_without_errcode($resp, "get_snsapi_userinfo");

		return $resp;
	}
	// 网页授权获取用户基本信息 end

	// 创建自定义菜单
	public function create_menu($menu_arr) {
		$params = array("button" => array());

		foreach ($menu_arr as $key => $value) {
			if(is_numeric($key)) {	// 普通一级菜单
				array_push($params["button"], $value);
			} else {				// 带有二级菜单的一级菜单
				$menu = array();
				$menu["name"] = $key;
				$menu["sub_button"] = array();
				foreach ($value as $sub_key => $sub_value) {
					array_push($menu["sub_button"], $sub_value);
				}
				array_push($params["button"], $menu);
			}
		}

		$menu_json_str = HTTP_Helper::_replace_unicode(json_encode($params));
		$access_token = $this->access_token;
		if (!$access_token) {
			throw new WxSDKException("access_token null");
		}
		$resp = $this->post("menu/create?access_token={$access_token}", $menu_json_str);
		return self::_check_resp_cb($resp, "create_menu failed.");
	}

	// 删除自定义菜单
	public function delete_menu() {
		$access_token = $this->access_token;
		if (!$access_token){
			throw new WxSDKException("access_token null");
		}
		$resp = $this->get("menu/delete", array("access_token" => $access_token));
		return self::_check_resp_cb($resp, "delete_menu failed.");
	}

	// 获取自定义菜单
	public function get_menu() {
		$access_token = $this->access_token;
		if (!$access_token){
			throw new WxSDKException("access_token null");
		}
		$resp = $this->get("menu/get", array("access_token" => $access_token));
		return self::_check_resp_cb_without_errcode($resp, "get_menu failed.");
	}

	private function _get_qr_code($params) {
		$access_token = $this->access_token;
		$qrcode_json_str = HTTP_Helper::_replace_unicode(json_encode($params));
		$resp = $this->post("qrcode/create?access_token={$access_token}", $qrcode_json_str);

		return $resp;
	}

	// 获取带参数永久二维码图片的ticket
	public function get_qr_code_forever($scene_id) {
		$params = array(
			"action_name" => "QR_LIMIT_SCENE",
			"action_info" => array(
				"scene" => array(
					"scene_id" => $scene_id
				)
			)
		);

		$resp = $this->_get_qr_code($params);
		$resp = self::_check_resp_cb_without_errcode($resp, "get_qr_code_forever failed.");

		return $resp;
	}

	// 获取带参数临时二维码图片的ticket
	public function get_qr_code_temporary($scene_id, $expire_seconds) {
		$params = array(
			"expire_seconds" => $expire_seconds,
			"action_name" => "QR_SCENE",
			"action_info" => array(
				"scene" => array(
					"scene_id" => $scene_id
				)
			)
		);

		$resp = $this->_get_qr_code($params);
		$resp = self::_check_resp_cb_without_errcode($resp, "get_qr_code_temporary failed.");

		return $resp;
	}

	// 通过ticket换取二维码图片url
	public function get_qr_code_url($ticket) {
		return 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='. $ticket;
	}
}