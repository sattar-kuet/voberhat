<?php 
global $product;

$time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );
?>
   <div class="product-block grid clearfix" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
		<div class="product-content">
			<div class="block-inner">
				<figure class="image">
					<?php woocommerce_show_product_loop_sale_flash(); ?>
					<a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>" class="product-image">
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
			
				<?php if ( $time_sale ): ?>
					<div class="time">
						<div class="tbay-countdown" data-time="timmer" data-days="<?php echo esc_html__('Days','greenmart'); ?>" data-hours="<?php echo esc_html__('Hours','greenmart'); ?>"  data-mins="<?php echo esc_html__('Mins','greenmart'); ?>" data-secs="<?php echo esc_html__('Secs','greenmart'); ?>"
							 data-date="<?php echo date('m', $time_sale).'-'.date('d', $time_sale).'-'.date('Y', $time_sale).'-'. date('H', $time_sale) . '-' . date('i', $time_sale) . '-' .  date('s', $time_sale) ; ?>">
						</div>
					</div> 
				<?php endif; ?> 
				
				<div class="meta">
					<div class="infor">
						<h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<div class="sub">
						<?php
							/**
							* woocommerce_after_shop_loop_item_title hook
							*
							* @hooked woocommerce_after_shop_loop_item_title - 5
							* @hooked woocommerce_template_loop_price - 10
							*/
							do_action( 'woocommerce_after_shop_loop_item_title');

						?>
						</div>
				
						 <div class="description"><?php echo greenmart_tbay_substring( get_the_excerpt(), 20, '...' ); ?></div>
						 
						<?php
							/**
							* woocommerce_after_shop_loop_item_title hook
							*
							* @hooked woocommerce_template_loop_rating - 5
							* @hooked woocommerce_template_loop_price - 10
							*/
							do_action( 'woocommerce_after_shop_loop_item_title');

						?>
						
					   
						
						<div class="groups-button">
							<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
							<?php
								$action_add = 'yith-woocompare-add-product';
								$url_args = array(
									'action' => $action_add,
									'id' => $product->get_id()
								);
							?> 
							<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
								<a href="#" class="button yith-wcqv-button tbay-tooltip" data-toggle="tooltip" title="<?php echo esc_html__('Quick View', 'greenmart'); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
									<span>
										<i class="icofont icofont-eye-alt"></i>
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
        </div>
		
    </div>
		
