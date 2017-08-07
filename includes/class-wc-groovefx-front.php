<?php
/**
 * Integration Awesome FRONT.
 *
 * @package  Awesome_Base
 * @category awesome
 * @author   WooThemes
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	class GROOVEFX_Front  {	
		
		/**
		* Construct the plugin.
		*/
		public function __construct() {
			
			require_once(GROOVEFX_INCLUDES.'/function.php');
			wp_enqueue_style( 'groovefx-front-style',GROOVEFX_FILES_URL . 'css/groovefx-front.css', array(), '1.0.3', true );			
			wp_enqueue_script( 'groovefx-modernizr', GROOVEFX_FILES_URL . 'js/modernizr.js', array(), '1.0.0', true );
			wp_enqueue_script( 'groovefx-front-js',GROOVEFX_FILES_URL . 'js/groovefx-front.js', array(), '1.0.6', true );			
			
			add_action( 'plugins_loaded', array( $this, 'init' ) );
			
		}
		
		
		/**
		* Initialize the plugin.
		*/
		public function init() {
				
				
				add_filter( 'woocommerce_add_cart_item_data', array( $this,'groovefx_add_cart_item_data'), 10, 2 );
				add_filter( 'woocommerce_get_cart_item_from_session', array( $this,'groovefx_get_cart_item_from_session'), 20, 2 );
				
				//panier
				add_filter( 'woocommerce_cart_item_name', array( $this,'groovefx_render_meta_on_cart_item'), 1, 3 );
				
				add_action('init',array( $this,'groovefx_remove_loop_button'));
							
				add_action('woocommerce_after_shop_loop_item',array( $this,'groovefx_replace_add_to_cart'));
				
				add_filter( 'woocommerce_cart_item_thumbnail', array( &$this, 'groovefx_cart_item_thumbnail' ), 10, 3 );
				add_filter( 'woocommerce_in_cart_product_thumbnail', array( &$this, 'groovefx_cart_item_thumbnail' ), 10, 3 );
				add_filter( 'woocommerce_cart_item_permalink', array( &$this, 'groovefx_cart_item_link' ), 10, 3 );	
				
				
				add_filter( 'woocommerce_before_add_to_cart_button', array( $this, 'groovefx_add_input' ),98 );
			
				add_action('woocommerce_single_product_summary',array( $this,'groovefx_replace_add_to_cart_product'));
			
				add_action( 'woocommerce_add_order_item_meta', array( $this,'groovefx_add_order_item_meta_custom'), 10, 2 );
				
						
		}
		
		
	


		
		
		
		public function groovefx_add_to_cart_validation( $true, $product_id, $quantity ) {
			global $woocommerce;
			if (isSet($_POST[ 'groovefx_id_groo' ]) and $_POST[ 'groovefx_id_groo' ]!=''  and ctype_alnum($_POST[ 'groovefx_id_groo' ])) {
				
				return true;
			} else {
				$url = get_permalink( $product_id );
				wp_redirect( $url);	
				exit();
			}
			
		}
		
		public static function groovefx_cart_item_link( $link,$cart_item, $cart_item_key ) {
			
			if( isset( $cart_item['groovefx_field'] ) ) {
			  
				return false;
			
			} else {
				
				return $link;
				
			}
		
		}
		
		public static function groovefx_cart_item_thumbnail( $get_image, $cart_item, $cart_item_key ) {
        
        $image_tag = $get_image;
		
		
		
	   //Try to get the design id, first from the cart_item and then from the session
        if( isset( $cart_item['groovefx_field'] ) ) {
			
			$path_img = 'http://img.gfxc2.com/'.get_option('bucket_groovefx').'/'.$cart_item['groovefx_field'].'.jpg';
		
			
            $new_src = 'src="'.$path_img . '" style="width:100%"';			
		    $image_tag = preg_replace( '/src\=".*?"/', $new_src, $get_image );
			$image_tag = preg_replace( '/srcset\=".*?"/', '', $image_tag );
		}    
       
	
        return $image_tag;
    }
		
		
		
		public function groovefx_add_input( $tabs ) {
			echo '<input type="hidden" id="groovefx_id_groo" name="groovefx_id_groo" value="" /><input type="hidden" id="groovefx_coul_groo" name="groovefx_coul_groo" value="" />';
					
			return $tabs;	
		}
		
		public function groovefx_add_cart_item_data($cart_item_meta, $product_id){
			global $woocommerce;

			if (isSet($_POST[ 'groovefx_id_groo' ]) and $_POST[ 'groovefx_id_groo' ]!='' and ctype_alnum($_POST[ 'groovefx_id_groo' ])) {
				if(empty($cart_item_meta['groovefx_field']))
				$cart_item_meta['groovefx_field'] = array();
				
				$cart_item_meta['groovefx_field'] = sanitize_key($_POST[ 'groovefx_id_groo' ]);            
				
				if (isSet($_POST[ 'groovefx_coul_groo' ]) and $_POST[ 'groovefx_coul_groo' ]!='' and  strlen ($_POST[ 'groovefx_coul_groo' ])==7) {
					$cart_item_meta['groovefx_field_2'] = array();				
					$cart_item_meta['groovefx_field_2'] = sanitize_hex_color($_POST[ 'groovefx_coul_groo' ]);   
				} 
				
				return $cart_item_meta;
			} else {
				return $cart_item_meta;
			}
		}
		
		public function groovefx_get_cart_item_from_session( $cart_item, $values ) {

			if (!empty($values['groovefx_field'])) :
				$cart_item['groovefx_field'] = $values['groovefx_field'];
				if (!empty($values['groovefx_field_2'])) {$cart_item['groovefx_field_2'] = $values['groovefx_field_2'];}
				$cart_item = $this->groovefx_woocommerce_add_cart_item_custom( $cart_item );
			endif;

			return $cart_item;

		}
		
		public  function groovefx_woocommerce_add_cart_item_custom( $cart_item ) {

			// operation done while item is added to cart.
			
			return $cart_item;

		}
		
		public function groovefx_render_meta_on_cart_item( $title = null, $cart_item = null, $cart_item_key = null ) {
			if( $cart_item_key && is_cart() && $cart_item['groovefx_field']!='') {
				if ($cart_item['groovefx_field_2']!='') $raj = '<dt class="">'.__( 'Product color', 'woocommerce-groovefx' ).' : </dt><dd ><div style="margin-top:10px;margin-left:10px;height:10px;width:10px;background-color:'.esc_html( $cart_item['groovefx_field_2']).';border:1px solid black;"></div></dd> ';
				else $raj='';
				echo $title. '<dl class="variation">
						 <dt class="">ID groovefx : </dt><dd><p>'.esc_html( $cart_item['groovefx_field']).'</p></dd>'.$raj.'  
						<dt class=""> </dt><dd><p><a href="http://img.gfxc2.com/'.get_option('bucket_groovefx').'/'.esc_html( $cart_item['groovefx_field']).'.jpg" target="_blank">'.__('See your customization', 'woocommerce-groovefx' ).'</a></p></dd>						 
					  </dl>';	
				
			}else {
				echo $title;
			}
		}
		
		
		public 	function groovefx_remove_loop_button(){			
			remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		}
				
		
		public function groovefx_replace_add_to_cart() {
			global $product;
			
		
			if (is_groovefx_product($product)){
				$link = $product->get_permalink();
				echo do_shortcode('<a href="'.$link.'" class="button addtocartbutton">'.__( 'View Product', 'woocommerce-groovefx' ).'</a>');
			} else {
					$link = $product->add_to_cart_url();
				echo do_shortcode('<a href="'.$link.'" class="button addtocartbutton">'.__( 'Add to cart', 'woocommerce-groovefx' ).'</a>');
			}
		}
		
		public function groovefx_replace_add_to_cart_product() {
			global $product;
			
			if (is_ssl()) $var_site = 'https://effet.gfxc2.com/';
			else  $var_site = 'http://effet.gfxc2.com/';
			
			
			echo '<input type="hidden" name="groovefx_language" id="groovefx_language" value="'.strtoupper(explode ( '_' , get_locale())[0]).'">';
			
			
			if (get_option('bucket_groovefx')!='groovefx'  ) {
				 $user = wp_get_current_user();
 
				if ( $user->exists() && get_current_user_id()!=0) {
					$id_client_groo = get_current_user_id();
				}
			}
			echo '<input type="hidden" name="groovefx_id_client" id="groovefx_id_client" value="'.$id_client_groo.'">';
			
			echo '<script>var url_site_groo="'.$var_site.'";var confirm_text="'.__( "Do you want to make a new customization from scratch ?", 'woocommerce-groovefx' ).'";var nom_groo="'.get_option('bucket_groovefx').'";</script>';			
			
			if (is_groovefx_product($product)){
			
				if ($product->product_type=='simple') {
					
					$prod_fx =  get_post_meta( $product->id, 'groovefx_product', true ).get_post_meta( $product->id, 'groovefx_effet', true );
					echo do_shortcode('<form class="cart cart_groovefx_perso"><a id="personnalise_groovefxbouton" href="javascript:groovefx_open(\''.$prod_fx.'\',\'&fx_width='.get_post_meta( $product->id, 'groovefx_width', true ).'&fx_height='.get_post_meta( $product->id, 'groovefx_height', true ).'&fx_couleur='.get_post_meta( $product->id, 'groovefx_colors', true ).'\')" class="groove_single_add_to_cart_button button alt">'.__( 'Customize', 'woocommerce-groovefx' ).'</a></form>');
					woocommerce_template_single_add_to_cart();
					echo '<script >jQuery(".cart").not(".cart_groovefx_perso").css("display","none");</script>';
					
				} else if ($product->product_type=='variable') {
					
					woocommerce_template_single_add_to_cart();					
					echo '<input type="hidden" id="product_groo" name="product_groo" value="" />';		
					echo '<input type="hidden" id="url_var_groo" name="url_var_groo" value="" />';	
					echo "						
					<script > var lst_var_groovefx=[];";
					$lst_var_groovefx = $product->get_available_variations();
					foreach ($lst_var_groovefx as $prod_variation) {
						$post_id = $prod_variation['variation_id'];
						$post_object = get_post($post_id);						
						echo 'lst_var_groovefx['.$post_id.'] = {product:"'.get_post_meta( $post_object->ID, '_select_groovefx', true).get_post_meta( $post_object->ID, '_select_effet', true).'",variable:"&fx_width='.get_post_meta( $post_object->ID, '_groovefx_width', true ).'&fx_height='.get_post_meta( $post_object->ID, '_groovefx_height', true ).'&fx_couleur='.get_post_meta( $post_object->ID, '_groovefx_colors', true ).'"};';
						
					}
					echo "
						jQuery('.variation_id').change( function(){	
							jQuery('.groove_single_add_to_cart_button,.button,.alt').attr('style','display: none !important');
							jQuery('#groovefx_id_groo').val('');;
							jQuery('#groovefx_coul_groo').val(''); 
							
							
							if (jQuery('.variation_id').val()!='') {
								var bout_groo = '<form class=\"cart cart_groovefxmulti\"><a id=\"personnalise_groovefxbouton\" href=\"javascript:groovefx_open( jQuery(\'#product_groo\').val(),jQuery(\'#url_var_groo\').val())\" class=\"groove_single_add_to_cart_button button alt\"  >".__( 'Customize', 'woocommerce-groovefx' )."</a></form>';
								
								jQuery('#product_groo').val(lst_var_groovefx[jQuery('.variation_id').val()].product);								
								jQuery('#url_var_groo').val(lst_var_groovefx[jQuery('.variation_id').val()].variable);
								
								if (!jQuery('.cart_groovefxmulti').length) jQuery('table.variations').after(bout_groo);
								jQuery('#personnalise_groovefxbouton').css('display','');
							} else {
								jQuery('#personnalise_groovefxbouton').attr('style','display: none !important');	
							}
							
						});
						
						jQuery('.groove_single_add_to_cart_button,.button,.alt').attr('style','display: none !important');	
						
					
						</script>						
						";			
				}
			} else {
				woocommerce_template_single_add_to_cart();
			}
		}
		
		public function groovefx_add_order_item_meta_custom( $item_id, $values ) {

			if ( ! empty( $values['groovefx_field'] ) ) {
				
				woocommerce_add_order_item_meta( $item_id, 'groovefx', $values['groovefx_field']); 
				if (isSet($values['groovefx_field_2']) and $values['groovefx_field_2']!='') woocommerce_add_order_item_meta( $item_id, 'color', $values['groovefx_field_2']); 
				
				
			}
		}
		
		
		
	}


	$WC_Front_Awesome = new GROOVEFX_Front( __FILE__ );

?>