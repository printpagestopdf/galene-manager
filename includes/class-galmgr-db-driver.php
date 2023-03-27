<?php

class Galmgr_DB_Driver {
	private static $_instace=null;

	public function __construct() {
		// error_log("DRIVER CREATED");
	}
	
	public static function inst()
	{
		if(self::$_instace == null) self::$_instace=new static();
		return self::$_instace;
	}

	public function get_records($_table,$order=null, $where='')
	{
		global $wpdb;
		$table=$wpdb->prefix . $_table;
		
		$sort=(is_array($order))?' order by ' . implode(' ',$order):'';

		$sql   = "SELECT * FROM {$table} {$where} {$sort}";
		return $wpdb->get_results( $sql, ARRAY_A );
		
	}
	public function get_rooms()
	{
		global $wpdb;
		$table=$wpdb->prefix . 'galene_room';
		$sql   = "SELECT * FROM {$table} order by `displayName` DESC";
		$roomRecs=$wpdb->get_results( $sql, ARRAY_A );
		return $this->expand_room_records($roomRecs);

	}
	
	public function get_room($id)
	{
		$rooms=array();
		$rooms[]=$this->get_record_by_id("galene_room",(int)$id);
		$rooms=$this->expand_room_records($rooms);

		return $rooms[0];

	}

	public function get_object($obj)
	{
		switch(get_class($obj))
		{
			case "Galmgr_Room":
				$rooms=array();
				$rooms[]=$this->get_record_by_id("galene_room",$obj->id);
				$rooms=$this->expand_room_records($rooms);
				return $rooms[0];
				break;

			default:
				return false;
				break;
		}

		return true;

	}
	
	public function store_object($obj)
	{
		switch(get_class($obj))
		{
			case "Galmgr_Room":

				if(empty(@$obj->id) || $obj->id < 0)
				{
					$dbFields=$this->roomrec_to_dbrec(get_object_vars($obj));
					$result = $this->insert_record("galene_room",$dbFields);
					if($result !== false) $obj->id = $result;
					return $result;
				}
				else
				{
					$dbFields=$this->roomrec_to_dbrec(get_object_vars($obj));
					return $this->update_record("galene_room",$obj->id,$dbFields);
				}
				break;

			default:
				return false;
				break;
		}

		return true;
	}

	private function roomrec_to_dbrec($args)
	{
		$fields=array();
		
		if(isset($args['id']) && $args['id'] > 0) { $fields['id']=(int)$args['id'];  }
		unset($args['id']);
		if(isset($args['displayName'])) { $fields['displayName']=sanitize_text_field($args['displayName']); unset($args['displayName']); } 
		if(isset($args['description'])) { $fields['description']=sanitize_textarea_field($args['description']); unset($args['description']); } 
		if(isset($args['galene_group'])) { $fields['galene_group']=sanitize_text_field($args['galene_group']); unset($args['galene_group']); } 
		if(isset($args['key64'])) { $fields['key64']=$args['key64']; unset($args['key64']); }
		if(isset($args['access'])) { $fields['access']=$args['access']; unset($args['access']); } 		
		
		$fields['attrs']=json_encode($args,JSON_PRETTY_PRINT);
		
		return $fields;
	}


	private function expand_room_records($roomRecs) 
	{
		
		foreach($roomRecs as &$rec)
		{
			if(!empty(@$rec['attrs']))
			{
				$attrs=json_decode($rec['attrs'],true);
				if(!empty($attrs)) {
					unset($rec['attrs']);
					$rec=array_merge($rec,$attrs);
				}
			}
		}
		return $roomRecs;
	}


	protected function get_record_by_id($_table,$id)
	{
		return $this->get_record($_table,["id", "=", $id]);
	}
	
	protected function get_record($_table,$where)
	{
		global $wpdb;
		// $table=$wpdb->prefix . $_table;
		
		$ret = $wpdb->get_row( self::_fetch_sql( $_table,$where ),ARRAY_A );
		
		if($ret == null || !is_array($ret) || count($ret) == 0)
			return false;
		else
			return $ret;
	}
	
	protected function update_record($_table,$id,$data)
	{
		global $wpdb;
		$table=$wpdb->prefix . $_table;
		return $wpdb->update( $table, $data, array( 'id' => $id ) );
		
	}
	
	protected function insert_record($_table,$data,$fmt="%s")
	{
		global $wpdb;
		$table=$wpdb->prefix . $_table;
		
		switch($_table)
		{
			case "galene_user":
				$fmt=array("%s", "%s", "%s", "%d");
				break;
				
			case "galene_room":
			default:
				$fmt="%s";
				break;
		}

		if($wpdb->insert( $table, $data, $fmt ) !== false)
			return $wpdb->insert_id;
		else
			return false;
		
	}
	
	public function delete_records($_table,$id,$fld = "id")
	{
		global $wpdb;
		$table=$wpdb->prefix . $_table;
		
		return $wpdb->delete( $table, array( $fld => $id ), array( '%d' ) );		
	}
	
	public function has_room_access($roomID,$userID,$role)
	{
		global $wpdb;
		$accessTable=$wpdb->prefix . 'galene_access';

		$sql=$wpdb->prepare("SELECT COUNT(*) FROM {$accessTable} WHERE room_id = %d AND user_id = %d AND {$role} = 1;",$roomID,$userID);
		$count=$wpdb->get_var($sql);

		return ($count >= 1);

	}

	public function has_wpuser_access($roomID,$role,$wp_user)
	{
		global $wpdb;
		$accessTable=$wpdb->prefix . 'galene_access';
		$userTable=$wpdb->prefix . 'galene_user';

		if(empty($wp_user->roles))	return false;

		$sql= <<< SQL
		SELECT u.login FROM {$userTable} as u LEFT JOIN {$accessTable} as a
			ON u.id = a.user_id	WHERE a.room_id = %d AND u.type = %d AND a.{$role} = 1;
SQL;

		$prepSql=$wpdb->prepare($sql,$roomID,GALMGR_WP_ROLE_TYPE);
		$dbRoles=$wpdb->get_results($prepSql, OBJECT_K);
		
		$dbRolesID=array_keys($dbRoles);
		$wpRolesID=array_values($wp_user->roles);

		return !empty(array_intersect($dbRolesID,$wpRolesID));
	}

	public function sync_wp_roles($newRoles=null)
	{
		global $wpdb;
		try {
			$userTable=$wpdb->prefix . 'galene_user';

			if($newRoles != null) {
				$wpRoles=array();
				foreach($newRoles as $rID => $r)
					$wpRoles[$rID]=$r['name'];
			}
			else
				$wpRoles=wp_roles()->get_names();

			$sql=$wpdb->prepare("SELECT login,id from {$userTable} WHERE type = %d;",GALMGR_WP_ROLE_TYPE);
			$dbRoles=$wpdb->get_results($sql,OBJECT_K);

			$wpRolesID=array_keys($wpRoles);
			$dbRolesID=array_keys($dbRoles);

			$rolesInsertID=array_diff($wpRolesID,$dbRolesID);
			$rolesDeleteID=array_diff($dbRolesID,$wpRolesID);

			foreach($rolesInsertID as $roleID)
			{
				$this->insert_record("galene_user",array(
					'displayName' => $wpRoles[$roleID],
					'login' => $roleID,
					'password' => '',
					'type' => 1,
					'isAdmin' => 0,
				));
			}

			foreach($rolesDeleteID as $roleID)
			{
				if(isset($dbRoles[$roleID]->id) && filter_var($dbRoles[$roleID]->id, FILTER_VALIDATE_INT) !== false)
					$this->delete_user($dbRoles[$roleID]->id);
			}

			return true;

		} catch (Throwable $t) {
			return false;
		}
	}

	public function get_room_access($roomId, $full=true)
	{
		global $wpdb;
		$userTable=$wpdb->prefix . 'galene_user';
		$accessTable=$wpdb->prefix . 'galene_access';
		
		if($full === true) {
			$sql=<<<SQL
				SELECT * FROM  {$userTable} as u 
					LEFT JOIN (SELECT * FROM {$accessTable} WHERE room_id = %d ) as a 
				ON a.user_id = u.id; 			
SQL;
		}
		else
		{
			$sql=<<<SQL
				SELECT * FROM {$userTable} as u JOIN {$accessTable} as a
				ON ( u.id = a.user_id AND a.room_id = %d );
SQL;
		}
		
		return $wpdb->get_results($wpdb->prepare( $sql, (int)$roomId ),ARRAY_A);
	}
	
	public function set_room_access($roomId,$data, $_table='galene_access')
	{
		global $wpdb;
		$accessTable=$wpdb->prefix . $_table;
		
		$wpdb->query( "START TRANSACTION" );
		$wpdb->delete( $accessTable, array( 'room_id' => (int)$roomId ), array( '%d' ) );
		foreach($data as $userId => $acc)
		{
			$wpdb->insert(
				$accessTable,
				array(
					'room_id' => (int)$roomId,
					'user_id' => (int)$userId,
					'is_operator' => array_key_exists('is_operator',$acc)?1:0,
					'is_presenter' => array_key_exists('is_presenter',$acc)?1:0,
					'is_other' => array_key_exists('is_other',$acc)?1:0,
				),
				array('%d','%d','%d','%d','%d')
			);			
		}
		
		
		return $wpdb->query( "COMMIT" );
	}


	public function get_users()
	{
		return $this->get_records('galene_user',['`login`', 'ASC']);
	}
	
	public function get_user($id)
	{
		return $this->get_record_by_id("galene_user",(int)$id);
	}
	
	public function get_user_by_login($login)
	{
		return $this->get_record("galene_user",["login","=",sanitize_text_field($login)]);
	}
	
	public function update_user($args)
	{
		if(!isset($args['galene_user'])	) return false;
		
		$fields=array();
		
		if(isset($args['displayName'])) $fields['displayName']=sanitize_text_field($args['displayName']);
		if(isset($args['login'])) $fields['login']=sanitize_text_field($args['login']);
		if(isset($args['password'])) $fields['password']=$args['password'];
		$fields['isAdmin']=(isset($args['isAdmin']))?1:0;
	
		return $this->update_record("galene_user",(int)$args['galene_user'],$fields);
		
	}
	
	public function insert_user($args)
	{
		if(@$args['galene_user'] >= 0) return false;
		
		$fields=array();
		
		if(isset($args['displayName'])) $fields['displayName']=sanitize_text_field($args['displayName']);
		if(isset($args['login'])) $fields['login']=sanitize_text_field($args['login']);
		if(isset($args['password'])) $fields['password']=$args['password'];
		$fields['isAdmin']=(isset($args['isAdmin']))?1:0;
	
		return $this->insert_record("galene_user",$fields);
		
	}
	
	public function delete_user($id)
	{
		if($this->delete_records("galene_user",(int)$id) !== false)
		{
			$this->delete_records("galene_access",(int)$id,"user_id");
			return true;
		}
		else
			return false;
	}

	public function get_setting($setting_key)
	{
		$rec=$this->get_record('galene_settings',[ 'setting_key', '=', sanitize_text_field($setting_key) ]);
		
		// if(empty($rec['type']) ||  $rec['type'] == 'json')
			// $rec['data']=json_decode($rec['data'],true);
		
		return $rec;
	}
	
	public function get_all_settings()
	{
		return $this->get_records("galene_settings");
		
	}


	public function upsert_setting($setting_key,$data,$setting_subkey=null, $type=null)
	{
		if($type == null || $type == "json")
			$data=json_encode($data,JSON_PRETTY_PRINT);
		return $this->_upsert_setting($setting_key,$data,$setting_subkey, $type);
	}

	private function _upsert_setting($setting_key,$data,$setting_subkey=null, $type=null)
	{
        global $wpdb;
		$table=$wpdb->prefix . 'galene_settings';
		
		if(self::_record_exists('galene_settings',$setting_key,$fld="setting_key","%s"))
		{
			$ret= $wpdb->update( $table,
						array(
							'setting_key' => $setting_key,
							'setting_subkey' => $setting_subkey,
							'data' => $data,
							'type' => $type,
						),
						array( 'setting_key' => $setting_key ) );
		}
		else
		{
			$ret=$wpdb->insert(	$table,
				array(
					'setting_key' => $setting_key,
					'setting_subkey' => $setting_subkey,
					'data' => $data,
					'type' => $type,
				),
				array('%s','%s','%s','%s')
			);			
		}
		
		return ($ret !== false);
	}

	private static function _record_exists($_table,$value,$fld="id",$type="%d")
	{
        global $wpdb;
		$table=$wpdb->prefix . $_table;
		
		$sql_prep=$wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$fld} = {$type} ", $value );
		
        $rowcount = $wpdb->get_var($sql_prep);
        return ($rowcount >= 1);
		
	}
	
	private static function _fetch_sql( $_table,$where ) {
		global $wpdb;
		$table= $wpdb->prefix . $_table;
		$value=$where[2];
		$sql = sprintf( 'SELECT * FROM %s WHERE %s %s %%s', $table, $where[0],$where[1] );
		return $wpdb->prepare( $sql, $value );
	}
	
}
?>