<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Galería</h1>
		<p class="section-subtitle">Fotos de rutas, paisajes y montaña enviadas por la comunidad</p>

		<div class="gallery-grid">
			<?php for ( $i = 1; $i <= 8; $i++ ) : ?>
				<a href="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>">
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/gallery-' . $i . '.jpg' ); ?>" alt="Foto de senderismo <?php echo esc_attr( $i ); ?>" loading="lazy">
				</a>
			<?php endfor; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
