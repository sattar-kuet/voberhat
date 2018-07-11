<?php

// add to cart modal box 
if ( !function_exists('greenmart_tbay_woocommerce_add_to_cart_modal') ) {
    function greenmart_tbay_woocommerce_add_to_cart_modal(){
    ?>
    <div class="modal fade" id="tbay-cart-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close btn btn-close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="modal-body-content"></div>
                </div>
            </div>
        </div>
    </div>
    <?php    
    }
}

// cart modal
if ( !function_exists('greenmart_tbay_woocommerce_cart_modal') ) {
    function greenmart_tbay_woocommerce_cart_modal() {
        wc_get_template( 'content-product-cart-modal.php' , array( 'product_id' => (int)$_GET['product_id'] ) );
        die;
    }
}

add_action( 'wp_ajax_greenmart_add_to_cart_product', 'greenmart_tbay_woocommerce_cart_modal' );
add_action( 'wp_ajax_nopriv_greenmart_add_to_cart_product', 'greenmart_tbay_woocommerce_cart_modal' );
add_action( 'wp_footer', 'greenmart_tbay_woocommerce_add_to_cart_modal' );


if ( !function_exists('greenmart_tbay_get_products') ) {
    function greenmart_tbay_get_products($categories = array(), $product_type = 'featured_product', $paged = 1, $post_per_page = -1, $orderby = '', $order = '') {
        global $woocommerce, $wp_query;
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $post_per_page,
            'post_status' => 'publish',
            'paged' => $paged,
            'orderby'   => $orderby,
            'order' => $order
        );

        if ( isset( $args['orderby'] ) ) {
            if ( 'price' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_price',
                    'orderby'   => 'meta_value_num'
                ) );
            }
            if ( 'featured' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_featured',
                    'orderby'   => 'meta_value'
                ) );
            }
            if ( 'sku' == $args['orderby'] ) {
                $args = array_merge( $args, array(
                    'meta_key'  => '_sku',
                    'orderby'   => 'meta_value'
                ) );
            }
        }

        
        if ( !empty($categories) && is_array($categories) ) {
            $args['tax_query']    = array(
                array(
                    'taxonomy'      => 'product_cat',
                    'field'         => 'slug',
                    'terms'         =>  $categories,
                    'operator'      => 'IN'
                )
            );
        }

        switch ($product_type) {
            case 'best_selling':
                $args['meta_key']='total_sales';
                $args['orderby']='meta_value_num';
                $args['ignore_sticky_posts']   = 1;
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;
            case 'featured_product':
                $args['ignore_sticky_posts']=1;
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['tax_query'][]    = array(
                   array(
                        'taxonomy' => 'product_visibility',
                        'field'    => 'name',
                        'terms'    => 'featured',
                    )
                );
                $query_args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;
            case 'top_rate':
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                break;
            case 'recent_product':
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                break;
            case 'deals':
                $args['meta_query'] = array();
                $args['meta_query'][] = $woocommerce->query->stock_status_meta_query();
                $args['meta_query'][] = $woocommerce->query->visibility_meta_query();
                $args['meta_query'][] =  array(
                    array(
                        'key'           => '_sale_price_dates_to',
                        'value'         => time(),
                        'compare'       => '>',
                        'type'          => 'numeric'
                    )
                );
                break;     
            case 'on_sale':
                $product_ids_on_sale    = wc_get_product_ids_on_sale();
                $product_ids_on_sale[]  = 0;
                $args['post__in'] = $product_ids_on_sale;
                break;
        }
        
        return new WP_Query($args);
    }
}

// hooks
if ( !function_exists('greenmart_tbay_woocommerce_enqueue_styles') ) {
    function greenmart_tbay_woocommerce_enqueue_styles() {
        
        $skin = greenmart_tbay_get_skin();
        // Load our main stylesheet.
          if( is_rtl() ){
          
               if ( $skin != 'default' && $skin ) {
                    $css_path =  get_template_directory_uri() . '/css/skin/'.$skin.'/woocommerce.rtl.css';
               } else {
                    $css_path =  get_template_directory_uri() . '/css/woocommerce.rtl.css';
               }
          }
          else{
               if ( $skin != 'default' && $skin ) {
                    $css_path =  get_template_directory_uri() . '/css/skin/'.$skin.'/woocommerce.css';
               } else {
                    $css_path =  get_template_directory_uri() . '/css/woocommerce.css';
               }
          }
        
        wp_enqueue_style( 'greenmart-woocommerce', $css_path , 'greenmart-woocommerce-front' , GREENMART_THEME_VERSION, 'all' );

        wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick.min.js', array( 'jquery' ), '1.0.0', true );

    }
    add_action( 'wp_enqueue_scripts', 'greenmart_tbay_woocommerce_enqueue_styles', 50 );
}

// cart
if ( !function_exists('greenmart_tbay_woocommerce_header_add_to_cart_fragment') ) {
    function greenmart_tbay_woocommerce_header_add_to_cart_fragment( $fragments ){
        global $woocommerce;
        $fragments['#cart .mini-cart-items'] =  sprintf(_n(' <span class="mini-cart-items"> %d  </span> ', ' <span class="mini-cart-items"> %d </span> ', $woocommerce->cart->cart_contents_count, 'greenmart'), $woocommerce->cart->cart_contents_count);
        $fragments['#cart .qty'] = '<span class="qty">'.trim( $woocommerce->cart->get_cart_total() ).'</span>';
        return $fragments;
    }
	add_filter('woocommerce_add_to_cart_fragments', 'greenmart_tbay_woocommerce_header_add_to_cart_fragment' );
}


// breadcrumb for woocommerce page
if ( !function_exists('greenmart_tbay_woocommerce_breadcrumb_defaults') ) {
    function greenmart_tbay_woocommerce_breadcrumb_defaults( $args ) {
        $breadcrumb_img = greenmart_tbay_get_config('woo_breadcrumb_image');
        $breadcrumb_color = greenmart_tbay_get_config('woo_breadcrumb_color');
        $style = array();
        $img = '';
        if( $breadcrumb_color  ){
            $style[] = 'background-color:'.$breadcrumb_color;
        }
        if ( isset($breadcrumb_img['url']) && !empty($breadcrumb_img['url']) ) {
            $img = '<img src=" '.esc_url($breadcrumb_img['url']).'" alt="">';
        }
        $estyle = !empty($style)? ' style="'.implode(";", $style).'"':"";
/*
        if ( is_single() ) {
            $title = esc_html__('Product Detail', 'greenmart');
        } else {
            $title = esc_html__('Products List', 'greenmart');
        }*/
        $args['wrap_before'] = '<section id="tbay-breadscrumb" class="tbay-breadscrumb"><div class="container">'.$img.'<div class="breadscrumb-inner"'.$estyle.'><ol class="tbay-woocommerce-breadcrumb breadcrumb" ' . ( is_single() ? 'itemprop="breadcrumb"' : '' ) . '>';
        $args['wrap_after'] = '</ol></div></div></section>';

        return $args;
    }
}

add_action( 'init', 'greenmart_woo_remove_wc_breadcrumb' );
function greenmart_woo_remove_wc_breadcrumb() {
    if( !greenmart_tbay_get_config('show_product_breadcrumbs', true) ) {
        remove_action( 'greenmart_woo_template_main_before', 'woocommerce_breadcrumb', 30, 0 );
    } else {
        add_filter( 'woocommerce_breadcrumb_defaults', 'greenmart_tbay_woocommerce_breadcrumb_defaults' );
        add_action( 'greenmart_woo_template_main_before', 'woocommerce_breadcrumb', 30, 0 );    
    }
}

// display woocommerce modes
if ( !function_exists('greenmart_tbay_woocommerce_display_modes') ) {
    function greenmart_tbay_woocommerce_display_modes(){
        global $wp;
        $current_url = add_query_arg( $wp->query_string, '', home_url( $wp->request ) );
        $woo_mode = greenmart_tbay_woocommerce_get_display_mode();
        echo '<form action="'.  esc_url($current_url)  .'" class="display-mode" method="get">';
            echo '<button title="'.esc_html__('Grid','greenmart').'" class="change-view '.($woo_mode == 'grid' ? 'active' : '').'" value="grid" name="display" type="submit"><i class="ti-layout-grid2"></i></button>';
            echo '<button title="'.esc_html__( 'List', 'greenmart' ).'" class="change-view '.($woo_mode == 'list' ? 'active' : '').'" value="list" name="display" type="submit"><i class="ti-view-list"></i></button>';  
        echo '</form>'; 
    }
}
add_action( 'woocommerce_before_shop_loop', 'greenmart_tbay_woocommerce_display_modes' , 2 );

if ( !function_exists('greenmart_tbay_woocommerce_get_display_mode') ) {
    function greenmart_tbay_woocommerce_get_display_mode() {
        $woo_mode = greenmart_tbay_get_config('product_display_mode', 'grid');
        if ( isset($_COOKIE['greenmart_woo_mode']) && ($_COOKIE['greenmart_woo_mode'] == 'list' || $_COOKIE['greenmart_woo_mode'] == 'grid') ) {
            $woo_mode = $_COOKIE['greenmart_woo_mode'];
        }
        return $woo_mode;
    }
}


if(!function_exists('greenmart_tbay_filter_before')){
    function greenmart_tbay_filter_before(){
        echo '<div class="tbay-filter">';
    }
}
if(!function_exists('greenmart_tbay_filter_after')){
    function greenmart_tbay_filter_after(){
        echo '</div>';
    }
}
add_action( 'woocommerce_before_shop_loop', 'greenmart_tbay_filter_before' , 1 );
add_action( 'woocommerce_before_shop_loop', 'greenmart_tbay_filter_after' , 40 );

// set display mode to cookie
if ( !function_exists('greenmart_tbay_before_woocommerce_init') ) {
    function greenmart_tbay_before_woocommerce_init() {
        if( isset($_GET['display']) && ($_GET['display']=='list' || $_GET['display']=='grid') ){  
            setcookie( 'greenmart_woo_mode', trim($_GET['display']) , time()+3600*24*100,'/' );
            $_COOKIE['greenmart_woo_mode'] = trim($_GET['display']);
        }
    }
}
add_action( 'init', 'greenmart_tbay_before_woocommerce_init' );

// Number of products per page
if ( !function_exists('greenmart_tbay_woocommerce_shop_per_page') ) {
    function greenmart_tbay_woocommerce_shop_per_page($number) {
        $value = greenmart_tbay_get_config('number_products_per_page');
        if ( is_numeric( $value ) && $value ) {
            $number = absint( $value );
        }
        return $number;
    }
}
add_filter( 'loop_shop_per_page', 'greenmart_tbay_woocommerce_shop_per_page' );

// Number of products per row
if ( !function_exists('greenmart_tbay_woocommerce_shop_columns') ) {
    function greenmart_tbay_woocommerce_shop_columns($number) {
        $value = greenmart_tbay_get_config('product_columns');
        if ( in_array( $value, array(2, 3, 4, 6) ) ) {
            $number = $value;
        }
        return $number;
    }
}
add_filter( 'loop_shop_columns', 'greenmart_tbay_woocommerce_shop_columns' );

// share box
if ( !function_exists('greenmart_tbay_woocommerce_share_box') ) {
    function greenmart_tbay_woocommerce_share_box() {
        if ( greenmart_tbay_get_config('show_product_social_share') ) {
            ?>
              <div class="tbay-woo-share">
                <div class="addthis_inline_share_toolbox"></div>
              </div>
            <?php
        }
    }
    add_filter( 'woocommerce_single_product_summary', 'greenmart_tbay_woocommerce_share_box', 100 );
}


// swap effect
if ( !function_exists('greenmart_tbay_swap_images') ) {
    function greenmart_tbay_swap_images() {
        global $post, $product, $woocommerce;
        $size = 'woocommerce_thumbnail';
        $placeholder = wc_get_image_size( $size );
        $placeholder_width = $placeholder['width']; 
        $placeholder_height = $placeholder['height'];

        $output='';
        $class = 'image-no-effect';
        if (has_post_thumbnail()) {
            $attachment_ids = $product->get_gallery_image_ids();
            if ($attachment_ids && isset($attachment_ids[0])) {
                $class = 'image-hover';
                $output .= wp_get_attachment_image($attachment_ids[0],'woocommerce_thumbnail',false,array('class'=>"attachment-shop_catalog image-effect"));
            }
            $output .= get_the_post_thumbnail( $post->ID,'woocommerce_thumbnail',array('class'=>$class) );
        } else {
            $output .= '<img src="'.wc_placeholder_img_src().'" alt="'.esc_html__('Placeholder' , 'greenmart').'" class="'. esc_attr($class) .'" width="'. esc_attr($placeholder_width) .'" height="'. esc_attr($placeholder_height) .'" />';
        }
        echo trim($output);
    }
}

if ( greenmart_tbay_get_global_config('show_swap_image') ) {
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    add_action('woocommerce_before_shop_loop_item_title', 'greenmart_tbay_swap_images', 10);
}  

// layout class for woo page
if ( !function_exists('greenmart_tbay_woocommerce_content_class') ) {
    function greenmart_tbay_woocommerce_content_class( $class ) {
        $page = 'archive';
        if ( is_singular( 'product' ) ) {
            $page = 'single';
        }
        if( greenmart_tbay_get_config('product_'.$page.'_fullwidth') ) {
            return 'container-fluid';
        }
        return $class;
    }
}
add_filter( 'greenmart_tbay_woocommerce_content_class', 'greenmart_tbay_woocommerce_content_class' );

// get layout configs
if ( !function_exists('greenmart_tbay_get_woocommerce_layout_configs') ) {
    function greenmart_tbay_get_woocommerce_layout_configs() {
        $page = 'archive';
        if ( is_singular( 'product' ) ) {
            $page = 'single';
        }
        $left = greenmart_tbay_get_config('product_'.$page.'_left_sidebar');
        $right = greenmart_tbay_get_config('product_'.$page.'_right_sidebar');

        if( isset($_GET['sidebar_product_position']) ) {
            switch ( $_GET['sidebar_product_position'] ) {
                case 'left':
                    $configs['left'] = array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
                    break;
                case 'right':
                    $configs['right'] = array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
                    break;
                case 'full':
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
                    break;
                case 'left-right':
                    $configs['left'] = array( 'sidebar' => $left,  'class' => 'col-xs-12 col-md-12 col-lg-3'  );
                    $configs['right'] = array( 'sidebar' => $right, 'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-6' );
                    break;
                default:
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
                    break;
            }
        } else {
            switch ( greenmart_tbay_get_config('product_'.$page.'_layout') ) {
                case 'left-main':
                    $configs['left'] = array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
                    break;
                case 'main-right':
                    $configs['right'] = array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
                    break;
                case 'main':
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
                    break;
                case 'left-main-right':
                    $configs['left'] = array( 'sidebar' => $left,  'class' => 'col-xs-12 col-md-12 col-lg-3'  );
                    $configs['right'] = array( 'sidebar' => $right, 'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-6' );
                    break;
                default:
                    $configs['main'] = array( 'class' => 'col-xs-12 col-md-12' );
                    break;
            }  
        }

        return $configs; 
    }
}

// Show/Hide related, upsells products
if ( !function_exists('greenmart_tbay_woocommerce_related_upsells_products') ) {
    function greenmart_tbay_woocommerce_related_upsells_products($located, $template_name) {
        $content_none = get_template_directory() . '/woocommerce/content-none.php';
        $show_product_releated = greenmart_tbay_get_config('show_product_releated');
        if ( 'single-product/related.php' == $template_name ) {
            if ( !$show_product_releated  ) {
                $located = $content_none;
            }
        } elseif ( 'single-product/up-sells.php' == $template_name ) {
            $show_product_upsells = greenmart_tbay_get_config('show_product_upsells');
            if ( !$show_product_upsells ) {
                $located = $content_none;
            }
        }

        return apply_filters( 'greenmart_tbay_woocommerce_related_upsells_products', $located, $template_name );
    }
}
add_filter( 'wc_get_template', 'greenmart_tbay_woocommerce_related_upsells_products', 10, 2 );

if ( !function_exists( 'greenmart_tbay_product_review_tab' ) ) {
    function greenmart_tbay_product_review_tab($tabs) {
        if ( !greenmart_tbay_get_config('show_product_review_tab') && isset($tabs['reviews']) ) {
            unset( $tabs['reviews'] ); 
        }
        return $tabs;
    }
    add_filter( 'woocommerce_product_tabs', 'greenmart_tbay_product_review_tab', 100 );
}


if ( !function_exists( 'greenmart_tbay_minicart') ) {
    function greenmart_tbay_minicart() {
        $template = apply_filters( 'greenmart_tbay_minicart_version', '' );
        get_template_part( 'woocommerce/cart/mini-cart-button', $template ); 
    }
}

if ( !function_exists( 'greenmart_tbay_woocomerce_icon_wishlist' ) ) {
    // Wishlist
    add_filter( 'yith_wcwl_button_label', 'greenmart_tbay_woocomerce_icon_wishlist'  );
    function greenmart_tbay_woocomerce_icon_wishlist( $value='' ){
        return '<i class="icofont icofont-heart-alt"></i><span>'.esc_html__('Wishlist','greenmart').'</span>';
    }

}

if ( !function_exists( 'greenmart_tbay_woocomerce_icon_wishlist_add' ) ) {
    add_filter( 'yith-wcwl-browse-wishlist-label', 'greenmart_tbay_woocomerce_icon_wishlist_add' );
    function greenmart_tbay_woocomerce_icon_wishlist_add(){
        return '<i class="icofont icofont-heart-alt"></i><span>'.esc_html__('Wishlist','greenmart').'</span>';
    }
}
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

if (class_exists('YITH_WCQV_Frontend')) {
    remove_action( 'woocommerce_after_shop_loop_item', array( YITH_WCQV_Frontend(), 'yith_add_quick_view_button' ), 15 );
}



if ( !function_exists( 'greenmart_product_description_heading' ) ) {
    //remove heading tab single product
    add_filter('woocommerce_product_description_heading',
'greenmart_product_description_heading');
    function greenmart_product_description_heading() {
        return '';
    }
}


/**
 * WooCommerce
 *
 */
if ( ! function_exists( 'greenmart_woocommerce_setup_support' ) ) {
    add_action( 'after_setup_theme', 'greenmart_woocommerce_setup_support' );
    function greenmart_woocommerce_setup_support() {
        add_theme_support( 'wc-product-gallery-zoom' );
        add_theme_support( 'wc-product-gallery-lightbox' );
        add_theme_support( 'wc-product-gallery-slider' );

        if( class_exists( 'YITH_Woocompare' ) ) {
            update_option( 'yith_woocompare_compare_button_in_products_list', 'no' ); 
        }

        add_filter( 'woocommerce_get_image_size_gallery_thumbnail', function( $size ) {

            $tbay_thumbnail_width       = get_option( 'tbay_woocommerce_thumbnail_image_width', 160);
            $tbay_thumbnail_height      = get_option( 'tbay_woocommerce_thumbnail_image_height', 130);
            $tbay_thumbnail_cropping    = get_option( 'tbay_woocommerce_thumbnail_cropping', 'yes');
            $tbay_thumbnail_cropping    = ($tbay_thumbnail_cropping == 'yes') ? true : false;

            return array(
                'width'  => $tbay_thumbnail_width,
                'height' => $tbay_thumbnail_height,
                'crop'   => $tbay_thumbnail_cropping,
            );
        } );
    }
}

if ( ! function_exists( 'greenmart_woocommerce_setup_size_image' ) ) {
    add_action( 'after_setup_theme', 'greenmart_woocommerce_setup_size_image' );
    function greenmart_woocommerce_setup_size_image() {

        $thumbnail_width = 405;
        $main_image_width = 570; 
        $cropping_custom_width = 81;
        $cropping_custom_height = 66;

        // Image sizes
        update_option( 'woocommerce_thumbnail_image_width', $thumbnail_width );
        update_option( 'woocommerce_single_image_width', $main_image_width ); 

        update_option( 'woocommerce_thumbnail_cropping', 'custom' );
        update_option( 'woocommerce_thumbnail_cropping_custom_width', $cropping_custom_width );
        update_option( 'woocommerce_thumbnail_cropping_custom_height', $cropping_custom_height );

    }
}

if(greenmart_tbay_get_global_config('config_media',false)) {
    remove_action( 'after_setup_theme', 'greenmart_woocommerce_setup_size_image' );
}



// Ajax Wishlist
if( defined( 'YITH_WCWL' ) && ! function_exists( 'greenmart_yith_wcwl_ajax_update_count' ) ){
function greenmart_yith_wcwl_ajax_update_count(){

    $wishlist_count = YITH_WCWL()->count_products();

    wp_send_json( array(
    'count' => $wishlist_count
    ) );
    }
    add_action( 'wp_ajax_yith_wcwl_update_wishlist_count', 'greenmart_yith_wcwl_ajax_update_count' );
    add_action( 'wp_ajax_nopriv_yith_wcwl_update_wishlist_count', 'greenmart_yith_wcwl_ajax_update_count' );
}

if ( ! function_exists( 'greenmart_woocommerce_saved_sales_price' ) ) {

    add_filter( 'woocommerce_get_saved_sales_price_html', 'greenmart_woocommerce_saved_sales_price' );

    function greenmart_woocommerce_saved_sales_price( $productid ) {

        $product = wc_get_product( $productid );

        
        $onsale         = $product->is_on_sale();
        $saleprice      = $product->get_sale_price();   
        $regularprice   = $product->get_regular_price();
        $priceDiff      = (int)$regularprice - (int)$saleprice;
        $price          = '';
        $price1         = '';

        $off_content    ='';
        if($priceDiff != 0){
            $price1 = '<span class="saved">'. esc_html__('Save you ', 'greenmart') .' <span class="price">'. sprintf( get_woocommerce_price_format(), get_woocommerce_currency_symbol(), $priceDiff ) . '</span></span>';     
            $price .= '<div class="block-save-price">'.$price1.'</div>'; 
        }
        
        // Sale price
        return $price;
        
    }
}

if( ! function_exists( 'greenmart_brands_get_name' ) && class_exists( 'YITH_WCBR' ) ) {

    function greenmart_brands_get_name($product_id) {

        $terms = wp_get_post_terms($product_id,'yith_product_brand');

        $brand = '';

        if( !empty($terms) ) {

            $brand  = '<ul class="show-brand">';

            foreach ($terms as $term) {
                
                $name = $term->name;
                $url = get_term_link( $term->slug, 'yith_product_brand' );

                $brand  .= '<li><a href='. esc_url($url) .'>'. esc_html($name) .'</a></li>';

            }

            $brand  .= '</ul>';
        }

        echo  $brand;

    }

}

if ( ! function_exists( 'greenmart_woo_show_product_loop_sale_flash' ) ) {
    /*Change sales woo*/
    add_filter( 'woocommerce_sale_flash', 'greenmart_woo_show_product_loop_sale_flash' );
    function greenmart_woo_show_product_loop_sale_flash( $html ) {

        $product = wc_get_product();
        $_product_sale = $product->get_sale_price();

        if(!empty($_product_sale ))  {
            $percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
            $percentage = '-  ' . trim( $percentage ) . '%';
                     
        } else {
            $percentage = esc_html__( 'Sale', 'greenmart' );
        }

        return str_replace( esc_html__( 'Sale!', 'greenmart' ), $percentage, $html );
    }
}

/*Custom signle product*/

if ( !function_exists('greenmart_tbay_woocommerce_tabs_style_product') ) {
    function greenmart_tbay_woocommerce_tabs_style_product($tabs_layout) {

        if ( is_singular( 'product' ) ) {
          $tabs_style       = greenmart_tbay_get_config('style_single_tabs_style','default');

          if ( isset($_GET['tabs_product']) ) {
              $tabs_layout = $_GET['tabs_product'];
          } else {
              $tabs_layout = $tabs_style;
          }  

          return $tabs_layout;
        }
    }
    add_filter( 'woo_tabs_style_single_product', 'greenmart_tbay_woocommerce_tabs_style_product' );
}

/**
* Function For Multi Layouts Single Product 
*/
//-----------------------------------------------------
/**
 * Output the product tabs.
 *
 * @subpackage  Product/Tabs
 */
if ( !function_exists('woocommerce_output_product_data_tabs') ) {
    function woocommerce_output_product_data_tabs() {
      $tabs_layout   =  apply_filters( 'woo_tabs_style_single_product', 10, 2 );

      if( isset($tabs_layout) ) {

        if( $tabs_layout == 'default') {
          wc_get_template( 'single-product/tabs/tabs.php' );
        } else {
          wc_get_template( 'single-product/tabs/tabs-'.$tabs_layout.'.php' );
        }
      }
  }
}

if ( !function_exists('woocommerce_product_data_tabs_action') ) {
    function woocommerce_product_data_tabs_action() {
      $tabs_layout   =  apply_filters( 'woo_tabs_style_single_product', 10, 2 );

      if( isset($tabs_layout) ) {

        if( $tabs_layout == 'default') {
            remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
            add_action( 'woocommerce_after_single_product_summary', 'greenmart_woo_output_product_description_tabs', 5 ); 

            add_action( 'woocommerce_after_single_product_summary', 'woocommerce_product_additional_information_tab', 5 ); 

            if ( greenmart_tbay_get_config('show_product_review_tab', true) ) {
                add_action( 'woocommerce_after_single_product_summary', 'comments_template', 25 ); 
            } 

            if( class_exists( 'WeDevs_Dokan' ) ) {
                add_action( 'woocommerce_after_single_product_summary', 'dokan_seller_product_tab', 5 ); 
            }

        }
      }
    }
    add_action('woocommerce_before_single_product', 'woocommerce_product_data_tabs_action');
}







if ( ! function_exists( 'greenmart_woo_output_product_description_tabs' ) ) {
    function greenmart_woo_output_product_description_tabs() { 
        wc_get_template( 'single-product/tabs/description.php' ); 
    } 

}



if ( ! function_exists( 'greenmart_woo_subtitle_field' ) ) {
    /* Subtitle Product */
    function greenmart_woo_subtitle_field() {

        woocommerce_wp_text_input( 
            array( 
                'id'          => '_subtitle', 
                'label'       => esc_html__( 'Subtitle', 'greenmart' ), 
                'placeholder' => esc_html__( 'Subtitle....', 'greenmart' ),
                'description' => esc_html__( 'Enter the subtitle.', 'greenmart' ) 
            )
        );

    }
	add_action( 'woocommerce_product_options_general_product_data', 'greenmart_woo_subtitle_field' );
    
}

if ( ! function_exists( 'greenmart_woo_subtitle_field_save' ) ) {
    function greenmart_woo_subtitle_field_save( $post_id ){  

        $subtitle = $_POST['_subtitle'];
        if( !empty( $subtitle ) )
            update_post_meta( $post_id, '_subtitle', esc_attr( $subtitle ) );

    }
	add_action( 'woocommerce_process_product_meta', 'greenmart_woo_subtitle_field_save' );
}

if ( ! function_exists( 'greenmart_woo_get_subtitle' ) ) {
    function greenmart_woo_get_subtitle( ) {

        global $post;

        $_subtitle = get_post_meta( $post->ID, '_subtitle', true );

        if(!($_subtitle == null || $_subtitle == '')){
            echo '<div class="tbay-subtitle">'. get_post_meta( $post->ID, '_subtitle', true ) .'</div>';
        }

    }

	add_action( 'greenmart_after_title_tbay_subtitle', 'greenmart_woo_get_subtitle', 0);
	add_action( 'woocommerce_single_product_summary', 'greenmart_woo_get_subtitle', 5);
}

/* ---------------------------------------------------------------------------
 * WooCommerce - Function get Query
 * --------------------------------------------------------------------------- */
 if ( ! function_exists( 'greenmart_woo_get_review_counting' ) ) {
    /* Fix ajax count cart */
    function greenmart_woo_get_review_counting(){

        global $post; 
        $output = array();

        for($i=1; $i <= 5; $i++){
             $args = array(
                'post_id'      => ( $post->ID ),
                'meta_query' => array(
                  array(
                    'key'   => 'rating',
                    'value' => $i
                  )
                ),      
                'count' => true
            );
            $output[$i] = get_comments( $args );
        }
        return $output;
    }
}
add_filter( 'woocommerce_add_to_cart_fragments', function($fragments) {

    ob_start();
    ?>

    <span class="mini-cart-items-fixed">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>

    <?php $fragments['span.mini-cart-items-fixed'] = ob_get_clean();

    return $fragments;

} );

add_filter( 'woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>

    <span class="sub-title-2">
        <?php echo esc_html__('My Cart ', 'greenmart'); ?>
        (<?php echo sprintf( '%d item', WC()->cart->cart_contents_count );?>)
    </span>

    <?php $fragments['span.sub-title-2'] = ob_get_clean();

    return $fragments;

} );

add_filter( 'woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>

    <span class="mini-cart-items cart-mobile">
        <?php echo sprintf( '%d', WC()->cart->cart_contents_count );?>
    </span>

    <?php $fragments['span.cart-mobile'] = ob_get_clean();

    return $fragments;

} );

 if ( ! function_exists( 'greenmart_ajax_product_remove' ) ) {
    // Remove product in the cart using ajax
    function greenmart_ajax_product_remove()
    {
        // Get mini cart
        ob_start();

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item)
        {
            if($cart_item['product_id'] == $_POST['product_id'] && $cart_item_key == $_POST['cart_item_key'] )
            {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        WC()->cart->calculate_totals();
        WC()->cart->maybe_set_cart_cookies();

        woocommerce_mini_cart();

        $mini_cart = ob_get_clean();

        // Fragments and mini cart are returned
        $data = array(
            'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                    'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'
                )
            ),
            'cart_hash' => apply_filters( 'woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5( json_encode( WC()->cart->get_cart_for_session() ) ) : '', WC()->cart->get_cart_for_session() )
        );

        wp_send_json( $data );
		
        die();
    }

    add_action( 'wp_ajax_product_remove', 'greenmart_ajax_product_remove' );
    add_action( 'wp_ajax_nopriv_product_remove', 'greenmart_ajax_product_remove' );
}
if ( wp_is_mobile() ) {
    add_filter ('woocommerce_add_to_cart_redirect', function() {
        return wc_get_cart_url();
      } );
}

/*Add video to product detail*/
if ( !function_exists('greenmart_tbay_woocommerce_add_video_field') ) {
  add_action( 'woocommerce_product_options_general_product_data', 'greenmart_tbay_woocommerce_add_video_field' );

  function greenmart_tbay_woocommerce_add_video_field(){

    $args = apply_filters( 'greenmart_tbay_woocommerce_simple_url_video_args', array(
        'id' => '_video_url',
        'label' => esc_html__('Featured Video URL', 'greenmart'),
        'placeholder' => esc_html__('Video URL', 'greenmart'),
        'desc_tip' => true,
        'description' => esc_html__('Enter the video url at https://vimeo.com/ or https://www.youtube.com/', 'greenmart'))
    );

    echo '<div class="options_group">';

    woocommerce_wp_text_input( $args ) ;

    echo '</div>';
  }
}

if ( !function_exists('greenmart_tbay_save_video_url') ) {
  add_action( 'woocommerce_process_product_meta', 'greenmart_tbay_save_video_url', 10, 2 );
  function greenmart_tbay_save_video_url( $post_id, $post ) {
      if ( isset( $_POST['_video_url'] ) ) {
          update_post_meta( $post_id, '_video_url', esc_attr( $_POST['_video_url'] ) );
      }
  }
}

if ( !function_exists('greenmart_tbay_VideoUrlType') ) {
  function greenmart_tbay_VideoUrlType($url) {


      $yt_rx = '/^((?:https?:)?\/\/)?((?:www|m)\.)?((?:youtube\.com|youtu.be))(\/(?:[\w\-]+\?v=|embed\/|v\/)?)([\w\-]+)(\S+)?$/';
      $has_match_youtube = preg_match($yt_rx, $url, $yt_matches);


      $vm_rx = '/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/';
      $has_match_vimeo = preg_match($vm_rx, $url, $vm_matches);


      //Then we want the video id which is:
      if($has_match_youtube) {
          $video_id = $yt_matches[5]; 
          $type = 'youtube';
      }
      elseif($has_match_vimeo) {
          $video_id = $vm_matches[5];
          $type = 'vimeo';
      }
      else {
          $video_id = 0;
          $type = 'none';
      }


      $data['video_id'] = $video_id;
      $data['video_type'] = $type;

      return $data;
  }
}

if ( !function_exists('greenmart_tbay_get_video_product') ) {
  add_action( 'tbay_product_video', 'greenmart_tbay_get_video_product', 10 );
  function  greenmart_tbay_get_video_product() {
    global $post, $product;


    if( get_post_meta( $post->ID, '_video_url', true ) ) {
      $video = greenmart_tbay_VideoUrlType(get_post_meta( $post->ID, '_video_url', true ));

      if( $video['video_type'] == 'youtube' ) {
        $url  = 'https://www.youtube.com/embed/'.$video['video_id'].'?autoplay=1';
        $icon = '<i class="fa fa-youtube-play" aria-hidden="true"></i>'.esc_html__('View Video','greenmart');

      }elseif(( $video['video_type'] == 'vimeo' )) {
        $url = 'https://player.vimeo.com/video/'.$video['video_id'].'?autoplay=1';
        $icon = '<i class="fa fa-vimeo-square" aria-hidden="true"></i>'.esc_html__('View Video','greenmart');

      }

    }

    ?>

    <?php if( !empty($url) ) : ?>

      <div class="modal fade" id="productvideo">
        <div class="modal-dialog">
          <div class="modal-content tbay-modalContent">

            <div class="modal-body">
              
              <div class="close-button">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              </div>
              <div class="embed-responsive embed-responsive-16by9">
                          <iframe class="embed-responsive-item"></iframe>
              </div>
            </div>

          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
      </div><!-- /.modal -->

      <button type="button" class="tbay-modalButton" data-toggle="modal" data-tbaySrc="<?php echo esc_attr($url); ?>" data-tbayWidth="640" data-tbayHeight="480" data-target="#productvideo"  data-tbayVideoFullscreen="true"><?php echo trim($icon); ?></button>

    <?php endif; ?>
  <?php
  }
}


/*product nav*/
if ( !function_exists('greenmart_render_product_nav') ) {
  function greenmart_render_product_nav($post, $position){
      if($post){
          $product = wc_get_product($post->ID);
          $img = '';
          if(has_post_thumbnail($post)){
              $img = get_the_post_thumbnail($post, 'woocommerce_gallery_thumbnail');
          }
          $link = get_permalink($post);
          echo "<div class='{$position} psnav'>";
          echo "<a class='img-link' href=\"{$link}\">";
           echo ($position == 'left')? trim($img) : '';   
          echo "</a>"; 
          echo "  <div class='product_single_nav_inner single_nav'>
                      <a href=\"{$link}\">
                          <span class='name-pr'>{$post->post_title}</span>
                      </a>
                  </div>";
          echo "<a class='img-link' href=\"{$link}\">";        
            echo ($position == 'right') ? $img:'';    
          echo "</a>"; 
          echo "</div>";
      }
  }
}

if ( !function_exists('greenmart_woo_product_nav') ) {
  function greenmart_woo_product_nav(){
        if ( greenmart_tbay_get_config('show_product_nav', false) ) {
            $prev = get_previous_post();
            $next = get_next_post();

            echo '<div class="product-nav pull-right">';  
            echo '<div class="link-images visible-lg">';
            greenmart_render_product_nav($prev, 'left');
            greenmart_render_product_nav($next, 'right');
            echo '</div>';

            echo '</div>';
        }
  }
  add_action( 'woocommerce_before_single_product', 'greenmart_woo_product_nav', 1 );
}

// catalog mode

if ( !function_exists('greenmart_tbay_woocommerce_catalog_mode_active') ) {
    function greenmart_tbay_woocommerce_catalog_mode_active($active) {
        $active = greenmart_tbay_get_config('enable_woocommerce_catalog_mode', false);

        $active = (isset($_GET['catalog_mode'])) ? $_GET['catalog_mode'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_catalog_mode', 'greenmart_tbay_woocommerce_catalog_mode_active' );

if ( !function_exists('greenmart_woocommerce_catalog_mode_active') ) {
    function greenmart_woocommerce_catalog_mode_active() {
        $active = apply_filters( 'greenmart_catalog_mode', 10,2 );
        if( isset($active) && $active ) {  
          define( 'GREENMART_WOOCOMMERCE_CATALOG_MODE_ACTIVED', true );
        }
    }

    add_action( 'init', 'greenmart_woocommerce_catalog_mode_active' );
}

// class catalog mode
if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_catalog_mod' ) ) {
    function greenmart_tbay_body_classes_woocommerce_catalog_mod( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_catalog_mode', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-body-woocommerce-catalog-mod';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_catalog_mod' );
}


if ( !function_exists('greenmart_woocommerce_catalog_mode') ) {
    function greenmart_woocommerce_catalog_mode() {
        $active = apply_filters( 'greenmart_catalog_mode', 10,2 );
        if( isset($active) && $active ) {  
           
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
            remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
            remove_action('woocommerce_add_to_cart_validation', 'avoid_add_to_cart',  10, 2 );       

            if ( defined( 'YITH_WCQV' ) && YITH_WCQV ) {
                remove_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_add_to_cart', 25 );
            }
        }

    }

    add_action( 'init', 'greenmart_woocommerce_catalog_mode' );
}

// cart modal
if ( !function_exists('greenmart_woocommerce_catalog_mode_redirect_page') ) {
    function greenmart_woocommerce_catalog_mode_redirect_page() {
        $active = apply_filters( 'greenmart_catalog_mode', 10,2 );
        if( isset($active) && $active ) {  
           
            $cart     = is_page( wc_get_page_id( 'cart' ) );
            $checkout = is_page( wc_get_page_id( 'checkout' ) );

            wp_reset_query();

            if ( $cart || $checkout ) {

                wp_redirect( home_url() );
                exit;

            }
        }

    }

    add_action( 'wp', 'greenmart_woocommerce_catalog_mode_redirect_page' );
}
/*End catalog mode*/

/*Greenmart compare styles*/
if( ! function_exists( 'greenmart_compare_styles' ) ) {
    add_action( 'wp_print_styles', 'greenmart_compare_styles', 200 );
    function greenmart_compare_styles() {
        if( ! class_exists( 'YITH_Woocompare' ) ) return;
        $view_action = 'yith-woocompare-view-table';
        if ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != $view_action ) ) return;
        wp_enqueue_style( 'font-awesome' );
        wp_enqueue_style( 'simple-line-icons' );
        wp_enqueue_style( 'greenmart-woocommerce' );
    }
}


if ( !function_exists('greenmart_tbay_woocommerce_search_category') ) {
    function greenmart_tbay_woocommerce_search_category($active) {
        $active = greenmart_tbay_get_config('search_category', false);

        $active = (isset($_GET['search_category'])) ? $_GET['search_category'] : $active;

        return $active;
    }
} 
add_filter( 'greenmart_woo_search_category', 'greenmart_tbay_woocommerce_search_category' );

// class hide sub title product
if ( !function_exists('greenmart_tbay_woocommerce_hide_sub_title') ) {
    function greenmart_tbay_woocommerce_hide_sub_title($active) {
        $active = greenmart_tbay_get_config('enable_hide_sub_title_product', false);

        $active = (isset($_GET['hide_sub_title'])) ? $_GET['hide_sub_title'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_hide_sub_title', 'greenmart_tbay_woocommerce_hide_sub_title' );

if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_hide_sub_title' ) ) {
    function greenmart_tbay_body_classes_woocommerce_hide_sub_title( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_hide_sub_title', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-body-hide-sub-title';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_hide_sub_title' );
}


/*Show Add to Cart on mobile*/
if ( !function_exists('greenmart_tbay_woocommerce_show_cart_mobile') ) {
    function greenmart_tbay_woocommerce_show_cart_mobile($active) {
        $active = greenmart_tbay_get_config('enable_add_cart_mobile', false);

        $active = (isset($_GET['add_cart_mobile'])) ? $_GET['add_cart_mobile'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_show_cart_mobile', 'greenmart_tbay_woocommerce_show_cart_mobile' );


if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_show_cart_mobile' ) ) {
    function greenmart_tbay_body_classes_woocommerce_show_cart_mobile( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_show_cart_mobile', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-show-cart-mobile';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_show_cart_mobile' );
}


/*Disable Add To Cart Fixed on mobile*/
if ( !function_exists('greenmart_tbay_woocommerce_disable_add_cart_fixed') ) {
    function greenmart_tbay_woocommerce_disable_add_cart_fixed($active) {
        $active = greenmart_tbay_get_config('disable_add_cart_fixed', false);

        $active = (isset($_GET['disable_add_cart_fixed'])) ? $_GET['disable_add_cart_fixed'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_disable_add_cart_fixed', 'greenmart_tbay_woocommerce_disable_add_cart_fixed' );

if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_disable_add_cart_fixed' ) ) {
    function greenmart_tbay_body_classes_woocommerce_disable_add_cart_fixed( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_disable_add_cart_fixed', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-disable-cart-fixed';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_disable_add_cart_fixed' );
}

/*Show Quantity on mobile*/
if ( !function_exists('greenmart_tbay_woocommerce_show_quantity_mobile') ) {
    function greenmart_tbay_woocommerce_show_quantity_mobile($active) {
        $active = greenmart_tbay_get_config('enable_quantity_mobile', false);

        $active = (isset($_GET['quantity_mobile'])) ? $_GET['quantity_mobile'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_show_quantity_mobile', 'greenmart_tbay_woocommerce_show_quantity_mobile' );

if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_show_quantity_mobile' ) ) {
    function greenmart_tbay_body_classes_woocommerce_show_quantity_mobile( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_show_quantity_mobile', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-show-quantity-mobile';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_show_quantity_mobile' );
}

if ( ! function_exists( 'greenmart_woo_show_product_loop_outstock_flash' ) ) {
    /*Change Out of Stock woo*/
    add_filter( 'woocommerce_before_shop_loop_item_title', 'greenmart_woo_show_product_loop_outstock_flash' ,15 );
    function greenmart_woo_show_product_loop_outstock_flash( $html ) {

        $product        = wc_get_product();
        $availability   = $product->get_availability();
        $return_content = '';

        if ( $availability['availability'] == 'Out of stock') {
           $return_content .= '<span class="out-stock">'. esc_html__('Out of stock', 'greenmart') .'</span>';
        }

        echo $return_content;
    }
}

/*product time countdown*/
if(!function_exists('greenmart_woo_product_single_time_countdown')){

    add_action( 'woocommerce_single_product_summary', 'greenmart_woo_product_single_time_countdown', 25 );
    function greenmart_woo_product_single_time_countdown() {

        global $product;

        $style_countdown   = greenmart_tbay_get_config('show_product_countdown',false);

        if ( isset($_GET['countdown']) ) {
            $countdown = $_GET['countdown'];
        }else {
            $countdown = $style_countdown;
        }  

        if(!$countdown || !$product->is_on_sale() ) {
          return '';
        }

        global $product;
        wp_enqueue_script( 'jquery-countdowntimer' ); 
        $time_sale = get_post_meta( $product->get_id(), '_sale_price_dates_to', true );

        ?>
        <?php if ( $time_sale ): ?>
          <div class="time tbay-single-time">
                <div class="tbay-countdown" data-time="timmer" data-days="<?php esc_html_e('Days','greenmart'); ?>" data-hours="<?php esc_html_e('Hours','greenmart'); ?>"  data-mins="<?php esc_html_e('Mins','greenmart'); ?>" data-secs="<?php esc_html_e('Secs','greenmart'); ?>"
                   data-date="<?php echo date('m', $time_sale).'-'.date('d', $time_sale).'-'.date('Y', $time_sale).'-'. date('H', $time_sale) . '-' . date('i', $time_sale) . '-' .  date('s', $time_sale) ; ?>">
              </div>
          </div> 
        <?php endif; ?> 
        <?php
    }
}

if ( ! function_exists( 'greenmart_tbay_get_title_mobile' ) ) {
    function greenmart_tbay_get_title_mobile( $title = '') {

        if ( is_product_category() || is_category() ) {
            $title = single_cat_title();
        }  else if ( is_search() ) {
            $title = esc_html__('Search results for "','greenmart')  . get_search_query();
        } else if ( is_tag() ) {
            $title = esc_html__('Posts tagged "', 'greenmart'). single_tag_title('', false) . '"';
        } else if ( is_author() ) {
            global $author;
            $userdata = get_userdata($author);
            $title = esc_html__('Articles posted by ', 'greenmart') . $userdata->display_name;
        } else if ( is_404() ) {
            $title = esc_html__('Error 404', 'greenmart');
        } else if( is_shop () ) {
            $title = esc_html__('shop','greenmart');
        } else {
            $title = get_the_title();
        }

        return $title;
    }
    add_filter( 'greenmart_get_filter_title_mobile', 'greenmart_tbay_get_title_mobile' );
}

//Enqueue Ajax Scripts
function greenmart_tbay_enqueue_cart_qty_ajax() {

    wp_register_script( 'cart-qty-ajax-js', get_template_directory_uri() . '/js/cart-qty-ajax.js', array( 'jquery' ), '', true );
    wp_localize_script( 'cart-qty-ajax-js', 'cart_qty_ajax', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    wp_enqueue_script( 'cart-qty-ajax-js' );

}
add_action('wp_enqueue_scripts', 'greenmart_tbay_enqueue_cart_qty_ajax');

function greenmart_tbay_ajax_qty_cart() {

    // Set item key as the hash found in input.qty's name
    $cart_item_key = $_POST['hash'];

    // Get the array of values owned by the product we're updating
    $threeball_product_values = WC()->cart->get_cart_item( $cart_item_key );

    // Get the quantity of the item in the cart
    $threeball_product_quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

    // Update cart validation
    $passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $threeball_product_values, $threeball_product_quantity );

    // Update the quantity of the item in the cart
    if ( $passed_validation ) {
        WC()->cart->set_quantity( $cart_item_key, $threeball_product_quantity, true );
    }

    die();

}

add_action('wp_ajax_tbay_qty_cart', 'greenmart_tbay_ajax_qty_cart');
add_action('wp_ajax_nopriv_tbay_qty_cart', 'greenmart_tbay_ajax_qty_cart');

/**
 * Remove password strength check.
 */
if ( ! function_exists( 'greenmart_tbay_remove_password_strength' ) ) {
    function greenmart_tbay_remove_password_strength() {
        $active = greenmart_tbay_get_config('disable_woocommerce_password_strength', false);

        if( isset($active) && $active ) {
            wp_dequeue_script( 'wc-password-strength-meter' );
        }
    }
    add_action( 'wp_print_scripts', 'greenmart_tbay_remove_password_strength', 10 );
}


// Quantity mode

if ( !function_exists('greenmart_tbay_woocommerce_quantity_mode_active') ) {
    function greenmart_tbay_woocommerce_quantity_mode_active($active) {
        $active = greenmart_tbay_get_config('enable_woocommerce_quantity_mode', false);

        $active = (isset($_GET['quantity_mode'])) ? $_GET['quantity_mode'] : $active;

        return $active;
    }
}
add_filter( 'greenmart_quantity_mode', 'greenmart_tbay_woocommerce_quantity_mode_active' );

// class catalog mode
if ( ! function_exists( 'greenmart_tbay_body_classes_woocommerce_quantity_mod' ) ) {
    function greenmart_tbay_body_classes_woocommerce_quantity_mod( $classes ) {
        $class = '';
        $active = apply_filters( 'greenmart_quantity_mode', 10,2 );
        if( isset($active) && $active ) {  
            $class = 'tbay-body-woocommerce-quantity-mod';
        }

        $classes[] = trim($class);

        return $classes;
    }
    add_filter( 'body_class', 'greenmart_tbay_body_classes_woocommerce_quantity_mod' );
}

if ( !function_exists('greenmart_woocommerce_quantity_mode') ) {
    function greenmart_woocommerce_quantity_mode() {
        $active = apply_filters( 'greenmart_quantity_mode', 10,2 );
        if( isset($active) && $active ) {  
            add_action( 'woocommerce_after_shop_loop_item', 'greenmart_tbay_qquantity_field_archive', 5);
        }

    }

    add_action( 'init', 'greenmart_woocommerce_quantity_mode' );
}

function greenmart_tbay_qquantity_field_archive( ) {

    global $product;
    if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {
        woocommerce_quantity_input( array( 'min_value' => 1, 'max_value' => $product->backorders_allowed() ? '' : $product->get_stock_quantity() ) );
    }

}
