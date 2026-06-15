<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();

while ( have_posts() ) :
	the_post();

	$distancia  = nevasenda_ruta_meta( get_the_ID(), 'distancia' );
	$desnivel   = nevasenda_ruta_meta( get_the_ID(), 'desnivel' );
	$duracion   = nevasenda_ruta_meta( get_the_ID(), 'duracion' );
	$wikiloc    = nevasenda_ruta_meta( get_the_ID(), 'wikiloc' );
	$requisitos = nevasenda_ruta_meta( get_the_ID(), 'requisitos' );
	$encuentro  = nevasenda_ruta_meta( get_the_ID(), 'punto_encuentro' );
	$hora_salida = nevasenda_ruta_meta( get_the_ID(), 'hora_salida' );
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
				<?php if ( $hora_salida ) : ?>
					<div class="dato"><strong><?php echo esc_html( $hora_salida ); ?></strong><span>Hora de salida</span></div>
				<?php endif; ?>
				<?php if ( $encuentro ) : ?>
					<div class="dato"><strong><?php echo esc_html( $encuentro ); ?></strong><span>Punto de encuentro</span></div>
				<?php endif; ?>
			</div>

			<div class="ruta-content entry-content">
				<?php the_content(); ?>
			</div>

			<?php if ( $wikiloc || $requisitos ) : ?>
				<div class="ruta-extra">
					<?php if ( $wikiloc ) : ?>
						<div class="ruta-map">
							<h2>Mapa de la ruta</h2>
							<div class="ruta-map-placeholder">
								<?php echo nevasenda_icon( 'pin' ); ?>
								<p>Consulta el mapa interactivo y el track GPX completo de esta ruta en Wikiloc.</p>
							</div>
							<a class="btn ruta-wikiloc-btn" href="<?php echo esc_url( $wikiloc ); ?>" target="_blank" rel="noopener">Ver track completo en Wikiloc <?php echo nevasenda_icon( 'arrow' ); ?></a>
						</div>
					<?php endif; ?>

					<?php if ( $requisitos ) : ?>
						<div class="ruta-requisitos">
							<h2>Requisitos y material recomendado</h2>
							<ul>
								<?php
								foreach ( preg_split( '/\r\n|\r|\n/', trim( $requisitos ) ) as $linea ) :
									$linea = trim( $linea );
									if ( '' === $linea ) {
										continue;
									}
									?>
									<li><?php echo nevasenda_icon( 'check' ); ?><?php echo esc_html( $linea ); ?></li>
									<?php
								endforeach;
								?>
							</ul>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php
endwhile;

get_footer();
