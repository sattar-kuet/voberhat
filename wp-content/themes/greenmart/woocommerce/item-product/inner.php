<?php 
global $product;

$rating				= wc_get_rating_html( $product->get_average_rating());


?>
<div class="product-block grid" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
	<div class="product-content">
		<div class="block-inner">
			<figure class="image">
				<?php woocommerce_show_product_loop_sale_flash(); ?>
				<a title="<?php the_title(); ?>" href="<?php echo the_permalink(); ?>" class="product-image">
					<?php
						/**
						* woocommerce_before_shop_loop_item_title hook
						*
						* @hooked woocommerce_show_product_loop_sale_flash - 10
						* @hooked woocommerce_template_loop_product_thumbnail - 10
						*/
						remove_action('woocommerce_before_shop_loop_item_title','woocommerce_show_product_loop_sale_flash', 10);
						do_action( 'woocommerce_before_shop_loop_item_title' );
					?>
				</a>
				<?php (class_exists( 'YITH_WCBR' )) ? greenmart_brands_get_name($product->get_id()): ''; ?>

				<div class="button-wishlist">
					<?php
						if( class_exists( 'YITH_WCWL' ) ) {
							echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
						}
					?>  
				</div>
			  

				
			</figure>

			

		</div>
		<div class="caption">
			<div class="meta">
				<div class="infor">
					<div class="name-subtitle">
						<h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

						<?php do_action( 'greenmart_after_title_tbay_subtitle'); ?>
						
					</div>
					
					<?php
						/**
						* woocommerce_after_shop_loop_item_title hook
						*
						* @hooked woocommerce_template_loop_rating - 5
						* @hooked woocommerce_template_loop_price - 10
						*/
						
						do_action( 'woocommerce_after_shop_loop_item_title');

					?>
					
				</div>
			</div>  
			<div class="groups-button clearfix">
				<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
				<?php
					$action_add = 'yith-woocompare-add-product';
					$url_args = array(
						'action' => $action_add,
						'id' => $product->get_id()
					);
				?>
				
				<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
					<a href="#" class="button yith-wcqv-button tbay-tooltip" data-toggle="tooltip" title="<?php echo esc_html__('Quick view', 'greenmart'); ?>"  data-product_id="<?php echo esc_attr($product->get_id()); ?>">
						<span>
							<i class="icofont icofont-eye-alt"> </i>
						</span>
					</a>
				<?php } ?>	
				
				<?php if( class_exists( 'YITH_Woocompare' ) ) { ?>
					<?php
						$action_add = 'yith-woocompare-add-product';
						$url_args = array(
							'action' => $action_add,
							'id' => $product->get_id()
						);
					?>
					<div class="yith-compare">
						<a href="<?php echo wp_nonce_url( add_query_arg( $url_args ), $action_add ); ?>" data-toggle="tooltip" title="<?php echo esc_html__('Compare', 'greenmart'); ?>" class="compare tbay-tooltip" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
							<i class="icofont icofont-refresh"></i>
						</a>
					</div>
				<?php } ?> 
			</div>		
		</div>
    </div>
</div>
