<?php


class WPCG_Option_Page {

	public function __construct() {
		add_action( 'admin_init', array( $this, 'wpcg_plugin_register_setting' ) );
		add_action( 'admin_menu', array( $this, 'wpcg_plugin_register_options_page' ) );
		add_action( 'admin_enqueue_scripts', array($this, 'wpcg_plugin_load_style_script') );
	}

	public function wpcg_plugin_register_setting() {
		register_setting(
							'wpcg_plugin_options_group', 
							'wpcg_allowed_pt'
						);
	}


	public function wpcg_plugin_register_options_page() {
		add_menu_page(
			__( 'WP Customize Gallery Plugin', 'textdomain' ), //page title
			__( 'WP Customize Gallery', 'textdomain' ), // menu title
			'manage_options', // required for this menu to be displayed to the user
			'wpcg-option', // page slug
			array(
				$this,
				'wpcg_plugin_options_page' // callback function
				),
			'dashicons-format-gallery',
			5 // position in the menu order this item should appear
		);
	}

	public function wpcg_plugin_options_page() {
		if(!current_user_can('manage_options')){
	        wp_die(__('You do not have sufficient permissions to access this page.'));
	    }
		?>
		<div>
			<h1><?php echo 'WP Customize Gallery Plugin'; ?></h1>
			<form method="post" action="options.php" novalidate="novalidate">
				<?php settings_fields( 'wpcg_plugin_options_group' ); ?>
				<?php do_settings_sections( 'wpcg_plugin_options_group' ); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="wpcg_allowed_pt">Post Types</label>
							</th>
							<td>
								<?php 
								$post_types = get_post_types(); //print_r($post_types);
								$saved_pt = get_option('wpcg_allowed_pt');
								//print_r($saved_pt); 
								?>
								<?php foreach ($post_types as $key => $post_type) { ?>
								<div>
									<input type="checkbox" value="<?php echo $post_type; ?>" name="wpcg_allowed_pt[]" <?php if(in_array($post_type, $saved_pt)){ echo "checked"; } ?>/>
									<label for="wpcg_allowed_pt"><?php echo ucfirst($post_type); ?></label>
								</div>
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function wpcg_plugin_load_style_script() {
		$current_screen = get_current_screen();
		if ( strpos($current_screen->base, 'wpcg')) {
			wp_enqueue_style(
				'wp-customize-gallery-style', 
				plugins_url( 'assets/css/wpcg-style.css', 
					dirname(__FILE__) ), 
				[], 
				date('YmdHis'), 
				'all'
			);
			wp_enqueue_script(
				'wp-customize-gallery-script', 
				plugins_url( 'assets/js/wpcg-script.js', 
					dirname(__FILE__) ), 
				['jquery'], 
				date('YmdHis'), 
				true
			);
		}
	}

}


$wpcg_option_page_obj = new WPCG_Option_Page();
