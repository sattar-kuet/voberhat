<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage greenmart
 * @since greenmart 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php if ( greenmart_tbay_get_config('preload',false) ) { ?>
	<?php greenmart_get_select_preloader(); ?>
<?php } ?>
<?php $tbay_header = apply_filters( 'greenmart_tbay_get_header_layout', greenmart_tbay_get_config('header_type') );
		if ( empty($tbay_header) ) {
			$tbay_header = 'v1';
		}
	?>
<div id="wrapper-container" class="wrapper-container <?php echo esc_attr($tbay_header); ?>">

	<?php get_template_part( 'page-templates/parts/offcanvas-menu' ); ?>

	<?php get_template_part( 'page-templates/parts/device/topbar-mobile' ); ?>
	<?php get_template_part( 'page-templates/parts/device/footer-mobile' ); ?>

	<?php get_template_part( 'page-templates/parts/topbar-mobile' ); ?>

	
	<?php get_template_part( 'headers/'.$tbay_header ); ?>

	<div id="tbay-main-content">
