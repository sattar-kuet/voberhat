<?php
extract( $args );
extract( $instance );
$title = apply_filters('widget_title', $instance['title']);

$get_sub_title = '';
if ( isset($sub_title) && $sub_title ) {
    $get_sub_title = '<span class="subtitle">'. esc_html($sub_title) .'</span>';
}

if ( $title ) {
    echo ($before_title)  . trim( $title ) . $after_title . $get_sub_title;
}

if ( $types == '' ) return;

if (isset($categories) && !empty($categories)) {
    $categories = explode(',', $categories);
}

$_id = greenmart_tbay_random_key();
$_count = 1;

$rand = '';
if($types == 'rand') {
	$types = 'product';
	$rand  = 'rand';
}

$loop = greenmart_tbay_get_products( $categories, $types, 1, $numbers, $rand );      


$screen_desktop          =      isset($columns) ? $columns : 4;
$screen_desktopsmall     =      isset($columns_destsmall) ? $columns_destsmall : 3;
$screen_tablet           =      isset($columns_tablet) ? $columns_tablet : 3;
$screen_mobile           =      isset($columns_mobile) ? $columns_mobile : 1;

$pagi_type 	= 	$paginations;
$nav_type 	= 	$navigations;

$layout_type = 'carousel';

?>
<div class="widget widget-<?php echo esc_attr($layout_type); ?> widget-products products">

	<?php if ( $loop->have_posts() ) : ?>
		<div class="widget-content woocommerce">
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">

                <?php  wc_get_template( 'layout-products/'. $layout_type .'.php' , array( 'loop' => $loop, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $numbers ) ); ?>

			</div>
		</div>
	<?php endif; ?>

</div>
