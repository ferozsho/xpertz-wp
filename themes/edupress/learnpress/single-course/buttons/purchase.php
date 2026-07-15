<?php
/**
 * Template for displaying Purchase button in single course (Overridden for WooCommerce integration).
 */

defined( 'ABSPATH' ) || exit();

if ( ! isset( $course ) ) {
$course = learn_press_get_course();
}

$course_id = $course->get_id();
$product_id = get_post_meta( $course_id, '_related_woocommerce_product_id', true );

if ( $product_id ) {
    // Redirect buy button directly to WooCommerce cart/checkout
    $checkout_url = wc_get_checkout_url() . '?add-to-cart=' . $product_id;
    ?>
    <div class="lp-woocommerce-purchase-wrapper">
        <a href="<?php echo esc_url( $checkout_url ); ?>" class="lp-button button button-purchase-course">
            <?php esc_html_e( 'Buy Now (via WooCommerce)', 'learnpress' ); ?>
        </a>
    </div>
    <?php
} else {
    // Default LearnPress purchase form fallback
    $classes_purchase  = 'purchase-course';
    $classes_purchase .= ( LearnPress::instance()->checkout()->is_enable_guest_checkout() ) ? ' guest_checkout' : '';
    $classes_purchase = apply_filters( 'lp/btn/purchase/classes', $classes_purchase );
    ?>
    <?php do_action( 'learn-press/before-purchase-form' ); ?>
    <form name="purchase-course" class="<?php echo esc_attr( $classes_purchase ); ?>" method="post" enctype="multipart/form-data">
        <?php do_action( 'learn-press/before-purchase-button' ); ?>
        <input type="hidden" name="purchase-course" value="<?php echo esc_attr( $course->get_id() ); ?>"/>
        <button class="lp-button button button-purchase-course">
            <?php echo esc_html( apply_filters( 'learn-press/purchase-course-button-text', esc_html__( 'Buy Now', 'learnpress' ), $course->get_id() ) ); ?>
        </button>
        <?php do_action( 'learn-press/after-purchase-button' ); ?>
    </form>
    <?php do_action( 'learn-press/after-purchase-form' ); ?>
    <?php
}
