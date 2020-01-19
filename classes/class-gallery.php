<?php

class WPCG_Gallery {

	public $saved_pt;

	public function __construct() {
		add_action('add_meta_boxes', array($this, 'wpcg_gallery_meta_field'));
		add_action('admin_print_scripts', array($this, 'wpcg_gallery_meta_field_script'));
    	add_action('admin_print_styles', array($this, 'wpcg_gallery_meta_field_style'));
    	add_action( 'save_post', array($this, 'wpcg_save_meta_field'), 10, 3);
    	add_filter('wpcg_get_gallery_images', array($this, 'wpcg_get_gallery_images_callable'), 10, 1);
		$this->saved_pt = get_option('wpcg_allowed_pt');
	}

	public function wpcg_gallery_meta_field() {
		add_meta_box(
					'wpcg_gallery_meta_field_id', // meta box id
					__( 'Gallery Images', 'textdomain' ), // Title of the meta box.
					array($this, 'wpcg_gallery_meta_field_callback'), // Function that fills the box with the desired content
					$this->saved_pt, //screens on which to show the box
					'side',
					'high'
					);
	}

	public function wpcg_gallery_meta_field_callback() {
		global $post;
		?>
		<input type="hidden" name="wpcg_gallery_noncename" id="wpcg_gallery_noncename" value="<?php echo wp_create_nonce( plugin_basename(__FILE__) ) ?>" />
			<div class="editor-post-featured-image">
				<div class="wpcg_gallery_outputs">
					<?php $saved_wpcg_image_ids = apply_filters('wpcg_get_gallery_images', $post->ID); //print_r($saved_wpcg_images); ?>
					<?php
						if(!empty($saved_wpcg_image_ids)){
							?>
							<ul class="wpcg_gallery_images">
								<?php foreach($saved_wpcg_image_ids as $key => $image_id){ ?>
									<li><img src="<?php echo wp_get_attachment_url($image_id); ?>"  id="wpcg_attachment_image_id_<?php echo $image_id; ?>" /></li>
								<?php } ?>
							</ul>
							<?php
							foreach($saved_wpcg_image_ids as $key => $image_id){
								?>
								<input type="hidden" name="wpcg_attachment_id_array[]" id="wpcg_attachment_id_<?php echo $image_id; ?>" value="<?php echo $image_id; ?>" />
								<?php
							}
						}
					?>
				</div>
				<div class="wpcg_gallery_actions">
				
					<button type="button" class="components-button wpcg_add_button wpcg_gallery_meta_field_button" <?php if(empty($saved_wpcg_image_ids)){ ?>style="display: block;"<?php }else{ ?>style="display: none;"<?php } ?>>Set Galley Images</button>
					<button type="button" class="components-button wpcg_update_button wpcg_gallery_meta_field_button" <?php if(!empty($saved_wpcg_image_ids)){ ?>style="display: block;"<?php }else{ ?>style="display: none;"<?php } ?>>Update Galley Images</button>
					<button type="button" class="components-button wpcg_remove_button wpcg_gallery_meta_field_button" <?php if(!empty($saved_wpcg_image_ids)){ ?>style="display: block;"<?php }else{ ?>style="display: none;"<?php } ?>>Remove Galley Images</button>
				</div>
			</div>
		<?php
	}

	public function wpcg_gallery_meta_field_style() {
		global $post;
		if(in_array($post->post_type, $this->saved_pt)){
			wp_enqueue_style('thickbox');
			wp_register_style(
								'wpcg-gallery-meta-field-upload-style',
								plugins_url( 'assets/css/wpcg-meta-field.css', 
																dirname(__FILE__) ),
								[], 
								date('YmdHis'), 
								'all'
							);
			wp_enqueue_style('wpcg-gallery-meta-field-upload-style');
		}
	}

	public function wpcg_gallery_meta_field_script() {
		global $post;
		if(in_array($post->post_type, $this->saved_pt)){
		    wp_enqueue_script('media-upload');
		    wp_enqueue_script('thickbox');
		    wp_register_script(
		    	'wpcg-gallery-meta-field-upload-script', 
		    	plugins_url( 'assets/js/wpcg-upload-script.js', 
						dirname(__FILE__) ), 
		    	array('jquery','media-upload','thickbox'),
				date('YmdHis'), 
				true
		    );
		    wp_enqueue_script('wpcg-gallery-meta-field-upload-script');
		    wp_enqueue_media();
		}
	}

	public function wpcg_save_meta_field($post_id, $post, $is_update) {

		if( !isset($_REQUEST['wpcg_gallery_noncename']) ){
			//die('nonce field not found');
			return $post_id;
		}

		if (!wp_verify_nonce($_REQUEST['wpcg_gallery_noncename'], plugin_basename(__FILE__))){
            //die('nonce field not verified');
            return $post_id;
        }

		if( !in_array($post->post_type, $this->saved_pt) ){
			return $post_id;
		}

		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE){
        	return $post_id;
		}

		if( !empty($_REQUEST['wpcg_attachment_id_array'])){
			//print_r($_REQUEST['wpcg_attachment_id_array']); die;
			//print_r(serialize($_REQUEST['wpcg_attachment_id_array'])); die;
			update_post_meta( $post_id, 'wpcg_gallery_images', serialize($_REQUEST['wpcg_attachment_id_array']));
		}else{
			update_post_meta( $post_id, 'wpcg_gallery_images', '');
		}
	}

	public function wpcg_get_gallery_images_callable($post_id) {
		if(!empty(unserialize(get_post_meta($post_id, 'wpcg_gallery_images', true)))) {
			return unserialize(get_post_meta($post_id, 'wpcg_gallery_images', true));
		}else{
			return false;
		}
	}

}

$wpcg_gallery_obj = new WPCG_Gallery();


