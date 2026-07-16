<?php
/**
 * XPERTZ LearnPress course page components and integrations.
 *
 * @package EduPress
 */

defined( 'ABSPATH' ) || exit;

/**
 * LearnPress 4 disables theme template overrides unless a theme opts in.
 */
add_filter( 'learn-press/override-templates', '__return_true' );

/**
 * Enqueue the course page assets only where they are needed.
 */
function xpertz_course_enqueue_assets() {
	if ( ! is_singular( 'lp_course' ) ) {
		return;
	}

	$css_path = get_template_directory() . '/assets/css/xpertz-course.min.css';
	$js_path  = get_template_directory() . '/assets/js/xpertz-course.js';

	wp_enqueue_style(
		'xpertz-course',
		get_template_directory_uri() . '/assets/css/xpertz-course.min.css',
		array( 'edupress-custom-design' ),
		file_exists( $css_path ) ? (string) filemtime( $css_path ) : ILOVEWP_VERSION
	);

	wp_enqueue_script(
		'xpertz-course',
		get_template_directory_uri() . '/assets/js/xpertz-course.js',
		array(),
		file_exists( $js_path ) ? (string) filemtime( $js_path ) : ILOVEWP_VERSION,
		true
	);

	wp_localize_script(
		'xpertz-course',
		'xpertzCourse',
		array(
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'reviewNonce'   => wp_create_nonce( 'xpertz_course_review' ),
			'copiedLabel'   => esc_html__( 'Link copied', 'edupress' ),
			'savedLabel'    => esc_html__( 'Saved', 'edupress' ),
			'reportedLabel' => esc_html__( 'Reported', 'edupress' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'xpertz_course_enqueue_assets', 20 );

/**
 * Return a small inline Lucide-style SVG icon.
 *
 * @param string $name  Icon name.
 * @param string $class Optional class name.
 * @return string
 */
function xpertz_course_icon( $name, $class = '' ) {
	$icons = array(
		'arrow-right'   => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
		'award'         => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
		'bar-chart'     => '<path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/>',
		'book-open'     => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
		'calendar'      => '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/>',
		'check'         => '<path d="m20 6-11 11-5-5"/>',
		'check-circle'  => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'clock'         => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
		'download'      => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/>',
		'file-text'     => '<path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5z"/><polyline points="14 2 14 8 20 8"/><line x1="8" x2="16" y1="13" y2="13"/><line x1="8" x2="16" y1="17" y2="17"/>',
		'flag'          => '<path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/>',
		'globe'         => '<circle cx="12" cy="12" r="10"/><line x1="2" x2="22" y1="12" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
		'heart'         => '<path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78z"/>',
		'infinity'      => '<path d="M18.178 8C19.426 8 21 9.258 21 12s-1.574 4-2.822 4C14.954 16 12.013 8 8.822 8 7.573 8 6 9.258 6 12s1.573 4 2.822 4C12.013 16 14.954 8 18.178 8Z"/>',
		'laptop'        => '<rect width="18" height="12" x="3" y="4" rx="2"/><line x1="2" x2="22" y1="20" y2="20"/>',
		'lock'          => '<rect width="18" height="11" x="3" y="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>',
		'mail'          => '<rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/>',
		'message'       => '<path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>',
		'play'          => '<circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/>',
		'quiz'          => '<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 1 1 5.83 1c0 2-3 2-3 4"/><path d="M12 18h.01"/>',
		'search'        => '<circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>',
		'share'         => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/>',
		'shield-check'  => '<path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3z"/><path d="m9 12 2 2 4-4"/>',
		'smartphone'    => '<rect width="14" height="20" x="5" y="2" rx="2" ry="2"/><line x1="12" x2="12.01" y1="18" y2="18"/>',
		'star'          => '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>',
		'tag'           => '<path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/>',
		'users'         => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
	);

	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="xpc-icon %1$s" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>',
		esc_attr( $class ),
		$icons[ $name ]
	);
}

/**
 * Normalize LearnPress's stored course level for display.
 *
 * @param string $level Stored level.
 * @return string
 */
function xpertz_course_level_label( $level ) {
	$levels = array(
		'all'          => esc_html__( 'All levels', 'edupress' ),
		'beginner'     => esc_html__( 'Beginner', 'edupress' ),
		'intermediate' => esc_html__( 'Intermediate', 'edupress' ),
		'expert'       => esc_html__( 'Advanced', 'edupress' ),
	);

	return $levels[ $level ] ?? ( $level ? ucwords( str_replace( array( '-', '_' ), ' ', $level ) ) : esc_html__( 'All levels', 'edupress' ) );
}

/**
 * Calculate rating data from approved course comments.
 *
 * @param int $course_id Course ID.
 * @return array{average: float, total: int, breakdown: array<int, int>}
 */
function xpertz_course_rating_data( $course_id ) {
	$comments  = get_comments(
		array(
			'post_id' => $course_id,
			'status'  => 'approve',
			'type'    => 'comment',
		)
	);
	$total     = 0;
	$sum       = 0;
	$breakdown = array_fill( 1, 5, 0 );

	foreach ( $comments as $comment ) {
		$rating = (int) get_comment_meta( $comment->comment_ID, 'xpertz_course_rating', true );
		if ( 0 === $rating ) {
			$rating = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
		}
		if ( $rating < 1 || $rating > 5 ) {
			continue;
		}

		++$total;
		$sum += $rating;
		++$breakdown[ $rating ];
	}

	return array(
		'average'   => $total ? round( $sum / $total, 1 ) : 0.0,
		'total'     => $total,
		'breakdown' => $breakdown,
	);
}

/**
 * Render five accessible stars.
 *
 * @param float $rating Rating value.
 * @return string
 */
function xpertz_course_stars( $rating ) {
	$rounded = (int) round( $rating );
	$html    = '<span class="xpc-stars" aria-hidden="true">';

	for ( $index = 1; $index <= 5; $index++ ) {
		$html .= '<span class="xpc-star' . ( $index <= $rounded ? ' is-filled' : '' ) . '">' . xpertz_course_icon( 'star' ) . '</span>';
	}

	return $html . '</span>';
}

/**
 * Return a performant responsive course image or an intentional fallback.
 *
 * @param int    $course_id Course ID.
 * @param string $class     CSS class.
 * @param bool   $priority  Whether this is the page's priority image.
 * @return string
 */
function xpertz_course_image( $course_id, $class = '', $priority = false ) {
	$thumbnail_id = get_post_thumbnail_id( $course_id );
	if ( $thumbnail_id ) {
		return wp_get_attachment_image(
			$thumbnail_id,
			'large',
			false,
			array(
				'alt'           => get_the_title( $course_id ),
				'class'         => $class,
				'loading'       => $priority ? 'eager' : 'lazy',
				'fetchpriority' => $priority ? 'high' : 'auto',
				'decoding'      => 'async',
				'sizes'         => '(max-width: 767px) 100vw, (max-width: 1100px) 46vw, 560px',
			)
		);
	}

	return '<span class="xpc-image-fallback ' . esc_attr( $class ) . '">' . xpertz_course_icon( 'book-open' ) . '</span>';
}

/**
 * Render a compact metadata item.
 *
 * @param string $icon  Icon name.
 * @param string $value Main value.
 * @param string $label Accessible label.
 * @return string
 */
function xpertz_course_meta_item( $icon, $value, $label ) {
	return sprintf(
		'<span class="xpc-meta-item" aria-label="%1$s">%2$s<span>%3$s</span></span>',
		esc_attr( $label . ': ' . wp_strip_all_tags( $value ) ),
		xpertz_course_icon( $icon ),
		esc_html( $value )
	);
}

/**
 * Render an overview list card when content exists.
 *
 * @param string $id      Element ID.
 * @param string $icon    Icon name.
 * @param string $eyebrow Eyebrow label.
 * @param string $title   Card heading.
 * @param array  $items   List items.
 */
function xpertz_course_list_card( $id, $icon, $eyebrow, $title, $items ) {
	$items = array_values( array_filter( (array) $items ) );
	if ( empty( $items ) ) {
		return;
	}
	?>
	<section class="xpc-card xpc-list-card"<?php echo $id ? ' id="' . esc_attr( $id ) . '"' : ''; ?>>
		<div class="xpc-section-heading xpc-section-heading--compact">
			<span class="xpc-section-icon"><?php echo xpertz_course_icon( $icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div>
				<span class="xpc-eyebrow"><?php echo esc_html( $eyebrow ); ?></span>
				<h2><?php echo esc_html( $title ); ?></h2>
			</div>
		</div>
		<ul class="xpc-check-list">
			<?php foreach ( $items as $item ) : ?>
				<li><?php echo xpertz_course_icon( 'check-circle' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo wp_kses_post( $item ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</section>
	<?php
}

/**
 * Determine whether a reviewer is an enrolled learner.
 *
 * @param int $user_id   User ID.
 * @param int $course_id Course ID.
 * @return bool
 */
function xpertz_course_is_verified_reviewer( $user_id, $course_id ) {
	if ( ! $user_id || ! class_exists( '\LearnPress\Models\UserItems\UserCourseModel' ) ) {
		return false;
	}

	$user_course = \LearnPress\Models\UserItems\UserCourseModel::find( $user_id, $course_id, true );
	return $user_course && ( $user_course->has_enrolled_or_finished() || $user_course->has_purchased() );
}

/**
 * Render course reviews and the rating form.
 *
 * @param int   $course_id Course ID.
 * @param array $rating    Rating data.
 */
function xpertz_course_render_reviews( $course_id, $rating ) {
	$comments = get_comments(
		array(
			'post_id' => $course_id,
			'status'  => 'approve',
			'type'    => 'comment',
			'order'   => 'DESC',
		)
	);
	?>
	<section id="reviews" class="xpc-card xpc-section xpc-reviews" aria-labelledby="xpc-reviews-title">
		<div class="xpc-section-heading">
			<span class="xpc-section-icon"><?php echo xpertz_course_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div>
				<span class="xpc-eyebrow"><?php esc_html_e( 'Learner feedback', 'edupress' ); ?></span>
				<h2 id="xpc-reviews-title"><?php esc_html_e( 'Reviews', 'edupress' ); ?></h2>
			</div>
		</div>

		<div class="xpc-rating-summary">
			<div class="xpc-rating-score">
				<strong><?php echo $rating['total'] ? esc_html( number_format_i18n( $rating['average'], 1 ) ) : esc_html__( 'New', 'edupress' ); ?></strong>
				<?php echo xpertz_course_stars( $rating['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span><?php echo esc_html( sprintf( _n( '%s rated review', '%s rated reviews', $rating['total'], 'edupress' ), number_format_i18n( $rating['total'] ) ) ); ?></span>
			</div>
			<div class="xpc-rating-bars">
				<?php for ( $stars = 5; $stars >= 1; $stars-- ) : ?>
					<?php $percentage = $rating['total'] ? round( ( $rating['breakdown'][ $stars ] / $rating['total'] ) * 100 ) : 0; ?>
					<div class="xpc-rating-row">
						<span><?php echo esc_html( $stars ); ?> <?php echo xpertz_course_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<div class="xpc-rating-track" role="progressbar" aria-label="<?php echo esc_attr( sprintf( __( '%d star reviews', 'edupress' ), $stars ) ); ?>" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr( $percentage ); ?>"><span style="--xpc-rating-width: <?php echo esc_attr( $percentage ); ?>%"></span></div>
						<small><?php echo esc_html( $percentage ); ?>%</small>
					</div>
				<?php endfor; ?>
			</div>
		</div>

		<div class="xpc-review-list" aria-live="polite">
			<?php if ( $comments ) : ?>
				<?php foreach ( $comments as $comment ) : ?>
					<?php
					$comment_rating = (int) get_comment_meta( $comment->comment_ID, 'xpertz_course_rating', true );
					if ( ! $comment_rating ) {
						$comment_rating = (int) get_comment_meta( $comment->comment_ID, 'rating', true );
					}
					?>
					<article class="xpc-review" id="review-<?php echo esc_attr( $comment->comment_ID ); ?>">
						<div class="xpc-review-avatar" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $comment->comment_author, 0, 1 ) ) ); ?></div>
						<div class="xpc-review-body">
							<header>
								<div>
									<strong><?php echo esc_html( $comment->comment_author ); ?></strong>
									<?php if ( xpertz_course_is_verified_reviewer( (int) $comment->user_id, $course_id ) ) : ?>
										<span class="xpc-verified"><?php echo xpertz_course_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Verified learner', 'edupress' ); ?></span>
									<?php endif; ?>
								</div>
								<time datetime="<?php echo esc_attr( get_comment_date( DATE_W3C, $comment ) ); ?>"><?php echo esc_html( human_time_diff( get_comment_time( 'U', true, $comment ), current_time( 'timestamp', true ) ) . ' ' . __( 'ago', 'edupress' ) ); ?></time>
							</header>
							<?php if ( $comment_rating ) : ?>
								<span class="screen-reader-text"><?php echo esc_html( sprintf( __( '%d out of 5 stars', 'edupress' ), $comment_rating ) ); ?></span>
								<?php echo xpertz_course_stars( $comment_rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php endif; ?>
							<div class="xpc-review-content"><?php echo wp_kses_post( wpautop( $comment->comment_content ) ); ?></div>
							<div class="xpc-review-actions">
								<button type="button" class="xpc-text-button" data-review-action="helpful" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"><?php echo xpertz_course_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Helpful', 'edupress' ); ?> <span><?php echo esc_html( (int) get_comment_meta( $comment->comment_ID, 'xpertz_helpful_count', true ) ); ?></span></button>
								<button type="button" class="xpc-text-button" data-review-action="report" data-comment-id="<?php echo esc_attr( $comment->comment_ID ); ?>"><?php echo xpertz_course_icon( 'flag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Report', 'edupress' ); ?></button>
							</div>
						</div>
					</article>
				<?php endforeach; ?>
			<?php else : ?>
				<div class="xpc-empty-state">
					<?php echo xpertz_course_icon( 'message' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<h3><?php esc_html_e( 'Be the first to share your experience', 'edupress' ); ?></h3>
					<p><?php esc_html_e( 'Your feedback helps future learners choose with confidence.', 'edupress' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( comments_open( $course_id ) ) : ?>
			<div class="xpc-review-form">
				<?php
				comment_form(
					array(
						'class_submit'         => 'xpc-button xpc-button--primary',
						'label_submit'         => esc_html__( 'Submit review', 'edupress' ),
						'title_reply'          => esc_html__( 'Leave a review', 'edupress' ),
						'title_reply_before'   => '<h3 id="reply-title" class="comment-reply-title">',
						'title_reply_after'    => '</h3>',
						'comment_notes_before' => '<p class="xpc-form-note">' . esc_html__( 'Share an honest, constructive review of this course.', 'edupress' ) . '</p>' . wp_nonce_field( 'xpertz_course_rating', 'xpertz_course_rating_nonce', true, false ),
						'comment_field'        => '<p class="comment-form-rating"><label for="xpertz-rating">' . esc_html__( 'Your rating', 'edupress' ) . ' <span class="required">*</span></label><select id="xpertz-rating" name="xpertz_course_rating" required><option value="">' . esc_html__( 'Choose a rating', 'edupress' ) . '</option><option value="5">' . esc_html__( '5 — Excellent', 'edupress' ) . '</option><option value="4">' . esc_html__( '4 — Very good', 'edupress' ) . '</option><option value="3">' . esc_html__( '3 — Good', 'edupress' ) . '</option><option value="2">' . esc_html__( '2 — Fair', 'edupress' ) . '</option><option value="1">' . esc_html__( '1 — Needs improvement', 'edupress' ) . '</option></select></p><p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'edupress' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="4000" required></textarea></p>',
					),
					$course_id
				);
				?>
			</div>
		<?php endif; ?>
	</section>
	<?php
}

/**
 * Save a submitted course rating alongside its comment.
 *
 * @param int $comment_id Comment ID.
 */
function xpertz_course_save_rating( $comment_id ) {
	$comment = get_comment( $comment_id );
	if ( ! $comment || 'lp_course' !== get_post_type( $comment->comment_post_ID ) ) {
		return;
	}

	$nonce  = isset( $_POST['xpertz_course_rating_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['xpertz_course_rating_nonce'] ) ) : '';
	$rating = isset( $_POST['xpertz_course_rating'] ) ? absint( $_POST['xpertz_course_rating'] ) : 0;
	if ( ! wp_verify_nonce( $nonce, 'xpertz_course_rating' ) || $rating < 1 || $rating > 5 ) {
		return;
	}

	update_comment_meta( $comment_id, 'xpertz_course_rating', $rating );
}
add_action( 'comment_post', 'xpertz_course_save_rating' );

/**
 * Store helpful/report feedback for an approved course review.
 */
function xpertz_course_review_action() {
	check_ajax_referer( 'xpertz_course_review', 'nonce' );

	$comment_id = isset( $_POST['commentId'] ) ? absint( $_POST['commentId'] ) : 0;
	$mode       = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';
	$comment    = get_comment( $comment_id );

	if ( ! $comment || '1' !== (string) $comment->comment_approved || 'lp_course' !== get_post_type( $comment->comment_post_ID ) ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Review not found.', 'edupress' ) ), 404 );
	}

	$meta_key = 'helpful' === $mode ? 'xpertz_helpful_count' : ( 'report' === $mode ? 'xpertz_report_count' : '' );
	if ( ! $meta_key ) {
		wp_send_json_error( array( 'message' => esc_html__( 'Invalid review action.', 'edupress' ) ), 400 );
	}

	$remote_address = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
	$actor          = get_current_user_id() ? 'user-' . get_current_user_id() : 'ip-' . $remote_address;
	$rate_key       = 'xpc_review_' . md5( $comment_id . '|' . $mode . '|' . $actor );
	if ( get_transient( $rate_key ) ) {
		wp_send_json_success( array( 'count' => (int) get_comment_meta( $comment_id, $meta_key, true ) ) );
	}

	$count = (int) get_comment_meta( $comment_id, $meta_key, true ) + 1;
	update_comment_meta( $comment_id, $meta_key, $count );
	set_transient( $rate_key, 1, 6 * HOUR_IN_SECONDS );

	wp_send_json_success( array( 'count' => $count ) );
}
add_action( 'wp_ajax_xpertz_course_review_action', 'xpertz_course_review_action' );
add_action( 'wp_ajax_nopriv_xpertz_course_review_action', 'xpertz_course_review_action' );

/**
 * Render the instructor profile with safe fallbacks for imported courses.
 *
 * @param \LearnPress\Models\CourseModel $course Course model.
 * @param array                           $rating Rating data.
 */
function xpertz_course_render_instructor( $course, $rating ) {
	$course_id    = $course->get_id();
	$author_id    = (int) get_post_field( 'post_author', $course_id );
	$author       = $author_id ? get_userdata( $author_id ) : false;
	$name         = $author ? $author->display_name : esc_html__( 'XPERTZ Faculty', 'edupress' );
	$title        = $author_id ? get_user_meta( $author_id, 'job_title', true ) : '';
	$title        = $title ?: esc_html__( 'Industry Learning Team', 'edupress' );
	$description  = $author ? get_the_author_meta( 'description', $author_id ) : '';
	$description  = $description ?: esc_html__( 'Learn with practical, outcome-focused guidance designed by the XPERTZ education team.', 'edupress' );
	$course_count = $author_id ? count_user_posts( $author_id, 'lp_course', true ) : 1;
	$initials     = '';
	foreach ( array_slice( preg_split( '/\s+/', trim( $name ) ), 0, 2 ) as $word ) {
		$initials .= strtoupper( substr( $word, 0, 1 ) );
	}
	?>
	<section id="instructor" class="xpc-card xpc-section xpc-instructor" aria-labelledby="xpc-instructor-title">
		<div class="xpc-section-heading">
			<span class="xpc-section-icon"><?php echo xpertz_course_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<div>
				<span class="xpc-eyebrow"><?php esc_html_e( 'Learn from experience', 'edupress' ); ?></span>
				<h2 id="xpc-instructor-title"><?php esc_html_e( 'Your instructor', 'edupress' ); ?></h2>
			</div>
		</div>
		<div class="xpc-instructor-profile">
			<div class="xpc-instructor-avatar">
				<?php if ( $author_id ) : ?>
					<?php echo get_avatar( $author_id, 160, '', $name, array( 'loading' => 'lazy' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php else : ?>
					<span aria-hidden="true"><?php echo esc_html( $initials ?: 'XF' ); ?></span>
				<?php endif; ?>
			</div>
			<div class="xpc-instructor-main">
				<span class="xpc-eyebrow"><?php echo esc_html( $title ); ?></span>
				<h3><?php echo esc_html( $name ); ?></h3>
				<div class="xpc-instructor-stats">
					<span><?php echo xpertz_course_icon( 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php echo esc_html( number_format_i18n( max( 1, $course_count ) ) ); ?></strong><?php esc_html_e( 'Courses', 'edupress' ); ?></span>
					<span><?php echo xpertz_course_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php echo esc_html( number_format_i18n( $course->count_students() ) ); ?></strong><?php esc_html_e( 'Learners', 'edupress' ); ?></span>
					<span><?php echo xpertz_course_icon( 'star' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><strong><?php echo $rating['total'] ? esc_html( number_format_i18n( $rating['average'], 1 ) ) : '—'; ?></strong><?php esc_html_e( 'Rating', 'edupress' ); ?></span>
				</div>
				<div class="xpc-instructor-bio"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
				<div class="xpc-instructor-links">
					<?php if ( $author && $author->user_url ) : ?>
						<a href="<?php echo esc_url( $author->user_url ); ?>" target="_blank" rel="noopener noreferrer"><?php echo xpertz_course_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Website', 'edupress' ); ?></a>
					<?php endif; ?>
					<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo xpertz_course_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Contact', 'edupress' ); ?></a>
				</div>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Render related courses in a consistent four-column grid.
 *
 * @param \LearnPress\Models\CourseModel $course Current course.
 */
function xpertz_course_render_related( $course ) {
	$category_ids = wp_list_pluck( $course->get_categories(), 'term_id' );
	$args         = array(
		'post_type'           => 'lp_course',
		'post_status'         => 'publish',
		'posts_per_page'      => 4,
		'post__not_in'        => array( $course->get_id() ),
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
		'orderby'             => 'date',
		'order'               => 'DESC',
	);

	if ( $category_ids ) {
		$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
			array(
				'taxonomy' => LP_COURSE_CATEGORY_TAX,
				'field'    => 'term_id',
				'terms'    => $category_ids,
			),
		);
	}

	$query = new WP_Query( $args );
	if ( ! $query->have_posts() && $category_ids ) {
		unset( $args['tax_query'] );
		$query = new WP_Query( $args );
	}

	if ( ! $query->have_posts() ) {
		return;
	}
	?>
	<section class="xpc-related" aria-labelledby="xpc-related-title">
		<div class="xpc-related-heading">
			<div>
				<span class="xpc-eyebrow"><?php esc_html_e( 'Continue learning', 'edupress' ); ?></span>
				<h2 id="xpc-related-title"><?php esc_html_e( 'Related courses', 'edupress' ); ?></h2>
			</div>
			<a href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>" class="xpc-text-link"><?php esc_html_e( 'Browse all courses', 'edupress' ); ?><?php echo xpertz_course_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
		<div class="xpc-related-grid">
			<?php while ( $query->have_posts() ) : ?>
				<?php
				$query->the_post();
				$related_id     = get_the_ID();
				$related_course = \LearnPress\Models\CourseModel::find( $related_id, true );
				if ( ! $related_course ) {
					continue;
				}
				$related_rating = xpertz_course_rating_data( $related_id );
				$categories     = $related_course->get_categories();
				$author_name    = get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $related_id ) );
				$legacy_course  = learn_press_get_course( $related_id );
				?>
				<article class="xpc-related-card">
					<a class="xpc-related-image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php echo xpertz_course_image( $related_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<div class="xpc-related-content">
						<?php if ( $categories ) : ?><span class="xpc-course-category"><?php echo esc_html( $categories[0]->name ); ?></span><?php endif; ?>
						<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
						<p class="xpc-related-instructor"><?php echo esc_html( $author_name ?: __( 'XPERTZ Faculty', 'edupress' ) ); ?></p>
						<div class="xpc-related-rating">
							<?php echo xpertz_course_stars( $related_rating['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php echo $related_rating['total'] ? esc_html( number_format_i18n( $related_rating['average'], 1 ) ) : esc_html__( 'New', 'edupress' ); ?></span>
						</div>
						<div class="xpc-related-meta">
							<span><?php echo xpertz_course_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $related_course->get_duration() ); ?></span>
							<span><?php echo xpertz_course_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( number_format_i18n( $related_course->count_students() ) ); ?></span>
						</div>
						<div class="xpc-related-footer">
							<div class="xpc-related-price"><?php echo $legacy_course ? $legacy_course->get_course_price_html() : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							<a class="xpc-icon-link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View %s', 'edupress' ), get_the_title() ) ); ?>"><?php echo xpertz_course_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						</div>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}

/**
 * Render the complete premium course experience.
 *
 * @param \LearnPress\Models\CourseModel $course LearnPress course model.
 */
function xpertz_render_course_page( $course ) {
	if ( ! $course instanceof \LearnPress\Models\CourseModel ) {
		return;
	}

	$course_id      = $course->get_id();
	$legacy_course  = learn_press_get_course( $course_id );
	$user           = \LearnPress\Models\UserModel::find( get_current_user_id(), true );
	$template       = \LearnPress\TemplateHooks\Course\SingleCourseTemplate::instance();
	$modern_layout  = \LearnPress\TemplateHooks\Course\SingleCourseModernLayout::instance();
	$categories     = $course->get_categories();
	$tags           = $course->get_tags();
	$rating         = xpertz_course_rating_data( $course_id );
	$lessons        = $course->count_items( LP_LESSON_CPT );
	$quizzes        = $course->count_items( LP_QUIZ_CPT );
	$assignments    = defined( 'LP_ASSIGNMENT_CPT' ) ? $course->count_items( LP_ASSIGNMENT_CPT ) : 0;
	$duration       = $course->get_duration() ?: esc_html__( 'Self-paced', 'edupress' );
	$level          = xpertz_course_level_label( (string) get_post_meta( $course_id, '_lp_level', true ) );
	$language_code  = determine_locale();
	$language_parts = explode( '_', $language_code );
	$language       = class_exists( 'Locale' ) ? Locale::getDisplayLanguage( $language_parts[0], 'en' ) : '';
	$language       = $language ?: strtoupper( $language_parts[0] );
	$features       = (array) get_post_meta( $course_id, '_lp_key_features', true );
	$requirements   = (array) get_post_meta( $course_id, '_lp_requirements', true );
	$audiences      = (array) get_post_meta( $course_id, '_lp_target_audiences', true );
	$faqs           = (array) get_post_meta( $course_id, '_lp_faqs', true );
	$description    = $course->get_description();
	$short_desc     = $course->get_short_description();
	$author_id      = (int) get_post_field( 'post_author', $course_id );
	$instructor     = get_the_author_meta( 'display_name', $author_id ) ?: esc_html__( 'XPERTZ Faculty', 'edupress' );
	$price_html     = $legacy_course ? $legacy_course->get_course_price_html() : '';
	$regular_price  = $course->get_regular_price();
	$current_price  = $course->get_price();
	$discount       = $regular_price > 0 && $current_price < $regular_price ? (int) round( ( ( $regular_price - $current_price ) / $regular_price ) * 100 ) : 0;
	$sale_end       = $course->has_sale_price() ? $course->get_sale_end() : '';
	$buttons        = $modern_layout->html_buttons( $course, $user );
	$curriculum     = $template->html_curriculum( $course, $user );
	$material       = $template->html_material( $course, $user );
	$course_url     = $course->get_permalink();
	$modified       = get_post_modified_time( get_option( 'date_format' ), false, $course_id );
	$category_name  = $categories ? $categories[0]->name : esc_html__( 'Professional development', 'edupress' );
	$category_url   = $categories ? get_term_link( $categories[0] ) : get_post_type_archive_link( 'lp_course' );
	$schema         = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Course',
		'name'        => $course->get_title(),
		'description' => wp_strip_all_tags( $short_desc ?: $description ),
		'url'         => $course_url,
		'provider'    => array(
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		),
	);

	if ( has_post_thumbnail( $course_id ) ) {
		$schema['image'] = wp_get_attachment_image_url( get_post_thumbnail_id( $course_id ), 'full' );
	}
	if ( $rating['total'] ) {
		$schema['aggregateRating'] = array(
			'@type'       => 'AggregateRating',
			'ratingValue' => $rating['average'],
			'reviewCount' => $rating['total'],
		);
	}
	?>
	<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></script>
	<a class="xpc-skip-link screen-reader-text" href="#course-overview"><?php esc_html_e( 'Skip to course content', 'edupress' ); ?></a>
	<main id="xpertz-course" class="xpc-course" data-course-id="<?php echo esc_attr( $course_id ); ?>" itemscope itemtype="https://schema.org/Course">
		<section class="xpc-hero">
			<div class="xpc-hero-glow xpc-hero-glow--one"></div>
			<div class="xpc-hero-glow xpc-hero-glow--two"></div>
			<div class="xpc-container xpc-hero-grid">
				<div class="xpc-hero-content">
					<nav class="xpc-breadcrumbs" aria-label="<?php esc_attr_e( 'Breadcrumb', 'edupress' ); ?>">
						<ol>
							<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'edupress' ); ?></a></li>
							<li><a href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>"><?php esc_html_e( 'Courses', 'edupress' ); ?></a></li>
							<li aria-current="page"><?php echo esc_html( wp_trim_words( $course->get_title(), 5, '…' ) ); ?></li>
						</ol>
					</nav>
					<a class="xpc-category-badge" href="<?php echo esc_url( $category_url ); ?>"><?php echo xpertz_course_icon( 'tag' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $category_name ); ?></a>
					<h1 itemprop="name"><?php echo esc_html( $course->get_title() ); ?></h1>
					<?php if ( $short_desc ) : ?><div class="xpc-hero-summary" itemprop="description"><?php echo wp_kses_post( wpautop( $short_desc ) ); ?></div><?php endif; ?>
					<div class="xpc-hero-rating">
						<?php if ( $rating['total'] ) : ?>
							<strong><?php echo esc_html( number_format_i18n( $rating['average'], 1 ) ); ?></strong>
							<?php echo xpertz_course_stars( $rating['average'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<a href="#reviews"><?php echo esc_html( sprintf( _n( '%s review', '%s reviews', $rating['total'], 'edupress' ), number_format_i18n( $rating['total'] ) ) ); ?></a>
						<?php else : ?>
							<span class="xpc-new-course"><?php esc_html_e( 'New course', 'edupress' ); ?></span>
						<?php endif; ?>
						<span class="xpc-hero-students"><?php echo xpertz_course_icon( 'users' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( _n( '%s learner', '%s learners', $course->count_students(), 'edupress' ), number_format_i18n( $course->count_students() ) ) ); ?></span>
					</div>
					<div class="xpc-hero-meta">
						<?php echo xpertz_course_meta_item( 'clock', $duration, __( 'Duration', 'edupress' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo xpertz_course_meta_item( 'book-open', sprintf( _n( '%s lesson', '%s lessons', $lessons, 'edupress' ), number_format_i18n( $lessons ) ), __( 'Lessons', 'edupress' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo xpertz_course_meta_item( 'bar-chart', $level, __( 'Skill level', 'edupress' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php echo xpertz_course_meta_item( 'globe', $language, __( 'Language', 'edupress' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
					<div class="xpc-hero-author">
						<span class="xpc-author-avatar" aria-hidden="true"><?php echo esc_html( strtoupper( substr( $instructor, 0, 1 ) ) ); ?></span>
						<span><?php esc_html_e( 'Created by', 'edupress' ); ?> <strong><?php echo esc_html( $instructor ); ?></strong></span>
						<span class="xpc-updated"><?php echo xpertz_course_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( sprintf( __( 'Updated %s', 'edupress' ), $modified ) ); ?></span>
					</div>
				</div>
				<div class="xpc-hero-media">
					<div class="xpc-media-frame">
						<?php echo xpertz_course_image( $course_id, 'xpc-hero-image', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<a class="xpc-preview-button" href="#curriculum"><span><?php echo xpertz_course_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><strong><?php esc_html_e( 'Preview course', 'edupress' ); ?></strong><small><?php esc_html_e( 'Explore the curriculum', 'edupress' ); ?></small></a>
					</div>
					<div class="xpc-trust-pill"><?php echo xpertz_course_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'XPERTZ quality', 'edupress' ); ?></strong><?php esc_html_e( 'Practical, structured learning', 'edupress' ); ?></span></div>
				</div>
			</div>
		</section>

		<nav class="xpc-course-nav" aria-label="<?php esc_attr_e( 'Course sections', 'edupress' ); ?>">
			<div class="xpc-container">
				<a href="#course-overview" class="is-active"><?php esc_html_e( 'Overview', 'edupress' ); ?></a>
				<a href="#curriculum"><?php esc_html_e( 'Curriculum', 'edupress' ); ?></a>
				<a href="#instructor"><?php esc_html_e( 'Instructor', 'edupress' ); ?></a>
				<a href="#reviews"><?php esc_html_e( 'Reviews', 'edupress' ); ?></a>
				<?php if ( $faqs ) : ?><a href="#faq"><?php esc_html_e( 'FAQ', 'edupress' ); ?></a><?php endif; ?>
				<?php if ( $material ) : ?><a href="#resources"><?php esc_html_e( 'Resources', 'edupress' ); ?></a><?php endif; ?>
			</div>
		</nav>

		<div class="xpc-page-bg">
			<div class="xpc-container xpc-course-layout">
				<div class="xpc-course-content">
					<section id="course-overview" class="xpc-card xpc-section xpc-description" aria-labelledby="xpc-overview-title">
						<div class="xpc-section-heading">
							<span class="xpc-section-icon"><?php echo xpertz_course_icon( 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
							<div><span class="xpc-eyebrow"><?php esc_html_e( 'Course overview', 'edupress' ); ?></span><h2 id="xpc-overview-title"><?php esc_html_e( 'About this course', 'edupress' ); ?></h2></div>
						</div>
						<div class="xpc-prose"><?php echo apply_filters( 'the_content', $description ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</section>

					<?php xpertz_course_list_card( '', 'award', __( 'What you will achieve', 'edupress' ), __( 'Learning outcomes', 'edupress' ), $features ); ?>
					<div class="xpc-overview-grid">
						<?php xpertz_course_list_card( '', 'check-circle', __( 'Before you begin', 'edupress' ), __( 'Requirements', 'edupress' ), $requirements ); ?>
						<?php xpertz_course_list_card( '', 'users', __( 'Built for you', 'edupress' ), __( 'Who this course is for', 'edupress' ), $audiences ); ?>
					</div>

					<section id="curriculum" class="xpc-card xpc-section xpc-curriculum" aria-labelledby="xpc-curriculum-title">
						<div class="xpc-section-heading xpc-section-heading--split">
							<div class="xpc-heading-group"><span class="xpc-section-icon"><?php echo xpertz_course_icon( 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><span class="xpc-eyebrow"><?php esc_html_e( 'Step-by-step learning', 'edupress' ); ?></span><h2 id="xpc-curriculum-title"><?php esc_html_e( 'Course curriculum', 'edupress' ); ?></h2></div></div>
							<div class="xpc-curriculum-stats"><span><?php echo esc_html( sprintf( _n( '%s lesson', '%s lessons', $lessons, 'edupress' ), number_format_i18n( $lessons ) ) ); ?></span><span><?php echo esc_html( sprintf( _n( '%s quiz', '%s quizzes', $quizzes, 'edupress' ), number_format_i18n( $quizzes ) ) ); ?></span><span><?php echo esc_html( $duration ); ?></span></div>
						</div>
						<div class="xpc-curriculum-body"><?php echo $curriculum; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					</section>

					<?php xpertz_course_render_instructor( $course, $rating ); ?>
					<?php xpertz_course_render_reviews( $course_id, $rating ); ?>

					<?php if ( $faqs ) : ?>
						<section id="faq" class="xpc-card xpc-section xpc-faq" aria-labelledby="xpc-faq-title">
							<div class="xpc-section-heading xpc-section-heading--split">
								<div class="xpc-heading-group"><span class="xpc-section-icon"><?php echo xpertz_course_icon( 'quiz' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><span class="xpc-eyebrow"><?php esc_html_e( 'Need to know', 'edupress' ); ?></span><h2 id="xpc-faq-title"><?php esc_html_e( 'Frequently asked questions', 'edupress' ); ?></h2></div></div>
								<label class="xpc-faq-search"><span class="screen-reader-text"><?php esc_html_e( 'Search frequently asked questions', 'edupress' ); ?></span><?php echo xpertz_course_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><input type="search" placeholder="<?php esc_attr_e( 'Search questions', 'edupress' ); ?>" data-faq-search></label>
							</div>
							<div class="xpc-faq-list">
								<?php foreach ( $faqs as $index => $faq ) : ?>
									<?php if ( empty( $faq[0] ) ) { continue; } ?>
									<div class="xpc-faq-item" data-faq-item>
										<h3><button type="button" aria-expanded="<?php echo 0 === $index ? 'true' : 'false'; ?>" aria-controls="xpc-faq-panel-<?php echo esc_attr( $index ); ?>"><span><?php echo esc_html( $faq[0] ); ?></span><?php echo xpertz_course_icon( 'chevron-down' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button></h3>
										<div id="xpc-faq-panel-<?php echo esc_attr( $index ); ?>" class="xpc-faq-panel"<?php echo 0 === $index ? '' : ' hidden'; ?>><?php echo wp_kses_post( $faq[1] ?? '' ); ?></div>
									</div>
								<?php endforeach; ?>
							</div>
							<p class="xpc-faq-empty" hidden><?php esc_html_e( 'No questions match your search.', 'edupress' ); ?></p>
						</section>
					<?php endif; ?>

					<?php if ( $material ) : ?>
						<section id="resources" class="xpc-card xpc-section xpc-resources" aria-labelledby="xpc-resources-title">
							<div class="xpc-section-heading"><span class="xpc-section-icon"><?php echo xpertz_course_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span><div><span class="xpc-eyebrow"><?php esc_html_e( 'Keep learning', 'edupress' ); ?></span><h2 id="xpc-resources-title"><?php esc_html_e( 'Course resources', 'edupress' ); ?></h2></div></div>
							<?php echo $material; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</section>
					<?php endif; ?>
				</div>

				<aside class="xpc-course-sidebar" aria-label="<?php esc_attr_e( 'Course enrollment', 'edupress' ); ?>">
					<div class="xpc-enrollment-card">
						<div class="xpc-sidebar-image"><?php echo xpertz_course_image( $course_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="#curriculum" aria-label="<?php esc_attr_e( 'Preview course curriculum', 'edupress' ); ?>"><?php echo xpertz_course_icon( 'play' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></div>
						<div class="xpc-enrollment-body">
							<div class="xpc-price-row"><div class="xpc-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php if ( $discount ) : ?><span class="xpc-discount"><?php echo esc_html( sprintf( __( '%d%% off', 'edupress' ), $discount ) ); ?></span><?php endif; ?></div>
							<?php if ( $sale_end ) : ?><div class="xpc-countdown" data-countdown="<?php echo esc_attr( $sale_end ); ?>"><?php echo xpertz_course_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Offer ends soon', 'edupress' ); ?></strong><small data-countdown-label></small></span></div><?php endif; ?>
							<div class="xpc-course-actions"><?php echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
							<a href="#curriculum" class="xpc-button xpc-button--secondary"><?php esc_html_e( 'View curriculum', 'edupress' ); ?></a>
							<div class="xpc-secondary-actions">
								<button type="button" data-wishlist-course="<?php echo esc_attr( $course_id ); ?>" aria-pressed="false"><?php echo xpertz_course_icon( 'heart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Wishlist', 'edupress' ); ?></span></button>
								<button type="button" data-share-course data-url="<?php echo esc_url( $course_url ); ?>" data-title="<?php echo esc_attr( $course->get_title() ); ?>"><?php echo xpertz_course_icon( 'share' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Share', 'edupress' ); ?></span></button>
							</div>
							<div class="xpc-guarantee"><?php echo xpertz_course_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php esc_html_e( 'Learn with confidence', 'edupress' ); ?></strong><small><?php esc_html_e( 'Secure enrollment and immediate access', 'edupress' ); ?></small></span></div>
							<div class="xpc-includes">
								<h3><?php esc_html_e( 'This course includes', 'edupress' ); ?></h3>
								<ul>
									<li><?php echo xpertz_course_icon( 'infinity' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Lifetime access', 'edupress' ); ?></span></li>
									<li><?php echo xpertz_course_icon( 'award' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Certificate of completion', 'edupress' ); ?></span></li>
									<li><?php echo xpertz_course_icon( 'smartphone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Mobile-friendly access', 'edupress' ); ?></span></li>
									<li><?php echo xpertz_course_icon( 'download' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php esc_html_e( 'Downloadable resources', 'edupress' ); ?></span></li>
									<?php if ( $quizzes ) : ?><li><?php echo xpertz_course_icon( 'quiz' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( sprintf( _n( '%s quiz', '%s quizzes', $quizzes, 'edupress' ), number_format_i18n( $quizzes ) ) ); ?></span></li><?php endif; ?>
									<?php if ( $assignments ) : ?><li><?php echo xpertz_course_icon( 'file-text' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( sprintf( _n( '%s assignment', '%s assignments', $assignments, 'edupress' ), number_format_i18n( $assignments ) ) ); ?></span></li><?php endif; ?>
								</ul>
							</div>
							<div class="xpc-course-facts">
								<h3><?php esc_html_e( 'Course details', 'edupress' ); ?></h3>
								<dl>
									<div><dt><?php echo xpertz_course_icon( 'bar-chart' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Level', 'edupress' ); ?></dt><dd><?php echo esc_html( $level ); ?></dd></div>
									<div><dt><?php echo xpertz_course_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Language', 'edupress' ); ?></dt><dd><?php echo esc_html( $language ); ?></dd></div>
									<div><dt><?php echo xpertz_course_icon( 'clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Duration', 'edupress' ); ?></dt><dd><?php echo esc_html( $duration ); ?></dd></div>
									<div><dt><?php echo xpertz_course_icon( 'book-open' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Lessons', 'edupress' ); ?></dt><dd><?php echo esc_html( number_format_i18n( $lessons ) ); ?></dd></div>
									<div><dt><?php echo xpertz_course_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Updated', 'edupress' ); ?></dt><dd><?php echo esc_html( $modified ); ?></dd></div>
								</dl>
							</div>
							<?php if ( $tags ) : ?><div class="xpc-tags"><h3><?php esc_html_e( 'Topics', 'edupress' ); ?></h3><div><?php foreach ( $tags as $tag ) : ?><a href="<?php echo esc_url( get_term_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a><?php endforeach; ?></div></div><?php endif; ?>
						</div>
					</div>
				</aside>
			</div>

			<div class="xpc-container"><?php xpertz_course_render_related( $course ); ?></div>
		</div>

		<div class="xpc-mobile-bar" aria-label="<?php esc_attr_e( 'Course enrollment', 'edupress' ); ?>">
			<div class="xpc-mobile-price"><?php echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
			<button type="button" class="xpc-button xpc-button--primary" data-mobile-enroll><?php esc_html_e( 'Enroll now', 'edupress' ); ?><?php echo xpertz_course_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
		</div>
	</main>
	<?php
}
