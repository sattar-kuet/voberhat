<?php
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$rows_count = $rows;

if( isset($responsive_type) && $responsive_type == 'yes') {
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
<div class="widget instagram-widget <?php echo esc_attr($align); ?> <?php echo esc_attr($el_class); ?> <?php echo isset($style) ? esc_attr($style) : ''; ?>">

    <?php if( (isset($subtitle) && $subtitle) || (isset($title) && $title)  ): ?>
        <h3 class="widget-title">
            <?php if ( isset($title) && $title ): ?>
                <span><?php echo esc_html( $title ); ?></span>
            <?php endif; ?>
            <?php if ( isset($subtitle) && $subtitle ): ?>
                <span class="subtitle"><?php echo esc_html($subtitle); ?></span>
            <?php endif; ?>
        </h3>
    <?php endif; ?>

    <?php 


    if ( !empty($username) ) {
        $media_array = tbay_framework_scrape_instagram( $username );

        if ( is_wp_error( $media_array ) ) {

            echo wp_kses_post( $media_array->get_error_message() );

        } else {

            // filter for images only?
            if ( $images_only = apply_filters( 'tbay_framework_instagram_widget_images_only', FALSE ) ) {
                $media_array = array_filter( $media_array, 'tbay_framework_images_only' );
            }

            // slice list down to required number
            $media_array = array_slice( $media_array, 0, $number );

            ?>
 

            <div class="owl-carousel slick-instagram" data-items="<?php echo esc_attr($columns); ?>" data-large="<?php echo esc_attr($screen_desktop);?>" data-medium="<?php echo esc_attr($screen_desktopsmall); ?>" data-smallmedium="<?php echo esc_attr($screen_tablet); ?>" data-extrasmall="<?php echo esc_attr($screen_mobile); ?>"  data-verysmall="<?php echo esc_attr($screen_mobile); ?>" data-carousel="owl" data-pagination="<?php echo ($pagi_type == 'yes') ? 'true' : 'false'; ?>" data-loop="true" data-nav="<?php echo ($nav_type == 'yes') ? 'true' : 'false'; ?>">
                <?php 
                    $count = 0;  
                    $countall = count($media_array);
                    foreach ( $media_array as $item ) { ?>

                    <?php if($count%$rows_count == 0){ ?>
                        <div class="item">
                    <?php } ?>

                        <div class="instagram-item-inner">
                            <a href="<?php echo esc_url( $item['link'] ); ?>" target="<?php echo esc_attr( $target ); ?>">

                                <span class="group-items"> 
                                        <span class="likes"><i class="icon-heart"></i><?php echo esc_html($item['likes']);?></span>

                                        <span class="comments"><i class="icon-bubbles icons"></i><?php echo esc_html($item['comments']);?></span>
                                </span>
                                <?php
                                    $time  = $item['time'];
                                ?>
                                <?php if( isset($time) && $time ) : ?>
                                    <span class="time elapsed-time"><?php  echo tbay_framework_time_ago($time,1); ?></span>
                                <?php endif; ?>

                                <img src="<?php echo esc_url( $item[$size] ); ?>" alt="<?php echo esc_attr( $item['description'] ); ?>" title="<?php echo esc_attr( $item['description'] ); ?>" />
                            </a>
                        </div>

                    <?php if($count%$rows_count == $rows_count-1 || $count==$countall -1){ ?>
                        </div>
                    <?php }
                    $count++; ?>

                <?php } ?>
            </div>
        <?php
        }
    }

    ?>


</div>