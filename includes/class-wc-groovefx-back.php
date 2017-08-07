<?php 
/** 
 * Integration Admin Groovefx.
 *
 * @package  Groovefx_Base
 * @category Groovefx
 * @author   WooThemes
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( is_admin() ) {
	class GROOVEFX_Back  {	
		
		private $groovefx_liste;
		private $taille_liste;
		private $effet_liste;
		/**
		* Construct the plugin.
		*/
		public function __construct() {
			
			
			$this->groovefx_recup_donne_effet();
			$this->groovefx_recup_donne_prod();
			$this->groovefx_recup_donne_taille();
			
			wp_enqueue_style( 'groovefx-back-style',GROOVEFX_FILES_URL . 'css/groovefx-back.css', array(), '1.0.1', false );		
			wp_enqueue_script( 'groovefx-modernizr', GROOVEFX_FILES_URL . 'js/modernizr.js', array(), '1.0.0', true );
			wp_enqueue_script( 'groovefx-back-js',GROOVEFX_FILES_URL . 'js/groovefx-back.js', array(), '1.0.2', true );
			add_action( 'plugins_loaded', array( $this, 'init' ) );
		}
		
		
		/**
		* Initialize the plugin.
		*/
		public function init() {

			// Checks if WooCommerce is installed.
			if ( class_exists( 'WC_Integration' ) ) {		
				
				
				
				add_filter('plugin_row_meta',   array( $this,'groovefx_register_plugins_links'), 10, 2);
				
				
				add_action( 'admin_menu', array( $this, 'groovefx_create_plugin_settings_page' ) );
				
				
				add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'groovefx_panel_tab' ) );
				add_action( 'woocommerce_product_write_panels',     array( $this, 'groovefx_write_panel' ) );
				
				add_action( 'woocommerce_process_product_meta', array( $this,  'groovefx_save_data'));
				add_action( 'admin_notices',  array( $this, 'groovefx_admin_notices' ) );
				add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'groovefx_variable_fields'), 10, 3 );
				//add_action( 'woocommerce_product_after_variable_attributes_js', array( $this, 'groovefx_variable_fields_js') );
				add_action( 'woocommerce_save_product_variation',array( $this,  'groovefx_save_variable_fields'), 10, 1 );
			
				add_action( 'woocommerce_admin_order_item_headers', array( $this,  'groovefx_add_order_item_header'  ));
				
				add_action( 'woocommerce_admin_order_item_values', array( $this,'groovefx_admin_order_item_values' ), 10, 3 ); 
				
			}
		}
	
		
		
		
	
/**
 * register_plugins_links 
 * Direct link to the settings page from the plugin page * @param  array  $links
 * @param  string $file
 * @return array
 */
public function groovefx_register_plugins_links ($links, $file){
		
	if ($file == 'groovefx/groovefx.php') {
		$links[] = '<a href="admin.php?page=groovefx_options">' . __('Settings','woocommerce-groovefx') . '</a>';
		$links[] = '<a href="http://groovefx.fr/en/#faq" target="_blank">' . __('FAQ','woocommerce-groovefx') . '</a>';
		$links[] = '<a href="http://groovefx.fr/en/#contact" target="_blank">' . __('Support','woocommerce-groovefx') . '</a>';
	}
	return $links;
}

	
	public function groovefx_recup_donne_taille(){
		global $wpdb;
		$tab = array();
		$table_name_taille = $wpdb->prefix . 'groovefx_list_taille';
		
		$list = $wpdb->get_results( 'SELECT id_groo FROM '.$table_name_taille.' WHERE 1 ', OBJECT );	
		if ($list) {
			foreach ( $list as $result ) {	
				$tab[$result->id_groo] = '';
			}
		}	else {
			
			$tab['p26']='';	
		}
		

		$this->taille_liste =  serialize($tab);
	}
		
	public function groovefx_recup_donne_prod(){
		global $wpdb;
		$tab = array();
		$tab['']='none';	
		
		$table_name_produit = $wpdb->prefix . 'groovefx_list_product';
		
		$list = $wpdb->get_results( 'SELECT id_groo,descr FROM '.$table_name_produit.' WHERE 1 ', OBJECT );	
		if ($list) {
			
			foreach ( $list as $result ) {	
				$tab[$result->id_groo] = $result->id_groo.' '.$result->descr;
			}
		}	else {
			
			$tab['p1']='p1 mug';	
			$tab['p26']='p26 Canvas border 20 mm';	
			$tab['p120']='p120 Coque Iphone 5';	
		}
		
		
		$this->groovefx_liste =  serialize($tab);
	}
	
	public function groovefx_recup_donne_effet(){
	
		$tab = array();
		$tab['']=__('express', 'woocommerce-groovefx' );	
		$tab['x']=__('creative', 'woocommerce-groovefx' );
		$tab['r']=__('designer', 'woocommerce-groovefx' );
		$tab['g']=__('PopArt andy 4 photos portrait', 'woocommerce-groovefx' );
		$tab['h']=__('PopArt andy 4 photos landscape', 'woocommerce-groovefx' );
		$tab['j']=__('modern PopArt 4 photos portrait', 'woocommerce-groovefx' );
		$tab['k']=__('modern PopArt 4 photos landscape', 'woocommerce-groovefx' );
		
		$this->effet_liste =  serialize($tab);
	}
	
	
	public function groovefx_admin_notices() {

			global $woocommerce;

			
			if( function_exists('get_woocommerce_currency') && version_compare($woocommerce->version, '2.1', '<') ): ?>
			<div class="error">
		        <p><?php _e( 'Please update WooCommerce to the latest version! GrooveFX only works with version 2.1 or newer.', 'woocommerce-groovefx' ); ?></p>
		    </div>
			<?php endif;
			

		}
	
		
		/**
		 * Adds a new tab to the Product Data postbox in the admin product interface
		 */
		public function groovefx_panel_tab() {
			echo "<li class=\"product_tabs_lite_tab show_if_simple \"><a href=\"#woocommerce_product_tabs_groovefx\">GrooveFX </a></li>";
		}
	
	
	
		/**
		 * Adds the panel to the Product Data postbox in the product interface
		 */
		public function groovefx_variable_fields( $loop, $variation_data, $variation ) {
			
			
			
		?>
			<div class="options">
					<?php
					// Text Field
					woocommerce_wp_select( 
						array( 
						'name'          => '_select_groovefx['.$loop.']', 
						'label'       => __( 'GrooveFX product', 'woocommerce-groovefx' ).' : ',
						'value'       => esc_attr(get_post_meta( $variation->ID, '_select_groovefx', true )),
							'class'     =>  'css-groovefx-'.$loop,
						'options' => unserialize($this->groovefx_liste)	
						)
					);
				
					woocommerce_wp_select( 
						array( 
						'name'          => '_select_effet['.$loop.']', 
						'label'       => __( 'GrooveFX feature', 'woocommerce-groovefx' ).' : ',
						'value'       => esc_attr(get_post_meta( $variation->ID, '_select_effet', true )),
							'class'     =>  'css-groo-'.$loop,
						'options' => unserialize($this->effet_liste)	
						)
					);
				
				
				$list_taill_dispo =  unserialize($this->taille_liste);
				
				if (get_post_meta( $variation->ID, '_select_groovefx', true )!='' &&  array_key_exists(get_post_meta( $variation->ID, '_select_groovefx', true ),$list_taill_dispo)) echo '<div style="" id="groovefx_liste_taille-'.$loop.'" name="groovefx_liste_taille-'.$loop.'">';
				else echo '<div style="display:none" id="groovefx_liste_taille-'.$loop.'" name="groovefx_liste_taille-'.$loop.'">';
				
				woocommerce_wp_text_input( 
					array( 
					'id'          => '_groovefx_width['.$loop.']', 
					'label'       => __( 'Width', 'woocommerce-groovefx' ), 
					'placeholder' => __( 'Width of your object in cm (rounded up) without border', 'woocommerce-groovefx' ),					
					'value'       => esc_attr(get_post_meta( $variation->ID, '_groovefx_width', true ))
					)
				);
			
				woocommerce_wp_text_input( 
					array( 
						'id'          => '_groovefx_height['.$loop.']', 
						'label'       => __( 'Height', 'woocommerce-groovefx' ), 
						'placeholder' => __( 'Height of your object in cm (rounded up) without border', 'woocommerce-groovefx' ),	
											
						'value'       => esc_attr(get_post_meta( $variation->ID, '_groovefx_height', true ))
					)
				);
				
			
				echo '</div>';
				
				woocommerce_wp_textarea_input( 
					array( 
						'id'          => '_groovefx_colors['.$loop.']',
						'class'     => 'long css-groo-color-'.$loop,
						'label'       => __( 'If your product have different colors. Eg : Tshirt', 'woocommerce-groovefx' ), 
						'placeholder' =>  __( 'Type your colors, hexadecimal without # separated by comma. Eg : 564586,a1a2a3,FFFFF', 'woocommerce-groovefx' ),	
											
						'value'       => esc_textarea(get_post_meta( $variation->ID, '_groovefx_colors', true ))
					)
				);
					echo '	<p class="form-field groovefx_list_color_'.$loop.' ">
				<div style="float: left; width: 150px;    font-size: 12px;  padding: 5px 9px;  line-height: 24px;">'.__( 'List of colors', 'woocommerce-groovefx' ).'</div>
				<div id="list_colors_'.$loop.'" name="list_colors_'.$loop.'" style="float:left;width:50%;overflow-x:auto;height: 40px; overflow-y: hidden; white-space: nowrap;">
				</div>
			</p>
			';
				
				wp_nonce_field( 'groovefx_variable_nonce', 'groovefx_variable_nonce' );
				
				echo '<input type="hidden" name="is_taille_ok_'.$loop.'" id="is_taille_ok_'.$loop.'" value="0">';
				echo '<script>
			jQuery(document).ready( function () {	
				jQuery(".css-groovefx-'.$loop.'").select2();
				var array_taille = [';
				foreach ($list_taill_dispo as $key => $value) {
					echo '"'.$key.'",';
				}
			echo '];
				jQuery(".css-groovefx-'.$loop.'").on("change", function (e) {
					
					var result =  array_taille.indexOf(jQuery(".css-groovefx-'.$loop.'  option:selected").val());  
					if (result!=-1) {
						jQuery("#groovefx_liste_taille-'.$loop.'").css("display","block");
						jQuery("#is_taille_ok_'.$loop.'").val(1);
					} else {
						jQuery("#groovefx_liste_taille-'.$loop.'").css("display","none");
						jQuery("#is_taille_ok_'.$loop.'").val(0);
					}
				});
				var result =  array_taille.indexOf("'.esc_js(get_post_meta( $variation->ID, '_select_groovefx', true )).'");  
				if (result!=-1) {
					jQuery("#groovefx_liste_taille-'.$loop.'").css("display","block");
					jQuery("#is_taille_ok_'.$loop.'").val(1);
				} else {
					jQuery("#groovefx_liste_taille-'.$loop.'").css("display","none");
					jQuery("#is_taille_ok_'.$loop.'").val(0);
				}
			
				jQuery(".css-groo-color-'.$loop.'").keyup(function() {
					valid_color('.$loop.');
				});
				jQuery(".css-groo-color-'.$loop.'").keydown(function() {
					valid_color('.$loop.');
				});
					valid_color('.$loop.');	
			});
			</script>';
				?>
				<div style="clear:both;"></div>
			</div>	
				
		<?php 
		}	
		
		public function groovefx_save_variable_fields( $post_id ) {
			
			if( ! isset( $_POST['groovefx_variable_nonce'] ) || ! wp_verify_nonce( $_POST['groovefx_variable_nonce'], 'groovefx_variable_nonce' ) ){
				return ;
			} 
			
			if (isset( $_POST['variable_sku'] ) ) :
			//	$variable_sku          = $_POST['variable_sku'];
				$variable_post_id      = $_POST['variable_post_id'];
				
				
				foreach ($variable_post_id as $key => $values) {
					if (isSet($_POST['_select_groovefx'][$key]) and preg_match('/^[-.0-9a-z]*$/i',$_POST['_select_groovefx'][$key])) update_post_meta( $values, '_select_groovefx', sanitize_text_field( $_POST['_select_groovefx'][$key] ) );
					if (isSet($_POST['_select_effet'][$key])  and (ctype_alpha($_POST['_select_effet'][$key]) or $_POST['_select_effet'][$key]=='' )) update_post_meta( $values, '_select_effet', sanitize_text_field( $_POST['_select_effet'][$key] ) );
					if (isSet($_POST['_groovefx_colors'][$key]) and  preg_match('/^[,0-9a-zA-Z]*$/i',$_POST['_groovefx_colors'][$key])) update_post_meta( $values, '_groovefx_colors', sanitize_text_field($_POST['_groovefx_colors'][$key]) );
					
					if (isSet($_POST['is_taille_ok_'.$key]) && $_POST['is_taille_ok_'.$key]!=0 && is_numeric($_POST['_groovefx_width'][$key]) &&  is_numeric($_POST['_groovefx_height'][$key]) ){
						update_post_meta( $values, '_groovefx_width',sanitize_text_field( $_POST['_groovefx_width'][$key]) );
						update_post_meta( $values, '_groovefx_height', sanitize_text_field($_POST['_groovefx_height'][$key]) );
					} else  {
						
						update_post_meta( $values, '_groovefx_width', '' );
						update_post_meta( $values, '_groovefx_height', '' );			
					}
				
				
				}			
				
			endif;
		}
	
		/**
		 * Adds the panel to the Product Data postbox in the product interface
		 */
		public function groovefx_write_panel() {
			
	
			global $post, $product;
			echo '<div id="woocommerce_product_tabs_groovefx" class="panel wc-metaboxes-wrapper woocommerce_options_panel">';	
			woocommerce_wp_select( 
					array( 
					'id'      => 'groovefx_product', 
					'label'   => __( 'GrooveFX product', 'woocommerce-groovefx' ).' : ', 
					'class'     => 'css-groovefx class-select-groovefx',
					'options' => unserialize($this->groovefx_liste)
					)
				);
				woocommerce_wp_select( 
					array( 
					'id'      => 'groovefx_effet', 
					'label'   => __( 'GrooveFX feature', 'woocommerce-groovefx' ).' : ', 
					'class'     => 'class-select-groovefx',
					'options' => unserialize($this->effet_liste)
					)
				);
				
				$list_taill_dispo =  unserialize($this->taille_liste);
				
				if (get_post_meta( $post->ID, 'groovefx_product', true )!='' &&  array_key_exists(get_post_meta( $post->ID, 'groovefx_product', true ),$list_taill_dispo)) echo '<div style="" id="groovefx_liste_taille" name="groovefx_liste_taille">';
				else echo '<div style="display:none" id="groovefx_liste_taille" name="groovefx_liste_taille">';
				
				woocommerce_wp_text_input( 
					array( 
					'id'          => 'groovefx_width', 
					'label'       => __( 'Width', 'woocommerce-groovefx' ), 
					'placeholder' => __( 'Width of your object in cm (rounded up) without border', 'woocommerce-groovefx' ),					
					'value'       => esc_attr(get_post_meta( $post->ID, 'groovefx_width', true ))
					)
				);
			
				woocommerce_wp_text_input( 
					array( 
						'id'          => 'groovefx_height', 
						'label'       => __( 'Height', 'woocommerce-groovefx' ), 
						'placeholder' => __( 'Height of your object in cm (rounded up) without border', 'woocommerce-groovefx' ),					
						'value'       => esc_attr(get_post_meta( $post->ID, 'groovefx_height', true ))
					)
				);
				
			
				echo '</div>';
		
				woocommerce_wp_textarea_input( 
					array( 
						'id'          => 'groovefx_colors', 
						'class'     => 'long',
						'label'       => __( 'If your product have different colors. Eg : Tshirt', 'woocommerce-groovefx' ), 
						'placeholder' =>  __( 'Type your colors, hexadecimal without # separated by comma. Eg : 564586,a1a2a3,FFFFF', 'woocommerce-groovefx' ),	
						'value'       => esc_textarea(get_post_meta( $post->ID, 'groovefx_colors', true ))
					)
				);
					echo '	<p class="form-field groovefx_list_color ">
				<div style="float: left; width: 150px;    font-size: 12px;  padding: 5px 9px;  line-height: 24px;">'.__( 'List of colors', 'woocommerce-groovefx' ).'</div>
				<div id="list_colors" name="list_colors" style="float:left;width:50%;overflow-x:auto;height: 40px; overflow-y: hidden; white-space: nowrap;">
				</div>
			</p>
			';
			echo '</div>';
			
			wp_nonce_field( 'groovefx_simple_nonce', 'groovefx_simple_nonce' ); 
			
			echo '<input type="hidden" name="is_taille_ok" id="is_taille_ok" value="0">';
			echo '<script>
			jQuery(document).ready( function () {	
				var array_taille = [';
				foreach ($list_taill_dispo as $key => $value) {
					echo '"'.$key.'",';
				}
			echo '];
				jQuery("#groovefx_product").on("change", function (e) {
					var result =  array_taille.indexOf(jQuery("#groovefx_product").val());  
					if (result!=-1) {
						jQuery("#groovefx_liste_taille").css("display","block");
						jQuery("#is_taille_ok").val(1);
					} else {
						jQuery("#groovefx_liste_taille").css("display","none");
						jQuery("#is_taille_ok").val(0);
					}
				});
				var result =  array_taille.indexOf(jQuery("#groovefx_product").val());  
				if (result!=-1) {
					jQuery("#groovefx_liste_taille").css("display","block");
					jQuery("#is_taille_ok").val(1);
				} else {
					jQuery("#groovefx_liste_taille").css("display","none");
					jQuery("#is_taille_ok").val(0);
				}
			
				
			
				jQuery( "#groovefx_colors" ).keyup(function() {
					valid_color("");
				});
				jQuery( "#groovefx_colors" ).keydown(function() {
					valid_color("");
				});
					valid_color("");	
			});
			</script>';
		}
		
		
		
		
		
		public function groovefx_save_data( $post_id ) {
			
			if( ! isset( $_POST['groovefx_simple_nonce'] ) || ! wp_verify_nonce( $_POST['groovefx_simple_nonce'], 'groovefx_simple_nonce' ) ){
				return ;
			}
			
			if (isSet($_POST['groovefx_product']) and preg_match('/^[-.0-9a-z]*$/i',$_POST['groovefx_product'])){
				update_post_meta( $post_id, 'groovefx_product', sanitize_text_field($_POST['groovefx_product']) );
			
			}	
			if (isSet($_POST['groovefx_effet']) and ( ctype_alpha($_POST['groovefx_effet']) or $_POST['groovefx_effet']=='') ){
				update_post_meta( $post_id, 'groovefx_effet', sanitize_text_field($_POST['groovefx_effet']) );
			
			}			
			if (isSet($_POST['groovefx_colors']) and preg_match('/^[,0-9a-zA-Z]*$/i',$_POST['groovefx_colors'])){
				update_post_meta( $post_id, 'groovefx_colors', sanitize_text_field($_POST['groovefx_colors']) );
			
			}	
			if (isSet($_POST['is_taille_ok']) && $_POST['is_taille_ok']!=0 && is_numeric($_POST['groovefx_width']) && is_numeric($_POST['groovefx_height']) ){
			
				update_post_meta( $post_id, 'groovefx_width', sanitize_text_field($_POST['groovefx_width']) );
				update_post_meta( $post_id, 'groovefx_height', sanitize_text_field($_POST['groovefx_height']));
			} else  {
				
				update_post_meta( $post_id, 'groovefx_width', '' );
				update_post_meta( $post_id, 'groovefx_height', '' );			
			}
		}
		
		

		
		
		public function groovefx_add_order_item_header() {
				echo '
					<th class="item-groovefx">GrooveFX</th>
				';
			}
			
		     
			
		public function groovefx_admin_order_item_values( $_product, $item, $item_id ) {
				
			$groovefx_data = wc_get_order_item_meta( $item_id, 'groovefx' );
			$groovefx_color = wc_get_order_item_meta( $item_id, 'color' );
			$api_groovefx = get_option('api_groovefx' );
		
		
		
				
				echo '   <td class="item-groovefx">'; 
				if (isSet($groovefx_data) and $groovefx_data!=null and  $groovefx_data!=''){
						$path_img = 'http://img.gfxc2.com/'.get_option('bucket_groovefx').'/'.esc_attr($groovefx_data).'.jpg';		
					 ?>
				<div class="groovefx-item">
					<div style="float:left;width:150px;">
						<?php	if ($api_groovefx != '') {	 ?>
							<a href="http://effet.gfxc2.com/production/rendu-production.php?fx_nom=<?php echo $api_groovefx;?>&fx_id=<?php echo esc_attr($groovefx_data);?>" target="_blank" ><?php _e( 'Get HD image', 'woocommerce-groovefx' );?></a>
						<?php } else { ?>
							<?php _e( 'You need to have an account with groovefx to get your HD image', 'woocommerce-groovefx' );?>
						<?php }  ?>
					</div>
					<div  style="float:left;width:150px;height:150px;background-color:white;background-position: center;background-size: contain;background-repeat: no-repeat;background-image: url('<?php echo $path_img;?>');">
			 
				</div>
					

				</div> <?php
						}
				echo '</td>';
				
			}
		

		
		 public function groovefx_create_plugin_settings_page() {

			
			add_menu_page( 'GrooveFX', 'GrooveFX', 'manage_options', 'groovefx_options', array( $this, 'plugin_settings_page_content' ), GROOVEFX_FILES_URL . '/img/groove.png', '56' );
		}
		
		public function plugin_settings_page_content() {
			if( $_POST['updated'] === 'true' ){
				$this->handle_form();
			} 
			
		
			
		
			
			?>
			
			<div class="wrap">
				<h2>GrooveFX Settings Page</h2>
				<form method="POST">
					<input type="hidden" name="updated" value="true" />
					<?php wp_nonce_field( 'groovefx_admin_nonce', 'groovefx_admin_nonce' ); ?>
					
					
					
					<div style="display:block; width:100%;">
						<div style="margin-top:25px">
							<div style="float:left;display:block; width:40%;">
								<table id="tab_general" name="tab_general" class="form-table groovefx_talbe" style="">
									<tbody>
										
										<tr>
											<th scope="row"><?php _e( 'GROOVEFX API KEY', 'woocommerce-groovefx' ); ?></th>
											<td>
												<input name="api_groovefx" id="api_groovefx" placeholder="" value="<?php echo esc_attr(get_option('api_groovefx')); ?>" type="text">
												<p class="description"> <?php _e( 'Enter your GrooveFX API KEY', 'woocommerce-groovefx' ); ?></p>
											</td>
										</tr>
										<tr>
											<th scope="row"><?php _e( 'GROOVEFX CLIENT NAME', 'woocommerce-groovefx' ); ?></th>
											<td><input name="bucket_groovefx" id="bucket_groovefx" placeholder="" value="<?php echo esc_attr(get_option('bucket_groovefx')); ?>" type="text">
												<p class="description"><?php echo  __( "Enter your GrooveFX client name", 'woocommerce-groovefx' );?></p>
											</td>
										</tr>
										<tr>
										<td colspan="2"> <input type="submit" name="download_groo_list" id="download_groo_list" class="button button-primary" value="<?php _e( 'Update products list', 'woocommerce-groovefx' ); ?>">
										</td>
									</tr>
										<tr >
										<td colspan="2"> <?php _e( 'For more information go to groovefx.fr', 'woocommerce-groovefx' ); ?> : <a href="http://www.groovefx.fr/EN" target="_blank" title="GrooveFX WebSite">www.groovefx.fr</a></td>
									</tr>
									
									
									<tr>
										<td colspan="2"> <?php submit_button(); ?>
										</td>
									</tr>	
									</tbody>
								</table>	
							</div>
								<div style="float:left;display:block; width:60%;">
								<table id="tab_general" name="tab_general" class="form-table groovefx_talbe" style="">
								<tbody>		
										<tr >
									<td colspan="2"> <?php _e( "<h3>This Woocommerce extension is just a bridge between the famous SAAS application GROOVEFX and your website.</h3><br>

<H2>START FIRST</H2>
You can try GrooveFX without account, but also without HD output:
To link a Woocommerce product to a GrooveFX product, just go to the Woocommerce product page you want to get customizable.<br>
For simple products, just select a product in the droplist (GrooveFX tab).<br>
For the Woocommerce variable products, each variation contain this droplist.<br><br>
You'll have 5 possible inputs: The GrooveFX product, the GrooveFX feature, the widh and the height (option if the product have variable size, see catalog),
and colors if your products need to have a choice of colors (like tshirt.).<br>
Once your client bought a product customized with GrooveFX, you will recover the ready-to-print file in the order page, within the 'GET HD IMAGE' button.
<br><br><br>
<H2>WHY ARE THERE ONLY 3 PRODUCTS IN THE DROPLIST?</H2>
You will first need to refresh the list, clicking on 'UPDATE PRODUCTS LIST' on the left side of this page.
<br><br><br>
<H2>CAN I CREATE MY OWN PRODUCTS?</H2>
Nope: if you don't find the right product you need in our 2500+ products list, you can contact us in order to add it in our catalog FOR FREE!
<a href='http://groovefx.fr/cat/index_en.php' target='_blank' title='GrooveFX Product Catalog'>GrooveFX Product Catalog</a>
<br><br><br>
<H2>WILL I BE ABLE TO CHANGE ANYTHING IN MY PRODUCT DESIGNER FRONTPAGE?</H2>
We can add fonts, or even your own cliparts on your account, FOR FREE. We can also manage little changes in your product designer frontpage (colors, logo...)
<br><br><br>
<H2>OK, I REALLY LOVE YOUR APPLICATION. SO WHAT'S NEXT?</H2>
The next step is simple: just go on <a href='http://www.groovefx.fr/EN' target='_blank' title='GrooveFX WebSite'>GrooveFX website</a> and click on 'START NOW'", 'woocommerce-groovefx' ); ?></td>
								</tr>
								<tr >
									<td colspan="2"> <?php _e( 'For more information go to groovefx.fr', 'woocommerce-groovefx' ); ?> : <a href="http://www.groovefx.fr/EN" target="_blank" title="GrooveFX WebSite">www.groovefx.fr</a></td>
								</tr>
								
								
								
								</tbody>
							</table>
						</div>
						
						</div>
					
					</div>
					
				  
				</form>
			</div> 
			<?php
		}
		public function handle_form() {
			if( ! isset( $_POST['groovefx_admin_nonce'] ) || ! wp_verify_nonce( $_POST['groovefx_admin_nonce'], 'groovefx_admin_nonce' ) ){ ?>
			   <div class="error">
				   <p>Sorry, your nonce was not correct. Please try again.</p>
			   </div> <?php
			   exit;
			} else {
			   
				$api_groovefx = sanitize_text_field( $_POST['api_groovefx'] );
				$bucket_groovefx = sanitize_text_field( $_POST['bucket_groovefx'] );
				
				
				
				if( $bucket_groovefx=='' or ctype_alnum($bucket_groovefx)) update_option( 'bucket_groovefx', $bucket_groovefx );
				if( $api_groovefx=='' or preg_match('/^[-0-9a-z]*$/i',$api_groovefx)) update_option( 'api_groovefx', $api_groovefx );
				
				
				if (isSet($_POST['download_groo_list']) ) {
					global $wpdb;
					
					if (strtoupper(explode ( '_' , get_locale())[0])=='FR') $url_fic_prod = 'http://effet.gfxc2.com/externe/liste_produit.csv';
					else $url_fic_prod =  'http://www.groovefx.fr/cat/liste_produit-cat_en.csv';
					
					$fic = fopen($url_fic_prod, "r");
					
					if ($fic) {
						$table_name_produit = $wpdb->prefix . 'groovefx_list_product';
						$wpdb->query( "delete from ".$table_name_produit." where 1;");
						$table_name_taille = $wpdb->prefix . 'groovefx_list_taille';
						$wpdb->query( "delete from ".$table_name_taille." where 1;");
												
						
						
						if (ctype_alnum($bucket_groovefx) && $bucket_groovefx!='groovefx') {
							
							$fic = fopen('http://effet.gfxc2.com/externe/liste_produit_'.$bucket_groovefx.'.csv', "r");
							if ($fic) {
																
								while($tab=fgetcsv($fic,1024,';')){
								
									$wpdb->insert($table_name_produit, array(
										   'id_groo'  =>sanitize_text_field($tab[0]), 
										   'descr'=> str_replace('-',' ',sanitize_title($tab[1]))
										   ));    
								}			
							}
							
						} 
						
						$fic = fopen($url_fic_prod, "r");
						if ($fic) {
							
												
							while($tab=fgetcsv($fic,1024,';')){
							
								$wpdb->insert($table_name_produit, array(
                                       'id_groo'  =>sanitize_text_field($tab[0]), 
                                       'descr'=> str_replace('-',' ',sanitize_title($tab[1]))
                                       ));    
								
							}								
											
						}
						
						
						$fic = fopen('http://effet.gfxc2.com/externe/liste_taille.csv', "r");
						if ($fic) {
														
							while($tab=fgetcsv($fic,1024,';')){								
								
								$wpdb->insert($table_name_taille, array(
										   'id_groo'  =>sanitize_text_field($tab[0])
										   ));    
									
							}								
						
						
													
						}
						
					} else {
						$error = __( 'Impossible to connect to groovefx server. Please retry later or contact us at support@groovefx.fr', 'woocommerce-groovefx' );
					}
				}
				
				
				if (!isSet($error)) {
				?>
				
					<div class="updated">
						
						<p>Data saved.</p>
					</div> <?php
				}else { 
				
				?>
				
					<div class="updated">
						
						<p><?php echo $error;?>.</p>
					</div> <?php
				}
				
			}
		}
		
		
		
		
	}


	$WC_Admin_Groovefx = new GROOVEFX_Back( __FILE__ );

}	
?>