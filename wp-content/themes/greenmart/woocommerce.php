<?php

global $product;

get_header();
$sidebar_configs = greenmart_tbay_get_woocommerce_layout_configs();

$page_title = '';
if( is_shop()){
	$page_title .= esc_html__('Shop', 'greenmart');
}else if( is_singular( 'product' ) ) {
	$page_title .= get_the_title();
} else {
	$page_title .= woocommerce_page_title(false);
}

if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) {
	$sidebar_configs['main']['class'] .= ' pull-right';
}

?>

<?php do_action( 'greenmart_woo_template_main_before' ); ?>


<section id="main-container" class="main-content <?php echo apply_filters('greenmart_tbay_woocommerce_content_class', 'container');?>">
	<div class="row">
		
		<?php if ( !is_singular( 'product' ) ) : ?>

		<?php if ( isset($sidebar_configs['left']) && isset($sidebar_configs['right']) ) : ?>
			<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
			  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
			   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
			  	</aside>
			</div>
		<?php endif; ?>

		<?php endif; ?>

		<div id="main-content" class="<?php  echo ( !is_singular( 'product' ) ) ? 'archive-shop' : 'singular-shop'; ?> col-xs-12 <?php echo ( !is_singular( 'product' ) ) ? esc_attr($sidebar_configs['main']['class']) : ''; ?>">
			<div class="top-archive">

				<?php if ( !is_singular( 'product' ) ) : ?>
					<?php if(is_active_sidebar('top-archive-product')) : ?>
							<?php dynamic_sidebar('top-archive-product'); ?>
						<!-- End Top Archive Product Widget -->
					<?php endif;?>
				<?php endif;?>

			</div>
			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">

					<?php  
				 if ( is_singular( 'product' ) ) {

		            while ( have_posts() ) : the_post();

		                wc_get_template_part( 'content', 'single-product' );

		            endwhile;

		        } else { ?>
		            <?php if ( apply_filters( 'woocommerce_show_page_title', true ) && isset($sidebar_configs['descreption']) &&  $sidebar_configs['descreption'] ) : ?>
		            		<h2 class="page-title title-woocommerce"><?php woocommerce_page_title(); ?></h2>
		            <?php endif; ?>



		            <?php

		            	if( isset($sidebar_configs['descreption']) &&  $sidebar_configs['descreption'] ) {  
			            	do_action( 'woocommerce_archive_description' ); 
			            }
		            ?>


		            

		            <?php if ( have_posts() ) : ?>



						<?php if((is_shop() && 'subcategories' !== get_option('woocommerce_shop_page_display')) ||(is_product_category() && 'subcategories' !== get_option('woocommerce_category_archive_display')) ): ?>	

							<?php do_action('woocommerce_before_shop_loop'); ?>

						<?php endif; ?>


			            <?php if((is_shop() && '' !== get_option('woocommerce_shop_page_display')) ||(is_product_category() && '' !== get_option('woocommerce_category_archive_display'))) : ?>
						
						<ul class="all-subcategories row">
							<?php woocommerce_output_product_categories(); ?>
							<li class="clearfix"></li>
						</ul>				
						
						<?php endif; ?>

						<?php remove_filter( 'woocommerce_product_loop_start', 'woocommerce_maybe_show_product_subcategories' ); ?>
		                <?php woocommerce_product_loop_start(); ?>

		                   
		                    <?php while ( have_posts() ) : the_post(); ?>
		                    	
		                        <?php wc_get_template_part( 'content', 'product' ); ?>

		                    <?php endwhile; // end of the loop. ?>

		                <?php woocommerce_product_loop_end(); ?>
		                

						<?php if( (is_shop() && 'subcategories' !== get_option('woocommerce_shop_page_display')) ||(is_product_category() && 'subcategories' !== get_option('woocommerce_category_archive_display')) || (is_product_category() && 'subcategories' !== get_option('woocommerce_category_archive_display')) || is_tax('yith_product_brand') ): ?>

		               		<?php do_action('woocommerce_after_shop_loop'); ?>

		               	<?php endif; ?>

		            <?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

		                <?php wc_get_template( 'loop/no-products-found.php' ); ?>

		            <?php endif;
		        }
				?>

				</div><!-- #content -->
			</div><!-- #primary -->

			<?php do_action( 'greenmart_woo_template_main_primary_after' ); ?>

		</div><!-- #main-content -->
		
		<?php if ( !is_singular( 'product' ) ) : ?>
			<?php if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) : ?>
				<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
				  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
				   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
				  	</aside>
				</div>
			<?php endif; ?>
			
			<?php if ( isset($sidebar_configs['right']) ) : ?>
				<div class="<?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
				  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
				   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
				  	</aside>
				</div>
			<?php endif; ?>
		<?php endif; ?>

	</div>
</section>

<?php if ( is_singular( 'product' ) ) : ?>

	<div class="woo-after-single-product-summary">
		<div class="container">
			<div class="row">

				<?php if ( isset($sidebar_configs['left']) && isset($sidebar_configs['right']) ) : ?>
					<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
					  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
					  	</aside>
					</div>
				<?php endif; ?>
	 

				<div class="woo-after-single-content col-xs-12 <?php echo esc_attr($sidebar_configs['main']['class']); ?>">
					
					<?php 
						/**
						 * woocommerce_after_single_product_summary hook
						 *
						 * @hooked woocommerce_output_product_data_tabs - 10
						 * @hooked woocommerce_upsell_display - 15
						 * @hooked woocommerce_output_related_products - 20
						 */
						do_action( 'woocommerce_after_single_product_summary' ); 
					?>

				</div>

				<?php if ( isset($sidebar_configs['left']) && !isset($sidebar_configs['right']) ) : ?>
					<div class="<?php echo esc_attr($sidebar_configs['left']['class']) ;?>">
					  	<aside class="sidebar sidebar-left" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					   		<?php dynamic_sidebar( $sidebar_configs['left']['sidebar'] ); ?>
					  	</aside>
					</div>
				<?php endif; ?>
				
				<?php if ( isset($sidebar_configs['right']) ) : ?>
					<div class="<?php echo esc_attr($sidebar_configs['right']['class']) ;?>">
					  	<aside class="sidebar sidebar-right" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
					   		<?php dynamic_sidebar( $sidebar_configs['right']['sidebar'] ); ?>
					  	</aside>
					</div>
				<?php endif; ?>


			</div>
		</div>
	</div>

<?php endif; ?>

<?php

get_footer();
