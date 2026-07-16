<?php
/**
 * Course purchase controls backed by the mapped WooCommerce product.
 *
 * @package EduPress
 */

defined( 'ABSPATH' ) || exit;

if ( ! isset( $course ) ) {
	$course = learn_press_get_course();
}

if ( ! $course ) {
	return;
}

$course_id   = $course->get_id();
$product_id  = function_exists( 'xpertz_wc_get_product_id_for_course' )
	? xpertz_wc_get_product_id_for_course( $course_id )
	: absint( get_post_meta( $course_id, '_related_woocommerce_product_id', true ) );
$product     = $product_id && function_exists( 'wc_get_product' ) ? wc_get_product( $product_id ) : false;
$owns_course = function_exists( 'xpertz_wc_user_owns_course' ) && xpertz_wc_user_owns_course( $course_id );

if ( $owns_course ) :
	?>
	<div class="lp-woocommerce-purchase-wrapper is-owned">
		<a href="<?php echo esc_url( get_permalink( $course_id ) . '#curriculum' ); ?>" class="lp-button button button-purchase-course">
			<?php esc_html_e( 'Continue learning', 'edupress' ); ?>
		</a>
	</div>
	<?php
elseif ( $product && $product->is_purchasable() && $product->is_in_stock() ) :
	?>
	<div class="lp-woocommerce-purchase-wrapper" data-course-product="<?php echo esc_attr( $product_id ); ?>">
		<button type="button" class="lp-button button button-purchase-course" data-add-course-to-cart="<?php echo esc_attr( $product_id ); ?>">
			<?php esc_html_e( 'Add course to cart', 'edupress' ); ?>
		</button>
		<button type="button" class="lp-button button xpc-buy-now-button" data-buy-course-now="<?php echo esc_attr( $product_id ); ?>">
			<?php esc_html_e( 'Buy now', 'edupress' ); ?>
		</button>
	</div>
	<?php
else :
	$classes_purchase  = 'purchase-course';
	$classes_purchase .= LearnPress::instance()->checkout()->is_enable_guest_checkout() ? ' guest_checkout' : '';
	$classes_purchase  = apply_filters( 'lp/btn/purchase/classes', $classes_purchase );
	?>
	<?php do_action( 'learn-press/before-purchase-form' ); ?>
	<form name="purchase-course" class="<?php echo esc_attr( $classes_purchase ); ?>" method="post" enctype="multipart/form-data">
		<?php do_action( 'learn-press/before-purchase-button' ); ?>
		<input type="hidden" name="purchase-course" value="<?php echo esc_attr( $course_id ); ?>">
		<button class="lp-button button button-purchase-course" type="submit">
			<?php echo esc_html( apply_filters( 'learn-press/purchase-course-button-text', esc_html__( 'Enroll now', 'learnpress' ), $course_id ) ); ?>
		</button>
		<?php do_action( 'learn-press/after-purchase-button' ); ?>
	</form>
	<?php do_action( 'learn-press/after-purchase-form' ); ?>
	<?php
endif;
