<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Authenticate as administrator (ID 1)
wp_set_current_user( 1 );
set_time_limit( 0 );

// Helper function to create order
function create_woocommerce_order_for_user( $user_id, $product_id, $date_str ) {
    if ( ! function_exists( 'wc_create_order' ) ) {
        return false;
    }
    
    $order = wc_create_order( array( 'customer_id' => $user_id ) );
    if ( is_wp_error( $order ) ) {
        return false;
    }
    
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return false;
    }
    
    $order->add_product( $product, 1 );
    
    $date = new WC_DateTime( $date_str );
    $order->set_date_created( $date );
    $order->set_date_completed( $date );
    $order->set_date_paid( $date );
    $order->set_status( 'completed' );
    
    $order_id = $order->save();
    
    // Update post dates directly in db to ensure correct date display in list tables
    global $wpdb;
    $wpdb->update(
        $wpdb->posts,
        array( 'post_date' => $date_str, 'post_date_gmt' => get_gmt_from_date( $date_str ) ),
        array( 'ID' => $order_id )
    );
    clean_post_cache( $order_id );
    
    return $order_id;
}

global $wpdb;

// 1. Delete previous mailinator users and their WooCommerce orders / LP data
echo "DELETING_PREVIOUS_MAILINATOR_USERS...\n";
$mailinator_users = get_users( array( 'search' => '*@mailinator.com', 'search_columns' => array( 'user_email' ), 'number' => -1 ) );
foreach ( $mailinator_users as $user ) {
    // Delete their LearnPress user items
    $wpdb->delete( $wpdb->prefix . 'learnpress_user_items', array( 'user_id' => $user->ID ) );
    
    // Delete their WooCommerce orders
    $orders = wc_get_orders( array( 'customer_id' => $user->ID, 'limit' => -1 ) );
    foreach ( $orders as $order ) {
        $order->delete( true ); // Force delete order
    }
    
    // Delete the user
    wp_delete_user( $user->ID );
}
echo "DELETED_PREVIOUS_USERS_AND_DATA\n";

// 2. Prepare user list
$users_to_create = array();

// 48 Students
for ( $i = 1; $i <= 48; $i++ ) {
    $users_to_create[] = array(
        'username' => 'student' . $i,
        'email'    => 'student' . $i . '@mailinator.com',
        'role'     => 'subscriber'
    );
}

// 5 Teachers
for ( $i = 1; $i <= 5; $i++ ) {
    $users_to_create[] = array(
        'username' => 'teacher' . $i,
        'email'    => 'teacher' . $i . '@mailinator.com',
        'role'     => 'lp_teacher'
    );
}

// 2 Managers
for ( $i = 1; $i <= 2; $i++ ) {
    $users_to_create[] = array(
        'username' => 'manager' . $i,
        'email'    => 'manager' . $i . '@mailinator.com',
        'role'     => 'shop_manager'
    );
}

// 1 Admin
$users_to_create[] = array(
    'username' => 'admin1',
    'email'    => 'admin1@mailinator.com',
    'role'     => 'administrator'
);

// 1 Super Admin
$users_to_create[] = array(
    'username' => 'superadmin1',
    'email'    => 'superadmin1@mailinator.com',
    'role'     => 'administrator'
);

// 3. Create users
$students_ids = array();
foreach ( $users_to_create as $u ) {
    $user_id = wp_insert_user( array(
        'user_login'   => $u['username'],
        'user_pass'    => 'password123',
        'user_email'   => $u['email'],
        'role'         => $u['role'],
        'display_name' => ucfirst( $u['username'] )
    ) );
    
    if ( is_wp_error( $user_id ) ) {
        echo "Error: Failed to create " . $u['username'] . " - " . $user_id->get_error_message() . "\n";
        continue;
    }
    
    if ( $u['role'] === 'subscriber' ) {
        $students_ids[] = $user_id;
    }
}
echo "CREATED_USERS_COUNT: " . count($users_to_create) . "\n";

// 4. Retrieve available LearnPress courses
$courses = get_posts( array( 'post_type' => 'lp_course', 'posts_per_page' => -1 ) );
if ( empty( $courses ) ) {
    echo "ERROR: No LearnPress courses found!\n";
    exit;
}

// 5. Populate student data with 6 months of timeline history
echo "POPULATING_STUDENT_DATES_DATA...\n";
foreach ( $students_ids as $index => $user_id ) {
    // Select 2 to 5 random courses for this student
    $num_courses = rand( 2, 5 );
    $shuffled_courses = $courses;
    shuffle( $shuffled_courses );
    $selected_courses = array_slice( $shuffled_courses, 0, $num_courses );
    
    foreach ( $selected_courses as $course ) {
        $course_id = $course->ID;
        $product_id = get_post_meta( $course_id, '_related_woocommerce_product_id', true );
        
        if ( ! $product_id ) {
            continue; // Skip if no related WooCommerce product
        }
        
        // Pick a purchase date in the last 6 months (180 days)
        $order_timestamp = time() - rand( 1, 180 * 24 * 3600 );
        $order_date_str = date( 'Y-m-d H:i:s', $order_timestamp );
        
        // Create the completed WooCommerce order
        create_woocommerce_order_for_user( $user_id, $product_id, $order_date_str );
        
        // Determine course completion status
        $course_finished = ( rand( 1, 100 ) <= 30 ); // 30% chance to have finished the course
        $graduation = 'in-progress';
        $status = 'enrolled';
        $course_end_time = null;
        
        if ( $course_finished ) {
            $status = 'finished';
            $graduation = ( rand( 1, 100 ) <= 75 ) ? 'passed' : 'failed'; // 75% pass rate
            $completion_timestamp = $order_timestamp + rand( 5 * 24 * 3600, 30 * 24 * 3600 );
            if ( $completion_timestamp > time() ) {
                $completion_timestamp = time();
            }
            $course_end_time = date( 'Y-m-d H:i:s', $completion_timestamp );
        }
        
        // Insert Course record in wp_learnpress_user_items
        $wpdb->insert(
            $wpdb->prefix . 'learnpress_user_items',
            array(
                'user_id'      => $user_id,
                'item_id'      => $course_id,
                'start_time'   => $order_date_str,
                'end_time'     => $course_end_time,
                'item_type'    => 'lp_course',
                'status'       => $status,
                'graduation'   => $graduation,
                'access_level' => 50,
                'ref_id'       => 0,
                'ref_type'     => '',
                'parent_id'    => 0
            )
        );
        $course_user_item_id = $wpdb->insert_id;
        
        // Get all section items (lessons, quizzes) in this course
        $sections = $wpdb->get_col( $wpdb->prepare( "SELECT section_id FROM {$wpdb->prefix}learnpress_sections WHERE section_course_id = %d", $course_id ) );
        if ( ! empty( $sections ) ) {
            $section_items = $wpdb->get_results( "SELECT item_id, item_type FROM {$wpdb->prefix}learnpress_section_items WHERE section_id IN (" . implode( ',', array_map( 'intval', $sections ) ) . ") ORDER BY item_order ASC" );
            
            if ( ! empty( $section_items ) ) {
                $total_items = count( $section_items );
                
                // Decide how many items they completed
                if ( $course_finished ) {
                    $completed_count = $total_items;
                } else {
                    $completed_count = rand( 0, $total_items - 1 ); // Completed a portion of the course
                }
                
                $current_time = $order_timestamp;
                
                for ( $j = 0; $j < $total_items; $j++ ) {
                    $item_id = $section_items[$j]->item_id;
                    $item_type = $section_items[$j]->item_type;
                    
                    $is_completed = ( $j < $completed_count );
                    $is_started = ( $j === $completed_count && ! $course_finished ); // Active item
                    
                    if ( $is_completed || $is_started ) {
                        // Lesson or Quiz activity occurs after previous item, spread out by hours/days
                        $current_time += rand( 3600, 48 * 3600 ); // 1 hour to 2 days later
                        if ( $current_time > time() ) {
                            $current_time = time();
                        }
                        
                        $item_start_time_str = date( 'Y-m-d H:i:s', $current_time );
                        $item_end_time_str = null;
                        
                        if ( $is_completed ) {
                            $duration = rand( 300, 3600 ); // 5 to 60 minutes duration
                            $item_end_time_str = date( 'Y-m-d H:i:s', $current_time + $duration );
                        }
                        
                        if ( $item_type === 'lp_lesson' ) {
                            $wpdb->insert(
                                $wpdb->prefix . 'learnpress_user_items',
                                array(
                                    'user_id'      => $user_id,
                                    'item_id'      => $item_id,
                                    'start_time'   => $item_start_time_str,
                                    'end_time'     => $item_end_time_str,
                                    'item_type'    => 'lp_lesson',
                                    'status'       => $is_completed ? 'completed' : 'started',
                                    'graduation'   => null,
                                    'access_level' => 50,
                                    'ref_id'       => $course_id,
                                    'ref_type'     => 'lp_course',
                                    'parent_id'    => $course_user_item_id
                                )
                            );
                        } elseif ( $item_type === 'lp_quiz' ) {
                            $quiz_graduation = null;
                            if ( $is_completed ) {
                                $quiz_graduation = ( rand( 1, 100 ) <= 80 ) ? 'passed' : 'failed'; // 80% pass rate on quiz
                            }
                            
                            $wpdb->insert(
                                $wpdb->prefix . 'learnpress_user_items',
                                array(
                                    'user_id'      => $user_id,
                                    'item_id'      => $item_id,
                                    'start_time'   => $item_start_time_str,
                                    'end_time'     => $item_end_time_str,
                                    'item_type'    => 'lp_quiz',
                                    'status'       => $is_completed ? 'completed' : 'started',
                                    'graduation'   => $quiz_graduation,
                                    'access_level' => 50,
                                    'ref_id'       => $course_id,
                                    'ref_type'     => 'lp_course',
                                    'parent_id'    => $course_user_item_id
                                )
                            );
                        }
                    }
                }
            }
        }
    }
}
echo "COMPLETED_SUCCESSFULLY\n";
