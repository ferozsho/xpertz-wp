<?php
/**
 * Plugin Name: XPERTZ WooCommerce LearnPress Integration
 * Description: Uses WooCommerce as the commerce engine for LearnPress courses and enrolls paid learners automatically.
 * Version: 2.3.0
 * Requires Plugins: woocommerce, learnpress
 * Text Domain: xpertz-commerce
 */

defined( 'ABSPATH' ) || exit;

define( 'XPERTZ_WC_LP_VERSION', '2.3.0' );

/**
 * Create the public LMS pages used by the global navigation.
 */
function xpertz_wc_lp_provision_pages() {
	$pages = array(
		'categories' => array( __( 'Course Categories', 'xpertz-commerce' ), '[xpertz_course_categories]' ),
		'pricing'    => array( __( 'Pricing', 'xpertz-commerce' ), '[xpertz_course_pricing]' ),
		'about'      => array( __( 'About XPERTZ', 'xpertz-commerce' ), '[xpertz_about]' ),
		'blog'       => array( __( 'XPERTZ Insights', 'xpertz-commerce' ), '[xpertz_blog_index]' ),
		'support'    => array( __( 'Learning Support', 'xpertz-commerce' ), '[xpertz_support_landing]' ),
		'contact'    => array( __( 'Contact XPERTZ', 'xpertz-commerce' ), '[xpertz_contact]' ),
		'wishlist'   => array( __( 'My Wishlist', 'xpertz-commerce' ), '[xpertz_wishlist]' ),
	);

	foreach ( $pages as $slug => $page_data ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			$page_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $page_data[0],
					'post_content' => $page_data[1],
				),
				true
			);
			$page = is_wp_error( $page_id ) ? false : get_post( $page_id );
		}

		if ( ! $page ) {
			continue;
		}

		$current_content = trim( (string) $page->post_content );
		$can_refresh     = '' === $current_content
			|| ( 'about' === $slug && false !== strpos( $current_content, 'XPERTZ helps professionals build practical' ) )
			|| ( 'contact' === $slug && '[xpertz_support_landing]' === $current_content );

		if ( $can_refresh && $current_content !== $page_data[1] ) {
			wp_update_post( array( 'ID' => $page->ID, 'post_content' => $page_data[1] ) );
		}
	}
}

/**
 * Ensure the catalog has a useful initial category instead of linking the
 * Categories navigation item back to the unfiltered course archive.
 */
function xpertz_wc_lp_provision_course_categories() {
	$taxonomy = defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category';
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return;
	}

	$term = term_exists( 'professional-development', $taxonomy );
	if ( ! $term ) {
		$term = wp_insert_term(
			__( 'Professional Development', 'xpertz-commerce' ),
			$taxonomy,
			array(
				'description' => __( 'Practical courses for career growth, leadership, and workplace capability.', 'xpertz-commerce' ),
				'slug'        => 'professional-development',
			)
		);
	}

	if ( is_wp_error( $term ) ) {
		return;
	}

	$term_id    = (int) ( is_array( $term ) ? $term['term_id'] : $term );
	$course_ids = get_posts(
		array(
			'post_type'      => 'lp_course',
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'no_found_rows'  => true,
		)
	);

	foreach ( $course_ids as $course_id ) {
		$assigned = wp_get_object_terms( $course_id, $taxonomy, array( 'fields' => 'ids' ) );
		if ( ! is_wp_error( $assigned ) && ! $assigned ) {
			wp_set_object_terms( $course_id, array( $term_id ), $taxonomy );
		}
	}
}

/** Flush endpoint and page rewrites after activation or an upgrade. */
function xpertz_wc_lp_maybe_flush_rewrites() {
	if ( ! get_option( 'xpertz_wc_lp_flush_rewrites' ) ) {
		return;
	}
	flush_rewrite_rules( false );
	delete_option( 'xpertz_wc_lp_flush_rewrites' );
}
add_action( 'init', 'xpertz_wc_lp_maybe_flush_rewrites', 99 );

/**
 * Enable account creation options when the integration is activated.
 */
function xpertz_wc_lp_activate() {
	update_option( 'users_can_register', 1 );
	update_option( 'woocommerce_enable_myaccount_registration', 'yes' );
	update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'yes' );
	update_option( 'woocommerce_registration_generate_username', 'no' );
	update_option( 'woocommerce_registration_generate_password', 'no' );
	xpertz_wc_lp_provision_pages();
	xpertz_wc_lp_provision_course_categories();
	update_option( 'xpertz_wc_lp_flush_rewrites', 1, false );
	update_option( 'xpertz_wc_lp_version', XPERTZ_WC_LP_VERSION );
}
register_activation_hook( __FILE__, 'xpertz_wc_lp_activate' );

/**
 * Return a sanitized value submitted through the WooCommerce registration form.
 *
 * WooCommerce verifies the registration nonce before processing the form. This
 * helper is also used to repopulate fields after validation errors.
 *
 * @param string $key Form field key.
 * @return string
 */
function xpertz_wc_registration_value( $key ) {
	if ( ! isset( $_POST[ $key ] ) || ! is_string( $_POST[ $key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return '';
	}

	return wc_clean( wp_unslash( $_POST[ $key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
}

/** Add customer identity fields before WooCommerce's account credentials. */
function xpertz_wc_registration_identity_fields() {
	?>
	<div class="xhc-register-name-grid">
		<p class="woocommerce-form-row form-row form-row-first">
			<label for="reg_first_name"><?php esc_html_e( 'First name', 'xpertz-commerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'xpertz-commerce' ); ?></span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="first_name" id="reg_first_name" autocomplete="given-name" value="<?php echo esc_attr( xpertz_wc_registration_value( 'first_name' ) ); ?>" required aria-required="true">
		</p>
		<p class="woocommerce-form-row form-row form-row-last">
			<label for="reg_last_name"><?php esc_html_e( 'Last name', 'xpertz-commerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'xpertz-commerce' ); ?></span></label>
			<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="last_name" id="reg_last_name" autocomplete="family-name" value="<?php echo esc_attr( xpertz_wc_registration_value( 'last_name' ) ); ?>" required aria-required="true">
		</p>
	</div>
	<?php
}
add_action( 'woocommerce_register_form_start', 'xpertz_wc_registration_identity_fields' );

/** Add contact and password-confirmation fields before WooCommerce privacy text. */
function xpertz_wc_registration_contact_fields() {
	?>
	<p class="woocommerce-form-row form-row form-row-wide">
		<label for="reg_billing_phone"><?php esc_html_e( 'Phone number', 'xpertz-commerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'xpertz-commerce' ); ?></span></label>
		<input type="tel" class="woocommerce-Input woocommerce-Input--text input-text" name="billing_phone" id="reg_billing_phone" autocomplete="tel" inputmode="tel" value="<?php echo esc_attr( xpertz_wc_registration_value( 'billing_phone' ) ); ?>" required aria-required="true">
	</p>
	<p class="woocommerce-form-row form-row form-row-wide">
		<label for="reg_password_confirm"><?php esc_html_e( 'Confirm password', 'xpertz-commerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'xpertz-commerce' ); ?></span></label>
		<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password_confirm" id="reg_password_confirm" autocomplete="new-password" required aria-required="true">
	</p>
	<?php
}
add_action( 'woocommerce_register_form', 'xpertz_wc_registration_contact_fields', 10 );

/**
 * Validate the additional customer registration fields.
 *
 * @param WP_Error $errors   Registration errors.
 * @param string   $username Requested username.
 * @param string   $password Requested password.
 * @param string   $email    Requested email address.
 * @return WP_Error
 */
function xpertz_wc_validate_registration_fields( $errors, $username, $password, $email ) {
	unset( $username, $email );

	$first_name       = xpertz_wc_registration_value( 'first_name' );
	$last_name        = xpertz_wc_registration_value( 'last_name' );
	$phone            = xpertz_wc_registration_value( 'billing_phone' );
	$password_confirm = xpertz_wc_registration_value( 'password_confirm' );

	if ( '' === $first_name ) {
		$errors->add( 'first_name_required', __( 'Please enter your first name.', 'xpertz-commerce' ) );
	}
	if ( '' === $last_name ) {
		$errors->add( 'last_name_required', __( 'Please enter your last name.', 'xpertz-commerce' ) );
	}
	if ( '' === $phone ) {
		$errors->add( 'phone_required', __( 'Please enter your phone number.', 'xpertz-commerce' ) );
	} elseif ( class_exists( 'WC_Validation' ) && ! WC_Validation::is_phone( $phone ) ) {
		$errors->add( 'phone_invalid', __( 'Please enter a valid phone number.', 'xpertz-commerce' ) );
	}
	if ( '' === $password_confirm ) {
		$errors->add( 'password_confirmation_required', __( 'Please confirm your password.', 'xpertz-commerce' ) );
	} elseif ( ! hash_equals( (string) $password, $password_confirm ) ) {
		$errors->add( 'password_mismatch', __( 'The passwords do not match.', 'xpertz-commerce' ) );
	}

	return $errors;
}
add_filter( 'woocommerce_process_registration_errors', 'xpertz_wc_validate_registration_fields', 10, 4 );

/**
 * Add submitted names to the new WordPress customer record.
 *
 * @param array $customer_data New customer data.
 * @return array
 */
function xpertz_wc_registration_customer_data( $customer_data ) {
	$first_name = xpertz_wc_registration_value( 'first_name' );
	$last_name  = xpertz_wc_registration_value( 'last_name' );
	$full_name  = trim( $first_name . ' ' . $last_name );

	$customer_data['first_name'] = $first_name;
	$customer_data['last_name']  = $last_name;
	if ( $full_name ) {
		$customer_data['display_name'] = $full_name;
	}

	return $customer_data;
}
add_filter( 'woocommerce_new_customer_data', 'xpertz_wc_registration_customer_data' );

/**
 * Store WooCommerce billing identity data for a newly registered customer.
 *
 * @param int $customer_id New customer ID.
 */
function xpertz_wc_save_registration_customer_meta( $customer_id ) {
	update_user_meta( $customer_id, 'billing_first_name', xpertz_wc_registration_value( 'first_name' ) );
	update_user_meta( $customer_id, 'billing_last_name', xpertz_wc_registration_value( 'last_name' ) );
	update_user_meta( $customer_id, 'billing_phone', xpertz_wc_registration_value( 'billing_phone' ) );
}
add_action( 'woocommerce_created_customer', 'xpertz_wc_save_registration_customer_meta' );

/**
 * Return the mapped WooCommerce product for a LearnPress course.
 *
 * @param int $course_id Course ID.
 * @return int
 */
function xpertz_wc_get_product_id_for_course( $course_id ) {
	$product_id = (int) get_post_meta( absint( $course_id ), '_related_woocommerce_product_id', true );
	return $product_id && 'product' === get_post_type( $product_id ) ? $product_id : 0;
}

/**
 * Return the mapped LearnPress course for a WooCommerce product.
 *
 * @param int $product_id Product or variation ID.
 * @return int
 */
function xpertz_wc_get_course_id_for_product( $product_id ) {
	$product_id = absint( $product_id );
	if ( 'product_variation' === get_post_type( $product_id ) ) {
		$product_id = (int) wp_get_post_parent_id( $product_id );
	}

	$course_id = (int) get_post_meta( $product_id, '_related_course_id', true );
	return $course_id && 'lp_course' === get_post_type( $course_id ) ? $course_id : 0;
}

/**
 * Determine whether a product represents a course.
 *
 * @param int $product_id Product ID.
 * @return bool
 */
function xpertz_wc_is_course_product( $product_id ) {
	return xpertz_wc_get_course_id_for_product( $product_id ) > 0;
}

/**
 * Keep LearnPress price metadata aligned with the WooCommerce product.
 *
 * WooCommerce remains authoritative for regular and sale pricing.
 *
 * @param int $product_id Product ID.
 */
function xpertz_wc_sync_course_price( $product_id ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	$product   = wc_get_product( $product_id );
	$course_id = xpertz_wc_get_course_id_for_product( $product_id );
	if ( ! $product || ! $course_id ) {
		return;
	}

	$regular_price = $product->get_regular_price( 'edit' );
	$sale_price    = $product->get_sale_price( 'edit' );
	$current_price = $product->get_price( 'edit' );

	update_post_meta( $course_id, '_lp_price', '' === $current_price ? '0' : wc_format_decimal( $current_price ) );
	update_post_meta( $course_id, '_lp_regular_price', '' === $regular_price ? '0' : wc_format_decimal( $regular_price ) );

	if ( '' === $sale_price ) {
		delete_post_meta( $course_id, '_lp_sale_price' );
		delete_post_meta( $course_id, '_lp_course_is_sale' );
	} else {
		update_post_meta( $course_id, '_lp_sale_price', wc_format_decimal( $sale_price ) );
		update_post_meta( $course_id, '_lp_course_is_sale', 1 );
	}

	clean_post_cache( $course_id );
}
add_action( 'woocommerce_update_product', 'xpertz_wc_sync_course_price' );

/**
 * Make a course and product a two-way mapping.
 *
 * @param int $course_id  Course ID.
 * @param int $product_id Product ID.
 */
function xpertz_wc_map_course_product( $course_id, $product_id ) {
	$course_id  = absint( $course_id );
	$product_id = absint( $product_id );
	if ( ! $course_id || ! $product_id ) {
		return;
	}

	update_post_meta( $course_id, '_related_woocommerce_product_id', $product_id );
	update_post_meta( $product_id, '_related_course_id', $course_id );
	xpertz_wc_sync_course_price( $product_id );
}

/**
 * Create a simple virtual product for a course that has no mapping yet.
 *
 * @param int $course_id Course ID.
 * @return int
 */
function xpertz_wc_create_course_product( $course_id ) {
	if ( ! class_exists( 'WC_Product_Simple' ) || 'lp_course' !== get_post_type( $course_id ) ) {
		return 0;
	}

	$product = new WC_Product_Simple();
	$product->set_name( get_the_title( $course_id ) );
	$product->set_slug( sanitize_title( get_the_title( $course_id ) . '-course' ) );
	$product->set_status( 'publish' === get_post_status( $course_id ) ? 'publish' : 'draft' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_virtual( true );
	$product->set_sold_individually( true );
	$product->set_manage_stock( false );
	$product->set_stock_status( 'instock' );
	$product->set_regular_price( (string) max( 0, (float) get_post_meta( $course_id, '_lp_regular_price', true ) ) );
	$product->set_description( (string) get_post_field( 'post_content', $course_id ) );
	$product->set_short_description( (string) get_post_field( 'post_excerpt', $course_id ) );
	$product->set_image_id( get_post_thumbnail_id( $course_id ) );
	$product_id = $product->save();

	if ( $product_id ) {
		xpertz_wc_map_course_product( $course_id, $product_id );
	}

	return (int) $product_id;
}

/**
 * Ensure every published course has a valid product and normalized settings.
 */
function xpertz_wc_sync_all_courses() {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return;
	}

	$course_ids = get_posts(
		array(
			'post_type'      => 'lp_course',
			'post_status'    => array( 'publish', 'draft', 'private' ),
			'posts_per_page' => -1,
			'fields'         => 'ids',
		)
	);

	foreach ( $course_ids as $course_id ) {
		$product_id = xpertz_wc_get_product_id_for_course( $course_id );
		if ( ! $product_id ) {
			$product_id = xpertz_wc_create_course_product( $course_id );
		}

		$product = $product_id ? wc_get_product( $product_id ) : false;
		if ( ! $product ) {
			continue;
		}

		$course_post = get_post( $course_id );
		$image_id    = get_post_thumbnail_id( $course_id );
		$changed = false;
		if ( $course_post && $product->get_name( 'edit' ) !== $course_post->post_title ) {
			$product->set_name( $course_post->post_title );
			$changed = true;
		}
		if ( $course_post && $product->get_description( 'edit' ) !== $course_post->post_content ) {
			$product->set_description( $course_post->post_content );
			$changed = true;
		}
		if ( $course_post && $product->get_short_description( 'edit' ) !== $course_post->post_excerpt ) {
			$product->set_short_description( $course_post->post_excerpt );
			$changed = true;
		}
		if ( $image_id && (int) $product->get_image_id( 'edit' ) !== (int) $image_id ) {
			$product->set_image_id( $image_id );
			$changed = true;
		}
		if ( ! $product->is_virtual() ) {
			$product->set_virtual( true );
			$changed = true;
		}
		if ( ! $product->is_sold_individually() ) {
			$product->set_sold_individually( true );
			$changed = true;
		}
		if ( 'instock' !== $product->get_stock_status( 'edit' ) ) {
			$product->set_stock_status( 'instock' );
			$changed = true;
		}
		if ( $changed ) {
			$product->save();
		}

		xpertz_wc_map_course_product( $course_id, $product_id );
	}

	update_option( 'xpertz_wc_lp_last_sync', time(), false );
}

/**
 * Run the mapping migration once after this plugin version changes.
 */
function xpertz_wc_maybe_upgrade() {
	if ( XPERTZ_WC_LP_VERSION === get_option( 'xpertz_wc_lp_version' ) ) {
		return;
	}

	xpertz_wc_lp_activate();
	xpertz_wc_sync_all_courses();
}
add_action( 'admin_init', 'xpertz_wc_maybe_upgrade' );

/**
 * Refresh product metadata when a course is saved without creating recursion.
 *
 * @param int     $course_id Course ID.
 * @param WP_Post $post      Course post.
 */
function xpertz_wc_course_saved( $course_id, $post ) {
	if ( wp_is_post_revision( $course_id ) || wp_is_post_autosave( $course_id ) ) {
		return;
	}

	$product_id = xpertz_wc_get_product_id_for_course( $course_id );
	if ( ! $product_id ) {
		xpertz_wc_create_course_product( $course_id );
		return;
	}

	$product = wc_get_product( $product_id );
	if ( ! $product ) {
		return;
	}

	$product->set_name( $post->post_title );
	$product->set_status( 'publish' === $post->post_status ? 'publish' : 'draft' );
	$product->set_description( $post->post_content );
	$product->set_short_description( $post->post_excerpt );
	$product->set_image_id( get_post_thumbnail_id( $course_id ) );
	$product->save();
}
add_action( 'save_post_lp_course', 'xpertz_wc_course_saved', 20, 2 );

/**
 * Read current WooCommerce pricing when LearnPress builds a course model.
 *
 * @param float $price     LearnPress price.
 * @param mixed $course_or_id Course model or ID.
 * @return float
 */
function xpertz_wc_filter_course_price( $price, $course_or_id ) {
	$course_id  = is_object( $course_or_id ) && method_exists( $course_or_id, 'get_id' ) ? $course_or_id->get_id() : absint( $course_or_id );
	$product_id = xpertz_wc_get_product_id_for_course( $course_id );
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	return $product ? (float) $product->get_price() : (float) $price;
}
add_filter( 'learnPress/course/price', 'xpertz_wc_filter_course_price', 20, 2 );

/**
 * Return the WooCommerce regular course price.
 *
 * @param float $price        LearnPress regular price.
 * @param mixed $course_or_id Course post model or ID.
 * @return float
 */
function xpertz_wc_filter_course_regular_price( $price, $course_or_id ) {
	$course_id  = is_object( $course_or_id ) && method_exists( $course_or_id, 'get_id' ) ? $course_or_id->get_id() : absint( $course_or_id );
	$product_id = xpertz_wc_get_product_id_for_course( $course_id );
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	return $product && '' !== $product->get_regular_price() ? (float) $product->get_regular_price() : (float) $price;
}
add_filter( 'learnPress/course/regular-price', 'xpertz_wc_filter_course_regular_price', 20, 2 );

/**
 * Replace the modern LearnPress purchase form with WooCommerce cart controls.
 *
 * @param array $section      LearnPress button components.
 * @param mixed $course_model Course model.
 * @param mixed $user_model   User model.
 * @return array
 */
function xpertz_wc_modern_purchase_buttons( $section, $course_model, $user_model ) {
	$course_id = is_object( $course_model ) && method_exists( $course_model, 'get_id' ) ? $course_model->get_id() : 0;
	$product_id = xpertz_wc_get_product_id_for_course( $course_id );
	$product    = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false;
	if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
		return $section;
	}

	return array(
		'wrapper' => sprintf( '<div class="lp-woocommerce-purchase-wrapper" data-course-product="%d">', $product_id ),
		'add'     => sprintf( '<button type="button" class="lp-button button button-purchase-course" data-add-course-to-cart="%1$d">%2$s</button>', $product_id, esc_html__( 'Add course to cart', 'xpertz-commerce' ) ),
		'buy'     => sprintf( '<button type="button" class="lp-button button xpc-buy-now-button" data-buy-course-now="%1$d">%2$s</button>', $product_id, esc_html__( 'Buy now', 'xpertz-commerce' ) ),
		'end'     => '</div>',
	);
}
add_filter( 'learn-press/course/html-button-purchase', 'xpertz_wc_modern_purchase_buttons', 20, 3 );

/**
 * Guarantee a clear continuation action for learners who own the course.
 *
 * @param array $section      Modern button components.
 * @param mixed $course_model Course model.
 * @param mixed $user_model   User model.
 * @return array
 */
function xpertz_wc_modern_owned_course_button( $section, $course_model, $user_model ) {
	$course_id = is_object( $course_model ) && method_exists( $course_model, 'get_id' ) ? $course_model->get_id() : 0;
	if ( ! $course_id || ! xpertz_wc_user_owns_course( $course_id ) ) {
		return $section;
	}

	$section['btn_buy']    = '';
	$section['btn_enroll'] = '';
	$first_item  = method_exists( $course_model, 'get_first_item_id' ) ? $course_model->get_first_item_id() : 0;
	$continue_url = $first_item && method_exists( $course_model, 'get_item_link' ) ? $course_model->get_item_link( $first_item ) : get_permalink( $course_id ) . '#curriculum';
	$section['btn_learning'] = sprintf(
		'<a class="lp-button button button-continue-course" href="%1$s">%2$s</a>',
		esc_url( $continue_url ),
		esc_html__( 'Continue learning', 'xpertz-commerce' )
	);

	return $section;
}
add_filter( 'learn-press/single-course/modern/section-right/buttons', 'xpertz_wc_modern_owned_course_button', 99, 3 );

/**
 * Return whether the current user already owns a course.
 *
 * @param int $course_id Course ID.
 * @param int $user_id   Optional user ID.
 * @return bool
 */
function xpertz_wc_user_owns_course( $course_id, $user_id = 0 ) {
	$user_id = $user_id ?: get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	if ( function_exists( 'learn_press_get_user' ) ) {
		$user = learn_press_get_user( $user_id );
		if ( $user && $user->has_enrolled_or_finished( $course_id ) ) {
			return true;
		}
	}

	$product_id = xpertz_wc_get_product_id_for_course( $course_id );
	$wp_user    = get_userdata( $user_id );
	return $product_id && $wp_user && wc_customer_bought_product( $wp_user->user_email, $user_id, $product_id );
}

/**
 * Prevent course products from being purchased twice.
 *
 * @param bool $passed     Existing validation result.
 * @param int  $product_id Product ID.
 * @return bool
 */
function xpertz_wc_validate_course_purchase( $passed, $product_id ) {
	$course_id = xpertz_wc_get_course_id_for_product( $product_id );
	if ( ! $course_id || ! is_user_logged_in() ) {
		return $passed;
	}
	$gift_email = isset( $_REQUEST['xpertz_gift_email'] ) && is_string( $_REQUEST['xpertz_gift_email'] ) ? sanitize_email( wp_unslash( $_REQUEST['xpertz_gift_email'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Validated by the gift AJAX endpoint.
	if ( $gift_email ) {
		$recipient = get_user_by( 'email', $gift_email );
		if ( $recipient && xpertz_wc_user_owns_course( $course_id, $recipient->ID ) ) {
			wc_add_notice( __( 'The gift recipient already owns this course.', 'xpertz-commerce' ), 'error' );
			return false;
		}
		return $passed;
	}

	if ( xpertz_wc_user_owns_course( $course_id ) ) {
		wc_add_notice( __( 'You already own this course. Continue learning from My Courses.', 'xpertz-commerce' ), 'error' );
		return false;
	}

	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'xpertz_wc_validate_course_purchase', 20, 2 );

/**
 * Make an owned course product non-purchasable.
 *
 * @param bool       $purchasable Current state.
 * @param WC_Product $product     Product object.
 * @return bool
 */
function xpertz_wc_course_is_purchasable( $purchasable, $product ) {
	$course_id = $product ? xpertz_wc_get_course_id_for_product( $product->get_id() ) : 0;
	if ( isset( $_REQUEST['xpertz_gift_email'] ) && is_string( $_REQUEST['xpertz_gift_email'] ) && sanitize_email( wp_unslash( $_REQUEST['xpertz_gift_email'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Validated by the gift AJAX endpoint.
		return $purchasable;
	}
	return $course_id && xpertz_wc_user_owns_course( $course_id ) ? false : $purchasable;
}
add_filter( 'woocommerce_is_purchasable', 'xpertz_wc_course_is_purchasable', 20, 2 );

/** Add a validated gift course to the WooCommerce cart and return fresh fragments. */
function xpertz_wc_ajax_add_gift_course() {
	check_ajax_referer( 'xpertz_gift_course', 'nonce' );
	$product_id = isset( $_POST['productId'] ) ? absint( $_POST['productId'] ) : 0;
	$email      = isset( $_POST['email'] ) && is_string( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$name       = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
	$message    = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
	$course_id  = xpertz_wc_get_course_id_for_product( $product_id );
	$product    = $product_id ? wc_get_product( $product_id ) : false;

	if ( ! $course_id || ! $product || ! is_email( $email ) || ! $product->is_in_stock() ) {
		wp_send_json_error( array( 'message' => __( 'Enter a valid recipient email for an available course.', 'xpertz-commerce' ) ), 400 );
	}

	$recipient = get_user_by( 'email', $email );
	if ( $recipient && xpertz_wc_user_owns_course( $course_id, $recipient->ID ) ) {
		wp_send_json_error( array( 'message' => __( 'The gift recipient already owns this course.', 'xpertz-commerce' ) ), 409 );
	}

	$_POST['xpertz_gift_email'] = $email;
	$cart_item_key = WC()->cart->add_to_cart(
		$product_id,
		1,
		0,
		array(),
		array(
			'xpertz_gift_email'   => $email,
			'xpertz_gift_name'    => $name,
			'xpertz_gift_message' => $message,
		)
	);
	if ( ! $cart_item_key ) {
		wp_send_json_error( array( 'message' => __( 'This gift could not be added to the cart.', 'xpertz-commerce' ) ), 409 );
	}

	WC_AJAX::get_refreshed_fragments();
}
add_action( 'wp_ajax_xpertz_add_gift_course', 'xpertz_wc_ajax_add_gift_course' );
add_action( 'wp_ajax_nopriv_xpertz_add_gift_course', 'xpertz_wc_ajax_add_gift_course' );

/**
 * Show course context in cart and checkout line items.
 *
 * @param array $data      Item display data.
 * @param array $cart_item Cart item.
 * @return array
 */
function xpertz_wc_course_cart_item_data( $data, $cart_item ) {
	$course_id = xpertz_wc_get_course_id_for_product( $cart_item['product_id'] ?? 0 );
	if ( ! $course_id ) {
		return $data;
	}

	$author_id = (int) get_post_field( 'post_author', $course_id );
	$data[]    = array(
		'key'   => __( 'Course access', 'xpertz-commerce' ),
		'value' => __( 'Single learner · Immediate access', 'xpertz-commerce' ),
	);
	$data[]    = array(
		'key'   => __( 'Instructor', 'xpertz-commerce' ),
		'value' => get_the_author_meta( 'display_name', $author_id ) ?: __( 'XPERTZ Faculty', 'xpertz-commerce' ),
	);
	if ( ! empty( $cart_item['xpertz_gift_email'] ) ) {
		$data[] = array(
			'key'   => __( 'Gift recipient', 'xpertz-commerce' ),
			'value' => sanitize_email( $cart_item['xpertz_gift_email'] ),
		);
	}

	return $data;
}
add_filter( 'woocommerce_get_item_data', 'xpertz_wc_course_cart_item_data', 10, 2 );

/**
 * Store a stable course reference on every order line.
 *
 * @param WC_Order_Item_Product $item          Order item.
 * @param string                $cart_item_key Cart key.
 * @param array                 $values        Cart item values.
 */
function xpertz_wc_add_order_course_meta( $item, $cart_item_key, $values ) {
	$course_id = xpertz_wc_get_course_id_for_product( $values['product_id'] ?? 0 );
	if ( $course_id ) {
		$item->add_meta_data( '_xpertz_course_id', $course_id, true );
		if ( ! empty( $values['xpertz_gift_email'] ) ) {
			$item->add_meta_data( '_xpertz_gift_email', sanitize_email( $values['xpertz_gift_email'] ), true );
			$item->add_meta_data( '_xpertz_gift_name', sanitize_text_field( $values['xpertz_gift_name'] ?? '' ), true );
			$item->add_meta_data( '_xpertz_gift_message', sanitize_textarea_field( $values['xpertz_gift_message'] ?? '' ), true );
			$item->add_meta_data( __( 'Gift recipient', 'xpertz-commerce' ), sanitize_email( $values['xpertz_gift_email'] ), true );
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'xpertz_wc_add_order_course_meta', 10, 3 );

/**
 * Course carts always need an account so successful payment can grant access.
 *
 * @param bool $required Current requirement.
 * @return bool
 */
function xpertz_wc_course_checkout_requires_account( $required ) {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return $required;
	}

	foreach ( WC()->cart->get_cart() as $cart_item ) {
		if ( xpertz_wc_is_course_product( $cart_item['product_id'] ?? 0 ) ) {
			return true;
		}
	}

	return $required;
}
add_filter( 'woocommerce_checkout_registration_required', 'xpertz_wc_course_checkout_requires_account' );

/**
 * Resolve or create the learner attached to a paid order.
 *
 * @param WC_Order $order Order object.
 * @return int
 */
function xpertz_wc_resolve_order_user( $order ) {
	$user_id = (int) $order->get_user_id();
	if ( $user_id ) {
		return $user_id;
	}

	$email = sanitize_email( $order->get_billing_email() );
	if ( ! $email ) {
		return 0;
	}

	$user = get_user_by( 'email', $email );
	if ( $user ) {
		$user_id = (int) $user->ID;
	} elseif ( function_exists( 'wc_create_new_customer' ) ) {
		$customer = wc_create_new_customer( $email, '', '', array( 'first_name' => $order->get_billing_first_name(), 'last_name' => $order->get_billing_last_name() ) );
		$user_id  = is_wp_error( $customer ) ? 0 : (int) $customer;
	}

	if ( $user_id ) {
		$order->set_customer_id( $user_id );
		$order->save();
	}

	return $user_id;
}

/**
 * Resolve or create a learner for a gift recipient email.
 *
 * @param string $email Recipient email.
 * @param string $name  Recipient name.
 * @return int
 */
function xpertz_wc_resolve_gift_user( $email, $name = '' ) {
	$email = sanitize_email( $email );
	if ( ! $email ) {
		return 0;
	}
	$user = get_user_by( 'email', $email );
	if ( $user ) {
		return (int) $user->ID;
	}
	if ( ! function_exists( 'wc_create_new_customer' ) ) {
		return 0;
	}
	$name_parts = preg_split( '/\s+/', trim( $name ), 2 );
	$user_id    = wc_create_new_customer(
		$email,
		'',
		'',
		array(
			'first_name' => $name_parts[0] ?? '',
			'last_name'  => $name_parts[1] ?? '',
		)
	);
	return is_wp_error( $user_id ) ? 0 : (int) $user_id;
}

/**
 * Grant a user LearnPress access without replacing existing progress.
 *
 * @param int $user_id   User ID.
 * @param int $course_id Course ID.
 * @param int $order_id  WooCommerce order ID.
 * @return bool
 */
function xpertz_wc_enroll_user( $user_id, $course_id, $order_id ) {
	if ( ! class_exists( 'LearnPress\\Models\\UserItems\\UserCourseModel' ) || ! defined( 'LP_COURSE_CPT' ) ) {
		return false;
	}

	$user_course = LearnPress\Models\UserItems\UserCourseModel::find( $user_id, $course_id, false );
	if ( $user_course && $user_course->has_enrolled_or_finished() ) {
		return true;
	}

	if ( ! $user_course ) {
		$user_course            = new LearnPress\Models\UserItems\UserCourseModel();
		$user_course->user_id   = $user_id;
		$user_course->item_id   = $course_id;
		$user_course->item_type = LP_COURSE_CPT;
	}

	$user_course->ref_id     = $order_id;
	$user_course->ref_type   = 'woocommerce_order';
	$user_course->status     = LearnPress\Models\UserItems\UserItemModel::STATUS_ENROLLED;
	$user_course->graduation = LearnPress\Models\UserItems\UserItemModel::GRADUATION_IN_PROGRESS;
	$user_course->start_time = gmdate( 'Y-m-d H:i:s', time() );
	$user_course->end_time   = null;
	$user_course->save();

	do_action( 'learnpress/user/course-enrolled', $order_id, $course_id, $user_id );
	return true;
}

/**
 * Enroll all course items after successful payment.
 *
 * @param int $order_id Order ID.
 */
function xpertz_wc_enroll_order_courses( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order || ! $order->is_paid() ) {
		return;
	}

	$user_id = xpertz_wc_resolve_order_user( $order );
	if ( ! $user_id ) {
		$order->add_order_note( __( 'Course enrollment is pending because no learner account could be resolved.', 'xpertz-commerce' ) );
		return;
	}

	$enrolled = array_values( array_filter( array_map( 'absint', (array) $order->get_meta( '_xpertz_enrolled_courses', true ) ) ) );
	$enrollment_keys = array_values( array_filter( array_map( 'sanitize_text_field', (array) $order->get_meta( '_xpertz_enrollment_keys', true ) ) ) );
	foreach ( $order->get_items() as $item ) {
		$course_id = (int) $item->get_meta( '_xpertz_course_id', true );
		if ( ! $course_id ) {
			$course_id = xpertz_wc_get_course_id_for_product( $item->get_product_id() );
		}
		if ( ! $course_id ) {
			continue;
		}
		$gift_email = sanitize_email( $item->get_meta( '_xpertz_gift_email', true ) );
		$learner_id = $gift_email ? xpertz_wc_resolve_gift_user( $gift_email, (string) $item->get_meta( '_xpertz_gift_name', true ) ) : $user_id;
		if ( ! $learner_id ) {
			$order->add_order_note( sprintf( __( 'Gift enrollment for %s is pending because a learner account could not be created.', 'xpertz-commerce' ), $gift_email ) );
			continue;
		}
		$enrollment_key = $course_id . ':' . $learner_id;
		if ( in_array( $enrollment_key, $enrollment_keys, true ) || ( ! $gift_email && in_array( $course_id, $enrolled, true ) ) ) {
			continue;
		}

		if ( xpertz_wc_enroll_user( $learner_id, $course_id, $order_id ) ) {
			$enrolled[] = $course_id;
			$enrollment_keys[] = $enrollment_key;
			if ( $gift_email ) {
				$order->add_order_note( sprintf( __( 'Gift course access granted to %s.', 'xpertz-commerce' ), $gift_email ) );
			}
		}
	}

	$order->update_meta_data( '_xpertz_enrolled_courses', array_values( array_filter( array_unique( array_map( 'absint', $enrolled ) ) ) ) );
	$order->update_meta_data( '_xpertz_enrollment_keys', array_values( array_unique( $enrollment_keys ) ) );
	$order->save();
}
add_action( 'woocommerce_payment_complete', 'xpertz_wc_enroll_order_courses' );
add_action( 'woocommerce_order_status_processing', 'xpertz_wc_enroll_order_courses' );
add_action( 'woocommerce_order_status_completed', 'xpertz_wc_enroll_order_courses' );

/**
 * Revoke active access after a refund while keeping course progress intact.
 *
 * @param int $order_id Order ID.
 */
function xpertz_wc_revoke_refunded_courses( $order_id ) {
	$order   = wc_get_order( $order_id );
	$user_id = $order ? (int) $order->get_user_id() : 0;
	if ( ! $order || ! $user_id || ! class_exists( 'LearnPress\\Models\\UserItems\\UserCourseModel' ) ) {
		return;
	}

	foreach ( $order->get_items() as $item ) {
		$course_id  = (int) $item->get_meta( '_xpertz_course_id', true );
		$course_id  = $course_id ?: xpertz_wc_get_course_id_for_product( $item->get_product_id() );
		$gift_email = sanitize_email( $item->get_meta( '_xpertz_gift_email', true ) );
		$learner    = $gift_email ? get_user_by( 'email', $gift_email ) : false;
		$learner_id = $learner ? (int) $learner->ID : $user_id;
		if ( $course_id && xpertz_wc_has_other_paid_course_order( $learner_id, $course_id, $order_id ) ) {
			continue;
		}
		$user_course = $course_id ? LearnPress\Models\UserItems\UserCourseModel::find( $learner_id, $course_id, false ) : false;
		if ( $user_course && $user_course->has_enrolled() ) {
			$user_course->status = LearnPress\Models\UserItems\UserItemModel::STATUS_CANCEL;
			$user_course->save();
		}
	}
}
add_action( 'woocommerce_order_status_refunded', 'xpertz_wc_revoke_refunded_courses' );
add_action( 'woocommerce_order_status_cancelled', 'xpertz_wc_revoke_refunded_courses' );

/**
 * Check for another paid order that still grants the same course.
 *
 * @param int $user_id   User ID.
 * @param int $course_id Course ID.
 * @param int $exclude   Order ID being revoked.
 * @return bool
 */
function xpertz_wc_has_other_paid_course_order( $user_id, $course_id, $exclude ) {
	$orders = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'exclude'     => $exclude,
			'status'      => wc_get_is_paid_statuses(),
			'limit'       => -1,
			'return'      => 'objects',
		)
	);

	foreach ( $orders as $paid_order ) {
		if ( (int) $paid_order->get_id() === (int) $exclude ) {
			continue;
		}
		foreach ( $paid_order->get_items() as $item ) {
			$item_course = (int) $item->get_meta( '_xpertz_course_id', true );
			$item_course = $item_course ?: xpertz_wc_get_course_id_for_product( $item->get_product_id() );
			if ( $item_course === (int) $course_id ) {
				return true;
			}
		}
	}

	$user = get_userdata( $user_id );
	if ( $user ) {
		$gift_orders = wc_get_orders(
			array(
				'exclude' => $exclude,
				'status'  => wc_get_is_paid_statuses(),
				'limit'   => -1,
				'return'  => 'objects',
			)
		);
		foreach ( $gift_orders as $gift_order ) {
			foreach ( $gift_order->get_items() as $item ) {
				$item_course = (int) $item->get_meta( '_xpertz_course_id', true );
				$item_course = $item_course ?: xpertz_wc_get_course_id_for_product( $item->get_product_id() );
				if ( $item_course === (int) $course_id && sanitize_email( $item->get_meta( '_xpertz_gift_email', true ) ) === $user->user_email ) {
					return true;
				}
			}
		}
	}

	return false;
}

/**
 * Send Buy Now course requests directly to checkout.
 *
 * @param string $url Default redirect.
 * @return string
 */
function xpertz_wc_buy_now_redirect( $url ) {
	$buy_now = isset( $_REQUEST['xpertz-buy-now'] ) ? absint( $_REQUEST['xpertz-buy-now'] ) : 0;
	return $buy_now ? wc_get_checkout_url() : $url;
}
add_filter( 'woocommerce_add_to_cart_redirect', 'xpertz_wc_buy_now_redirect' );

/**
 * Link course products back to their richer LearnPress page.
 */
function xpertz_wc_redirect_product_to_course() {
	if ( ! is_singular( 'product' ) ) {
		return;
	}

	$course_id = xpertz_wc_get_course_id_for_product( get_queried_object_id() );
	if ( $course_id ) {
		wp_safe_redirect( get_permalink( $course_id ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'xpertz_wc_redirect_product_to_course' );

/**
 * Use course URLs for cart line items.
 *
 * @param string     $permalink Current permalink.
 * @param array|null $cart_item Cart item.
 * @return string
 */
function xpertz_wc_course_cart_permalink( $permalink, $cart_item ) {
	$course_id = xpertz_wc_get_course_id_for_product( $cart_item['product_id'] ?? 0 );
	return $course_id ? get_permalink( $course_id ) : $permalink;
}
add_filter( 'woocommerce_cart_item_permalink', 'xpertz_wc_course_cart_permalink', 10, 2 );

/** Return shoppers to the course catalog from empty-cart screens. */
function xpertz_wc_return_to_courses() {
	$url = get_post_type_archive_link( 'lp_course' );
	return $url ?: home_url( '/courses/' );
}
add_filter( 'woocommerce_return_to_shop_redirect', 'xpertz_wc_return_to_courses' );
