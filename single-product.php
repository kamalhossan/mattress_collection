<?php
/**
 * The template for displaying single product pages.
 * This template only shows the WYSIWYG editor content without WooCommerce functionalities.
 *
 * @package Salient Child Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

?>

<div class="container-wrap no-sidebar" data-midnight="dark">
	<div class="container main-content">
		
		<?php
		// Main content loop.
		if ( have_posts() ) :
			while ( have_posts() ) :
				the_post();
				
				// Get the custom product content template
				get_template_part( 'woocommerce/content-single-product' );
				
			endwhile;
		endif;
		?>

	</div><!--/container main-content-->
</div><!--/container-wrap-->

<?php get_footer(); ?>
