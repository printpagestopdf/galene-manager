<?php
require_once GALMGR_PLUGIN_PATH . 'includes/class-galmgr-util.php';
require_once GALMGR_DB_DRIVER;

class Galmgr_Room
{
	private $_urls=null;

	
	public $id=-1;
	public $displayName=null;
	public $description=null;
	public $galene_group=null;
	public $key64=null;
	public $access=null;
	public $created=null;
	public $max_clients=0;
	public $room_accesscode=null;

	public $needs_code=false;
	public $needs_nickname=false;
	public $auth_for_other=false;
	public $auth_for_presentator=false;
	public $auth_for_operator=false;
	public $allow_anonymous=false;
	public $allow_subgroups=false;
	public $autolock=false;
	public $allow_recording=false;
	public $show_on_roomslist=false;

	
	public function __construct($data=null) {
		
		if(is_array($data)) {
			$this->update($data);
		}
		elseif( is_numeric($data)) {
			$this->id=$data;
			$this->update(Galmgr_DB_Driver::inst()->get_object($this));			
		}
		
	}

	public function update($fields) 
	{
		$pub_props=Galmgr_util::get_public_props($this);

		foreach($pub_props as $key => $value)
		{
			if(array_key_exists($key,$fields))
			{
				if(is_bool($this->{$key}))
					$this->{$key}=!empty($fields[$key]);
				else
					$this->{$key}=$fields[$key];
			}
		}
		if(array_key_exists('galene_room',$fields)) $this->id=$fields['galene_room'];
	}


	public function store()
	{
		$errorMsgs=$this->validate();

		if(count($errorMsgs) > 0) {
			throw new Exception( "<ul class='my-0 inside'><li>" . implode("</li><li>",$errorMsgs) . "</li></ul>");
		}

		$result=Galmgr_DB_Driver::inst()->store_object($this);
		if($result === false)
			throw new Exception(__("Error saving Data, nothing saved!",'manager-for-galene-videoconference'));
		
		return $result;
	}

	public function delete()
	{
		if(Galmgr_DB_Driver::inst()->delete_records("galene_room",$this->id) !== false)
		{
			Galmgr_DB_Driver::inst()->delete_records("galene_access",$this->id,"room_id");
			return true;
		}
		else
			return false;

	}

	public function has_access($userID, $role)
	{
		return Galmgr_DB_Driver::inst()->has_room_access($this->id,$userID,$role);
	}

	public function get_access($full=true)
	{
		return self::get_room_access($this->id, $full);
	}

	public static function get_room_access($roomID, $full=true)
	{
		return Galmgr_DB_Driver::inst()->get_room_access($roomID, $full);
	}
		
	public static function set_room_access($roomID, $acc)
	{
		return Galmgr_DB_Driver::inst()->set_room_access($roomID, $acc);
	}
		
	public function __get($prop) {
		if($prop == 'urls' && $this->id !== null) {
			if($this->_urls == null)
				$this->_urls=self::room_urls($this->id);
			return $this->_urls;
		}
		
		if($prop == 'serverFileName' && !empty(@$this->galene_group)) {
			return $this->galene_group . ".json";
		}

		return null;
	}
	
	public function __set($prop,$value) {
		$this->{$prop} = $value;
	}
	
	public static function get_rooms($public_only=false) {
		$rooms=Galmgr_DB_Driver::inst()->get_rooms();
		foreach($rooms as $room)
		{
			if($public_only && @$room['show_on_roomslist'] !== true) continue;
			yield new static($room);			
		}
		
	}
	
	private function validate()
	{
		$errorMsgs=array();
		if(empty($this->displayName)) $errorMsgs[]=__("Display name must not be empty",'manager-for-galene-videoconference');
		if(empty($this->key64)) $terrorMsgs[]=__("Missing valid Galène key",'manager-for-galene-videoconference');
		if(empty($this->galene_group)) {
			$errorMsgs[]=__("Galène group must not be empty",'manager-for-galene-videoconference');
		}
		else {
			$recs=Galmgr_DB_Driver::inst()->get_records('galene_room',null," where galene_group = \"{$this->galene_group}\" ");
			if( count($recs) > 1 || (count($recs) == 1 && $recs[0]['id'] != $this->id))
				$errorMsgs[]=__("This Galène group is already in use: ",'manager-for-galene-videoconference') . ": " . $this->galene_group;
		}
				
		return $errorMsgs;
		
	}

	public function get_json()
	{

		$content=[
			"authKeys" => [
				[
					"kty" => "oct",
					"alg" => "HS256",
					"key_ops" => ["sign","verify"],
					"k" => $this->key64,
					"kid" => GALMGR_ROOM_AUTH_KID
				],
			],
			"comment" => "created by galene Manager Addon",
		];
	
		if(!empty($this->allow_anonymous)) $content['allow-anonymous']=$this->allow_anonymous;
		if(!empty($this->allow_subgroups)) $content['allow-subgroups']=$this->allow_subgroups;
		if(!empty($this->allow_recording)) $content['allow-recording']=$this->allow_recording;
		if(!empty($this->autolock)) $content['autolock']=$this->autolock;
		if(!empty($this->max_clients)) $content['max-clients']=$this->max_clients;
		if(!empty($this->description)) $content['description']=$this->description;
		if(!empty($this->displayName)) $content['displayName']=$this->displayName;
		
		$obj= new stdClass();
		
		$obj->filename=$this->galene_group . ".json";
		$obj->json=json_encode($content,JSON_PRETTY_PRINT	);	
			
		return $obj;
		
	}
		
	public static function room_urls($id) 
	{
		$host=Galmgr_util::host_url();
		
		return array(
			"choose" => Galmgr_util::encode_url_param_ar($host,[
							"galene_room" => $id ,
							"galene_action" => 'access_room',
						]),
			"recipient" => Galmgr_util::encode_url_param_ar($host,[
							"galene_room" => $id ,
							"galene_action" => 'access_room',
							"g_type" => 'other',
						]),
			"presenter" =>  Galmgr_util::encode_url_param_ar($host,[
							"galene_room" => $id ,
							"galene_action" => 'access_room',
							"g_type" => 'presenter',
						]),
			"admin" => Galmgr_util::encode_url_param_ar($host,[
							"galene_room" => $id ,
							"galene_action" => 'access_room',
							"g_type" => 'op',
						]),
		);
		
		
	}
}

?>