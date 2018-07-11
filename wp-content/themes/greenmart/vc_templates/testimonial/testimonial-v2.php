<?php
   $job = get_post_meta( get_the_ID(), 'tbay_testimonial_job', true );
?>
<div class="testimonials-body media v2">
   <div class="testimonials-content">
	   
	   <div class="description media-body">
		<i class="icofont icofont-quote-left"></i>
		<p><?php echo greenmart_tbay_substring( get_the_excerpt(), 20, ''); ?></p>
		</div>
		<div class="testimonials-profile"> 
		  <div class="testimonial-meta">
			 <span class="name-client"> <?php the_title(); ?></span>
			 <span class="job"><?php echo esc_html($job); ?></span>
		  </div> 
	   </div> 
   </div> 
</div>