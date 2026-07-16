<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<div id="container">

	<a class="skip-link screen-reader-text" href="#site-main"><?php esc_html_e( 'Skip to content', 'edupress' ); ?></a>
	<?php if ( function_exists( 'xpertz_render_lms_header' ) ) : ?>
		<?php xpertz_render_lms_header(); ?>
	<?php else : ?>
		<header class="site-header" role="banner">
			<div class="wrapper wrapper-header">
				<div id="site-header-main">
					<div class="site-branding">
						<?php if ( function_exists( 'has_custom_logo' ) && has_custom_logo() ) : ?>
							<?php edupress_the_custom_logo(); ?>
						<?php else : ?>
							<p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
							<p class="site-description"><?php bloginfo( 'description' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</header>
	<?php endif; ?>
