<?php
/**
 * greenmart functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage greenmart
 * @since greenmart 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since greenmart 1.0
 */
define( 'GREENMART_THEME_VERSION', '1.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 660;
}

if ( ! function_exists( 'greenmart_tbay_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since greenmart 1.0
 */
function greenmart_tbay_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on greenmart, use a find and replace
	 * to change 'greenmart' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'greenmart', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( array( 'css/editor-style.css', greenmart_fonts_url() ) );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	
	
	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu','greenmart' ),
		'mobile-menu' => esc_html__( 'Mobile Menu','greenmart' ),
		'topmenu'  => esc_html__( 'Top Menu', 'greenmart' ),
		'nav-account'  => esc_html__( 'Nav Account', 'greenmart' ),
		'category-menu'  => esc_html__( 'Category Menu', 'greenmart' ),
		'category-menu-image'  => esc_html__( 'Category Menu Image', 'greenmart' ),
		'social'  => esc_html__( 'Social Links Menu', 'greenmart' ),
		'footer-menu'  => esc_html__( 'Footer Menu', 'greenmart' ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	add_theme_support( "woocommerce" );
	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );

	$color_scheme  = greenmart_tbay_get_color_scheme();
	$default_color = trim( $color_scheme[0], '#' );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'greenmart_custom_background_args', array(
		'default-color'      => $default_color,
		'default-attachment' => 'fixed',
	) ) );
	
	greenmart_tbay_get_load_plugins();
}
endif; // greenmart_tbay_setup
add_action( 'after_setup_theme', 'greenmart_tbay_setup' );

function theme_setup_size_image() {
    // Be sure your theme supports post-thumbnails
		add_theme_support( 'post-thumbnails' );
		// Post Thumbnails Size
		set_post_thumbnail_size(380, 220, true); // Unlimited height, soft crop

		update_option('thumbnail_size_w', 380);
		update_option('thumbnail_size_h', 220);		

		update_option('medium_size_w', 470);
		update_option('medium_size_h', 272);		

		update_option('large_size_w', 470);
		update_option('large_size_h', 272);
}
add_action( 'after_setup_theme', 'theme_setup_size_image' );

/*
* Remove config default media
*
*/
if(greenmart_tbay_get_global_config('config_media',false)) {
	remove_action( 'after_setup_theme', 'theme_setup_size_image' );
}

 

/**
 * Load Google Front
 */
function greenmart_fonts_url() {
    $fonts_url = '';

    /* Translators: If there are characters in your language that are not
    * supported by Montserrat, translate this to 'off'. Do not translate
    * into your own language.
    */
    $Roboto 		= _x( 'on', 'Roboto font: on or off', 'greenmart' );
    $Roboto_Slab    		= _x( 'on', 'Roboto_Slab font: on or off', 'greenmart' );
 
    if ( 'off' !== $Roboto || 'off' !== $montserrat ) {
        $font_families = array();
 
        if ( 'off' !== $Roboto ) {
            $font_families[] = 'Roboto:100,100i,300,300i,400,400i,500,500i,700,700i,900';
        }
		
		if ( 'off' !== $Roboto_Slab ) {
            $font_families[] = 'Roboto+Slab:100,300,400,700';
        }
 
        $query_args = array(
            'family' => ( implode( '%7C', $font_families ) ),
            'subset' => urlencode( 'latin,latin-ext' ),
        );
 		
 		$protocol = is_ssl() ? 'https:' : 'http:';
        $fonts_url = add_query_arg( $query_args, $protocol .'//fonts.googleapis.com/css' );
    }
 
    return esc_url_raw( $fonts_url );
}

function greenmart_tbay_fonts_url() {  
	$protocol = is_ssl() ? 'https:' : 'http:';
	wp_enqueue_style( 'greenmart-theme-fonts', greenmart_fonts_url(), array(), null );
}
add_action('wp_enqueue_scripts', 'greenmart_tbay_fonts_url');


function greenmart_tbay_include_files($path) {
    $files = glob( $path );
    if ( ! empty( $files ) ) {
        foreach ( $files as $file ) {
            include $file;
        }
    }
}

/**
 * Enqueue scripts and styles.
 *
 * @since greenmart 1.0
 */
function greenmart_tbay_scripts() { 
	
	
	$skin = greenmart_tbay_get_skin();
	// Load our main stylesheet.
	if( is_rtl() ){
		
		if ( $skin != 'default' && $skin ) {
			$css_path =  get_template_directory_uri() . '/css/skin/'.$skin.'/template.rtl.css';
		} else {
			$css_path =  get_template_directory_uri() . '/css/template.rtl.css';
		}
	}
	else{
		if ( $skin != 'default' && $skin ) {
			$css_path =  get_template_directory_uri() . '/css/skin/'.$skin.'/template.css';
		} else {
			$css_path =  get_template_directory_uri() . '/css/template.css';
		}
	}
	wp_enqueue_style( 'greenmart-template', $css_path, array(), '1.0' );
	
	$footer_style = greenmart_tbay_print_style_footer();
	if ( !empty($footer_style) ) {
		wp_add_inline_style( 'greenmart-template', $footer_style );
	}
	$custom_style = greenmart_tbay_custom_styles();
	if ( !empty($custom_style) ) {
		wp_add_inline_style( 'greenmart-template', $custom_style );
	}
	
	wp_enqueue_style( 'greenmart-style', get_template_directory_uri() . '/style.css', array(), '3.2' );
	//load font awesome
	wp_enqueue_style( 'font-awesome', get_template_directory_uri() . '/css/font-awesome.css', array(), '4.5.0' );
	
	//load font simple-line-icons
	wp_enqueue_style( 'simple-line-icons', get_template_directory_uri() . '/css/simple-line-icons.css', array(), '2.4.0' );
	
	//load font material-design-iconic-font
	wp_enqueue_style( 'material-design-iconic-font', get_template_directory_uri() . '/css/material-design-iconic-font.min.css', array(), '2.2.0' );
	
	//load font ico-font
	wp_enqueue_style( 'icofont', get_template_directory_uri() . '/css/icofont.css', array(), '1.0.0' );
	
	//load font themify-icons
	wp_enqueue_style( 'themify-icons', get_template_directory_uri() . '/css/themify-icons.css', array(), '4.8.1' );

	// load animate version 3.5.0
	wp_enqueue_style( 'animate', get_template_directory_uri() . '/css/animate.css', array(), '3.5.0' );
	
	// load Sumoselect version 1.0.0
	wp_enqueue_style('sumoselect', get_template_directory_uri() . '/css/sumoselect.css', array(), '1.0.0', 'all');



	wp_enqueue_style( 'jquery-fancybox', get_template_directory_uri() . '/css/jquery.fancybox.css', array(), '3.2.0' );
	
	wp_enqueue_script( 'greenmart-skip-link-fix', get_template_directory_uri() . '/js/greenmart-skip-link-fix.js', array(), '20141010', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/*Treeview menu*/
	wp_enqueue_style( 'jquery-treeview', get_template_directory_uri() . '/css/jquery.treeview.css', array(), '1.0.0' );
	wp_enqueue_script( 'jquery-treeview', get_template_directory_uri() . '/js/jquery.treeview.js', array( 'jquery' ), '20150330', true );
	
	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/js/bootstrap.min.js', array( 'jquery' ), '20150330', true );
	
	// Add js Sumoselect version 3.0.2
	wp_enqueue_script('jquery-sumoselect', get_template_directory_uri() . '/js/jquery.sumoselect.js', array(), '3.0.2', TRUE);

	wp_dequeue_script('wpb_composer_front_js');
	wp_enqueue_script( 'wpb_composer_front_js');
	
	wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl.carousel.min.js', array( 'jquery' ), '2.3.2', true );
	wp_enqueue_script( 'greenmart-woocommerce', get_template_directory_uri() . '/js/woocommerce.js', array( 'jquery' ), '20150330', true );

	wp_enqueue_script( 'jquery-countdowntimer', get_template_directory_uri() . '/js/jquery.countdownTimer.min.js', array( 'jquery' ), '20150315', true );

	wp_enqueue_script( 'jquery-fancybox', get_template_directory_uri() . '/js/jquery.fancybox.js', array( 'jquery' ), '20150315', true );

	wp_register_script( 'greenmart-script', get_template_directory_uri() . '/js/functions.js', array( 'jquery' ), '20150330', true );
	wp_localize_script( 'greenmart-script', 'greenmart_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	wp_enqueue_script( 'greenmart-script' );
	if ( greenmart_tbay_get_config('header_js') != "" ) {
		wp_add_inline_script( 'jquery-core', greenmart_tbay_get_config('header_js') );
	}
	
	wp_enqueue_style( 'greenmart-style', get_template_directory_uri() . '/style.css', array(), '1.0' );
}
add_action( 'wp_enqueue_scripts', 'greenmart_tbay_scripts', 100 );

function greenmart_tbay_footer_scripts() {
	if ( greenmart_tbay_get_config('footer_js') != "" ) {
		wp_add_inline_script( 'greenmart-footer-js', greenmart_tbay_get_config('footer_js') );
	}
}
add_action('wp_footer', 'greenmart_tbay_footer_scripts');

add_action( 'admin_enqueue_scripts', 'greenmart_tbay_load_admin_styles' );
function greenmart_tbay_load_admin_styles() {
	wp_enqueue_style( 'greenmart-custom-admin', get_template_directory_uri() . '/css/admin/custom-admin.css', false, '1.0.0' );
}  


/**
 * Display descriptions in main navigation.
 *
 * @since greenmart 1.0
 *
 * @param string  $item_output The menu item output.
 * @param WP_Post $item        Menu item object.
 * @param int     $depth       Depth of the menu.
 * @param array   $args        wp_nav_menu() arguments.
 * @return string Menu item with possible description.
 */
function greenmart_tbay_nav_description( $item_output, $item, $depth, $args ) {
	if ( 'primary' == $args->theme_location && $item->description ) {
		$item_output = str_replace( $args->link_after . '</a>', '<div class="menu-item-description">' . $item->description . '</div>' . $args->link_after . '</a>', $item_output );
	}

	return $item_output;
}
add_filter( 'walker_nav_menu_start_el', 'greenmart_tbay_nav_description', 10, 4 );

/**
 * Add a `screen-reader-text` class to the search form's submit button.
 *
 * @since greenmart 1.0
 *
 * @param string $html Search form HTML.
 * @return string Modified search form HTML.
 */
function greenmart_tbay_search_form_modify( $html ) {
	return str_replace( 'class="search-submit"', 'class="search-submit screen-reader-text"', $html );
}
add_filter( 'get_search_form', 'greenmart_tbay_search_form_modify' );

function greenmart_tbay_get_config($name, $default = '') {
	global $tbay_options;
    if ( isset($tbay_options[$name]) ) {
        return $tbay_options[$name];
    }
    return $default;
}

if ( ! function_exists( 'greenmart_time_link' ) ) :
/**
 * Gets a nicely formatted string for the published date.
 */
function greenmart_time_link() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';

	$time_string = sprintf( $time_string,
		get_the_date( DATE_W3C ),
		get_the_date(),
		get_the_modified_date( DATE_W3C ),
		get_the_modified_date()
	);

	// Wrap the time string in a link, and preface it with 'Posted on'.
	return sprintf(
		/* translators: %s: post date */
		__( '<span class="screen-reader-text">Posted on</span> %s', 'greenmart' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
}
endif;

function greenmart_tbay_get_global_config($name, $default = '') {
	$options = get_option( 'greenmart_tbay_theme_options', array() );
	if ( isset($options[$name]) ) {
        return $options[$name];
    }
    return $default;
}

function greenmart_tbay_widgets_init() {

	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar Default', 'greenmart' ),
		'id'            => 'sidebar-default',
		'description'   => esc_html__( 'Add widgets here to appear in your Sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Top Contact Layout 2', 'greenmart' ),
		'id'            => 'top-contact',
		'description'   => esc_html__( 'Add widgets here to appear in Top Contact.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Contact Layout 4', 'greenmart' ),
		'id'            => 'top-contact-2',
		'description'   => esc_html__( 'Add widgets here to appear in Top Contact Layout 4.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Header Contact Layout 4', 'greenmart' ),
		'id'            => 'header-contact-v4',
		'description'   => esc_html__( 'Add widgets here to appear in Header Contact Layout 4.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Top Archive Product', 'greenmart' ),
		'id'            => 'top-archive-product',
		'description'   => esc_html__( 'Add widgets here to appear in Top Archive Product.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
	register_sidebar( array(
		'name'          => esc_html__( 'Blog left sidebar', 'greenmart' ),
		'id'            => 'blog-left-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Blog right sidebar', 'greenmart' ),
		'id'            => 'blog-right-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Product left sidebar', 'greenmart' ),
		'id'            => 'product-left-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Product right sidebar', 'greenmart' ),
		'id'            => 'product-right-sidebar',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer', 'greenmart' ),
		'id'            => 'footer',
		'description'   => esc_html__( 'Add widgets here to appear in your sidebar.', 'greenmart' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	
}
add_action( 'widgets_init', 'greenmart_tbay_widgets_init' );

function greenmart_tbay_get_load_plugins() {

	$plugins[] =(array(
		'name'                     => esc_html__( 'Cmb2', 'greenmart' ),
	    'slug'                     => 'cmb2',
	    'required'                 => true,
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'WooCommerce', 'greenmart' ),
	    'slug'                     => 'woocommerce',
	    'required'                 => true,
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'MailChimp', 'greenmart' ),
	    'slug'                     => 'mailchimp-for-wp',
	    'required'                 =>  true
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'Contact Form 7', 'greenmart' ),
	    'slug'                     => 'contact-form-7',
	    'required'                 => true,
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'WPBakery Visual Composer', 'greenmart' ),
		'slug'                     => 'js_composer',
		'required'                 => true,
		'source'         		   		 => get_template_directory() . '/plugins/js_composer.zip',
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'Tbay Framework For Themes', 'greenmart' ),
		'slug'                     => 'tbay-framework',
		'required'                 => true ,
		'source'         		   		 => esc_url( 'https://plugins.thembay.com/tbay-framework.zip' ),
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'WooCommerce Variation Swatches', 'greenmart' ),
	    'slug'                     => 'variation-swatches-for-woocommerce',
	    'required'                 =>  true
	));	

	$plugins[] =(array(
		'name'                     => esc_html__( 'YITH WooCommerce Quick View', 'greenmart' ),
	    'slug'                     => 'yith-woocommerce-quick-view',
	    'required'                 =>  true
	));
	
	$plugins[] =(array(
		'name'                     => esc_html__( 'YITH WooCommerce Wishlist', 'greenmart' ),
	    'slug'                     => 'yith-woocommerce-wishlist',
	    'required'                 =>  true
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'YITH Woocommerce Compare', 'greenmart' ),
        'slug'                     => 'yith-woocommerce-compare',
        'required'                 => true
	));

	$plugins[] =(array(
		'name'                     => esc_html__( 'Revolution Slider', 'greenmart' ),
		'slug'                     => 'revslider',
		'required'                 => true ,
		'source'         		   => get_template_directory() . '/plugins/revslider.zip'
	));
	
	tgmpa( $plugins );
}

require get_template_directory() . '/inc/plugins/class-tgm-plugin-activation.php';
require get_template_directory() . '/inc/functions-helper.php';
require get_template_directory() . '/inc/functions-frontend.php';

/**
 * Implement the Custom Header feature.
 *
 */
require get_template_directory() . '/inc/custom-header.php';
require get_template_directory() . '/inc/classes/megamenu.php';
require get_template_directory() . '/inc/classes/custommenu.php';

/**
 * Custom template tags for this theme.
 *
 */
require get_template_directory() . '/inc/template-tags.php'; 


if ( defined( 'TBAY_FRAMEWORK_REDUX_ACTIVED' ) ) {
	greenmart_tbay_include_files( get_template_directory() . '/inc/vendors/redux-framework/*.php' );
	define( 'GREENMART_REDUX_FRAMEWORK_ACTIVED', true );
}
if( in_array( 'cmb2/init.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	greenmart_tbay_include_files( get_template_directory() . '/inc/vendors/cmb2/*.php' );
	define( 'GREENMART_CMB2_ACTIVED', true );
}
if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	greenmart_tbay_include_files( get_template_directory() . '/inc/vendors/woocommerce/*.php' );
	define( 'GREENMART_WOOCOMMERCE_ACTIVED', true );
}
if( in_array( 'js_composer/js_composer.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	greenmart_tbay_include_files( get_template_directory() . '/inc/vendors/visualcomposer/*.php' );
	define( 'GREENMART_VISUALCOMPOSER_ACTIVED', true );
}
if( in_array( 'tbay-framework/tbay-framework.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	greenmart_tbay_include_files( get_template_directory() . '/inc/widgets/*.php' );
	define( 'GREENMART_TBAY_FRAMEWORK_ACTIVED', true );
}
/**
 * Customizer additions.
 *
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom Styles
 *
 */
require get_template_directory() . '/inc/custom-styles.php';
require get_template_directory() . '/inc/custom-styles.php';