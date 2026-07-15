<?php
/**
 * Plugin Name: Xpertz WooCommerce LearnPress Integration
 * Description: Integrates WooCommerce with LearnPress by automatically enrolling users in courses when they purchase the corresponding WooCommerce product.
 * Version: 1.0
 */

defined( 'ABSPATH' ) || exit();

// Hook into WooCommerce order status change to Completed
add_action( 'woocommerce_order_status_completed', 'xpertz_enroll_user_in_course_on_completed_order', 10, 1 );

function xpertz_enroll_user_in_course_on_completed_order( $order_id ) {
    $order = wc_get_order( $order_id );
    if ( ! $order ) {
        return;
    }
    
    $user_id = $order->get_user_id();
    if ( ! $user_id ) {
        return; // No user linked to this order
    }
    
    // Loop through order items
    foreach ( $order->get_items() as $item ) {
        $product_id = $item->get_product_id();
        
        // Get the related LearnPress course ID from the product meta
        $course_id = get_post_meta( $product_id, '_related_course_id', true );
        
        if ( $course_id ) {
            // Enroll the user in the course
            // 1. Delete old user items if any
            if ( class_exists( 'LP_User_Items_DB' ) ) {
                LP_User_Items_DB::getInstance()->delete_user_items_old( $user_id, $course_id );
            }
            
            // 2. Insert new user course enrollment
            if ( class_exists( 'LearnPress\Models\UserItems\UserCourseModel' ) ) {
                $user_course_new             = new LearnPress\Models\UserItems\UserCourseModel();
                $user_course_new->user_id    = $user_id;
                $user_course_new->item_id    = $course_id;
                $user_course_new->item_type  = LP_COURSE_CPT;
                $user_course_new->ref_type   = '';
                $user_course_new->status     = LP_COURSE_ENROLLED;
                $user_course_new->graduation = LP_COURSE_GRADUATION_IN_PROGRESS;
                $user_course_new->start_time = gmdate( 'Y-m-d H:i:s', time() );
                $user_course_new->save();
            }
        }
    }
}
