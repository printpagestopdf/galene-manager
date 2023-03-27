<?php

class Galmgr_Filesystem_Driver {
	
	private static $instance = null;
	private $srv=null;
	public $last_error="";
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
		$this->srv=$settings->get_setting('filesystem');
		$this->srv['server_path']=trailingslashit($this->srv['server_path']);
			$serverPath=$this->srv['server_path'];
		if(is_dir($this->srv['server_path']))
		{
			$this->can_write=true;
			$this->can_read=true;
		}

	}
	
	public function put_file($fname,$content="",$path=null)
	{
		try {
			$ret=file_put_contents($this->srv['server_path'] . $fname, $content);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
	}
	
	public function get_file($fname,$path=null)
	{
		try {
			$ret=file_get_contents($this->srv['server_path'] . $fname);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
		
	}
	
	public function delete_file($fname,$path=null)
	{
		try {
			$ret=unlink($this->srv['server_path'] . $fname);			
			return $ret;
			
		} catch (\Throwable $e) { 
			$this->last_error=$e->getMessage();
		} 
		
		return false;		
	}
	
	public function list_files($path=null)
	{
		try {
			$files=scandir($this->srv['server_path']);
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
			
			$serverPath=$this->srv['server_path'];
			$result[]= [ "Server Path: <br> {$serverPath}",(is_dir($serverPath)?' FOUND':' NOT FOUND') ];

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
		
		if(!empty($this->last_error))
			$result[]=[ "ERRORS",$this->last_error ];

		return $result;
		
	}
	
}
