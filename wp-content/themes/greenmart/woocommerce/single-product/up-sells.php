<?php
/**
 * Single Product Up-Sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$per_page = greenmart_tbay_get_config('number_product_releated', 4);

$upsells = $product->get_upsell_ids();

if ( sizeof( $upsells ) == 0 ) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $posts_per_page,
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->get_id() ),
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

if( isset($_GET['releated_columns']) ) { 
	$woocommerce_loop['columns'] = $_GET['releated_columns'];
} else {
	$woocommerce_loop['columns'] = greenmart_tbay_get_config('releated_product_columns', 4);
}

if ( $products->have_posts() ) : ?>

	<div class="upsells widget products">
		<h2 class="widget-title"><?php esc_html_e( 'You may also like&hellip;', 'greenmart' ) ?></h2>
		<?php wc_get_template( 'layout-products/carousel-related.php' , array( 'loop'=>$products,'rows' => '1', 'pagi_type' => 'no', 'nav_type' => 'yes','columns'=>$woocommerce_loop['columns'],'posts_per_page'=>$products->post_count,'screen_desktop'=>$woocommerce_loop['columns'],'screen_desktopsmall'=>'3','screen_tablet'=>'2','screen_mobile'=>'2' ) ); ?>
	</div>

<?php endif;

wp_reset_postdata();
