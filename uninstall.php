<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

class Galmgr_Uninstall {
	
	public function uninstall()
	{
		$this->remove_role();
		$this->delete_tables();
	}
	
	private function remove_role()
	{
		$role='galene_mgr';
		if(wp_roles()->is_role( $role )) {
			remove_role($role);
		}
	}

	private function delete_tables()
	{
		global $wpdb;

		foreach( array(
			$wpdb->prefix . 'galene_user',
			$wpdb->prefix . 'galene_room',
			$wpdb->prefix . 'galene_access',
			$wpdb->prefix . 'galene_settings',		
												) as $table){

			$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
		}
	
	}
	
}

$uninst=new Galmgr_Uninstall();
$uninst->uninstall();

?>