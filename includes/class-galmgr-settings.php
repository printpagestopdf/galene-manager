<?php

use phpseclib3\Crypt\Blowfish;

require_once GALMGR_DB_DRIVER;


class Galmgr_Settings {
	
	private static $instance = null;

	public static function inst()
	{
		if(null === self::$instance)
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function __construct() {
	}

	public function update($args)
	{
		
		$da=(!empty(@$args['direct_server_access']) == 'sftp')?$args['direct_server_access']:null;
		$res=Galmgr_DB_Driver::inst()->upsert_setting('direct_server_access',null,$da, 'none');
		
		if(array_key_exists('sftp',$args))
		{
			if(!empty(@$args['sftp']['password_new']) && $args['sftp']['password_new'] == @$args['sftp']['password_repeat'])
				$res=Galmgr_DB_Driver::inst()->upsert_setting('sftp_password',self::blowfish_crypt($args['sftp']['password_new']),"password", "raw");	
			
			if(!empty(@$args['sftp']['key_password_new']) && $args['sftp']['key_password_new'] == @$args['sftp']['key_password_repeat'])
				$res=Galmgr_DB_Driver::inst()->upsert_setting('sftp_password',self::blowfish_crypt($args['sftp']['key_password_new']),"key_password", "raw");	
			
			unset($args['sftp']['password_new']);
			unset($args['sftp']['password_repeat']);
			unset($args['sftp']['key_password_new']);
			unset($args['sftp']['key_password_repeat']);

			$res=Galmgr_DB_Driver::inst()->upsert_setting('sftp',$args['sftp'],null, null);
		}
		
		if(array_key_exists('filesystem',$args))
		{
			$res=Galmgr_DB_Driver::inst()->upsert_setting('filesystem',$args['filesystem'],null, null);			
		}

		if(array_key_exists('general',$args))
		{
			$res=Galmgr_DB_Driver::inst()->upsert_setting('general',$args['general'],null, null);			
		}

		if(array_key_exists('sftp-keyfile',$_FILES) && file_exists($_FILES["sftp-keyfile"]["tmp_name"]))
		{
			$pubkey=file_get_contents($_FILES["sftp-keyfile"]["tmp_name"]);
			$pubkey_enc=self::blowfish_crypt($pubkey);
			
			$res=Galmgr_DB_Driver::inst()->upsert_setting('sftp_pubkey',$pubkey_enc,null, 'raw');
		}
		
		return $res;
	}
	
	public function get_settings()
	{
		$recs= Galmgr_DB_Driver::inst()->get_all_settings();

		$sftp_password=null;
		$result=array();
		foreach($recs as $rec)
		{
			if(empty($rec['type']))
			{
				$result[$rec['setting_key']]=json_decode($rec['data'],true);
												
			}
			elseif($rec['type'] == 'raw') {
				if($rec['setting_key'] == 'sftp_password')
					$sftp_password=$rec;
				else
					$result[$rec['setting_key']]=$rec['data'];
			}
			elseif($rec['type'] == 'none')
			{
				$result[$rec['setting_key']]=$rec['setting_subkey'];
				if($rec['setting_key'] == 'sftp_pubkey')
					$result[$rec['setting_key']]=self::blowfish_crypt($result[$rec['setting_key']],true);
			}
		}
		
		if(!empty(@$rec['sftp']) && $sftp_password != null) {
			$rec['sftp'][$sftp_password['setting_subkey']]=self::blowfish_crypt($sftp_password['data'],true);
		}
		
		
		return $result;
	}
	
	public function get_setting($key)
	{
		$rec=Galmgr_DB_Driver::inst()->get_setting($key);
		
		if(empty($rec['type']))
		{
			$result=json_decode($rec['data'],true);
			
			if($key == 'sftp') {
				$sftp_password=Galmgr_DB_Driver::inst()->get_setting('sftp_password');
				$result[$sftp_password['setting_subkey']]=self::blowfish_crypt($sftp_password['data'],true);
			}
		}
		elseif($rec['type'] == 'raw')
			$result=$rec['data'];
		elseif($rec['type'] == 'none')
			$result=$rec['setting_subkey'];
		
		if($key == 'sftp_pubkey')
			$result=self::blowfish_crypt($result,true);
		
		return $result;
	}
	
	public function get_server_access()
	{
		$srv=$this->get_setting('direct_server_access');
		
		return $srv;
	}
		
	public static function blowfish_crypt($data,$decrypt=false)
	{
		if (!defined('GALMGR_CRYPT_KEY')) {
			return $data;
		}
		$bf = new Blowfish('ecb');
		// $blowfish =new \phpseclib3\Crypt\Blowfish('ctr');
		$bf->setKey(base64_decode(GALMGR_CRYPT_KEY));

		if($decrypt)
		{
			$result = $bf->decrypt(base64_decode($data));
		}
		else
		{
			$result =base64_encode($bf->encrypt($data));
		}

		return $result;
		
		
	}
		
}