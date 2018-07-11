<header id="tbay-header" class="site-header header-v2 hidden-sm hidden-xs <?php echo (greenmart_tbay_get_config('keep_header') ? 'main-sticky-header' : ''); ?>" role="banner">
	<div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs">
        <div class="container">
	
            <div class="topbar-inner clearfix">
                <div class="row">
					<?php if(is_active_sidebar('top-contact')) : ?>
						<div class="col-md-4 top-contact">
							<?php dynamic_sidebar('top-contact'); ?>
						</div><!-- End Top Contact Widget -->
					<?php endif;?>
					
					
					
					<div class="pull-right col-md-8 text-right">
						
						<?php if ( greenmart_tbay_get_config('header_login') ) { ?>
							<?php get_template_part( 'page-templates/parts/topbar-account' ); ?>
						<?php } ?>
						
						<?php if ( !(defined('GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED ): ?>
							<div class="pull-right top-cart-wishlist">
								
								<!-- Cart -->
								<div class="top-cart hidden-xs">
									<?php get_template_part( 'woocommerce/cart/mini-cart-button-2' ); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>

				</div>
				
            </div>
        </div> 
    </div>
	
	<div class="header-main clearfix">
        <div class="container">
            <div class="header-inner clearfix row">
                <!-- LOGO -->
                <div class="logo-in-theme pull-left">
                    <?php get_template_part( 'page-templates/parts/logo' ); ?>
                </div>
				
				<!-- Main menu -->
				<div class="tbay-mainmenu pull-right">

					<?php get_template_part( 'page-templates/parts/nav' ); ?>
					
					 <div class="pull-right header-search-v2">
						<div class="header-setting ">
							<div class="pull-right">

							<button type="button" class="btn-search-totop">
								<i class="icofont icofont-search-alt-1"></i>
							</button>
							<?php get_template_part( 'page-templates/parts/productsearchform' ); ?>

							</div>
						</div>
					</div>
						<!-- //Search -->
					
                </div>
				
               
               
				
            </div>
        </div>
    </div>
</header>