<?php
// ACF fields for elements
if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_nerdpress-elements',
		'title' => 'NerdPress Elements',
		'fields' => array (
			array (
				'key' => 'field_52854c98304c9',
				'label' => 'Element Type',
				'name' => 'element_type',
				'type' => 'radio',
				'instructions' => 'What kind of element will you be making today?',
				'choices' => array (
					'gallery' => 'Gallery',
					'carousel' => 'Carousel',
					'accordion' => 'Accordion',
					'tabs' => 'Tabs',
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
			),
			array (
				'key' => 'field_52854e4f15a7c',
				'label' => 'Build Your Gallery',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'gallery',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854e6d15a7d',
				'label' => 'Columns',
				'name' => 'gal_columns',
				'type' => 'radio',
				'choices' => array (
					2 => 2,
					3 => 3,
					4 => 4,
					6 => 6,
				),
				'other_choice' => 0,
				'save_other_choice' => 0,
				'default_value' => '',
				'layout' => 'horizontal',
			),
			array (
				'key' => 'field_52854e9815a7e',
				'label' => 'Images',
				'name' => 'gal_images',
				'type' => 'gallery',
				'preview_size' => 'thumbnail',
				'library' => 'all',
			),
			array (
				'key' => 'field_52854ea915a7f',
				'label' => 'Build Your Carousel',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'carousel',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854db462645',
				'label' => 'Carousel',
				'name' => 'carousel',
				'type' => 'repeater',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'carousel',
						),
					),
					'allorany' => 'all',
				),
				'sub_fields' => array (
					array (
						'key' => 'field_52854dd062646',
						'label' => 'Image',
						'name' => 'slide_image',
						'type' => 'image',
						'column_width' => '',
						'save_format' => 'url',
						'preview_size' => 'thumbnail',
						'library' => 'all',
					),
					array (
						'key' => 'field_52854dfc62647',
						'label' => 'Caption',
						'name' => 'slide_caption',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_52854e0662648',
						'label' => 'Link',
						'name' => 'slide_link',
						'type' => 'text',
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
				),
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Slide',
			),
			array (
				'key' => 'field_52854f1ba9804',
				'label' => 'Build Your Accordion',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'accordion',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854f39a9805',
				'label' => 'Accordion',
				'name' => 'accordion',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_52854f46a9806',
						'label' => 'Title',
						'name' => 'acc_title',
						'type' => 'text',
						'required' => 1,
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_52854f53a9807',
						'label' => 'Content',
						'name' => 'acc_content',
						'type' => 'wysiwyg',
						'column_width' => '',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Accordion Section',
			),
			array (
				'key' => 'field_52854fa2ab49d',
				'label' => 'Build Your Tabs',
				'name' => '',
				'type' => 'tab',
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_52854c98304c9',
							'operator' => '==',
							'value' => 'tabs',
						),
					),
					'allorany' => 'all',
				),
			),
			array (
				'key' => 'field_52854fb5ab49e',
				'label' => 'Tabs',
				'name' => 'tabs',
				'type' => 'repeater',
				'sub_fields' => array (
					array (
						'key' => 'field_52854fb5ab49f',
						'label' => 'Title',
						'name' => 'tab_title',
						'type' => 'text',
						'required' => 1,
						'column_width' => '',
						'default_value' => '',
						'placeholder' => '',
						'prepend' => '',
						'append' => '',
						'formatting' => 'none',
						'maxlength' => '',
					),
					array (
						'key' => 'field_52854fb5ab4a0',
						'label' => 'Content',
						'name' => 'tab_content',
						'type' => 'wysiwyg',
						'column_width' => '',
						'default_value' => '',
						'toolbar' => 'full',
						'media_upload' => 'yes',
					),
				),
				'row_min' => 1,
				'row_limit' => '',
				'layout' => 'table',
				'button_label' => '+ Add Tab',
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'nrd_element',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'acf_after_title',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
?>