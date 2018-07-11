<?php $thumbsize = isset($thumbsize) ? $thumbsize : 'medium';?>
<?php
  $post_category = "";
  $categories = get_the_category();
  $separator = ' | ';
  $output = ''; 
  if($categories){
    foreach($categories as $category) {
      $output .= '<a href="'.esc_url( get_category_link( $category->term_id ) ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s", 'greenmart' ), $category->name ) ) . '">'.$category->cat_name.'</a>'.$separator;
    }
  $post_category = trim($output, $separator);
  }      
?>
<div class="post-grid">
    <article class="post">   
        <figure class="entry-thumb <?php echo  (!has_post_thumbnail() ? 'no-thumb' : ''); ?>">
            <span class="comments-link"><i class="icofont icofont-speech-comments"></i> <?php comments_popup_link( '0', '1', esc_html__( '%', 'greenmart' ) ); ?></span>
            <a href="<?php the_permalink(); ?>" title="" class="entry-image">
                <?php
                    if ( defined('GREENMART_VISUALCOMPOSER_ACTIVED') && GREENMART_VISUALCOMPOSER_ACTIVED ) {
                        $post_thumbnail = wpb_getImageBySize( array( 'post_id' => get_the_ID(), 'thumb_size' => $thumbsize ) );
                        echo $post_thumbnail['thumbnail'];
                    } else {
                        the_post_thumbnail();
                    }
                ?>
            </a>
            <div class="entry-meta">
                <?php
                    if (get_the_title()) {
                    ?>
                        <h4 class="entry-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h4>
                    <?php
                }
                ?>
                 
            </div>
            
        </figure>
        <div class="entry-content">
            
           <div class="entry">
                 <div class="meta-info">
                    <span class="author"><?php echo get_avatar(get_the_author_meta( 'ID' ), 50); ?> <?php the_author_posts_link(); ?></span>
                    <span class="entry-date"><i class="icofont icofont-calendar" aria-hidden="true"></i><?php echo greenmart_time_link(); ?></span>
                    <?php if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) : ?>
                    
                    <?php endif; ?>
                    
                </div>
                
                <div>
                    <?php
                        if (! has_excerpt()) {
                            echo "";
                        } else {
                            ?>
                                <div class="entry-description"><?php echo greenmart_tbay_substring( get_the_excerpt(), 20, '[...]' ); ?></div>
                            <?php
                        }
                    ?>
                </div>
                <div class="readmore">
                    <a href="<?php the_permalink(); ?>" title="<?php esc_html_e( 'Read More', 'greenmart' ); ?>"><i class="icofont icofont-long-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </article>
    
</div>