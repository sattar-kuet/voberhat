<header id="tbay-header" class="site-header header-v5 header-v7 hidden-sm hidden-xs <?php echo (greenmart_tbay_get_config('keep_header') ? 'main-sticky-header' : ''); ?>" role="banner">

    <div class="header-main clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">
                    <!-- LOGO -->
                    <div class="pull-left logo-in-theme col-md-2">
                        <?php get_template_part( 'page-templates/parts/logo' ); ?>
                    </div>
				    <div class="box-search-5 col-md-5">
					   <?php get_template_part( 'page-templates/parts/productsearchform','category' ); ?>
					</div>
					
					
					
					<div class="col-md-5 right-item">
						<div class="row">
							<?php if ( greenmart_tbay_get_config('header_login') ) { ?>
							<div class="top-account-v5" >
								<?php get_template_part( 'page-templates/parts/topbar-account' ); ?>
							</div>
							<?php } ?>
						
							<?php if ( !(defined('GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED ): ?>
								<div class="top-cart-wishlist ">
									
										<!-- Setting -->
										<div class="top-cart hidden-xs">
											<?php get_template_part( 'woocommerce/cart/mini-cart-button' ); ?>
										</div>
														
								
									
								</div>
							<?php endif; ?>

							<!-- Main menu -->
							<div class="tbay-mainmenu topbar-mobile pull-right ">
								 <div class="top active-mobile">
									<button data-toggle="offcanvas" class="btn btn-sm btn-danger btn-offcanvas btn-toggle-canvas offcanvas pull-right" type="button">
									   <i class="icofont icofont-navigation-menu"></i>
									</button>
								 </div>
								

							</div>
						</div>
						
					</div>
					
					
					
                </div>
            </div>
        </div>
    </div>
</header>