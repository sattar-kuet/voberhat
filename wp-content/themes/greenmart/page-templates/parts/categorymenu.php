<?php if ( has_nav_menu( 'category-menu' ) ): ?>
<div class="category-inside">
		<h3 class="category-inside-title"><?php esc_html_e('All Categories', 'greenmart'); ?></h3>
		<div class="category-inside-content">
			 <nav class="tbay-topmenu" role="navigation">
				<?php
					
					$args = array(
						'theme_location' => 'category-menu',
						'container_class' => 'tbay-menu-category list-inline',
						'menu_class' => 'nav navbar-nav megamenu',
						'fallback_cb' => '',
						'menu_id' => 'category-menu',
						'walker' => new greenmart_Tbay_Nav_Menu()
					);
					wp_nav_menu($args);
				?>
			</nav>
		</div>
</div><!-- End Category Menu -->
<?php endif;?>
