<?php

// ========================================
// CUSTOM WOOCOMMERCE TAXONOMIES
// ========================================

// Register custom taxonomies for mattress products
function register_mattress_taxonomies() {
    
    // Mattress Type Taxonomy
    register_taxonomy(
        'mattress_type',
        'product',
        array(
            'labels' => array(
                'name' => 'Mattress Types',
                'singular_name' => 'Mattress Type',
                'search_items' => 'Search Mattress Types',
                'all_items' => 'All Mattress Types',
                'parent_item' => 'Parent Mattress Type',
                'parent_item_colon' => 'Parent Mattress Type:',
                'edit_item' => 'Edit Mattress Type',
                'update_item' => 'Update Mattress Type',
                'add_new_item' => 'Add New Mattress Type',
                'new_item_name' => 'New Mattress Type Name',
                'menu_name' => 'Mattress Types'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'public' => false,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'rewrite' => false
        )
    );
    
    // Firmness Level Taxonomy
    register_taxonomy(
        'firmness_level',
        'product',
        array(
            'labels' => array(
                'name' => 'Firmness Levels',
                'singular_name' => 'Firmness Level',
                'search_items' => 'Search Firmness Levels',
                'all_items' => 'All Firmness Levels',
                'parent_item' => 'Parent Firmness Level',
                'parent_item_colon' => 'Parent Firmness Level:',
                'edit_item' => 'Edit Firmness Level',
                'update_item' => 'Update Firmness Level',
                'add_new_item' => 'Add New Firmness Level',
                'new_item_name' => 'New Firmness Level Name',
                'menu_name' => 'Firmness Levels'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'public' => false,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'rewrite' => false
        )
    );
    
    // Size Taxonomy
    register_taxonomy(
        'mattress_size',
        'product',
        array(
            'labels' => array(
                'name' => 'Mattress Sizes',
                'singular_name' => 'Mattress Size',
                'search_items' => 'Search Mattress Sizes',
                'all_items' => 'All Mattress Sizes',
                'parent_item' => 'Parent Mattress Size',
                'parent_item_colon' => 'Parent Mattress Size:',
                'edit_item' => 'Edit Mattress Size',
                'update_item' => 'Update Mattress Size',
                'add_new_item' => 'Add New Mattress Size',
                'new_item_name' => 'New Mattress Size Name',
                'menu_name' => 'Mattress Sizes'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'public' => false,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'rewrite' => false
        )
    );
    
    // Bedframe Size Taxonomy
    register_taxonomy(
        'bedframe_size',
        'product',
        array(
            'labels' => array(
                'name' => 'Bedframe Sizes',
                'singular_name' => 'Bedframe Size',
                'search_items' => 'Search Bedframe Sizes',
                'all_items' => 'All Bedframe Sizes',
                'parent_item' => 'Parent Bedframe Size',
                'parent_item_colon' => 'Parent Bedframe Size:',
                'edit_item' => 'Edit Bedframe Size',
                'update_item' => 'Update Bedframe Size',
                'add_new_item' => 'Add New Bedframe Size',
                'new_item_name' => 'New Bedframe Size Name',
                'menu_name' => 'Bedframe Sizes'
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => false,
            'query_var' => true,
            'public' => false,
            'publicly_queryable' => false,
            'show_in_nav_menus' => false,
            'show_in_rest' => false,
            'rewrite' => false
        )
    );
}
add_action('init', 'register_mattress_taxonomies');

// regsiter post types for mattress

add_action('init', 'register_mattress_post_types');

function register_mattress_post_types() {
    //testimonial
    register_post_type('testimonial', array(
        'labels' => array(
            'name' => 'Testimonials',
            'singular_name' => 'Testimonial',
            'search_items' => 'Search Testimonials',
            'all_items' => 'All Testimonials',
            'parent_item' => 'Parent Testimonial',
            'parent_item_colon' => 'Parent Testimonial:', 
            'edit_item' => 'Edit Testimonial',
            'update_item' => 'Update Testimonial',
            'add_new_item' => 'Add New Testimonial',
            'new_item_name' => 'New Testimonial Name',
            'menu_name' => 'Testimonials'
        ),
        'public' => true,
        'has_archive' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'custom-fields'
        ),
        'show_in_rest' => true,
        )
    );
}


// Add custom fields to taxonomy terms
function add_mattress_size_term_fields($term) {
    // Get current term meta
    $dimensions = get_term_meta($term->term_id, 'size_dimensions', true);
    $standard_name = get_term_meta($term->term_id, 'size_standard_name', true);
    
    ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row">
                <label for="size_dimensions">Size Dimensions</label>
            </th>
            <td>
                <input type="text" name="size_dimensions" id="size_dimensions" value="<?php echo esc_attr($dimensions); ?>" />
                <p class="description">Enter the dimensions (e.g., 135cm x 190cm)</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="size_standard_name">Standard Size Name</label>
            </th>
            <td>
                <input type="text" name="size_standard_name" id="size_standard_name" value="<?php echo esc_attr($standard_name); ?>" />
                <p class="description">Enter the standard size name (e.g., Queen, King, Single)</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('mattress_size_edit_form_fields', 'add_mattress_size_term_fields', 10, 2);

// Save custom fields for taxonomy terms
function save_mattress_size_term_fields($term_id) {
    if (isset($_POST['size_dimensions'])) {
        update_term_meta($term_id, 'size_dimensions', sanitize_text_field($_POST['size_dimensions']));
    }
    if (isset($_POST['size_standard_name'])) {
        update_term_meta($term_id, 'size_standard_name', sanitize_text_field($_POST['size_standard_name']));
    }
}
add_action('edited_mattress_size', 'save_mattress_size_term_fields');
add_action('created_mattress_size', 'save_mattress_size_term_fields');

// Add custom fields to bedframe_size taxonomy terms
function add_bedframe_size_term_fields($term) {
    // Get current term meta
    $dimensions = get_term_meta($term->term_id, 'size_dimensions', true);
    $standard_name = get_term_meta($term->term_id, 'size_standard_name', true);
    
    ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row">
                <label for="size_dimensions">Size Dimensions</label>
            </th>
            <td>
                <input type="text" name="size_dimensions" id="size_dimensions" value="<?php echo esc_attr($dimensions); ?>" />
                <p class="description">Enter the dimensions (e.g., 135cm x 190cm)</p>
            </td>
        </tr>
        <tr class="form-field">
            <th scope="row">
                <label for="size_standard_name">Standard Size Name</label>
            </th>
            <td>
                <input type="text" name="size_standard_name" id="size_standard_name" value="<?php echo esc_attr($standard_name); ?>" />
                <p class="description">Enter the standard size name (e.g., Queen, King, Single)</p>
            </td>
        </tr>
    </table>
    <?php
}
add_action('bedframe_size_edit_form_fields', 'add_bedframe_size_term_fields', 10, 2);

// Save custom fields for bedframe_size taxonomy terms
function save_bedframe_size_term_fields($term_id) {
    if (isset($_POST['size_dimensions'])) {
        update_term_meta($term_id, 'size_dimensions', sanitize_text_field($_POST['size_dimensions']));
    }
    if (isset($_POST['size_standard_name'])) {
        update_term_meta($term_id, 'size_standard_name', sanitize_text_field($_POST['size_standard_name']));
    }
}
add_action('edited_bedframe_size', 'save_bedframe_size_term_fields');
add_action('created_bedframe_size', 'save_bedframe_size_term_fields');

// Add default terms for each taxonomy
function add_default_mattress_terms() {
    
    // Default Mattress Types
    $mattress_types = array(
        'Memory Foam' => 'Memory foam mattresses that contour to your body',
        'Latex' => 'Natural latex mattresses for durability and comfort',
        'Hybrid' => 'Combination of innerspring and foam for best of both worlds',
        'Innerspring' => 'Traditional coil-based mattresses',
        'Pocket Spring' => 'Individual pocketed springs for better motion isolation',
        'Gel Foam' => 'Cooling gel-infused foam mattresses'
    );
    
    foreach ($mattress_types as $name => $description) {
        if (!term_exists($name, 'mattress_type')) {
            wp_insert_term($name, 'mattress_type', array('description' => $description));
        }
    }
    
    // Default Firmness Levels
    $firmness_levels = array(
        'Soft' => 'Plush and cushioning feel',
        'Medium-Soft' => 'Slightly plush with some support',
        'Medium' => 'Balanced comfort and support',
        'Medium-Firm' => 'Firm support with some cushioning',
        'Firm' => 'Strong support for back sleepers',
        'Extra Firm' => 'Maximum support and minimal sink'
    );
    
    foreach ($firmness_levels as $name => $description) {
        if (!term_exists($name, 'firmness_level')) {
            wp_insert_term($name, 'firmness_level', array('description' => $description));
        }
    }
    
    // Default Mattress Sizes with metadata
    $mattress_sizes = array(
        'Single' => array(
            'description' => 'Standard single bed size',
            'dimensions' => '90cm x 190cm',
            'standard_name' => 'Single'
        ),
        'Small Double' => array(
            'description' => 'Compact double size',
            'dimensions' => '120cm x 190cm',
            'standard_name' => 'Small Double'
        ),
        'Double' => array(
            'description' => 'Standard double bed size',
            'dimensions' => '135cm x 190cm',
            'standard_name' => 'Double'
        ),
        'King' => array(
            'description' => 'Large double bed size',
            'dimensions' => '150cm x 200cm',
            'standard_name' => 'King'
        ),
        'Super King' => array(
            'description' => 'Extra large bed size',
            'dimensions' => '180cm x 200cm',
            'standard_name' => 'Super King'
        ),
        'EU Single' => array(
            'description' => 'European single size',
            'dimensions' => '90cm x 200cm',
            'standard_name' => 'EU Single'
        ),
        'EU Double' => array(
            'description' => 'European double size',
            'dimensions' => '160cm x 200cm',
            'standard_name' => 'EU Double'
        )
    );
    
    foreach ($mattress_sizes as $name => $data) {
        if (!term_exists($name, 'mattress_size')) {
            $term = wp_insert_term($name, 'mattress_size', array('description' => $data['description']));
            
            if (!is_wp_error($term)) {
                // Add metadata for the term
                update_term_meta($term['term_id'], 'size_dimensions', $data['dimensions']);
                update_term_meta($term['term_id'], 'size_standard_name', $data['standard_name']);
            }
        }
    }
    
    // Default Bedframe Sizes with metadata
    $bedframe_sizes = array(
        'Single' => array(
            'description' => 'Standard single bedframe size',
            'dimensions' => '90cm x 190cm',
            'standard_name' => 'Single'
        ),
        'Small Double' => array(
            'description' => 'Compact double bedframe size',
            'dimensions' => '120cm x 190cm',
            'standard_name' => 'Small Double'
        ),
        'Double' => array(
            'description' => 'Standard double bedframe size',
            'dimensions' => '135cm x 190cm',
            'standard_name' => 'Double'
        ),
        'King' => array(
            'description' => 'Large double bedframe size',
            'dimensions' => '150cm x 200cm',
            'standard_name' => 'King'
        ),
        'Super King' => array(
            'description' => 'Extra large bedframe size',
            'dimensions' => '180cm x 200cm',
            'standard_name' => 'Super King'
        ),
        'EU Single' => array(
            'description' => 'European single bedframe size',
            'dimensions' => '90cm x 200cm',
            'standard_name' => 'EU Single'
        ),
        'EU Double' => array(
            'description' => 'European double bedframe size',
            'dimensions' => '160cm x 200cm',
            'standard_name' => 'EU Double'
        )
    );
    
    foreach ($bedframe_sizes as $name => $data) {
        if (!term_exists($name, 'bedframe_size')) {
            $term = wp_insert_term($name, 'bedframe_size', array('description' => $data['description']));
            
            if (!is_wp_error($term)) {
                // Add metadata for the term
                update_term_meta($term['term_id'], 'size_dimensions', $data['dimensions']);
                update_term_meta($term['term_id'], 'size_standard_name', $data['standard_name']);
            }
        }
    }
}
add_action('init', 'add_default_mattress_terms');

// Helper function to get size dimensions
function get_mattress_size_dimensions($term_id) {
    return get_term_meta($term_id, 'size_dimensions', true);
}

// Helper function to get size standard name
function get_mattress_size_standard_name($term_id) {
    return get_term_meta($term_id, 'size_standard_name', true);
}

// Helper function to get bedframe size dimensions
function get_bedframe_size_dimensions($term_id) {
    return get_term_meta($term_id, 'size_dimensions', true);
}

// Helper function to get bedframe size standard name
function get_bedframe_size_standard_name($term_id) {
    return get_term_meta($term_id, 'size_standard_name', true);
}

// Add custom fields to product variations for size-specific measurements
function add_size_measurement_fields($loop, $variation_data, $variation) {
    $variation_id = $variation->ID;
    
    echo '<div class="variation-size-fields">';
    
    // Size-specific dimensions (optional, for custom measurements)
    woocommerce_wp_text_input(
        array(
            'id' => '_variation_custom_measurement[' . $variation_id . ']',
            'label' => __('Custom Dimensions', 'woocommerce'),
            'placeholder' => 'e.g., 95cm x 195cm (optional)',
            'value' => get_post_meta($variation_id, '_variation_custom_measurement', true),
            'desc_tip' => 'true',
            'description' => __('Enter custom dimensions if different from standard size', 'woocommerce')
        )
    );
    
    echo '</div>';
}
add_action('woocommerce_product_after_variable_attributes', 'add_size_measurement_fields', 10, 3);

// Save size measurement fields
function save_size_measurement_fields($variation_id, $loop) {
    $custom_measurement = $_POST['_variation_custom_measurement'][$variation_id];
    if (!empty($custom_measurement)) {
        update_post_meta($variation_id, '_variation_custom_measurement', esc_attr($custom_measurement));
    }
}
add_action('woocommerce_save_product_variation', 'save_size_measurement_fields', 10, 2);

// Add custom fields to Product backend
add_action('woocommerce_product_options_general_product_data', 'add_custom_product_fields');
function add_custom_product_fields() {
    echo '<div class="options_group">';

    // Height
    woocommerce_wp_text_input(array(
        'id' => '_product_height',
        'label' => __('Height', 'woocommerce'),
        'desc_tip' => true,
        'description' => __('Enter the product height.', 'woocommerce'),
    ));

    // Material
    woocommerce_wp_text_input(array(
        'id' => '_product_material',
        'label' => __('Material', 'woocommerce'),
        'desc_tip' => true,
        'description' => __('Enter the product material.', 'woocommerce'),
    ));

    // Medium
    woocommerce_wp_text_input(array(
        'id' => '_product_medium',
        'label' => __('Medium', 'woocommerce'),
        'desc_tip' => true,
        'description' => __('Enter the product medium.', 'woocommerce'),
    ));

    // Fibre
    woocommerce_wp_text_input(array(
        'id' => '_product_fibre',
        'label' => __('Fibre', 'woocommerce'),
        'desc_tip' => true,
        'description' => __('Enter the product fibre.', 'woocommerce'),
    ));

    echo '</div>';
}

// Save custom fields
add_action('woocommerce_process_product_meta', 'save_custom_product_fields');
function save_custom_product_fields($post_id) {
    $fields = array('_product_height', '_product_material', '_product_medium', '_product_fibre');

    foreach ($fields as $field) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $field, sanitize_text_field($_POST[$field]));
        }
    }
}

// ========================================
// WP BAKERY TEMPLATE POPUP FOR PRODUCT EDITING
// ========================================

// Add popup for WP Bakery template instructions when editing products
function add_wpbakery_template_popup() {
    // Only show on product pages
    if (!is_admin() || !function_exists('get_current_screen')) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }
    
    // Show on both new product creation and product editing pages
    $is_new_product = isset($_GET['post_type']) && $_GET['post_type'] === 'product' && !isset($_GET['post']);
    $is_editing_product = isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] === 'edit';
    
    if (!$is_new_product && !$is_editing_product) {
        return;
    }
    
    ?>
    <div id="wpbakery-template-popup" class="wpbakery-popup-overlay" style="display: none;">
        <div class="wpbakery-popup-content">
            <div class="wpbakery-popup-header">
                <h3>📋 Add template design based on your product category</h3>
                <button type="button" class="wpbakery-popup-close" onclick="closeWPBakeryPopup()">&times;</button>
            </div>
            <div class="wpbakery-popup-body">
                <div class="template-instructions">
                    <p><strong>Close this popup and follow the steps below:</strong></p>
                    
                    <ol>
                        <li>If you see the Backend Editor, click <strong>Backend Editor and it will now show the Classic Mode</strong>.</li>
                        <li>If Classic Mode is already showing, leave it as is.</li>
                        <li>Click on <strong>My Templates</strong>.</li>
                        <li>From the list, choose the template that matches your product category.</li>
                        <li>Click <strong>Save</strong>.</li>
                    </ol>
                    
                    <p><strong>Your template is now loaded - you can edit and customize the content however you like.</strong></p>
                </div>
            </div>
            <div class="wpbakery-popup-footer">
                <button type="button" class="button button-primary" onclick="closeWPBakeryPopup()">Got it!</button>
            </div>
        </div>
    </div>
    
    <style>
        .wpbakery-popup-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 999999;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .wpbakery-popup-content {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
        }
        
        .wpbakery-popup-header {
            background: #0073aa;
            color: #fff;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .wpbakery-popup-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #fff;
        }
        
        .wpbakery-popup-close {
            background: none;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s;
        }
        
        .wpbakery-popup-close:hover {
            background: rgba(255, 255, 255, 0.2);
        }
        
        .wpbakery-popup-body {
            padding: 25px;
        }
        
        .template-instructions p {
            margin: 15px 0;
            line-height: 1.6;
        }
        
        .template-instructions ol {
            margin: 20px 0;
            padding-left: 20px;
        }
        
        .template-instructions ol li {
            margin-bottom: 12px;
            line-height: 1.6;
        }
        
        .wpbakery-popup-footer {
            padding: 20px 25px;
            border-top: 1px solid #e5e5e5;
            display: flex;
            justify-content: center;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }
        
        @media (max-width: 768px) {
            .wpbakery-popup-content {
                width: 95%;
                margin: 20px;
            }
        }
    </style>
    
    <script>
        // Show popup automatically only for new products
        document.addEventListener('DOMContentLoaded', function() {
            console.log('WP Bakery popup script loaded');
            
            // Check if this is a new product page
            const isNewProduct = window.location.href.includes('post-new.php?post_type=product');
            
            if (isNewProduct) {
                // Auto-show popup for new products after 1 second
                if (localStorage.getItem('wpbakery_popup_hidden') !== 'true') {
                    console.log('New product page - showing popup automatically');
                    setTimeout(function() {
                        const popup = document.getElementById('wpbakery-template-popup');
                        if (popup) {
                            popup.style.display = 'flex';
                            console.log('WP Bakery popup displayed automatically');
                        }
                    }, 1000);
                }
            } else {
                console.log('Edit product page - popup will show only when clicked');
            }
        });
        
        // Close popup function
        function closeWPBakeryPopup() {
            const popup = document.getElementById('wpbakery-template-popup');
            popup.style.display = 'none';
        }
        
        // Close popup when clicking outside
        document.addEventListener('click', function(event) {
            const popup = document.getElementById('wpbakery-template-popup');
            if (event.target === popup) {
                closeWPBakeryPopup();
            }
        });
        
        // Close popup with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeWPBakeryPopup();
            }
        });
    </script>
    <?php
}

// Hook to admin footer to add the popup
add_action('admin_footer', 'add_wpbakery_template_popup');

// Add admin notice to remind users about the popup
function wpbakery_template_admin_notice() {
    // Only show on product pages
    if (!is_admin() || !function_exists('get_current_screen')) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }
    
    // Show on both new product creation and product editing pages
    $is_new_product = isset($_GET['post_type']) && $_GET['post_type'] === 'product' && !isset($_GET['post']);
    $is_editing_product = isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] === 'edit';
    
    if (!$is_new_product && !$is_editing_product) {
        return;
    }
    
    ?>
    <div class="notice notice-info is-dismissible">
        <p>
            <strong>💡 WP Bakery Template Tip:</strong> 
            Need help adding saved templates to your product page? 
            <button type="button" class="button button-small" onclick="showWPBakeryPopup()" style="margin-left: 10px;">
                Show Instructions
            </button>
        </p>
    </div>
    
    <script>
        function showWPBakeryPopup() {
            document.getElementById('wpbakery-template-popup').style.display = 'flex';
        }
    </script>
    <?php
}

// Hook to admin notices
add_action('admin_notices', 'wpbakery_template_admin_notice');
