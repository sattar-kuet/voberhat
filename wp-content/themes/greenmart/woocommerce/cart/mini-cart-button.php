<?php   global $woocommerce; ?>
<div class="tbay-topcart">
 <div id="cart" class="dropdown version-1">
        <span class="text-skin cart-icon">
			<i class="icofont icofont-shopping-cart"></i>
			<span class="mini-cart-items">
			   <?php echo sprintf( '%d', WC()->cart->cart_contents_count );?>
			</span>
		</span>
        <a class="dropdown-toggle mini-cart" data-toggle="dropdown" aria-expanded="true" role="button" aria-haspopup="true" data-delay="0" href="#" title="<?php esc_html_e('View your shopping cart', 'greenmart'); ?>">
            
			<span class="sub-title"><?php echo esc_html__('My Shopping Cart ', 'greenmart'); ?> <i class="icofont icofont-rounded-down"></i> </span>
			<span class="qty"><?php echo WC()->cart->get_cart_subtotal();?></span>
            
        </a>            
        <div class="dropdown-menu"><div class="widget_shopping_cart_content">
            <?php woocommerce_mini_cart(); ?>
        </div></div>
    </div>
</div>    