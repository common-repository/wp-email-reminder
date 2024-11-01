<?php

function wp_email_reminder_options_init()
{
	register_setting('wp_email_reminder_options', 'wp_email_reminder_opt', 'wp_email_reminder_options_sanitize');
}



function wp_email_reminder_options_sanitize($input)
{	
	$input['subject'] = wp_email_reminder_sanitize_textfield($input['subject']);
	
	$escape_html = true;
	
	$input['message'] = wp_email_reminder_sanitize_htmlfield($input['message'], $escape_html);

	
	return $input;
	
	//TODO : add_settings_error()
}





function wp_email_reminder_print_potential_addresses() 
{
	global $wpdb;
	
	global $table_name;
	
	$sql = "SELECT name, email FROM " . $table_name . ";";
	
	$myrows = $wpdb->get_results($sql);
	
	echo '<table id="rem_recipients_tbl">';
	
	if($myrows){
	
		foreach ($myrows as $row){
	
			?>
			
			<tr>					
				<td>
				
					<input type="checkbox" value="<?php echo stripslashes($row->name); ?>" checked="checked" />
					
				</td>
				
				<td>
				
					<?php echo stripslashes($row->name); ?>
					
				</td>
				
				<td> 
				
					&lt;<?php 
					
                    $string = preg_replace("/(?<=.).(?=.*@)/u","*", $row->email);
					
                    $string = preg_replace("/(?<=@.)[a-zA-Z0-9]*(?=(?:[.]|$))/u","*", $string);
					
                    echo $string; ?>&gt;
					
				</td>
				
			</tr>
			
			<?php
		
		}//end foreach
		
	echo '</table>';
	
	}//end if
		
}





function wp_email_reminder_print_outreached_recipients() 
{
	global $wpdb;
	
	global $table_name;
	
	$sql = "SELECT name, email FROM " . $table_name . " WHERE rem_sent >=" . REM_LIMIT . ";";
	
	$myrows = $wpdb->get_results($sql);
	
	if(!$myrows)
	{	
		_e('<p><i>Your database is clean.<i></p>','WPEC');
		
		return;
	}
	
	?>
	
	<p> <?php _e('The following recipients reached the maximum number of reminders. To prevent spamming, any further tries to send them emails will be discarded.','WPEC'); ?></p>
	
	<table id="rem_clean_db_tbl">
	
	<?php
	
	foreach ($myrows as $row){

		?>
		
		<tr>					
			
			<td>
			
				<?php echo stripslashes($row->name); ?>
				
			</td>
			
			<td> 
			
				&lt;<?php $string = preg_replace("/(?<=.).(?=.*@)/u","*", $row->email);
				
                $string = preg_replace("/(?<=@.)[a-zA-Z0-9]*(?=(?:[.]|$))/u","*", $string);
				
                echo $string; ?>&gt;
				
			</td>
			
		</tr>
		
		<?php
	
	}//end foreach
	
	?>
	
	</table>
	
	<p><?php _e('Do you want <strong>WP Email Reminder</strong> to clean the database for you?','WPEC'); ?></p>
					
	<p class="submit">  
	
		<input type="button" class="button-primary" style="width:140px;" name="submit" value="<?php _e('Yes, do the job','WPEC'); ?>" onclick="clean_db_ajax();" />  
		
	</p> 
	
	<?php	
}




function wp_email_reminder_clean_db()
{
	global $wpdb;
	
	global $table_name;
	
	$deleted = $wpdb->query("DELETE FROM " . $table_name . " WHERE rem_sent >= " . REM_LIMIT . ";");
	
	return ($deleted == "false" OR $deleted < 1) ? 'No record deleted.' : $deleted . ' record(s) deleted. ';
}

?>