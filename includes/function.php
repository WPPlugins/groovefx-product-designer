<?php


function is_groovefx_product($product) {
	if ($product->product_type=='simple') {
		$prod_fx =  get_post_meta( $product->id, 'groovefx_product', true );
	} else if ($product->product_type=='variable') {
		$lst_var = $product->get_available_variations();
		
		foreach ($lst_var as $prod_variation) {
			
			$post_id = $prod_variation['variation_id'];
			$post_object = get_post($post_id);						
			
			if (get_post_meta( $post_object->ID, '_select_groovefx', true)!='') {
				
				$prod_fx = 'ok';
				break;
			}
		}
	}
	if (isSet($prod_fx ) and $prod_fx !='') return true;
	else return false;
}
		
?>