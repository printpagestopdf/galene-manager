<?php
	
class Galmgr_util {

	public static function translate_wprole($wpRoleName)
	{
		return translate_user_role($wpRoleName);
	}
	
	public static function get_public_props($obj)
	{
		return get_object_vars($obj);
	}

	public static function add_arg($args)
	{
		$enc_args=array_map(function($val){ return rawurlencode($val); },$args);
		return esc_url(add_query_arg($enc_args,remove_query_arg(['galene_user','active_tab', 'galene_room','galene_action','room_display_name', 'msgid'])));
	}
	
	public static function set_query_args($args)
	{
		global $wp;
		return add_query_arg($args, home_url( $wp->request ));		
	}

	public static function encode_url_param_ar($host, $args )
	{
		if(@$args['g_type'] == 'op')
			$g_type=4;
		elseif(@$args['g_type'] == 'presenter')
			$g_type=2;
		elseif(@$args['g_type'] == 'other')
			$g_type=1;
		else
			$g_type=0;
		
		return $host . self::add_arg([
				'galenc' =>  self::base64url_encode("{$args['galene_room']}:{$g_type}"),
			]);
		
		return $host . self::add_arg([
				'galenc' =>  self::base64url_encode(json_encode($args)),
			]);
		
	}
		
	public static function decode_url_param($par)
	{
		$str=self::base64url_decode($par);
		
		$vals=explode(":",$str);
		$ret=array(
			"galene_room" => $vals[0] ,
			"galene_action" => 'access_room',		
		);
			
		if($vals[1] == 4)
			$ret["g_type"]="op";
		elseif($vals[1] == 2)
			$ret["g_type"]="presenter";
		elseif($vals[1] == 1)
			$ret["g_type"]="other";
			
		return $ret;
	}
	
	public static function host_url()
	{
	  $protocol = (!empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on' || $_SERVER['HTTPS'] == '1')) ? 'https://' : 'http://';
	  $server = $_SERVER['SERVER_NAME'];
	  if (!empty(@$_SERVER['SERVER_PORT']) && !in_array($_SERVER['SERVER_PORT'], [80, 443]))
		  $port =  ':'.$_SERVER['SERVER_PORT'];
	  else
		  $port = '';
	  
	  return $protocol.$server.$port;
	}		

	public static  function base64url_encode($data) {
	  return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
	}

	public static  function base64url_decode($data) {
	  return base64_decode(str_pad(strtr($data, '-_', '+/'), 4 - ((strlen($data) % 4) ?: 4), '=', STR_PAD_RIGHT));
	}

	public static function secret_h256_base64()
	{
		$secret = random_bytes(32);
		return self::base64url_encode($secret);
	}

	public static function simple_token($ar_header,$ar_payload,$secret)
	{
		// Create token header as a JSON stringGal_
		$header = json_encode($ar_header);

		// Create token payload as a JSON string
		$payload = json_encode($ar_payload);

		// Encode Header to Base64Url String
		$base64UrlHeader = self::base64url_encode($header);

		// Encode Payload to Base64Url String
		$base64UrlPayload = self::base64url_encode($payload);

		// Create Signature Hash
		$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);

		// Encode Signature to Base64Url String
		$base64UrlSignature = self::base64url_encode($signature);

		// Create JWT
		$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

		return $jwt;
		
	}

	public static function get_room_auth_link($roomUrl,$payload, $secret64, $settings)
	{
		$payload += [
			'aud' => $roomUrl,
			'iat' => time() - (int)@$settings['general']['galene_issued_at']?:0,
			'exp' => time() + (int)@$settings['general']['galene_token_exp']?:30,
			'iss' => get_home_url(),
			];
		
		$secret=self::base64url_decode($secret64);
		$token=self::simple_token(['typ' => 'JWT', 'alg' => 'HS256', 'kid' => GALMGR_ROOM_AUTH_KID ],$payload,$secret);	
		
		return $roomUrl . "?token=" . $token;
	}		

	public static function generate_jwt()
	{
		// $secret = bin2hex(random_bytes(16));
		$secret = random_bytes(32);
		$secret_encoded=self::base64url_encode($secret);
		
		return $secret_encoded;
	}
	
	public static function random_name($for_surname =false) {
		require_once "random-name-generator/class-galmgr-name-generator.php";
		if($for_surname) {
			$r = new Galmgr_name_generator('array');
			$names = $r->generateNames(1);
			return $names[0];
		}
		else {
			$r = new Galmgr_name_generator('associative_array');
			$names = $r->generateNames(1);
			return $names[0]['first_name'];
		}
	}
}