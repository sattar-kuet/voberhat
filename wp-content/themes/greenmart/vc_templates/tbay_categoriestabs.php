<?php

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );
$_id = greenmart_tbay_random_key();

if (isset($categoriestabs) && !empty($categoriestabs)):
    $categoriestabs = (array) vc_param_group_parse_atts( $categoriestabs );
    $i = 0;

if($responsive_type == 'yes') {
    $screen_desktop          =      isset($screen_desktop) ? $screen_desktop : 4;
    $screen_desktopsmall     =      isset($screen_desktopsmall) ? $screen_desktopsmall : 3;
    $screen_tablet           =      isset($screen_tablet) ? $screen_tablet : 3;
    $screen_mobile           =      isset($screen_mobile) ? $screen_mobile : 1;
} else {
    $screen_desktop          =     $columns;
    $screen_desktopsmall     =      3;
    $screen_tablet           =      3;
    $screen_mobile           =     1;  
}

$cat_array = array();
$args = array(
    'type' => 'post',
    'child_of' => 0,
    'orderby' => 'name',
    'order' => 'ASC',
    'hide_empty' => false,
    'hierarchical' => 1,
    'taxonomy' => 'product_cat' 
);

$categories = get_categories( $args );
greenmart_tbay_get_category_childs( $categories, 0, 0, $cat_array );

$cat_array_id   = array();
foreach ($cat_array as $key => $value) {
    $cat_array_id[]   = $value;
}


?>

    <div class="widget widget-products widget-categoriestabs <?php echo esc_attr($align); ?> <?php echo esc_attr($el_class); ?>">
        <?php if ($title!=''): ?>
            <h3 class="widget-title">
                <span><?php echo esc_html( $title ); ?></span>
                <?php if ( isset($subtitle) && $subtitle ): ?>
                    <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
                <?php endif; ?>
            </h3>
        <?php endif; ?>
        <div class="widget-content woocommerce">
            <ul role="tablist" class="nav nav-tabs">
                <?php foreach ($categoriestabs as $tab) : ?>
                    <?php 

                        if( !in_array($tab['category'], $cat_array_id) ) {
                            $cat_category    = esc_html('all-categories','greenmart');
                            $cat_name        = esc_html('All Categories','greenmart');
                        } else {
                            $cat_category    = $tab['category'];
                            $category        = get_term_by( 'id', $cat_category, 'product_cat' );
                            $cat_name        = $category->name;
                        }

                    ?>
                    <li <?php echo ($i == 0 ? ' class="active"' : ''); ?>>
                        <a href="#tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" data-toggle="tab" class="<?php echo (isset($tab['icon']) || isset($tab['icon_font']) ? 'has-icon' : 'no-icon'); ?>">
                            <?php if ( isset($tab['icon']) && !empty($tab['icon']) ): ?>
                                <?php $img = wp_get_attachment_image_src($tab['icon'], 'full'); ?>
                                <?php if ( isset($img[0]) ) { ?>
                                    <img src="<?php echo esc_url( $img[0] );?>" alt="<?php echo esc_attr( $title ); ?>"  />
                                <?php } ?>
                            <?php elseif ( isset($tab['icon_font']) && $tab['icon_font'] ): ?>
                                <i class="<?php echo esc_attr($tab['icon_font']); ?>"></i>
                            <?php endif; ?>
                            <?php echo esc_html($cat_name); ?>
                        </a>
                    </li>
                <?php $i++; endforeach; ?>
            </ul>
            <div class="widget-inner">
                <?php if( !empty($image_cat) ) : ?>
                    <?php $img = wp_get_attachment_image_src($image_cat,'full'); ?>
                    <div class="col-lg-3 hidden-md hidden-sm hidden-xs <?php echo esc_attr( $image_float );?>">
                        <img src="<?php echo esc_url_raw($img[0]); ?>" alt="">
                    </div>
                <?php endif; ?>
                <div class="<?php echo !empty($image_cat) ? 'col-lg-9 col-xs-12' : ''; ?>">
                    <div class="tab-content">
                        <?php $i = 0; foreach ($categoriestabs as $tab) : ?>

                            <?php 

                            if( !in_array($tab['category'], $cat_array_id) ) {
                                $cat_category    = esc_html('all-categories','greenmart');
                                $loop            = greenmart_tbay_get_products( -1 , $type, 1, $number );
                                $link            = get_permalink( wc_get_page_id( 'shop' ) );
                            } else {
                                $category       = get_term_by( 'id', $tab['category'], 'product_cat' );
                                $cat_category   = $category->slug;
                                $loop           = greenmart_tbay_get_products( array($cat_category), $type, 1, $number );
                                $link           = get_term_link( $category->term_id, 'product_cat' );
                            }

                            ?>

                            <div id="tab-<?php echo esc_attr($_id);?>-<?php echo esc_attr($i); ?>" class="tab-pane <?php echo ($i == 0 ? 'active' : ''); ?>">

								<?php wc_get_template( 'layout-products/'. $layout_type .'.php' , array( 'loop' => $loop, 'columns' => $columns, 'rows' => $rows, 'pagi_type' => $pagi_type, 'nav_type' => $nav_type,'screen_desktop' => $screen_desktop,'screen_desktopsmall' => $screen_desktopsmall,'screen_tablet' => $screen_tablet,'screen_mobile' => $screen_mobile, 'number' => $number ) ); ?>

                                <a href="<?php echo esc_url( $link ); ?>" class="btn btn-block btn-view-all"><?php echo esc_html__('view all', 'greenmart'); ?><i class="icofont icofont-simple-right"></i></a>
                            </div>
                        <?php $i++; endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>