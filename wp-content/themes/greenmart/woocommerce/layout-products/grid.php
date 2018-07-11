<?php
global $woocommerce_loop; 
$woocommerce_loop['columns'] = $columns;
$product_item = isset($product_item) ? $product_item : 'inner';

$screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
$screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
$screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
$screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;

$count = 0;

$data_responsive = '';
$data_responsive .= ' data-xlgdesktop='. $columns .'';
$data_responsive .= ' data-desktop='. $screen_desktop .'';
$data_responsive .= ' data-desktopsmall='. $screen_desktopsmall .'';
$data_responsive .= ' data-tablet='. $screen_tablet .'';
$data_responsive .= ' data-mobile='. $screen_mobile .''; 


?>
<div class="<?php echo $columns <= 1 ? 'w-products-list' : 'products products-grid';?>">
	<div class="row" <?php echo esc_attr($data_responsive); ?>>
		<?php while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>

			<?php 

			if( $count%$screen_desktop == '0' ) {
				$class_desktop = 'first-lg';
			} else {
				$class_desktop = '';
			}				
			if( $count%$screen_desktopsmall == '0' ) {
				$class_desktopsmall = 'first-md';
			} else {
				$class_desktopsmall = '';
			}			

			if( $count%$screen_tablet == '0' ) {
				$class_tablet = 'first-sm';
			} else {
				$class_tablet = '';
			}			

			if( $count%$screen_mobile == '0' ) {
				$class_mobile = 'first-xs';
			} else {
				$class_mobile = '';
			}


			?>

			<?php wc_get_template( 'content-products.php', array('product_item' => $product_item,'screen_desktop' => $screen_desktop,'class_desktop' => $class_desktop,'screen_desktopsmall' => $screen_desktopsmall,'class_desktopsmall' => $class_desktopsmall,'screen_tablet' => $screen_tablet,'class_tablet' => $class_tablet,'screen_mobile' => $screen_mobile,'class_mobile' => $class_mobile) ); ?>

			<?php $count++; ?>

		<?php endwhile; ?>
	</div>
</div>

<?php wp_reset_postdata(); ?>