<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

$columns = 6;
$classes[] = 'col-lg-'.$columns.' col-md-'.$columns.' col-sm-'.$columns.' col-xs-12 list';

$woo_display = greenmart_tbay_woocommerce_get_display_mode();
if ( $woo_display == 'list' ) { 	
?>
	<div <?php post_class( $classes ); ?>>
	 	<?php wc_get_template_part( 'item-product/inner-list' ); ?>
	</div>
<?php 
} else {
	
	// Store loop count we're currently on
	if ( empty( $woocommerce_loop['loop'] ) ) {
		$woocommerce_loop['loop'] = 0;
	}

	// Store column count for displaying the grid
	if ( empty( $woocommerce_loop['columns'] ) ) {
		$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );
	}

	// Ensure visibility
	if ( ! $product || ! $product->is_visible() ) {
		return;
	}

	// Increase loop count
	 
	// Extra post classes
	$classes = array();
	$columns = 12/$woocommerce_loop['columns'];
	$classes[] = 'col-xs-6 col-lg-'.$columns.' col-md-'.$columns.' col-sm-6 grid';
	?>

	<div <?php post_class( $classes ); ?>>
		 	<?php wc_get_template_part( 'item-product/inner' ); ?>
	</div>

<?php } ?>