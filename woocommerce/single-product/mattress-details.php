<?php




$product_id = get_the_ID();
global $product;
$theme_url = get_stylesheet_directory_uri();
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

echo '<div class="mattress_product_details_wrapper">';

if($is_offer){
    if($is_offer) {
        echo '<div class="offer_section">
            <a href="#" class="nectar-button offer-btn">' . $details_title . '</a>
            <h4>' . $deals_details . '</h4>
        </div>';
    }
}
echo '<div class="mattress-product-details">';

if($is_mattress || $is_pillow){
       // Get the current product's featured image
    if (has_post_thumbnail()) {
        echo '<div class="product-feature-image-wrap">';
        $image_id = get_post_thumbnail_id();
        $image_url = wp_get_attachment_image_url($image_id, 'full');
        $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
        
        if ($image_url) {
            echo '<div class="matt-featured-image">';
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '">';
            echo '</div>';
        }
        echo '</div>';
    } 
}

if($is_bed){

    // 1. Get featured image
    $featured_image_id  = $product->get_image_id();
    $slide_image_urls   = array();

    if ( $featured_image_id ) {
        $featured_image_url = wp_get_attachment_image_url( $featured_image_id, 'large' );
        if ( $featured_image_url ) {
            $slide_image_urls[] = array(
                'url' => $featured_image_url,
                'alt' => get_post_meta( $featured_image_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $product_id ),
            );
        }
    }

    // 2. Get gallery images
    $gallery_image_ids = $product->get_gallery_image_ids();

    if ( ! empty( $gallery_image_ids ) ) {
        foreach ( $gallery_image_ids as $gallery_image_id ) {
            $gallery_image_url = wp_get_attachment_image_url( $gallery_image_id, 'large' );
            if ( $gallery_image_url ) {
                $slide_image_urls[] = array(
                    'url' => $gallery_image_url,
                    'alt' => get_post_meta( $gallery_image_id, '_wp_attachment_image_alt', true ) ?: get_the_title( $product_id ),
                );
            }
        }
    }

    // 3. Render Slider Output if images exist
    if ( ! empty( $slide_image_urls ) ) {
        echo '<div class="bed-gallery-container">';

            // Left Sidebar Thumbnails
            echo '<div class="bed-gallery-thumb-wrapper">';
                echo '<div class="bed-gallery-thumb-slider">';
                foreach ( $slide_image_urls as $image ) {
                    echo '<div class="bed-gallery-thumb-slide">';
                        echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '" loading="lazy">';
                    echo '</div>';
                }
                echo '</div>';
            echo '</div>';

            // Right Main Showcase Slider
            echo '<div class="bed-gallery-main-wrapper">';
                echo '<div class="bed-gallery-main-slider">';
                foreach ( $slide_image_urls as $image ) {
                    echo '<div class="bed-gallery-main-slide">';
                        echo '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $image['alt'] ) . '">';
                    echo '</div>';
                }
                echo '</div>';
            echo '</div>';

        echo '</div>';
    } else {
        // Optional placeholder display if the product literally has 0 images uploaded
        echo '<div class="bed-gallery-placeholder">' . wc_placeholder_img( 'large' ) . '</div>';
    }
    
}


$gurantee_info = get_field('gurantee_info');

if(!empty($gurantee_info && is_array($gurantee_info))){
echo '<div class="mattress-features">';
   echo '<ul class="matt-features-list">';

   foreach($gurantee_info as $info){
    $value = $info['value'];
    $label = $info['label'];

    if($value == "gurantee"){
        $icon = $theme_url . '/images/gurantee.png';
    }else if($value == "trial"){
        $icon = $theme_url . '/images/trial.png';
    }else{
        $icon = $theme_url . '/images/delivery.png';
    }

    echo '<li class="matt-feature-item">';
    echo '<div class="matt-feature-icon">';
    echo '<img src="' . $icon . '">';
    echo '</div>';
    echo '<div class="matt-feature-text">';
    echo '<span class="matt-feature-title">' . $label . '</span>';
    echo '</div>';
    echo '</li>';
   }
   
   echo '</ul>';
   echo '</div>';

}

echo '</div>';

?>
<div class="matt-base-info mobile">
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
            echo'<select name="mattress_size">';
            
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
            echo '<button type="submit" class="nectar-button">REQUEST A QUOTE</button>';
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


// layer information

if(have_rows('layer_info')){
echo '<div class="matt-layer-info-wrapper">';
echo '<div class="matt-layer-info">';
    while ( have_rows('layer_info') ) {
            the_row();
            $image_url = get_sub_field('image');
            $title = get_sub_field('title');
            $description = get_sub_field('description');
            echo '<div class="layer-item">';
            echo '<div class="layer-icon"><img src="' . $image_url . '" alt="Icon"></div>';
            echo '<div class="layer-content">';
            echo '<h3>' . $title . '</h3>';
            echo '<p>' . $description . '</p>';
          echo '</div>';
          echo '</div>';
    }
    echo '</div>';
echo '</div>';
}


if(have_rows('certification')) {
    echo '<div class="product-certification-wrapper">';
    echo '<ul class="grid grid-4">';
    $image_url = get_stylesheet_directory_uri() . '/images/certification.png';
    while ( have_rows('certification') ) {
        the_row();
        $url = get_sub_field('url');
        $title = get_sub_field('title');
        echo '<li>';
        echo '<a href="' . $url . '" target="_blank">';
        echo '<img src="' . $image_url . '">';
        echo '<span>' . $title . '</span>';
        echo '</a>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
}

// <!-- MATT PRODUCT GALLERY -->

if($is_mattress || $is_pillow) {
    if (empty($product) || $product->get_id() !== $product_id) {
        $product = wc_get_product($product_id);
    }

    $gallery_image_ids = array();
    if ($product) {
        $gallery_image_ids = $product->get_gallery_image_ids();
    }

    if (empty($gallery_image_ids)) {
        $featured_image_id = get_post_thumbnail_id($product_id);
        if ($featured_image_id) {
            $gallery_image_ids[] = $featured_image_id;
        }
    }

    if (!empty($gallery_image_ids)) {
        echo '<div class="matt-gallery-wrapper">';
        echo ' <div class="matt-grid-gallery">';
        $chunks = array_chunk($gallery_image_ids, 2);
        foreach ($chunks as $chunk) {
            echo '<div class="matt-row">';
            foreach ($chunk as $image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'full');
                $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
                if (!$image_url) {
                    continue;
                }
                echo '<div class="matt-grid-item">';
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt ? $image_alt : get_the_title($product_id)) . '">';
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
}


// properties acf repeater field Perfect for hot sleepers

if(have_rows('properties')) {

echo '<div class="matt-properties-wrapper">';
echo '<div class="property_icon">';
echo '<h4>Properties</h4>';
	echo '<ul class="properties_list">';

    while(have_rows('properties')) {
        the_row();
        $image = get_sub_field('image');
        $title = get_sub_field('title');
        echo '<li>';
        echo '<span class="icon_bar">';
        echo '<img src="' . $image . '">';
        echo '</span>';
        echo '<p>' . $title . '</p>';
        echo '</li>';
    }
    echo '</ul>';
	echo '</div>';
	echo '</div>';
}

// wrapper close
echo '</div>';