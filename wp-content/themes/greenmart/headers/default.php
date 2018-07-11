<header id="tbay-header" class="clearfix site-header header-default <?php echo (greenmart_tbay_get_config('keep_header') ? 'main-sticky-header' : ''); ?>" role="banner">
    <div id="tbay-topbar" class="tbay-topbar hidden-sm hidden-xs">
        <div class="container">
            <div class="topbar-inner clearfix">
                
				<?php if(is_active_sidebar('top-contact')) : ?>
					<div class="pull-left top-contact">
						<?php dynamic_sidebar('top-contact'); ?>
					</div><!-- End Top Contact Widget -->
				<?php endif;?>

                <div class="pull-right ">
				
					<?php if (!(defined('GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED') && GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED) && defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED ): ?>
						<div class="pull-right top-cart-wishlist">
							
							<!-- Cart -->
							<div class="pull-right top-cart hidden-xs">
								<?php get_template_part( 'woocommerce/cart/mini-cart-button' ); ?>
							</div>
							
							<?php if( class_exists( 'YITH_WCWL' ) ) { ?>
								<a class="pull-right text-skin wishlist-icon" href="<?php $wishlist_url = YITH_WCWL()->get_wishlist_url(); echo esc_url($wishlist_url); ?>"><i class="icofont icofont-heart-alt" aria-hidden="true"></i><span class="count_wishlist"><?php $wishlist_count = YITH_WCWL()->count_products(); echo esc_html($wishlist_count); ?></span></a>
							<?php } ?>
						
						</div>
					<?php endif; ?>
					<?php if ( greenmart_tbay_get_config('header_login') ) { ?>
						<ul class="list-inline acount pull-right">
							<i class="icon-login icons"></i>
							<?php if( !is_user_logged_in() ){ ?>
								<li> <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Sign up','greenmart'); ?>"> <?php esc_html_e('Sign up', 'greenmart'); ?> </a></li>
								<li> <a href="<?php echo esc_url( get_permalink( get_option('woocommerce_myaccount_page_id') ) ); ?>" title="<?php esc_html_e('Login','greenmart'); ?>"> <?php esc_html_e('Login', 'greenmart'); ?> </a></li>
							<?php }else{ ?>
								<?php $current_user = wp_get_current_user(); ?>
							  <li>  <span class="hidden-xs"><?php esc_html_e('Welcome ','greenmart'); ?><?php echo ( $current_user->display_name); ?> !</span></li>
							  <li><a href="<?php echo wp_logout_url(home_url()); ?>"><?php esc_html_e('Logout ','greenmart'); ?></a></li>
							<?php } ?>
						</ul>
					<?php } ?>
					
                </div>
				
            </div>
        </div> 
    </div>
    <div class="header-main clearfix">
        <div class="container">
            <div class="header-inner">
                <div class="row">
					<!-- //LOGO -->
                    <div class="logo-in-theme col-md-3 text-center">
                        <?php get_template_part( 'page-templates/parts/logo' ); ?>
                    </div>
					
                    <!-- SEARCH -->
                    <div class="search col-md-6 hidden-sm hidden-xs">
                        <div class="pull-right">
							<?php get_template_part( 'page-templates/parts/productsearchform' ); ?>
						</div>
                    </div>
					
					<!-- Shipping -->
					<?php if(is_active_sidebar('top-shipping')) : ?>
					<div class="top-shipping col-md-3 hidden-sm hidden-xs">
						<?php dynamic_sidebar('top-shipping'); ?>
					</div><!-- End Top shipping Widget -->
					<?php endif;?>
					
                </div>
            </div>
        </div>
    </div>
    <section id="tbay-mainmenu" class="tbay-mainmenu hidden-xs hidden-sm">
        <div class="container"> 
			<?php if ( has_nav_menu( 'category-menu' ) ): ?>
			<div class="pull-left category-inside">
					<h3 class="category-inside-title"><?php esc_html_e('All Categories', 'greenmart'); ?></h3>
					<div class="category-inside-content">
						 <nav class="tbay-topmenu" role="navigation">
							<?php
								$args = array(
									'theme_location'  => 'category-menu',
									'menu_class'      => 'tbay-menu-category list-inline',
									'fallback_cb'     => '',
									'menu_id'         => 'category-menu'
								);
								wp_nav_menu($args);
							?>
						</nav>
					</div>
			</div><!-- End Category Menu -->
			<?php endif;?>
			
            <?php if ( has_nav_menu( 'primary' ) ) : ?>
				<nav data-duration="400" class=" tbay-megamenu slide animate navbar" role="navigation">
				<?php
					$args = array(
						'theme_location' => 'primary',
						'container_class' => 'collapse navbar-collapse',
						'menu_class' => 'nav navbar-nav megamenu',
						'fallback_cb' => '',
						'menu_id' => 'primary-menu',
						'walker' => new greenmart_Tbay_Nav_Menu()
					);
					wp_nav_menu($args);
				?>
				</nav>
            <?php endif; ?>
			
			<!-- Offer -->
			<?php if(is_active_sidebar('top-offer')) : ?>
			<div class="pull-right top-offer">
				<?php dynamic_sidebar('top-offer'); ?>
			</div><!-- End Top offer Widget -->
			<?php endif;?>
			
        </div>      
    </section>
</header>