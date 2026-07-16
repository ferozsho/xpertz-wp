<?php
/**
 * Premium XPERTZ learning platform homepage.
 *
 * @package EduPress
 */

get_header();

$user_counts    = count_users();
$course_count   = (int) ( wp_count_posts( 'lp_course' )->publish ?? 0 );
$learner_count  = array_sum( array_intersect_key( $user_counts['avail_roles'], array_flip( array( 'subscriber', 'customer', 'lp_student' ) ) ) );
$teacher_count  = (int) ( $user_counts['avail_roles']['lp_teacher'] ?? 0 );
$catalog_url    = get_post_type_archive_link( 'lp_course' ) ?: home_url( '/courses/' );
$categories_url = function_exists( 'xpertz_commerce_page_url' ) ? xpertz_commerce_page_url( 'categories' ) : home_url( '/categories/' );
?>

<main id="site-main" class="xhc-home">
	<section class="xhc-home-hero">
		<div class="xhc-container xhc-home-hero-grid">
			<div class="xhc-home-hero-copy">
				<span class="xhc-home-eyebrow"><?php echo xpertz_commerce_icon( 'graduation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Practical learning. Real momentum.', 'edupress' ); ?></span>
				<h1><?php esc_html_e( 'Build skills that move your', 'edupress' ); ?> <span><?php esc_html_e( 'career forward', 'edupress' ); ?></span></h1>
				<p><?php esc_html_e( 'Learn from focused, expert-led courses with clear progress tracking, secure enrollment, and lifetime access from any device.', 'edupress' ); ?></p>
				<div class="xhc-home-actions"><a class="xhc-home-primary" href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'Explore all courses', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a><a class="xhc-home-secondary" href="<?php echo esc_url( $categories_url ); ?>"><?php esc_html_e( 'Browse categories', 'edupress' ); ?></a></div>
				<ul class="xhc-home-trust"><li><?php echo xpertz_commerce_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Lifetime access', 'edupress' ); ?></li><li><?php echo xpertz_commerce_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Secure checkout', 'edupress' ); ?></li><li><?php echo xpertz_commerce_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Completion progress', 'edupress' ); ?></li></ul>
			</div>

			<aside class="xhc-home-hero-panel" aria-label="<?php esc_attr_e( 'Learning experience highlights', 'edupress' ); ?>">
				<div class="xhc-home-panel-top"><span><?php echo xpertz_commerce_icon( 'layers' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><small><?php esc_html_e( 'Your learning workspace', 'edupress' ); ?></small><strong><?php esc_html_e( 'Simple, focused, measurable', 'edupress' ); ?></strong></div></div>
				<div class="xhc-home-progress-card"><div><span><?php esc_html_e( 'Course progress', 'edupress' ); ?></span><strong>72%</strong></div><i><span></span></i><small><?php esc_html_e( 'Continue exactly where you left off', 'edupress' ); ?></small></div>
				<div class="xhc-home-panel-grid"><article><span><?php echo xpertz_commerce_icon( 'book' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><strong><?php esc_html_e( 'Structured lessons', 'edupress' ); ?></strong><small><?php esc_html_e( 'Clear next steps', 'edupress' ); ?></small></article><article><span><?php echo xpertz_commerce_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><strong><?php esc_html_e( 'Track progress', 'edupress' ); ?></strong><small><?php esc_html_e( 'Stay motivated', 'edupress' ); ?></small></article></div>
				<div class="xhc-home-panel-note"><?php echo xpertz_commerce_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Learn at your pace', 'edupress' ); ?></strong><small><?php esc_html_e( 'Desktop, tablet, and mobile ready', 'edupress' ); ?></small></span></div>
			</aside>
		</div>
	</section>

	<section class="xhc-home-stats" aria-label="<?php esc_attr_e( 'Platform statistics', 'edupress' ); ?>">
		<div class="xhc-container"><div><strong><?php echo esc_html( number_format_i18n( $course_count ) ); ?>+</strong><span><?php esc_html_e( 'Practical courses', 'edupress' ); ?></span></div><div><strong><?php echo esc_html( number_format_i18n( $learner_count ) ); ?>+</strong><span><?php esc_html_e( 'Registered learners', 'edupress' ); ?></span></div><div><strong><?php echo esc_html( number_format_i18n( max( 1, $teacher_count ) ) ); ?>+</strong><span><?php esc_html_e( 'Expert instructors', 'edupress' ); ?></span></div><div><strong>24/7</strong><span><?php esc_html_e( 'Course access', 'edupress' ); ?></span></div></div>
	</section>

	<section class="xhc-home-section xhc-featured-courses">
		<div class="xhc-container">
			<div class="xhc-section-heading"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Featured learning', 'edupress' ); ?></span><h2><?php esc_html_e( 'Start building your next skill', 'edupress' ); ?></h2><p><?php esc_html_e( 'Choose from focused courses built to be practical, approachable, and easy to continue.', 'edupress' ); ?></p></div><a href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'View all courses', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
			<div class="xhc-catalog-grid">
				<?php
				$featured_courses = new WP_Query(
					array(
						'post_type'           => 'lp_course',
						'post_status'         => 'publish',
						'posts_per_page'      => 6,
						'ignore_sticky_posts' => true,
						'no_found_rows'       => true,
					)
				);
				while ( $featured_courses->have_posts() ) {
					$featured_courses->the_post();
					xpertz_commerce_catalog_course_card( get_the_ID() );
				}
				wp_reset_postdata();
				?>
			</div>
		</div>
	</section>

	<section class="xhc-home-section xhc-home-benefits">
		<div class="xhc-container">
			<div class="xhc-section-heading is-centered"><div><span class="xhc-eyebrow"><?php esc_html_e( 'Designed around learners', 'edupress' ); ?></span><h2><?php esc_html_e( 'Everything you need to keep moving', 'edupress' ); ?></h2><p><?php esc_html_e( 'A cohesive learning experience from course discovery through completion.', 'edupress' ); ?></p></div></div>
			<div class="xhc-value-grid"><article><span><?php echo xpertz_commerce_icon( 'briefcase' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Career-focused content', 'edupress' ); ?></h3><p><?php esc_html_e( 'Practical course structures connect learning with real professional capability.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Flexible by design', 'edupress' ); ?></h3><p><?php esc_html_e( 'Study at your pace and return to your learning from any supported device.', 'edupress' ); ?></p></article><article><span><?php echo xpertz_commerce_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><h3><?php esc_html_e( 'Reliable enrollment', 'edupress' ); ?></h3><p><?php esc_html_e( 'Secure WooCommerce checkout provides immediate access after successful payment.', 'edupress' ); ?></p></article></div>
		</div>
	</section>

	<section class="xhc-home-cta"><div class="xhc-container"><div><span class="xhc-home-eyebrow"><?php echo xpertz_commerce_icon( 'graduation' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Your next skill starts here', 'edupress' ); ?></span><h2><?php esc_html_e( 'Turn learning into forward momentum.', 'edupress' ); ?></h2><p><?php esc_html_e( 'Explore the catalog and choose a course that matches your next professional goal.', 'edupress' ); ?></p></div><a class="xhc-home-primary" href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'Browse courses', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div></section>
</main>

<?php
get_footer();
