<?php

use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;

class Galmgr_Sftp_Driver {
	
	private static $instance = null;
	private $srv=null;
	private $sftp=null;
	public $last_error="";
	public $is_logged_in=false;
	public $can_write=false;
	public $can_read=false;

	public static function inst($settings)
	{
		if(null === self::$instance)
		{
			self::$instance = new self($settings);

		}
		return self::$instance;
	}
	
	private function __construct($settings) {
		$this->srv=$settings->get_setting('sftp');
		$this->srv['server_path']=trailingslashit($this->srv['server_path']);
		
		$res=$this->login($settings);
		if($res === true)
		{
			$this->is_logged_in=true;
			$this->can_write=true;
			$this->can_read=true;
			
		}
		elseif($res === false)
		{
			$this->last_error="Login failed";
			$this->is_logged_in=false;
		}
		else
		{
			$this->last_error=$res;
			$this->is_logged_in=false;
		}
		
	}
	
	public function put_file($fname,$content="",$path=null)
	{
		try {
			$ret=$this->sftp->put($this->srv['server_path'] . $fname, $content);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
	}
	
	public function get_file($fname,$path=null)
	{
		try {
			$ret=$this->sftp->get($this->srv['server_path'] . $fname);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
		
	}
	
	public function delete_file($fname,$path=null)
	{
		try {
			$ret=$this->sftp->delete($this->srv['server_path'] . $fname);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
	}
	
	public function list_files($path=null)
	{
		try {
			$files=$this->sftp->nlist($this->srv['server_path']);
			$files=array_filter($files,function($file) {
				return ($file != "." && $file != "..");
			});
			
			return $files;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;
	}
	
	
	
	public function test_connection()
	{
		$result=array();
		
		try {
			if($this->is_logged_in)
				$result[]=["LOGIN","SUCCESS"];
			else
			{
				$result[]=["LOGIN",$this->last_error];
				throw new Exception("ERROR");
			}
			
			$serverPath=$this->srv['server_path'];
			$result[]= [ "Server Path: <br> {$serverPath}",($this->sftp->file_exists($serverPath)?' FOUND':' NOT FOUND') ];

			$files=$this->list_files();
			if(count($files) == 0)
				$flist="Directory empty";
			else
				$flist=implode("<br>",$files);
			
			$result[]=["Files in: <br> {$serverPath}" , $flist];
			
			$txt="This is a write test";
			$result[]=$this->put_file("test.tmp",$txt)?["WRITE FILE", "SUCCESS"]:["WRITE FILE", "FAILED"];
			
			$ret=$this->get_file("test.tmp");
			$result[]=($ret == $txt)?["READ FILE", "SUCCESS"]:["READ FILE", "FAILED"];
			
			$result[]=$this->delete_file("test.tmp")?["DELETE FILE", "SUCCESS"]:["DELETE FILE", "FAILED"];

			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		if(is_object($this->sftp))
		{		
			$errs=$this->sftp->getErrors();

			$errs=array_filter($errs,function($err) {
				return !(strpos($err,"SSH_MSG_DEBUG") === 0 || strpos($err,"SSH_MSG_GLOBAL") === 0 );
			});
			
			if(count($errs) > 0)
				$result[]=[ "ERRORS",implode("<br>",$errs) ];
		}

		return $result;
		
	}
	
	private function login($settings)
	{

		if(!array_key_exists("port",$this->srv) || empty($this->srv['port'])) $this->srv['port']=22;
		if($this->srv["auth_mode"] == "private_key" )
		{
			
			try {
				$this->sftp = new SFTP($this->srv['host'],$this->srv['port']);
				
				$key_dec=$settings->get_setting('sftp_pubkey');
				if(@$this->srv["is-key-encrypted"] == "1") {
					$key = PublicKeyLoader::load($key_dec,@$this->srv["key_password"]);
				}
				else
					$key = PublicKeyLoader::load($key_dec);
				
				return $this->sftp->login($this->srv['login'], $key);
				
			} catch (\Throwable $e) { 
				return $e->getMessage();
			} 
		}
		elseif($this->srv["auth_mode"] == "login_password" )
		{
			
			try {
				$this->sftp = new SFTP($this->srv['host'],$this->srv['port']);
				
				return $this->sftp->login($this->srv['login'], @$this->srv["password"]);
				
			} catch (\Throwable $e) { 
				return $e->getMessage();
			} 
		}
		
		
	}
	
}
