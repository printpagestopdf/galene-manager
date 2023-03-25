<?php

require_once GALENE_PLUGIN_PATH . 'includes/class-gal-view-generator.php';
require_once GALENE_PLUGIN_PATH . 'includes/class-gal-settings.php';
require_once GALENE_PLUGIN_PATH . 'includes/class-gal-server.php';
require_once GALENE_PLUGIN_PATH . 'includes/class-gal-room.php';
require_once GALENE_DB_DRIVER;


use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Blowfish;

class Gal_Shortcode {
	const FORM_ACTIONS=array(
		"admin_update_room",
		"admin_update_settings",
		"admin_update_user",
		"admin_update_userselect",
		
	);

	protected static $instance = NULL;

	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new static();
		return self::$instance;
	}
	
	public function __construct() {
		add_shortcode( 'galene_main',[ $this, "galene_main"] );
	}
	
	private function enqueue()
	{
		add_action('wp_enqueue_scripts', function(){
		   wp_enqueue_script('galene_js');

		   wp_enqueue_style( 'galene_fw_style' );
		   wp_enqueue_style( 'galene_fw_list_style' );
		   wp_enqueue_style( 'galene_fw_cc_style' );
		   wp_enqueue_style( 'galene_style' );
		});
	}
	
	function render_main($msgs=null)
	{
		global $wp;
		$this->enqueue();		
		$rooms=Gal_Room::get_rooms(true);

		ob_start();
		
		Gal_View_Generator::view("roomlist.twig", [
			"rooms" => $rooms,
			"d" => ($msgs != null)?array( 'msg' => $msgs ):array(),
		],GALENE_PLUGIN_PATH . 'views/');
	
		return ob_get_clean();
		
	}
	
	function render_auth_ui($room,$parts, $presets=array())
	{
		switch(@$presets['g_type'])
		{				
			case "presenter": //presentation
				$presets[ 'g_type_disp' ]=__("Presenter",'galene-mgr');
				break;
			
			case "op": //operator
				$presets[ 'g_type_disp' ]= __("Operator",'galene-mgr');
				break;			
			
			case "other": // others
				$presets[ 'g_type_disp' ]=__("Listener",'galene-mgr');
				break;
		}
		
		$this->enqueue();
		ob_start();
		
		Gal_View_Generator::view("user_auth.twig", [
			"room" => $room,
			"parts" => $parts,
			"d" => $presets,
		],GALENE_PLUGIN_PATH . 'views/');
		
		return ob_get_clean();		
	}
	
	
	function check_auth($room, $args)
	{
		$displayName=@$args['galene_login'];
		$needsAuth=false;
		switch($args['g_type'])
		{
			case "other": // others
				$needsAuth=(!empty(@$room->auth_for_other))?'is_other':false;
				break;
				
			case "presenter": //presentation
				$needsAuth=(!empty(@$room->auth_for_presentator))?'is_presenter':false;
				break;
			
			case "op": //operator
				$needsAuth=(!empty(@$room->auth_for_operator))?'is_operator':false;
				break;			
		}
		
		if($needsAuth !== false)
		{
			if( is_user_logged_in()  ) {
				$curUser=wp_get_current_user();
				if(Gal_DB_Driver::inst()->has_wpuser_access($room->id,$needsAuth,$curUser)) {
					if(!empty(@$curUser->user_nicename))
						$displayName=$curUser->user_nicename;
					elseif(!empty(@$curUser->display_name))
						$displayName=$curUser->display_name;
					else
						$displayName=$curUser->user_login;

					return [ true, $displayName ];
				}
			}

			if(empty(@$args['galene_password']) || empty(@$args['galene_login'])) 
			{
				return [false, $this->render_auth_ui($room, [ "name",  "password" ],$args) ];
			}
			else
			{
				if ( ! wp_verify_nonce( @$args['gal_form_id'], "access_room" ) ) {
					wp_die( __( 'There is an error in submitted data', 'galene-mgr' ),__( 'Data error', 'galene-mgr' ) ); 
				}
	
				$user=Gal_DB_Driver::inst()->get_user_by_login($args['galene_login']);
				if( $user === false || !password_verify( $args['galene_password'],$user["password"]))
				{
					$args["msg"]=[ self::error(__("Error in login data",'galene-mgr')) ];
					return [false, $this->render_auth_ui($room, [ "name",  "password" ],$args) ];
				}
				
				if($room->has_access($user['id'],$needsAuth) === false)
				{
					$args["msg"]=[ self::error(__("This login is not registered for the requested role",'galene-mgr')) ];
					return [false, $this->render_auth_ui($room, [ "name",  "password" ],$args) ];
				}
				$displayName=$user['displayName'];

			}			
		}
		else
		{
			$askFor=array();
			if((!empty(@$args['galene_code']) || !empty(@$args['galene_login'])) && !wp_verify_nonce( @$args['gal_form_id'], "access_room" )) {
				wp_die( __( 'There is an error in submitted data', 'galene-mgr' ),__( 'Data error', 'galene-mgr' ) ); 
			}

			if(!empty(@$room->needs_code) && @$args['galene_code'] !== @$room->room_accesscode )
			{
				if(!empty($args['galene_code']))
					$args["msg"][]=self::error(__("Wrong code",'galene-mgr') );
				$askFor=[ "code", "name" ];
			}
			
			if(!empty(@$room->needs_nickname) && empty(@$args['galene_login']))
			{
				$askFor=[ "code", "name" ];
			}
			
			if(!empty($askFor))
				return [false, $this->render_auth_ui($room, $askFor,$args) ];
		}
		
		return [ true, $displayName ];
	}
	
	function check_admin_auth($args)
	{
		$login_error=false;
		if(!empty($args['galene_login']) && !empty($args['galene_password']))
		{
			if ( ! wp_verify_nonce( @$args['gal_form_id'], "admin_auth" ) ) {
				wp_die( __( 'There is an error in submitted data', 'galene-mgr' ),__( 'Data error', 'galene-mgr' ) ); 
			}
		
			$user=Gal_DB_Driver::inst()->get_user_by_login($args['galene_login']);
			if($user !== false && $user['isAdmin'] == 1 && password_verify( $args['galene_password'],$user["password"])) 
			{
				$_SESSION['galene_user']=$user['id'];
				$_SESSION['galene_is_logged_in']=true;
				session_write_close();
			}
			else
				$login_error=true;
		}
		if( empty(@$_SESSION['galene_user']) || @$_SESSION['galene_is_logged_in'] !== true)
		{
			if( is_user_logged_in()  ) {
				$curUser=wp_get_current_user();
				if ( in_array( GALENE_WP_ROLE_NAME, (array) $curUser->roles ) ) {
					$_SESSION['galene_user']=-1 * abs($curUser->ID); //negative id for wp user
					$_SESSION['galene_is_logged_in']=true;
					session_write_close();

					return true;
				}
			}

			$this->enqueue();
			ob_start();
			
			$params=[
				"presets" => $args,
				"action" => @$args['galene_action'],
			];
			if($login_error) 
			{
				$params['d']=array( 'msg' => [ self::error(__("Error in login data",'galene-mgr')) ] );
			}
		
			Gal_View_Generator::view("admin_auth.twig", $params,GALENE_PLUGIN_PATH . 'views/');
			
			return ob_get_clean();					
		}
		
		return true;
	}
	
	function galene_admin_main($args)
	{
		remove_query_arg('active_tab');
		
		$auth_check=$this->check_admin_auth($args);
		if($auth_check !== true) return $auth_check;
		
		if(strpos(@$args['galene_action'], 'admin_screen_') === 0) 
		{
			$d=array();
			if(!empty($args['msg'])) $d['msg'] = $args['msg'];
			
			switch($args['galene_action'])
			{
				case 'admin_screen_roomedit':
					if(@$args['galene_subaction'] == 'new_room')
					{
						// $room=json_decode(file_get_contents(GALENE_PLUGIN_PATH . "views/roomtpl_{$args['new_room_preset']}.json"),true);
						$room=json_decode(file_get_contents(GALENE_PLUGIN_PATH . "views/roomtpl_{$args['new_room_preset']}.json"));
						$room->key64=Gal_util::secret_h256_base64();
						if(@$room->access == "code")
							$room->room_accesscode= sprintf("%06d",random_int(1, 999999));
						 $args['galene_room']=-1;
						$d['room'] = $room;
					}
					else
					{
						$d['room'] = new Gal_Room($args['galene_room']);
						$d['local_url']=Gal_util::host_url() . Gal_util::add_arg([
								"galene_room" => $args['galene_room'] ,
								"galene_action" => 'access_room',
							]);
						$d['room_urls']=Gal_Room::room_urls($d['room']->id);

						$d['iframe_src']=Gal_util::add_arg([ 'galene_action' => 'admin_screen_userselect', 'galene_room' => $args['galene_room'] ]);
						$d['users'] = $d['room']->get_access(false);
						$d['active_tab']=@$args['active_tab']?$args['active_tab']:'settings-tab';
					}
					
					$d['form_classes'] = ($d['room']->access == 'code')?" is-access-code ":"";
					break;
					
				case 'admin_screen_useredit':
					if(empty(@$args['galene_user'])) {
						$d['user'] = array( 'id' => -1, 'displayName' => '', 'login' => '', );
					}
					else {
						$d['user'] = Gal_DB_Driver::inst()->get_user($args['galene_user']);		
					}
					break;
					
				case 'admin_screen_roomsettings':
					$d['admin_screen_roomsettings']=" is-active ";
					// $gs=new GaleneStorage();
					// $d['rooms'] = $gs->get_rooms();
					$d['rooms'] = Gal_Room::get_rooms();
					break;
									
				case 'admin_screen_userselect':
					$d['users'] = Gal_Room::get_room_access($args['galene_room']);
					$d['room']=$args['galene_room'];
					$d['room_display_name']=@$args['room_display_name'];
					break;
					
				case 'admin_screen_usersettings':
					$d['admin_screen_usersettings']=" is-active ";
					$d['users'] = Gal_DB_Driver::inst()->get_users();
					break;
				
				case 'admin_screen_settings':
					$d['settings']=Gal_Settings::inst()->get_settings();
					break;
				
				default:
					$d['action']=empty(@$args['galene_action'])?"none":$args['galene_action'];
					$d[$d['action']]=" is-active ";
					break;
			}
			
			if(!empty(@$args['msgid']))
			{
				if ( false !== ( $tMsgs = get_transient( $args['msgid'] ) ) ) {
					delete_transient(  $args['msgid'] );
					$d['msg']=(!empty(@$d['msg']))?array_merge($d['msg'],$tMsgs):$tMsgs;
				}				
			}
			
			$template="admin_" . substr($args['galene_action'],13) . ".twig";
			$this->enqueue();
			ob_start();

			Gal_View_Generator::view($template, [
				"args" => $args,
				"d" => $d,
				"is_admin" => true,
			],GALENE_PLUGIN_PATH . 'views/');
			
			return ob_get_clean();					
		
		}
		
		switch(@$args['galene_action'])
		{
			case "admin_update_room":
				$msgs=array();
				
				try {
					if($args['galene_room'] < 0)
					{
						$prevRoomJSON=null;
						$room=new Gal_Room($args);
						$room->store();
						$args['galene_room']=$room->id;
					}
					else
					{
						$prevRoomJSON=(new Gal_Room($args['galene_room']))->get_json();
						$room=new Gal_Room($args);
						$room->store();
					}

					$msgs[]=self::success( __("Data successfully saved",'galene-mgr') );

					if(Gal_Server::inst()->can_write)
					{
						$r=$room->get_json();
						
						if($prevRoomJSON == null || $prevRoomJSON != $r)
						{
							if(Gal_Server::inst()->put_file($r->filename,$r->json) !== false)
								$msgs[]=self::success(__("Data succesfully saved on Galène server",'galene-mgr'));
							else
								$msgs[]=self::error(__("Error saving data on Galène server",'galene-mgr'));
						}
					}
				} catch( Exception $e) {

					$msgs[]=self::error($e->getMessage());
					$msgs[]=self::error(__("Error saving Data, nothing saved!",'galene-mgr'));
				}
				
				return $this->galene_admin_main([
					'galene_action' => 'admin_screen_roomedit',
					'galene_room' => $args['galene_room'],
					'msg' => $msgs,
				]);
				break;
			
			case "admin_room_delete":
					$msgs=array();
					$room=new Gal_Room($args['galene_room']);
					$serverFileName=$room->serverFileName;
					if($room->delete($args['galene_room']) !== false)
					{
						$msgs[]=self::success(__("Room deleted from manager Database",'galene-mgr'));
						if(Gal_Server::inst()->delete_file($serverFileName) !== false)
							$msgs[]=self::success(__("Room config on Galène server successfully deleted",'galene-mgr'));
						else
							$msgs[]=self::error(__("Error deleting room config from Galène server",'galene-mgr'));
					}
					else
						$msgs[]=self::error(__("Error deleting room from manager Database",'galene-mgr'));
					
					$msgid=uniqid('roomsettings');
					set_transient($msgid,$msgs,60);

					$url=Gal_util::set_query_args( ['galene_action' => 'admin_screen_roomsettings', 'msgid' => $msgid ] );
					wp_redirect($url,303);
					exit;
					break;
								
			case "admin_user_delete":
					$msgs=array();

					if(Gal_DB_Driver::inst()->delete_user($args['galene_user']) !== false)
					{
						$msgs[]=self::success(__("Successfully deleted user from Database",'galene-mgr'));
					}
					else
						$msgs[]=self::error(__("Error deleting user from Database",'galene-mgr'));

					$msgid=uniqid('usersettings');
					set_transient($msgid,$msgs,60);

					$url=Gal_util::set_query_args( ['galene_action' => 'admin_screen_usersettings', 'msgid' => $msgid ] );
					wp_redirect($url,303);
					exit;
					break;
								
			case "admin_update_user":
				if(!empty($args['password_new']) )
				{
					if($args['password_new'] != $args['password_repeat'])
					{
						return $this->galene_admin_main([
							'galene_action' => 'admin_screen_useredit',
							'galene_user' => $args['galene_user'],
							'msg' => [ self::error(__("Password repeat does not match Password",'galene-mgr')) ],
						]);
					}
					else
					{

						$args['password'] = password_hash($args['password_new'], PASSWORD_DEFAULT);
						
					}
				}

				if($args['galene_user'] < 0 ) //new user
					$args['galene_user']=Gal_DB_Driver::inst()->insert_user($args);
				else
					Gal_DB_Driver::inst()->update_user($args);
				
				return $this->galene_admin_main([
					'galene_action' => 'admin_screen_useredit',
					'galene_user' => $args['galene_user'],
					'msg' => [ self::success(__("Data successfully saved",'galene-mgr')) ],
				]);
				break;
				
			case "admin_update_userselect":
				if(!empty(@$args['acc']))
					Gal_Room::set_room_access( $args['galene_room'],$args['acc']);
				else
					Gal_Room::set_room_access( $args['galene_room'],array());

				$msgs=array(self::success(__("Userlist successfully updated",'galene-mgr')));
				$msgid=uniqid('userselect');
				set_transient($msgid,$msgs,60);

				$url=Gal_util::set_query_args( ['galene_action' => 'admin_screen_roomedit', 
											'galene_room' => $args['galene_room'], 
											'active_tab' => 'privileges-tab',
											'msgid' => $msgid ] ) . '#userlist';
				wp_redirect($url,303);
				exit;

				return $this->galene_admin_main([
					'galene_action' => 'admin_screen_userselect',
					'galene_room' => $args['galene_room'],
					'msg' => [ self::success(__("Data successfully saved",'galene-mgr') ) ],
				]);
				break;
				
			case "admin_action_logout":
				unset($_SESSION['galene_user']);
				unset($_SESSION['galene_is_logged_in']);
				session_write_close();
				return $this->render_main();
				break;
			
			case "admin_download_room_json":
				$r=(new Gal_Room($args['galene_room']))->get_json();
				ob_clean();
				echo $r->json;
				
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="'. $r->filename .'"');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Length: ' . ob_get_length());
				flush(); // Flush system output buffer
				die();
				
				break;
				
			case "admin_update_settings":
				// error_log(print_r($_REQUEST,true));
				Gal_Settings::inst()->update($args);
				
				return $this->galene_admin_main([
					'galene_action' => 'admin_screen_settings',
					'msg' => [ self::success(__("Data successfully saved",'galene-mgr')) ],
				]);				
				break;
				
			case "admin_test_galene_access":
				ob_clean();
				$result=Gal_Server::inst()->test_connection();
				echo <<< HEAD
				<html>
				<head>
				<style>
					table, th, td {
					  border: 1px solid black;
					  border-collapse: collapse;
					}
					table { width: 80%;  margin-left: auto; margin-right: auto; }
					.center { text-align: center; }
				</style>
				</head>
				<body>
				<h2 class="center">Connection Test Result</h2>
				<table><tbody>
HEAD;

				foreach($result as $line)
				{
					echo "<tr><td>{$line[0]}</td><td>{$line[1]}</td></tr>";
				}
				echo "</tbody></table></body></html>";
				ob_end_flush();
				die();
				
			default:
				return $this->render_main();
				break;
		
		}
		
	}
	
	
	function galene_main( $attrs, $SCcontent = null ) {
		$a = shortcode_atts( array(), $attrs );
		
		if(!empty(@$_REQUEST['galenc']))
		{
			// $enc_args=json_decode(Gal_util::base64url_decode($_REQUEST['galenc']),true);
			$enc_args=Gal_util::decode_url_param($_REQUEST['galenc']);
			$req_args=array_merge($enc_args,$_POST);			
		}
		else
			$req_args=$_REQUEST;
		
		if(in_array(@$req_args['galene_action'], self::FORM_ACTIONS)) {
			if ( ! wp_verify_nonce( @$req_args['gal_form_id'], @$req_args['galene_action'] ) ) {
				wp_die( __( 'There is an error in submitted data', 'galene-mgr' ),__( 'Data error', 'galene-mgr' ) ); 
			}			
		}

		if(strpos(@$req_args['galene_action']?:'', 'admin_') === 0) return $this->galene_admin_main($req_args);
		
		switch(@$req_args['galene_action'])
		{			
			case "access_room":
				$settings=Gal_Settings::inst()->get_settings();

				$room=new Gal_Room($req_args['galene_room']);

				if($room === false) return $this->render_main([ self::error(__("This room does not (longer) exist",'galene-mgr')) ]);

				list($auth_check, $content)=$this->check_auth($room,$req_args);
				
				if($auth_check !== true)
					return $content;
				
				switch($req_args['g_type'])
				{
						
					case "presenter": //presentation
						$perms=[ 'present' ];
						break;
					
					case "op": //operator
						$perms=[ 'op', 'record', 'present' ];
						break;			
					
					case "other": // others
					default:
						$perms=[ 'other' ];
						break;
				}
				
				$nickname=(empty(@$room->needs_nickname) && empty(@$content))?Gal_util::random_name():@$content;
				
				$room_auth=Gal_util::get_room_auth_link(
					trailingslashit(trailingslashit($settings['general']['galene_url']) . $room->galene_group),
					['sub' => $nickname, 'permissions' => $perms ],
					$room->key64, $settings	);
				
				ob_end_clean();
				wp_redirect($room_auth,303);
				exit;
				break;
			
			default:
				return $this->render_main();
				break;
		}
		
		
		return "Sorry something went wrong";						
		
	}
	
	// private function secret_h256_base64()
	// {
	// 	$secret = random_bytes(32);
	// 	return Gal_Util::base64url_encode($secret);
	// }
	
	// private function simple_token($ar_header,$ar_payload,$secret)
	// {
	// 	// Create token header as a JSON string
	// 	$header = json_encode($ar_header);

	// 	// Create token payload as a JSON string
	// 	$payload = json_encode($ar_payload);

	// 	// Encode Header to Base64Url String
	// 	$base64UrlHeader = Gal_Util::base64url_encode($header);

	// 	// Encode Payload to Base64Url String
	// 	$base64UrlPayload = Gal_Util::base64url_encode($payload);

	// 	// Create Signature Hash
	// 	$signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);

	// 	// Encode Signature to Base64Url String
	// 	$base64UrlSignature = Gal_util::base64url_encode($signature);

	// 	// Create JWT
	// 	$jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

	// 	return $jwt;
		
	// }
		
	public static function success($msg)
	{
		return self::msg("",$msg,"is-success");
	}
	
	public static function error($msg)
	{
		return self::msg("",$msg,"is-danger");
	}
	
	
	public static function msg($msg,$title="Mitteilung", $type="is-success")
	{
		return array(
			"title" => $title,
			"type" => $type,
			"content" => $msg,
			);
	}
			
} //class Gal_Shortcode

Gal_Shortcode::get_instance();