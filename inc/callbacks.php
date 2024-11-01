<?php


function wp_email_reminder_send_callback()
{
	ob_clean();
	
	if (!wp_verify_nonce($_POST['wp_email_reminder_nonce'], 'wp_email_reminder_nonce'))
	{
		$ret = $_POST['wp_email_reminder_nonce'] . 'wrong nonce';
		
		die($ret);
	}	
			
	echo wp_email_reminder_send_emails();

	die();
}



function wp_email_reminder_clean_callback()
{
	ob_clean();
	
	if (!wp_verify_nonce($_POST['wp_email_reminder_nonce'], 'wp_email_reminder_nonce'))
	{
		$ret = $_POST['wp_email_reminder_nonce'] . 'wrong nonce';
		
		die($ret);
	}	
			
	echo wp_email_reminder_clean_db();

	die();
}
	
	
	
function wp_email_reminder_upd_clean_tab_callback()
{
	ob_clean();
	
	if (!wp_verify_nonce($_POST['wp_email_reminder_nonce'], 'wp_email_reminder_nonce'))
	{
		$ret = $_POST['wp_email_reminder_nonce'] . 'wrong nonce';
		
		die($ret);
	}	
			
	echo wp_email_reminder_print_outreached_recipients();

	die();
}

?>