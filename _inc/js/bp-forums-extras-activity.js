jQuery(document).ready( function() {

	jQuery('.view-activity').live('click', function() {
		
		var type = jQuery(this).attr('class');
		var id = jQuery(this).attr('id');
		var parentID = id.replace("view-activity", "view-activity-comment");
	
		if ( !jQuery(this).hasClass('open') ) {
			
			jQuery(this).addClass('loading');
			jQuery('#' + id).removeClass('loading').addClass('open');
			jQuery('#' + parentID).slideDown('fast');
				
			return false;

		} else {

			jQuery(this).removeClass('loading, open');
			jQuery('#' + parentID).slideUp('fast');
			return false;

		};
	});

});