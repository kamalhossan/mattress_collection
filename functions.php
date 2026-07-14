<?php

add_action('wp_enqueue_scripts', 'salient_child_enqueue_styles', 100);
function salient_child_enqueue_styles()
{
	$nectar_theme_version = nectar_get_theme_version();
	wp_enqueue_style('slick', get_stylesheet_directory_uri() . '/css/slick.css');
	wp_enqueue_style('kunal-style', get_stylesheet_directory_uri() . '/kunal.css?v1');
	wp_enqueue_style('salient-child-style', get_stylesheet_directory_uri() . '/style.css', '', $nectar_theme_version);
	wp_enqueue_style('font-abs', get_stylesheet_directory_uri() . '/fonts/abc/stylesheet.css', array(), $nectar_theme_version, 'all');
	if (is_rtl()) {
		wp_enqueue_style('salient-rtl',  get_template_directory_uri() . '/rtl.css', array(), '1', 'screen');
	}
	wp_enqueue_script('slick', get_stylesheet_directory_uri() . '/js/slick.js', array('jquery'), null, true);
	wp_enqueue_script('custom-script', get_stylesheet_directory_uri() . '/js/custom-script.js', array('jquery','swiper-js'), null, true);
	
// 	if ( is_page(239) || is_page(571) || is_page(443) ) {
		wp_enqueue_style('swiper','https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
		wp_enqueue_script('swiper-js','https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',array(),null,true);
// 	}

	// $wp_ajx_array = array('wp_ajax_url' => admin_url('admin-ajax.php'));
	// wp_localize_script('custom-script', 'admin_ajaax', $wp_ajx_array); //
	$wp_ajx_array = array('ajax_url' => admin_url('admin-ajax.php'));
	wp_localize_script('custom-script', 'admin_ajaax', $wp_ajx_array);//  localize ajax url in script
}

require_once(get_stylesheet_directory() . "/includes/custom-function.php");
require_once(get_stylesheet_directory() . "/includes/api-function.php");
require_once(get_stylesheet_directory() . "/includes/vc-widgets.php");

//add SVG to allowed file uploads
add_action('upload_mimes', 'add_file_types_to_uploads');
function add_file_types_to_uploads($file_types)
{
	$new_filetypes = array();
	$new_filetypes['svg'] = 'image/svg+xml';
	$file_types = array_merge($file_types, $new_filetypes);
	return $file_types;
}

add_filter("redux/salient_redux/field/typography/custom_fonts", "salient_redux_custom_fonts");
function salient_redux_custom_fonts()
{
	return array(
		'Custom Fonts' => array(
			'Rockness' => 'Rockness',
			'Product Sans' => 'Product Sans',
		)
	);
}

add_shortcode("mattress_collection", "mattress_collection_shortcode");
function mattress_collection_shortcode()
{
	ob_start();
?>
<div class="mattress-collection-grid">
	<!-- Mattresses -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mattress.png" alt="Mattresses">
		<div class="card-title">MATTRESSES</div>
		<div class="card-desc">From hybrid to orthopaedic, find the perfect base for better sleep.</div>
		<a href="/mattresses" class="nectar-button medium see-through">Browse Mattresses</a>
	</div>
	<!-- Designer & Simply Beds -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/designer-bed.png" alt="Designer & Simply Beds">
		<div class="card-title">DESIGNER & SIMPLY BEDS</div>
		<div class="card-desc">Choose your frame, storage, and finish for a custom sleep experience.</div>
		<a href="/beds" class="nectar-button medium see-through">Browse Beds</a>
	</div>
	<!-- Pillows & Accessories -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/accessories.png" alt="Pillows & Accessories">
		<div class="card-title">PILLOWS & ACCESSORIES</div>
		<div class="card-desc">Pillows, toppers and everything else for a complete sleep setup.</div>
		<a href="/accessories" class="nectar-button medium see-through">Browse Accessories</a>
	</div>
	<!-- Kid's Sleep Solutions -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/kids-solution.png" alt="Kid's Sleep Solutions">
		<div class="card-title">KID'S SLEEP SOLUTIONS</div>
		<div class="card-desc">Hypoallergenic, safe, and supportive mattresses for growing bodies.</div>
		<a href="/kids-sleep" class="nectar-button medium see-through">Browse Solutions</a>
	</div>
	<!-- Commercial Systems -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/commercial-systems.png" alt="Commercial Systems">
		<div class="card-title">COMMERCIAL SYSTEMS</div>
		<div class="card-desc">For delivery vans, dormitories, and commercial needs: innovative sleep solutions.</div>
		<a href="/commercial-systems" class="nectar-button medium see-through">Browse Commercial</a>
	</div>
	<!-- Mechanical Solutions -->
	<div class="mattress-collection-card">
		<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/mechanical-solutions.png" alt="Mechanical Solutions">
		<div class="card-title">MECHANICAL SOLUTIONS</div>
		<div class="card-desc">Adjustable beds, special support systems, and more for home and special needs.</div>
		<a href="/mechanical-solutions" class="nectar-button medium see-through">Browse Solutions</a>
	</div>
</div>
</div>
<?php
	return ob_get_clean();
}

add_shortcode('our_most_loved_sleep_systems', 'our_most_loved_sleep_systems_shortcode');
function our_most_loved_sleep_systems_shortcode() { ob_start(); 
    $page_id = get_the_ID();

    $prev_arrow = "https://mattresscollection.bisontesting.com/wp-content/uploads/2025/07/Icon-Button.png";
	$next_arrow = "https://mattresscollection.bisontesting.com/wp-content/uploads/2025/07/rIcon-Button.png";

    if (is_page(25)) { // replace 123 with your page ID
        $prev_arrow = "/wp-content/uploads/2025/07/Icon-Button.png";
		$next_arrow = "/wp-content/uploads/2025/07/rIcon-Button.png";
    }
?>
<div class="our-most-loved-sleep-systems-wrapper">
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<div class="badge_overlay">
					<span class="sale_badge">BESTSELLER</span>
				</div>
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<div class="badge_overlay">
					<span class="off_badge">Sale</span>
				</div>
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
	<div class="sleep-systems-item">
		<div class="sleep-systems-item-inner">
			<div class="sleep-systems-item-top">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/sleep-systems.png" alt="Sleep Systems">
			</div>
			<div class="sleep-systems-item-bottom">
				<div class="sleep-systems-item-top-text-title">
					<h4 class="sleep-systems-item-title">Aloe Vera Medium Memory Foam Pillow (low)</h4>
					<span class="item-price">€55.00</span>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="arrows_section">
	<span class="prev"><img src="<?php echo esc_url($prev_arrow); ?>" alt="Previous"></span>
<span class="next"><img src="<?php echo esc_url($next_arrow); ?>" alt="Next"></span> 
 
</div>

<?php
	return ob_get_clean();
}

add_shortcode('mattress_collection_trust_slider_old', 'mattress_collection_trust_slider_shortcode_old');
function mattress_collection_trust_slider_shortcode_old() {
	ob_start();
	$count = 10;
?>
<div class="mattress_collection_trust_wrapper">
	<div class="mattress_collection_trust_slider">
		<?php for($i = 0; $i < $count; $i++){
		?>
		<div class="slider-item-wrapper">
			<div class="mattress_collection_trust_slider_item">
				<div class="trust-features-image">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/trust-slider.png" alt="Trust Features Image">
				</div>
				<div class="trust-slider-content">
					<div class="content-top-icons">
						<ul class="features-item">
							<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/medical-plus.png" alt=""></li>
							<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/medical-sign.png" alt=""></li>
							<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/location.png" alt=""></li>
							<li><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/certificate.png" alt=""></li>
						</ul>
					</div>
					<div class="content-bottom-text">
						<h4 class="title">
							Medical grade quality
						</h4>
						<p class="desc">Certified materials and ergonomic designs trusted by hospitals and health institutions.</p>
					</div>
				</div>
			</div>
		</div>
		<?php }
		?>
	</div>
</div>
<?php 
	return ob_get_clean();
}

add_shortcode('mattress_happy_customer_slider', 'mattress_happy_customer_slider_shortcode');
function mattress_happy_customer_slider_shortcode()
{
	ob_start();

	$loop = 10;
?>
<div class="mattress_happy_customer_wrapper">
	<div class="mattress_happy_customer_slider">
		<?php 
	for($i = 0; $i < $loop; $i++){
		$rating = 5;
		?>
		<div class="slider-item-wrapper">
			<div class="mattress-happy-customer-slider-item">
				<div class="product-features-image">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/pillow.png" alt="Trust Features Image">
				</div>
				<div class="happy-customer-content">
					<div class="ratings">
						<?php for($j = 0; $j < $rating; $j++) {
						?>
						<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/star-rating.png" alt="Star Rating">
						<?php }
						?>
					</div>
					<div class="customer-review-content">
						<p class="feedback">
							After years of back pain, I finally sleep through the night. Their team guided me perfectly!</p>
						<span class="customer-info">
							Carla M, 28 - Mellieha
						</span>
						<a href="#" target="_blank" class="original-product">View the orginal pillow</a>
					</div>
				</div>
			</div>
		</div>
		<?php }
		?>
	</div>
</div>
<?php 
	return ob_get_clean();
}

add_shortcode('mattress_sleep_smart_slider', 'mattress_sleep_smart_slider_shortcode');
function mattress_sleep_smart_slider_shortcode()
{
	ob_start();
	$loop = 10;
?>
<div class="mattress_sleep_smart_wrapper">
	<div class="mattress_sleep_smart_slider">
		<?php 
	for($i = 0; $i < $loop; $i++){
		$image_number = ($i % 3) + 1;
		?>
		<div class="sleep-slider-item-wrapper">
			<div class="smart-sleep-item">
				<div class="post-features-image">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/post-<?php echo $image_number;?>.png" alt="Trust Features Image">
				</div>
				<div class="smart-sleep-content">
					<h4 class="post-title">Mattress Types Explained</h4>
					<a href="#" target="_blank" class="read-more">Read more ></a>
				</div>
			</div>
		</div>
		<?php
	}
		?>
	</div>
</div>
<?php
	return ob_get_clean();
}


function bed_product_grid_enqueue_styles() {

	wp_enqueue_style( 'google-font-dm-sans', 'https://fonts.googleapis.com/css2?family=DM+Sans&display=swap', false );


	wp_enqueue_style( 'bed-product-grid-style', plugins_url( 'style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'bed_product_grid_enqueue_styles' );




function bed_product_grid_shortcode() {
	ob_start();

	$products = array(
		array('title' => 'Alexia', 'desc' => 'Alexia blends old and new tradition...', 'price' => '€499.00'),
		array('title' => 'Allison', 'desc' => 'Beautiful Contoured Headboard...', 'price' => '€499.00'),
		array('title' => 'Asia', 'desc' => 'The Asia bed has its lightness...', 'price' => '€499.00'),
		array('title' => 'Ava', 'desc' => 'Evoking the timeless, classic look...', 'price' => '€499.00'),
		array('title' => 'Bella', 'desc' => 'Bella combines sleek modern design...', 'price' => '€549.00'),
		array('title' => 'Clara', 'desc' => 'Clara bed adds elegance with its tufted headboard...', 'price' => '€599.00'),
		array('title' => 'Diana', 'desc' => 'Diana is a perfect fusion of minimalism...', 'price' => '€529.00'),
		array('title' => 'Ella', 'desc' => 'Ella’s contemporary look makes it an instant favorite.', 'price' => '€579.00'),
		array('title' => 'Fiona', 'desc' => 'Fiona’s classic charm fits perfectly in any decor.', 'price' => '€499.00'),
		array('title' => 'Grace', 'desc' => 'Grace bed exudes sophistication with its sleek lines.', 'price' => '€599.00'),
		array('title' => 'Hanna', 'desc' => 'Hanna bed brings luxurious comfort and modern appeal.', 'price' => '€549.00'),
		array('title' => 'Ivy', 'desc' => 'Ivy is designed to offer both elegance and durability.', 'price' => '€529.00'),
	);

	echo '<div class="bed-product-grid" id="bed-product-grid">';
	$chunks = array_chunk($products, 4); // 4 টা করে row
	foreach ($chunks as $row) {
		echo '<div class="bed-product-row">';
		foreach ($row as $product) {
?>
<div class="bed-product-column">
	<div class="bed-product-item">
		<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/bed1.jpg'); ?>" alt="<?php echo esc_attr($product['title']); ?>">
		<div class="bed-product-title"><?php echo esc_html($product['title']); ?></div>
		<div class="bed-product-description"><?php echo esc_html($product['desc']); ?></div>
		<div class="bed-product-price"><?php echo esc_html($product['price']); ?></div>
		<div class="bed-product-button"><a href="#">View</a></div>
	</div>
</div>
<?php
								   }
		echo '</div>';
	}
	echo '</div>';

	echo '<div id="load-more-container" style="text-align:center; margin:20px 0;">
            <button id="load-more-button" data-offset="0" style="cursor:pointer;">Load More</button>
          </div>';

	return ob_get_clean();
}
add_shortcode('bed_product_grid', 'bed_product_grid_shortcode');




function bed_product_load_more_ajax() {
	$offset = intval($_POST['offset']);

	$args = array(
		'post_type' => 'bed_product',
		'posts_per_page' => 12,
		'offset' => $offset,
		'post_status' => 'publish',
	);

	$query = new WP_Query($args);

	if ($query->have_posts()) {
		ob_start();
		while ($query->have_posts()) {
			$query->the_post();
?>
<div class="bed-product-row">
	<div class="bed-product-column">
		<div class="bed-product-item">
			<img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'medium'); ?>" alt="<?php the_title(); ?>">
			<div class="bed-product-title"><?php the_title(); ?></div>
			<div class="bed-product-description"><?php the_excerpt(); ?></div>
			<div class="bed-product-price"><?php echo get_post_meta(get_the_ID(), 'product_price', true); ?></div>
			<div class="bed-product-button"><a href="<?php the_permalink(); ?>">View</a></div>
		</div>
	</div>
</div>
<?php
		}
		wp_send_json_success(ob_get_clean());
	} else {
		wp_send_json_error('No more products');
	}

	wp_die();
}
add_action('wp_ajax_bed_product_load_more', 'bed_product_load_more_ajax');
add_action('wp_ajax_nopriv_bed_product_load_more', 'bed_product_load_more_ajax');





function bed_product_grid_static_shortcode() {
	ob_start();

	$products = array(
		array('title' => 'Alexia', 'desc' => 'Alexia blends old and new tradition for an elegant and stylish focal point for your bedroom.', 'price' => '€499.00'),
		array('title' => 'Allison', 'desc' => 'Beautiful Contoured Headboard with buttons in the centre. Stylish and comfortable.', 'price' => '€499.00'),
		array('title' => 'Asia', 'desc' => 'The Asia bed has its lightness, like simplicity. Glamorous and chic.', 'price' => '€499.00'),
		array('title' => 'Ava', 'desc' => 'Evoking the timeless, classic look, the Ava bed is a stunning centerpiece.', 'price' => '€499.00'),
		array('title' => 'Bella', 'desc' => 'Bella combines sleek modern design with luxurious comfort.', 'price' => '€549.00'),
		array('title' => 'Clara', 'desc' => 'Clara bed adds elegance with its tufted headboard and soft curves.', 'price' => '€599.00'),
	);

	$chunks = array_chunk($products, 4);  // 4 ta kore row e

	echo '<div class="bed-product-grid">';
	foreach ($chunks as $row) {
		echo '<div class="bed-product-row">';
		foreach ($row as $product) {
?>
<div class="bed-product-column">
	<div class="bed-product-item">
		<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/bed1.jpg'); ?>" alt="<?php echo esc_attr($product['title']); ?>">
		<div class="bed-product-title"><?php echo esc_html($product['title']); ?></div>
		<div class="bed-product-description"><?php echo esc_html($product['desc']); ?></div>
		<div class="bed-product-price"><?php echo esc_html($product['price']); ?></div>
		<div class="bed-product-button"><a href="#">View</a></div>
	</div>
</div>
<?php
								   }
		echo '</div>';
	}
	echo '</div>';

	return ob_get_clean();
}
add_shortcode('bed_product_static', 'bed_product_grid_static_shortcode');





// pillow page


function static_pillow_products_shortcode() {
	ob_start();
	$products = array(
		array(
			'title' => 'ALOE VERA MEDIUM MEMORY FOAM PILLOW',
			'desc' => '9cm Height – The Aloe Vera Medium Firm Memory Foam Pillow is the firmest of our Aloe Vera pillows.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
		array(
			'title' => 'ALOE VERA PLUSH MEMORY FOAM PILLOW',
			'desc' => '15cm Height – The Aloe Vera Plush Memory Foam Pillow is the softest of our Aloe Vera pillows. It has the usual soap bar shape and has holes.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
		array(
			'title' => 'COOL GEL MEMORY FOAM PILLOW',
			'desc' => '12cm Height – The Cool Gel Pillow stays cool to the touch as it contours to your body shape keeping you relaxed and at right temperature.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
		array(
			'title' => 'COOL GEL CONTOUR MEMORY FOAM PILLOW',
			'desc' => '12cm Height – The Cool Gel Pillow stays cool to the touch as it contours to your body shape keeping you relaxed and at right temperature.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
		array(
			'title' => 'ORGANIC COTTON PILLOW CASE',
			'desc' => 'The Organic Cotton pillow case features a soft 100% organic cotton fabric for a luxurious and healthy sleep.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
		array(
			'title' => 'TENCEL PILLOW CASE',
			'desc' => 'Eucalyptus fibre, often known by its brand name TENCEL™, is a revolutionary new plant-based fibre.',
			'height' => '9cm',
			'medium' => 'Medium',
			'material' => '',
			'fibre' => '',
			'price' => '€55.00',
		),
	);

	echo '<div class="bed-product-grid">';
	$count = 0;
	foreach ($products as $product) {
		if ($count % 4 == 0) echo '<div class="bed-product-row">';

?>
<div class="bed-product-column">
	<div class="bed-product-item">
		<div class="bed-product-title"><?php echo esc_html($product['title']); ?></div>
		<div class="bed-product-description"><?php echo esc_html($product['desc']); ?></div>
		<div class="bed-product-image">
			<img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/images/pillow.jpg'); ?>" alt="<?php echo esc_attr($product['title']); ?>">
		</div>
		<div class="bed-product-attributes">
			<div class="attribute-row">
				<span>Height: <?php echo esc_html($product['height']); ?></span>
				<span>Medium: <?php echo esc_html($product['medium']); ?></span>
			</div>
			<div class="attribute-row">
				<span>Material: <?php echo esc_html($product['material']); ?></span>
				<span>Fibre: <?php echo esc_html($product['fibre']); ?></span>
			</div>
		</div>
		<div class="bed-product-price"><?php echo esc_html($product['price']); ?></div>
	</div>
</div>
<?php

		$count++;
		if ($count % 4 == 0) echo '</div>';
	}
	if ($count % 4 != 0) echo '</div>'; // Close last row if not completed

	echo '</div>';
	return ob_get_clean();
}
add_shortcode('static_pillow_products', 'static_pillow_products_shortcode');

function product_gallery() {
	return '
        <div class="gallery_slider">
			<div class="slide-item">
				<img src="/wp-content/uploads/2025/08/Group-1606.jpg">
			</div>
        </div>
		<ul class="featured_icon">
			<li><img src="/wp-content/uploads/2025/08/Vector.svg">10 Year Gurantee</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-1.svg">30-Day Sleep trial</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-2-1.svg">Free Delivery</li>
		</ul>';
}
add_shortcode('product_gallery', 'product_gallery');

function product_details() {
	$title = get_the_title();
	return '
		<div class="offer_section">
			<a href="#" class="nectar-button offer-btn">SPRING DEALS</a>
			<h4>Up to 20% off on mattresses!*</h4>
		</div>
		<div class="product_details">
			 <h3>' . esc_html($title) . '</h3>
			<span class="tag_size">Firmness level: <span>Medium</span></span>
			<p>Alexia blends old and new tradition for an elegant and stylish focal point for your bedroom. The design is made of shapes, lines and emotions utilising tactile perceptions and materials.</p> 
			<div class="quantity">
				<p>Select Size <span>Size guide</span></p>
				<select name="cars">
					<optgroup label="Queen">
						<option value="volvo">59.5W x 79.5L x 15H - Weight 16 kg</option>
					</optgroup>
				</select>
				<button type="submit" class="nectar-button">REQUEST A QUOTE</button>
			</div>
			<p class="stock_tag">In Stock -  Delivered within 3-5 days</p>
		</div>
    ';
}
add_shortcode('product_details', 'product_details');

function column_details() {
	return '
		<div class="fifty-column">
			<div class="column_card">
				<div class="card_icon">
					<img src="/wp-content/uploads/2025/08/Group-1645.svg">
				</div>
				<div class="card_content">
					<h6>Top Layer</h6>
					<p>The Top layer of this mattress is made of a generous 7cm of Natural Visco Elastic Memory Foam, giving more than enough depth to contour to your entire body shape and support those sensitive and protruding parts of our bodies.</p>
				</div>
			</div>
			<div class="column_card">
				<div class="card_icon">
					<img src="/wp-content/uploads/2025/08/Group-1629.svg">
				</div>
				<div class="card_content">
					<h6>Bottom Layer</h6>
					<p>The bottom part of this mattress is made with 13cm of High Density Water based support foam, a highly supportive and responsive water based foam that has been laboratory tested to give the most rejuvenating and muscle relaxing sleep available. By replacing the toxic solvents usually used to make and clean foam with natural water, this support foam also ensures you sleep in a truly healthy environment.</p>
				</div>
			</div>
		</div>';
}
add_shortcode('column_details', 'column_details');

function icon_with_slider() {
	return '
		<div class="icon_with_slider swiper">
			<div class="swiper-wrapper">
				<div class="icon_with_slider_card swiper-slide">
					<img src="/wp-content/uploads/2025/08/Vector-3.svg">
					<h5>Zero Motion Transfer</h5>
					<p>Individually wrapped coils isolate movement, ensuring undisturbed sleep for you and your partner.</p>
				</div>
				<div class="icon_with_slider_card swiper-slide">
					<img src="/wp-content/uploads/2025/08/Vector-1-1.svg">
					<h5>Six-Layer Construction</h5>
					<p>Engineered with six unique layers, the mattress combines cooling, comfort, and support for optimal sleep.</p>
				</div>
			</div>
			<div class="swiper-scrollbar"></div>
		</div>';
}
add_shortcode('icon_with_slider', 'icon_with_slider');

function product_faq() {
	return '
    <div class="faq_wrapper">
	<details class="faq_section">
		<summary>Foundation</summary>
		<div class="tab_content">
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
		</div>
	</details>
		<details class="faq_section">
		<summary>Benefits</summary>
		<div class="tab_content">
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
		</div>
	</details>
	<details class="faq_section">
		<summary>Product Details</summary>
		<div class="tab_content">
			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>
		</div>
	</details>
    </div>
	';
}
add_shortcode('product_faq', 'product_faq');

// add_shortcode('product_faq', 'product_faq');

function certification() {
	return '
	<ul class="grid grid-4">
		<li>
			<img src="/wp-content/uploads/2025/08/Group-1646.svg">
			<span>Certification 1</span>
		</li>
		<li>
			<img src="/wp-content/uploads/2025/08/Group-1646.svg">
			<span>Certification 2</span>
		</li>
		<li>
			<img src="/wp-content/uploads/2025/08/Group-1646.svg">
			<span>Certification 3</span>
		</li>
		<li>
			<img src="/wp-content/uploads/2025/08/Group-1646.svg">
			<span>Certification 4</span>
		</li>
	</ul>
	';
}
add_shortcode('certification', 'certification');

function product_image() {
	return '
	<div class="product_images">
		<div class="card_img width-54">
			<img src="/wp-content/uploads/2025/08/Rectangle-119.jpg">
		</div>
		<div class="card_img width-43">
			<img src="/wp-content/uploads/2025/08/Rectangle-120.jpg">
		</div>
	</div>
	<div class="product_images">
		<div class="card_img width-42">
			<img src="/wp-content/uploads/2025/08/Rectangle-120.jpg">
		</div>
		<div class="card_img width-54">
			<img src="/wp-content/uploads/2025/08/Rectangle-119.jpg">
		</div>
	</div>';
}
add_shortcode('product_image', 'product_image');

function property_icon() {
	return '
	<div class="property_icon">
		<h4>Properties</h4>
		<ul>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-4.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-5.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-1-2.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-4.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-5.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-1-2.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
		</ul>
	</div>';
}
add_shortcode('property_icon', 'property_icon');

function addons_product() {
	return '
	<div class="addons_product">
		<h5><span>Properties</span></h5>
		<div class="addons_product_list">
			<div class="addons_product_card">
				<span class="badge_tag">Classic Comfort</span>
				<div class="addons_card">
					<div class="addons_img">
						<img src="/wp-content/uploads/2025/08/Rectangle-109.jpg">
					</div>
					<div class="addons_content">
						<div>
							<h6>Alexia</h6>
							<p>€365.00</p>
						</div>
						<button type="submit" class="outfill-button">Add</button>
					</div>
				</div>
			</div>
			<div class="addons_product_card">
				<span class="badge_tag">Most Popular</span>
				<div class="addons_card">
					<div class="addons_img">
						<img src="/wp-content/uploads/2025/08/Rectangle-109.jpg">
					</div>
					<div class="addons_content">
						<div>
							<h6>Beatrix</h6>
							<p>€365.00</p>
						</div>
						<button type="submit" class="outfill-button">Add</button>
					</div>
				</div>
			</div>
			<div class="addons_product_card">
				<span class="badge_tag">Premium Choice</span>
				<div class="addons_card">
					<div class="addons_img">
						<img src="/wp-content/uploads/2025/08/Rectangle-109.jpg">
					</div>
					<div class="addons_content">
						<div>
							<h6>Corinne</h6>
							<p>€365.00</p>
						</div>
						<button type="submit" class="outfill-button">Add</button>
					</div>
				</div>
			</div>
		</div>
		<p class="learn_more">
			<a href="#">Learn About Our Bed Frames</a>
		</p>
	</div>';
}
add_shortcode('addons_product', 'addons_product');

function product_request_a_quote_form_shortcode() {
    ob_start();
    $theme_url = get_stylesheet_directory_uri();
    echo '<div class="product_request_a_quote_form">';
    echo '<div class="form-wrapper">';
    echo do_shortcode('[forminator_form id="424"]');
    echo '</div>';
    echo '<a href="/find-a-store" class="nectar-button location_btn"><img src=" ' . $theme_url . '/images/map-pin.svg" alt="Map Pin"> Find a store</a>';
    echo '</div>';
	return ob_get_clean();
}
add_shortcode('product_request_a_quote_form', 'product_request_a_quote_form_shortcode');

function product_list() {
	return '
        <div class="product-list">
            <div class="product-item">
                <div class="featured-image">
                    <img src="/wp-content/uploads/2025/08/Rectangle-119.jpg" alt="Product 1">
                    <div class="badge_overlay">
                        <span class="sale_badge">BESTSELLER</span>
                        <span class="off_badge">20% OFF</span>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Alexia</h3>
                    <p class="price">From <span>€499.00</span> <del>€79.00</del> </p>
                    <a href="#" class="nectar-button">Contact Us</a>
                </div>
            </div>
            <div class="product-item">
                <div class="featured-image">
                    <img src="/wp-content/uploads/2025/08/Rectangle-119.jpg" alt="Product 1">
                    <div class="badge_overlay">
                        <span class="off_badge">20% OFF</span>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Alexia</h3>
                    <p class="price">From <span>€499.00</span> <del>€79.00</del> </p>
                    <a href="#" class="nectar-button">Contact Us</a>
                </div>
            </div>
            <div class="product-item">
                <div class="featured-image">
                    <img src="/wp-content/uploads/2025/08/Rectangle-119.jpg" alt="Product 1">
                    <div class="badge_overlay">
                        <span class="off_badge">20% OFF</span>
                    </div>
                </div>
                <div class="product-info">
                    <h3>Alexia</h3>
                    <p class="price">From <span>€499.00</span> <del>€79.00</del> </p>
                    <a href="#" class="nectar-button">Contact Us</a>
                </div>
            </div>
        </div>
	';
}
add_shortcode('product_list', 'product_list');

function breadcrumb() {
	global $post;
	$separator = ' / ';
	$breadcrumb = '<div class="breadcrumb">';
	$breadcrumb .= '<a href="' . home_url() . '">Home</a>' . $separator;

	if (is_category() || is_single()) {
		$categories = get_the_category();
		if ($categories) {
			$breadcrumb .= get_category_parents($categories[0], true, $separator);
		}
		if (is_single()) {
			$breadcrumb .= '<span class="active">' . get_the_title() . '</span>';
		}
	} elseif (is_page() && !is_front_page()) {
		if ($post->post_parent) {
			$parent_id = $post->post_parent;
			$breadcrumbs = [];
			while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id = $page->post_parent;
			}
			$breadcrumbs = array_reverse($breadcrumbs);
			foreach ($breadcrumbs as $crumb) {
				$breadcrumb .= $crumb . $separator;
			}
		}
		$breadcrumb .= '<span class="active">' . get_the_title() . '</span>';
	} elseif (is_search()) {
		$breadcrumb .= 'Search results for "' . get_search_query() . '"';
	} elseif (is_404()) {
		$breadcrumb .= 'Error 404';
	} else {
		$breadcrumb .= '<span class="active">Shop</span>';
	}

	$breadcrumb .= '</div>';

	return $breadcrumb;
}
add_shortcode('breadcrumb', 'breadcrumb');

add_shortcode('add_to_cart_btn_custom', 'add_to_cart_btn_custom_func');
function add_to_cart_btn_custom_func() {
	return '
	<a href="#" class="nectar-button location_btn"><img src="/wp-content/uploads/2025/08/Vector-2-2.svg"> Find a store</a>
	';
}


add_shortcode('add_sale_pillow_image', 'add_sale_pillow_image_func');
function add_sale_pillow_image_func() {
	return '
	<div class="product_pillow_images product_images">
		<div class="card_img width-54">
			<img src="/wp-content/uploads/2025/08/susan-wilkinson-o_lPww2A2t0-unsplash-2.jpg">
		</div>
		<div class="card_img width-43">
			<img src="https://mattresscollection.bisontesting.com/wp-content/uploads/2025/08/Rectangle-119.jpg">

		</div>
	</div>
	';
}

function gallery_thumb() {
	return '
        <div class="gallery_slider gallery-with-thumbs">
			<div class="swiper gallery_large">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
				</div>
			</div>
			<div thumbsSlider="" class="swiper gallery_thumb">
				<div class="swiper-wrapper">
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
					<div class="swiper-slide">
						<img src="/wp-content/themes/salient-child/images/placeholder.jpg" />
					</div>
				</div>
			</div>
        </div>
		<!--
		<ul class="featured_icon">
			<li><img src="/wp-content/uploads/2025/08/Vector.svg">10 Year Gurantee</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-1.svg">30-Day Sleep trial</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-2-1.svg">Free Delivery</li>
		</ul>-->';
}
add_shortcode('gallery_thumb', 'gallery_thumb');

function variant_option() {
	return '
		<div class="variant_option">
			<h5 class="line-with-title"><span>START CUSTOMISING</span></h5>
			<div class="variant_dropdown">
				<div class="toggle_dropdown">
					<h4 class="toggle_title active">Select an Option</h4>
					<div class="toggle_content active">
						<label for="fabric1">
							<input type="radio" id="fabric1" name="fabric" value="fabric1">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category A</span>
						</label>
						<label for="fabric2">
							<input type="radio" id="fabric2" name="fabric" value="fabric2">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category B</span>
						</label>
						<label for="fabric3">
							<input type="radio" id="fabric3" name="fabric" value="fabric3">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category C</span>
						</label>
						<label for="fabric4">
							<input type="radio" id="fabric4" name="fabric" value="fabric4">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category D</span>
						</label>
						<label for="fabric5">
							<input type="radio" id="fabric5" name="fabric" value="fabric5">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category E</span>
						</label>
						<label for="fabric6">
							<input type="radio" id="fabric6" name="fabric" value="fabric6">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category F</span>
						</label>
					</div>
				</div>
				<div class="toggle_dropdown">
					<h4 class="toggle_title">Select Storage Type</h4>
					<div class="toggle_content">
						<label for="Storage1">
							<input type="radio" id="Storage1" name="Storage" value="Storage1">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category A</span>
						</label>
						<label for="Storage2">
							<input type="radio" id="Storage2" name="Storage" value="Storage2">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category B</span>
						</label>
						<label for="Storage3">
							<input type="radio" id="Storage3" name="Storage" value="Storage3">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category C</span>
						</label>
					</div>
				</div>
				<div class="toggle_dropdown">
					<h4 class="toggle_title">Choose Slats</h4>
					<div class="toggle_content">
						<label for="slats1">
							<input type="radio" id="slats1" name="slats" value="slats1">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category A</span>
						</label>
						<label for="slats2">
							<input type="radio" id="slats2" name="slats" value="slats2">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category B</span>
						</label>
						<label for="slats3">
							<input type="radio" id="slats3" name="slats" value="slats3">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category C</span>
						</label>
					</div>
				</div>
				<div class="toggle_dropdown">
					<h4 class="toggle_title">Bed Frame Model</h4>
					<div class="toggle_content">
						<label for="frame1">
							<input type="radio" id="frame1" name="frame" value="frame1">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category A</span>
						</label>
						<label for="frame2">
							<input type="radio" id="frame2" name="frame" value="frame2">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category B</span>
						</label>
						<label for="frame3">
							<input type="radio" id="frame3" name="frame" value="frame3">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category C</span>
						</label>
					</div>
				</div>
				<div class="toggle_dropdown">
					<h4 class="toggle_title">Select Feet / Legs</h4>
					<div class="toggle_content">
						<label for="feet1">
							<input type="radio" id="feet1" name="feet" value="feet1">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category A</span>
						</label>
						<label for="feet2">
							<input type="radio" id="feet2" name="feet" value="feet2">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category B</span>
						</label>
						<label for="feet3">
							<input type="radio" id="feet3" name="feet" value="feet3">
							<img src="/wp-content/uploads/2025/08/Product-Category-Icon.jpg" />
							<span>Category C</span>
						</label>
					</div>
				</div>
			</div>
		</div>';
}
add_shortcode('variant_option', 'variant_option');

add_shortcode('product_Mattress_detail', 'product_Mattress_detail_func');
function product_Mattress_detail_func() {
	return '
		<div class="offer_section">
			<a href="#" class="nectar-button offer-btn">SPRING DEALS</a>
			<h4>Up to 20% off on mattresses!*</h4>
		</div>
		<div class="product_details">
			<h3>Aloe Vera Medium Memory Foam Pillow</h3>
			<p>Alexia blends old and new tradition for an elegant and stylish focal point for your bedroom. The design is made of shapes, lines and emotions utilising tactile perceptions and materials.</p> 
			<div class="quantity">
				<p>Select Size <span>Size guide</span></p>
				<select name="cars">
					<optgroup label="Queen">
						<option value="volvo">59.5W x 79.5L x 15H - Weight 16 kg</option>
					</optgroup>
				</select>
				<button type="submit" class="nectar-button">REQUEST A QUOTE</button>
			</div>
			<p class="stock_tag">In Stock -  Delivered within 3-5 days</p>
		</div>';
}

add_shortcode('perfer_Mattres_icon', 'perfer_Mattres_icon_func');
function perfer_Mattres_icon_func() {
	return '<div class="property_icon">
			<h4>Properties</h4>
			<div class="property_ul_item">
			<ul>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-4.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-5.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-1-2.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
		</ul>
		<ul>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-4.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-5.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
			<li>
				<span class="icon_bar">
					<img src="/wp-content/uploads/2025/08/Vector-1-2.svg">
				</span>
				<p>Perfect for hot sleepers</p>
			</li>
		</ul>
		</div>
	</div>';
}

function filter_section() {
	return '
        <div class="filter">
            <button class="filter-button">
                Filter
                <img src="/wp-content/uploads/2025/08/Vector-1-3.svg">
            </button>
            <div class="filter-form">
                <div class="filter-header">
                    <h4>FILTER & SORT</h4>
                    <img src="/wp-content/uploads/2025/08/Vector-2-3.svg">
                </div>
                <form action="" method="get">
                    <details class="filter-group">
                        <summary class="filter-toggle">
                            Mattress Type
                            <img src="/wp-content/uploads/2025/08/Vector-7.svg">
                        </summary>
                        <div class="filter-options">
                            <label>
                                <input type="checkbox" name="mattress" value="mattress1">
                                <span>Foam</span>
                            </label>
                            <label>
                                <input type="checkbox" name="mattress" value="mattress2">
                                <span>Hybrid</span>
                            </label>
                            <label>
                                <input type="checkbox" name="mattress" value="mattress3">
                                <span>Cooling</span>
                            </label>
                        </div>
                    </details>
                    <details class="filter-group">
                        <summary class="filter-toggle">
                            Firmness Level
                            <img src="/wp-content/uploads/2025/08/Vector-7.svg">
                        </summary>
                        <div class="filter-options">
                            <label>
                                <input type="checkbox" name="firmness" value="firmness1">
                                <span>Foam</span>
                            </label>
                            <label>
                                <input type="checkbox" name="firmness" value="firmness2">
                                <span>Hybrid</span>
                            </label>
                            <label>
                                <input type="checkbox" name="firmness" value="firmness3">
                                <span>Cooling</span>
                            </label>
                        </div>
                    </details>
                    <details class="filter-group">
                        <summary class="filter-toggle">
                            Size
                            <img src="/wp-content/uploads/2025/08/Vector-7.svg">
                        </summary>
                        <div class="filter-options">
                            <label>
                                <input type="checkbox" name="size" value="size1">
                                <span>Foam</span>
                            </label>
                            <label>
                                <input type="checkbox" name="size" value="size2">
                                <span>Hybrid</span>
                            </label>
                            <label>
                                <input type="checkbox" name="size" value="size3">
                                <span>Cooling</span>
                            </label>
                        </div>
                    </details>
                    <details class="filter-group">
                        <summary class="filter-toggle">
                            Brand
                            <img src="/wp-content/uploads/2025/08/Vector-7.svg">
                        </summary>
                        <div class="filter-options">
                            <label>
                                <input type="checkbox" name="brand" value="brand1">
                                <span>Foam</span>
                            </label>
                            <label>
                                <input type="checkbox" name="brand" value="brand2">
                                <span>Hybrid</span>
                            </label>
                            <label>
                                <input type="checkbox" name="brand" value="brand3">
                                <span>Cooling</span>
                            </label>
                        </div>
                    </details>
                </form>
            </div>
            <div class="overlay_filter"></div>
        </div>';
}
add_shortcode('filter_section', 'filter_section');


function product_sale_gallery() {
	return '
        <div class="gallery_slider">
			<div class="slide-item">
				<img id="mainImage" src="https://mattresscollection.bisontesting.com/wp-content/uploads/2025/08/Rectangle-95.jpg">
			</div>
        </div>
		<!--
		<ul class="featured_icon">
			<li><img src="/wp-content/uploads/2025/08/Vector.svg">10 Year Gurantee</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-1.svg">30-Day Sleep trial</li>
			<li><img src="/wp-content/uploads/2025/08/Vector-2-1.svg">Free Delivery</li>
		</ul>-->';
}
add_shortcode('product_sale_gallery', 'product_sale_gallery');

function sleep_culture_product() {
	return '
    <div class="featured-collection sleep_culture_product">
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
        <div class="product-card">
            <div class="shop-featured-img">
                <img src="/wp-content/uploads/2025/08/mattress-demo-photo-scaled.jpg">
            </div>
            <div class="shop-product-info">
                <h3>Spinal Alignment Support</h3>
                <p>Designed with zoned firmness to support natural spinal curvature.</p>
            </div>
        </div>
    </div>';
}
add_shortcode('sleep_culture_product', 'sleep_culture_product');

add_shortcode('post_date', function(){
	return '<span class="post-author">Date:' . get_the_date('m/d/Y') . '</span>';
});
add_shortcode('post_author', function(){
	return '<span class="post-author">Author:' . get_the_author() . '</span>';
});


// add_shortcode()

function render_salient_g_section_post($post_id = null)
{
    $post = get_post($post_id);
    if ($post && $post->post_type === 'salient_g_sections') {
        setup_postdata($post);
        echo '<div class="custom-salient-g-section">';
        echo apply_filters('the_content', $post->post_content);
        wp_reset_postdata();
        echo '</div>';
    } else {
        echo '<p>' . __('No salient_g_sections found.', 'salient') . '</p>';
    }
}


function render_product_size_selector($sizes = array()) {
    echo '<div class="quantity">';
    echo '<p>Select Size <span>Size guide</span></p>';
    echo '<select name="mattress_size" id="mattress_size_select">';
        foreach ($sizes as $size) {
            $size_dimensions = get_term_meta($size->term_id, 'size_dimensions', true);
            // Format: "Double - 135cm x 190cm"
            $display_text = $size->name;
            if (!empty($size_dimensions)) {
                $display_text .= ' - ' . $size_dimensions;
            }
            echo '<option value="' . esc_attr($size->slug) . '">' . esc_html($display_text) . '</option>';
        }
    echo '</select>';
    echo '<button type="submit" id="mattress_size_submit" class="nectar-button">REQUEST A QUOTE</button>';
    echo '</div>';
}