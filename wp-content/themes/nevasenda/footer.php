<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<footer class="site-footer">
	<div class="container">
		<div>
			<h4>Nevasenda</h4>
			<p><?php bloginfo( 'description' ); ?></p>
		</div>

		<div>
			<h4>Navegación</h4>
			<?php
			wp_nav_menu( array(
				'theme_location' => 'footer',
				'container'      => false,
				'menu_class'     => 'footer-menu',
				'fallback_cb'    => 'nevasenda_fallback_menu',
			) );
			?>
		</div>

		<div class="copy">
			&copy; <?php echo esc_html( date( 'Y' ) ); ?> Nevasenda. Todos los derechos reservados.
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
