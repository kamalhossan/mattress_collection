<?php
$theme_url = get_stylesheet_directory_uri();
$product_id = get_the_ID();
$product_cats = wp_get_post_terms($product_id, 'product_cat');
$firmness_cats = wp_get_post_terms($product_id, 'firmness_level');

// checking product category type
$is_mattress =  $args['is_mattress'] ?? false;
$is_bed =  $args['is_bed'] ?? false;
$is_pillow =  $args['is_pillow'] ?? false;
$is_offer = false;
$details_title = 'SPRING DEALS';
$deals_details = 'Up to 20% off on mattresses!*';

$product_deals = get_field('enable_deals', 'option');
if($product_deals){
    $is_offer = true;
    $details_title = get_field('deals_title', 'option');
    $deals_details = get_field('deals_details', 'option');
}


$addons_section_title = '';
if($is_mattress) {
    $addons_section_title = 'Choose Your BED';
} else if($is_bed) {
    $addons_section_title = 'Choose Your MATTRESS';
} else if($is_pillow) {
    $addons_section_title = 'Pillow Addons';
}

$addon_learn_url = '#';
$addon_learn_text = 'Learn more';

?>


<div class="matt-base-info desktop">
    <?php

    if($is_offer) {
        echo '<div class="offer_section">
            <a href="#" class="nectar-button offer-btn">' . $details_title . '</a>
            <h4>' . $deals_details . '</h4>
        </div>';
    }
    
    ?>
    <div class="product_details">
    	<h3><?php the_title(); ?></h3>
        <?php 

        
        if (!empty($firmness_cats)) {
            $firmness_level = '';
            foreach ($firmness_cats as $firmness_cat) {
                    $firmness_level = $firmness_cat->name;
                    break;
            }
            if (!empty($firmness_level)) { 
                echo '<span class="tag_size">Firmness level: <span>' . esc_html($firmness_level) . '</span></span>';
            }
        }

        $description = get_the_excerpt();
        if (empty($description)) {
            $description = get_the_content();
        }
        if (!empty($description)) {
            echo '<p>' . esc_html(wp_trim_words($description, 30)) . '</p>';
        }


        // quantity only for mattress
        // Get mattress sizes from taxonomy
        if($is_mattress || $is_pillow){
            $sizes = wp_get_post_terms($product_id, 'mattress_size');
      
            if (!empty($sizes) && !is_wp_error($sizes)) {
                render_product_size_selector($sizes);
            }
        }

        if($is_bed){
            echo '<div class="quantity">';
            echo'<p>Select Size <span>Size guide</span></p>';
            echo'<select name="mattress_size" id="mattress_size_select">';
            
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
                    echo '<option value="' . esc_attr($size->slug) . '">' . esc_html($display_text) . '</option>';
                }
            }
            
            echo '</select>';
            echo '<button type="submit" id="mattress_size_submit" class="nectar-button">REQUEST A QUOTE</button>';
            echo '</div>';
            
        }

        // Show stock status text
        if (!empty($atts['stock_text'])) {
            echo '<p class="stock_tag">' . esc_html($atts['stock_text']) . '</p>';
        }
       
        ?> 
    </div>
</div>

<?php

// echo do_shortcode('[icon_with_slider]'); 

/// acf repeater field product_features_slider 

if(have_rows('product_features_slider')){
echo '<div class="matt-product-features-slider-wrapper">';
echo '<div class="slider-container">';
echo '<div class="matt-product-features-slider">';
    while ( have_rows('product_features_slider') ) {
            the_row();
            $image_url = get_sub_field('Image');
            $title = get_sub_field('title');
            $description = get_sub_field('description');
            echo '<div class="slide-item">';
            echo '<div class="slide-content">';
            echo '<div class="icon"><img src="' . $image_url . '" alt="Icon"></div>';
            echo '<h3>' . $title . '</h3>';
            echo '<p>' . $description . '</p>';
          echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="slider-progress-container">';
    echo '<div class="slider-progress-bar"></div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

// product faq

if(have_rows('product_faq')){
   echo '<div class="matt-faq-wrapper">';
    while(have_rows('product_faq')){
        the_row();
        $title = get_sub_field('title');
        $description = get_sub_field('description');
        echo '<details class="faq_section">';
        echo '<summary>' . $title . '</summary>';
        echo '<div class="tab_content">';
        echo '<p>' . $description . '</p>';
        echo '</div>';
        echo '</details>';
    }
    echo '</div>';
}


// product addons

if (empty($product_cats) || is_wp_error($product_cats)) {
    echo '<p>No product category found.</p>';
} else {
    // Get the first category to find related products
    $main_category = $product_cats[0];

        // Query related products from the same category
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 3,
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
        echo '<p>No related products found in this category.</p>';
    }

    echo '<div class="addons_product">';
    echo '<h5><span>' . $addons_section_title . '</span></h5>';
    echo '<div class="addons_product_list">';
    
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
        
        echo '<div class="addons_product_card">';
        if (!empty($badge_text)) {
            echo '<span class="badge_tag">' . esc_html($badge_text) . '</span>';
        }
        echo '<div class="addons_card">';
        echo '<div class="addons_img" style="max-width: 75px;">';
        echo '<img src="' . esc_url($product_image) . '" alt="' . esc_attr($product_title) . '">';
        echo '</div>';
        echo '<div class="addons_content">';
        echo '<div>';
        echo '<h6>' . esc_html($product_title) . '</h6>';
        echo '<p>€' . esc_html(mattress_safe_number_format($product_price, 2)) . '</p>';
        echo '</div>';
        echo '</div>'; // Removed the Add button as requested
        echo '</div>';
        echo '</div>';
    }
    
    wp_reset_postdata();
    
    echo '</div>';
    
    // if (!empty($atts['learn_more_text']) && !empty($atts['learn_more_url'])) {
        echo '<p class="learn_more">';
        echo '<a href="' . esc_url($addon_learn_url) . '">' . esc_html($addon_learn_text) . '</a>';
        echo '</p>';
    // }
    echo '</div>';

}

// product request a quote

echo '<div class="product_request_a_quote_form">';
echo '<div class="form-wrapper">';
echo '<h3 class="form_title">Request a quote</h3>';
echo do_shortcode('[forminator_form id="424"]');
echo '</div>';
echo '<a href="/find-a-store" class="nectar-button location_btn"><img src=" ' . $theme_url . '/images/map-pin.svg" alt="Map Pin"> Find a store</a>';
echo '</div>';
