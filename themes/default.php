<div class="ya_slideshow">
<div id="ei-slider-<?php echo $widget_id;?>" class="ei-slider"> 
	<ul class="ei-slider-large">
		<?php foreach ( $list as $i => $post ) { ?>
			<li>
				<?php echo get_the_post_thumbnail ($post->ID, 'large'); ?>
				<div class="ei-title">
					<h2><a href="<?php echo get_permalink( $post->ID ); ?>"><?php echo $post->post_title; ?></a></h2>
					<h3>
						<?php
							if ( preg_match('/<!--more(.*?)?-->/', $post->post_content, $matches) ) {
								$content = explode($matches[0], $post->post_content, 2);
								$content = $content[0];
							} else {
								$text = strip_shortcodes( $post->post_content );
								$text = apply_filters('the_content', $text);
								$text = str_replace(']]>', ']]&gt;', $text);
								$content = wp_trim_words($text, $length);
							}
							echo $content;
						?>
					</h3>
				</div>
			</li>
		<?php }?>
	</ul><!-- ei-slider-large -->
	<ul class="ei-slider-thumbs ei-slider-thumbs-theme1">
		<li class="ei-slider-element">Current Post</li>
		<?php foreach ( $list as $i => $post ) { ?>
			<li><a href="#"><?php echo $post->post_title; ?></a><?php echo get_the_post_thumbnail ($post->ID, 'medium'); ?></li>
		<?php }?>
							  
	</ul><!-- ei-slider-thumbs -->
</div><!-- ei-slider -->
<script type="text/javascript">
    
     jQuery(document).ready( function() {
       jQuery(function() {
            jQuery('#ei-slider-<?php echo $widget_id;?>').eislideshow({                  
                    animation : '<?php echo $instance['animation'];?>',
                    autoplay : parseInt(<?php echo $instance['autoplay'];?>),
                    slideshow_interval : parseInt(<?php echo $instance['slideshow_interval'];?>),
                    easing : '<?php echo $instance['easing'];?>',
                    titleeasing : '<?php echo $instance['titleeasing'];?>',
                    speed : parseInt(<?php echo $instance['speed'];?>)
                    
            });                                      
        });
       }, false);
	    
    
 
</script>

</div>