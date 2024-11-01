<?php
	/*
		Plugin Name: WP Email Reminder
		Description: Add-on for the excellent <a href="http://www.wpemailcapture.com/">WP Email Capture</a> Plugin, WP Email Reminder enables you to easily send reminder emails to all your "temp-members". Everything is done from a simple menu added to your dashboard.
		Version: 0.2
		Author: Lucy Linder (lucy.linder)
		Author URI: mailto:lucy.derlin@gmail.com
		License: GPLv2
		License URI: http://www.gnu.org/licenses/gpl-2.0.html
	*/
	
	/** globals */
	define('REM_PATH', dirname(__FILE__)); 
	
	define('REM_LIMIT', 2); 
	
	global $wpdb;
	// debug : $wpdb->show_errors();
	
	global $table_name;
	
	$table_name = $wpdb->prefix . "wp_email_capture_temp_members";
	
	/** include all the other files */
	require_once(REM_PATH . '/inc/include.php');

	
	/** configuration page : include the code described in the nwa_admin.php */
	function wp_email_reminder_menu_page() 
	{
		if (is_admin()) 
		{
			wp_email_reminder_get_menu();			
		} 
	}
	
	
	/** add the menu under the settings tabs in the dashboard */
	function wp_email_reminder_menu()
	{
		global $wp_email_reminder_menu_page;
		
		$wp_email_reminder_menu_page = add_options_page("WP Email Reminder", "WP Email Reminder", "administrator", "wp_email_reminder", "wp_email_reminder_menu_page"); 
	}

		
	/** include jquery script */
	function wp_email_reminder_load_scripts($hook) 
	{			
		global $wp_email_reminder_menu_page;
		
		if($hook != $wp_email_reminder_menu_page)
			
			return;
		
		wp_enqueue_script('jquery');
		
		wp_enqueue_script('jquery-ui-tabs');
		
		wp_enqueue_style('wp_email_reminder_custom_css', plugins_url(). '/wp-email-reminder/inc/rem_style.css');
		
		wp_enqueue_script('wp_email_reminder_custom_js', plugins_url(). '/wp-email-reminder/inc/custom.js'); 
		
		wp_enqueue_script("jquery_autosize", plugins_url(). '/wp-email-reminder/inc/jquery.autosize-min.js'); // autosize textarea script
		
		wp_enqueue_style("jui_theme_blacktie", plugins_url(). '/wp-email-reminder/inc/black-tie/jquery-ui-1.8.23.blacktie_cleaned.css'); //jquery ui theme

	}
	
	    function wp_email_reminder_plugins_loaded()
    {

		if (function_exists('load_plugin_textdomain'))
		{
			$plugin_dir = basename(dirname(__FILE__));
			
			load_plugin_textdomain('WPEC','wp-content/plugins/'.$plugin_dir.'/languages',$plugin_dir.'/languages');
		}
		
		return;
    }
	
	
	/** add actions and shortcode functionalities */
	if(is_admin())
	{
		register_activation_hook(__FILE__, 'wp_email_reminder_activate');
		
		add_action( 'admin_init', 'wp_email_reminder_options_init');
		
		add_action('admin_enqueue_scripts', 'wp_email_reminder_load_scripts'); 
		
		add_action('admin_menu', 'wp_email_reminder_menu'); 
		
		add_action('wp_ajax_wp_email_reminder_send_mails', 'wp_email_reminder_send_callback');
		
		add_action('wp_ajax_wp_email_reminder_clean_db', 'wp_email_reminder_clean_callback'); 
		
		add_action('wp_ajax_wp_email_reminder_update_clean_tab', 'wp_email_reminder_upd_clean_tab_callback');
	}
	
	// Loads any language file if present
    
    add_action('plugins_loaded','wp_email_reminder_plugins_loaded');
    
?>