<?php

add_action( 'vc_before_init', 'showcase_vc_addon_function' );

// Generate param type "number"
if ( function_exists('add_shortcode_param'))
{
	add_shortcode_param( 'number', 'sc_number_field' );
}

// Function generate param type "number"
function sc_number_field($settings, $value)
{
	$dependency = vc_generate_dependencies_attributes($settings);
	$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
	$type = isset($settings['type']) ? $settings['type'] : '';
	$min = isset($settings['min']) ? $settings['min'] : '';
	$max = isset($settings['max']) ? $settings['max'] : '';
	$suffix = isset($settings['suffix']) ? $settings['suffix'] : '';
	$class = isset($settings['class']) ? $settings['class'] : '';
	$output .= '<input type="number" min="'.$min.'" max="'.$max.'" class="wpb_vc_param_value ' . $param_name . ' ' . $type . ' ' . $class . '" name="' . $param_name . '" value="'.$value.'" style="max-width:100px; margin-right: 10px;" />'.$suffix;
	return $output;
}

// Generate param type "custom_size"
if ( function_exists('add_shortcode_param'))
{
	add_shortcode_param( 'custom_size', 'sc_custom_size' );
}

function sc_custom_size( $settings, $value ) {
	$param_name = isset($settings['param_name']) ? $settings['param_name'] : '';
	$type = isset($settings['type']) ? $settings['type'] : '';
	$class = isset($settings['class']) ? $settings['class'] : '';
	$added_sizes = get_intermediate_image_sizes();
	$output .= '<select id="thumb" name="'.$param_name.'" class="wpb_vc_param_value '.$param_name.' '.$type.' '.$class.'">';
		foreach($added_sizes as $key => $sc_value){
			if($value == $sc_value){
				$selected = "selected='selected'";
			} else {
				$selected = '';
			}
			$output .= '<option '.$selected.' value="'. $sc_value .'">'. $sc_value .'</option>';
		}
	$output .= '</select>' ;
	return $output;
}


function showcase_vc_addon_function() {

	vc_map( array(
        'name' => __('Showcases', 'showcase-visual-composer-addon'),
        'base' => 'sc_showcase_box',
		"description" => __("Shortcode for Showcase", 'showcase-visual-composer-addon'),
        "icon" => "icon-sc-vc-addon",
        'params'=>array(
        	array(
				"param_name" => "info_title_separator",
				"heading" => __( 'Configure Title Style', 'showcase-visual-composer-addon'),
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
        	array(
				"type" => "colorpicker",
				"heading" => __( "Title color", 'showcase-visual-composer-addon' ),
				"param_name" => "showcase_color_title",
				"value" => '#333333',
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' ),
				"description" => __( "Color Default is #333333", "showcase-visual-composer-addon" ),
				"description" => __( "Choose title color", "showcase-visual-composer-addon" )
			),
			array(
				"type" => "number",
				"class" => "",
				"heading" => __("Font Size Title", "showcase-visual-composer-addon"),
				"param_name" => "showcase_size_title",
				"min" => 12,
				"suffix" => "px",
				"value" => '30',
				"dependency" => array(
					"element" => "content",
					"not_empty" => true
				),
				"description" => __( "Default is 30px", "showcase-visual-composer-addon" ),
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Text Align - Title', 'showcase-visual-composer-addon' ),
					'param_name' => 'showcase_size_align_title',
					"value"      => array(
						__('Left', 'showcase-visual-composer-addon')  	=> 'left',
						__('Center', 'showcase-visual-composer-addon')   => 'center',
						__('Right', 'showcase-visual-composer-addon')    => 'right',
				    ),
				    "description" => __( "Default is Left", "showcase-visual-composer-addon" ),
				    'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
				"param_name" => "box_title_separator",
				"heading" => __( 'Configure Content Box Style', 'showcase-visual-composer-addon' ),
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
				"type" => "colorpicker",
				"heading" => __( "Border Color", "showcase-visual-composer-addon" ),
				"param_name" => "showcase_border_color",
				"value" => '#F4F4F4',
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' ),
				"description" => __( "Choose Border Color<br /><small>Color Default is #F4F4F4</small>", "showcase-visual-composer-addon" )
			),
			array(
				"type" => "number",
				"class" => "",
				"heading" => __("Border Width", "showcase-visual-composer-addon"),
				"param_name" => "showcase_border_width",
				"min" => 0,
				"max" => 15,
				"suffix" => "px",
				"value" => '6',
				"dependency" => Array("element" => "content", "not_empty" => true),
				"description" => __( "Default is 6px", "showcase-visual-composer-addon" ),
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
				"type" => "number",
				"class" => "",
				"heading" => __("Border Radius", "showcase-visual-composer-addon"),
				"param_name" => "showcase_border_radius",
				"min" => 0,
				"max" => 15,
				"suffix" => "px",
				"value" => '0',
				"dependency" => Array("element" => "content", "not_empty" => true),
				"description" => __( "Default is 0px", "showcase-visual-composer-addon" ),
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
				'type' => 'dropdown',
				'heading' => __( 'Border Style', 'showcase-visual-composer-addon' ),
				'param_name' => 'showcase_border_style',
				"value"       => array(
					__('Solid', 'showcase-visual-composer-addon')  	=> 'solid',
					__('Dotted', 'showcase-visual-composer-addon')   => 'dotted',
					__('Dashed', 'showcase-visual-composer-addon')    => 'dashed',
					__('Double', 'showcase-visual-composer-addon')  	=> 'double',
					__('None', 'showcase-visual-composer-addon')  	=> 'none',
			    ),
			    "description" => __( "Default is Solid", "showcase-visual-composer-addon" ),
			    'group' => __( 'General Settings', 'showcase-visual-composer-addon' )
			),
			array(
				"type" => "colorpicker",
				"heading" => __( "Background Color", "showcase-visual-composer-addon" ),
				"param_name" => "showcase_bg_color",
				"value" => '#FCFCFC',
				'group' => __( 'General Settings', 'showcase-visual-composer-addon' ),
				"description" => __( "Choose Background Color<br /><small>Color Default is #FCFCFC</small>", "showcase-visual-composer-addon" )
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Order Showcase', 'showcase-visual-composer-addon' ),
					'param_name' => 'sc_order',
					"value"       => array(
						__('DESC', 'showcase-visual-composer-addon')  => 'DESC',
				        __('ASC', 'showcase-visual-composer-addon')  => 'ASC',
				        __('RAND', 'showcase-visual-composer-addon') => 'rand'
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' )
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Show Content', 'showcase-visual-composer-addon' ),
					'param_name' => 'show_content',
					"admin_label" => true,
					"value"       => array(
						__('Below', 'showcase-visual-composer-addon')  => 'vc_below',
				        __('Above', 'showcase-visual-composer-addon')  => 'vc_above',
				        __('Floats', 'showcase-visual-composer-addon') => 'vc_floats'
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'Presentation of Content', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'List per', 'showcase-visual-composer-addon' ),
					'param_name' => 'post_count',
					"value"       => array(
				        __('4 per line', 'showcase-visual-composer-addon')  => '4',
				        __('6 per line', 'showcase-visual-composer-addon')  => '6',
				        __('8 per line', 'showcase-visual-composer-addon')  => '8',
				        __('10 per line', 'showcase-visual-composer-addon') => '10'
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'How many Showcases per scroll', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'stopOnHover', 'showcase-visual-composer-addon' ),
					'param_name' => 'stop_hover',
					"value"       => array(
				        __('Deactivated', 'showcase-visual-composer-addon')  => 'false',
				        __('Activated', 'showcase-visual-composer-addon')    => 'true',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'Stop autoplay on mouse hover', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'autoPlay', 'showcase-visual-composer-addon' ),
					'param_name' => 'auto_play',
					"value"       => array(
				        __('3 Seconds', 'showcase-visual-composer-addon')  => '3000',
				        __('6 Seconds', 'showcase-visual-composer-addon')  => '6000',
				        __('8 Seconds', 'showcase-visual-composer-addon')  => '8000',
				        __('14 Seconds', 'showcase-visual-composer-addon') => '14000',
				        __('28 Seconds', 'showcase-visual-composer-addon') => '28000',
				        __('56 Seconds', 'showcase-visual-composer-addon') => '56000',
				        __('Stoped', 'showcase-visual-composer-addon')	 => 'false',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' )
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Pagination Speed', 'showcase-visual-composer-addon' ),
					'param_name' => 'pagination_speed',
					"value"       => array(
						__('4 Milliseconds', 'showcase-visual-composer-addon')  => '400',
				        __('8 Milliseconds', 'showcase-visual-composer-addon')  => '800',
				        __('16 Milliseconds', 'showcase-visual-composer-addon')  => '1600',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' )
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Navigation', 'showcase-visual-composer-addon' ),
					'param_name' => 'navigation',
					"value"       => array(
				        __('Deactivated', 'showcase-visual-composer-addon')  => 'false',
				        __('Activated', 'showcase-visual-composer-addon')    => 'true',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'Display "Next" and "Previous" buttons.', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Pagination', 'showcase-visual-composer-addon' ),
					'param_name' => 'pagination',
					"value"       => array(
						__('Activated', 'showcase-visual-composer-addon')    => 'true',
				        __('Deactivated', 'showcase-visual-composer-addon')  => 'false',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'Show pagination', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Pagination with Numbers', 'showcase-visual-composer-addon' ),
					'param_name' => 'pagination_numbers',
					"value"       => array(
						__('Deactivated', 'showcase-visual-composer-addon')  => 'false',
						__('Activated', 'showcase-visual-composer-addon')    => 'true',
				    ),
				    'group' => __( 'Carousel options', 'showcase-visual-composer-addon' ),
					'description' => __( 'Show numbers inside pagination buttons', 'showcase-visual-composer-addon')
			),
			array(
					'type' => 'dropdown',
					'heading' => __( 'Image Thumbnail', 'showcase-visual-composer-addon' ),
					'param_name' => 'image_thumbnail',
					"admin_label" => true,
					"value"       => array(
				        __('Circle', 'showcase-visual-composer-addon')  => 'showcase-circle',
				        __('Square', 'showcase-visual-composer-addon')  => 'showcase-square',
				    ),
				    'group' => __( 'Image Configuration', 'showcase-visual-composer-addon' ),
					'description' => __( 'Select the presentation thumbnail format', 'showcase-visual-composer-addon')
			),
			array(
					'type' 		 => 'custom_size',
					'heading' 	 => __( 'Image Size Thumb', 'showcase-visual-composer-addon' ),
					'param_name' => 'showcase_thumb',
					"admin_label" => true,
					"value" => "",
					'group' => __( 'Image Configuration', 'showcase-visual-composer-addon' ),
					'description' => __( 'Add your custom <strong>image_size()</strong> if you want.', 'showcase-visual-composer-addon')
			),
			array(
					'type' 		 => 'textfield',
					'heading' 	 => __( 'Add Custom Class', 'showcase-visual-composer-addon' ),
					'param_name' => 'base_class',
					"value"       => '',
					'group' => __( 'Image Configuration', 'showcase-visual-composer-addon' ),
					'description' => __( "Add your custom <strong>Class</strong> if you want.", 'showcase-visual-composer-addon')
			),
		),
		'category' => __( 'CHR Designer - Shortcodes', 'showcase-visual-composer-addon' ),     
    ) );

}