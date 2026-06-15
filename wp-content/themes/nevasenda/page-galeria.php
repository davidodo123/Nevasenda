<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Galería</h1>
		<p class="section-subtitle">Fotos de rutas, paisajes y montaña enviadas por la comunidad</p>

		<div class="gallery-grid">
			<?php foreach ( nevasenda_galeria_fotos() as $foto ) : ?>
				<a href="<?php echo esc_url( $foto['full'] ); ?>">
					<img src="<?php echo esc_url( $foto['thumb'] ); ?>" alt="<?php echo esc_attr( $foto['alt'] ); ?>" loading="lazy">
				</a>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
