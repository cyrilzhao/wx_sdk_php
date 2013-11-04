<?php

/**
 * 微信第三方接口SDK
 * @author raphealguo
 */
class WxSDKException extends Exception {
	public function __construct($msg = 'WxSDKException'){
	}
}

class WxSDK{

	public static $boundary = '';

	private $host = 'https://api.weixin.qq.com/cgi-bin/';
	
	private $appid = '';
	
	private $secret = '';


	public $timeout = 30;

	public $connecttimeout = 30;

	public $ssl_verifypeer = FALSE;

	public $useragent = 'WeiXin SDK 1.0.0';

	private $access_token = null;

	private $expires_in = 0;

	public function __construct($appid = '', $secret = ''){
		$this->appid = $appid;
		$this->secret = $secret;
		$this->get_access_token();
	}

	//用于开发者模式下校验来自公众平台的token参数
	private function _checkSignature($signature, $timestamp, $nonce, $token){
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
	public function valid_token($echostr, $signature, $timestamp, $nonce, $token){
		if ($this->_checkSignature($signature, $timestamp, $nonce, $token)){
			echo $echostr;
		}
		exit();
	}
	//用于开发者模式下校验来自公众平台的token参数 end

	//微信不支持\uxxxx的UNICODE模式，所以需要转成中文！
	private static function _replace_unicode($str) {
		return preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $str); 
	}

	public static function build_http_query_multi($params) {
		if (!$params) return '';

		uksort($params, 'strcmp');

		$pairs = array();

		self::$boundary = $boundary = uniqid('------------------');
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

	function get_http_header($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}

	public function http_send($url, $method, $postfields = NULL, $headers = array()) {
		$this->http_info = array();
		$ci = curl_init();

		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer);
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

	function do_request($cgi, $method, $parameters, $multi = false) {

		if (strrpos($cgi, 'https://') !== 0 && strrpos($cgi, 'https://') !== 0) {
			$cgi = "{$this->host}{$cgi}";
		}

		switch ($method) {
			case 'GET':
				$cgi = $cgi . '?' . http_build_query($parameters);
				return $this->http_send($cgi, 'GET');
			default:
				$headers = array();
				if (!$multi){
					if ((is_array($parameters) || is_object($parameters)) ) {
						$body = http_build_query($parameters);
					}else{
						$body = $parameters;
					}
				} else {
					$body = self::build_http_query_multi($parameters);
					$headers[] = "Content-Type: multipart/form-data; boundary=" . self::$boundary;
				}
				return $this->http_send($cgi, $method, $body, $headers);
		}
	}
	function get($url, $parameters = array(), $format = 'json') {
		$resp = $this->do_request($url, 'GET', $parameters);
		if ($format === 'json') {
			return json_decode($resp, true);
		}
		return $resp;
	}

	/**
	 * POST wreapper for oAuthRequest.
	 *
	 * @return mixed
	 */
	function post($url, $parameters = array(), $multi = false, $format = 'json') {
		$resp = $this->do_request($url, 'POST', $parameters, $multi );

		if ($format === 'json') {
			return json_decode($resp, true);
		}
		return $resp;
	}

	private static function _inner_resp_cb($resp, $excepition_msg){
		if (is_array($resp) && isset($resp['errcode']) && $resp['errcode'] == 0) {
			return $resp;
		} else {
			throw new WxSDKException($excepition_msg . " msg : " . $resp['errmsg'], ' code :' . $resp['errcode']);
		}
	}
	private static function _inner_resp_cb_without_errcode($resp, $excepition_msg){
		if (is_array($resp) && !isset($resp['errcode'])) {
			return $resp;
		} else {
			throw new WxSDKException($excepition_msg . " msg : " . $resp['errmsg'], ' code :' . $resp['errcode']);
		}
	}

	//获取access token
	public function get_access_token(){
		$params = array();
		$params['grant_type'] = 'client_credential';
		$params['appid'] = $this->appid;
		$params['secret'] = $this->secret;

		$resp = $this->get('token', $params);
		//var_dump($token);
		$resp = self::_inner_resp_cb_without_errcode($resp, 'get_access_token');
		$this->access_token = $resp['access_token'];
		$this->expires_in = $resp['expires_in'];

		return $resp;
	}
	
	//自定义菜单接口封装
	//自定义菜单创建
	/**
	 * DEMO
	 *	$sdk = new WxSDK('YOUR APPID', 'YOUR SECRET');
	 *	$resp = $sdk->create_menu(
	 *		array(
	 *				array(
	 *						"type" => "click",
	 *						"name" => "今aa日歌曲1",
	 *						"key" => "V1001_TODAY_MUSIC",
	 *					),
	 *
	 *				array(
	 *						"type" => "view",
	 *						"name" => "跳v1转2",
	 *						"url" => "http://www.qq.com",
	 *					)
	 *			)
	 *		);
	 *
	 */
	public function create_menu($menu_arr){
		$menu_json_str = self::_replace_unicode(json_encode(array("button"=>$menu_arr)));

		$access_token = $this->access_token;
		if (!$access_token){
			throw new WxSDKException("access_token null");
		}
		$resp = $this->post("menu/create?access_token={$access_token}", $menu_json_str);
		return self::_inner_resp_cb($resp, "create_menu failed.");
	}

	//自定义菜单删除
	/**
	 * DEMO
	 *	$sdk = new WxSDK('YOUR APPID', 'YOUR SECRET');
	 *	$resp = $sdk->del_menu();
	 *
	 */
	public function del_menu(){
		$access_token = $this->access_token;
		if (!$access_token){
			throw new WxSDKException("access_token null");
		}
		$resp = $this->get("menu/delete", array("access_token"=>$access_token));
		return self::_inner_resp_cb($resp, "del_menu failed.");
	}

	//获取自定义菜单
	/**
	 * DEMO
	 *	$sdk = new WxSDK('YOUR APPID', 'YOUR SECRET');
	 *	$resp = $sdk->get_menu();
	 *
	 */
	public function get_menu(){
		$access_token = $this->access_token;
		if (!$access_token){
			throw new WxSDKException("access_token null");
		}
		$resp = $this->get("menu/get", array("access_token"=>$access_token));
		var_dump($resp);
		return self::_inner_resp_cb($resp, "get_menu failed.");
	}

	//获取永久二维码图片的URL
	public function get_qr_code($qrcode_arr){
		$access_token = $this->access_token;
		$qrcode_json_str = self::_replace_unicode(json_encode($qrcode_arr));

		$resp = $this->post("qrcode/create?access_token={$access_token}", $qrcode_json_str);
		var_dump($resp);
		$resp = self::_inner_resp_cb_without_errcode($resp, "get_qr_code failed.");

		$ticket = $resp['ticket'];
		return array(
			'resp' => $resp,
			"url" => "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket={$ticket}"
		);
	}
}