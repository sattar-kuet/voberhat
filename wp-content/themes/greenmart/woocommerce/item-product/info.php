<?php 
global $product;


?>
<div class="product-container">
<div class="product-block" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <figure class="image pull-left">
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

    </figure> 
    <div class="caption-list">
        
        <div class="meta">

         <h3 class="name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php
                /**
                * woocommerce_after_shop_loop_item_title hook
                *
                * @hooked woocommerce_template_loop_rating - 5
                * @hooked woocommerce_template_loop_price - 10
                */
                do_action( 'woocommerce_after_shop_loop_item_title');

            ?>
            <p><?php echo  the_excerpt();  ?></p>
            <div class="button-groups add-button">
                <?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
                <?php
                    $action_add = 'yith-woocompare-add-product';
                    $url_args = array(
                        'action' => $action_add,
                        'id' => $product->get_id()
                    );
                ?>
            </div>
            <div class="action-bottom clearfix">                
                 <?php
                if( class_exists( 'YITH_WCWL' ) ) {
                    echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
                }
            ?>   
    
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
			
			<?php if (class_exists('YITH_WCQV_Frontend')) { ?>
				<div class="quick-view">
                    <a href="#" class="button yith-wcqv-button tbay-tooltip" data-toggle="tooltip" title="<?php echo esc_html__('Quick View', 'greenmart'); ?>" data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                        <span>
                            <i class="icofont icofont-eye-alt"></i>
                        </span>
                    </a>
				</div>
			<?php } ?>
		
            </div>

        </div>    
        
    </div>      
</div>
</div>
