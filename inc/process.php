<?php

function wp_email_reminder_send_emails()
{
	//check that no data is missing
	
	if(!ISSET($_POST['addresses']))
	
		return 'No recipient selected.';
		
	if(!ISSET($_POST['subject']) OR !ISSET($_POST['message']))
	
		return 'Please, specify the subject and content of the message to send.';
	
	if(!preg_match('/%CONFIRM_LINK%/', $_POST['message']))
	
		return 'No confirm link found in email\'s content. Please, add \'%CONFIRM_LINK%\' somewhere in the body and try again.';
		
	//sanitize email subject + content
			
	$subject = wp_email_reminder_stripslashes($_POST['subject']);
	
	$subject = wp_email_reminder_stripslashes(wp_email_reminder_sanitize_textfield($subject)); //call strip then addslashes
	
	$message = wp_email_reminder_stripslashes(wp_email_reminder_sanitize_htmlfield($_POST['message']));
	
	
	//prepare data for emails
	
	global $wpdb;
	
	global $table_name;

	$send_as_html = ISSET($_POST['html']) ? $_POST['html'] : false;
	
	$emails_sent_count = 0;
	
	$error = false;
	
	$err_msg = 'Delivery to the following recipients failed : <br /> <ul style="list-style: circle inside none;">';
		
	$header_from = wp_email_reminder_get_header_from();
	
	//begin for each loop - individual treatment
	
	foreach($_POST['addresses'] as $name)
	{
		//check name 
		
		$name = wp_email_reminder_stripslashes($name);
		
		//verify wp_email_reminder count (reminder messages already sent)
		
		$rem_count = wp_email_reminder_get_count($name);
		
		if($rem_count < 0 || $rem_count >= REM_LIMIT)
		{
			wp_email_reminder_add_recipient_error($name, $err_msg, $error, 'number of reminders reached the limit');
				
			continue;
		}
		
		//get email address
		
		$email =  $wpdb->get_var( $wpdb->prepare("SELECT email FROM " . $table_name . " WHERE name = %s;", $name) );
		
		if($email == null)
		{
			wp_email_reminder_add_recipient_error($name, $err_msg, $error, 'email address not found');
			
			continue;
		}
		
		//get confirm link
		
		$confirm_code = $wpdb->get_var( $wpdb->prepare("SELECT confirm_code FROM " .$table_name . " WHERE name = %s;", $name) );
		
		if($confirm_code == null)
		{
			wp_email_reminder_add_recipient_error($name, $err_msg, $error, 'confirm code not found');
			
			continue;
		}
		
		$conf_link = wp_email_reminder_add_last_char(get_option('home')) . '?wp_email_confirm=1&wp_email_capture_passkey=' . $confirm_code;
		
		//update message with personal infos
		
		$message_perso = preg_replace('/%NAME%/', $name, $message);
		
		$message_perso = wp_email_reminder_insert_confirm_link($conf_link, $html, $message_perso);
		
		//create and send email
		
		if($send_as_html == 'true')
		
			add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
		
		if(!wp_mail($email, $subject, $message_perso, $header_from))
		{
			wp_email_reminder_add_recipient_error($name, $err_msg, $error, 'an error occured while sending');
			
			continue;
		}
		
		//update counters 
		
		$updated = wp_email_reminder_update_count($name);
		
		$emails_sent_count++;
		
		/*debug :echo $header_from . ' | to : ' . $email . '<br />' . $subject . '<br />' . $message_perso . '<br />' ;
				   echo $send_as_html == 'true' ? 'oui' : 'non' . '<br /><br />';	*/
		
	} //end for each
	
	//return infos
	
	if($error == 'true')
	{
		$err_msg = $err_msg . '</ul>';
		
		echo $err_msg;
	}
	
	echo $emails_sent_count > 0 ? $emails_sent_count . ' email(s) successfully sent.' : 'No email sent.';
	
}

function wp_email_reminder_add_recipient_error($name, &$err_msg, &$error, $precisions = false)
{
	$error = true;
	
	if($precisions)
	{
		$err_msg = $err_msg . '<li>' . $name . '    (' . $precisions . ') </li>';
	}
	else
	{
		$err_msg = $err_msg . '<li>' . $name . '</li>';
	}
}



function wp_email_reminder_get_header_from()
{
	$from_name = get_option('wp_email_capture_from_name');
	
	$from = get_option('wp_email_capture_from');
	
	return 'From: ' . $from_name . ' <' . $from . '>' . "\r\n"; //double quotes for new line important!

}



function wp_email_reminder_get_count($name)
{
	global $wpdb;
	
	global $table_name;
	
	$count = $wpdb->get_var( $wpdb->prepare("SELECT rem_sent FROM " . $table_name . " WHERE name = %s;", $name) );
	
	return ($count == null ? -1 : $count);
}


function wp_email_reminder_update_count($name)
{
	global $wpdb;
	
	global $table_name;
	
	$count = wp_email_reminder_get_count($name);
	
	if($count < 0)
	
		return false;
		
	$count++;
	
	$updated = $wpdb->query( $wpdb->prepare("UPDATE " . $table_name . " SET rem_sent=" . $count . " WHERE name = %s;", $name) );
	
	return ($updated == 'false' ? 'false' : 'true');	
}

?>