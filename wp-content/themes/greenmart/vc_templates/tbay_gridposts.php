<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$category = get_category_by_slug( $category );

$args = array(
    'posts_per_page' =>     $number,
    'post_status'    =>    'publish',
    'orderby'        =>     'name',
    'order'          =>     'ASC',
    'taxonomy'       =>    'category',
);

if($category) {
	$cat_id = $category->term_id;
	$args['cat'] = $cat_id;
}

$loop = new WP_Query($args);

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

$rows_count = isset($rows) ? $rows : 1;
set_query_var( 'thumbsize', $thumbsize );

?>

<div class="widget widget-blog <?php echo esc_attr($align); ?> <?php echo esc_attr($layout_type); ?> <?php echo esc_attr($el_class); ?>">
    <?php if ($title!=''): ?>
        <h3 class="widget-title">
            <span><?php echo esc_html( $title ); ?></span>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>
    <div class="widget-content"> 
        <?php $post_item = '_single'; ?>
        <?php if ( $layout_type == 'carousel' ): ?> 

            <div class="owl-carousel posts" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-extrasmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo (isset($pagi_type) && $pagi_type== 'yes' ) ? 'true' : 'false'; ?>" data-nav="<?php echo ( isset($nav_type) && $nav_type =='yes' ) ? 'true' : 'false'; ?>">
                <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>

                    <?php if($count%$rows_count == 0){ ?>
                        <div class="item">
                    <?php } ?>

                        <?php 
                            get_template_part( 'vc_templates/post/_single_carousel'); 

                        ?>

                <?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
                    </div>
                <?php }
                $count++; ?>   

                <?php endwhile; ?>
            </div>

        <?php elseif ( $layout_type == 'carousel-vertical' ): ?>

            <div class="owl-carousel posts" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-extrasmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo (isset($pagi_type) && $pagi_type== 'yes' ) ? 'true' : 'false'; ?>" data-nav="<?php echo ( isset($nav_type) && $nav_type =='yes' ) ? 'true' : 'false'; ?>">
                <?php $count = 0; while ( $loop->have_posts() ): $loop->the_post(); global $product; ?>

                    <?php if($count%$rows_count == 0){ ?>
                        <div class="item">
                    <?php } ?>

                        <?php get_template_part( 'vc_templates/post/_single_carousel_vertical'); ?>

                <?php if($count%$rows_count == $rows_count-1 || $count==$loop->post_count -1){ ?>
                    </div>
                <?php }
                $count++; ?>   

                <?php endwhile; ?>
            </div>

        <?php elseif ( $layout_type == 'grid' ): ?>

            <div class="layout-blog">
                <div class="row">
                    <?php $count = 0; while ( $loop->have_posts() ) : $loop->the_post(); ?>

					
						<?php 
						
						switch ($columns) {
							case 1:
								$columns_class = 'col-md-12 col-lg-12';		
								break;	
							case 2:
								$columns_class = 'col-md-6 col-lg-6';
								break;								
							case 3:
								$columns_class = 'col-md-3 col-lg-4';
								break;		
							case 4:
								$columns_class = 'col-md-4 col-lg-3';
								break;								
							case 6:
								$columns_class = 'col-md-2 col-lg-2';
								break;
							default:
								$columns_class = 'col-md-5 col-lg-3';
						}
						
						?>
					
                    	<?php  
                    		$_class = '';
                    		if( $count%$columns == 0 ) {
                    			$_class = ' first';
                    		}
                    	?>

                        <div class="col-xs-6 <?php echo esc_attr($columns_class); ?> <?php echo esc_attr($_class); ?>">
                            <?php get_template_part( 'vc_templates/post/_single' ); ?>
                        </div>

                        <?php $count++; ?>
                    <?php endwhile; ?>
                </div>
            </div>

        <?php else: ?>

                <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                        <?php get_template_part( 'vc_templates/post/_single_list' ); ?>
                <?php endwhile; ?>
            
        <?php endif; ?>
    </div>

</div>
<?php wp_reset_postdata(); ?>