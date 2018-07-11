<?php

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

if ( $producttabs == '' ) return;

if (isset($categories) && !empty($categories)) {
    $categories = explode(',', $categories);
}


$_id = greenmart_tbay_random_key();
$_count = 1;

$list_query = $this->getListQuery( $atts );

$loop = greenmart_tbay_get_products( $categories, $producttabs, 1, $number ); 

if($responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =     	$columns;
    $screen_desktopsmall     =      $columns;
    $screen_tablet           =      $columns;
    $screen_mobile           =      $columns;  
}


if ( count($list_query) > 0 ) {
?>
	<div class="widget <?php echo esc_attr($align); ?> widget-products widget-product-tabs products <?php echo esc_attr($el_class); ?>">
		<div class="tabs-container tab-heading clearfix tab-v8">
			<?php if($title!=''):?>
				<h3 class="widget-title">
            		<span><span><?php echo esc_html( $title ); ?></span></span><?php if( isset($subtitle) && $subtitle ){ ?><span class="subtitle"><?php echo esc_html($subtitle); ?></span> <?php } ?>
				</h3>
			<?php endif; ?>
			<ul class="tabs-list nav nav-tabs">
				<?php $__count=0; ?>
				<?php foreach ($list_query as $key => $li) { ?>
						<li <?php echo ($__count==0)?' class="active"':''; ?>><a href="#<?php echo esc_attr($key.'-'.$_id); ?>" data-toggle="tab" data-title="<?php echo esc_attr($li['title']);?>"><?php echo trim( $li['title_tab'] );?></a></li>
					<?php $__count++; ?>
				<?php } ?>
			</ul>
		</div>


		<?php if(  $layout_type == 'carousel' || $layout_type == 'carousel-special' ) { ?>

			<div class="widget-content tab-content woocommerce">
				<?php $__count=0; ?>
				<?php foreach ($list_query as $key => $li) { ?>
					<div class="tab-pane<?php echo ($__count == 0 ? ' active' : ''); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
						<div class="grid-wrapper">
							<?php

								if ( $loop->have_posts()) {

									wc_get_template( 'layout-products/'. $layout_type .'.php' , array( 'loop' => $loop, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number ) );
								}
							?>
						</div>

					</div>
					<?php $__count++; ?>
				<?php } ?>
			</div>

		<?php } else { ?>

			<div class="widget-content tab-content woocommerce">
				<?php $__count=0; ?>
				<?php foreach ($list_query as $key => $li) { ?>
					<div class="tab-pane<?php echo ($__count == 0 ? ' active' : ''); ?>" id="<?php echo esc_attr($key).'-'.$_id; ?>">
						<div class="grid-wrapper">
							<?php

								if ( $loop->have_posts()) {
									
									wc_get_template( 'layout-products/'. $layout_type .'.php' , array( 'loop' => $loop, 'columns' => $columns, 'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number ) );
								}
							?>
						</div>

					</div>
					<?php $__count++; ?>
				<?php } ?>
			</div>			

		<?php } ?>

	</div>
<?php wp_reset_postdata(); ?>
<?php } ?>

