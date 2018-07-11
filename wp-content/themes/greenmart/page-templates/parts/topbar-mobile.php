<div class="topbar-mobile  hidden-lg hidden-md hidden-xxs clearfix">
	<div class="logo-mobile-theme col-xs-6 text-left">
		<?php get_template_part( 'page-templates/parts/logo' ); ?>
	</div>
     <div class="topbar-mobile-right col-xs-6 text-right">
        <div class="active-mobile">
            <button data-toggle="offcanvas" class="btn btn-sm btn-danger btn-offcanvas btn-toggle-canvas offcanvas" type="button">
               <i class="fa fa-bars"></i>
            </button>
        </div>
        <div class="topbar-inner">
            <div class="search-device">
				<a class="show-search" href="javascript:;"><i class="icon-magnifier icons"></i></a>
				<?php get_template_part( 'page-templates/parts/productsearchform-mobile' ); ?>
			</div>
            
            <div class="setting-popup">

                <div class="dropdown">
                    <button class="btn btn-sm btn-primary btn-outline dropdown-toggle" type="button" data-toggle="dropdown"><span class="fa fa-user"></span></button>
                    <div class="dropdown-menu">
                        <?php if ( has_nav_menu( 'topmenu' ) ) { ?>
                            <div class="pull-left">
                                <?php
                                    $args = array(
                                        'theme_location'  => 'topmenu',
                                        'container_class' => '',
                                        'menu_class'      => 'menu-topbar'
                                    );
                                    wp_nav_menu($args);
                                ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

            </div>
            <div class="active-mobile top-cart">

                <div class="dropdown">
                    <button class="btn btn-sm btn-primary btn-outline dropdown-toggle" type="button" data-toggle="dropdown"><span class="fa fa-shopping-cart"></span></button>
                    <div class="dropdown-menu">
                        <div class="widget_shopping_cart_content"></div>
                    </div>
                </div>
                
            </div>  
        </div>
    </div>       
</div>
