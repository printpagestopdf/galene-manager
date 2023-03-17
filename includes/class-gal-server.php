<?php
	
class Gal_Server {
	
	private static $instance = null;
	private $settings=null;
	private $drv=null;
	public $can_write=false;
	public $can_read=false;

	public static function inst()
	{
		if(null === self::$instance)
		{
			self::$instance = new static();
		}
		return self::$instance;
	}
	
	private function __construct() {
		require_once GALENE_PLUGIN_PATH . 'includes/class-gal-settings.php';
		$this->settings=Gal_Settings::inst();
		
		switch($this->settings->get_server_access())
		{
			case "sftp":
				require_once GALENE_PLUGIN_PATH . 'includes/class-gal-sftp-driver.php';
				$this->drv=Gal_Sftp_Driver::inst($this->settings);
				$this->can_write=$this->drv->can_write;
				$this->can_read=$this->drv->can_read;
				break;
				
			case "filesystem":
				require_once GALENE_PLUGIN_PATH . 'includes/class-gal-filesystem-driver.php';
				$this->drv=Gal_Filesystem_Driver::inst($this->settings);
				$this->can_write=$this->drv->can_write;
				$this->can_read=$this->drv->can_read;
				break;
		}
	}
	
	public function put_file($fname,$content="",$path=null)
	{
		return $this->drv->put_file($fname,$content,$path);
	}
	
	public function get_file($fname,$path=null)
	{
		return $this->drv->get_file($fname,$path);
	}
	
	public function delete_file($fname,$path=null)
	{
		return $this->drv->delete_file($fname,$path);
	}
	
	public function list_files($path=null)
	{
		return $this->drv->list_files($path);
	}
	
	public function test_connection()
	{
		return $this->drv->test_connection();
	}
}