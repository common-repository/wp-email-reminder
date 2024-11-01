<?php

function wp_email_reminder_get_menu()
{
?>
	<noscript><div class="wrap" style="margin: 20px auto; font-weight:bold;">Javascript is currently disabled. Please, enable it in your browser and reload the page.</div></noscript>
	
	<div class="wrap" style="display:none">
		
		<div class="updated" style="display:none"></div>
		
		<h2><?php _e('WP Email Reminder','WPEC'); ?></h2>

		<div id="tabs">
		
			<ul>
				<li><a href="#tabs-1"><?php _e('Email Contents','WPEC'); ?></a></li>
				<li><a href="#tabs-2"><?php _e('Recipients','WPEC'); ?></a></li>
				<li><a href="#tabs-3"><?php _e('Send','WPEC'); ?></a></li>
				<li><a href="#tabs-4"><?php _e('Clean','WPEC'); ?></a></li>
			</ul>
			
			<div id="tabs-1">
			
				<h3><?php _e('Email Contents','WPEC'); ?></h3>
				
				<form id="rem_email_content_form" method="POST" action="options.php" >
				
					<?php
					
						settings_fields('wp_email_reminder_options');
						
						$options = get_option('wp_email_reminder_opt');
												
						/*debug : echo $options['subject'] . " " . $options['message'];*/
						
					?>
					
					<p>
						<strong><?php _e('Subject','WPEC'); ?>:</strong> <input type="text" id="subject" name="wp_email_reminder_opt[subject]" value="<?php echo stripslashes($options['subject']); ?>" size="35" title="<?php _e('Any tags, semi-colons or double quotes will be stripped (use single quotes instead).','WPEC'); ?>">
					</p>  
					
					<p>
						<strong><?php _e('Content','WPEC'); ?>:</strong> 
						
						<div id="content_area">
						
							<p><?php _e('Use <strong>%NAME%</strong> for the name of the recipient and <strong>%CONFIRM_LINK%</strong> for the confirmation link.','WPEC'); ?> </p>
							
							<textarea name="wp_email_reminder_opt[message]" id="content" ><?php echo stripslashes($options['message']); ?></textarea>
							
						</div>
						
						<p>
							<input type="checkbox" id="html" onclick="this.checked ? jQuery('div#html_preview').css('display', 'block') : jQuery('div#html_preview').css('display', 'none');" /> <label for="html">Send content as HTML</label>
						
						</p>
						
						<div id="html_preview">
							<p style="text-align:justify">
								<strong>Warning</strong>: some email programs still don't have the capability to read HTML email or have difficulties displaying it properly. If you have any doubt, uncheck this box. 
							</p>
							
							<p class="submit">
							
								<input class="submit" type="button" value="preview HTML" onclick="html_preview()" />
								
							</p>
							
							<div></div>
							
						</div>
						
						
					</p>
					
					<p class="submit">
					
						<input class="submit" type="submit" value="<?php _e('Save As Template','WPEC'); ?>" />
						
					</p>
										
				</form>
			
			</div> 	
		
			<div id="tabs-2">
			
				<h3><?php _e('Mailing List','WPEC'); ?></h3> 
				
				<p class="submit">
				
					<input type="button" value="uncheck all" onclick="check_uncheck(this);" />
					
				</p>
			
				<div id="rem_div_addresses">
				
					<?php 
					
					if ( function_exists('wp_nonce_field') ) 
					{						
						wp_nonce_field('wp_email_reminder_nonce', 'wp_email_reminder_nonce');
						
						wp_nonce_field('wp_email_reminder_clean_nonce');
					}
					
					wp_email_reminder_print_potential_addresses(); 
					
					?>  
				
				</div>
				
			</div>
				
			<div id="tabs-3">
					
				<h3><?php _e('Send','WPEC'); ?></h3>
				
				<p><?php _e('Before sending the confirmation emails, make sure that :','WPEC'); ?>
				
				<ol>
				
					<li><?php _e('<a href="http://www.wpemailcapture.com/"><strong>WP Email Capture</strong></a> is active | you have not deactivated <a href="http://www.wpemailcapture.com/"><strong>WP Email Capture</strong></a> after the activation of <strong>WP Email Reminder</strong>','WPEC'); ?></li>
					
					<li><?php _e('You entered a subject and a body in the "Email Content" tab','WPEC'); ?></li>
					
					<li><?php _e('The body of the email does not contain scripts or illegal content (it will be removed anyway)','WPEC'); ?></li>
					
					<li><?php _e('You selected the recipients in the "Recipients" tab','WPEC'); ?></li>
					
				</ol>

				</p>
				
				<p class="submit">  
				
					<input type="button" class="button-primary" style="width:70px;" name="submit" value="Send" onclick="if(!confirm('Are you sure you want to mail all the selected recipients?')) return false; send_mails_ajax();" />  
					
				</p> 
				
				<p><?php _e('Note : <strong>WP Email Reminder</strong> does not condone spamming, hacking or harming in any ways. Any kind of abuse will be reported.','WPEC'); ?></p>
				
			</div>
			
			<div id="tabs-4">
					
				<h3><?php _e('Clean','WPEC'); ?></h3>				
				
				<div id="rem_clean_tab_content">
				
					<?php echo wp_email_reminder_print_outreached_recipients(); ?>
					
				</div>
				
			</div>
		
		</div><!-- end tabs-->	
	
	<script type="text/javascript">	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>"; jQuery(document).ready(function($){ $("#tabs").tabs(); $('div.wrap').css('display', 'block'); $("#content-tmce").trigger('click'); $('textarea#content').autosize(); jQuery('input#html').attr('checked', false); save_draft_ajax(); });</script>
		
	</div>
	
<?php
}

?>