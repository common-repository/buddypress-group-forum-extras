jQuery(document).ready( function() {

     var hideDelay = 500;  
     var hideTimer = null;
     
	 var hideFunction = function() {  
         
		 if (hideTimer)  
             clearTimeout(hideTimer);
			 
         hideTimer = setTimeout( function() { 
			jQuery('.preview-popup-tr').slideUp("fast", function() {
				jQuery('.preview-popup-tr').remove();
			});
         }, hideDelay);  
     };  

    jQuery('.topic-title').live('mouseover', function() {
        
		if ( !jQuery(this).data('hoverIntentAttached') ) {
            
			jQuery(this).data('hoverIntentAttached', true);
			
            jQuery(this).hoverIntent ( 
				config = {
					sensitivity: 4, // number = sensitivity threshold (must be 1 or higher)
					interval: 500, // number = milliseconds for onMouseOver polling interval
					over: function() { // hoverIntent mouseOver
					
						if ( hideTimer )  
							clearTimeout( hideTimer );  
							
						var id = jQuery(this).attr('href');
						id = id.substring(id.indexOf("/forum/topic/")+13,id.length - 1);
						
						if (!id)
							return;
						
						//make sure the same preview is not already open otherwise they stack
						if ( jQuery('#preview-'+ id).length == 0 ) {
							//what if a theme/plugin added more td's 
							var tdcount = jQuery(this).parent().parent().children().size();

							jQuery(this).parent().parent().after('<tr class="preview-popup-tr" id="preview-'+ id +'" style="display:none"><td colspan='+ tdcount +' class="preview-first-post">'+ jQuery('#' + id).html() +'</td></tr>');

							//allow the mouse to actually hover over the preview post (click links and such)
							jQuery('.preview-popup-tr').mouseover(function() {
								if ( hideTimer )
									clearTimeout( hideTimer );
							});
						   
							// Hide after mouseout  
							jQuery('.preview-popup-tr').mouseout( hideFunction );

							//display it and remove display:block as that shifts everything right
							jQuery('#preview-' + id).slideDown("fast", function() {
								 jQuery('#preview-' + id).css('display','');
							});
						}

					},
					timeout: 500, // number = milliseconds delay before onMouseOut  
					out: hideFunction
				}
            );
			
            jQuery(this).trigger('mouseover');
        }
    });
	
     // Allow mouse over of details without hiding details  
     jQuery('.preview-popup-tr').mouseover(function() {  
         if ( hideTimer )
             clearTimeout( hideTimer );  
     });  
   
     // Hide after mouseout  
     jQuery('.preview-popup-tr').mouseout( hideFunction );  
	
});