<?php
   $job = get_post_meta( get_the_ID(), 'tbay_testimonial_job', true );
?>
<div class="testimonials-body media">
   <div class="testimonials-profile"> 
      <div class="wrapper-avatar">
         <div class=" testimonial-avatar">
         <?php the_post_thumbnail('widget') ?>
         </div>
      </div>
      <div class="testimonial-meta">
         <span class="name-client"> <?php the_title(); ?></span>
         <span class="job"><?php echo esc_html($job); ?></span>
      </div> 
   </div> 
   <div class="description media-body">
	<?php echo greenmart_tbay_substring( get_the_excerpt(), 25, ''); ?>
	</div>
</div>