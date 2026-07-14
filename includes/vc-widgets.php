<?php

// ========================================
// WPBakery Widgets for Mattress Store
// ========================================

if (!function_exists('mattress_safe_number_format')) {
    function mattress_safe_number_format($value, $decimals = 2) {
        if ($value === '' || $value === null || is_array($value)) {
            return number_format(0, $decimals);
        }

        if (!is_numeric($value)) {
            return number_format(0, $decimals);
        }

        return number_format((float) $value, $decimals);
    }
}

// Get product categories dynamically from database
function get_product_categories_for_widget() {
    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false, // show all, even empty
        'parent' => 0 // Only top-level categories
    ));
    
    $category_options = array();
    if (!empty($categories) && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            // WPBakery needs: 'Label' => 'Value'
            $category_options[$category->name] = strval($category->term_id);
        }
    }

    // fallback if empty
    if (empty($category_options)) {
        $category_options['No Categories Found'] = '';
    }
    
    return $category_options;
}

// Get all subcategories for the widget
function get_all_subcategories_for_widget() {
    $terms = get_terms( array(
        'taxonomy'   => 'product_cat',
        'hide_empty' => false,
    ) );

    $options = array();

    $options = array('None Selected' => 0);
    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        foreach ( $terms as $term ) {
            if ( $term->parent != 0 ) {
                $parent = get_term( $term->parent, 'product_cat' );
                $options[$parent->name . ' → ' . $term->name] = $term->term_id;
            }
        }
    }

    return $options;
}

// Helper function to get products for dropdown
if (!function_exists('get_products_for_dropdown')) {
    function get_products_for_dropdown() {
        $products = array();
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product_id = get_the_ID();
                $product_title = get_the_title();
                $products[$product_title] = $product_id;
            }
        }
        
        wp_reset_postdata();
        return $products;
    }
}

// Helper function to get posts for dropdown
if (!function_exists('get_posts_for_dropdown')) {
    function get_posts_for_dropdown() {
        $posts = array();
        $args = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $post_id = get_the_ID();
                $post_title = get_the_title();
                $posts[$post_title] = $post_id;
            }
        }
        
        wp_reset_postdata();
        return $posts;
    }
}

// Helper function to get products with categories for dropdown
if (!function_exists('get_products_with_categories_for_dropdown')) {
    function get_products_with_categories_for_dropdown() {
        $products = array();
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        );
        
        $query = new WP_Query($args);
        
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                $product_id = get_the_ID();
                $product_title = get_the_title();
                
                // Get product categories
                $product_cats = wp_get_post_terms($product_id, 'product_cat');
                $category_name = '';
                
                if (!empty($product_cats) && !is_wp_error($product_cats)) {
                    // Get the first category
                    $category_name = $product_cats[0]->name;
                }
                
                // Format: "Product Name - Category"
                $display_name = $product_title;
                if (!empty($category_name)) {
                    $display_name .= ' - ' . $category_name;
                }
                
                $products[$display_name] = $product_id;
            }
        }
        
        wp_reset_postdata();
        return $products;
    }
}

// Create multi dropdown param type for mattress store
if (!function_exists('mattress_dropdown_multi_settings_field')) {
    function mattress_dropdown_multi_settings_field($param, $value) {
        $param_line = '';
        $param_line .= '<select multiple name="'. esc_attr($param['param_name']).'" class="wpb_vc_param_value wpb-input wpb-select '. esc_attr($param['param_name']).' '. esc_attr($param['type']).'">';
        
        foreach ($param['value'] as $text_val => $val) {
            if (is_numeric($text_val) && (is_string($val) || is_numeric($val))) {
                $text_val = $val;
            }
            $text_val = __($text_val, "js_composer");
            $selected = '';

            if (!is_array($value)) {
                $param_value_arr = explode(',', $value);
            } else {
                $param_value_arr = $value;
            }

            if ($value !== '' && in_array($val, $param_value_arr)) {
                $selected = ' selected="selected"';
            }
            $param_line .= '<option class="'.$val.'" value="'.$val.'"'.$selected.'>'.$text_val.'</option>';
        }
        
        $param_line .= '</select>';
        return $param_line;
    }
}

// Register the custom parameter type
if (!function_exists('register_mattress_custom_vc_params')) {
    function register_mattress_custom_vc_params() {
        vc_add_shortcode_param('mattress_dropdown_multi', 'mattress_dropdown_multi_settings_field');
    }
}
add_action('vc_before_init', 'register_mattress_custom_vc_params');

// Register WPBakery widgets
function register_mattress_vc_widgets() {
        if (!function_exists('vc_map')) { return; }
    $category_values = get_product_categories_for_widget();

    vc_map(array(
        'name' => 'Mattress Product Filter',
        'base' => 'mattress_product_filter',
        'description' => 'Filter and display mattress products based on category selection',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-post',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => 'Product Category',
                'param_name' => 'product_category',
                'value' => $category_values,
                'description' => 'Select the main product category to filter',
                'admin_label' => true,
                'save_always' => true,
                'std' => !empty($category_values) ? reset($category_values) : ''
            ),
            array(
                'type' => 'dropdown',
                'heading' => 'Subcategory',
                'param_name' => 'product_subcategories',
                'value' => get_all_subcategories_for_widget(), // ✅ safe default
                'description' => 'Select a specific subcategory (optional)',
                'admin_label' => true,
                'save_always' => true
            ),            
            array(
                'type' => 'textfield',
                'heading' => 'Posts Per Page',
                'param_name' => 'posts_per_page',
                'value' => '6',
                'save_always' => true
            ),
            array(
                'type' => 'dropdown',
                'heading' => 'Order By',
                'param_name' => 'orderby',
                'value' => array(
                    'Date' => 'date',
                    'Title' => 'title',
                    'Price' => 'price',
                    'Menu Order' => 'menu_order'
                ),
                'save_always' => true
            ),
            array(
                'type' => 'dropdown',
                'heading' => 'Order',
                'param_name' => 'order',
                'value' => array(
                    'Descending' => 'DESC',
                    'Ascending' => 'ASC'
                ),
                'save_always' => true
            ),
            array(
                'type' => 'checkbox', // show filter
                'heading' => 'Show Filter',
                'param_name' => 'show_filter',
                'value' => array(
                    'Yes' => 'yes'
                ),
            )
        )
    ));

    // Product Feature Image Widget
    vc_map(array(
        'name' => 'Product Feature Image',
        'base' => 'product_feature_image',
        'description' => 'Display the current product\'s featured image with gallery slider. This widget automatically fetches the featured image from the right sidebar images.',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-format-image',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => 'Image Size',
                'param_name' => 'image_size',
                'value' => array(
                    'Full Size' => 'full',
                    'Large' => 'large',
                    'Medium' => 'medium',
                    'Thumbnail' => 'thumbnail',
                ),
                'std' => 'full',
                'description' => 'Select the image size to display',
            ),
        )
    ));

    // Product Features Icons Widget
    vc_map(array(
        'name' => 'Product Features Icons',
        'base' => 'product_features_icons',
        'description' => 'Display product features with icons and text',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-star-filled',
        'params' => array(
            array(
                'type' => 'param_group',
                'heading' => 'Feature Items',
                'param_name' => 'feature_items',
                'description' => 'Add feature items with icons and text',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'heading' => 'Icon',
                        'param_name' => 'icon',
                        'description' => 'Select icon image for this feature',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Feature Text',
                        'param_name' => 'text',
                        'description' => 'Enter the feature description text',
                        'admin_label' => true,
                    ),
                ),
            ),
            array(
                'type' => 'dropdown',
                'heading' => 'Layout Style',
                'param_name' => 'layout_style',
                'value' => array(
                    'Horizontal' => 'horizontal',
                    'Vertical' => 'vertical',
                ),
                'std' => 'horizontal',
                'description' => 'Choose the layout style for features',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Icon Size',
                'param_name' => 'icon_size',
                'value' => '24',
                'description' => 'Enter icon size in pixels (e.g., 24)',
            ),
        )
    ));

    // Product Offer Banner Widget
    vc_map(array(
        'name' => 'Product Offer Banner',
        'base' => 'product_offer_banner',
        'description' => 'Display product offer banner with background image, title and subtitle',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-megaphone',
        'params' => array(
            array(
                'type' => 'attach_image',
                'heading' => 'Background Image',
                'param_name' => 'background_image',
                'description' => 'Select background image for the offer banner',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Button Text',
                'param_name' => 'button_text',
                'value' => 'SPRING DEALS',
                'description' => 'Enter the button text',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Title',
                'param_name' => 'title',
                'value' => 'Up to 20% off on mattresses!*',
                'description' => 'Enter the offer title',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Subtitle',
                'param_name' => 'subtitle',
                'description' => 'Enter subtitle text (optional)',
            ),
        )
    ));

    // Product Base Details Widget
    vc_map(array(
        'name' => 'Product Base Details',
        'description' => 'Display product details with configurable options',
        'base' => 'product_base_details',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-info',
        'params' => array(
            array(
                'type' => 'checkbox',
                'heading' => 'Show Product Title',
                'param_name' => 'show_title',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display the product title',
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show Product Description',
                'param_name' => 'show_description',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display the product description',
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show Firmness Level',
                'param_name' => 'show_firmness',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display firmness level',
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show Mattress Sizes (Only for Mattress Products)',
                'param_name' => 'show_sizes',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display size selection (only shown for mattress category products)',
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show Bedframe Sizes (Only for Bedframe Products)',
                'param_name' => 'show_bedframe_sizes',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display size selection (only shown for bedframe category products)',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Stock Status Text',
                'param_name' => 'stock_text',
                'value' => 'In Stock - Delivered within 3-5 days',
                'description' => 'Enter the stock status text',
            ),
        )
    ));

    // Product Addons Widget
    vc_map(array(
        'name' => 'Product Addons',
        'base' => 'product_addons',
        'description' => 'Display related product addons based on current product category',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-products',
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => 'Number of Products to Show',
                'param_name' => 'products_count',
                'value' => '3',
                'description' => 'Enter the number of addon products to display',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Section Title',
                'param_name' => 'section_title',
                'value' => 'Properties',
                'description' => 'Enter the section title',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Learn More Link Text',
                'param_name' => 'learn_more_text',
                'value' => 'Learn About Our Bed Frames',
                'description' => 'Enter the learn more link text',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Learn More Link URL',
                'param_name' => 'learn_more_url',
                'value' => '#',
                'description' => 'Enter the learn more link URL',
            ),
        )
    ));

    // Product Image Gallery Widget
    vc_map(array(
        'name' => 'Product Image Gallery',
        'base' => 'product_image_gallery',
        'description' => 'Display product gallery images in alternating width layout. This widget automatically fetches the gallery images from the right sidebar images.',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-format-gallery',
        'params' => array(        )
    ));

    // Our Most Loved Products Widget
    vc_map(array(
        'name' => 'Our Most Loved Products',
        'base' => 'our_most_loved_products',
        'description' => 'Display selected products in a products slider with dynamic badges',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-heart',
        'params' => array(
            array(
                'type' => 'mattress_dropdown_multi',
                'heading' => 'Select Products',
                'param_name' => 'selected_products',
                'description' => 'Select multiple products to display',
                'value' => get_products_for_dropdown(),
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show View All Button',
                'param_name' => 'show_view_all_button',
                'value' => array('Yes' => 'yes'),
                'std' => 'yes',
                'description' => 'Display the View All Products button section',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Button Text',
                'param_name' => 'button_text',
                'value' => 'View All Products',
                'description' => 'Enter the text for the button',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Button URL',
                'param_name' => 'button_url',
                'value' => '#',
                'description' => 'Enter the URL for the button',
            ),
        )
    ));

    // Scroll Gallery Banner Widget
    vc_map(array(
        'name' => 'Scroll Gallery Banner',
        'base' => 'scroll_gallery_banner',
        'description' => 'Display product featured image and gallery images in a Swiper slider with thumbnails',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-images-alt2',
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => 'Widget Title',
                'param_name' => 'widget_title',
                'value' => 'Product Gallery',
                'description' => 'Enter a title for this widget (optional)',
            ),
        )
    ));

    // Offer's Product Widget
    vc_map(array(
        'name' => 'Offer Products',
        'base' => 'offer_products',
        'description' => 'Display sale products from selected category with discount labels. Note: This widget only shows products that are on sale.',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-tag',
        'params' => array(
            array(
                'type' => 'dropdown',
                'heading' => 'Product Category',
                'param_name' => 'product_category',
                'description' => 'Select the category to display sale products from',
                'value' => get_product_categories_for_widget(),
                'admin_label' => true,
                'save_always' => true,
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Contact Button Text',
                'param_name' => 'contact_button_text',
                'value' => 'Contact Us',
                'description' => 'Enter the text for the contact button',
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Contact Button URL',
                'param_name' => 'contact_button_url',
                'value' => '#',
                'description' => 'Enter the URL for the contact button',
            ),
        )
    ));

    // Sleep Culture Product Widget
    vc_map(array(
        'name' => 'Sleep Culture Products',
        'base' => 'sleep_culture_products',
        'description' => 'Display products on sleep culture page',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-images-alt2',
        'params' => array(
            array(
                'type' => 'mattress_dropdown_multi',
                'heading' => 'Select Products',
                'param_name' => 'selected_products',
                'description' => 'Select products to display in the sleep culture section',
                'value' => get_products_with_categories_for_dropdown(),
            ),
        )
    ));

    // Mattress Post Selection with Description Widget
    vc_map(array(
        'name' => 'Mattress Post Selection with Description',
        'base' => 'mattress_post_selection_with_description',
        'description' => 'Display selected posts with title, description and featured image',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-post',
        'params' => array(
            array(
                'type' => 'mattress_dropdown_multi',
                'heading' => 'Select Posts',
                'param_name' => 'selected_posts',
                'description' => 'Select posts to display with description',
                'value' => get_posts_for_dropdown(),
            ),
        )
    ));

    // Mattress Collection Widget
    vc_map(array(
        'name' => 'Mattresses Collection Product Category Listing',
        'base' => 'mattresses_collection_product_category_listing',
        'description' => 'Display mattress collection grid with dynamic items',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-grid-view',
        'params' => array(
            array(
                'type' => 'param_group',
                'heading' => 'Collection Items',
                'param_name' => 'collection_items',
                'description' => 'Add collection items with image, title, subtitle, link and link text',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'heading' => 'Image',
                        'param_name' => 'image',
                        'description' => 'Select image for this collection item',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Title',
                        'param_name' => 'title',
                        'description' => 'Enter the title for this collection item',
                        'admin_label' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'heading' => 'Subtitle/Description',
                        'param_name' => 'subtitle',
                        'description' => 'Enter the subtitle or description for this collection item',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Link URL',
                        'param_name' => 'link_url',
                        'description' => 'Enter the URL for the link button',
                        'value' => '#',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Link Text',
                        'param_name' => 'link_text',
                        'description' => 'Enter the text for the link button',
                        'value' => 'Browse Now',
                    ),
                ),
            ),
        )
    ));

    // Mattress Collection Trust Slider Widget
    vc_map(array(
        'name' => 'Mattress Collection Trust Slider',
        'base' => 'mattress_collection_trust_slider',
        'description' => 'Display trust slider with dynamic items for mattress collection',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-format-gallery',
        'params' => array(
            array(
                'type' => 'param_group',
                'heading' => 'Trust Items',
                'param_name' => 'trust_items',
                'description' => 'Add trust items with image, title and testimonial',
                'params' => array(
                    array(
                        'type' => 'attach_image',
                        'heading' => 'Image',
                        'param_name' => 'image',
                        'description' => 'Select image for this trust item',
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Title',
                        'param_name' => 'title',
                        'description' => 'Enter the title for this trust item',
                        'admin_label' => true,
                    ),
                    array(
                        'type' => 'textarea',
                        'heading' => 'Testimonial/Description',
                        'param_name' => 'testimonial',
                        'description' => 'Enter the testimonial or description for this trust item',
                    ),
                ),
            ),
        )
    ));

    // Mattress Sleep Smart Slider Widget
    vc_map(array(
        'name' => 'Mattresses Post Selection',
        'base' => 'mattress_post_selection_slider',
        'description' => 'Display sleep smart slider with selected posts',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-post',
        'params' => array(
            array(
                'type' => 'mattress_dropdown_multi',
                'heading' => 'Select Posts',
                'param_name' => 'selected_posts',
                'description' => 'Select posts to display in the slider',
                'value' => get_posts_for_dropdown(),
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Read More Text',
                'param_name' => 'read_more_text',
                'description' => 'Enter the text for the read more link',
                'value' => 'Read more >',
            ),
        )
    ));

    // Map Widget
    vc_map(array(
        'name' => 'Map',
        'base' => 'map_custom_widget',
        'description' => 'Display map with pinned location',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-post',
        'params' => array(
            array(
                'type' => 'param_group',
                'value' => '',
                'param_name' => 'map_marker',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'heading' => 'Marker Name',
                        'param_name' => "marker_name",
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Latitude',
                        'param_name' => "marker_latitude",
                    ),
                    array(
                        'type' => 'textfield',
                        'heading' => 'Longitude',
                        'param_name' => "marker_longitude",
                    ),
                )
            ),
        )
    ));

    // Testimonial Widget
    vc_map(array(
        'name' => 'Testimonials Mattress Slider',
        'base' => 'testimonial_custom_widget',
        'description' => 'Show Testimonials with stars',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-post',
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => 'Number of Testimonials',
                'param_name' => 'number_testimonials',
                'description' => 'Enter the number how many testimonials needs to be shown',
                'value' => '10',
            ),
        )
    ));

    // Bedframe Variation Option List Widget
    vc_map(array(
        'name' => 'Bedframe Variation Option List',
        'base' => 'bedframe_variation_option_list',
        'description' => 'Display bedframe variation options dynamically from WooCommerce product attributes',
        'category' => 'Mattress Store',
        'icon' => 'dashicons-admin-settings',
        'params' => array(
            array(
                'type' => 'textfield',
                'heading' => 'Widget Title',
                'param_name' => 'widget_title',
                'value' => 'START CUSTOMISING',
                'description' => 'Enter the title for the variation options widget'
            ),
            array(
                'type' => 'checkbox',
                'heading' => 'Show Variation Categories',
                'param_name' => 'show_variations',
                'value' => array(
                    'Fabric' => 'fabric',
                    'Finish' => 'finish',
                    'Storage' => 'storage', 
                    'Slats' => 'slats',
                    'Model' => 'model',
                    'Feet' => 'feet'
                ),
                'description' => 'Select which variation categories to display'
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Custom Image URL',
                'param_name' => 'custom_image_url',
                'value' => '/wp-content/uploads/2025/08/Product-Category-Icon.jpg',
                'description' => 'Enter custom image URL for variation options (optional)'
            ),
            array(
                'type' => 'textfield',
                'heading' => 'Forminator Form ID',
                'param_name' => 'forminator_form_id',
                'value' => '',
                'description' => 'Enter the Forminator form ID to send variation data to (optional)'
            )
        )
    ));

}
add_action('vc_before_init', 'register_mattress_vc_widgets');

// Fallback hook in case vc_before_init doesn't fire
// add_action('init', 'register_mattress_vc_widgets'); // Only runs via vc_before_init when WPBakery is active

// Shortcode function for mattress product filter
function mattress_product_filter_shortcode($atts) {
    $atts = shortcode_atts(array(
        'product_category' => '',
        'product_subcategories' => '',
        'posts_per_page' => '6',
        'orderby' => 'date',
        'order' => 'DESC',
        'show_filter' => '',
    ), $atts);
    
    // Check if category is numeric (ID) or string (slug/name)
    $category_id = $atts['product_category'];
    $subcategory_id = $atts['product_subcategories'];
    $category_slug = '';
    $show_filter = $atts['show_filter'] === 'yes' ? 'has_filter' : 'no_filter';
    $themes_uri = get_stylesheet_directory_uri();

    // If it's numeric, it's already an ID
    if (is_numeric($category_id)) {
        $category_term = get_term($category_id, 'product_cat');
        if ($category_term && !is_wp_error($category_term)) {
            $category_slug = $category_term->slug;
        }
    } else {
        // If it's a string, try to get the term by slug
        $category_term = get_term_by('slug', $category_id, 'product_cat');
        if ($category_term && !is_wp_error($category_term)) {
            $category_id = $category_term->term_id;
            $category_slug = $category_term->slug;
        }
    }
    
    // Build tax query based on category and subcategory
    $tax_query = array();
    
    if ($subcategory_id && !empty($subcategory_id) && ($subcategory_id != 0)) {
        // If subcategory is selected, filter by subcategory
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $subcategory_id
        );
    } elseif ($category_id && !empty($category_id)) {
        // If no subcategory, filter by main category
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $category_id
        );
    }
    
    // Add filter parameters for mattresses category
    if ($category_slug === 'mattresses' && empty($subcategory_id)) {
        // Mattress Type filter
        if (!empty($_GET['mattress_type']) && is_array($_GET['mattress_type'])) {
            $tax_query[] = array(
                'taxonomy' => 'mattress_type',
                'field' => 'term_id',
                'terms' => array_map('intval', $_GET['mattress_type']),
                'operator' => 'IN'
            );
        }
        
        // Firmness Level filter
        if (!empty($_GET['firmness_level']) && is_array($_GET['firmness_level'])) {
            $tax_query[] = array(
                'taxonomy' => 'firmness_level',
                'field' => 'term_id',
                'terms' => array_map('intval', $_GET['firmness_level']),
                'operator' => 'IN'
            );
        }
        
        // Mattress Size filter
        if (!empty($_GET['mattress_size']) && is_array($_GET['mattress_size'])) {
            $tax_query[] = array(
                'taxonomy' => 'mattress_size',
                'field' => 'term_id',
                'terms' => array_map('intval', $_GET['mattress_size']),
                'operator' => 'IN'
            );
        }
        
        // Brand filter
        if (!empty($_GET['product_brand']) && is_array($_GET['product_brand'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field' => 'term_id',
                'terms' => array_map('intval', $_GET['product_brand']),
                'operator' => 'IN'
            );
        }
    }
    
    // Query products with category filter using ID
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $atts['posts_per_page'],
        'orderby' => $atts['orderby'],
        'order' => $atts['order']
    );
    
    // Only add tax_query if we have categories to filter by
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        $message = 'No products found';
        if ($subcategory_id) {
            $message .= ' in the selected subcategory';
        } elseif ($category_id) {
            $message .= ' in the selected category';
        } else {
            $message .= ' - no category filter applied';
        }
        if ($category_id) {
            $message .= '. Category ID: ' . $category_id;
        }
        return '<p>' . $message . '</p>';
    }
    
    ob_start();

    // Show filter for mattresses or bed frames when no subcategory is selected
    if (($category_slug === 'mattresses' || $category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) && empty($subcategory_id)) {
        // Get dynamic filter options based on category
        if ($category_slug === 'mattresses') {
            $mattress_types = get_terms(array(
                'taxonomy' => 'mattress_type',
                'hide_empty' => true,
            ));
            
            $mattress_sizes = get_terms(array(
                'taxonomy' => 'mattress_size',
                'hide_empty' => true,
            ));
            
            $brands = get_terms(array(
                'taxonomy' => 'product_brand',
                'hide_empty' => true,
            ));
        }
        
        // Firmness levels for both mattresses and bed frames
        $firmness_levels = get_terms(array(
            'taxonomy' => 'firmness_level',
            'hide_empty' => true,
        ));
        
        // Bed frame sizes for bed frames
        if ($category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) {
            $bedframe_sizes = get_terms(array(
                'taxonomy' => 'bedframe_size',
                'hide_empty' => true,
            ));
        }

        echo '<div class="filter mattress-filter ' . esc_attr($show_filter) . '">
                <button class="filter-button">
                    Filters
                    <img src="/wp-content/themes/salient-child/images/filters.svg">
                </button>
                <div class="filter-form">
                    <div class="filter-header">
                        <h4>FILTER & SORT</h4>
                        <img src="/wp-content/themes/salient-child/images/close.svg">
                    </div>
                    <form action="" method="get">
                        <input type="hidden" name="category" value="' . esc_attr($category_id) . '">
                        <input type="hidden" name="posts_per_page" value="' . esc_attr($atts['posts_per_page']) . '">';
        
        // Mattress Type Filter (only for mattresses)
        if ($category_slug === 'mattresses' && !empty($mattress_types) && !is_wp_error($mattress_types)) {
            echo '<details class="filter-group">
                    <summary class="filter-toggle">
                        Mattress Type
                        <img src="/wp-content/themes/salient-child/images/chevron.svg">
                    </summary>
                    <div class="filter-options">';
            foreach ($mattress_types as $type) {
                $checked = isset($_GET['mattress_type']) && in_array($type->term_id, $_GET['mattress_type']) ? 'checked' : '';
                echo '<label>
                        <input type="checkbox" name="mattress_type[]" value="' . esc_attr($type->term_id) . '" ' . $checked . '>
                        <span>' . esc_html($type->name) . '</span>
                    </label>';
            }
            echo '</div>
                </details>';
        }
        
        // Firmness Level Filter
        if (!empty($firmness_levels) && !is_wp_error($firmness_levels)) {
            echo '<details class="filter-group">
                    <summary class="filter-toggle">
                        Firmness Level
                        <img src="/wp-content/themes/salient-child/images/chevron.svg">
                    </summary>
                    <div class="filter-options">';
            foreach ($firmness_levels as $firmness) {
                $checked = isset($_GET['firmness_level']) && in_array($firmness->term_id, $_GET['firmness_level']) ? 'checked' : '';
                echo '<label>
                        <input type="checkbox" name="firmness_level[]" value="' . esc_attr($firmness->term_id) . '" ' . $checked . '>
                        <span>' . esc_html($firmness->name) . '</span>
                    </label>';
            }
            echo '</div>
                </details>';
        }
        
        // Size Filter (mattress sizes for mattresses, bedframe sizes for bed frames)
        if ($category_slug === 'mattresses' && !empty($mattress_sizes) && !is_wp_error($mattress_sizes)) {
            echo '<details class="filter-group">
                    <summary class="filter-toggle">
                        Size
                        <img src="/wp-content/themes/salient-child/images/chevron.svg">
                    </summary>
                    <div class="filter-options">';
            foreach ($mattress_sizes as $size) {
                $checked = isset($_GET['mattress_size']) && in_array($size->term_id, $_GET['mattress_size']) ? 'checked' : '';
                echo '<label>
                        <input type="checkbox" name="mattress_size[]" value="' . esc_attr($size->term_id) . '" ' . $checked . '>
                        <span>' . esc_html($size->name) . '</span>
                    </label>';
            }
            echo '</div>
                </details>';
        } elseif (($category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) && !empty($bedframe_sizes) && !is_wp_error($bedframe_sizes)) {
            echo '<details class="filter-group">
                    <summary class="filter-toggle">
                        Size
                        <img src="/wp-content/themes/salient-child/images/chevron.svg">
                    </summary>
                    <div class="filter-options">';
            foreach ($bedframe_sizes as $size) {
                $checked = isset($_GET['bedframe_size']) && in_array($size->term_id, $_GET['bedframe_size']) ? 'checked' : '';
                echo '<label>
                        <input type="checkbox" name="bedframe_size[]" value="' . esc_attr($size->term_id) . '" ' . $checked . '>
                        <span>' . esc_html($size->name) . '</span>
                    </label>';
            }
            echo '</div>
                </details>';
        }
        
        // Brand Filter (only for mattresses)
        if ($category_slug === 'mattresses' && !empty($brands) && !is_wp_error($brands)) {
            echo '<details class="filter-group">
                    <summary class="filter-toggle">
                        Brand
                        <img src="/wp-content/themes/salient-child/images/chevron.svg">
                    </summary>
                    <div class="filter-options">';
            foreach ($brands as $brand) {
                $checked = isset($_GET['product_brand']) && in_array($brand->term_id, $_GET['product_brand']) ? 'checked' : '';
                echo '<label>
                        <input type="checkbox" name="product_brand[]" value="' . esc_attr($brand->term_id) . '" ' . $checked . '>
                        <span>' . esc_html($brand->name) . '</span>
                    </label>';
            }
            echo '</div>
                </details>';
        }
        
        echo '</form>
                </div>
                <div class="overlay_filter"></div>
            </div>';
        
        // end filters here

        // Add AJAX filtering JavaScript
        static $ajax_script_loaded = false;
        if (!$ajax_script_loaded) {
            echo '<script>
            jQuery(document).ready(function($) {
                // Handle filter form submission for all filter forms
                $(document).on("change", ".filter-form input[type=\"checkbox\"]", function() {
                    var $form = $(this).closest("form");
                    var currentCategoryId = $form.find("input[name=\"category\"]").val();
                    var currentPostsPerPage = $form.find("input[name=\"posts_per_page\"]").val();
                    var $productsGrid = $form.closest(".filter").siblings(".mattress-products-grid, .featured-collection");
                    
                    // Store original products data for this specific widget
                    if (!$productsGrid.data("original-content")) {
                        $productsGrid.data("original-content", $productsGrid.html());
                    }
                    var formData = new FormData();
                    formData.append("action", "filter_mattress_products");
                    formData.append("category_id", currentCategoryId);
                    formData.append("posts_per_page", currentPostsPerPage);
                    formData.append("nonce", "' . wp_create_nonce('filter_mattress_products') . '");
                    
                    // Collect selected filters from this specific form
                    $form.find("input[type=\"checkbox\"]:checked").each(function() {
                        var name = $(this).attr("name");
                        var value = $(this).val();
                        if (formData.has(name)) {
                            formData.append(name + "[]", value);
                        } else {
                            formData.append(name, value);
                        }
                    });
                    
                    // Show loading state
                    $productsGrid.html("<div class=\"loading\">Loading products...</div>");
                    
                    // AJAX request
                    $.ajax({
                        url: "' . admin_url('admin-ajax.php') . '",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $productsGrid.html(response.data);
                            } else {
                                $productsGrid.html("<div class=\"no-products-message\" style=\"text-align: center; padding: 60px 20px; color: #666;\"><h3 style=\"margin-bottom: 15px; color: #222D5A;\">Nothing at the moment in this filter</h3><p style=\"margin-bottom: 0; font-size: 16px;\">Please try different filter combinations to find the perfect mattress for you.</p></div>");
                            }
                        },
                        error: function() {
                            $productsGrid.html("<div class=\"error-message\" style=\"text-align: center; padding: 60px 20px; color: #d32f2f;\"><h3 style=\"margin-bottom: 15px;\">Oops! Something went wrong</h3><p style=\"margin-bottom: 0; font-size: 16px;\">Please refresh the page and try again.</p></div>");
                        }
                    });
                });
            });
            </script>';
            $ajax_script_loaded = true;
        }
    }
    
    // Check category type and render appropriate structure
    if ($category_slug === 'mattresses') {
        // Mattress structure
        ?>
        <div class="mattress-products-grid">
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                
                // Get prices
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                $current_price = $product->get_price();
                
                // Get mattress sizes for product code
                $sizes = wp_get_post_terms($product_id, 'mattress_size');
                $product_code = '';
                if (!empty($sizes) && !is_wp_error($sizes)) {
                    $first_size = $sizes[0];
                    $size_dimensions = get_term_meta($first_size->term_id, 'size_dimensions', true);
                    $product_code = $size_dimensions ? $size_dimensions : $first_size->name;
                }
                            
                // Get product categories
                $product_cats = wp_get_post_terms($product_id, 'product_cat');
                // Get subcategories
                $subcategories = array();
                foreach ($product_cats as $cat) {
                    if ($cat->parent != 0) {
                        $subcategories[] = $cat->name;
                    }
                }
                if (!empty($subcategories)) {
                    $collection = implode(', ', $subcategories);
                }

                

            ?>
            
            <!-- Mattress Product Card -->
            <a class="mattress-product-card" href="<?php echo get_permalink( $product_id );?>">
                <div class="product-top-info">
                    <h2 class="product-title"><?php echo esc_html($title); ?></h2>
                    <?php if ($collection) : ?>
                        <span class="collection"><?php echo esc_html($collection); ?></span>
                    <?php endif; ?>
                    <?php if ($description) : ?>
                        <p class="product-desc"><?php echo esc_html($description); ?></p>
                    <?php endif; ?>
                </div>
                
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                
                <div class="product-bottom-info">
                    <div class="product-features">
                        <ul class="features-list">
                            <li class="features-item hot-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/hot-sleeper.svg" alt="Feature 1" width="36" height="36">
                                <span class="tooltip-text">Perfect for hot sleepers</span>
                            </li>
                            <li class="features-item night-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/night-sleeper.svg" alt="Feature 2" width="36" height="36">
                                <span class="tooltip-text">Perfect for night sleepers</span>
                            </li>
                            <li class="features-item temp-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/temp-sleeper.svg" alt="Feature 3" width="36" height="36">
                                <span class="tooltip-text">Works with all temp</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="product-details">
                        <span class="from">From:</span>
                        <div class="product-price">
                            <?php if ($sale_price && $sale_price < $regular_price) : ?>
                                <s class="regular-price">€<?php echo esc_html(mattress_safe_number_format($regular_price, 2)); ?></s>
                                <span class="sale-price">€<?php echo esc_html(mattress_safe_number_format($sale_price, 2)); ?></span>
                            <?php else : ?>
                                <span class="price">€<?php echo esc_html(mattress_safe_number_format($current_price, 2)); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($product_code) : ?>
                            <span class="product-code"><?php echo esc_html($product_code); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </a>
            
            <?php endwhile; ?>
        </div>
        
        <?php
    } elseif ($category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) {
        // If it's numeric, it's already an ID
        $category_name = '';
        $category_description = '';
        $category_term = '';
        if (is_numeric($subcategory_id) && $subcategory_id != 0) {
            $category_term = get_term($subcategory_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        } else {
            $category_term = get_term($category_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        }


        mattress_products_top_info($category_id);
        

        // Bed Frame structure
        ?>
        <div class="featured-collection">
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                if (!$image) {
                    $image = get_stylesheet_directory_uri() . '/images/product-image.png';
                }
                
                // Get price
                $current_price = $product->get_price();
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                
                // Get product URL
                $product_url = get_permalink($product_id);
            ?>
            
            <div class="product-card">
                <?php 
                    // Check if product is NEW (added in last 30 days)
                    $product_date = get_the_date('Y-m-d', $product_id);
                    $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
                    $is_new = ($product_date >= $thirty_days_ago);
                    
                    // Check if product is on SALE
                    $is_sale = ($sale_price && $sale_price < $regular_price);
                ?>
				<?php if( $is_new || $is_sale) : ?>
				    <div class="badge_overlay">
						<?php if ($is_new) : ?>
							<span class="product-label new-label sale_badge">NEW</span>
						<?php endif; ?>

						<?php if ($is_sale) : ?>
							<span class="product-label sale-label off_badge">SALE</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>

                <div class="shop-featured-img">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                    <h3><?php echo esc_html($title); ?></h3>
                    <a href="<?php echo esc_url($product_url); ?>" class="nectar-button"></a>
                </div>
            </div>
            
            <?php endwhile; ?>
        </div>
        
        <?php
    } else if ($category_slug === 'pillow') {
        // Pillow structure
        
        // If it's numeric, it's already an ID
        $category_name = '';
        $category_description = '';
        $category_term = '';
        if (is_numeric($subcategory_id) && $subcategory_id != 0) {
            $category_term = get_term($subcategory_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        } else {
            $category_term = get_term($category_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        }

        mattress_products_top_info($category_id);
        ?>

       

        <div class="featured-collection">
        
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                
                // Get prices
                $current_price = $product->get_price();
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                
                // Get product URL
                $product_url = get_permalink($product_id);

                $height   = get_post_meta($product_id, '_product_height', true);
                $firmness = get_post_meta($product_id, '_product_material', true);
                $medium   = get_post_meta($product_id, '_product_medium', true);
                $fiber    = get_post_meta($product_id, '_product_fibre', true);
            ?>
            
                <a href="<?php echo get_permalink( $product_id );?>" class="pillow_product">
                    <div class="pillow_card">
                        <h3><?php echo esc_html($title); ?></h3>
                        <p><?php echo esc_html($description); ?></p>
                    </div>
                    
                    <div class="pillow_img">                        
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </div>
                    
                    <div class="pillow_card">
                        <div class="table">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>Height: <strong><?php echo esc_html($height ? $height : ''); ?></strong></td>
                                        <td>Material: <strong><?php echo esc_html($firmness ? $firmness : ''); ?></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Medium: <strong><?php echo esc_html($medium ? $medium : ''); ?></strong></td>
                                        <td>Fiber: <strong><?php echo esc_html($fiber ? $fiber : ''); ?></strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span class="price">
                            <?php if ($sale_price && $sale_price < $regular_price) : ?>
                                <s>€<?php echo esc_html(mattress_safe_number_format($regular_price, 2)); ?></s>
                                €<?php echo esc_html(mattress_safe_number_format($sale_price, 2)); ?>
                            <?php else : ?>
                                €<?php echo esc_html(mattress_safe_number_format($current_price, 2)); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </a>
            
            <?php endwhile; ?>
        </div>
        
        <?php
    }  else if ($category_slug === 'accessories') {
        // Accessories structure
        
        // If it's numeric, it's already an ID
        $category_name = '';
        $category_description = '';
        $category_term = '';
        if (is_numeric($subcategory_id) && $subcategory_id != 0) {
            $category_term = get_term($subcategory_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        } else {
            $category_term = get_term($category_id, 'product_cat');
            if ($category_term && !is_wp_error($category_term)) {
                $category_name = $category_term->name;
                $category_description = $category_term->description;
            }
        }

        mattress_products_top_info($category_id);

        ?>

        <div class="featured-collection">
        
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                
                // Get prices
                $current_price = $product->get_price();
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                
                // Get product URL
                $product_url = get_permalink($product_id);
            ?>
            
                <a class="pillow_product" href="<?php echo get_permalink( $product_id );?>">
                    <div class="pillow_card">
                        <h3><?php echo esc_html($title); ?></h3>
                        <p><?php echo esc_html($description); ?></p>
                    </div>
                    
                    <div class="pillow_img">                        
                        <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                    </div>
                    
                    <div class="pillow_card">
                        <ul class="features-list">
                            <li class="features-item hot-sleeper">
                                <img src="<?php echo esc_url($themes_uri . '/images/hot-sleeper.svg'); ?>" width="36" height="36">
                                <span class="tooltip-text">Perfect for hot sleepers</span>
                            </li>
                            <li class="features-item night-sleeper" >
                                <img src="<?php echo esc_url($themes_uri . '/images/night-sleeper.svg'); ?>" width="36" height="36">
                                <span class="tooltip-text">Perfect for night sleepers</span>
                            </li>
                            <li class="features-item temp-sleeper">
                                <img src="<?php echo esc_url($themes_uri . '/images/temp-sleeper.svg'); ?>" width="36" height="36">
                                <span class="tooltip-text">Works with all temp</span>
                            </li>
                        </ul>
                        <span class="price">
                            <?php if ($sale_price && $sale_price < $regular_price) : ?>
                                <s>€<?php echo esc_html(mattress_safe_number_format($regular_price, 2)); ?></s>
                                €<?php echo esc_html(mattress_safe_number_format($sale_price, 2)); ?>
                            <?php else : ?>
                                €<?php echo esc_html(mattress_safe_number_format($current_price, 2)); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </a>
            
            <?php endwhile; ?>
        </div>
        
        <?php
    } else {
        // Default structure for other categories
        ?>
        <div class="products-grid">
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                $current_price = $product->get_price();
                $product_url = get_permalink($product_id);
            ?>
            
            <!-- If none of the category based structure is there then use the default structure -->
            <div class="product-item">
                <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                <h3><?php echo esc_html($title); ?></h3>
                <p><?php echo esc_html($description); ?></p>
                <span class="price">€<?php echo esc_html(mattress_safe_number_format($current_price, 2)); ?></span>
                <a href="<?php echo esc_url($product_url); ?>" class="view-button">View</a>
            </div>
            
            <?php endwhile; ?>
        </div>
        
        <?php
    }
    
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('mattress_product_filter', 'mattress_product_filter_shortcode');

// AJAX handler for filtering mattress products
add_action('wp_ajax_filter_mattress_products', 'handle_filter_mattress_products_ajax');
add_action('wp_ajax_nopriv_filter_mattress_products', 'handle_filter_mattress_products_ajax');

function handle_filter_mattress_products_ajax() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'filter_mattress_products')) {
        wp_die('Security check failed');
    }
    
    $category_id = intval($_POST['category_id']);
    $posts_per_page = intval($_POST['posts_per_page']);
    
    // Build tax query
    $tax_query = array();
    
    // Add category filter
    if ($category_id) {
        $tax_query[] = array(
            'taxonomy' => 'product_cat',
            'field' => 'term_id',
            'terms' => $category_id
        );
    }
    
    // Get category info to determine filter types
    $category_term = get_term($category_id, 'product_cat');
    $category_slug = $category_term ? $category_term->slug : '';
    
    // Add filter parameters based on category
    if ($category_slug === 'mattresses') {
        // Mattress filters
        if (!empty($_POST['mattress_type']) && is_array($_POST['mattress_type'])) {
            $tax_query[] = array(
                'taxonomy' => 'mattress_type',
                'field' => 'term_id',
                'terms' => array_map('intval', $_POST['mattress_type']),
                'operator' => 'IN'
            );
        }
        
        if (!empty($_POST['mattress_size']) && is_array($_POST['mattress_size'])) {
            $tax_query[] = array(
                'taxonomy' => 'mattress_size',
                'field' => 'term_id',
                'terms' => array_map('intval', $_POST['mattress_size']),
                'operator' => 'IN'
            );
        }
        
        if (!empty($_POST['product_brand']) && is_array($_POST['product_brand'])) {
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field' => 'term_id',
                'terms' => array_map('intval', $_POST['product_brand']),
                'operator' => 'IN'
            );
        }
    } elseif ($category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) {
        // Bed frame filters
        if (!empty($_POST['bedframe_size']) && is_array($_POST['bedframe_size'])) {
            $tax_query[] = array(
                'taxonomy' => 'bedframe_size',
                'field' => 'term_id',
                'terms' => array_map('intval', $_POST['bedframe_size']),
                'operator' => 'IN'
            );
        }
    }
    
    // Firmness level filter (for both mattresses and bed frames)
    if (!empty($_POST['firmness_level']) && is_array($_POST['firmness_level'])) {
        $tax_query[] = array(
            'taxonomy' => 'firmness_level',
            'field' => 'term_id',
            'terms' => array_map('intval', $_POST['firmness_level']),
            'operator' => 'IN'
        );
    }
    
    // Query products
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => $posts_per_page,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    
    if (!empty($tax_query)) {
        $args['tax_query'] = $tax_query;
    }
    
    $products = new WP_Query($args);
    
    if (!$products->have_posts()) {
        $no_products_message = '<div class="no-products-message" style="text-align: center; padding: 60px 20px; color: #666;">
            <h3 style="margin-bottom: 15px; color: #222D5A;">Nothing at the moment in this filter</h3>
            <p style="margin-bottom: 0; font-size: 16px;">Please try different filter combinations to find the perfect mattress for you.</p>
        </div>';
        wp_send_json_success($no_products_message);
        return;
    }
    $themes_uri = get_stylesheet_directory_uri();
    ob_start();
    
    // Determine which structure to use based on category
    if ($category_slug === 'bed-frames' || strpos($category_slug, 'bed') !== false || strpos($category_slug, 'frame') !== false) {
        // Bed frame structure
        ?>
        <!-- <div class="featured-collection"> -->
            <?php while ($products->have_posts()) : $products->the_post(); 
                global $product;
                $product_id = get_the_ID();
                
                // Get product data
                $title = get_the_title();
                $description = get_the_excerpt();
                $image = get_the_post_thumbnail_url($product_id, 'medium');
                if (!$image) {
                    $image = get_stylesheet_directory_uri() . '/images/product-image.png';
                }
                
                // Get price
                $current_price = $product->get_price();
                $regular_price = $product->get_regular_price();
                $sale_price = $product->get_sale_price();
                
                // Get product URL
                $product_url = get_permalink($product_id);
            ?>
            
            <div class="product-card">
                <?php 
                    // Check if product is NEW (added in last 30 days)
                    $product_date = get_the_date('Y-m-d', $product_id);
                    $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
                    $is_new = ($product_date >= $thirty_days_ago);
                    
                    // Check if product is on SALE
                    $is_sale = ($sale_price && $sale_price < $regular_price);
                ?>
                <?php if( $is_new || $is_sale) : ?>
                    <div class="badge_overlay">
                        <?php if ($is_new) : ?>
                            <span class="product-label new-label sale_badge">NEW</span>
                        <?php endif; ?>

                        <?php if ($is_sale) : ?>
                            <span class="product-label sale-label off_badge">SALE</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="shop-featured-img">
                    <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
                    <h3><?php echo esc_html($title); ?></h3>
                    <a href="<?php echo esc_url($product_url); ?>" class="hidden-link"></a>
                </div>
            </div>
            
            <?php endwhile; ?>
        <!-- </div> -->
        <?php
    } else {
        // Mattress structure
        ?>
        <?php while ($products->have_posts()) : $products->the_post(); 
            global $product;
            $product_id = get_the_ID();
            
            // Get product data
            $title = get_the_title();
            $description = get_the_excerpt();
            $image = get_the_post_thumbnail_url($product_id, 'medium');
            
            // Get prices
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();
            $current_price = $product->get_price();
            
            // Get mattress sizes for product code
            $sizes = wp_get_post_terms($product_id, 'mattress_size');
            $product_code = '';
            if (!empty($sizes) && !is_wp_error($sizes)) {
                $first_size = $sizes[0];
                $size_dimensions = get_term_meta($first_size->term_id, 'size_dimensions', true);
                $product_code = $size_dimensions ? $size_dimensions : $first_size->name;
            }
                        
            // Get product categories
            $product_cats = wp_get_post_terms($product_id, 'product_cat');
            // Get subcategories
            $subcategories = array();
            foreach ($product_cats as $cat) {
                if ($cat->parent != 0) {
                    $subcategories[] = $cat->name;
                }
            }
            if (!empty($subcategories)) {
                $collection = implode(', ', $subcategories);
            }
        ?>
        
        <!-- Mattress Product Card -->
        <a class="mattress-product-card" href="<?php echo get_permalink( $product_id );?>">
            <div class="product-top-info">
                <h2 class="product-title"><?php echo esc_html($title); ?></h2>
                <?php if (isset($collection) && $collection) : ?>
                    <span class="collection"><?php echo esc_html($collection); ?></span>
                <?php endif; ?>
                <?php if ($description) : ?>
                    <p class="product-desc"><?php echo esc_html($description); ?></p>
                <?php endif; ?>
            </div>
            
            <img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>">
            
            <div class="product-bottom-info">
                <div class="product-features">
                        <ul class="features-list">
                            <li class="features-item hot-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/hot-sleeper.svg" alt="Feature 1" width="36" height="36">
                                <span class="tooltip-text">Perfect for hot sleepers</span>
                            </li>
                            <li class="features-item night-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/night-sleeper.svg" alt="Feature 2" width="36" height="36">
                                <span class="tooltip-text">Perfect for night sleepers</span>
                            </li>
                            <li class="features-item temp-sleeper">
                                <img src="<?php echo $themes_uri; ?>/images/temp-sleeper.svg" alt="Feature 3" width="36" height="36">
                                <span class="tooltip-text">Works with all temp</span>
                            </li>
                        </ul>
                </div>
                
                <div class="product-details">
                    <span class="from">From:</span>
                    <div class="product-price">
                        <?php if ($sale_price && $sale_price < $regular_price) : ?>
                            <s class="regular-price">€<?php echo esc_html(mattress_safe_number_format($regular_price, 2)); ?></s>
                            <span class="sale-price">€<?php echo esc_html(mattress_safe_number_format($sale_price, 2)); ?></span>
                        <?php else : ?>
                            <span class="price">€<?php echo esc_html(mattress_safe_number_format($current_price, 2)); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if ($product_code) : ?>
                        <span class="product-code"><?php echo esc_html($product_code); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        
        <?php endwhile; ?>
        <?php
    }
    
    wp_reset_postdata();
    
    $output = ob_get_clean();
    wp_send_json_success($output);
}



// ========================================
// Shortcode functions for the new widgets
// ========================================

// Product Feature Image Shortcode
function product_feature_image_shortcode($atts) {
    $atts = shortcode_atts(array(
        'image_size' => 'full',
    ), $atts);
    
    $output = '<div class="product-feature-image-widget">';
    
    // Get the current product's featured image
    if (has_post_thumbnail()) {
        $image_id = get_post_thumbnail_id();
        $image_url = wp_get_attachment_image_url($image_id, $atts['image_size']);
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        if ($image_url) {
            $output .= '<div class="gallery_slider">';
            $output .= '<div class="slide-item">';
            $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
            $output .= '</div>';
            $output .= '</div>';
        }
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_feature_image', 'product_feature_image_shortcode');

// Product Features Icons Shortcode
function product_features_icons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'feature_items' => '',
        'layout_style' => 'horizontal',
        'icon_size' => '24',
    ), $atts);
    
    $output = '<div class="product-features-icons-widget layout-' . esc_attr($atts['layout_style']) . '">';
    $output .= '<ul class="featured_icon">';
    
    if (!empty($atts['feature_items'])) {
        $feature_items = vc_param_group_parse_atts($atts['feature_items']);
        
        if (is_array($feature_items)) {
            foreach ($feature_items as $item) {
                if (!empty($item['icon']) && !empty($item['text'])) {
                    $icon_url = wp_get_attachment_image_url($item['icon'], 'full');
                    $icon_alt = get_post_meta($item['icon'], '_wp_attachment_image_alt', true);
                    
                    if ($icon_url) {
                        $output .= '<li>';
                        $output .= '<img src="' . esc_url($icon_url) . '" alt="' . esc_attr($icon_alt) . '" style="width: ' . esc_attr($atts['icon_size']) . 'px; height: auto;">';
                        $output .= esc_html($item['text']);
                        $output .= '</li>';
                    }
                }
            }
        }
    }
    
    $output .= '</ul>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_features_icons', 'product_features_icons_shortcode');

// ========================================
// Shortcode functions for the new widgets
// ========================================

// Product Offer Banner Shortcode
function product_offer_banner_shortcode($atts) {
    $atts = shortcode_atts(array(
        'background_image' => '',
        'button_text' => 'SPRING DEALS',
        'title' => 'Up to 20% off on mattresses!*',
        'subtitle' => '',
    ), $atts);
    
    $output = '<div class="offer_section">';
    
    // Add background image if provided
    if (!empty($atts['background_image'])) {
        $image_url = wp_get_attachment_image_url($atts['background_image'], 'full');
        if ($image_url) {
            $output .= '<div class="offer-background" style="background-image: url(' . esc_url($image_url) . ');"></div>';
        }
    }
    
    $output .= '<a href="#" class="nectar-button offer-btn">' . esc_html($atts['button_text']) . '</a>';
    $output .= '<h4>' . esc_html($atts['title']) . '</h4>';
    
    if (!empty($atts['subtitle'])) {
        $output .= '<p>' . esc_html($atts['subtitle']) . '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_offer_banner', 'product_offer_banner_shortcode');

// Product Base Details Shortcode
function product_base_details_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_title' => 'yes',
        'show_description' => 'yes',
        'show_firmness' => 'yes',
        'show_sizes' => 'yes',
        'show_bedframe_sizes' => 'yes',
        'stock_text' => 'In Stock - Delivered within 3-5 days',
    ), $atts);
    
    $output = '<div class="product_details">';
    
    // Show title if checkbox is selected
    if ($atts['show_title'] === 'yes') {
        $title = get_the_title();
        if (!empty($title)) {
            $output .= '<h3>' . esc_html($title) . '</h3>';
        }
    }
    
    // Show firmness level only for mattress products
    if ($atts['show_firmness'] === 'yes') {
        $product_id = get_the_ID();
        $firmness_cats = wp_get_post_terms($product_id, 'firmness_level');
        
        if (!empty($firmness_cats)) {
            $firmness_level = '';
            foreach ($firmness_cats as $firmness_cat) {
                    $firmness_level = $firmness_cat->name;
                    break;
            }
            if (!empty($firmness_level)) { 
                $output .= '<span class="tag_size">Firmness level: <span>' . esc_html($firmness_level) . '</span></span>';
            }
        }
    }
    
    // Show description if checkbox is selected
    if ($atts['show_description'] === 'yes') {
        $description = get_the_excerpt();
        if (empty($description)) {
            $description = get_the_content();
        }
        if (!empty($description)) {
            $output .= '<p>' . esc_html(wp_trim_words($description, 30)) . '</p>';
        }
    }
    
    // Show sizes only for mattress products
    if ($atts['show_sizes'] === 'yes') {
        $product_id = get_the_ID();
        $product_cats = wp_get_post_terms($product_id, 'product_cat');
        $is_mattress = false;
        
        foreach ($product_cats as $cat) {
            if (strpos(strtolower($cat->name), 'mattress') !== false || $cat->slug === 'mattresses') {
                $is_mattress = true;
                break;
            }
        }
        
        if ($is_mattress) {
            $output .= '<div class="quantity">';
            $output .= '<p>Select Size <span>Size guide</span></p>';
            $output .= '<select name="mattress_size">';
            
            // Get mattress sizes from taxonomy
            $sizes = wp_get_post_terms($product_id, 'mattress_size');
            
            if (!empty($sizes) && !is_wp_error($sizes)) {
                foreach ($sizes as $size) {
                    $size_dimensions = get_term_meta($size->term_id, 'size_dimensions', true);
                    // Format: "Double - 135cm x 190cm"
                    $display_text = $size->name;
                    if (!empty($size_dimensions)) {
                        $display_text .= ' - ' . $size_dimensions;
                    }
                    
                    $output .= '<option value="' . esc_attr($size->slug) . '">' . esc_html($display_text) . '</option>';
                }
            }
            
            $output .= '</select>';
            $output .= '<button type="submit" class="nectar-button">REQUEST A QUOTE</button>';
            $output .= '</div>';
        }
    }

    // Show sizes only for bedframe products
    if ($atts['show_bedframe_sizes'] === 'yes') {
        $product_id = get_the_ID();
        $product_cats = wp_get_post_terms($product_id, 'product_cat');
        $is_bedframe = false;
        
        foreach ($product_cats as $cat) {
            if (strpos(strtolower($cat->name), 'bed-frames') !== false || $cat->slug === 'bed-frames') {
                $is_bedframe = true;
                break;
            }
        }
        
        if ($is_bedframe) {
            $output .= '<div class="quantity">';
            $output .= '<p>Select Size <span>Size guide</span></p>';
            $output .= '<select name="mattress_size">';
            
            // Get mattress sizes from taxonomy
            $sizes = wp_get_post_terms($product_id, 'bedframe_size');
            
            if (!empty($sizes) && !is_wp_error($sizes)) {
                foreach ($sizes as $size) {
                    $size_dimensions = get_term_meta($size->term_id, 'size_dimensions', true);
                    // Format: "Double - 135cm x 190cm"
                    $display_text = $size->name;
                    if (!empty($size_dimensions)) {
                        $display_text .= ' - ' . $size_dimensions;
                    }
                    
                    $output .= '<option value="' . esc_attr($size->slug) . '">' . esc_html($display_text) . '</option>';
                }
            }
            
            $output .= '</select>';
            $output .= '<button type="submit" class="nectar-button">REQUEST A QUOTE</button>';
            $output .= '</div>';
        }
    }
    
    // Show stock status text
    if (!empty($atts['stock_text'])) {
        $output .= '<p class="stock_tag">' . esc_html($atts['stock_text']) . '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_base_details', 'product_base_details_shortcode');

// Product Addons Shortcode
function product_addons_shortcode($atts) {
    $atts = shortcode_atts(array(
        'products_count' => '3',
        'section_title' => 'Properties',
        'learn_more_text' => 'Learn About Our Bed Frames',
        'learn_more_url' => '#',
    ), $atts);
    
    // Check if current post is a product
    if (get_post_type() !== 'product') {
        return '<p>This widget can only be used on product pages.</p>';
    }
    
    $product_id = get_the_ID();
    $product_cats = wp_get_post_terms($product_id, 'product_cat');
    
    if (empty($product_cats) || is_wp_error($product_cats)) {
        return '<p>No product category found.</p>';
    }
    
    // Get the first category to find related products
    $main_category = $product_cats[0];
    
    // Query related products from the same category
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => intval($atts['products_count']),
        'post__not_in' => array($product_id), // Exclude current product
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $main_category->term_id,
            ),
        ),
        'orderby' => 'menu_order',
        'order' => 'ASC',
    );
    
    $related_products = new WP_Query($args);
    
    if (!$related_products->have_posts()) {
        return '<p>No related products found in this category.</p>';
    }
    
    $output = '<div class="addons_product">';
    $output .= '<h5><span>' . esc_html($atts['section_title']) . '</span></h5>';
    $output .= '<div class="addons_product_list">';
    
    while ($related_products->have_posts()) {
        $related_products->the_post();
        global $product;
        
        $product_id = get_the_ID();
        $product_title = get_the_title();
        $product_price = $product->get_price();
        $product_image = get_the_post_thumbnail_url($product_id, 'medium');
        
        if (!$product_image) {
            $product_image = get_stylesheet_directory_uri() . '/images/product-image.png';
        }
        
        // Get product badge (you can customize this logic)
        $badge_text = ''; // Default badge
        $product_date = get_the_date('Y-m-d', $product_id);
        $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
        
        if ($product_date >= $thirty_days_ago) {
            $badge_text = 'New Arrival';
        } elseif ($product->is_on_sale()) {
            $badge_text = 'On Sale';
        }
        
        $output .= '<div class="addons_product_card">';
        if (!empty($badge_text)) {
            $output .= '<span class="badge_tag">' . esc_html($badge_text) . '</span>';
        }
        $output .= '<div class="addons_card">';
        $output .= '<div class="addons_img" style="max-width: 75px;">';
        $output .= '<img src="' . esc_url($product_image) . '" alt="' . esc_attr($product_title) . '">';
        $output .= '</div>';
        $output .= '<div class="addons_content">';
        $output .= '<div>';
        $output .= '<h6>' . esc_html($product_title) . '</h6>';
        $output .= '<p>€' . esc_html(mattress_safe_number_format($product_price, 2)) . '</p>';
        $output .= '</div>';
        $output .= '</div>'; // Removed the Add button as requested
        $output .= '</div>';
        $output .= '</div>';
    }
    
    wp_reset_postdata();
    
    $output .= '</div>';
    
    if (!empty($atts['learn_more_text']) && !empty($atts['learn_more_url'])) {
        $output .= '<p class="learn_more">';
        $output .= '<a href="' . esc_url($atts['learn_more_url']) . '">' . esc_html($atts['learn_more_text']) . '</a>';
        $output .= '</p>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('product_addons', 'product_addons_shortcode');

// Product Image Gallery Shortcode
function product_image_gallery_shortcode($atts) {
    $atts = shortcode_atts(array(), $atts);
    $image_size = 'large'; // Default image size
    
    // Check if current post is a product
    if (get_post_type() !== 'product') {
        return '<p>This widget can only be used on product pages.</p>';
    }
    
    $product_id = get_the_ID();
    global $product;

    if (empty($product)) { return; }

    $gallery_image_ids = $product->get_gallery_image_ids();
    
    if (!$gallery_image_ids) {
        return;
    }
    
    if (empty($gallery_image_ids)) {
        return '<p>No product images found.</p>';
    }
    
    $output = '';
    $image_count = count($gallery_image_ids);
    
    // Process images in pairs for the alternating layout
    for ($i = 0; $i < $image_count; $i += 2) {
        $output .= '<div class="product_images">';
        
        // First image in the pair
        if (isset($gallery_image_ids[$i])) {
            $first_image_url = wp_get_attachment_image_url($gallery_image_ids[$i], $image_size);
            $first_image_alt = get_post_meta($gallery_image_ids[$i], '_wp_attachment_image_alt', true);
            
            if ($first_image_url) {
                $output .= '<div class="card_img width-54">';
                $output .= '<img src="' . esc_url($first_image_url) . '" alt="' . esc_attr($first_image_alt) . '">';
                $output .= '</div>';
            }
        }
        
        // Second image in the pair
        if (isset($gallery_image_ids[$i + 1])) {
            $second_image_url = wp_get_attachment_image_url($gallery_image_ids[$i + 1], $image_size);
            $second_image_alt = get_post_meta($gallery_image_ids[$i + 1], '_wp_attachment_image_alt', true);
            
            if ($second_image_url) {
                $output .= '<div class="card_img width-43">';
                $output .= '<img src="' . esc_url($second_image_url) . '" alt="' . esc_attr($second_image_alt) . '">';
                $output .= '</div>';
            }
        }
        
        $output .= '</div>';
        
        // If there are more images, create the next row with reversed widths
        if (isset($gallery_image_ids[$i + 2])) {
            $output .= '<div class="product_images">';
            
            // Third image in the group
            if (isset($gallery_image_ids[$i + 2])) {
                $third_image_url = wp_get_attachment_image_url($gallery_image_ids[$i + 2], $image_size);
                $third_image_alt = get_post_meta($gallery_image_ids[$i + 2], '_wp_attachment_image_alt', true);
                
                if ($third_image_url) {
                    $output .= '<div class="card_img width-42">';
                    $output .= '<img src="' . esc_url($third_image_url) . '" alt="' . esc_attr($third_image_alt) . '">';
                    $output .= '</div>';
                }
            }
            
            // Fourth image in the group
            if (isset($gallery_image_ids[$i + 3])) {
                $fourth_image_url = wp_get_attachment_image_url($gallery_image_ids[$i + 3], $image_size);
                $fourth_image_alt = get_post_meta($gallery_image_ids[$i + 3], '_wp_attachment_image_alt', true);
                
                if ($fourth_image_url) {
                    $output .= '<div class="card_img width-54">';
                    $output .= '<img src="' . esc_url($fourth_image_url) . '" alt="' . esc_attr($fourth_image_alt) . '">';
                    $output .= '</div>';
                }
            }
            
            $output .= '</div>';
            
            // Skip the next iteration since we've processed 4 images
            $i += 2;
        }
    }
    
    return $output;
}
add_shortcode('product_image_gallery', 'product_image_gallery_shortcode');

// Our Most Loved Products Shortcode
function our_most_loved_products_shortcode($atts) {
    $atts = shortcode_atts(array(
        'selected_products' => '',
        'show_view_all_button' => 'yes',
        'button_text' => 'View All Products',
        'button_url' => '#',
    ), $atts);
    
    if (empty($atts['selected_products'])) {
        return '<p>No products selected.</p>';
    }
    
    // Convert comma-separated product IDs to array
    $product_ids = explode(',', $atts['selected_products']);
    
    $output = '<div class="our-most-loved-sleep-systems-wrapper">';
    
    foreach ($product_ids as $product_id) {
        $product_id = trim($product_id);
        if (empty($product_id)) continue;
        
        $product = wc_get_product($product_id);
        if (!$product) continue;
        
        $product_title = $product->get_name();
        $product_price = $product->get_price();
        $product_image = get_the_post_thumbnail_url($product_id, 'medium');

        $product_url = get_permalink($product_id);
        
        if (!$product_image) {
            $product_image = get_stylesheet_directory_uri() . '/images/sleep-systems.png';
        }
        
        // Determine badge type dynamically
        $badge_html = '';
        $product_date = get_the_date('Y-m-d', $product_id);
        $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
        
        if ($product_date >= $thirty_days_ago) {
            $badge_html = '<div class="badge_overlay"><span class="sale_badge">NEW</span></div>';
        } elseif ($product->is_on_sale()) {
            $badge_html = '<div class="badge_overlay"><span class="off_badge">Sale</span></div>';
        } elseif ($product->get_total_stock() > 0 && $product->get_total_stock() < 10) {
            $badge_html = '<div class="badge_overlay"><span class="sale_badge">BESTSELLER</span></div>';
        }
        
        $output .= '<div class="sleep-systems-item">';
        $output .= '<div class="sleep-systems-item-inner">';
        $output .= '<div class="sleep-systems-item-top">';
        
        if (!empty($badge_html)) {
            $output .= $badge_html;
        }
        
        $output .= '<img src="' . esc_url($product_image) . '" alt="' . esc_attr($product_title) . '">';
        $output .= '</div>';
        $output .= '<div class="sleep-systems-item-bottom">';
        $output .= '<div class="sleep-systems-item-top-text-title">';
        $output .= '<h4 class="sleep-systems-item-title">' . esc_html($product_title) . '</h4>';
        $output .= '<span class="item-price">€' . esc_html(mattress_safe_number_format($product_price, 2)) . '</span>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<a class="sleep-link" href="'.$product_url.'"></a>';
        $output .= '</div>';
        $output .= '</div>';
        
        wp_reset_postdata();
    }
    
    $output .= '</div>';
    
    // Navigation arrows - hardcoded with dynamic domain
    $output .= '<div class="arrows_section">';
    $output .= '<span class="prev"><img src="' . esc_url(site_url('/wp-content/themes/salient-child/images/arrow.png')) . '" alt="Previous"></span>';
    $output .= '<span class="next"><img src="' . esc_url(site_url('/wp-content/themes/salient-child/images/arrow.png')) . '" alt="Next"></span>';
    $output .= '</div>';
    
    // Button section - only show if checkbox is checked
    if ($atts['show_view_all_button'] === 'yes' && !empty($atts['button_text']) && !empty($atts['button_url'])) {
        $output .= '<div class="view-all-products-section">';
        $output .= '<a href="' . esc_url($atts['button_url']) . '" class="view-all-products-btn">' . esc_html($atts['button_text']) . '</a>';
        $output .= '</div>';
    }
    
    return $output;
}
add_shortcode('our_most_loved_products', 'our_most_loved_products_shortcode');

// Scroll Gallery Banner Shortcode
function scroll_gallery_banner_shortcode($atts) {
    $atts = shortcode_atts(array(
        'widget_title' => 'Product Gallery',
    ), $atts);

    // Check if current post is a product
    if (get_post_type() !== 'product') {
        return '<p>This widget can only be used on product pages.</p>';
    }

    $product_id = get_the_ID();
    $product = wc_get_product($product_id);

    if (!$product) {
        return '<p>Product not found.</p>';
    }

    // Get featured image
    $featured_image_id = get_post_thumbnail_id($product_id);
    $featured_image_url = '';

    if ($featured_image_id) {
        $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'large');
    } else {
        // Fallback to default image if no featured image
        $featured_image_url = '/wp-content/uploads/2025/08/Hero-Background-1.jpg';
    }

    // Get gallery images
    $gallery_image_ids = $product->get_gallery_image_ids();

    // Start output using gallery_thumb structure
    $output = '<div class="gallery_slider gallery-with-thumbs">
        <div class="swiper gallery_large">
            <div class="swiper-wrapper">';

    // Featured image first
    $output .= '<div class="swiper-slide">
        <img src="' . esc_url($featured_image_url) . '" />
    </div>';

    // Gallery images
    if (!empty($gallery_image_ids)) {
        foreach ($gallery_image_ids as $gallery_image_id) {
            $gallery_image_url = wp_get_attachment_image_url($gallery_image_id, 'large');
            if ($gallery_image_url) {
                $output .= '<div class="swiper-slide">
                    <img src="' . esc_url($gallery_image_url) . '" />
                </div>';
            }
        }
    }

    $output .= '</div>
        </div>
        <div thumbsSlider="" class="swiper gallery_thumb">
            <div class="swiper-wrapper">';

    // Featured image in thumbs
    $output .= '<div class="swiper-slide">
        <img src="' . esc_url($featured_image_url) . '" />
    </div>';

    // Gallery images in thumbs
    if (!empty($gallery_image_ids)) {
        foreach ($gallery_image_ids as $gallery_image_id) {
            $gallery_image_url = wp_get_attachment_image_url($gallery_image_id, 'large');
            if ($gallery_image_url) {
                $output .= '<div class="swiper-slide">
                    <img src="' . esc_url($gallery_image_url) . '" />
                </div>';
            }
        }
    }

    $output .= '</div>
        </div>
    </div>';

    return $output;
}
add_shortcode('scroll_gallery_banner', 'scroll_gallery_banner_shortcode');

// Offer's Product Widget Shortcode
function offer_products_shortcode($atts) {
    $atts = shortcode_atts(array(
        'product_category' => '',
        'contact_button_text' => 'Contact Us',
        'contact_button_url' => '#',
    ), $atts);
    
    if (empty($atts['product_category'])) {
        return '<p>Please select a product category.</p>';
    }
    
    // Query sale products from selected category
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $atts['product_category'],
            ),
        ),
        'meta_query' => array(
            array(
                'key' => '_sale_price',
                'value' => '',
                'compare' => '!='
            )
        )
    );
    
    $sale_products = new WP_Query($args);
    
    if (!$sale_products->have_posts()) {
        return '<p>No sale products found in the selected category.</p>';
    }
    
    $output = '<div class="product-list">';
    
    while ($sale_products->have_posts()) {
        $sale_products->the_post();
        global $product;
        $product_id = get_the_ID();
        
        // Get product data
        $product_title = get_the_title();
        $featured_image = get_the_post_thumbnail_url($product_id, 'medium');
        
        // Fallback to default image if no featured image
        if (empty($featured_image)) {
            $featured_image = '/wp-content/uploads/2025/08/Rectangle-119.jpg';
        }
        
        // Get image alt text
        $image_alt = get_post_meta(get_post_thumbnail_id($product_id), '_wp_attachment_image_alt', true);
        if (empty($image_alt)) {
            $image_alt = esc_attr($product_title);
        }
        
        // Get prices
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
        
        // Calculate discount percentage
        $discount_percentage = '';
        if ($regular_price && $sale_price && $regular_price > $sale_price) {
            $discount = (($regular_price - $sale_price) / $regular_price) * 100;
            $discount_percentage = round($discount) . '% OFF';
        }
        
        // Check if product is bestseller (you can customize this logic)
        $is_bestseller = false;
        $product_date = get_the_date('Y-m-d', $product_id);
        $thirty_days_ago = date('Y-m-d', strtotime('-30 days'));
        
        // Consider it bestseller if it's new (added in last 30 days) or has high stock
        if ($product_date >= $thirty_days_ago || $product->get_total_stock() > 10) {
            $is_bestseller = true;
        }
        
        $output .= '<div class="product-item">';
        $output .= '<div class="featured-image">';
        $output .= '<img src="' . esc_url($featured_image) . '" alt="' . esc_attr($image_alt) . '" style="width: 100%; height: auto; object-fit: cover;">';
        $output .= '<div class="badge_overlay">';
        
        // Add bestseller badge (yellow)
        if ($is_bestseller) {
            $output .= '<span class="sale_badge">BESTSELLER</span>';
        }
        
        // Add discount badge (red)
        if ($discount_percentage) {
            $output .= '<span class="off_badge">' . esc_html($discount_percentage) . '</span>';
        }
        
        $output .= '</div>';
        $output .= '</div>';
        $output .= '<div class="product-info">';
        $output .= '<h3>' . esc_html($product_title) . '</h3>';
        $output .= '<p class="price">From <span>€' . esc_html(mattress_safe_number_format($sale_price, 2)) . '</span>';
        
        if ($regular_price && $regular_price > $sale_price) {
            $output .= ' <del>€' . esc_html(mattress_safe_number_format($regular_price, 2)) . '</del>';
        }
        
        $output .= '</p>';
        $output .= '<a href="' . esc_url($atts['contact_button_url']) . '" class="nectar-button">' . esc_html($atts['contact_button_text']) . '</a>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    wp_reset_postdata();
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('offer_products', 'offer_products_shortcode');


// Sleep Culture Products Widget Shortcode
function sleep_culture_products_shortcode($atts) {
    $atts = shortcode_atts(array(
        'selected_products' => '',
    ), $atts);
    
    if (empty($atts['selected_products'])) {
        return '<p>No products selected.</p>';
    }
    
    // Convert comma-separated product IDs to array
    $product_ids = explode(',', $atts['selected_products']);
    
    $output = '<div class="featured-collection sleep_culture_product">';
    
    foreach ($product_ids as $product_id) {
        $product_id = trim($product_id);
        if (empty($product_id)) continue;
        
        $product = wc_get_product($product_id);
        if (!$product) continue;
        
        // Get product data
        $product_title = $product->get_name();
        $product_description = $product->get_short_description();
        
        // If no short description, get excerpt or content
        if (empty($product_description)) {
            $product_description = get_the_excerpt($product_id);
        }
        if (empty($product_description)) {
            $product_description = wp_trim_words(get_the_content($product_id), 20);
        }
        
        // Get featured image
        $featured_image = get_the_post_thumbnail_url($product_id, 'medium');
        
        // Fallback to default image if no featured image
        if (empty($featured_image)) {
            $featured_image = '/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg';
        }
        
        // Get image alt text
        $image_alt = get_post_meta(get_post_thumbnail_id($product_id), '_wp_attachment_image_alt', true);
        if (empty($image_alt)) {
            $image_alt = esc_attr($product_title);
        }

        // Get product URL
        $product_url = get_permalink($product_id);
        
        $output .= '<div class="product-card">';
        $output .= '<div class="shop-featured-img">';
        $output .= '<img src="' . esc_url($featured_image) . '" alt="' . esc_attr($image_alt) . '">';
        $output .= '</div>';
        $output .= '<div class="shop-product-info">';
        $output .= '<h3>' . esc_html($product_title) . '</h3>';
        $output .= '<p>' . esc_html($product_description) . '</p>';
        $output .= '</div>';
        $output .= '<a href="'.esc_url($product_url).'" class="shp-prdt-lnk"></a>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('sleep_culture_products', 'sleep_culture_products_shortcode');

// Mattress Post Selection with Description Widget
function mattress_post_selection_with_description_shortcode($atts) {
    $atts = shortcode_atts(array(
        'selected_posts' => '',
    ), $atts);
    
    if (empty($atts['selected_posts'])) {
        return '<p>No posts selected.</p>';
    }
    
    // Convert comma-separated post IDs to array
    $post_ids = explode(',', $atts['selected_posts']);
    $post_ids = array_filter(array_map('trim', $post_ids)); // Clean and filter IDs
    
    if (empty($post_ids)) {
        return '<p>No valid posts selected.</p>';
    }
    
    // Generate unique ID for this widget instance
    $widget_id = 'mattress_posts_' . uniqid();
    
    $output = '<div class="featured-collection sleep_culture_product_v2" id="' . esc_attr($widget_id) . '">';
    
    // Show first 4 posts initially
    $posts_to_show = array_slice($post_ids, 0, 4);
    $remaining_posts = array_slice($post_ids, 4);
    
    foreach ($posts_to_show as $index => $post_id) {
        $post = get_post($post_id);
        if (!$post) continue;
        
        $output .= mattress_post_selection_render_post_card($post_id, $index);
    }
    
    // Add hidden posts for load more functionality
    if (!empty($remaining_posts)) {
        $output .= '<div class="hidden-posts" style="display: none;">';
        foreach ($remaining_posts as $index => $post_id) {
            $post = get_post($post_id);
            if (!$post) continue;
            
            $output .= mattress_post_selection_render_post_card($post_id, $index + 4);
        }
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    // Add load more button outside the main container
    if (!empty($remaining_posts)) {
        $output .= '<div class="load-more-container" style="text-align: center; margin-top: 30px;">';
        $output .= '<a href="#" class="load-more-btn text-link" data-widget-id="' . esc_attr($widget_id) . '" data-posts-per-load="4">Load More</a>';
        $output .= '</div>';
    }
    
    // Add JavaScript for load more functionality (only once per page)
    static $script_loaded = false;
    if (!$script_loaded) {
        $output .= '<script>
        jQuery(document).ready(function($) {
            $(document).on("click", ".load-more-btn", function(e) {
                e.preventDefault();
                
                var widgetId = $(this).data("widget-id");
                var postsPerLoad = $(this).data("posts-per-load");
                
                var widget = $("#" + widgetId);
                var hiddenPosts = widget.find(".hidden-posts");
                var loadMoreBtn = $(this).closest(".load-more-container");
                
                // Get next batch of posts
                var nextPosts = hiddenPosts.find(".product-card").slice(0, postsPerLoad);
                
                if (nextPosts.length > 0) {
                    // Clone the posts before moving them
                    var clonedPosts = nextPosts.clone();
                    
                    // Show cloned posts and insert them before hidden container
                    clonedPosts.show();
                    hiddenPosts.before(clonedPosts);
                    
                    // Remove original posts from hidden container
                    nextPosts.remove();
                    
                    // Check if there are more posts to show
                    var remainingPosts = hiddenPosts.find(".product-card").length;
                    
                    if (remainingPosts === 0) {
                        loadMoreBtn.hide();
                    }
                }
            });
        });
        </script>';
        $script_loaded = true;
    }
    
    return $output;
}

// Helper function to render individual post card
function mattress_post_selection_render_post_card($post_id, $index) {
    $post = get_post($post_id);
    if (!$post) return '';
    
    // Get post data
    $post_title = get_the_title($post_id);
    $post_description = get_the_excerpt($post_id);
    
    // If no excerpt, get trimmed content and clean it
    if (empty($post_description)) {
        $post_content = get_the_content($post_id);
        // Strip shortcodes and HTML tags, then trim
        $post_content = strip_shortcodes($post_content);
        $post_content = wp_strip_all_tags($post_content);
        $post_description = wp_trim_words($post_content, 20);
    }

    if( empty($post_description) ) {
        $post_description = '&nbsp;';
    }
    
    // Get featured image
    $featured_image = get_the_post_thumbnail_url($post_id, 'medium');
    
    // Fallback to default image if no featured image
    if (empty($featured_image)) {
        $featured_image = '/wp-content/uploads/2025/08/Rectangle-57-1.jpg';
    }
    
    // Get image alt text
    $image_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
    if (empty($image_alt)) {
        $image_alt = esc_attr($post_title);
    }
    
    // Get post URL
    $post_url = get_permalink($post_id);
    
    $output = '<div class="product-card">';
    $output .= '<div class="shop-featured-img">';
    $output .= '<img src="' . esc_url($featured_image) . '" alt="' . esc_attr($image_alt) . '">';
    $output .= '</div>';
    $output .= '<div class="shop-product-info">';
    $output .= '<h3>' . esc_html($post_title) . '</h3>';
    $output .= '<p>' . esc_html($post_description) . '</p>';
    $output .= '<a href="' . esc_url($post_url) . '" class="text-link">Read more</a>';
    $output .= '</div>';
	$output .= '<a href="'.esc_url($post_url).'" class="rd-mr-lnk"></a>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('mattress_post_selection_with_description', 'mattress_post_selection_with_description_shortcode');

// Mattress Collection Widget Shortcode
function mattresses_collection_product_category_listing_shortcode($atts) {
    $atts = shortcode_atts(array(
        'collection_items' => '',
    ), $atts);
    
    if (empty($atts['collection_items'])) {
        return '<p>No collection items found.</p>';
    }
    
    $collection_items = vc_param_group_parse_atts($atts['collection_items']);
    
    if (!is_array($collection_items) || empty($collection_items)) {
        return '<p>No collection items found.</p>';
    }
    
    $output = '<div class="mattress-collection-grid">';
    
    foreach ($collection_items as $item) {
        if (empty($item['title'])) continue;
        
        // Get image URL
        $image_url = '';
        if (!empty($item['image'])) {
            $image_url = wp_get_attachment_image_url($item['image'], 'full');
        }
        
        // Fallback to default image if no image is set
        if (empty($image_url)) {
            $image_url = get_stylesheet_directory_uri() . '/images/mattress.png';
        }
        
        // Get image alt text
        $image_alt = '';
        if (!empty($item['image'])) {
            $image_alt = get_post_meta($item['image'], '_wp_attachment_image_alt', true);
        }
        if (empty($image_alt)) {
            $image_alt = esc_attr($item['title']);
        }
        
        // Set default values
        $title = !empty($item['title']) ? $item['title'] : 'Collection Item';
        $subtitle = !empty($item['subtitle']) ? $item['subtitle'] : 'Description for this collection item.';
        $link_url = !empty($item['link_url']) ? $item['link_url'] : '#';
        $link_text = !empty($item['link_text']) ? $item['link_text'] : 'Browse Now';
        
        $output .= '<div class="mattress-collection-card">';
        $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
        $output .= '<div class="card-title">' . esc_html($title) . '</div>';
        $output .= '<div class="card-desc">' . esc_html($subtitle) . '</div>';
        $output .= '<a href="' . esc_url($link_url) . '" class="nectar-button medium see-through">' . esc_html($link_text) . '</a>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    
    return $output;
}
add_shortcode('mattresses_collection_product_category_listing', 'mattresses_collection_product_category_listing_shortcode');

// Mattress Collection Trust Slider Widget Shortcode
function mattress_collection_trust_slider_vc_shortcode($atts) {
    $atts = shortcode_atts(array(
        'trust_items' => '',
    ), $atts);
    
    if (empty($atts['trust_items'])) {
        return '<p>No trust items found.</p>';
    }
    
    $trust_items = vc_param_group_parse_atts($atts['trust_items']);
    
    if (!is_array($trust_items) || empty($trust_items)) {
        return '<p>No trust items found.</p>';
    }
    
    $output = '<div class="mattress_collection_trust_wrapper">';
    
    // Add widget title if provided
    if (!empty($atts['widget_title'])) {
        $output .= '<h3 class="widget-title">' . esc_html($atts['widget_title']) . '</h3>';
    }
    
    $output .= '<div class="mattress_collection_trust_slider">';
    
    foreach ($trust_items as $item) {
        if (empty($item['title'])) continue;
        
        // Get image URL
        $image_url = '';
        if (!empty($item['image'])) {
            $image_url = wp_get_attachment_image_url($item['image'], 'full');
        }
        
        // Fallback to default image if no image is set
        if (empty($image_url)) {
            $image_url = get_stylesheet_directory_uri() . '/images/trust-slider.png';
        }
        
        // Get image alt text
        $image_alt = '';
        if (!empty($item['image'])) {
            $image_alt = get_post_meta($item['image'], '_wp_attachment_image_alt', true);
        }
        if (empty($image_alt)) {
            $image_alt = esc_attr($item['title']);
        }
        
        // Set default values
        $title = !empty($item['title']) ? $item['title'] : 'Trust Item';
        $testimonial = !empty($item['testimonial']) ? $item['testimonial'] : 'Description for this trust item.';
        
        $output .= '<div class="slider-item-wrapper">';
        $output .= '<div class="mattress_collection_trust_slider_item">';
        $output .= '<div class="trust-features-image">';
        $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
        $output .= '</div>';
        $output .= '<div class="trust-slider-content">';
        $output .= '<div class="content-top-icons">';
        $output .= '<ul class="features-item">';
        $output .= '<li><img src="' . get_stylesheet_directory_uri() . '/images/medical-plus.png" alt=""></li>';
        $output .= '<li><img src="' . get_stylesheet_directory_uri() . '/images/medical-sign.png" alt=""></li>';
        $output .= '<li><img src="' . get_stylesheet_directory_uri() . '/images/location.png" alt=""></li>';
        $output .= '<li><img src="' . get_stylesheet_directory_uri() . '/images/certificate.png" alt=""></li>';
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '<div class="content-bottom-text">';
        $output .= '<h4 class="title">' . esc_html($title) . '</h4>';
        $output .= '<p class="desc">' . esc_html($testimonial) . '</p>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}
add_shortcode('mattress_collection_trust_slider', 'mattress_collection_trust_slider_vc_shortcode');

// Mattress Sleep Smart Slider Widget Shortcode
function mattresses_post_selection_vc_shortcode($atts) {
    $atts = shortcode_atts(array(
        'selected_posts' => '',
        'read_more_text' => 'Read more >',
    ), $atts);
    
    if (empty($atts['selected_posts'])) {
        return '<p>No posts selected.</p>';
    }
    
    // Convert comma-separated post IDs to array
    $post_ids = explode(',', $atts['selected_posts']);
    
    $output = '<div class="mattress_sleep_smart_wrapper">';
    $output .= '<div class="mattress_sleep_smart_slider">';
    
    foreach ($post_ids as $post_id) {
        $post_id = trim($post_id);
        if (empty($post_id)) continue;
        
        $post = get_post($post_id);
        if (!$post) continue;
        
        // Get post data
        $post_title = get_the_title($post_id);
        $post_url = get_permalink($post_id);
        $featured_image = get_the_post_thumbnail_url($post_id, 'medium');
        
        // Fallback to default image if no featured image
        if (empty($featured_image)) {
            $image_number = (array_search($post_id, $post_ids) % 3) + 1;
            $featured_image = get_stylesheet_directory_uri() . '/images/post-' . $image_number . '.png';
        }
        
        // Get image alt text
        $image_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
        if (empty($image_alt)) {
            $image_alt = esc_attr($post_title);
        }
        
        $output .= '<div class="sleep-slider-item-wrapper">';
        $output .= '<div class="smart-sleep-item">';
        $output .= '<div class="post-features-image">';
        $output .= '<img src="' . esc_url($featured_image) . '" alt="' . esc_attr($image_alt) . '">';
        $output .= '</div>';
        $output .= '<div class="smart-sleep-content">';
        $output .= '<h4 class="post-title">' . esc_html($post_title) . '</h4>';
        $output .= '<a href="' . esc_url($post_url) . '" target="_blank" class="read-more">' . esc_html($atts['read_more_text']) . '</a>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    return $output;
}
// Remove old shortcode if it exists and register new one
add_shortcode('mattress_post_selection_slider', 'mattresses_post_selection_vc_shortcode');


// Map With marker Shortcode
function map_custom_widget_shortcode($atts) {
    $atts = shortcode_atts(array(
        'map_marker' => '',
    ), $atts);
    
    $output = '<div id="map" style="height: 400px; width: 100%;"></div>
                <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
                <script>
                    var map = L.map("map").setView([35.8847842909664, 14.403376625310996], 11);
                    // Load tiles from OpenStreetMap
                    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
                        maxZoom: 20,
                    }).addTo(map);';
    
    if (!empty($atts['map_marker'])) {
        $map_marker = vc_param_group_parse_atts($atts['map_marker']);
        
        if (is_array($map_marker)) {
            foreach ($map_marker as $marker) {
                if (!empty($marker['marker_latitude']) && !empty($marker['marker_longitude'])) {

                    // Add marker
                    $output .= 'L.marker(['.$marker['marker_latitude'].', '.$marker['marker_longitude'].']).addTo(map)
                        .bindPopup("'.$marker['marker_name'].'")
                        .openPopup();';
                    
                }
            }
        }

    }

    $output .= '</script>';
    
    return $output;
}
add_shortcode('map_custom_widget', 'map_custom_widget_shortcode');


// Testimonial with stars Shortcode
function testimonial_custom_widget_shortcode($atts) {
    $atts = shortcode_atts(array(
        'number_testimonials' => 10,
    ), $atts);

    $args = array(
        'post_type' => 'testimonial',
        'posts_per_page' => $atts['number_testimonials'],
        'post_status' => 'publish',
        'orderby' => 'DATE',
        'order' => 'ASC'
    );
    
    $query = new WP_Query($args);

    $output = '<div class="mattress_happy_customer_wrapper">
                <div class="mattress_happy_customer_slider">';
    
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $testimonial_id = get_the_ID();
            $testimonial_title = get_the_title();

            $description = get_the_excerpt();
            if (empty($description)) {
                $description = get_the_content();
            }
            if (!empty($description)) {
                $description = esc_html(wp_trim_words($description, 30));
            }

            // Get featured image
            $featured_image_id = get_post_thumbnail_id($testimonial_id);
            $featured_image_url = '';

            if ($featured_image_id) {
                $featured_image_url = wp_get_attachment_image_url($featured_image_id, 'large');
            } else {
                // Fallback to default image if no featured image
                $featured_image_url = get_stylesheet_directory_uri().'/images/pillow.png';
            }

            $stars = get_field("stars", $testimonial_id);
            $original_products = get_field("original_products", $testimonial_id); // post id

            $stars_image = "";

            for( $i = 1; $i <= $stars; $i++ ) {
                $stars_image .= '<img src="'.get_stylesheet_directory_uri().'/images/star-rating.png" alt="Star Rating">';
            }

            $product_url = '';
            if (!empty($original_products)) {
                $product_url = get_permalink($original_products);
            }

            $output .= '<div class="slider-item-wrapper">
                    <div class="mattress-happy-customer-slider-item">
                        <div class="product-features-image">
                            <img src="'.$featured_image_url.'" alt="Trust Features Image">
                        </div>
                        <div class="happy-customer-content">
                            <div class="ratings">
                                '.$stars_image.'
                            </div>
                            <div class="customer-review-content">
                                <p class="feedback">'.$description.'</p>
                                <span class="customer-info">
                                    '.$testimonial_title.'
                                </span>';
            if (!empty($product_url)) {
                $output .= '<a href="'.esc_url($product_url).'" target="_blank" rel="noopener noreferrer" class="original-product">View the orginal pillow</a>';
            }
            $output .= '</div>
                        </div>
                    </div>
                </div>';
        }
    }
    
    wp_reset_postdata();

    $output .= '</div>
            </div>';
    
    return $output;
}
add_shortcode('testimonial_custom_widget', 'testimonial_custom_widget_shortcode');

// Shortcode function for bedframe variation option list
function bedframe_variation_option_list_shortcode($atts) {
    $atts = shortcode_atts(array(
        'widget_title' => 'START CUSTOMISING',
        'show_variations' => 'fabric,finish,storage,slats,model,feet',
        'custom_image_url' => '/wp-content/uploads/2025/08/Product-Category-Icon.jpg',
        'forminator_form_id' => ''
    ), $atts);
    
    // Get the current product ID
    global $product;
    $product_id = null;
    
    if (is_product() && $product) {
        $product_id = $product->get_id();
    } elseif (isset($_GET['product_id'])) {
        $product_id = intval($_GET['product_id']);
    }
    
    if (!$product_id) {
        return '<div class="bedframe-variation-error">No product found. This widget should be used on a product page.</div>';
    }
    
    // Get product attributes
    $product_obj = wc_get_product($product_id);
    if (!$product_obj) {
        return '<div class="bedframe-variation-error">Product not found.</div>';
    }
    
    $attributes = $product_obj->get_attributes();
    $selected_variations = explode(',', $atts['show_variations']);
    $custom_image = $atts['custom_image_url'];
    
    
    // Variation mapping
    $variation_mapping = array(
        'fabric' => 'pa_fabric',
        'finish' => 'pa_finish',
        'storage' => 'pa_storage', 
        'slats' => 'pa_slats',
        'model' => 'pa_model',
        'feet' => 'pa_feet'
    );
    
    // Variation display names
    $variation_names = array(
        'fabric' => 'Select an Option',
        'finish' => 'Select Finish',
        'storage' => 'Select Storage Type',
        'slats' => 'Choose Slats',
        'model' => 'Bed Frame Model',
        'feet' => 'Select Feet / Legs'
    );
    
    $output = '<div class="variant_option">';
    $output .= '<h5 class="line-with-title"><span>' . esc_html($atts['widget_title']) . '</span></h5>';
    $output .= '<div class="variant_dropdown">';
    
    foreach ($selected_variations as $variation_key) {
        $variation_key = trim($variation_key);
        $display_name = isset($variation_names[$variation_key]) ? $variation_names[$variation_key] : ucfirst($variation_key);
        
        // Try different attribute name formats
        $attribute_name = null;
        $possible_names = array(
            $variation_key,                    // Direct name: fabric
            'pa_' . $variation_key,           // With pa_ prefix: pa_fabric
            'attribute_' . $variation_key,    // With attribute_ prefix: attribute_fabric
        );
        
        // Find the correct attribute name
        foreach ($possible_names as $name) {
            if (isset($attributes[$name])) {
                $attribute_name = $name;
                break;
            }
        }
        
        // If still not found, try to find by slug or taxonomy
        if (!$attribute_name) {
            foreach ($attributes as $attr_name => $attr_obj) {
                if (is_object($attr_obj)) {
                    // Try by name
                    if (method_exists($attr_obj, 'get_name')) {
                        $attr_name_value = $attr_obj->get_name();
                        if (strtolower($attr_name_value) === strtolower($variation_key)) {
                            $attribute_name = $attr_name;
                            break;
                        }
                    }
                    
                    // Try by taxonomy
                    if (method_exists($attr_obj, 'get_taxonomy')) {
                        $attr_taxonomy = $attr_obj->get_taxonomy();
                        if (strtolower($attr_taxonomy) === strtolower($variation_key) || 
                            strtolower($attr_taxonomy) === strtolower('pa_' . $variation_key)) {
                            $attribute_name = $attr_name;
                            break;
                        }
                    }
                    
                    // Try by slug
                    if (method_exists($attr_obj, 'get_slug')) {
                        $attr_slug = $attr_obj->get_slug();
                        if (strtolower($attr_slug) === strtolower($variation_key)) {
                            $attribute_name = $attr_name;
                            break;
                        }
                    }
                }
            }
        }
        
        // Check if this attribute exists for the product
        if ($attribute_name && isset($attributes[$attribute_name])) {
            $attribute = $attributes[$attribute_name];
            $terms = wc_get_product_terms($product_id, $attribute_name, array('fields' => 'all'));
            
            
            if (!empty($terms) && !is_wp_error($terms)) {
                $is_first = ($variation_key === 'fabric') ? ' active' : '';
                $is_first_content = ($variation_key === 'fabric') ? ' active' : '';
                
                $output .= '<div class="toggle_dropdown">';
                $output .= '<h4 class="toggle_title' . $is_first . '">' . esc_html($display_name) . '</h4>';
                $output .= '<div class="toggle_content' . $is_first_content . '">';
                
                foreach ($terms as $index => $term) {
                    $term_id = $term->term_id;
                    $term_name = $term->name;
                    $term_slug = $term->slug;
                    $input_id = $variation_key . ($index + 1);
                    
                    // Get custom image from term meta
                    $term_image = get_term_meta($term_id, 'attribute_image', true);
                    if (empty($term_image)) {
                        // Try alternative meta field names
                        $term_image = get_term_meta($term_id, 'image', true);
                    }
                    if (empty($term_image)) {
                        // Try with attribute name prefix
                        $term_image = get_term_meta($term_id, $variation_key . '_image', true);
                    }
                    
                    // Only show image if custom image exists, otherwise skip the image
                    $display_image = !empty($term_image) ? $term_image : null;
                    
                    $output .= '<label for="' . esc_attr($input_id) . '">';
                    $output .= '<input type="radio" id="' . esc_attr($input_id) . '" name="' . esc_attr($variation_key) . '" value="' . esc_attr($term_slug) . '" data-term-name="' . esc_attr($term_name) . '" data-attribute="' . esc_attr($variation_key) . '">';
                    if ($display_image) {
                        $output .= '<img src="' . esc_url($display_image) . '" alt="' . esc_attr($term_name) . '" />';
                    }
                    $output .= '<span>' . esc_html($term_name) . '</span>';
                    $output .= '</label>';
                }
                
                $output .= '</div>';
                $output .= '</div>';
            }
        }
    }
    
    $output .= '</div>';
    $output .= '</div>';
    
    // Add Forminator integration if form ID is provided
    if (!empty($atts['forminator_form_id'])) {
        $output .= '<div id="bedframe-variation-data" style="display: none;">';
        $output .= '<input type="hidden" id="bedframe-fabric" name="bedframe_fabric" value="">';
        $output .= '<input type="hidden" id="bedframe-finish" name="bedframe_finish" value="">';
        $output .= '<input type="hidden" id="bedframe-storage" name="bedframe_storage" value="">';
        $output .= '<input type="hidden" id="bedframe-slats" name="bedframe_slats" value="">';
        $output .= '<input type="hidden" id="bedframe-model" name="bedframe_model" value="">';
        $output .= '<input type="hidden" id="bedframe-feet" name="bedframe_feet" value="">';
        $output .= '<input type="hidden" id="bedframe-fabric-name" name="bedframe_fabric_name" value="">';
        $output .= '<input type="hidden" id="bedframe-finish-name" name="bedframe_finish_name" value="">';
        $output .= '<input type="hidden" id="bedframe-storage-name" name="bedframe_storage_name" value="">';
        $output .= '<input type="hidden" id="bedframe-slats-name" name="bedframe_slats_name" value="">';
        $output .= '<input type="hidden" id="bedframe-model-name" name="bedframe_model_name" value="">';
        $output .= '<input type="hidden" id="bedframe-feet-name" name="bedframe_feet_name" value="">';
        $output .= '</div>';
        
        $output .= '<script>
        jQuery(document).ready(function($) {
            var formId = "' . esc_js($atts['forminator_form_id']) . '";
            
            // Function to update URL parameters and form fields
            function updateForminatorFields() {
                var selectedData = {};
                var selectedNames = {};
                
                // Get all selected values
                $(".variant_option input[type=radio]:checked").each(function() {
                    var attribute = $(this).data("attribute");
                    var value = $(this).val();
                    var name = $(this).data("term-name");
                    
                    // Only add if we have a valid value and attribute
                    if (attribute && value) {
                        selectedData[attribute] = value;
                        selectedNames[attribute] = name;
                    }
                });
                
                // Update hidden fields in the bedframe variation data div
                $.each(selectedData, function(attr, value) {
                    $("#bedframe-" + attr).val(value);
                });
                
                $.each(selectedNames, function(attr, name) {
                    $("#bedframe-" + attr + "-name").val(name);
                });
                
                // Update URL parameters for query parameter integration
                var url = new URL(window.location);
                
                // Clear existing bedframe parameters
                var paramsToRemove = [];
                for (var param of url.searchParams.keys()) {
                    if (param.startsWith("bedframe_")) {
                        paramsToRemove.push(param);
                    }
                }
                paramsToRemove.forEach(function(param) {
                    url.searchParams.delete(param);
                });
                
                // Add selected values as URL parameters
                $.each(selectedData, function(attr, value) {
                    if (value) {
                        url.searchParams.set("bedframe_" + attr, value);
                    }
                });
                
                $.each(selectedNames, function(attr, name) {
                    if (name) {
                        url.searchParams.set("bedframe_" + attr + "_name", name);
                    }
                });
                
                // Update the URL without page reload
                window.history.replaceState({}, "", url);
                
                // Also update Forminator form hidden fields using parent class selectors as backup
                if (formId && $("#forminator-module-" + formId).length) {
                    var form = $("#forminator-module-" + formId);
                    
                    // Map of attributes to their corresponding hidden field classes
                    var attributeMap = {
                        "fabric": "bedframe_fabric",
                        "finish": "bedframe_finish", 
                        "storage": "bedframe_storage",
                        "slats": "bedframe_slats",
                        "model": "bedframe_model",
                        "feet": "bedframe_feet"
                    };
                    
                    // Clear all hidden fields first
                    form.find(".forminator-hidden input[type=hidden]").val("");
                    
                    // Update hidden fields with selected values
                    $.each(selectedData, function(attr, value) {
                        var className = attributeMap[attr];
                        if (className) {
                            form.find("." + className + " input[type=hidden]").val(value);
                        }
                    });
                    
                    // Update name fields
                    $.each(selectedNames, function(attr, name) {
                        var className = attributeMap[attr] + "_name";
                        if (className) {
                            form.find("." + className + " input[type=hidden]").val(name);
                        }
                    });
                }
            }
            
            // Listen for radio button changes
            $(".variant_option input[type=radio]").on("change", function() {
                updateForminatorFields();
            });
            
            // Initial update
            updateForminatorFields();
            
            // Listen for Forminator form submission
            if (formId) {
                $(document).on("forminator:form:submit", function(e, formId) {
                    if (formId === "' . esc_js($atts['forminator_form_id']) . '") {
                        updateForminatorFields();
                    }
                });
            }
        });
        </script>';
    }
    
    return $output;
}
add_shortcode('bedframe_variation_option_list', 'bedframe_variation_option_list_shortcode');

// Helper function to add image meta field to attribute terms
function add_attribute_image_meta_field() {
    if (!function_exists('wc_get_attribute_taxonomies')) return; // Guard: WooCommerce required
    // Add image field to all product attribute terms
    $attribute_taxonomies = wc_get_attribute_taxonomies();
    
    foreach ($attribute_taxonomies as $taxonomy) {
        $taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);
        
        // Add form field for adding/editing term images
        add_action($taxonomy_name . '_add_form_fields', 'add_attribute_image_field');
        add_action($taxonomy_name . '_edit_form_fields', 'edit_attribute_image_field');
        add_action('created_' . $taxonomy_name, 'save_attribute_image_field');
        add_action('edited_' . $taxonomy_name, 'save_attribute_image_field');
    }
}
add_action('init', 'add_attribute_image_meta_field');

// Add image field to add new term form
function add_attribute_image_field($taxonomy) {
    ?>
    <div class="form-field">
        <label for="attribute_image"><?php _e('Attribute Image', 'textdomain'); ?></label>
        <div class="attribute-image-upload">
            <input type="hidden" name="attribute_image" id="attribute_image" value="" />
            <div id="attribute-image-preview" style="margin-top: 10px;"></div>
            <button type="button" class="button" id="upload-attribute-image"><?php _e('Select Image', 'textdomain'); ?></button>
            <button type="button" class="button" id="remove-attribute-image" style="display: none;"><?php _e('Remove Image', 'textdomain'); ?></button>
        </div>
        <p class="description"><?php _e('Select an image for this attribute term.', 'textdomain'); ?></p>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#upload-attribute-image').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Select Attribute Image',
                button: {
                    text: 'Select Image'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#attribute_image').val(attachment.url);
                $('#attribute-image-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
                $('#remove-attribute-image').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove-attribute-image').click(function(e) {
            e.preventDefault();
            $('#attribute_image').val('');
            $('#attribute-image-preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

// Add image field to edit term form
function edit_attribute_image_field($term) {
    $image_url = get_term_meta($term->term_id, 'attribute_image', true);
    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="attribute_image"><?php _e('Attribute Image', 'textdomain'); ?></label>
        </th>
        <td>
            <div class="attribute-image-upload">
                <input type="hidden" name="attribute_image" id="attribute_image" value="<?php echo esc_attr($image_url); ?>" />
                <div id="attribute-image-preview" style="margin-top: 10px;">
                    <?php if ($image_url): ?>
                        <img src="<?php echo esc_url($image_url); ?>" style="max-width: 150px; height: auto;" />
                    <?php endif; ?>
                </div>
                <button type="button" class="button" id="upload-attribute-image"><?php _e('Select Image', 'textdomain'); ?></button>
                <button type="button" class="button" id="remove-attribute-image" <?php echo $image_url ? '' : 'style="display: none;"'; ?>><?php _e('Remove Image', 'textdomain'); ?></button>
            </div>
            <p class="description"><?php _e('Select an image for this attribute term.', 'textdomain'); ?></p>
        </td>
    </tr>
    
    <script>
    jQuery(document).ready(function($) {
        var mediaUploader;
        
        $('#upload-attribute-image').click(function(e) {
            e.preventDefault();
            
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            
            mediaUploader = wp.media({
                title: 'Select Attribute Image',
                button: {
                    text: 'Select Image'
                },
                multiple: false
            });
            
            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#attribute_image').val(attachment.url);
                $('#attribute-image-preview').html('<img src="' + attachment.url + '" style="max-width: 150px; height: auto;" />');
                $('#remove-attribute-image').show();
            });
            
            mediaUploader.open();
        });
        
        $('#remove-attribute-image').click(function(e) {
            e.preventDefault();
            $('#attribute_image').val('');
            $('#attribute-image-preview').html('');
            $(this).hide();
        });
    });
    </script>
    <?php
}

// Save image field
function save_attribute_image_field($term_id) {
    if (isset($_POST['attribute_image'])) {
        update_term_meta($term_id, 'attribute_image', sanitize_url($_POST['attribute_image']));
    }
}


function mattress_products_top_info($category_id) {
    $category = get_term($category_id);
    $category_name = $category->name;
    $category_description = $category->description;
    echo '<div class="category_information">';
    echo '<h3 class="category-title">' . esc_html($category_name) . '</h3>';
    if (!empty($category_description)) {
    echo '<p class="category-description">' . esc_html($category_description) . '</p>';
    }
    echo '</div>';
}