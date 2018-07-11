<?php

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$taxonomy     = 'product_cat';
$orderby      = 'name';  
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no  
$title        = '';  
$empty        = 0;

$args = array(
     'taxonomy'     => $taxonomy,
     'orderby'      => $orderby,
     'number'       => $number,
     'pad_counts'   => $pad_counts,
     'hierarchical' => $hierarchical,
     'title_li'     => $title,
     'parent'       => 0,
     'hide_empty'   => $empty
);
$all_categories = get_categories( $args );


$_id = greenmart_tbay_random_key();
$_count = 1;

if($responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =      $columns;
    $screen_desktopsmall     =      $columns;
    $screen_tablet           =      $columns;
    $screen_mobile           =      $columns;  
}

?>
<div class="widget widget-<?php echo esc_attr($layout_type); ?> widget-categories categories <?php echo esc_attr($el_class); ?>">

	<?php if ($title!=''): ?>
        <h3 class="widget-title">
            <span><?php echo esc_html( $title ); ?></span>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>

	<?php if ( $all_categories ) : ?>
		<div class="widget-content woocommerce">
			<div class="<?php echo esc_attr( $layout_type ); ?>-wrapper">

                <?php if( $layout_type == 'carousel' ) : ?>


                    <?php  wc_get_template( 'layout-categories/'. $layout_type .'.php' , array( 'all_categories' => $all_categories, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number ) ); ?>

                <?php else : ?>
                    <div class="row">
                        <?php  wc_get_template( 'layout-categories/'. $layout_type .'.php' , array( 'all_categories' => $all_categories, 'columns' => $columns, 'number' => $number ) ); ?>
                    </div>
                <?php endif; ?>


			</div>
		</div>
	<?php endif; ?>

</div>
