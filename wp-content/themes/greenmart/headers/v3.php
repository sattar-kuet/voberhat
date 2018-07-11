<header id="tbay-header" class="site-header header-v3 hidden-sm hidden-xs <?php echo (greenmart_tbay_get_config('keep_header') ? 'main-sticky-header' : ''); ?>" role="banner">
    <section id="tbay-mainmenu" class="tbay-mainmenu hidden-xs hidden-sm">
        <div class="container-full container"> 
		
			
			
			<div class="row">
				
				<div class="header-inner col-md-5 col-lg-5">
					<div class="row header-logo-search">
						<!-- LOGO -->
						<div class="logo-in-theme">
							<?php get_template_part( 'page-templates/parts/logo' ); ?>
						</div>
					   
						<div class="header-search hidden-sm hidden-xs">
							<div class="search">
								<?php get_template_part( 'page-templates/parts/productsearchform' ); ?>
							</div>	
						</div>
					</div>
					
				</div>
				
				<div class="col-md-6 col-lg-7 header-menu">
					<?php get_template_part( 'page-templates/parts/nav' ); ?>
					
					<div class="right-item pull-right">
						
						<?php if ( greenmart_tbay_get_config('header_login') ) { ?>
							<?php get_template_part( 'page-templates/parts/topbar-account' ); ?>
						<?php } ?>
						
						<?php if ( !(defined('GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED ): ?>
							<div class=" top-cart-wishlist clearfix">
								<div class="pull-right">
									<!-- Setting -->
									<div class="top-cart hidden-xs">
										<?php get_template_part( 'woocommerce/cart/mini-cart-button-3' ); ?>
									</div>
								</div>						

							</div>
						<?php endif; ?>
						
						
						
						
					</div>
				</div>
				
				

			</div>      
        </div>      
    </section>
</header>