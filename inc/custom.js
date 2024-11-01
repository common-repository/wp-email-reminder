function html_preview(){

	clean_html = jQuery( '<div>' + jQuery('textarea#content').val() + '</div>' )
		.find("script,noscript")
		.remove()
		.end()
		.html(); 
		
	jQuery('div#html_preview > div').html( clean_html );
}

/** ajax */
function save_draft_ajax(){
	jQuery('form#rem_email_content_form').submit( function () {
		
		var b =  jQuery(this).serialize();
		jQuery.post( 'options.php', b ).error( 
			function() {
				jQuery('div.updated')
					.html('<p><strong>An error occured while communicating with the server.</strong> <br /> Please, refresh the page and try again.</p>') 
					.css('display', 'block');
					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
			}).success( function(response) {
				/*debug : alert('updated');*/
				jQuery('div.updated')
					.html('<p><strong>Template saved.</strong></p>') 
					.css('display', 'block'); 
					jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
			});
			return false;    
	});
}


function send_mails_ajax(){

	var addresses = new Array();
	
	jQuery('#rem_recipients_tbl input[type=checkbox]:checked').each(function(index){
		addresses.push(jQuery(this).val());
	});
	
	jQuery.post(
	   ajaxurl + '?action=wp_email_reminder_send_mails', 
	   {
		   'subject' : jQuery('input#subject').val(),
		   'message' : jQuery('textarea#content').val(),
		   'addresses[]' : addresses,
		   'html' : jQuery('input#html').attr('checked') ? 'true' : 'false',
		   'wp_email_reminder_nonce' : jQuery('input#wp_email_reminder_nonce').val()
	   }, 
	   function(response){
		jQuery('div.updated')
			.html('<p><strong>' + response + '</strong></p>') 
			.css('display', 'block'); 
			jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
		
		update_clean_tab_ajax();		
	   }
	); 		
}

function clean_db_ajax(){

	jQuery.post(
	   ajaxurl + '?action=wp_email_reminder_clean_db', 
	   {
		   'wp_email_reminder_nonce' : jQuery('input#wp_email_reminder_nonce').val()
	   }, 
	   function(response){
	   
	   update_clean_tab_ajax();
	   
		jQuery('div.updated')
			.html('<p><strong>' + response + '</strong></p>') 
			.css('display', 'block'); 
			jQuery('html, body').animate({ scrollTop: 0 }, 'slow');
	   }
	); 

}


function update_clean_tab_ajax(){

	jQuery.post(
	   ajaxurl + '?action=wp_email_reminder_update_clean_tab', 
	   {
		   'wp_email_reminder_nonce' : jQuery('input#wp_email_reminder_nonce').val()
	   }, 
	   function(response){
		jQuery('div#rem_clean_tab_content').html(response);		
	   }
	); 		
}
	

		
function check_uncheck(button){
	if(button.value=='check all'){
		jQuery('table#rem_recipients_tbl input').each(function(){
			jQuery(this).attr('checked', 'checked');
		}); 
		button.value='uncheck all'; 
	}else{ 
		jQuery('table#rem_recipients_tbl input').each(function(){
			jQuery(this).removeAttr('checked');
		});
		button.value='check all'; 
	}//end if
}