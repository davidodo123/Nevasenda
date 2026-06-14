<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) :
	the_post();

	$distancia  = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
	$desnivel   = nevasenda_ruta_meta( get_the_ID(), 'desnivel' );
	$duracion   = nevasenda_ruta_meta( get_the_ID(), 'duracion' );
	$dificultad = get_the_terms( get_the_ID(), 'dificultad' );
	$zona       = get_the_terms( get_the_ID(), 'zona' );
	?>

	<section class="section">
		<div class="container">
			<h1 class="entry-title"><?php the_title(); ?></h1>

			<div class="card-meta" style="justify-content:center; margin-bottom: 24px;">
				<?php if ( ! empty( $zona ) && ! is_wp_error( $zona ) ) : ?>
					<span class="badge"><?php echo esc_html( $zona[0]->name ); ?></span>
				<?php endif; ?>
				<?php if ( ! empty( $dificultad ) && ! is_wp_error( $dificultad ) ) : ?>
					<span class="badge badge-blue"><?php echo esc_html( $dificultad[0]->name ); ?></span>
				<?php endif; ?>
			</div>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="ruta-hero"><?php the_post_thumbnail( 'large' ); ?></div>
			<?php endif; ?>

			<div class="ruta-datos">
				<?php if ( $distancia ) : ?>
					<div class="dato"><strong><?php echo esc_html( $distancia ); ?> km</strong><span>Distancia</span></div>
				<?php endif; ?>
				<?php if ( $desnivel ) : ?>
					<div class="dato"><strong><?php echo esc_html( $desnivel ); ?> m</strong><span>Desnivel</span></div>
				<?php endif; ?>
				<?php if ( $duracion ) : ?>
					<div class="dato"><strong><?php echo esc_html( $duracion ); ?></strong><span>Duración</span></div>
				<?php endif; ?>
			</div>

			<div class="ruta-content entry-content">
				<?php the_content(); ?>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
