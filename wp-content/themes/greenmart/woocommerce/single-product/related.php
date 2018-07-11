<?php
/**
 * Related Products
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}
$per_page = greenmart_tbay_get_config('number_product_releated', 4);
$related = wc_get_related_products( $product->get_id(), $per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'            => 'product',
	'ignore_sticky_posts'  => 1,
	'no_found_rows'        => 1,
	'posts_per_page'       => $per_page,
	'orderby'              => $orderby,
	'post__in'             => $related,
	'post__not_in'         => array( $product->get_id() )
) );

$products = new WP_Query( $args );

if( isset($_GET['releated_columns']) ) { 
	$woocommerce_loop['columns'] = $_GET['releated_columns'];
} else {
	$woocommerce_loop['columns'] = greenmart_tbay_get_config('releated_product_columns', 4);
}

if ( $products->have_posts() ) : ?>

	<div class="related products widget ">
		<h3 class="widget-title"><span><?php esc_html_e( 'Related Products', 'greenmart' ); ?></span></h3>
		<?php wc_get_template( 'layout-products/carousel-related.php' , array('loop'=>$products,'rows' => '1', 'pagi_type' => 'no', 'nav_type' => 'yes','columns'=>$woocommerce_loop['columns'],'posts_per_page'=>$products->post_count,'screen_desktop'=>$woocommerce_loop['columns'],'screen_desktopsmall'=>'3','screen_tablet'=>'2','screen_mobile'=>'2' ) ); ?>

	</div>

<?php endif;

wp_reset_postdata();