<?php if ( has_nav_menu( 'category-menu-image' ) ): ?>

	<nav class="tbay-category-menu-image categorymenu tbay_custom_menu treeview-menu" role="navigation">
		<h3 class="category-inside-title"><?php esc_html_e('Categories', 'greenmart'); ?></h3>
		<?php   $args = array(
				'theme_location' => 'category-menu-image',
				'container_class' => 'menu-category-menu-container',
				'menu_class' => 'menu treeview',
				'fallback_cb' => '',
				'menu_id' => 'category-menu-image',
				'walker' => new greenmart_Tbay_Nav_Menu()
			);
			wp_nav_menu($args);
		?>
	</nav>
<?php endif;?>