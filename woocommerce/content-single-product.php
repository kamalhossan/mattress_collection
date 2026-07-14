<?php
/**
 * The template for displaying product content in the single-product.php template
 * Modified to only show WYSIWYG editor content without WooCommerce functionality
 *
 * @package Salient Child Theme
 */

defined( 'ABSPATH' ) || exit;

global $post;


$theme_url = get_stylesheet_directory_uri();
$product_category = get_the_terms(get_the_ID(), 'product_cat');
// check if mattress

$is_mattress = false;
$is_bed = false;
$is_pillow = false;

if ($product_category) {
    foreach ($product_category as $category) {
        if ($category->slug === 'mattresses') {
            $is_mattress = true;
            break;
        }

        if ($category->slug === 'bed-frames' || strpos($category->slug, 'bed') !== false || strpos($category->slug, 'frame') !== false) {
              $is_bed = true;
            break;
        }

        if ($category->slug === 'pillow' || strpos($category->slug, 'pillow')) {
            $is_pillow = true;
            break;
        }
    }
}

?>

<div id="product-<?php the_ID(); ?>" <?php post_class('custom-product-display'); ?>>
	
	<div class="product-content-wrapper">
        
		<div class="product-content">

			<div class="product-description">
				<?php
				// Display only the WYSIWYG editor content
				// the_content();
                echo '<div class="matt-product-wrapper ' . $category->slug . '">';
                echo '<div class="matt-details-wrapper">';
        
                get_template_part( 'woocommerce/single-product/mattress-details', 'mattress', array('is_mattress' => $is_mattress, 'is_bed' => $is_bed, 'is_pillow' => $is_pillow) );
                
                // echo do_shortcode('[matt_product_details]'); 
                echo '</div>';
                echo '<div class="matt-info-wrapper">';
                //  get_template_part( 'woocommerce/single-product/product-info', 'mattress' );

                get_template_part( 'woocommerce/single-product/mattress-info', 'mattress', array('is_mattress' => $is_mattress, 'is_bed' => $is_bed, 'is_pillow' => $is_pillow) );
                
                echo '</div>';
                echo '</div>';

				?>
			</div>
			
		</div><!-- .product-content -->
		
	</div><!-- .product-content-wrapper -->

</div><!-- #product-<?php the_ID(); ?> -->
