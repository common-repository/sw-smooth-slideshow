<?php
/**
 * Plugin Name: SW Slideshow Widget
 * Plugin URI: http://smartaddons.com
 * Description: A widget that serves as an slideshow for developing more advanced widgets.
 * Version: 1.0
 * Author: smartaddons.com
 * Author URI: http://smartaddons.com
 *
 * This Widget help you to show images of content as a beauty reponsive slideshow
 */

add_action( 'widgets_init', 'sw_slideshow_load_widgets' );

add_action('init', load_index_page_script);
/**
 * Register our widget.
 * 'Slideshow_Widget' is the widget class used below.
 */
function sw_slideshow_load_widgets() {
	register_widget( 'sw_slideshow_Widget' );
}

/**
 * Load script (css, js).
 * 
 */
 
function load_index_page_script(){
        if (!is_admin() && !defined('SW_SLIDESHOW')) {      
            define('SW_SLIDESHOW', 'ASSETS SW SLIDESHOW');
            wp_register_style( 'slide-style2', plugins_url('css/style.css', __FILE__) );
            wp_enqueue_style( 'slide-style2' );            
            wp_register_style( 'google-font', 'http://fonts.googleapis.com/css?family=Oleo+Script+Swash+Caps' );
            wp_enqueue_style( 'google-font' );
    
            wp_register_script( 'slide-cycle', plugins_url( '/js/cycle.js', __FILE__ ) );
            wp_enqueue_script( 'slide-cycle' );      
            wp_register_script( 'eislideshow', plugins_url( '/js/jquery.eislideshow.js', __FILE__ ) );
            wp_enqueue_script( 'eislideshow' );
            wp_register_script( 'easing', plugins_url( '/js/jquery.easing.1.3.js', __FILE__ ) );
            wp_enqueue_script( 'easing' );
             
        }
    }

/**
 * ya slideshow Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, display, and update.  Nice!
 */
class sw_slideshow_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function sw_slideshow_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'slideshow', 'description' => __('A beauty widget slideshow responsive', 'slideshow') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'sw-slideshow-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'sw-slideshow-widget', __('Sw Smooth slideshow', 'sw-slideshow'), $widget_ops, $control_ops );
	}

	/**
	 * Display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract($args);
		
		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
		if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		
		if (!isset($instance['category'])){
			$instance['category'] = 0;
		}
		
		extract($instance);
		
        /*$args = array(
            'posts_per_page'  => 5,
            'numberposts'     => 5,
            'offset'          => 0,
            'category'        => '',
            'orderby'         => 'post_date',
            'order'           => 'DESC',
            'include'         => '',
            'exclude'         => '',
            'meta_key'        => '',
            'meta_value'      => '',
            'post_type'       => 'post',
            'post_mime_type'  => '',
            'post_parent'     => '',
            'post_status'     => 'publish',
            'suppress_filters' => true );  */
        
		$default = array(
			'category' => $category,
			'orderby' => $orderby,
			'numberposts' => $numberposts,
			'length' => $length,
            'meta_key'    => '_thumbnail_id'
		);
		
		$list = get_posts($default);

		if ( !array_key_exists('widget_template', $instance) ){
			$instance['widget_template'] = 'default';
		}
		
		if ( $tpl = $this->getTemplatePath( $instance['widget_template'] ) ){ 
			$link_img = plugins_url('images/', __FILE__);
			$widget_id = $args['widget_id'];		
			include $tpl;
		}
				
		/* After widget (defined by themes). */
		echo $after_widget;
	}    

	protected function getTemplatePath($tpl='default', $type=''){
		$file = '/'.$tpl.$type.'.php';
		$dir =realpath(dirname(__FILE__)).'/themes';
		
		if ( file_exists( $dir.$file ) ){
			return $dir.$file;
		}
		
		return $tpl=='default' ? false : $this->getTemplatePath('default', $type);
	}
	
	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// strip tag on text field
		$instance['title'] = strip_tags( $new_instance['title'] );

		// int or array
		if ( array_key_exists('category', $new_instance) ){
			if ( is_array($new_instance['category']) ){
				$instance['category'] = array_map( 'intval', $new_instance['category'] );
			} else {
				$instance['category'] = intval($new_instance['category']);
			}
		}

		if ( array_key_exists('orderby', $new_instance) ){
			$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		}

		if ( array_key_exists('order', $new_instance) ){
			$instance['order'] = strip_tags( $new_instance['order'] );
		}

		if ( array_key_exists('numberposts', $new_instance) ){
			$instance['numberposts'] = intval( $new_instance['numberposts'] );
		}

		if ( array_key_exists('length', $new_instance) ){
			$instance['length'] = intval( $new_instance['length'] );
		}

        $instance['widget_template'] = strip_tags( $new_instance['widget_template'] );
        
        $instance['animation'] = strip_tags( $new_instance['animation'] );
        $instance['autoplay'] = strip_tags( $new_instance['autoplay'] );
        $instance['slideshow_interval'] = strip_tags( $new_instance['slideshow_interval'] );
        $instance['easing'] = strip_tags( $new_instance['easing'] );
        $instance['titleeasing'] = strip_tags( $new_instance['titleeasing'] );
		$instance['speed'] = strip_tags( $new_instance['speed'] );        
        
		return $instance;
	}

	function category_select( $field_name, $opts = array(), $field_value = null ){
		$default_options = array(
				'multiple' => false,
				'disabled' => false,
				'size' => 5,
				'class' => 'widefat',
				'required' => false,
				'autofocus' => false,
				'form' => false,
		);
		$opts = wp_parse_args($opts, $default_options);
	
		if ( (is_string($opts['multiple']) && strtolower($opts['multiple'])=='multiple') || (is_bool($opts['multiple']) && $opts['multiple']) ){
			$opts['multiple'] = 'multiple';
			if ( !is_numeric($opts['size']) ){
				if ( intval($opts['size']) ){
					$opts['size'] = intval($opts['size']);
				} else {
					$opts['size'] = 5;
				}
			}
		} else {
			// is not multiple
			unset($opts['multiple']);
			unset($opts['size']);
			if (is_array($field_value)){
				$field_value = array_shift($field_value);
			}
			if (array_key_exists('allow_select_all', $opts) && $opts['allow_select_all']){
				unset($opts['allow_select_all']);
				$allow_select_all = '<option value="0">All Categories</option>';
			}
		}
	
		if ( (is_string($opts['disabled']) && strtolower($opts['disabled'])=='disabled') || is_bool($opts['disabled']) && $opts['disabled'] ){
			$opts['disabled'] = 'disabled';
		} else {
			unset($opts['disabled']);
		}
	
		if ( (is_string($opts['required']) && strtolower($opts['required'])=='required') || (is_bool($opts['required']) && $opts['required']) ){
			$opts['required'] = 'required';
		} else {
			unset($opts['required']);
		}
	
		if ( !is_string($opts['form']) ) unset($opts['form']);
	
		if ( !isset($opts['autofocus']) || !$opts['autofocus'] ) unset($opts['autofocus']);
	
		$opts['id'] = $this->get_field_id($field_name);
	
		$opts['name'] = $this->get_field_name($field_name);
		if ( isset($opts['multiple']) ){
			$opts['name'] .= '[]';
		}
		$select_attributes = '';
		foreach ( $opts as $an => $av){
			$select_attributes .= "{$an}=\"{$av}\" ";
		}
		
		$categories = get_categories();
		// if (!$templates) return '';
		$all_category_ids = array();
		foreach ($categories as $cat) $all_category_ids[] = (int)$cat->cat_ID;
		
		$is_valid_field_value = is_numeric($field_value) && in_array($field_value, $all_category_ids);
		if (!$is_valid_field_value && is_array($field_value)){
			$intersect_values = array_intersect($field_value, $all_category_ids);
			$is_valid_field_value = count($intersect_values) > 0;
		}
		if (!$is_valid_field_value){
			$field_value = '0';
		}
	
		$select_html = '<select ' . $select_attributes . '>';
		if (isset($allow_select_all)) $select_html .= $allow_select_all;
		foreach ($categories as $cat){
			$select_html .= '<option value="' . $cat->cat_ID . '"';
			if ($cat->cat_ID == $field_value || (is_array($field_value)&&in_array($cat->cat_ID, $field_value))){ $select_html .= ' selected="selected"';}
			$select_html .=  '>'.$cat->name.'</option>';
		}
		$select_html .= '</select>';
		return $select_html;
	}
	

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array();
		$instance = wp_parse_args( (array) $instance, $defaults ); 		
		         
		$categoryid = isset( $instance['category'] )    ? $instance['category'] : 0;
		$orderby    = isset( $instance['orderby'] )     ? strip_tags($instance['orderby']) : 'ID';
		$order      = isset( $instance['order'] )       ? strip_tags($instance['order']) : 'ASC';
		$number     = isset( $instance['numberposts'] ) ? intval($instance['numberposts']) : 5;
        $length     = isset( $instance['length'] )      ? intval($instance['length']) : 25;
		$widget_template     = isset( $instance['widget_template'] ) ? strip_tags($instance['widget_template']) : 'default';
                    
        $animation = isset( $instance['animation'] )    ? strip_tags($instance['animation']) : 'Sides'; 
        $autoplay = isset( $instance['autoplay'] )    ? $instance['autoplay'] : 1; 
        $slideshow_interval = isset( $instance['slideshow_interval'] )    ? $instance['slideshow_interval'] : 3000; 
        $easing = isset( $instance['easing'] )    ? strip_tags($instance['easing']) : ''; 
        $titleeasing = isset( $instance['titleeasing'] )    ? strip_tags($instance['titleeasing']) : ''; 
        $speed = isset( $instance['speed'] )    ? $instance['speed'] : 800; 
                 
		?>
        </p> 
          <div style="background: Blue; color: white; font-weight: bold; text-align:center; padding: 3px"> * Data Config * </div>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category ID', 'smartaddons')?></label>
			<br />
			<?php echo $this->category_select('category', array('allow_select_all' => true), $categoryid); ?>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e('Orderby', 'smartaddons')?></label>
			<br />
			<?php $allowed_keys = array('name' => 'Name', 'author' => 'Author', 'date' => 'Date', 'title' => 'Title', 'modified' => 'Modified', 'parent' => 'Parent', 'ID' => 'ID', 'rand' =>'Rand', 'comment_count' => 'Comment Count'); ?>
			<select class="widefat"
				id="<?php echo $this->get_field_id('orderby'); ?>"
				name="<?php echo $this->get_field_name('orderby'); ?>">
				<?php
				$option ='';
				foreach ($allowed_keys as $value => $key) :
					$option .= '<option value="' . $value . '" ';
					if ($value == $orderby){
						$option .= 'selected="selected"';
					}
					$option .=  '>'.$key.'</option>';
				endforeach;
				echo $option;
				?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order', 'smartaddons')?></label>
			<br />
			<select class="widefat"
				id="<?php echo $this->get_field_id('order'); ?>"
				name="<?php echo $this->get_field_name('order'); ?>">
				<option value="DESC" <?php if ($order=='DESC'){?> selected="selected"
				<?php } ?>>
					<?php _e('Descending', 'smartaddons')?>
				</option>
				<option value="ASC" <?php if ($order=='ASC'){?> selected="selected"
				<?php } ?>>
					<?php _e('Ascending', 'smartaddons')?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('numberposts'); ?>"><?php _e('Number of Posts', 'smartaddons')?></label>
			<br />
			<input class="widefat"
				id="<?php echo $this->get_field_id('numberposts'); ?>"
				name="<?php echo $this->get_field_name('numberposts'); ?>" type="text"
				value="<?php echo esc_attr($number); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('length'); ?>"><?php _e('Excerpt length (in words): ', 'smartaddons')?></label>
			<br />
			<input class="widefat"
				id="<?php echo $this->get_field_id('length'); ?>"
				name="<?php echo $this->get_field_name('length'); ?>" type="text"
				value="<?php echo esc_attr($length); ?>" />
		</p>  
		<p>
			<label for="<?php echo $this->get_field_id('widget_template'); ?>"><?php _e("Template", 'smartaddons')?></label>
			<br/>
			
			<select class="widefat"
				id="<?php echo $this->get_field_id('widget_template'); ?>"
				name="<?php echo $this->get_field_name('widget_template'); ?>">
				<option value="default" <?php if ($widget_template=='default'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme1', 'smartaddons')?>
				</option>
				<option value="theme2" <?php if ($widget_template=='theme2'){?> selected="selected"
				<?php } ?>>
					<?php _e('Theme2', 'smartaddons')?>
				</option>
			</select>
		</p> 
          <div style="background: Blue; color: white; font-weight: bold; text-align:center; padding: 3px"> * Effects Config * </div>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('animation'); ?>"><?php _e("Animation", 'smartaddons')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('animation'); ?>"
                name="<?php echo $this->get_field_name('animation'); ?>">
                <option value="sides" <?php if ($animation=='sides'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Sides', 'smartaddons')?>
                </option>
                <option value="center" <?php if ($animation=='center'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Center', 'smartaddons')?>
                </option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e("autoplay", 'smartaddons')?></label>
            <br/>
            <select class="widefat"
                id="<?php echo $this->get_field_id('autoplay'); ?>"
                name="<?php echo $this->get_field_name('autoplay'); ?>">
                <option value="1" <?php if ($autoplay=='1'){?> selected="selected"
                <?php } ?>>
                    <?php _e('Yes', 'smartaddons')?>
                </option>
                <option value="0" <?php if ($autoplay=='0'){?> selected="selected"
                <?php } ?>>
                    <?php _e('No', 'smartaddons')?>
                </option>
            </select>
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('slideshow_interval'); ?>"><?php _e("Slideshow interval", 'smartaddons')?></label>
            <br/>
            <input class="widefat"
                id="<?php echo $this->get_field_id('slideshow_interval'); ?>"
                name="<?php echo $this->get_field_name('slideshow_interval'); ?>" type="text"
                value="<?php echo esc_attr($slideshow_interval); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('speed'); ?>"><?php _e("Speed of effect", 'smartaddons')?></label>
            <br/>
            <input class="widefat"
                id="<?php echo $this->get_field_id('speed'); ?>"
                name="<?php echo $this->get_field_name('speed'); ?>" type="text"
                value="<?php echo esc_attr($speed); ?>" />
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('easing'); ?>"><?php _e("Select easing type for images", 'smartaddons')?></label>
            <br/>
            <?php 
                $arr_easing = array(
                'easeInBounce'=>'easeInBounce',
                'easeOutBounce'=>'easeOutBounce',
                'easeInOutBack'=>'easeInOutBack',
                'easeOutBack'=>'easeOutBack',
                'easeInOutElastic'=>'easeInOutElastic',
                'easeOutElastic'=>'easeOutElastic',
                'easeInElastic'=>'easeInElastic',
                'easeInOutCirc'=>'easeInOutCirc',
                'easeOutCirc'=>'easeOutCirc',
                'swing'=>'swing',
                'easeInQuad'=>'easeInQuad',
                'easeInOutQuint'=>'easeInOutQuint'
                );  
              
            ?>
             <select class="widefat"
                id="<?php echo $this->get_field_id('easing'); ?>"
                name="<?php echo $this->get_field_name('easing'); ?>">
                <?php foreach($arr_easing as $eas): ?>
                      <option value="<?php echo $eas;?>" <?php if ($easing==$eas){?> selected="selected" <?php } ?>>
                        <?php _e($eas , 'smartaddons') ?>
                    </option>
                <?php endforeach;?>
                               
            </select>
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('titleeasing'); ?>"><?php _e("Select easing type for title", 'smartaddons')?></label>
            <br/>          
             <select class="widefat"
                id="<?php echo $this->get_field_id('titleeasing'); ?>"
                name="<?php echo $this->get_field_name('titleeasing'); ?>">
                <?php foreach($arr_easing as $eas): ?>
                      <option value="<?php echo $eas;?>" <?php if ($titleeasing==$eas){?> selected="selected" <?php } ?>>
                        <?php _e($eas , 'smartaddons') ?>
                    </option>
                <?php endforeach;?>
                               
            </select>
        </p>
        
            
	<?php
	}
	
}


?>