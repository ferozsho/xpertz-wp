<?php
/**
 * Premium XPERTZ single course layout.
 *
 * This template overrides LearnPress's modern single course layout while
 * delegating course state, actions, pricing, and curriculum permissions to
 * LearnPress models and template hooks.
 *
 * @package EduPress
 * @version 4.2.7.6
 */

use LearnPress\Models\CourseModel;

defined( 'ABSPATH' ) || exit;

if ( ! wp_is_block_theme() ) {
	get_header( 'course' );
}

$course_id = get_the_ID();

if ( $course_id ) {
	if ( post_password_required() ) {
		?>
		<div class="xpc-password-form"><?php echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
		<?php
	} else {
		$course = CourseModel::find( $course_id, true );
		if ( $course instanceof CourseModel ) {
			do_action( 'learn-press/before-single-course' );
			?>
			<div class="xpc-global-message xpc-container"><?php learn_press_show_message(); ?></div>
			<?php
			xpertz_render_course_page( $course );
			do_action( 'learn-press/after-single-course' );
		}
	}
}

if ( ! wp_is_block_theme() ) {
	get_footer( 'course' );
}
