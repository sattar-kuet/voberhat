<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;
	
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

if($woocommerce_loop['columns'] == 5) {
	$columns = 'cus-5';
}else {
	$columns = 12/$woocommerce_loop['columns'];
}


$desktop         	 	=      isset($screen_desktop) ? (12/$screen_desktop) : (12/$woocommerce_loop['columns']);
$desktopsmall          	=      isset($screen_desktopsmall) ? (12/$screen_desktopsmall) : (12/$woocommerce_loop['columns']);
$tablet          		=      isset($screen_tablet) ? (12/$screen_tablet) : (12/$woocommerce_loop['columns']);
$mobile          		=      isset($screen_mobile) ? (12/$screen_mobile) : 6;


$classes[] = 'col-xs-'. $mobile .' col-lg-'.$desktop.' col-md-'.$desktopsmall.' col-sm-'.$tablet. ' '.$class_desktop. ' '.$class_desktopsmall. ' '.$class_tablet. ' '.$class_mobile;

?>
<div <?php post_class( $classes ); ?> >
	<?php $product_item = isset($product_item) ? $product_item : 'inner'; ?>
 	<?php wc_get_template_part( 'item-product/'.$product_item ); ?>
</div>
