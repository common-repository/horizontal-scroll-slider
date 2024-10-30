<?php
/*
Plugin Name: Horizontal Scroll Slider
Plugin URL: http://beautiful-module.com/demo/horizontal-scroll-slider/
Description: A simple Responsive Horizontal Scroll Slider
Version: 1.0
Author: Module Express
Author URI: http://beautiful-module.com
Contributors: Module Express
*/
/*
 * Register CPT sp_horizontal.scroll
 *
 */
if(!class_exists('Horizontal_Scroll_Slider')) {
	class Horizontal_Scroll_Slider {

		function __construct() {
		    if(!function_exists('add_shortcode')) {
		            return;
		    }
			add_action ( 'init' , array( $this , 'hss_responsive_gallery_setup_post_types' ));

			/* Include style and script */
			add_action ( 'wp_enqueue_scripts' , array( $this , 'hss_register_style_script' ));
			
			/* Register Taxonomy */
			add_action ( 'init' , array( $this , 'hss_responsive_gallery_taxonomies' ));
			add_action ( 'add_meta_boxes' , array( $this , 'hss_rsris_add_meta_box_gallery' ));
			add_action ( 'save_post' , array( $this , 'hss_rsris_save_meta_box_data_gallery' ));
			register_activation_hook( __FILE__, 'hss_responsive_gallery_rewrite_flush' );


			// Manage Category Shortcode Columns
			add_filter ( 'manage_responsive_hss_slider-category_custom_column' , array( $this , 'hss_responsive_gallery_category_columns' ), 10, 3);
			add_filter ( 'manage_edit-responsive_hss_slider-category_columns' , array( $this , 'hss_responsive_gallery_category_manage_columns' ));
			require_once( 'hss_gallery_admin_settings_center.php' );
		    add_shortcode ( 'sp_horizontal.scroll' , array( $this , 'hss_responsivegallery_shortcode' ));
		}


		function hss_responsive_gallery_setup_post_types() {

			$responsive_gallery_labels =  apply_filters( 'sp_horizontal_scroll_labels', array(
				'name'                => 'Horizontal Scroll Slider',
				'singular_name'       => 'Horizontal Scroll Slider',
				'add_new'             => __('Add New', 'sp_horizontal_scroll'),
				'add_new_item'        => __('Add New Image', 'sp_horizontal_scroll'),
				'edit_item'           => __('Edit Image', 'sp_horizontal_scroll'),
				'new_item'            => __('New Image', 'sp_horizontal_scroll'),
				'all_items'           => __('All Images', 'sp_horizontal_scroll'),
				'view_item'           => __('View Image', 'sp_horizontal_scroll'),
				'search_items'        => __('Search Image', 'sp_horizontal_scroll'),
				'not_found'           => __('No Image found', 'sp_horizontal_scroll'),
				'not_found_in_trash'  => __('No Image found in Trash', 'sp_horizontal_scroll'),
				'parent_item_colon'   => '',
				'menu_name'           => __('Horizontal Scroll Slider', 'sp_horizontal_scroll'),
				'exclude_from_search' => true
			) );


			$responsiveslider_args = array(
				'labels' 			=> $responsive_gallery_labels,
				'public' 			=> true,
				'publicly_queryable'		=> true,
				'show_ui' 			=> true,
				'show_in_menu' 		=> true,
				'query_var' 		=> true,
				'capability_type' 	=> 'post',
				'has_archive' 		=> true,
				'hierarchical' 		=> false,
				'menu_icon'   => 'dashicons-format-gallery',
				'supports' => array('title','editor','thumbnail')
				
			);
			register_post_type( 'sp_horizontal_scroll', apply_filters( 'sp_faq_post_type_args', $responsiveslider_args ) );

		}
		
		function hss_register_style_script() {
		    //wp_enqueue_style( 'hss_responsiveimgslider',  plugin_dir_url( __FILE__ ). 'css/responsiveimgslider.css' );
			wp_enqueue_style( 'hss_main',  plugin_dir_url( __FILE__ ). 'css/horizontal-scroll-slider.css' );
			
			/*   REGISTER ALL CSS FOR SITE */
						
			wp_enqueue_style( 'hss_owl.carousel',  plugin_dir_url( __FILE__ ). 'css/owl.carousel.css' );
			wp_enqueue_style( 'hss_owl.theme',  plugin_dir_url( __FILE__ ). 'css/owl.theme.css' );

			/*   REGISTER ALL JS FOR SITE */	
			wp_enqueue_script( 'hss_owl.carousel', plugin_dir_url( __FILE__ ) . 'js/owl.carousel.js', array( 'jquery' ));
		}
		
		
		function hss_responsive_gallery_taxonomies() {
		    $labels = array(
		        'name'              => _x( 'Category', 'taxonomy general name' ),
		        'singular_name'     => _x( 'Category', 'taxonomy singular name' ),
		        'search_items'      => __( 'Search Category' ),
		        'all_items'         => __( 'All Category' ),
		        'parent_item'       => __( 'Parent Category' ),
		        'parent_item_colon' => __( 'Parent Category:' ),
		        'edit_item'         => __( 'Edit Category' ),
		        'update_item'       => __( 'Update Category' ),
		        'add_new_item'      => __( 'Add New Category' ),
		        'new_item_name'     => __( 'New Category Name' ),
		        'menu_name'         => __( 'Gallery Category' ),
		    );

		    $args = array(
		        'hierarchical'      => true,
		        'labels'            => $labels,
		        'show_ui'           => true,
		        'show_admin_column' => true,
		        'query_var'         => true,
		        'rewrite'           => array( 'slug' => 'responsive_hss_slider-category' ),
		    );

		    register_taxonomy( 'responsive_hss_slider-category', array( 'sp_horizontal_scroll' ), $args );
		}

		function hss_responsive_gallery_rewrite_flush() {  
				hss_responsive_gallery_setup_post_types();
		    flush_rewrite_rules();
		}


		function hss_responsive_gallery_category_manage_columns($theme_columns) {
		    $new_columns = array(
		            'cb' => '<input type="checkbox" />',
		            'name' => __('Name'),
		            'gallery_hss_shortcode' => __( 'Gallery Category Shortcode', 'hss_slick_slider' ),
		            'slug' => __('Slug'),
		            'posts' => __('Posts')
					);

		    return $new_columns;
		}

		function hss_responsive_gallery_category_columns($out, $column_name, $theme_id) {
		    $theme = get_term($theme_id, 'responsive_hss_slider-category');

		    switch ($column_name) {      
		        case 'title':
		            echo get_the_title();
		        break;
		        case 'gallery_hss_shortcode':
					echo '[sp_horizontal.scroll cat_id="' . $theme_id. '"]';			  	  

		        break;
		        default:
		            break;
		    }
		    return $out;   

		}

		/* Custom meta box for slider link */
		function hss_rsris_add_meta_box_gallery() {
			add_meta_box('custom-metabox',__( 'LINK URL', 'link_textdomain' ),array( $this , 'hss_rsris_gallery_box_callback' ),'sp_horizontal_scroll');			
		}
		
		function hss_rsris_gallery_box_callback( $post ) {
			wp_nonce_field( 'hss_rsris_save_meta_box_data_gallery', 'rsris_meta_box_nonce' );
			$value = get_post_meta( $post->ID, 'rsris_hss_link', true );
			echo '<input type="url" id="rsris_hss_link" name="rsris_hss_link" value="' . esc_attr( $value ) . '" size="80" /><br />';
			echo 'ie http://www.google.com';
		}
		
		function hss_truncate($string, $length = 100, $append = "&hellip;")
		{
			$string = trim($string);
			if (strlen($string) > $length)
			{
				$string = wordwrap($string, $length);
				$string = explode("\n", $string, 2);
				$string = $string[0] . $append;
			}

			return $string;
		}
			
		function hss_rsris_save_meta_box_data_gallery( $post_id ) {
			if ( ! isset( $_POST['rsris_meta_box_nonce'] ) ) {
				return;
			}
			if ( ! wp_verify_nonce( $_POST['rsris_meta_box_nonce'], 'hss_rsris_save_meta_box_data_gallery' ) ) {
				return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
			if ( isset( $_POST['post_type'] ) && 'sp_horizontal_scroll' == $_POST['post_type'] ) {

				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return;
				}
			} else {

				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
			}
			if ( ! isset( $_POST['rsris_hss_link'] ) ) {
				return;
			}
			$link_data = sanitize_text_field( $_POST['rsris_hss_link'] );
			update_post_meta( $post_id, 'rsris_hss_link', $link_data );
		}
		
		/*
		 * Add [sp_horizontal.scroll] shortcode
		 *
		 */
		function hss_responsivegallery_shortcode( $atts, $content = null ) {
			
			extract(shortcode_atts(array(
				"limit"  => '',
				"cat_id" => '',
				"width" => '',
				"items" => '',
				"autoplay_interval" => ''
			), $atts));
			
			if( $limit ) { 
				$posts_per_page = $limit; 
			} else {
				$posts_per_page = '-1';
			}
			if( $cat_id ) { 
				$cat = $cat_id; 
			} else {
				$cat = '';
			}
			
			if( $width ) { 
				$width_slider = $width . "px"; 
			} else {
				$width_slider = '100%';
			}	 	
			
			if( $items ) { 
				$items_slider = $items; 
			} else {
				$items_slider = '3';
			}
			
			if( $autoplay_interval ) { 
				$autoplay_intervalslider = $autoplay_interval; 
			} else {
				$autoplay_intervalslider = '4000';
			}
						

			ob_start();
			// Create the Query
			$post_type 		= 'sp_horizontal_scroll';
			$orderby 		= 'post_date';
			$order 			= 'DESC';
						
			$args = array ( 
		            'post_type'      => $post_type, 
		            'orderby'        => $orderby, 
		            'order'          => $order,
		            'posts_per_page' => $posts_per_page,  
		           
		            );
			if($cat != ""){
		            	$args['tax_query'] = array( array( 'taxonomy' => 'responsive_hss_slider-category', 'field' => 'id', 'terms' => $cat) );
		            }        
		      $query = new WP_Query($args);

			$post_count = $query->post_count;
			$itemindex = 0;

			if( $post_count > 0) :
			$categories = get_the_category_by_ID( $cat );
			?>
			<div style="width:<?php echo $width_slider; ?>;">
				<div id="hss_box_container" class="hss_colright hss_tem_box1 box_category hss_width_common">
					<div class="hss_round_colright hss_width_common">
						<h3 class="hss_txt_head"><a href="javascript:void(0);"><?php echo $categories;?></a></h3>
						<div class="hss_right hss_nav_item">
							<a href="javascript:;" class="hss_btn_control_ds prev_slider_item">Next</a>
							<a href="javascript:;" class="hss_btn_control_ds next_slider_item">Preview</a>
						</div>
						<div class="hss_content_box_category hss_width_common">
							<div class="hss_list_news">
								<div class="hss_list_ds">
									<div id="hss_box_items" class="owl-carousel owl-theme" style="opacity: 1; display: block;">
									<?php
										$html = '';
										while ($query->have_posts()) : $query->the_post();									
											if($itemindex % $items_slider == 0)
											{
												$thumb_url =wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(), array(300, 143) ) );
												if($itemindex == 0)
												{
													$html = $html.'<div class="owl-item">';
													$html = $html.'	<div class="hss_item_slider">';
													$html = $html.'		<ul class="hss_list_new">';
													$html = $html.'<li>';
													$html = $html.'	<div class="hss_img_colright">';

													$html = $html.'			<img src='.$thumb_url.' />';
													$html = $html.'	</div>';
													$html = $html.'	<div class="hss_title_colright">';											
													$sliderurl = get_post_meta( get_the_ID(),'rsris_hss_link', true );
													$content = get_the_title();
													$content = strip_tags($content);
													$summary = $this->hss_truncate($content, 50);
													if($sliderurl != '')
													{
														$html = $html.'	   <a title="'.$content.'" href="'.$sliderurl.'">'.$summary;					
														$html = $html.'	   </a>';
													}else{													
														$html = $html.$summary;
													}
													$html = $html.'	</div>';
													$html = $html.'</li>';
												}else{
													$html = $html.'		</ul>';
													$html = $html.'	</div>';
													$html = $html.'</div>';
													$html = $html.'<div class="owl-item">';
													$html = $html.'	<div class="hss_item_slider">';
													$html = $html.'		<ul class="hss_list_new">';
													$html = $html.'<li>';
													$html = $html.'	<div class="hss_img_colright">';

													$html = $html.'			<img src='.$thumb_url.' />';
													$html = $html.'	</div>';
													$html = $html.'	<div class="hss_title_colright">';
													
													$sliderurl = get_post_meta( get_the_ID(),'rsris_hss_link', true );
													$content = get_the_title();
													$content = strip_tags($content);
													$summary = $this->hss_truncate($content, 50);
													if($sliderurl != '')
													{
														$html = $html.'	   <a title="'.$content.'" href="'.$sliderurl.'">'.$summary;					
														$html = $html.'	   </a>';
													}else{													
														$html = $html.$summary;
													}
													$html = $html.'	</div>';
													$html = $html.'</li>';
												}
																							
											}
											else{
												$html = $html.'<li>';
												$html = $html.'	<div class="hss_img_colright">';

												$thumb_url =wp_get_attachment_url( get_post_thumbnail_id(get_the_ID(), array(300, 143) ) );
												$html = $html.'			<img src='.$thumb_url.' />';
												$html = $html.'	</div>';
												$html = $html.'	<div class="hss_title_colright">';											
			
												$sliderurl = get_post_meta( get_the_ID(),'rsris_hss_link', true );
												$content = get_the_title();
												$content = strip_tags($content);
												$summary = $this->hss_truncate($content, 50);
												if($sliderurl != '')
												{
													$html = $html.'	   <a title="'.$content.'" href="'.$sliderurl.'">'.$summary;					
													$html = $html.'	   </a>';
												}else{													
													$html = $html.$summary;
												}
												$html = $html.'	</div>';
												$html = $html.'</li>';
											}
											$itemindex++;
										endwhile;	
										print $html;									
									?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				endif;
				// Reset query to prevent conflicts
				wp_reset_query();
			?>							
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					var owl_box = $(".hss_content_box_category #hss_box_items");
					owl_box.owlCarousel({
						autoPlay: <?php echo $autoplay_intervalslider; ?>,
						items: 1,
						itemsDesktop: [1199,1],
						itemsTablet: [600,1], //2 items between 600 and 0
						itemsDesktopSmall: [900,1],// betweem 900px and 601px
						itemsMobile: [479,1],
						pagination:false
					});
					$(".next_slider_item").click(function(){
						owl_box.trigger('owl.next');
					});
					$(".prev_slider_item").click(function(){
						owl_box.trigger('owl.prev');
					});				
				});

			</script>
			<?php
			return ob_get_clean();
		}		
	}
}
	
function hss_master_gallery_images_load() {
        global $mfpd;
        $mfpd = new Horizontal_Scroll_Slider();
}
add_action( 'plugins_loaded', 'hss_master_gallery_images_load' );