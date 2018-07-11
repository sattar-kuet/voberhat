<?php

if ( ! function_exists( 'greenmart_tbay_body_classes' ) ) {
	function greenmart_tbay_body_classes( $classes ) {
		global $post;
		if ( is_page() && is_object($post) ) {
			$class = get_post_meta( $post->ID, 'tbay_page_extra_class', true );
			if ( !empty($class) ) {
				$classes[] = trim($class);
			}
		}
		if ( greenmart_tbay_get_config('preload') ) {
			$classes[] = 'tbay-body-loader';
		}

		if ( !(defined('GREENMART_WOOCOMMERCE_ACTIVED') && GREENMART_WOOCOMMERCE_ACTIVED) ) {
			$classes[] = 'tb-no-shop';
		}

		return $classes;
	}
	add_filter( 'body_class', 'greenmart_tbay_body_classes' );
}

if ( ! function_exists( 'greenmart_tbay_get_shortcode_regex' ) ) {
	function greenmart_tbay_get_shortcode_regex( $tagregexp = '' ) {
		// WARNING! Do not change this regex without changing do_shortcode_tag() and strip_shortcode_tag()
		// Also, see shortcode_unautop() and shortcode.js.
		return
			'\\['                                // Opening bracket
			. '(\\[?)'                           // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
			. "($tagregexp)"                     // 2: Shortcode name
			. '(?![\\w-])'                       // Not followed by word character or hyphen
			. '('                                // 3: Unroll the loop: Inside the opening shortcode tag
			. '[^\\]\\/]*'                   // Not a closing bracket or forward slash
			. '(?:'
			. '\\/(?!\\])'               // A forward slash not followed by a closing bracket
			. '[^\\]\\/]*'               // Not a closing bracket or forward slash
			. ')*?'
			. ')'
			. '(?:'
			. '(\\/)'                        // 4: Self closing tag ...
			. '\\]'                          // ... and closing bracket
			. '|'
			. '\\]'                          // Closing bracket
			. '(?:'
			. '('                        // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
			. '[^\\[]*+'             // Not an opening bracket
			. '(?:'
			. '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
			. '[^\\[]*+'         // Not an opening bracket
			. ')*+'
			. ')'
			. '\\[\\/\\2\\]'             // Closing shortcode tag
			. ')?'
			. ')'
			. '(\\]?)';                          // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
}

if ( ! function_exists( 'greenmart_tbay_tagregexp' ) ) {
	function greenmart_tbay_tagregexp() {
		return apply_filters( 'greenmart_tbay_custom_tagregexp', 'video|audio|playlist|video-playlist|embed|greenmart_tbay_media' );
	}
}

if ( !function_exists('greenmart_tbay_class_container_vc') ) {
	function greenmart_tbay_class_container_vc($class, $isfullwidth, $post_type) {
		global $post;
		$isfullwidth = false;
		if ( $post_type == 'tbay_megamenu' ) {
			$isfullwidth = false;
		} elseif ( $post_type == 'tbay_footer' ) {
			$isfullwidth = false;
		} else {
			if ( is_page() ) {
				$isfullwidth  = get_post_meta( $post->ID, 'tbay_page_fullwidth', true );
				if ( $isfullwidth == 'no' ) {
					$isfullwidth = false;
				} else {
					$isfullwidth = true;
				}
			} elseif ( is_woocommerce() ) {
				if ( is_singular('product') ) {
					$isfullwidth  = greenmart_tbay_get_config( 'product_single_fullwidth', false );
				} else {
					$isfullwidth  = greenmart_tbay_get_config( 'product_archive_fullwidth', false );
				}
			} else {
				if ( is_singular('post') ) {
					$isfullwidth  = greenmart_tbay_get_config( 'post_single_fullwidth', false );
				} else {
					$isfullwidth  = greenmart_tbay_get_config( 'post_archive_fullwidth', false );
				}
			}
		}

		if ( $isfullwidth ) {
			return 'tbay-'.$class;
		}
		return $class;
	}
}
add_filter( 'greenmart_tbay_class_container_vc', 'greenmart_tbay_class_container_vc', 1, 3);

if ( !function_exists('greenmart_tbay_get_skins') ) {
	function greenmart_tbay_get_skins() {
		$skins = array( '' => esc_html__('Default', 'greenmart') );
		$path = get_template_directory() . '/css/skins/';
		if ( is_dir($path) ) {
			$folders = scandir($path);
			$excludes = array('.', '..', '.svn');
			foreach ($folders as $folder) {
				if ( !in_array( $folder, $excludes ) && is_dir($path . $folder) ) {
					$skins[$folder] = $folder;
				}
			}
		}
		return $skins;
	}
}

if ( !function_exists('greenmart_tbay_get_skin') ) {
	function greenmart_tbay_get_skin() {
		return greenmart_tbay_get_config('active_skin', '');
	}
}

if ( !function_exists('greenmart_tbay_get_header_layouts') ) {
	function greenmart_tbay_get_header_layouts() {
		$headers = array();
		$files = glob( get_template_directory() . '/headers/*.php' );
	    if ( !empty( $files ) ) {
	        foreach ( $files as $file ) {
	        	$header = str_replace( '.php', '', basename($file) );
	            $headers[$header] = $header;
	        }
	    }
		return $headers;
	}
}

if ( !function_exists('greenmart_tbay_get_header_layout') ) {
	function greenmart_tbay_get_header_layout() {
		global $post;
		if ( is_page() && is_object($post) && isset($post->ID) ) {
			return greenmart_tbay_page_header_layout();
		}
		return greenmart_tbay_get_config('header_type');
	}
	add_filter( 'greenmart_tbay_get_header_layout', 'greenmart_tbay_get_header_layout' );
}

if ( !function_exists('greenmart_tbay_get_footer_layouts') ) {
	function greenmart_tbay_get_footer_layouts() {
		$footers = array( '' => esc_html__('Default', 'greenmart'));
		$args = array(
			'posts_per_page'   => -1,
			'offset'           => 0,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'post_type'        => 'tbay_footer',
			'post_status'      => 'publish',
			'suppress_filters' => true 
		);
		$posts = get_posts( $args );
		foreach ( $posts as $post ) {
			$footers[$post->post_name] = $post->post_title;
		}
		return $footers;
	}
}

if ( !function_exists('greenmart_tbay_get_footer_layout') ) {
	function greenmart_tbay_get_footer_layout() {
		if ( is_page() ) {
			global $post;
			$footer = '';
			if ( is_object($post) && isset($post->ID) ) {
				$footer = get_post_meta( $post->ID, 'tbay_page_footer_type', true );
				if ( $footer == 'global' ) {
					return greenmart_tbay_get_config('footer_type', '');
				}
			}
			return $footer;
		}
		return greenmart_tbay_get_config('footer_type', '');
	}
	add_filter('greenmart_tbay_get_footer_layout', 'greenmart_tbay_get_footer_layout');
}

if ( !function_exists('greenmart_tbay_blog_content_class') ) {
	function greenmart_tbay_blog_content_class( $class ) {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		if ( greenmart_tbay_get_config('blog_'.$page.'_fullwidth') ) {
			return 'container-fluid';
		}
		return $class;
	}
}
add_filter( 'greenmart_tbay_blog_content_class', 'greenmart_tbay_blog_content_class', 1 , 1  );


if ( !function_exists('greenmart_tbay_get_blog_layout_configs') ) {
	function greenmart_tbay_get_blog_layout_configs() {
		$page = 'archive';
		if ( is_singular( 'post' ) ) {
            $page = 'single';
        }
		$left = greenmart_tbay_get_config('blog_'.$page.'_left_sidebar');
		$right = greenmart_tbay_get_config('blog_'.$page.'_right_sidebar');

		switch ( greenmart_tbay_get_config('blog_'.$page.'_layout') ) {
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

		return $configs; 
	}
}

if ( !function_exists('greenmart_tbay_page_content_class') ) {
	function greenmart_tbay_page_content_class( $class ) {
		global $post;
		$fullwidth = get_post_meta( $post->ID, 'tbay_page_fullwidth', true );
		if ( !$fullwidth || $fullwidth == 'no' ) {
			return $class;
		}
		return 'container-fluid';
	}
}
add_filter( 'greenmart_tbay_page_content_class', 'greenmart_tbay_page_content_class', 1 , 1  );

if ( !function_exists('greenmart_tbay_get_page_layout_configs') ) {
	function greenmart_tbay_get_page_layout_configs() {
		global $post;
		if( isset($post->ID) ) {
			$left = get_post_meta( $post->ID, 'tbay_page_left_sidebar', true );
			$right = get_post_meta( $post->ID, 'tbay_page_right_sidebar', true );

			switch ( get_post_meta( $post->ID, 'tbay_page_layout', true ) ) {
				case 'left-main':
					$configs['left'] = array( 'sidebar' => $left, 'class' => 'col-xs-12 col-md-12 col-lg-3'  );
					$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					break;
				case 'main-right':
					$configs['right'] = array( 'sidebar' => $right,  'class' => 'col-xs-12 col-md-12 col-lg-3' ); 
					$configs['main'] = array( 'class' => 'col-xs-12 col-md-12 col-lg-9' );
					break;
				case 'main':
					$configs['main'] = array( 'class' => 'clearfix' );
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

			return $configs; 
		}
	}
}

if ( !function_exists('greenmart_tbay_page_header_layout') ) {
	function greenmart_tbay_page_header_layout() {
		global $post;
		$header = get_post_meta( $post->ID, 'tbay_page_header_type', true );
		if ( $header == 'global' ) {
			return greenmart_tbay_get_config('header_type');
		}
		return $header;
	}
}

if ( ! function_exists( 'greenmart_tbay_get_first_url_from_string' ) ) {
	function greenmart_tbay_get_first_url_from_string( $string ) {
		$pattern = "/^\b(?:(?:https?|ftp):\/\/)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
		preg_match( $pattern, $string, $link );

		return ( ! empty( $link[0] ) ) ? $link[0] : false;
	}
}

if ( !function_exists( 'greenmart_tbay_get_link_attributes' ) ) {
	function greenmart_tbay_get_link_attributes( $string ) {
		preg_match( '/<a href="(.*?)">/i', $string, $atts );

		return ( ! empty( $atts[1] ) ) ? $atts[1] : '';
	}
}

if ( !function_exists( 'greenmart_tbay_post_media' ) ) {
	function greenmart_tbay_post_media( $content ) {
		$is_video = ( get_post_format() == 'video' ) ? true : false;
		$media = greenmart_tbay_get_first_url_from_string( $content );
		if ( ! empty( $media ) ) {
			global $wp_embed;
			$content = do_shortcode( $wp_embed->run_shortcode( '[embed]' . $media . '[/embed]' ) );
		} else {
			$pattern = greenmart_tbay_get_shortcode_regex( greenmart_tbay_tagregexp() );
			preg_match( '/' . $pattern . '/s', $content, $media );
			if ( ! empty( $media[2] ) ) {
				if ( $media[2] == 'embed' ) {
					global $wp_embed;
					$content = do_shortcode( $wp_embed->run_shortcode( $media[0] ) );
				} else {
					$content = do_shortcode( $media[0] );
				}
			}
		}
		if ( ! empty( $media ) ) {
			$output = '<div class="entry-media">';
			$output .= ( $is_video ) ? '<div class="pro-fluid"><div class="pro-fluid-inner">' : '';
			$output .= $content;
			$output .= ( $is_video ) ? '</div></div>' : '';
			$output .= '</div>';

			return $output;
		}

		return false;
	}
}

if ( !function_exists( 'greenmart_tbay_post_gallery' ) ) {
	function greenmart_tbay_post_gallery( $content ) {
		$pattern = greenmart_tbay_get_shortcode_regex( 'gallery' );
		preg_match( '/' . $pattern . '/s', $content, $media );
		if ( ! empty( $media[2] )  ) {
			return '<div class="entry-gallery">' . do_shortcode( $media[0] ) . '<hr class="pro-clear" /></div>';
		}

		return false;
	}
}

if ( !function_exists( 'greenmart_tbay_random_key' ) ) {
    function greenmart_tbay_random_key($length = 5) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $return;
    }
}

if ( !function_exists('greenmart_tbay_substring') ) {
    function greenmart_tbay_substring($string, $limit, $afterlimit = '[...]') {
        if ( empty($string) ) {
        	return $string;
        }
       	$string = explode(' ', strip_tags( $string ), $limit);

        if (count($string) >= $limit) {
            array_pop($string);
            $string = implode(" ", $string) .' '. $afterlimit;
        } else {
            $string = implode(" ", $string);
        }
        $string = preg_replace('`[[^]]*]`','',$string);
        return strip_shortcodes( $string );
    }
}

if ( !function_exists( 'greenmart_tbay_autocomplete_search' ) ) {
    function greenmart_tbay_autocomplete_search() {

        if ( greenmart_tbay_get_global_config('autocomplete_search') ) {
            wp_register_script( 'greenmart-autocomplete-js', get_template_directory_uri() . '/js/autocomplete-search-init.js', array('jquery','jquery-ui-autocomplete'), null, true);
            wp_enqueue_script( 'greenmart-autocomplete-js' );

            add_action( 'wp_ajax_greenmart_autocomplete_search', 'greenmart_tbay_autocomplete_suggestions' );
            add_action( 'wp_ajax_nopriv_greenmart_autocomplete_search', 'greenmart_tbay_autocomplete_suggestions' );
        }
    }
}
add_action( 'init', 'greenmart_tbay_autocomplete_search' );

if ( !function_exists( 'greenmart_tbay_autocomplete_suggestions' ) ) {
    function greenmart_tbay_autocomplete_suggestions() {
        // Query for suggestions
        $args = array( 's' => $_REQUEST['term'] );
        if ( isset($_REQUEST['post_type']) ) {
        	$args['post_type'] = $_REQUEST['post_type'];
        }
        if ( isset($_REQUEST['category']) ) {
        	
        	if ( $args['post_type'] == 'product' ) {
        		$args['product_cat'] = $_REQUEST['category'];
        	} else {
        		$args['category'] = $_REQUEST['category'];
        	}
        }
        $posts = get_posts( $args );
        $suggestions = array();
        $show_image = greenmart_tbay_get_config('show_search_product_image');
        $show_price = greenmart_tbay_get_config('search_type') == 'product' ? greenmart_tbay_get_config('show_search_product_price') : false;
        global $post;
        foreach ($posts as $post): setup_postdata($post);
            
            $suggestion = array();
            $suggestion['label'] = esc_html($post->post_title);
            $suggestion['link'] = get_permalink();
            if ( $show_image && has_post_thumbnail( $post->ID ) ) {
                $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'thumbnail' );
                $suggestion['image'] = $image[0];
            } else {
                $suggestion['image'] = '';
            }
            if ( $show_price ) {
            	$product = new WC_Product( get_the_ID() );
                $suggestion['price'] = esc_html__('Price', 'greenmart').' '.$product->get_price_html();
            } else {
                $suggestion['price'] = '';
            }

            $suggestions[]= $suggestion;
        endforeach;
        
        $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
        echo $response;
     
        exit;
    }
}
/*testimonials*/
if ( !function_exists('greenmart_tbay_get_testimonials_layouts') ) {
	function greenmart_tbay_get_testimonials_layouts() {
		$testimonials = array();
		$files = glob( get_template_directory() . '/vc_templates/testimonial/testimonial-*.php' );
	    if ( !empty( $files ) ) {
	        foreach ( $files as $file ) {
	        	$testi = str_replace( "testimonial-", '', str_replace( '.php', '', basename($file) ) );
	            $testimonials[$testi] = $testi;
	        }
	    }

		return $testimonials;
	}
}

/*Check in home page*/
if ( !function_exists('greenmart_tbay_is_home_page') ) {
	function greenmart_tbay_is_home_page() {
		$is_home = false;

		if( is_home() || is_front_page() || is_page( 'home-8' )  || is_page( 'home-7' )  || is_page( 'home-7-2' ) || is_page( 'home-1' ) || is_page( 'home-2' ) || is_page( 'home-3' ) || is_page( 'home-4' ) || is_page( 'home-5' ) || is_page( 'home-6' ) || is_page( 'home-7' ) || is_page( 'home-8' ) ) {
			$is_home = true;
		}

		return $is_home;
	}
}

if ( !function_exists('greenmart_gallery_atts') ) {

	add_filter( 'shortcode_atts_gallery', 'greenmart_gallery_atts', 10, 3 );
	
	/* Change attributes of wp gallery to modify image sizes for your needs */
	function greenmart_gallery_atts( $output, $pairs, $atts ) {

			
		if ( isset($atts['columns']) && $atts['columns'] == 1 ) {
			//if gallery has one column, use large size
			$output['size'] = 'full';
		} else if ( isset($atts['columns']) && $atts['columns'] >= 2 && $atts['columns'] <= 4 ) {
			//if gallery has between two and four columns, use medium size
			$output['size'] = 'full';
		} else {
			//if gallery has more than four columns, use thumbnail size
			$output['size'] = 'full';
		}
	
		return $output;
	
	}
}

if ( !function_exists('greenmart_tbay_share_js') ) {
	function greenmart_tbay_share_js() {
		 if ( is_single()   ) {
		 			echo greenmart_tbay_get_config('code_share');
		 	}
	}
	add_action('wp_head', 'greenmart_tbay_share_js');
}

/*Get Preloader*/
if ( ! function_exists( 'greenmart_get_select_preloader' ) ) {
    function greenmart_get_select_preloader( ) {

    	$preloader = greenmart_tbay_get_global_config('select_preloader', 1);

    	if( isset($preloader) ) {
	    	switch ($preloader) {
	    		case 'loader1': 
	    			?>
	                <div class="tbay-page-loader">
					  	<div id="loader"></div>
					  	<div class="loader-section section-left"></div>
					  	<div class="loader-section section-right"></div>
					</div>
	    			<?php
	    			break;    		

	    		case 'loader2':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-two">
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader3':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-three">
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    	<span></span>
					    </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader4':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-four"> <span class="spinner-cube spinner-cube1"></span> <span class="spinner-cube spinner-cube2"></span> <span class="spinner-cube spinner-cube3"></span> <span class="spinner-cube spinner-cube4"></span> <span class="spinner-cube spinner-cube5"></span> <span class="spinner-cube spinner-cube6"></span> <span class="spinner-cube spinner-cube7"></span> <span class="spinner-cube spinner-cube8"></span> <span class="spinner-cube spinner-cube9"></span> </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader5':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-five"> <span class="spinner-cube-1 spinner-cube"></span> <span class="spinner-cube-2 spinner-cube"></span> <span class="spinner-cube-4 spinner-cube"></span> <span class="spinner-cube-3 spinner-cube"></span> </div>
					</div>
	    			<?php
	    			break;    		
	    		case 'loader6':
	    			?>
					<div class="tbay-page-loader">
					    <div class="tbay-loader tbay-loader-six"> <span class=" spinner-cube-1 spinner-cube"></span> <span class=" spinner-cube-2 spinner-cube"></span> </div>
					</div>
	    			<?php
	    			break;
	    			
	    		default:
	    			?>
	    			<div class="tbay-page-loader">
					  	<div id="loader"></div>
					  	<div class="loader-section section-left"></div>
					  	<div class="loader-section section-right"></div>
					</div>
	    			<?php
	    			break;
	    	}
	    }
     	
    }
}

/*Hidden footer body class*/
if ( ! function_exists( 'greenmart_body_class_hidden_footer' ) ) {
  function greenmart_body_class_hidden_footer( $classes ) {

    if(greenmart_tbay_get_config('hidden_footer',false)) {
      $classes[] = 'mobile-hidden-footer';
    }
    return $classes;

  }
  add_filter( 'body_class', 'greenmart_body_class_hidden_footer',99 );
}

// Number of blog per row
if ( !function_exists('greenmart_tbay_blog_loop_columns') ) {
    function greenmart_tbay_blog_loop_columns($number) {

    		$sidebar_configs = greenmart_tbay_get_blog_layout_configs();

    		$columns 	= greenmart_tbay_get_config('blog_columns', 1);

        if( isset($_GET['blog_columns']) && is_numeric($_GET['blog_columns']) ) {
            $value = $_GET['blog_columns']; 
        } elseif( empty($columns) && isset($sidebar_configs['columns']) ) {
    			$value = 	$sidebar_configs['columns']; 
    		} else {
          $value = $columns;          
        }

        if ( in_array( $value, array(1, 2, 3, 4, 5, 6) ) ) {
            $number = $value;
        }
        return $number;
    }
}
add_filter( 'loop_blog_columns', 'greenmart_tbay_blog_loop_columns' );