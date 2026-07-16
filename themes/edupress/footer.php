<?php
/**
 * Premium XPERTZ site footer.
 *
 * @package EduPress
 */

$catalog_url = get_post_type_archive_link( 'lp_course' ) ?: home_url( '/courses/' );
?>

<footer class="site-footer xhc-site-footer" role="contentinfo">
	<div class="xhc-container xhc-footer-grid">
		<div class="xhc-footer-brand">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<p><?php esc_html_e( 'Focused, expert-led learning experiences that help professionals build practical skills and lasting career momentum.', 'edupress' ); ?></p>
			<span><?php echo xpertz_commerce_icon( 'shield-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php esc_html_e( 'Secure learning and payments', 'edupress' ); ?></span>
		</div>

		<nav aria-labelledby="xhc-footer-learn">
			<h2 id="xhc-footer-learn"><?php esc_html_e( 'Learn', 'edupress' ); ?></h2>
			<a href="<?php echo esc_url( $catalog_url ); ?>"><?php esc_html_e( 'All courses', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'categories' ) ); ?>"><?php esc_html_e( 'Categories', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'pricing' ) ); ?>"><?php esc_html_e( 'Pricing', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'blog' ) ); ?>"><?php esc_html_e( 'Insights', 'edupress' ); ?></a>
		</nav>

		<nav aria-labelledby="xhc-footer-company">
			<h2 id="xhc-footer-company"><?php esc_html_e( 'XPERTZ', 'edupress' ); ?></h2>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'about' ) ); ?>"><?php esc_html_e( 'About', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'support' ) ); ?>"><?php esc_html_e( 'Learner support', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( xpertz_commerce_page_url( 'wishlist' ) ); ?>"><?php esc_html_e( 'Wishlist', 'edupress' ); ?></a>
		</nav>

		<nav aria-labelledby="xhc-footer-account">
			<h2 id="xhc-footer-account"><?php esc_html_e( 'Account', 'edupress' ); ?></h2>
			<a href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : wp_login_url() ); ?>"><?php esc_html_e( 'My account', 'edupress' ); ?></a>
			<a href="<?php echo esc_url( function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ) ); ?>"><?php esc_html_e( 'Cart', 'edupress' ); ?></a>
			<?php if ( is_user_logged_in() && function_exists( 'wc_get_account_endpoint_url' ) ) : ?><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'my-courses' ) ); ?>"><?php esc_html_e( 'My courses', 'edupress' ); ?></a><?php endif; ?>
		</nav>
	</div>

	<div class="xhc-footer-bottom"><div class="xhc-container"><p><?php echo esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'edupress' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) ); ?></p><p><?php esc_html_e( 'Learn continuously. Grow confidently.', 'edupress' ); ?></p></div></div>
</footer>

</div><!-- #container -->

<?php wp_footer(); ?>

</body>
</html>
