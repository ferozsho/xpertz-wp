<?php
/**
 * Premium LMS header and WooCommerce customer experience.
 *
 * @package EduPress
 */

defined( 'ABSPATH' ) || exit;

/**
 * Add WooCommerce theme support.
 */
function xpertz_commerce_theme_support() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
}
add_action( 'after_setup_theme', 'xpertz_commerce_theme_support', 20 );

/**
 * Return a Lucide-style header icon.
 *
 * @param string $name Icon name.
 * @return string
 */
function xpertz_commerce_icon( $name ) {
	$icons = array(
		'address'      => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
		'arrow-right'  => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
		'award'        => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13 17 22l-5-3-5 3 1.5-9"/>',
		'bell'         => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
		'book'         => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2Z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7Z"/>',
		'briefcase'    => '<rect width="20" height="14" x="2" y="7" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16M2 12h20"/>',
		'cart'         => '<circle cx="9" cy="20" r="1"/><circle cx="19" cy="20" r="1"/><path d="M3 4h2l2.7 10.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.6L21 8H6"/>',
		'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
		'chevron-down' => '<path d="m6 9 6 6 6-6"/>',
		'chevron-right'=> '<path d="m9 18 6-6-6-6"/>',
		'clock'        => '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>',
		'close'        => '<path d="M18 6 6 18M6 6l12 12"/>',
		'credit-card'  => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>',
		'dashboard'    => '<rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>',
		'download'     => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="m7 10 5 5 5-5M12 15V3"/>',
		'graduation'   => '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>',
		'heart'        => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l8.9 8.8 8.8-8.8a5.5 5.5 0 0 0 0-7.8Z"/>',
		'help'         => '<circle cx="12" cy="12" r="10"/><path d="M9.1 9a3 3 0 1 1 5.8 1c0 2-3 2-3 4M12 18h.01"/>',
		'layers'       => '<path d="m12.83 2.18 8 4a2 2 0 0 1 0 3.58l-8 4a2 2 0 0 1-1.66 0l-8-4a2 2 0 0 1 0-3.58l8-4a2 2 0 0 1 1.66 0Z"/><path d="m22 12.5-9.17 4.59a2 2 0 0 1-1.66 0L2 12.5M22 17.5l-9.17 4.59a2 2 0 0 1-1.66 0L2 17.5"/>',
		'login'        => '<path d="M10 17l5-5-5-5M15 12H3"/><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>',
		'mail'         => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'menu'         => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'orders'       => '<path d="M16 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8Z"/><path d="M16 3v5h5M8 13h8M8 17h5"/>',
		'profile'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
		'search'       => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'settings'     => '<path d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Z"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1A1.7 1.7 0 0 0 9 4.6a1.7 1.7 0 0 0 1-1.6v-.2h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z"/>',
		'shield-check' => '<path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3Z"/><path d="m9 12 2 2 4-4"/>',
		'star'         => '<path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01Z"/>',
		'ticket'       => '<path d="M2 9a3 3 0 0 0 0 6v4h20v-4a3 3 0 0 0 0-6V5H2Z"/><path d="M13 5v2M13 17v2M13 11v2"/>',
		'users'        => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return '<svg class="xhc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Enqueue the global commerce interface.
 */
function xpertz_commerce_enqueue_assets() {
	$css_path = get_template_directory() . '/assets/css/xpertz-commerce.min.css';
	$js_path  = get_template_directory() . '/assets/js/xpertz-commerce.js';

	wp_enqueue_style(
		'xpertz-commerce',
		get_template_directory_uri() . '/assets/css/xpertz-commerce.min.css',
		array( 'edupress-custom-design' ),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : ILOVEWP_VERSION
	);

	if ( wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	wp_enqueue_script(
		'xpertz-commerce',
		get_template_directory_uri() . '/assets/js/xpertz-commerce.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : ILOVEWP_VERSION,
		true
	);

	wp_localize_script(
		'xpertz-commerce',
		'xpertzCommerce',
		array(
			'ajaxUrl'           => admin_url( 'admin-ajax.php' ),
			'cartUrl'           => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
			'checkoutUrl'       => function_exists( 'wc_get_checkout_url' ) ? wc_get_checkout_url() : home_url( '/checkout/' ),
			'wishlistUrl'       => xpertz_commerce_page_url( 'wishlist' ),
			'giftNonce'         => wp_create_nonce( 'xpertz_gift_course' ),
			'searchNonce'       => wp_create_nonce( 'xpertz_search' ),
			'wishlistNonce'     => wp_create_nonce( 'xpertz_wishlist' ),
			'notificationsNonce'=> wp_create_nonce( 'xpertz_notifications' ),
			'isLoggedIn'        => is_user_logged_in(),
			'strings'           => array(
				'added'       => esc_html__( 'Added to cart', 'edupress' ),
				'addToCart'   => esc_html__( 'Add to cart', 'edupress' ),
				'error'       => esc_html__( 'Something went wrong. Please try again.', 'edupress' ),
				'giftAdded'   => esc_html__( 'Gift course added to cart', 'edupress' ),
				'noResults'   => esc_html__( 'No matching courses or articles found.', 'edupress' ),
				'removed'     => esc_html__( 'Removed from wishlist', 'edupress' ),
				'saved'       => esc_html__( 'Saved to wishlist', 'edupress' ),
				'savedLabel'  => esc_html__( 'Saved', 'edupress' ),
				'searching'   => esc_html__( 'Searching…', 'edupress' ),
				'shareDone'   => esc_html__( 'Wishlist link copied', 'edupress' ),
				'wishlist'    => esc_html__( 'Wishlist', 'edupress' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'xpertz_commerce_enqueue_assets', 30 );

/**
 * Find a page URL by slug with a stable fallback.
 *
 * @param string $slug Page slug.
 * @return string
 */
function xpertz_commerce_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

/** Track a bounded, anonymous recently viewed course list. */
function xpertz_commerce_track_recent_courses() {
	if ( ! is_singular( 'lp_course' ) ) {
		return;
	}
	$course_id = get_queried_object_id();
	$recent    = isset( $_COOKIE['xpertz_recent_courses'] ) ? wp_parse_id_list( sanitize_text_field( wp_unslash( $_COOKIE['xpertz_recent_courses'] ) ) ) : array();
	$recent    = array_slice( array_values( array_unique( array_merge( array( $course_id ), $recent ) ) ), 0, 6 );
	$value     = implode( ',', $recent );
	if ( function_exists( 'wc_setcookie' ) ) {
		wc_setcookie( 'xpertz_recent_courses', $value, time() + MONTH_IN_SECONDS );
	} else {
		setcookie( 'xpertz_recent_courses', $value, time() + MONTH_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true );
	}
	$_COOKIE['xpertz_recent_courses'] = $value;
}
add_action( 'template_redirect', 'xpertz_commerce_track_recent_courses', 20 );

/**
 * Return valid recently viewed courses excluding the current course.
 *
 * @param int $exclude Course ID to omit.
 * @return int[]
 */
function xpertz_commerce_recent_course_ids( $exclude = 0 ) {
	$recent = isset( $_COOKIE['xpertz_recent_courses'] ) ? wp_parse_id_list( sanitize_text_field( wp_unslash( $_COOKIE['xpertz_recent_courses'] ) ) ) : array();
	return array_values(
		array_filter(
			$recent,
			static fn( $course_id ) => (int) $course_id !== (int) $exclude && 'lp_course' === get_post_type( $course_id ) && 'publish' === get_post_status( $course_id )
		)
	);
}

/**
 * Default navigation used until an administrator assigns a WordPress menu.
 */
function xpertz_commerce_default_navigation() {
	$items = array(
		array( __( 'Home', 'edupress' ), home_url( '/' ) ),
		array( __( 'Courses', 'edupress' ), get_post_type_archive_link( 'lp_course' ) ?: xpertz_commerce_page_url( 'courses' ) ),
		array( __( 'Pricing', 'edupress' ), xpertz_commerce_page_url( 'pricing' ) ),
		array( __( 'About', 'edupress' ), xpertz_commerce_page_url( 'about' ) ),
		array( __( 'Blog', 'edupress' ), xpertz_commerce_page_url( 'blog' ) ),
		array( __( 'Support', 'edupress' ), xpertz_commerce_page_url( 'support' ) ),
		array( __( 'Contact', 'edupress' ), xpertz_commerce_page_url( 'contact' ) ),
	);

	echo '<ul class="xhc-nav-list">';
	foreach ( $items as $index => $item ) {
		printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $item[1] ), esc_html( $item[0] ) );
		if ( 1 === $index ) {
			$categories_url = xpertz_commerce_page_url( 'categories' );
			$categories = get_terms(
				array(
					'taxonomy'   => defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category',
					'hide_empty' => true,
					'number'     => 8,
				)
			);
			if ( ! is_wp_error( $categories ) && $categories ) {
				echo '<li class="xhc-has-submenu"><button type="button" aria-expanded="false">' . esc_html__( 'Categories', 'edupress' ) . xpertz_commerce_icon( 'chevron-down' ) . '</button><div class="xhc-category-menu"><span class="xhc-menu-eyebrow">' . esc_html__( 'Explore topics', 'edupress' ) . '</span><ul><li><a class="xhc-category-all" href="' . esc_url( $categories_url ) . '"><span>' . esc_html__( 'All categories', 'edupress' ) . '</span>' . xpertz_commerce_icon( 'arrow-right' ) . '</a></li>';
				foreach ( $categories as $category ) {
					printf( '<li><a href="%1$s"><span>%2$s</span><small>%3$s</small></a></li>', esc_url( get_term_link( $category ) ), esc_html( $category->name ), esc_html( sprintf( _n( '%s course', '%s courses', $category->count, 'edupress' ), number_format_i18n( $category->count ) ) ) );
				}
				echo '</ul></div></li>';
			} else {
				printf( '<li><a href="%1$s">%2$s</a></li>', esc_url( $categories_url ), esc_html__( 'Categories', 'edupress' ) );
			}
		}
	}
	echo '</ul>';
}

/**
 * Render the selected primary menu or the LMS fallback menu.
 */
function xpertz_commerce_navigation( $context = 'desktop' ) {
	if ( has_nav_menu( 'primary' ) ) {
		wp_nav_menu(
			array(
				'container'      => false,
				'theme_location' => 'primary',
				'menu_class'     => 'xhc-nav-list',
				'menu_id'        => 'mobile' === $context ? 'xhc-mobile-menu' : 'xhc-primary-menu',
				'fallback_cb'    => false,
			)
		);
		return;
	}

	xpertz_commerce_default_navigation();
}

/**
 * Return saved course IDs for a user or guest browser.
 *
 * @param int $user_id Optional user ID.
 * @return int[]
 */
function xpertz_commerce_wishlist_ids( $user_id = 0 ) {
	$user_id = $user_id ?: get_current_user_id();
	if ( $user_id ) {
		return array_values( array_filter( wp_parse_id_list( get_user_meta( $user_id, '_xpertz_course_wishlist', true ) ) ) );
	}

	$cookie = isset( $_COOKIE['xpertz_course_wishlist'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['xpertz_course_wishlist'] ) ) : '';
	return array_values( array_filter( wp_parse_id_list( $cookie ) ) );
}

/**
 * Save course wishlist IDs.
 *
 * @param int[] $ids Course IDs.
 */
function xpertz_commerce_save_wishlist( $ids ) {
	$ids = array_values( array_unique( array_filter( wp_parse_id_list( $ids ) ) ) );
	if ( is_user_logged_in() ) {
		update_user_meta( get_current_user_id(), '_xpertz_course_wishlist', $ids );
		return;
	}

	$value = implode( ',', $ids );
	if ( function_exists( 'wc_setcookie' ) ) {
		wc_setcookie( 'xpertz_course_wishlist', $value, time() + MONTH_IN_SECONDS );
	} else {
		setcookie( 'xpertz_course_wishlist', $value, time() + MONTH_IN_SECONDS, COOKIEPATH ?: '/', COOKIE_DOMAIN, is_ssl(), true );
	}
	$_COOKIE['xpertz_course_wishlist'] = $value;
}

/**
 * Toggle a wishlist course via AJAX.
 */
function xpertz_commerce_toggle_wishlist() {
	check_ajax_referer( 'xpertz_wishlist', 'nonce' );
	$course_id = isset( $_POST['courseId'] ) ? absint( $_POST['courseId'] ) : 0;
	if ( ! $course_id || 'lp_course' !== get_post_type( $course_id ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Course not found.', 'edupress' ) ), 404 );
	}

	$ids   = xpertz_commerce_wishlist_ids();
	$saved = in_array( $course_id, $ids, true );
	if ( $saved ) {
		$ids = array_values( array_diff( $ids, array( $course_id ) ) );
	} else {
		$ids[] = $course_id;
	}
	xpertz_commerce_save_wishlist( $ids );

	wp_send_json_success(
		array(
			'count' => count( $ids ),
			'saved' => ! $saved,
		)
	);
}
add_action( 'wp_ajax_xpertz_toggle_wishlist', 'xpertz_commerce_toggle_wishlist' );
add_action( 'wp_ajax_nopriv_xpertz_toggle_wishlist', 'xpertz_commerce_toggle_wishlist' );

/**
 * Build notification objects from orders and newly published courses.
 *
 * @param int $user_id User ID.
 * @return array
 */
function xpertz_commerce_notifications( $user_id ) {
	$notifications = array();
	if ( function_exists( 'wc_get_orders' ) ) {
		$orders = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'limit'       => 4,
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		foreach ( $orders as $order ) {
			$notifications[] = array(
				'icon'      => 'orders',
				'title'     => sprintf( __( 'Order #%s is %s', 'edupress' ), $order->get_order_number(), wc_get_order_status_name( $order->get_status() ) ),
				'detail'    => wp_date( get_option( 'date_format' ), $order->get_date_created()->getTimestamp() ),
				'url'       => $order->get_view_order_url(),
				'timestamp' => $order->get_date_modified()->getTimestamp(),
			);
		}
	}

	$latest_course = get_posts(
		array(
			'post_type'      => 'lp_course',
			'post_status'    => 'publish',
			'posts_per_page' => 2,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	foreach ( $latest_course as $course ) {
		$notifications[] = array(
			'icon'      => 'book',
			'title'     => sprintf( __( 'New course: %s', 'edupress' ), $course->post_title ),
			'detail'    => __( 'Explore the latest XPERTZ learning path', 'edupress' ),
			'url'       => get_permalink( $course ),
			'timestamp' => get_post_time( 'U', true, $course ),
		);
	}

	foreach ( array_slice( xpertz_commerce_user_course_ids( $user_id, true ), 0, 2 ) as $course_id ) {
		$user_course = class_exists( 'LearnPress\\Models\\UserItems\\UserCourseModel' ) ? LearnPress\Models\UserItems\UserCourseModel::find( $user_id, $course_id, false ) : false;
		$end_time    = $user_course && ! empty( $user_course->end_time ) ? strtotime( (string) $user_course->end_time ) : 0;
		$notifications[] = array(
			'icon'      => 'award',
			'title'     => sprintf( __( 'Course completed: %s', 'edupress' ), get_the_title( $course_id ) ),
			'detail'    => __( 'Your completion record is ready to review', 'edupress' ),
			'url'       => wc_get_account_endpoint_url( 'certificates' ),
			'timestamp' => $end_time ?: (int) get_post_modified_time( 'U', true, $course_id ),
		);
	}

	usort( $notifications, static fn( $first, $second ) => $second['timestamp'] <=> $first['timestamp'] );
	return array_slice( $notifications, 0, 6 );
}

/**
 * Count notifications newer than the last opened timestamp.
 *
 * @param int $user_id User ID.
 * @return int
 */
function xpertz_commerce_unread_notifications( $user_id ) {
	$seen  = (int) get_user_meta( $user_id, '_xpertz_notifications_seen', true );
	$count = 0;
	foreach ( xpertz_commerce_notifications( $user_id ) as $notification ) {
		$count += $notification['timestamp'] > $seen ? 1 : 0;
	}
	return $count;
}

/**
 * Mark the current user's notifications as viewed.
 */
function xpertz_commerce_mark_notifications() {
	check_ajax_referer( 'xpertz_notifications', 'nonce' );
	if ( ! is_user_logged_in() ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Sign in required.', 'edupress' ) ), 401 );
	}
	update_user_meta( get_current_user_id(), '_xpertz_notifications_seen', time() );
	wp_send_json_success();
}
add_action( 'wp_ajax_xpertz_mark_notifications', 'xpertz_commerce_mark_notifications' );

/**
 * Search courses, articles, and pages for the global header search.
 */
function xpertz_commerce_ajax_search() {
	check_ajax_referer( 'xpertz_search', 'nonce' );
	$query = isset( $_GET['query'] ) ? sanitize_text_field( wp_unslash( $_GET['query'] ) ) : '';
	$query_length = function_exists( 'mb_strlen' ) ? mb_strlen( $query ) : strlen( $query );
	if ( $query_length < 2 ) {
		wp_send_json_success( array( 'results' => array() ) );
	}

	$search = new WP_Query(
		array(
			'post_type'           => array( 'lp_course', 'post', 'page' ),
			'post_status'         => 'publish',
			'posts_per_page'      => 8,
			's'                   => $query,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
	$results = array();
	foreach ( $search->posts as $post ) {
		$results[] = array(
			'id'       => $post->ID,
			'image'    => get_the_post_thumbnail_url( $post, 'thumbnail' ) ?: '',
			'subtitle' => 'lp_course' === $post->post_type ? __( 'Course', 'edupress' ) : ( 'post' === $post->post_type ? __( 'Article', 'edupress' ) : __( 'Page', 'edupress' ) ),
			'title'    => get_the_title( $post ),
			'url'      => get_permalink( $post ),
		);
	}

	if ( count( $results ) < 8 ) {
		$categories = get_terms(
			array(
				'taxonomy'   => defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category',
				'hide_empty' => true,
				'number'     => 8 - count( $results ),
				'search'     => $query,
			)
		);
		if ( ! is_wp_error( $categories ) ) {
			foreach ( $categories as $category ) {
				$results[] = array(
					'id'       => 'term-' . $category->term_id,
					'image'    => '',
					'subtitle' => __( 'Course category', 'edupress' ),
					'title'    => $category->name,
					'url'      => get_term_link( $category ),
				);
			}
		}
	}
	wp_send_json_success( array( 'results' => $results ) );
}
add_action( 'wp_ajax_xpertz_global_search', 'xpertz_commerce_ajax_search' );
add_action( 'wp_ajax_nopriv_xpertz_global_search', 'xpertz_commerce_ajax_search' );

/**
 * Render the mini-cart control and dropdown.
 *
 * @param bool $echo Whether to echo the fragment.
 * @return string
 */
function xpertz_commerce_header_cart( $echo = true ) {
	$count    = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
	$subtotal = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_subtotal() : '';
	ob_start();
	?>
	<div class="xhc-action xhc-header-cart" data-header-cart>
		<button type="button" class="xhc-icon-button" aria-label="<?php echo esc_attr( sprintf( _n( 'Cart with %s item', 'Cart with %s items', $count, 'edupress' ), number_format_i18n( $count ) ) ); ?>" aria-expanded="false" data-popover-toggle="cart">
			<?php echo xpertz_commerce_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<span class="xhc-count<?php echo $count ? '' : ' is-empty'; ?>" data-cart-count><?php echo esc_html( $count ); ?></span>
		</button>
		<div class="xhc-popover xhc-mini-cart" data-popover="cart" hidden>
			<div class="xhc-popover-header"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Your learning cart', 'edupress' ); ?></span><h2><?php esc_html_e( 'Ready when you are', 'edupress' ); ?></h2></div><a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View cart', 'edupress' ); ?></a></div>
			<div class="xhc-mini-cart-content"><?php woocommerce_mini_cart(); ?></div>
			<?php if ( $count ) : ?>
				<div class="xhc-mini-cart-footer"><div><span><?php esc_html_e( 'Subtotal', 'edupress' ); ?></span><strong><?php echo wp_kses_post( $subtotal ); ?></strong></div><a class="xhc-primary-link" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Secure checkout', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
			<?php endif; ?>
		</div>
	</div>
	<?php
	$html = ob_get_clean();
	if ( $echo ) {
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
	return $html;
}

/**
 * Refresh the complete header cart after WooCommerce AJAX mutations.
 *
 * @param array $fragments Fragments.
 * @return array
 */
function xpertz_commerce_cart_fragment( $fragments ) {
	$fragments['.xhc-header-cart'] = xpertz_commerce_header_cart( false );
	return $fragments;
}
add_filter( 'woocommerce_add_to_cart_fragments', 'xpertz_commerce_cart_fragment' );

/**
 * Render notifications dropdown for signed-in users.
 */
function xpertz_commerce_header_notifications() {
	if ( ! is_user_logged_in() ) {
		return;
	}
	$user_id       = get_current_user_id();
	$notifications = xpertz_commerce_notifications( $user_id );
	$unread        = xpertz_commerce_unread_notifications( $user_id );
	?>
	<div class="xhc-action xhc-notifications">
		<button type="button" class="xhc-icon-button" aria-label="<?php echo esc_attr( sprintf( _n( '%s unread notification', '%s unread notifications', $unread, 'edupress' ), number_format_i18n( $unread ) ) ); ?>" aria-expanded="false" data-popover-toggle="notifications">
			<?php echo xpertz_commerce_icon( 'bell' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $unread ) : ?><span class="xhc-count"><?php echo esc_html( min( 9, $unread ) ); ?></span><?php endif; ?>
		</button>
		<div class="xhc-popover xhc-notification-panel" data-popover="notifications" hidden>
			<div class="xhc-popover-header"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Stay on track', 'edupress' ); ?></span><h2><?php esc_html_e( 'Notifications', 'edupress' ); ?></h2></div><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'notifications' ) ); ?>"><?php esc_html_e( 'View all', 'edupress' ); ?></a></div>
			<div class="xhc-notification-list">
				<?php foreach ( $notifications as $notification ) : ?>
					<a href="<?php echo esc_url( $notification['url'] ); ?>"><span class="xhc-notification-icon"><?php echo xpertz_commerce_icon( $notification['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><span><strong><?php echo esc_html( $notification['title'] ); ?></strong><small><?php echo esc_html( $notification['detail'] ); ?></small></span></a>
				<?php endforeach; ?>
				<?php if ( ! $notifications ) : ?><p class="xhc-empty"><?php esc_html_e( 'You are all caught up.', 'edupress' ); ?></p><?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Account links shared by desktop and mobile interfaces.
 *
 * @return array
 */
function xpertz_commerce_account_links() {
	return array(
		'dashboard'       => array( __( 'Dashboard', 'edupress' ), wc_get_account_endpoint_url( 'dashboard' ), 'dashboard' ),
		'my-courses'      => array( __( 'My Courses', 'edupress' ), wc_get_account_endpoint_url( 'my-courses' ), 'book' ),
		'orders'          => array( __( 'My Orders', 'edupress' ), wc_get_account_endpoint_url( 'orders' ), 'orders' ),
		'wishlist'        => array( __( 'Wishlist', 'edupress' ), wc_get_account_endpoint_url( 'wishlist' ), 'heart' ),
		'certificates'    => array( __( 'Certificates', 'edupress' ), wc_get_account_endpoint_url( 'certificates' ), 'award' ),
		'downloads'       => array( __( 'Downloads', 'edupress' ), wc_get_account_endpoint_url( 'downloads' ), 'download' ),
		'edit-address'    => array( __( 'Addresses', 'edupress' ), wc_get_account_endpoint_url( 'edit-address' ), 'address' ),
		'payment-methods' => array( __( 'Payment Methods', 'edupress' ), wc_get_account_endpoint_url( 'payment-methods' ), 'credit-card' ),
		'notifications'   => array( __( 'Notifications', 'edupress' ), wc_get_account_endpoint_url( 'notifications' ), 'bell' ),
		'support'         => array( __( 'Support Tickets', 'edupress' ), wc_get_account_endpoint_url( 'support' ), 'ticket' ),
		'edit-account'    => array( __( 'Profile', 'edupress' ), wc_get_account_endpoint_url( 'edit-account' ), 'profile' ),
		'settings'        => array( __( 'Settings', 'edupress' ), wc_get_account_endpoint_url( 'settings' ), 'settings' ),
		'logout'          => array( __( 'Logout', 'edupress' ), wc_logout_url(), 'login' ),
	);
}

/**
 * Render the signed-in account popover.
 */
function xpertz_commerce_header_account() {
	if ( ! is_user_logged_in() ) {
		?>
		<div class="xhc-auth-actions"><a class="xhc-sign-up" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Sign in / Register', 'edupress' ); ?></a></div>
		<?php
		return;
	}

	$user = wp_get_current_user();
	?>
	<div class="xhc-action xhc-account">
		<button type="button" class="xhc-account-toggle" aria-expanded="false" data-popover-toggle="account"><?php echo get_avatar( $user->ID, 36, '', $user->display_name, array( 'loading' => 'eager' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><small><?php esc_html_e( 'Welcome back', 'edupress' ); ?></small><strong><?php echo esc_html( $user->display_name ); ?></strong></span><?php echo xpertz_commerce_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		<div class="xhc-popover xhc-account-menu" data-popover="account" hidden>
			<div class="xhc-account-summary"><?php echo get_avatar( $user->ID, 48, '', $user->display_name ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php echo esc_html( $user->display_name ); ?></strong><small><?php echo esc_html( $user->user_email ); ?></small></span></div>
			<nav aria-label="<?php esc_attr_e( 'Account menu', 'edupress' ); ?>">
				<?php foreach ( xpertz_commerce_account_links() as $key => $link ) : ?>
					<a class="<?php echo 'logout' === $key ? 'is-logout' : ''; ?>" href="<?php echo esc_url( $link[1] ); ?>"><?php echo xpertz_commerce_icon( $link[2] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $link[0] ); ?></span></a>
				<?php endforeach; ?>
			</nav>
		</div>
	</div>
	<?php
}

/**
 * Render the complete premium site header and its mobile/search layers.
 */
function xpertz_render_lms_header() {
	$wishlist_count = count( xpertz_commerce_wishlist_ids() );
	?>
	<header class="site-header xhc-site-header" role="banner" data-lms-header>
		<div class="xhc-header-shell">
			<div class="xhc-brand"><?php if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) { edupress_the_custom_logo(); } else { ?><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a><?php } ?></div>
			<nav class="xhc-desktop-nav" aria-label="<?php esc_attr_e( 'Primary navigation', 'edupress' ); ?>"><?php xpertz_commerce_navigation( 'desktop' ); ?></nav>
			<div class="xhc-header-actions">
				<button type="button" class="xhc-icon-button" aria-label="<?php esc_attr_e( 'Search', 'edupress' ); ?>" data-search-open><?php echo xpertz_commerce_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<?php xpertz_commerce_header_notifications(); ?>
				<a class="xhc-icon-button xhc-wishlist-link" href="<?php echo esc_url( is_user_logged_in() ? wc_get_account_endpoint_url( 'wishlist' ) : xpertz_commerce_page_url( 'wishlist' ) ); ?>" aria-label="<?php echo esc_attr( sprintf( _n( 'Wishlist with %s course', 'Wishlist with %s courses', $wishlist_count, 'edupress' ), number_format_i18n( $wishlist_count ) ) ); ?>"><?php echo xpertz_commerce_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="xhc-count<?php echo $wishlist_count ? '' : ' is-empty'; ?>" data-wishlist-count><?php echo esc_html( $wishlist_count ); ?></span></a>
				<?php if ( function_exists( 'WC' ) ) { xpertz_commerce_header_cart(); } ?>
				<?php if ( function_exists( 'wc_get_page_permalink' ) ) { xpertz_commerce_header_account(); } ?>
				<button type="button" class="xhc-mobile-toggle" aria-expanded="false" aria-controls="xhc-mobile-drawer" data-mobile-open><?php echo xpertz_commerce_icon( 'menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Open menu', 'edupress' ); ?></span></button>
			</div>
		</div>
	</header>

	<div class="xhc-search-dialog" role="dialog" aria-modal="true" aria-labelledby="xhc-search-title" data-search-dialog hidden>
		<div class="xhc-search-backdrop" data-search-close></div>
		<div class="xhc-search-panel">
			<div class="xhc-search-heading"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Find your next skill', 'edupress' ); ?></span><h2 id="xhc-search-title"><?php esc_html_e( 'Search XPERTZ', 'edupress' ); ?></h2></div><button type="button" aria-label="<?php esc_attr_e( 'Close search', 'edupress' ); ?>" data-search-close><?php echo xpertz_commerce_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></div>
			<label class="xhc-search-input"><?php echo xpertz_commerce_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span class="screen-reader-text"><?php esc_html_e( 'Search courses, categories, and articles', 'edupress' ); ?></span><input type="search" autocomplete="off" placeholder="<?php esc_attr_e( 'Search courses, categories, and articles…', 'edupress' ); ?>" data-global-search></label>
			<div class="xhc-search-results" data-search-results aria-live="polite"><div class="xhc-search-suggestions"><span><?php esc_html_e( 'Popular searches', 'edupress' ); ?></span><button type="button">Leadership</button><button type="button">Technology</button><button type="button">Business</button></div></div>
		</div>
	</div>

	<div class="xhc-mobile-overlay" data-mobile-close hidden></div>
	<aside id="xhc-mobile-drawer" class="xhc-mobile-drawer" aria-label="<?php esc_attr_e( 'Mobile navigation', 'edupress' ); ?>" aria-hidden="true">
		<div class="xhc-mobile-header"><div class="xhc-brand"><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></div><button type="button" aria-label="<?php esc_attr_e( 'Close menu', 'edupress' ); ?>" data-mobile-close><?php echo xpertz_commerce_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></div>
		<button type="button" class="xhc-mobile-search" data-search-open><?php echo xpertz_commerce_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Search courses and articles', 'edupress' ); ?></span></button>
		<nav class="xhc-mobile-nav" aria-label="<?php esc_attr_e( 'Mobile primary navigation', 'edupress' ); ?>"><?php xpertz_commerce_navigation( 'mobile' ); ?></nav>
		<div class="xhc-mobile-quick-links">
			<a href="<?php echo esc_url( is_user_logged_in() ? wc_get_account_endpoint_url( 'wishlist' ) : xpertz_commerce_page_url( 'wishlist' ) ); ?>"><?php echo xpertz_commerce_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Wishlist', 'edupress' ); ?></span><strong data-wishlist-count><?php echo esc_html( $wishlist_count ); ?></strong></a>
			<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : xpertz_commerce_page_url( 'cart' ) ); ?>"><?php echo xpertz_commerce_icon( 'cart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Cart', 'edupress' ); ?></span></a>
			<?php if ( is_user_logged_in() ) : ?><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'notifications' ) ); ?>"><?php echo xpertz_commerce_icon( 'bell' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Notifications', 'edupress' ); ?></span></a><?php endif; ?>
		</div>
		<div class="xhc-mobile-account">
			<?php if ( is_user_logged_in() ) : ?>
				<a class="xhc-mobile-account-primary" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo get_avatar( get_current_user_id(), 40 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><small><?php esc_html_e( 'Signed in as', 'edupress' ); ?></small><strong><?php echo esc_html( wp_get_current_user()->display_name ); ?></strong></span><?php echo xpertz_commerce_icon( 'chevron-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php else : ?>
				<a class="xhc-primary-link" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Sign in / Register', 'edupress' ); ?></a>
			<?php endif; ?>
		</div>
	</aside>
	<?php
}

/**
 * Register the additional My Account endpoints and private support tickets.
 */
function xpertz_commerce_register_account_features() {
	foreach ( array( 'my-courses', 'wishlist', 'certificates', 'notifications', 'support', 'settings' ) as $endpoint ) {
		add_rewrite_endpoint( $endpoint, EP_PAGES );
	}

	register_post_type(
		'xpertz_ticket',
		array(
			'labels'       => array( 'name' => __( 'Support Tickets', 'edupress' ), 'singular_name' => __( 'Support Ticket', 'edupress' ) ),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'supports'     => array( 'title', 'editor', 'author' ),
			'map_meta_cap' => true,
			'capability_type' => 'post',
		)
	);
}
add_action( 'init', 'xpertz_commerce_register_account_features' );

/**
 * Add XPERTZ sections to the WooCommerce account navigation.
 *
 * @param array $items Existing account menu.
 * @return array
 */
function xpertz_commerce_account_menu_items( $items ) {
	$logout = $items['customer-logout'] ?? __( 'Logout', 'edupress' );
	unset( $items['customer-logout'] );

	$menu = array(
		'dashboard'       => $items['dashboard'] ?? __( 'Dashboard', 'edupress' ),
		'my-courses'      => __( 'My Courses', 'edupress' ),
		'orders'          => $items['orders'] ?? __( 'Orders', 'edupress' ),
		'wishlist'        => __( 'Wishlist', 'edupress' ),
		'certificates'    => __( 'Certificates', 'edupress' ),
		'downloads'       => $items['downloads'] ?? __( 'Downloads', 'edupress' ),
		'edit-address'    => $items['edit-address'] ?? __( 'Addresses', 'edupress' ),
		'payment-methods' => $items['payment-methods'] ?? __( 'Payment Methods', 'edupress' ),
		'notifications'   => __( 'Notifications', 'edupress' ),
		'support'         => __( 'Support', 'edupress' ),
		'edit-account'    => $items['edit-account'] ?? __( 'Profile', 'edupress' ),
		'settings'        => __( 'Settings', 'edupress' ),
		'customer-logout' => $logout,
	);

	return $menu;
}
add_filter( 'woocommerce_account_menu_items', 'xpertz_commerce_account_menu_items' );

/**
 * Add consistent headings to WooCommerce's core account endpoints.
 *
 * @param string $endpoint_value Current endpoint value, when available.
 */
function xpertz_commerce_account_core_heading( $endpoint_value = '' ) {
	$headings = array(
		'woocommerce_account_orders_endpoint'             => array( __( 'Purchase history', 'edupress' ), __( 'Orders', 'edupress' ), __( 'Review course purchases, payment status, and order details.', 'edupress' ) ),
		'woocommerce_account_view-order_endpoint'          => array( __( 'Purchase details', 'edupress' ), __( 'Order details', 'edupress' ), __( 'Review the items, totals, and status for this purchase.', 'edupress' ) ),
		'woocommerce_account_downloads_endpoint'          => array( __( 'Your resources', 'edupress' ), __( 'Downloads', 'edupress' ), __( 'Access downloadable files included with your purchases.', 'edupress' ) ),
		'woocommerce_account_payment-methods_endpoint'     => array( __( 'Secure payments', 'edupress' ), __( 'Payment Methods', 'edupress' ), __( 'Manage the payment options saved to your account.', 'edupress' ) ),
		'woocommerce_account_add-payment-method_endpoint'  => array( __( 'Secure payments', 'edupress' ), __( 'Add a payment method', 'edupress' ), __( 'Save a payment option for a faster checkout experience.', 'edupress' ) ),
		'woocommerce_account_edit-account_endpoint'       => array( __( 'Personal profile', 'edupress' ), __( 'Account Details', 'edupress' ), __( 'Update your name, email address, display name, and password.', 'edupress' ) ),
	);

	$hook = current_filter();
	if ( 'woocommerce_account_edit-address_endpoint' === $hook ) {
		$address_type = sanitize_key( (string) $endpoint_value );
		if ( in_array( $address_type, array( 'billing', 'shipping' ), true ) ) {
			/* translators: %s: billing or shipping address type. */
			$title = sprintf( __( 'Edit %s address', 'edupress' ), $address_type );
			$headings[ $hook ] = array( __( 'Checkout details', 'edupress' ), $title, __( 'Keep your contact and delivery information accurate.', 'edupress' ) );
		} else {
			$headings[ $hook ] = array( __( 'Checkout details', 'edupress' ), __( 'Addresses', 'edupress' ), __( 'Manage the billing and delivery information used at checkout.', 'edupress' ) );
		}
	}

	if ( empty( $headings[ $hook ] ) ) {
		return;
	}

	list( $eyebrow, $title, $description ) = $headings[ $hook ];
	echo '<div class="xhc-account-heading xhc-account-core-heading"><span class="xhc-eyebrow">' . esc_html( $eyebrow ) . '</span><h2>' . esc_html( $title ) . '</h2><p>' . esc_html( $description ) . '</p></div>';
}
add_action( 'woocommerce_account_orders_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_view-order_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_downloads_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_edit-address_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_payment-methods_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_add-payment-method_endpoint', 'xpertz_commerce_account_core_heading', 5 );
add_action( 'woocommerce_account_edit-account_endpoint', 'xpertz_commerce_account_core_heading', 5 );

/**
 * Query course IDs belonging to a learner.
 *
 * @param int  $user_id User ID.
 * @param bool $finished Only completed courses.
 * @return int[]
 */
function xpertz_commerce_user_course_ids( $user_id, $finished = false ) {
	global $wpdb;
	$table = $wpdb->prefix . 'learnpress_user_items';
	if ( $finished ) {
		$sql = $wpdb->prepare( "SELECT DISTINCT item_id FROM {$table} WHERE user_id = %d AND item_type = %s AND status = %s", $user_id, 'lp_course', 'finished' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	} else {
		$sql = $wpdb->prepare( "SELECT DISTINCT item_id FROM {$table} WHERE user_id = %d AND item_type = %s AND status IN (%s, %s, %s)", $user_id, 'lp_course', 'enrolled', 'finished', 'purchased' ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	}
	return array_map( 'absint', $wpdb->get_col( $sql ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
}

/**
 * Render course cards for the account and wishlist screens.
 *
 * @param int[] $course_ids Course IDs.
 * @param bool  $progress   Show learner progress.
 */
function xpertz_commerce_account_course_grid( $course_ids, $progress = false ) {
	if ( ! $course_ids ) {
		echo '<div class="xhc-account-empty">' . xpertz_commerce_icon( 'book' ) . '<h3>' . esc_html__( 'Your learning journey starts here', 'edupress' ) . '</h3><p>' . esc_html__( 'Explore expert-led courses and save the ones that match your goals.', 'edupress' ) . '</p><a class="xhc-primary-link" href="' . esc_url( get_post_type_archive_link( 'lp_course' ) ) . '">' . esc_html__( 'Browse courses', 'edupress' ) . '</a></div>';
		return;
	}

	$lp_user = $progress && function_exists( 'learn_press_get_user' ) ? learn_press_get_user( get_current_user_id() ) : false;
	echo '<div class="xhc-account-course-grid">';
	foreach ( $course_ids as $course_id ) {
		$course = class_exists( 'LearnPress\\Models\\CourseModel' ) ? LearnPress\Models\CourseModel::find( $course_id, true ) : false;
		if ( ! $course ) {
			continue;
		}
		$percentage = 0;
		$status     = '';
		if ( $lp_user ) {
			$course_data = $lp_user->get_course_data( $course_id );
			if ( $course_data ) {
				$percentage = min( 100, max( 0, (float) $course_data->get_percent_completed_items() ) );
				$status     = $course_data->get_status();
			}
		}
		$product_id = function_exists( 'xpertz_wc_get_product_id_for_course' ) ? xpertz_wc_get_product_id_for_course( $course_id ) : 0;
		$product    = $product_id ? wc_get_product( $product_id ) : false;
		$author_id  = (int) get_post_field( 'post_author', $course_id );
		$instructor = get_the_author_meta( 'display_name', $author_id ) ?: __( 'XPERTZ Faculty', 'edupress' );
		$updated    = get_post_modified_time( get_option( 'date_format' ), false, $course_id );
		?>
		<article class="xhc-account-course-card" data-course-id="<?php echo esc_attr( $course_id ); ?>">
			<a class="xhc-account-course-image" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php echo xpertz_course_image( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<div class="xhc-account-course-content"><span class="xhc-eyebrow"><?php echo esc_html( xpertz_course_level_label( get_post_meta( $course_id, '_lp_level', true ) ) ); ?></span><h3><a href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></a></h3><p class="xhc-course-card-meta"><?php echo esc_html( sprintf( __( 'By %1$s · Updated %2$s', 'edupress' ), $instructor, $updated ) ); ?></p>
			<?php if ( $progress ) : ?><div class="xhc-progress"><div><span><?php echo esc_html( 'finished' === $status ? __( 'Completed', 'edupress' ) : __( 'Course progress', 'edupress' ) ); ?></span><strong><?php echo esc_html( round( $percentage ) ); ?>%</strong></div><span class="xhc-progress-track"><span style="--xhc-progress:<?php echo esc_attr( $percentage ); ?>%"></span></span></div><?php endif; ?>
			<div class="xhc-account-course-footer"><?php if ( ! $progress && $product ) : ?><strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong><span class="xhc-course-card-actions"><button type="button" class="xhc-wishlist-remove" data-wishlist-course="<?php echo esc_attr( $course_id ); ?>" aria-pressed="true" aria-label="<?php esc_attr_e( 'Remove from wishlist', 'edupress' ); ?>"><?php echo xpertz_commerce_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button><button type="button" class="xhc-small-button" data-add-course-to-cart="<?php echo esc_attr( $product_id ); ?>"><?php esc_html_e( 'Move to cart', 'edupress' ); ?></button></span><?php else : ?><span class="xhc-course-card-actions"><?php if ( 'finished' === $status ) : ?><a class="xhc-certificate-link" href="<?php echo esc_url( wc_get_account_endpoint_url( 'certificates' ) ); ?>"><?php echo xpertz_commerce_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Certificate', 'edupress' ); ?></a><?php endif; ?><a class="xhc-small-button" href="<?php echo esc_url( get_permalink( $course_id ) ); ?>"><?php echo 'finished' === $status ? esc_html__( 'Review course', 'edupress' ) : esc_html__( 'Continue learning', 'edupress' ); ?></a></span><?php endif; ?></div></div>
		</article>
		<?php
	}
	echo '</div>';
}

/** Render My Courses endpoint. */
function xpertz_commerce_account_courses() {
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Keep growing', 'edupress' ) . '</span><h2>' . esc_html__( 'My Courses', 'edupress' ) . '</h2><p>' . esc_html__( 'Continue learning and track your progress in one place.', 'edupress' ) . '</p></div>';
	xpertz_commerce_account_course_grid( xpertz_commerce_user_course_ids( get_current_user_id() ), true );
}
add_action( 'woocommerce_account_my-courses_endpoint', 'xpertz_commerce_account_courses' );

/** Render Wishlist endpoint. */
function xpertz_commerce_account_wishlist() {
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Saved for later', 'edupress' ) . '</span><h2>' . esc_html__( 'Course Wishlist', 'edupress' ) . '</h2><p>' . esc_html__( 'Build a focused learning plan, then move courses to your cart when ready.', 'edupress' ) . '</p><button type="button" class="xhc-secondary-link xhc-share-wishlist" data-share-wishlist>' . xpertz_commerce_icon( 'heart' ) . esc_html__( 'Share wishlist', 'edupress' ) . '</button></div>';
	xpertz_commerce_account_course_grid( xpertz_commerce_wishlist_ids( get_current_user_id() ), false );
}
add_action( 'woocommerce_account_wishlist_endpoint', 'xpertz_commerce_account_wishlist' );
add_shortcode( 'xpertz_wishlist', 'xpertz_commerce_wishlist_shortcode' );

/** Public wishlist shortcode. */
function xpertz_commerce_wishlist_shortcode() {
	$course_ids = xpertz_commerce_wishlist_ids();
	if ( isset( $_GET['xpertz_courses'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only public share state.
		$shared_ids = wp_parse_id_list( sanitize_text_field( wp_unslash( $_GET['xpertz_courses'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$course_ids = array_values(
			array_filter(
				$shared_ids,
				static fn( $course_id ) => 'lp_course' === get_post_type( $course_id ) && 'publish' === get_post_status( $course_id )
			)
		);
	}
	ob_start();
	echo '<div class="xhc-standalone-account xhc-container"><div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Saved for later', 'edupress' ) . '</span><h2>' . esc_html__( 'My Wishlist', 'edupress' ) . '</h2><button type="button" class="xhc-secondary-link xhc-share-wishlist" data-share-wishlist>' . xpertz_commerce_icon( 'heart' ) . esc_html__( 'Share wishlist', 'edupress' ) . '</button></div>';
	xpertz_commerce_account_course_grid( $course_ids, false );
	echo '</div>';
	return ob_get_clean();
}

/** Render certificate readiness endpoint. */
function xpertz_commerce_account_certificates() {
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Celebrate progress', 'edupress' ) . '</span><h2>' . esc_html__( 'Certificates', 'edupress' ) . '</h2><p>' . esc_html__( 'Completed courses appear here. Certificate downloads become available when the certificate add-on is enabled for a course.', 'edupress' ) . '</p></div>';
	xpertz_commerce_account_course_grid( xpertz_commerce_user_course_ids( get_current_user_id(), true ), true );
}
add_action( 'woocommerce_account_certificates_endpoint', 'xpertz_commerce_account_certificates' );

/** Render full notification history. */
function xpertz_commerce_account_notifications() {
	$notifications = xpertz_commerce_notifications( get_current_user_id() );
	update_user_meta( get_current_user_id(), '_xpertz_notifications_seen', time() );
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Updates', 'edupress' ) . '</span><h2>' . esc_html__( 'Notifications', 'edupress' ) . '</h2></div><div class="xhc-account-notifications">';
	foreach ( $notifications as $notification ) {
		echo '<a href="' . esc_url( $notification['url'] ) . '"><span class="xhc-notification-icon">' . xpertz_commerce_icon( $notification['icon'] ) . '</span><span><strong>' . esc_html( $notification['title'] ) . '</strong><small>' . esc_html( $notification['detail'] ) . '</small></span>' . xpertz_commerce_icon( 'chevron-right' ) . '</a>';
	}
	if ( ! $notifications ) {
		echo '<p class="xhc-empty">' . esc_html__( 'You are all caught up.', 'edupress' ) . '</p>';
	}
	echo '</div>';
}
add_action( 'woocommerce_account_notifications_endpoint', 'xpertz_commerce_account_notifications' );

/** Render settings endpoint. */
function xpertz_commerce_account_settings() {
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Account preferences', 'edupress' ) . '</span><h2>' . esc_html__( 'Settings', 'edupress' ) . '</h2><p>' . esc_html__( 'Manage your personal details, password, billing addresses, and saved payment methods.', 'edupress' ) . '</p></div><div class="xhc-settings-grid"><a href="' . esc_url( wc_get_account_endpoint_url( 'edit-account' ) ) . '">' . xpertz_commerce_icon( 'profile' ) . '<span><strong>' . esc_html__( 'Profile and password', 'edupress' ) . '</strong><small>' . esc_html__( 'Update your name, email, and credentials', 'edupress' ) . '</small></span></a><a href="' . esc_url( wc_get_account_endpoint_url( 'edit-address' ) ) . '">' . xpertz_commerce_icon( 'address' ) . '<span><strong>' . esc_html__( 'Billing addresses', 'edupress' ) . '</strong><small>' . esc_html__( 'Manage checkout details', 'edupress' ) . '</small></span></a><a href="' . esc_url( wc_get_account_endpoint_url( 'payment-methods' ) ) . '">' . xpertz_commerce_icon( 'credit-card' ) . '<span><strong>' . esc_html__( 'Payment methods', 'edupress' ) . '</strong><small>' . esc_html__( 'Manage saved payment options', 'edupress' ) . '</small></span></a></div>';
}
add_action( 'woocommerce_account_settings_endpoint', 'xpertz_commerce_account_settings' );

/** Render support tickets and creation form. */
function xpertz_commerce_account_support() {
	$tickets = new WP_Query( array( 'post_type' => 'xpertz_ticket', 'post_status' => 'private', 'author' => get_current_user_id(), 'posts_per_page' => 20, 'no_found_rows' => true ) );
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'We are here to help', 'edupress' ) . '</span><h2>' . esc_html__( 'Support Tickets', 'edupress' ) . '</h2><p>' . esc_html__( 'Send course, account, or order questions directly to the XPERTZ support team.', 'edupress' ) . '</p></div>';
	if ( isset( $_GET['ticket-created'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		echo '<div class="woocommerce-message" role="status">' . esc_html__( 'Your support ticket was created successfully.', 'edupress' ) . '</div>';
	}
	echo '<div class="xhc-support-layout"><form class="xhc-support-form" method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '"><input type="hidden" name="action" value="xpertz_create_ticket">';
	wp_nonce_field( 'xpertz_create_ticket', 'xpertz_ticket_nonce' );
	echo '<label>' . esc_html__( 'Subject', 'edupress' ) . '<input type="text" name="ticket_subject" required maxlength="160"></label><label>' . esc_html__( 'Related order (optional)', 'edupress' ) . '<input type="text" name="ticket_order" maxlength="30" inputmode="numeric"></label><label>' . esc_html__( 'How can we help?', 'edupress' ) . '<textarea name="ticket_message" rows="6" required maxlength="5000"></textarea></label><button type="submit" class="xhc-primary-link">' . esc_html__( 'Create ticket', 'edupress' ) . '</button></form><div class="xhc-ticket-list"><h3>' . esc_html__( 'Your tickets', 'edupress' ) . '</h3>';
	if ( $tickets->have_posts() ) {
		while ( $tickets->have_posts() ) {
			$tickets->the_post();
			echo '<article><span class="xhc-ticket-status">' . esc_html( get_post_meta( get_the_ID(), '_xpertz_ticket_status', true ) ?: __( 'Open', 'edupress' ) ) . '</span><h4>' . esc_html( get_the_title() ) . '</h4><time>' . esc_html( get_the_date() ) . '</time><p>' . esc_html( wp_trim_words( get_the_content(), 24 ) ) . '</p></article>';
		}
	} else {
		echo '<p class="xhc-empty">' . esc_html__( 'No support tickets yet.', 'edupress' ) . '</p>';
	}
	wp_reset_postdata();
	echo '</div></div>';
}
add_action( 'woocommerce_account_support_endpoint', 'xpertz_commerce_account_support' );

/** Handle secure support ticket creation. */
function xpertz_commerce_create_ticket() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( wc_get_page_permalink( 'myaccount' ) );
		exit;
	}
	check_admin_referer( 'xpertz_create_ticket', 'xpertz_ticket_nonce' );
	$subject = isset( $_POST['ticket_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket_subject'] ) ) : '';
	$message = isset( $_POST['ticket_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['ticket_message'] ) ) : '';
	$order   = isset( $_POST['ticket_order'] ) ? sanitize_text_field( wp_unslash( $_POST['ticket_order'] ) ) : '';
	if ( ! $subject || ! $message ) {
		wp_safe_redirect( wc_get_account_endpoint_url( 'support' ) );
		exit;
	}
	$ticket_id = wp_insert_post( array( 'post_type' => 'xpertz_ticket', 'post_status' => 'private', 'post_title' => $subject, 'post_content' => $message, 'post_author' => get_current_user_id() ), true );
	if ( ! is_wp_error( $ticket_id ) ) {
		update_post_meta( $ticket_id, '_xpertz_ticket_status', 'Open' );
		update_post_meta( $ticket_id, '_xpertz_ticket_order', $order );
	}
	wp_safe_redirect( add_query_arg( 'ticket-created', 1, wc_get_account_endpoint_url( 'support' ) ) );
	exit;
}
add_action( 'admin_post_xpertz_create_ticket', 'xpertz_commerce_create_ticket' );

/** Add modern dashboard cards before WooCommerce's default dashboard copy. */
function xpertz_commerce_account_dashboard_intro() {
	$user_id      = get_current_user_id();
	$course_count = count( xpertz_commerce_user_course_ids( $user_id ) );
	$order_count  = function_exists( 'wc_get_customer_order_count' ) ? wc_get_customer_order_count( $user_id ) : 0;
	$wishlist     = count( xpertz_commerce_wishlist_ids( $user_id ) );
	echo '<div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Learning command center', 'edupress' ) . '</span><h2>' . esc_html( sprintf( __( 'Welcome back, %s', 'edupress' ), wp_get_current_user()->display_name ) ) . '</h2><p>' . esc_html__( 'Resume courses, review purchases, and manage your learning account.', 'edupress' ) . '</p></div><div class="xhc-dashboard-stats"><a href="' . esc_url( wc_get_account_endpoint_url( 'my-courses' ) ) . '">' . xpertz_commerce_icon( 'book' ) . '<strong>' . esc_html( number_format_i18n( $course_count ) ) . '</strong><span>' . esc_html__( 'Active courses', 'edupress' ) . '</span></a><a href="' . esc_url( wc_get_account_endpoint_url( 'orders' ) ) . '">' . xpertz_commerce_icon( 'orders' ) . '<strong>' . esc_html( number_format_i18n( $order_count ) ) . '</strong><span>' . esc_html__( 'Orders', 'edupress' ) . '</span></a><a href="' . esc_url( wc_get_account_endpoint_url( 'wishlist' ) ) . '">' . xpertz_commerce_icon( 'heart' ) . '<strong>' . esc_html( number_format_i18n( $wishlist ) ) . '</strong><span>' . esc_html__( 'Saved courses', 'edupress' ) . '</span></a></div>';
}
add_action( 'woocommerce_account_dashboard', 'xpertz_commerce_account_dashboard_intro', 5 );

/**
 * Return concise supporting copy for managed public pages.
 *
 * @param string $slug Page slug.
 * @return string
 */
function xpertz_commerce_page_intro( $slug ) {
	$descriptions = array(
		'about'      => __( 'Meet the learning platform built for practical career growth.', 'edupress' ),
		'blog'       => __( 'Ideas, guides, and perspectives for continuous professional development.', 'edupress' ),
		'cart'       => __( 'Review your learning plan and continue to secure checkout.', 'edupress' ),
		'categories' => __( 'Explore focused learning paths designed around the skills professionals use most.', 'edupress' ),
		'checkout'   => __( 'Complete your purchase securely and start learning immediately.', 'edupress' ),
		'contact'    => __( 'Connect with learner support for course, account, and purchase questions.', 'edupress' ),
		'my-account' => __( 'Manage your courses, purchases, profile, and learning progress.', 'edupress' ),
		'pricing'    => __( 'Simple course pricing with secure payment and lifetime access.', 'edupress' ),
		'support'    => __( 'Find answers and get direct help from the XPERTZ learner support team.', 'edupress' ),
		'wishlist'   => __( 'Keep your next learning goals organized in one place.', 'edupress' ),
	);

	return $descriptions[ $slug ] ?? __( 'Practical learning experiences for ambitious professionals.', 'edupress' );
}

/**
 * Use theme-owned, update-safe templates for course and category archives.
 *
 * @param string $template Resolved template path.
 * @return string
 */
function xpertz_commerce_catalog_template( $template ) {
	$taxonomy = defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category';
	if ( ! is_post_type_archive( 'lp_course' ) && ! is_tax( $taxonomy ) ) {
		return $template;
	}

	$catalog_template = get_theme_file_path( '/archive-lp_course.php' );
	return file_exists( $catalog_template ) ? $catalog_template : $template;
}
add_filter( 'template_include', 'xpertz_commerce_catalog_template', 100 );

/**
 * Configure the server-rendered course archive query.
 *
 * @param WP_Query $query Main WordPress query.
 */
function xpertz_commerce_catalog_query( $query ) {
	$taxonomy = defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category';
	if ( is_admin() || ! $query->is_main_query() || ( ! $query->is_post_type_archive( 'lp_course' ) && ! $query->is_tax( $taxonomy ) ) ) {
		return;
	}

	$query->set( 'posts_per_page', 12 );
	$search = isset( $_GET['course_search'] ) && is_string( $_GET['course_search'] ) ? sanitize_text_field( wp_unslash( $_GET['course_search'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter.
	$sort   = isset( $_GET['course_sort'] ) && is_string( $_GET['course_sort'] ) ? sanitize_key( wp_unslash( $_GET['course_sort'] ) ) : 'newest'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter.

	if ( $search ) {
		$query->set( 's', $search );
	}

	switch ( $sort ) {
		case 'oldest':
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'ASC' );
			break;
		case 'title':
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
			break;
		default:
			$query->set( 'orderby', 'date' );
			$query->set( 'order', 'DESC' );
	}
}
add_action( 'pre_get_posts', 'xpertz_commerce_catalog_query' );

/**
 * Render one reusable premium course card.
 *
 * @param int $course_id Course post ID.
 */
function xpertz_commerce_catalog_course_card( $course_id ) {
	$course_id = absint( $course_id );
	if ( ! $course_id || 'lp_course' !== get_post_type( $course_id ) ) {
		return;
	}

	$course       = class_exists( '\\LearnPress\\Models\\CourseModel' ) ? \LearnPress\Models\CourseModel::find( $course_id, true ) : false;
	$legacy       = function_exists( 'learn_press_get_course' ) ? learn_press_get_course( $course_id ) : false;
	$product_id   = function_exists( 'xpertz_wc_get_product_id_for_course' ) ? xpertz_wc_get_product_id_for_course( $course_id ) : 0;
	$product      = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false;
	$categories   = get_the_terms( $course_id, defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category' );
	$category     = ! is_wp_error( $categories ) && $categories ? reset( $categories ) : false;
	$author_id    = (int) get_post_field( 'post_author', $course_id );
	$instructor   = get_the_author_meta( 'display_name', $author_id ) ?: __( 'XPERTZ Faculty', 'edupress' );
	$duration     = $course ? $course->get_duration() : '';
	$lessons      = $course && defined( 'LP_LESSON_CPT' ) ? $course->count_items( LP_LESSON_CPT ) : 0;
	$students     = $course ? $course->count_students() : 0;
	$rating       = function_exists( 'xpertz_course_rating_data' ) ? xpertz_course_rating_data( $course_id ) : array( 'average' => 0, 'total' => 0 );
	$wishlist_ids = xpertz_commerce_wishlist_ids();
	$is_saved     = in_array( $course_id, $wishlist_ids, true );
	$price_html   = $product ? $product->get_price_html() : ( $legacy ? $legacy->get_course_price_html() : '' );
	$permalink    = get_permalink( $course_id );
	$image        = get_the_post_thumbnail(
		$course_id,
		'medium_large',
		array(
			'loading'  => 'lazy',
			'decoding' => 'async',
			'sizes'    => '(max-width: 700px) 100vw, (max-width: 1100px) 50vw, 33vw',
		)
	);
	?>
	<article class="xhc-catalog-card">
		<a class="xhc-catalog-card-image" href="<?php echo esc_url( $permalink ); ?>" tabindex="-1" aria-hidden="true">
			<?php echo $image ? wp_kses_post( $image ) : xpertz_commerce_icon( 'graduation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php if ( $category ) : ?><span><?php echo esc_html( $category->name ); ?></span><?php endif; ?>
		</a>
		<div class="xhc-catalog-card-body">
			<?php if ( $category ) : ?><a class="xhc-catalog-category" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endif; ?>
			<h2><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( get_the_title( $course_id ) ); ?></a></h2>
			<p class="xhc-catalog-instructor"><?php echo esc_html( sprintf( __( 'By %s', 'edupress' ), $instructor ) ); ?></p>
			<div class="xhc-catalog-rating"><strong><?php echo $rating['total'] ? esc_html( number_format_i18n( $rating['average'], 1 ) ) : esc_html__( 'New', 'edupress' ); ?></strong><?php echo xpertz_commerce_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><small><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $rating['total'], 'edupress' ), number_format_i18n( $rating['total'] ) ) ); ?></small></div>
			<div class="xhc-catalog-meta">
				<span><?php echo xpertz_commerce_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $duration ?: __( 'Self-paced', 'edupress' ) ); ?></span>
				<span><?php echo xpertz_commerce_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( _n( '%s lesson', '%s lessons', $lessons, 'edupress' ), number_format_i18n( $lessons ) ) ); ?></span>
				<span><?php echo xpertz_commerce_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( number_format_i18n( $students ) ); ?></span>
			</div>
			<div class="xhc-catalog-card-footer">
				<strong><?php echo wp_kses_post( $price_html ?: __( 'Free', 'edupress' ) ); ?></strong>
				<div>
					<button type="button" class="xhc-catalog-wishlist<?php echo $is_saved ? ' is-saved' : ''; ?>" data-wishlist-course="<?php echo esc_attr( $course_id ); ?>" aria-pressed="<?php echo $is_saved ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr( $is_saved ? __( 'Remove from wishlist', 'edupress' ) : __( 'Add to wishlist', 'edupress' ) ); ?>"><?php echo xpertz_commerce_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
					<a class="xhc-catalog-cta" href="<?php echo esc_url( $permalink ); ?>"><?php esc_html_e( 'View course', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				</div>
			</div>
		</div>
	</article>
	<?php
}

/** Render the public course category directory. */
function xpertz_commerce_categories_shortcode() {
	$taxonomy = defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category';
	$terms    = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
	ob_start();
	?>
	<section class="xhc-standalone-account xhc-category-directory">
		<div class="xhc-account-heading"><span class="xhc-eyebrow"><?php esc_html_e( 'Learning paths', 'edupress' ); ?></span><h2><?php esc_html_e( 'Find courses by category', 'edupress' ); ?></h2><p><?php esc_html_e( 'Choose a focused subject area, then compare practical courses built for real-world progress.', 'edupress' ); ?></p></div>
		<div class="xhc-category-grid">
			<a class="xhc-category-card is-featured" href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>"><span><?php echo xpertz_commerce_icon( 'layers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><small><?php esc_html_e( 'Complete catalog', 'edupress' ); ?></small><h3><?php esc_html_e( 'All courses', 'edupress' ); ?></h3><p><?php esc_html_e( 'Browse every available XPERTZ learning experience.', 'edupress' ); ?></p></div><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			<?php if ( ! is_wp_error( $terms ) ) : ?>
				<?php foreach ( $terms as $term ) : ?>
					<a class="xhc-category-card" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><span><?php echo xpertz_commerce_icon( 'briefcase' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><small><?php echo esc_html( sprintf( _n( '%s course', '%s courses', $term->count, 'edupress' ), number_format_i18n( $term->count ) ) ); ?></small><h3><?php echo esc_html( $term->name ); ?></h3><p><?php echo esc_html( $term->description ?: __( 'Build practical skills with focused expert-led training.', 'edupress' ) ); ?></p></div><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'xpertz_course_categories', 'xpertz_commerce_categories_shortcode' );

/** Render the branded About page content. */
function xpertz_commerce_about_shortcode() {
	$course_count  = (int) ( wp_count_posts( 'lp_course' )->publish ?? 0 );
	$user_counts   = count_users();
	$learner_count = array_sum( array_intersect_key( $user_counts['avail_roles'], array_flip( array( 'subscriber', 'customer', 'lp_student' ) ) ) );
	ob_start();
	?>
	<section class="xhc-standalone-account xhc-about-page">
		<div class="xhc-about-lead"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Built for forward motion', 'edupress' ); ?></span><h2><?php esc_html_e( 'Practical learning for meaningful career growth', 'edupress' ); ?></h2></div><p><?php esc_html_e( 'XPERTZ brings expert-led courses, clear learning paths, and secure access together in one focused platform. Every experience is designed to help professionals turn knowledge into capability.', 'edupress' ); ?></p></div>
		<div class="xhc-about-stats"><div><strong><?php echo esc_html( number_format_i18n( $course_count ) ); ?>+</strong><span><?php esc_html_e( 'Focused courses', 'edupress' ); ?></span></div><div><strong><?php echo esc_html( number_format_i18n( $learner_count ) ); ?>+</strong><span><?php esc_html_e( 'Registered learners', 'edupress' ); ?></span></div><div><strong>24/7</strong><span><?php esc_html_e( 'Learning access', 'edupress' ); ?></span></div></div>
		<div class="xhc-value-grid"><article><span><?php echo xpertz_commerce_icon( 'briefcase' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Career-relevant', 'edupress' ); ?></h3><p><?php esc_html_e( 'Focused content helps learners build capabilities they can apply in real professional settings.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Structured clearly', 'edupress' ); ?></h3><p><?php esc_html_e( 'Thoughtful lessons, quizzes, and progress tracking make every next step easy to understand.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Designed with trust', 'edupress' ); ?></h3><p><?php esc_html_e( 'Secure purchasing, reliable access, and learner-first support are built into the experience.', 'edupress' ); ?></p></article></div>
		<div class="xhc-page-cta"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Start your next chapter', 'edupress' ); ?></span><h2><?php esc_html_e( 'Choose the skill you want to build next.', 'edupress' ); ?></h2></div><a class="xhc-primary-link" href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>"><?php esc_html_e( 'Explore courses', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'xpertz_about', 'xpertz_commerce_about_shortcode' );

/** Render a distinct public Contact page. */
function xpertz_commerce_contact_shortcode() {
	$account_url = function_exists( 'wc_get_account_endpoint_url' ) && is_user_logged_in() ? wc_get_account_endpoint_url( 'support' ) : wc_get_page_permalink( 'myaccount' );
	ob_start();
	?>
	<section class="xhc-standalone-account xhc-contact-page">
		<div class="xhc-contact-panel"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Learner-first assistance', 'edupress' ); ?></span><h2><?php esc_html_e( 'Let’s solve it together', 'edupress' ); ?></h2><p><?php esc_html_e( 'For the fastest response, sign in and send a support ticket with your course or order details.', 'edupress' ); ?></p><a class="xhc-primary-link" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Contact learner support', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div><span class="xhc-contact-visual"><?php echo xpertz_commerce_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></div>
		<div class="xhc-value-grid"><article><span><?php echo xpertz_commerce_icon( 'ticket' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Course and order help', 'edupress' ); ?></h3><p><?php esc_html_e( 'Include the course title or order number so the support team can respond efficiently.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'profile' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Account access', 'edupress' ); ?></h3><p><?php esc_html_e( 'Get assistance with registration, sign-in, learning access, and profile settings.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'help' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'General questions', 'edupress' ); ?></h3><p><?php esc_html_e( 'Ask about course content, certificates, pricing, or choosing your next learning path.', 'edupress' ); ?></p></article></div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'xpertz_contact', 'xpertz_commerce_contact_shortcode' );

/**
 * Render the public course pricing catalog.
 *
 * @return string
 */
function xpertz_commerce_pricing_shortcode() {
	$course_ids = get_posts(
		array(
			'post_type'      => 'lp_course',
			'post_status'    => 'publish',
			'posts_per_page' => 12,
			'orderby'        => 'menu_order date',
			'order'          => 'DESC',
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	ob_start();
	echo '<section class="xhc-standalone-account xhc-pricing-catalog"><div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Simple course pricing', 'edupress' ) . '</span><h2>' . esc_html__( 'Invest in skills that move you forward', 'edupress' ) . '</h2><p>' . esc_html__( 'Choose a focused course, pay securely, and receive immediate lifetime access through your XPERTZ account.', 'edupress' ) . '</p></div>';
	xpertz_commerce_account_course_grid( $course_ids, false );
	echo '</section>';
	return ob_get_clean();
}
add_shortcode( 'xpertz_course_pricing', 'xpertz_commerce_pricing_shortcode' );

/**
 * Render a stable public support and contact destination.
 *
 * @return string
 */
function xpertz_commerce_support_shortcode() {
	$account_url = function_exists( 'wc_get_account_endpoint_url' )
		? ( is_user_logged_in() ? wc_get_account_endpoint_url( 'support' ) : wc_get_page_permalink( 'myaccount' ) )
		: wp_login_url();

	ob_start();
	?>
	<section class="xhc-standalone-account xhc-support-landing">
		<div class="xhc-account-heading">
			<span class="xhc-eyebrow"><?php esc_html_e( 'Learner-first support', 'edupress' ); ?></span>
			<h2><?php esc_html_e( 'How can we help?', 'edupress' ); ?></h2>
			<p><?php esc_html_e( 'Get help with a course, payment, order, or account from the XPERTZ support team.', 'edupress' ); ?></p>
		</div>
		<div class="xhc-settings-grid">
			<a href="<?php echo esc_url( $account_url ); ?>"><?php echo xpertz_commerce_icon( 'ticket' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Open a support ticket', 'edupress' ); ?></strong><small><?php esc_html_e( 'Send a secure request from your learning account', 'edupress' ); ?></small></span></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'courses' ) ); ?>"><?php echo xpertz_commerce_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Browse courses', 'edupress' ); ?></strong><small><?php esc_html_e( 'Explore current learning paths and course details', 'edupress' ); ?></small></span></a>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo xpertz_commerce_icon( 'profile' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Manage your account', 'edupress' ); ?></strong><small><?php esc_html_e( 'Update profile, orders, addresses, and payment methods', 'edupress' ); ?></small></span></a>
		</div>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'xpertz_support_landing', 'xpertz_commerce_support_shortcode' );

/** Render a responsive article index for the provisioned Blog page. */
function xpertz_commerce_blog_shortcode() {
	$articles = new WP_Query(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => 9,
			'ignore_sticky_posts' => false,
		)
	);
	ob_start();
	echo '<section class="xhc-standalone-account xhc-blog-index"><div class="xhc-account-heading"><span class="xhc-eyebrow">' . esc_html__( 'Ideas for continuous growth', 'edupress' ) . '</span><h2>' . esc_html__( 'XPERTZ Insights', 'edupress' ) . '</h2><p>' . esc_html__( 'Practical perspectives on skills, careers, and professional learning.', 'edupress' ) . '</p></div><div class="xhc-blog-grid">';
	while ( $articles->have_posts() ) {
		$articles->the_post();
		echo '<article><a class="xhc-blog-image" href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'medium_large', array( 'loading' => 'lazy' ) ) . '</a><div><span class="xhc-eyebrow">' . esc_html( get_the_date() ) . '</span><h2><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h2><p>' . esc_html( wp_trim_words( get_the_excerpt(), 22 ) ) . '</p><a class="xhc-text-link" href="' . esc_url( get_permalink() ) . '">' . esc_html__( 'Read article', 'edupress' ) . '</a></div></article>';
	}
	if ( ! $articles->post_count ) {
		echo '<div class="xhc-account-empty"><h3>' . esc_html__( 'New insights are on the way', 'edupress' ) . '</h3><p>' . esc_html__( 'Check back soon for new learning and career resources.', 'edupress' ) . '</p></div>';
	}
	echo '</div></section>';
	wp_reset_postdata();
	return ob_get_clean();
}
add_shortcode( 'xpertz_blog_index', 'xpertz_commerce_blog_shortcode' );

/**
 * Add high-conversion context around the native WooCommerce Cart and Checkout blocks.
 *
 * @param string $content Page content.
 * @return string
 */
function xpertz_commerce_page_context( $content ) {
	if ( is_admin() || ! is_main_query() || ! in_the_loop() ) {
		return $content;
	}

	if ( function_exists( 'is_cart' ) && is_cart() ) {
		$catalog_url = get_post_type_archive_link( 'lp_course' ) ?: home_url( '/courses/' );
		$context = '<div class="xhc-commerce-context"><div><span class="xhc-eyebrow">' . esc_html__( 'Secure learning purchase', 'edupress' ) . '</span><strong>' . esc_html__( 'Instant access after successful payment', 'edupress' ) . '</strong></div><a class="xhc-secondary-link" href="' . esc_url( $catalog_url ) . '">' . esc_html__( 'Continue shopping', 'edupress' ) . '</a></div>';
		return $context . $content;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) {
		$context = '<div class="xhc-commerce-context xhc-checkout-context"><div>' . xpertz_commerce_icon( 'award' ) . '<span><strong>' . esc_html__( 'Complete your enrollment', 'edupress' ) . '</strong><small>' . esc_html__( 'Your course appears in My Courses as soon as payment succeeds.', 'edupress' ) . '</small></span></div><span>' . esc_html__( 'Secure checkout', 'edupress' ) . '</span></div>';
		return $context . $content;
	}

	return $content;
}
add_filter( 'the_content', 'xpertz_commerce_page_context', 20 );

/**
 * Add a printable receipt action to paid orders.
 *
 * @param array    $actions Existing actions.
 * @param WC_Order $order   Order object.
 * @return array
 */
function xpertz_commerce_order_receipt_action( $actions, $order ) {
	if ( $order instanceof WC_Order && $order->is_paid() ) {
		$actions['xpertz-receipt'] = array(
			'url'  => add_query_arg( 'print-receipt', 1, $order->get_view_order_url() ),
			'name' => __( 'Receipt', 'edupress' ),
		);
	}
	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', 'xpertz_commerce_order_receipt_action', 20, 2 );

/** Mark printable order views without changing the underlying WooCommerce template. */
function xpertz_commerce_receipt_body_class( $classes ) {
	if ( function_exists( 'is_account_page' ) && is_account_page() && isset( $_GET['print-receipt'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only presentation flag.
		$classes[] = 'xhc-print-receipt';
	}
	return $classes;
}
add_filter( 'body_class', 'xpertz_commerce_receipt_body_class' );
