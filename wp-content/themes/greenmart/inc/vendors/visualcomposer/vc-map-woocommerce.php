<?php
if(!class_exists('WPBakeryShortCode')) return;

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	if ( !function_exists('greenmart_tbay_vc_get_term_object')) {
		function greenmart_tbay_vc_get_term_object($term) {
			$vc_taxonomies_types = vc_taxonomies_types();

			return array(
				'label' => $term->name,
				'value' => $term->slug,
				'group_id' => $term->taxonomy,
				'group' => isset( $vc_taxonomies_types[ $term->taxonomy ], $vc_taxonomies_types[ $term->taxonomy ]->labels, $vc_taxonomies_types[ $term->taxonomy ]->labels->name ) ? $vc_taxonomies_types[ $term->taxonomy ]->labels->name : esc_html__( 'Taxonomies', 'greenmart' ),
			);
		}
	} 

	if ( !function_exists('greenmart_tbay_category_field_search')) {
		function greenmart_tbay_category_field_search( $search_string ) {
			$data = array();
			$vc_taxonomies_types = array('product_cat');
			$vc_taxonomies = get_terms( $vc_taxonomies_types, array(
				'hide_empty' => false,
				'search' => $search_string
			) );
			if ( is_array( $vc_taxonomies ) && ! empty( $vc_taxonomies ) ) {
				foreach ( $vc_taxonomies as $t ) {
					if ( is_object( $t ) ) {
						$data[] = greenmart_tbay_vc_get_term_object( $t );
					}
				}
			}
			return $data;
		}
	}

	if ( !function_exists('greenmart_tbay_category_render')) {
		function greenmart_tbay_category_render($query) {  
			$category = get_term_by('id', (int)$query['value'], 'product_cat');
			if ( ! empty( $query ) && !empty($category)) {
				$data = array();
				$data['value'] = $category->slug;
				$data['label'] = $category->name;
				return ! empty( $data ) ? $data : false;
			}
			return false;
		}
	}

	$bases = array( 'tbay_productstabs', 'tbay_products', 'tbay_product_countdown' );
	foreach( $bases as $base ){   
		add_filter( 'vc_autocomplete_'.$base .'_categories_callback', 'greenmart_tbay_category_field_search', 10, 1 );
	 	add_filter( 'vc_autocomplete_'.$base .'_categories_render', 'greenmart_tbay_category_render', 10, 1 );
	}

	if ( !function_exists('greenmart_tbay_woocommerce_get_categories') ) {
	    function greenmart_tbay_woocommerce_get_categories() {
	        $return = array( esc_html__(' --- Choose a Category --- ', 'greenmart') );

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
	        greenmart_tbay_get_category_childs( $categories, 0, 0, $return );

	        return $return;
	    }
	}

	if ( !function_exists('greenmart_tbay_get_category_childs') ) {
	    function greenmart_tbay_get_category_childs( $categories, $id_parent, $level, &$dropdown ) {
	        foreach ( $categories as $key => $category ) {
	            if ( $category->category_parent == $id_parent ) { 
	                $dropdown = array_merge( $dropdown, array( str_repeat( "- ", $level ) . $category->name => $category->term_id ) );
	                unset($categories[$key]);
	                greenmart_tbay_get_category_childs( $categories, $category->term_id, $level + 1, $dropdown );
	            }
	        }
	    }
	}

	if ( !function_exists('greenmart_tbay_load_woocommerce_element')) {
		function greenmart_tbay_load_woocommerce_element() {
			$categories = greenmart_tbay_woocommerce_get_categories();
			$orderbys = array(
				esc_html__( 'Date', 'greenmart' ) => 'date',
				esc_html__( 'Price', 'greenmart' ) => 'price',
				esc_html__( 'Random', 'greenmart' ) => 'rand',
				esc_html__( 'Sales', 'greenmart' ) => 'sales',
				esc_html__( 'ID', 'greenmart' ) => 'ID'
			);

			$orderways = array(
				esc_html__( 'Descending', 'greenmart' ) => 'DESC',
				esc_html__( 'Ascending', 'greenmart' ) => 'ASC',
			);
			$layouts = array(
				'Grid'=>'grid',
				'Special'=>'special',
				'List'=>'list',
				'Carousel'=>'carousel',
				'Carousel Special'=>'carousel-special'
			);
			$types = array(
				'Best Selling' => 'best_selling',
				'Featured Products' => 'featured_product',
				'Top Rate' => 'top_rate',
				'Recent Products' => 'recent_product',
				'On Sale' => 'on_sale',
			);

			$producttabs = array(
	            array( 'recent_product', esc_html__('Latest Products', 'greenmart') ),
	            array( 'featured_product', esc_html__('Featured Products', 'greenmart') ),
	            array( 'best_selling', esc_html__('BestSeller Products', 'greenmart') ),
	            array( 'top_rate', esc_html__('TopRated Products', 'greenmart') ),
	            array( 'on_sale', esc_html__('On Sale Products', 'greenmart') )
	        );
			$columns = array(1,2,3,4,6);
			$rows 	 = array(1,2,3);
			vc_map( array(
		        "name" => esc_html__("Tbay Product CountDown",'greenmart'),
		        "base" => "tbay_product_countdown",
		        "icon"        => "vc-icon-tbay",
		        "class" => "",
		    	"category" => esc_html__('Tbay Woocommerce','greenmart'),
		    	'description'	=> esc_html__( 'Display Product Sales with Count Down', 'greenmart' ),
		        "params" => array(
		            array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Title','greenmart'),
		                "param_name" => "title",
		            ),
		            array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','greenmart'),
		                "param_name" => "subtitle",
		            ),
		            array(
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'greenmart' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose a categories if you want to show products of that them', 'greenmart' ),
					    'settings' => array(
					     	'multiple' => true,
					     	'unique_values' => true
					    ),
				   	),
		            array(
		                "type" => "textfield",
		                "heading" => esc_html__("Number items to show", 'greenmart'),
		                "param_name" => "number",
		                'std' => '1',
		                "description" => esc_html__("", 'greenmart')
		            ),
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                "value" => $columns
		            ),
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__("Layout",'greenmart'),
		                "param_name" => "layout_type",
		                "value" => array(
		                			esc_html__('Carousel', 'greenmart') => 'carousel', 
		                			esc_html__('Carousel Vertical', 'greenmart') => 'carousel-vertical', 
		                		 	esc_html__('Grid', 'greenmart') =>'grid',
		                		 ),
		                "admin_label" => true,
		                "description" => esc_html__("Select Columns.",'greenmart')
		            ),
					array(
		                "type" 		=> "dropdown",
		                "heading" 	=> esc_html__('Rows','greenmart'),
		                "param_name" => 'rows',
		                "value" 	=> $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-vertical',
									'carousel-thumbnail',
								),
						),
		            ),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-vertical',
									'carousel-thumbnail',
								),
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-vertical',
									'carousel-thumbnail',
								),
						),
					),

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),

		            array(
		                "type" => "textfield",
		                "heading" => esc_html__("Extra class name", 'greenmart'),
		                "param_name" => "el_class",
		                "description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.", 'greenmart')
		            ),
		        )
		    ));
			
			// Product Category
			vc_map( array(
			    "name" => esc_html__("Tbay Product Category",'greenmart'),
			    "base" => "tbay_productcategory",
			    "icon"        => "vc-icon-tbay",
			    "class" => "",
				"category" => esc_html__('Tbay Woocommerce','greenmart'),
			    'description'=> esc_html__( 'Show Products In Carousel, Grid, List, Special','greenmart' ), 
			    "params" => array(
			    	array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'greenmart'),
						"param_name" => "title",
						"value" =>''
					),
					 array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','greenmart'),
		                "param_name" => "subtitle",
		            ),
				   	array(
						"type" => "dropdown",
						"heading" => esc_html__("Category",'greenmart'),
						"param_name" => "category",
						"value" => $categories
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__("Layout Type",'greenmart'),
						"param_name" => "layout_type",
						"value" => $layouts
					),
					array(
						"type"        => "attach_image",
						"description" => esc_html__("Upload an image for categories", 'greenmart'),
						"param_name"  => "image_cat",
						"value"       => '',
						'heading'     => esc_html__('Image', 'greenmart' )
					),
					array(
						"type" => "textfield",
						"heading" => esc_html__("Number of products to show",'greenmart'),
						"param_name" => "number",
						"value" => '8'
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                'std'   => '4',
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
									'special',
									'grid',
								),
						),
		            ),

					array(
						"type" => "dropdown",
						"heading" => esc_html__('Rows','greenmart'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),
					
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),

					array(
						"type" => "textfield",
						"heading" => esc_html__("Extra class name",'greenmart'),
						"param_name" => "el_class",
						"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.",'greenmart')
					)
			   	)
			));
			
			
			// List Categories
			vc_map( array(
				"name"     => esc_html__("Tbay List Categories",'greenmart'),
				"base"     => "tbay_list_categories",
				"icon"        => "vc-icon-tbay",
				'description' => esc_html__( 'Show images and links of sub categories in block','greenmart' ),
				"class"    => "",
				"category" => esc_html__('Tbay Woocommerce','greenmart'),
				"params"   => array(
					array(
						"type" => "textfield",
						"class" => "",
						"heading" => esc_html__('Title', 'greenmart'),
						"param_name" => "title",
						"value" =>''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','greenmart'),
		                "param_name" => "subtitle",
		            ),
					array(
						"type"       => "textfield",
						"heading"    => esc_html__("Number of categories to show",'greenmart'),
						"param_name" => "number",
						"value"      => '6',
						"admin_label" => true,
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                "value" => $columns,
		                'std'   => '4',
		                "admin_label" => true,
		            ),
		            array(
						"type" => "dropdown",
						"heading" => esc_html__("Layout Type",'greenmart'),
						"param_name" => "layout_type",
						'std'       => 'grid',
		                "value" => array(
		                	esc_html__('Grid', 'greenmart') =>'grid',
                			esc_html__('Carousel', 'greenmart') => 'carousel', 
                		 ),
		                "admin_label" => true,
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__('Rows','greenmart'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
								),
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
								),
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
								),
						),
					),
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
						'dependency' 	=> array(
							'element' 	=> 'layout_type',
							'value' 	=> array (
								'carousel',
							),
						),
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),
					array(
						"type"        => "textfield",
						"heading"     => esc_html__("Extra class name",'greenmart'),
						"param_name"  => "el_class",
						"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.",'greenmart')
					)
			   	)
			));
			
			/**
			 * tbay_products
			 */
			vc_map( array(
			    "name" => esc_html__("Tbay Products",'greenmart'),
			    "base" => "tbay_products",
			    "icon"        => "vc-icon-tbay",
			    'description'=> esc_html__( 'Show products as bestseller, featured in block', 'greenmart' ),
			    "class" => "",
			   	"category" => esc_html__('Tbay Woocommerce','greenmart'),
			    "params" => array(
			    	array(
						"type" => "textfield",
						"heading" => esc_html__("Title",'greenmart'),
						"param_name" => "title",
						"admin_label" => true,
						"value" => ''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','greenmart'),
		                "param_name" => "subtitle",
		            ),
					array(
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'greenmart' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose categories if you want show products of them', 'greenmart' ),
					    'settings' => array(
					     	'multiple' => true,
					     	'unique_values' => true
					    ),
				   	),
			    	array(
						"type" => "dropdown",
						"heading" => esc_html__("Type",'greenmart'),
						"param_name" => "type",
						"value" => $types,
						"admin_label" => true,
						"description" => esc_html__("Select Columns.",'greenmart')
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                'std'   => '4',
		                "value" => $columns,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
									'special',
									'grid',
								),
						),
		            ),
					array(
						"type" => "textfield",
						"heading" => esc_html__("Number of products to show",'greenmart'),
						"param_name" => "number",
						"value" => '4'
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__("Layout Type",'greenmart'),
						"param_name" => "layout_type",
						"value" => $layouts
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__('Rows','greenmart'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special',
								),
						),
					),

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),

					array(
						"type" => "textfield",
						"heading" => esc_html__("Extra class name",'greenmart'),
						"param_name" => "el_class",
						"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.",'greenmart')
					)
			   	)
			));
			/**
			 * tbay_all_products
			 */
			vc_map( array(
			    "name" => esc_html__("Tbay Products Tabs",'greenmart'),
			    "base" => "tbay_productstabs",
			    "icon"        => "vc-icon-tbay",
			    'description'	=> esc_html__( 'Display BestSeller, TopRated ... Products In tabs', 'greenmart' ),
			    "class" => "",
			   	"category" => esc_html__('Tbay Woocommerce','greenmart'),
			    "params" => array(
			    	array(
						"type" => "textfield",
						"heading" => esc_html__("Title",'greenmart'),
						"param_name" => "title",
						"value" => ''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__('Sub Title','greenmart'),
		                "param_name" => "subtitle",
		            ),
		            array(
					    'type' => 'autocomplete',
					    'heading' => esc_html__( 'Categories', 'greenmart' ),
					    'value' => '',
					    'param_name' => 'categories',
					    "admin_label" => true,
					    'description' => esc_html__( 'Choose categories if you want show products of them', 'greenmart' ),
					    'settings' => array(
					     	'multiple' => true,
					     	'unique_values' => true
					    ),
				   	),
					array(
			            "type" => "sorted_list",
			            "heading" => esc_html__("Show Tab", 'greenmart'),
			            "param_name" => "producttabs",
			            "description" => esc_html__("Control teasers look. Enable blocks and place them in desired order.", 'greenmart'),
			            "value" => "recent_product",
			            "options" => $producttabs
			        ),
			        array(
						"type" => "dropdown",
						"heading" => esc_html__("Layout Type",'greenmart'),
						"param_name" => "layout_type",
						"value" => $layouts
					),		
					array(
						"type" => "dropdown",
						"heading" => esc_html__('Rows','greenmart'),
						"param_name" => 'rows',
						"value" => $rows,
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special'
							),
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special'
							),
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
						"value" 		=> array(
											esc_html__('No', 'greenmart') => 'no', 
											esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> array (
									'carousel',
									'carousel-special'
							),
						),
					),
					array(
						"type" => "textfield",
						"heading" => esc_html__("Number of products to show",'greenmart'),
						"param_name" => "number",
						"value" => '4'
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                "value" => $columns
		            ),

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),

					array(
						"type" => "textfield",
						"heading" => esc_html__("Extra class name",'greenmart'),
						"param_name" => "el_class",
						"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.",'greenmart')
					)
			   	)
			));
			// Categories tabs
			vc_map( array(
				'name' => esc_html__( 'Products Categories Tabs ', 'greenmart' ),
				'base' => 'tbay_categoriestabs',
				"icon"        => "vc-icon-tbay",
				'category' => esc_html__( 'Tbay Woocommerce', 'greenmart' ),
				'description' => esc_html__( 'Display  categories in Tabs', 'greenmart' ),
				'params' => array(
					array(
						"type" => "textfield",
						"heading" => esc_html__( 'Title','greenmart' ),
						"param_name" => "title",
						"value" => ''
					),
					array(
		                "type" => "textfield",
		                "class" => "",
		                "heading" => esc_html__( 'Sub Title','greenmart' ),
		                "param_name" => "subtitle",
		            ),
					
					array(
						'type' => 'param_group',
						'heading' => esc_html__( 'Tabs', 'greenmart' ),
						'param_name' => 'categoriestabs',
						'description' => '',
						'value' => '',
						'params' => array(
							array(
								"type" => "dropdown",
								"heading" => esc_html__( 'Category', 'greenmart' ),
								"param_name" => "category",
								"value" => $categories
							),
							array(
								'type' => 'attach_image',
								'heading' => esc_html__( 'Icon', 'greenmart' ),
								'param_name' => 'icon',
								'description' => esc_html__( 'You can choose a icon image or you can use icon font', 'greenmart' ),
							),
							array(
								'type' => 'textfield',
								'heading' => esc_html__( 'Icon Font', 'greenmart' ),
								'param_name' => 'icon_font',
								'description' => esc_html__( 'You can use font awesome icon. Eg: fa fa-home', 'greenmart' ),
							),
							
						)
					),
					array(
						"type" => "dropdown",
						"heading" => esc_html__("Type",'greenmart'),
						"param_name" => "type",
						"value" => $types,
						"admin_label" => true,
						"description" => esc_html__("Select Columns.",'greenmart')
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__( 'Number Products', 'greenmart' ),
						'value' => 12,
						'param_name' => 'number',
						'description' => esc_html__( 'Number products per page to show', 'greenmart' ),
					),
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Columns','greenmart'),
		                "param_name" => 'columns',
		                "value" => $columns
		            ),
					
					array(
		                "type" => "dropdown",
		                "heading" => esc_html__("Layout",'greenmart'),
		                "param_name" => "layout_type",
		                "value" => array(
		                			esc_html__('Carousel', 'greenmart') => 'carousel', 
		                		 	esc_html__('Grid', 'greenmart') =>'grid' ),
		                "admin_label" => true,
		                "description" => esc_html__("Select Columns.",'greenmart')
		            ),
					array(
					    "type" => "dropdown",
					    "heading" => esc_html__('Rows','greenmart'),
					    "param_name" => 'rows',
					    "value" => $rows,
					    'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> 'carousel',
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__( 'Title, Navigation, Pagination align:', 'greenmart' ),
						'param_name' => 'align',
						'value'       => array(
							'Center'  	=> 'center',
							'Left'   	=> 'left',
							'Right'   	=> 'right'
						),
						'std'       => 'center',
						'save_always' => true,
					),	

					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Navigation ", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Navigation ', 'greenmart' ),
						"param_name" 	=> "nav_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> 'carousel',
						),
					),					
					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Pagination", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden Pagination', 'greenmart' ),
						"param_name" 	=> "pagi_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
						'dependency' 	=> array(
								'element' 	=> 'layout_type',
								'value' 	=> 'carousel',
						),
					),


					array(
						"type" 			=> "dropdown",
						"heading" 		=> esc_html__( "Show config Responsive", 'greenmart' ),
						"description" 	=> esc_html__( 'Show/hidden config Responsive', 'greenmart' ),
						"param_name" 	=> "responsive_type",
		                "value" 		=> array(
		                					esc_html__('No', 'greenmart') => 'no', 
		                		 			esc_html__('Yes', 'greenmart') =>'yes' ),
					),
					array(
		                "type" 	  => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktop','greenmart'),
		                "param_name" => 'screen_desktop',
		                "value" => $columns,
		                'std'       => '4',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),					
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen desktopsmall','greenmart'),
		                "param_name" => 'screen_desktopsmall',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		           
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen tablet','greenmart'),
		                "param_name" => 'screen_tablet',
		                "value" => $columns,
		                'std'       => '3',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),		            
		            array(
		                "type" => "dropdown",
		                "heading" => esc_html__('Number of columns screen mobile','greenmart'),
		                "param_name" => 'screen_mobile',
		                "value" => $columns,
		                'std'       => '1',
		                'dependency' 	=> array(
								'element' 	=> 'responsive_type',
								'value' 	=> 'yes',
						),
		            ),

					
		            array(
						"type" => "textfield",
						"heading" => esc_html__("Extra class name",'greenmart'),
						"param_name" => "el_class",
						"description" => esc_html__("If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.",'greenmart')
					)
				)
			) );
		}
	}
	add_action( 'vc_after_set_mode', 'greenmart_tbay_load_woocommerce_element', 99 );

	class WPBakeryShortCode_Tbay_productstabs extends WPBakeryShortCode {

		public function getListQuery( $atts ) { 
			$this->atts  = $atts; 
			$list_query = array();
			$types = isset($this->atts['producttabs']) ? explode(',', $this->atts['producttabs']) : array();
			foreach ($types as $type) {
				$list_query[$type] = $this->getTabTitle($type);
			}
			return $list_query;
		}

		public function getTabTitle($type){ 
			switch ($type) {
				case 'recent_product':
					return array('title' => esc_html__('Latest Products', 'greenmart'), 'title_tab'=>esc_html__('Latest', 'greenmart'));
				case 'featured_product':
					return array('title' => esc_html__('Featured Products', 'greenmart'), 'title_tab'=>esc_html__('Featured', 'greenmart'));
				case 'top_rate':
					return array('title' => esc_html__('Top Rated Products', 'greenmart'), 'title_tab'=>esc_html__('Top Rated', 'greenmart'));
				case 'best_selling':
					return array('title' => esc_html__('BestSeller Products', 'greenmart'), 'title_tab'=>esc_html__('BestSeller', 'greenmart'));
				case 'on_sale':
					return array('title' => esc_html__('Special Products', 'greenmart'), 'title_tab'=>esc_html__('Special', 'greenmart'));
			}
		}
	}

	class WPBakeryShortCode_Tbay_product_countdown extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_productcategory extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_category_info extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_list_categories extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_products extends WPBakeryShortCode {}
	class WPBakeryShortCode_Tbay_categoriestabs extends WPBakeryShortCode {}
}