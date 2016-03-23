<?php
function ecart_update_permalink_slugs() 
{
	global $wpdb;

	$wpsc_pageurl_option = array(
		'ecart' => '[ECART]',
		'shopping_cart' => '[SHOPPING_CART]',
		'customer_profile' => '[CUSTOMER_PROFILE]',
		'product_special' => '[PRODUCT_SPECIAL]'
	);

	$ids = array();

	foreach ( $wpsc_pageurl_option as $option_key => $page_string ) {
		
		$id = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` = 'page' AND `post_content` LIKE '%$page_string%' LIMIT 1" );

		$ids[$page_string] = $id;

		$the_new_link = get_page_link( $id );
		
		if (!$id )
			$the_new_link = get_home_url()."/?";

		if ( stristr( get_option( $option_key ), "https://" ) )
			$the_new_link = str_replace( 'http://', "https://", $the_new_link );

		update_option( $option_key, $the_new_link );
	}
}

function ecart_refresh_page_urls( $post_id, $post ) 
{
	if ( ! current_user_can( 'manage_options' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return;

	if ( ! in_array( $post->post_status, array( 'publish', 'private' ) ) )
		return;

	ecart_update_permalink_slugs();

	return $post_id;
}

add_action( 'save_post', 'ecart_refresh_page_urls', 10, 2 );


function frontend_ecart( $content = '' ) {
	if ( ! in_the_loop() )
		return $content;
	if ( preg_match( "/\[ECART\]/", $content ) ) {
		define( 'DONOTCACHEPAGE', true );

		ob_start();

		include( ECART_FRONTEND_PATH . '/ecart.php' );
		$output = ob_get_clean();
		$content = preg_replace( "/(<p>)*\[ECART\](<\/p>)*/", '[ECART]', $content );
		return str_replace( '[ECART]', $output, $content );
	} else {
		return $content;
	}
}

function frontend_shopping_cart( $content = '' ) {
	if ( ! in_the_loop() )
		return $content;
	if ( preg_match( "/\[SHOPPING_CART\]/", $content ) ) {
		define( 'DONOTCACHEPAGE', true );

		ob_start();

		include( ECART_FRONTEND_PATH . '/shopping_cart.php' );
		$output = ob_get_clean();
		$content = preg_replace( "/(<p>)*\[SHOPPING_CART\](<\/p>)*/", '[SHOPPING_CART]', $content );
		return str_replace( '[SHOPPING_CART]', $output, $content );
	} else {
		return $content;
	}
}

function frontend_customer_profile( $content = '' ) {
	if ( ! in_the_loop() )
		return $content;
	if ( preg_match( "/\[CUSTOMER_PROFILE\]/", $content ) ) {
		define( 'DONOTCACHEPAGE', true );

		ob_start();

		include( ECART_FRONTEND_PATH . '/customer_profile.php' );
		$output = ob_get_clean();
		$content = preg_replace( "/(<p>)*\[CUSTOMER_PROFILE\](<\/p>)*/", '[CUSTOMER_PROFILE]', $content );
		return str_replace( '[CUSTOMER_PROFILE]', $output, $content );
	} else {
		return $content;
	}
}

function frontend_product_special( $content = '' ) {
	if ( ! in_the_loop() )
		return $content;
	if ( preg_match( "/\[PRODUCT_SPECIAL\]/", $content ) ) {
		define( 'DONOTCACHEPAGE', true );

		ob_start();

		include( ECART_FRONTEND_PATH . '/product_special.php' );
		$output = ob_get_clean();
		$content = preg_replace( "/(<p>)*\[PRODUCT_SPECIAL\](<\/p>)*/", '[PRODUCT_SPECIAL]', $content );
		return str_replace( '[PRODUCT_SPECIAL]', $output, $content );
	} else {
		return $content;
	}
}


function wpsc_enable_page_filters( $excerpt = '' ) 
{
	add_filter( 'the_content', 'frontend_ecart', 12 );
	add_filter( 'the_content', 'frontend_shopping_cart', 12 );
	add_filter( 'the_content', 'frontend_customer_profile', 12 );
	add_filter( 'the_content', 'frontend_product_special', 12 );
	return $excerpt;
}

wpsc_enable_page_filters();
?>