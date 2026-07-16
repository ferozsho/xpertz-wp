<?php
/**
 * Premium server-rendered LearnPress course catalog.
 *
 * @package EduPress
 */

defined( 'ABSPATH' ) || exit;

get_header();

$taxonomy      = defined( 'LP_COURSE_CATEGORY_TAX' ) ? LP_COURSE_CATEGORY_TAX : 'course_category';
$is_category   = is_tax( $taxonomy );
$archive_title = $is_category ? single_term_title( '', false ) : __( 'Explore courses', 'edupress' );
$archive_copy  = $is_category ? term_description() : __( 'Build practical skills with focused, expert-led learning experiences designed for real career progress.', 'edupress' );
$search        = isset( $_GET['course_search'] ) && is_string( $_GET['course_search'] ) ? sanitize_text_field( wp_unslash( $_GET['course_search'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter.
$sort          = isset( $_GET['course_sort'] ) && is_string( $_GET['course_sort'] ) ? sanitize_key( wp_unslash( $_GET['course_sort'] ) ) : 'newest'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only catalog filter.
$categories    = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) );
$current_term  = $is_category ? get_queried_object_id() : 0;
?>

<main id="site-main" class="xhc-catalog-page">
	<header class="xhc-catalog-hero">
		<div class="xhc-container">
			<span class="xhc-eyebrow"><?php echo $is_category ? esc_html__( 'Course category', 'edupress' ) : esc_html__( 'XPERTZ course catalog', 'edupress' ); ?></span>
			<h1><?php echo esc_html( $archive_title ); ?></h1>
			<div class="xhc-catalog-description"><?php echo wp_kses_post( $archive_copy ); ?></div>
		</div>
	</header>

	<div class="xhc-container xhc-catalog-content">
		<?php if ( ! is_wp_error( $categories ) && $categories ) : ?>
			<nav class="xhc-category-chips" aria-label="<?php esc_attr_e( 'Filter courses by category', 'edupress' ); ?>">
				<a class="<?php echo $current_term ? '' : 'is-active'; ?>" href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>"><?php esc_html_e( 'All courses', 'edupress' ); ?></a>
				<?php foreach ( $categories as $category ) : ?>
					<a class="<?php echo (int) $category->term_id === (int) $current_term ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?><span><?php echo esc_html( number_format_i18n( $category->count ) ); ?></span></a>
				<?php endforeach; ?>
				<a href="<?php echo esc_url( xpertz_commerce_page_url( 'categories' ) ); ?>"><?php esc_html_e( 'Browse categories', 'edupress' ); ?><?php echo xpertz_commerce_icon( 'arrow-right' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
			</nav>
		<?php endif; ?>

		<form class="xhc-catalog-toolbar" action="<?php echo esc_url( $is_category ? get_term_link( get_queried_object() ) : get_post_type_archive_link( 'lp_course' ) ); ?>" method="get">
			<label class="xhc-catalog-search">
				<span class="screen-reader-text"><?php esc_html_e( 'Search courses', 'edupress' ); ?></span>
				<?php echo xpertz_commerce_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<input type="search" name="course_search" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search courses…', 'edupress' ); ?>">
			</label>
			<div class="xhc-catalog-toolbar-actions">
				<label><span><?php esc_html_e( 'Sort by', 'edupress' ); ?></span><select name="course_sort"><option value="newest" <?php selected( $sort, 'newest' ); ?>><?php esc_html_e( 'Newest', 'edupress' ); ?></option><option value="oldest" <?php selected( $sort, 'oldest' ); ?>><?php esc_html_e( 'Oldest', 'edupress' ); ?></option><option value="title" <?php selected( $sort, 'title' ); ?>><?php esc_html_e( 'Course title', 'edupress' ); ?></option></select></label>
				<button type="submit" class="xhc-primary-link"><?php esc_html_e( 'Apply', 'edupress' ); ?></button>
			</div>
		</form>

		<div class="xhc-catalog-results-heading">
			<h2><?php echo esc_html( sprintf( _n( '%s course', '%s courses', $GLOBALS['wp_query']->found_posts, 'edupress' ), number_format_i18n( $GLOBALS['wp_query']->found_posts ) ) ); ?></h2>
			<?php if ( $search ) : ?><p><?php echo esc_html( sprintf( __( 'Results for “%s”', 'edupress' ), $search ) ); ?></p><?php endif; ?>
		</div>

		<?php if ( have_posts() ) : ?>
			<div class="xhc-catalog-grid">
				<?php while ( have_posts() ) : ?>
					<?php the_post(); ?>
					<?php xpertz_commerce_catalog_course_card( get_the_ID() ); ?>
				<?php endwhile; ?>
			</div>
			<?php
			the_posts_pagination(
				array(
					'mid_size'  => 2,
					'prev_text' => __( 'Previous', 'edupress' ),
					'next_text' => __( 'Next', 'edupress' ),
					'add_args'  => array_filter( array( 'course_search' => $search, 'course_sort' => $sort ) ),
				)
			);
			?>
		<?php else : ?>
			<div class="xhc-account-empty"><?php echo xpertz_commerce_icon( 'search' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><h3><?php esc_html_e( 'No courses matched your search', 'edupress' ); ?></h3><p><?php esc_html_e( 'Try a broader phrase or browse every available course.', 'edupress' ); ?></p><a class="xhc-primary-link" href="<?php echo esc_url( get_post_type_archive_link( 'lp_course' ) ); ?>"><?php esc_html_e( 'View all courses', 'edupress' ); ?></a></div>
		<?php endif; ?>
	</div>
</main>

<?php
get_footer();
