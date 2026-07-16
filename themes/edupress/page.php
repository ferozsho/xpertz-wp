<?php
/**
 * Premium public page template.
 *
 * @package EduPress
 */

get_header();
?>

<main id="site-main" class="xhc-page-main">
	<?php while ( have_posts() ) : ?>
		<?php
		the_post();
		$page_slug = get_post_field( 'post_name', get_the_ID() );
		$intro     = function_exists( 'xpertz_commerce_page_intro' ) ? xpertz_commerce_page_intro( $page_slug ) : '';
		?>
		<header class="xhc-page-hero">
			<div class="xhc-container">
				<span class="xhc-eyebrow"><?php esc_html_e( 'XPERTZ learning platform', 'edupress' ); ?></span>
				<h1><?php the_title(); ?></h1>
				<?php if ( $intro ) : ?><p><?php echo esc_html( $intro ); ?></p><?php endif; ?>
			</div>
		</header>

		<div class="xhc-container xhc-page-content">
			<?php if ( has_post_thumbnail() && 1 === (int) get_theme_mod( 'edupress_single_featured_image', 1 ) ) : ?>
				<figure class="xhc-page-featured-image"><?php the_post_thumbnail( 'edupress-large-thumbnail', array( 'loading' => 'eager' ) ); ?></figure>
			<?php endif; ?>

			<div class="xhc-page-entry">
				<?php the_content(); ?>
				<?php wp_link_pages( array( 'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'edupress' ), 'after' => '</nav>' ) ); ?>
			</div>

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		</div>
	<?php endwhile; ?>
</main>

<?php
get_footer();
