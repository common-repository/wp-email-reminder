<?php

 /** generic functions */
 

function wp_email_reminder_sanitize_textfield($string)
{
	$string = wp_email_reminder_escape_for_input_value($string);
	
	$string = wp_email_reminder_escape_for_sql($string);
	
	$string = addslashes(sanitize_text_field($string));
	
	return $string;
}


function wp_email_reminder_sanitize_htmlfield($string, $escape_html = false)
{
	$string = addslashes($string);
	
	$string = wp_filter_post_kses( $string ); // calls stripslashes then addslashes
	
	$string = stripslashes($string);
	
	if($escape_html)
		
		$string = esc_html( $string );
		
	return $string;
}



function wp_email_reminder_escape_for_sql($string) //detect any carriage return (\r, %0d), line feed (\n, %0a) or NULL (\x00)
{
	return preg_replace('/(%0A|%0D|\n+|\r+|\x1A|\x00)/i', '', $string); 
}



//strip all double quotes or semi-colons (messes with html)
function wp_email_reminder_escape_for_input_value($string)
{
	return preg_replace(
		array('/\"/', '/;/', '/\x22/', '/\x3b/'),
		'',
		$string
	);
}


function wp_email_reminder_add_last_char($url)
{
	$last = $url[strlen($url)-1]; 
	
	if ($last != "/")
	{
		$url = $url . "/";
	}
	
	return $url;
}



function wp_email_reminder_stripslashes($string) 
{
	if (defined('TEMPLATEPATH') || (get_magic_quotes_gpc())) 

		return stripslashes($string);

	return $string;
}


function wp_email_reminder_insert_confirm_link($conf_link, $html, $message) //TODO take a attributes (target, etc.) into account instead of wiping them out
{
	if($html == 'true')
	{
	
		$conf_a_tag = "<a href='" . $conf_link . "'>" . $conf_link . "</a>";
			
		$message = preg_replace('/<a.*%CONFIRM_LINK%.*<\/a>/', $conf_a_tag, $message);
			
		return preg_replace('/%CONFIRM_LINK%/', $conf_a_tag, $message);
	}
	
	return preg_replace('/%CONFIRM_LINK%/', $conf_link, $message);
}

?>