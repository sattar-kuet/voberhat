<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage greenmart
 * @since greenmart 1.0
 */

$footer = apply_filters( 'greenmart_tbay_get_footer_layout', 'default' );

?>

	</div><!-- .site-content -->

	<footer id="tbay-footer" class="tbay-footer" role="contentinfo">
		<?php if ( !empty($footer) ): ?>
			<?php greenmart_tbay_display_footer_builder($footer); ?>
		<?php else: ?>
			<?php if ( is_active_sidebar( 'footer' ) ) : ?>
				<div class="footer">
					<div class="container">
						<div class="row">
							<?php dynamic_sidebar( 'footer' ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>
			<div class="tbay-copyright">
				<div class="container">
					<div class="copyright-content">
						<div class="text-copyright text-center">
						<?php
								$allowed_html_array = array( 'a' => array('href' => array() ) );
								echo wp_kses(__('Copyright &copy; 2017 - greenmart. All Rights Reserved. <br/> Powered by <a href="//thembay.com/">ThemBay</a>', 'greenmart'), $allowed_html_array);
							
						?>

						</div> 
					</div>
				</div>
			</div>
			
		<?php endif; ?>			
	</footer><!-- .site-footer -->

	<?php $tbay_header = apply_filters( 'greenmart_tbay_get_header_layout', greenmart_tbay_get_config('header_type') );
		if ( empty($tbay_header) ) {
			$tbay_header = 'v1';
		}
	?>
	
	<?php 

	$_id = greenmart_tbay_random_key();

	?>

	<?php
	if ( greenmart_tbay_get_config('back_to_top') ) { ?>
		<div class="tbay-to-top <?php echo esc_attr($tbay_header); ?>">
			
			<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
			<a class="text-skin wishlist-icon" href="<?php $wishlist_url = YITH_WCWL()->get_wishlist_url(); echo esc_url($wishlist_url); ?>"><i class="icofont icofont-heart-alt" aria-hidden="true"></i><span class="count_wishlist"><?php $wishlist_count = YITH_WCWL()->count_products(); echo esc_attr($wishlist_count); ?></span></a>
			<?php } ?>
			
			
			<?php if ( !(defined('GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED ): ?>
			<!-- Setting -->
			<div class="tbay-cart top-cart hidden-xs">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="mini-cart">
					<i class="icofont icofont-shopping-cart"></i>
					<span class="mini-cart-items-fixed">
					   <?php echo sprintf( '%d', WC()->cart->cart_contents_count );?>
					</span>
				</a>
			</div>
			<?php endif; ?>
			
			<a href="#" id="back-to-top">
				<p><?php esc_html_e('TOP', 'greenmart'); ?></p>
			</a>
		</div>
		
		
	<?php
	}
	?>
	
	

</div><!-- .site -->

<?php wp_footer(); ?>

</body>
</html>

