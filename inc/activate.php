<?php

	function wp_email_reminder_trigger_error($message, $errno) 
	{
 
		if(isset($_GET['action']) && $_GET['action'] == 'error_scrape') 
		{	 
			echo '<strong>' . $message . '</strong>';
	 
			exit;	 
		} 
		else 
		{
	 			trigger_error($message, $errno);	 
		}
		
	}
	
	
	function wp_email_reminder_activate()
	{
		if (!is_plugin_active('wp-email-capture/wp-email-capture.php'))
		{		
			wp_email_reminder_trigger_error('Please, ensure that you activated wp-email-capture before activating this plugin', E_USER_ERROR);
		}
		
		global $wpdb;
		
		global $table_name;
		
		$col_name = 'rem_sent';
		
		if($wpdb->get_var("SHOW TABLES like '$table_name'") != $table_name)
		{
			wp_email_reminder_trigger_error('The table ' . $table_name . ' does not exist. Have you changed the settings of the WP Email Capture?', E_USER_ERROR);
		}
		

		if($wpdb->get_var("SHOW COLUMNS from $table_name like '$col_name'") == NULL)
		{
			$sql = "ALTER TABLE " . $table_name . " ADD COLUMN rem_sent tinyint default 0;";
			
			$wpdb->query($sql);
		}
		
		//debug : wp_email_reminder_trigger_error($wpdb->last_error, E_USER_ERROR);
	}
	
?>