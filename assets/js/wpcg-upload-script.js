(function($) {
	console.log("wpcg-upload-script.js loaded"); // for checking

	$(document).ready(function() {

		var wpcg_plugin_media_upload;

		$("button.wpcg_add_button, button.wpcg_update_button").click(function(e){

		    e.preventDefault();
		    // If the uploader object has already been created, reopen the dialog
		    if (wpcg_plugin_media_upload) {
		    	wpcg_plugin_media_upload.open();
		    	return;
		    }
		    // Extend the wp.media object
		    wpcg_plugin_media_upload = wp.media.frames.file_frame = wp.media({
		    	title: 'Upload Gallery Images',
		    	button: {text: 'Upload Images'},
		    	multiple: true, //allowing for multiple image selection
		    	library: {
		    		type: [ 'video', 'image' ]
		    	}
		    });
		    /*
			*When frame is open, select existing image attachments from custom field
			*/
		    wpcg_plugin_media_upload.on('open', function(){
		    	if($("input[name^='wpcg_attachment_id_array']").length > 0){
			    	var selected_ids = [];
			    	$("input[name^='wpcg_attachment_id_array']").each(function(){
			    		//console.log($(this).val());
			    		selected_ids.push($(this).val());
			    	});
			    	//console.log(selected_ids);

			    	var selection = wpcg_plugin_media_upload.state().get('selection');
			    	selected_ids.forEach(function(id) {
						attachment = wp.media.attachment(id);
						attachment.fetch();
						selection.add( attachment ? [ attachment ] : [] );
					});
		    	}
		    });
		    /*
			*When multiple images are selected, get the multiple attachment objects
			*/
			wpcg_plugin_media_upload.on('select', function(){
				var attachments = wpcg_plugin_media_upload.state().get('selection').map(function(attachment){
					attachment.toJSON();
					return attachment;
				});
				var images_output = '';
				var hidden_fields = '';
				images_output += '<ul class="wpcg_gallery_images">';
				for (var i = 0; i<attachments.length; i++) {
					//console.log(attachments[i].id, attachments[i].attributes.url);
					images_output += '<li><img src="' + 
                    attachments[i].attributes.url + '"  id="wpcg_attachment_image_id_' + attachments[i].id + '" /></li>';
					hidden_fields += '<input type="hidden" name="wpcg_attachment_id_array[]" id="wpcg_attachment_id_' + attachments[i].id + '" value="' + attachments[i].id + '" />';
				}
				images_output += '</ul>';
				$(".wpcg_gallery_outputs").html(images_output);
				$(".wpcg_gallery_outputs").append(hidden_fields);
				$("button.wpcg_update_button, button.wpcg_remove_button").show();
				$("button.wpcg_add_button").hide();
			});

			wpcg_plugin_media_upload.open();

		});

		$("button.wpcg_remove_button").click(function(){
			$(".wpcg_gallery_outputs").html('');
			//$(".wpcg_gallery_outputs").append('<input type="hidden" name="wpcg_attachment_id_array[]" id="" value="" />');
			$("button.wpcg_update_button, button.wpcg_remove_button").hide();
			$("button.wpcg_add_button").show();
		});

	});
}(jQuery));