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

	$etapas = nevasenda_ruta_etapas( get_the_ID() );
	$colors = nevasenda_etapa_colors();
	$etapas_data = array();
	foreach ( $etapas as $i => $etapa ) {
		$etapas_data[] = array(
			'nombre' => $etapa['nombre'] ? $etapa['nombre'] : sprintf( 'Etapa %d', $i + 1 ),
			'gpx'    => $etapa['gpx'],
			'color'  => $colors[ $i % count( $colors ) ],
		);
	}
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

			<?php
			$stats_items = array();
			if ( $distancia ) {
				$stats_items[] = array( 'ruler', esc_html( $distancia ) . ' km', 'Distancia' );
			}
			if ( $desnivel ) {
				$stats_items[] = array( 'terrain', esc_html( $desnivel ) . ' m', 'Desnivel' );
			}
			if ( $duracion ) {
				$stats_items[] = array( 'clock', esc_html( $duracion ), 'Duración' );
			}
			if ( $encuentro ) {
				$stats_items[] = array( 'pin', esc_html( $encuentro ), 'Punto de encuentro' );
			}
			if ( $hora_salida ) {
				$stats_items[] = array( 'clock', esc_html( $hora_salida ), 'Hora de salida' );
			}
			?>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="ruta-hero"><?php the_post_thumbnail( 'large' ); ?></div>
				<?php nevasenda_render_ruta_stats( $stats_items, 'ruta-stats--float' ); ?>
			<?php else : ?>
				<?php nevasenda_render_ruta_stats( $stats_items ); ?>
			<?php endif; ?>

			<div class="ruta-content entry-content">
				<?php the_content(); ?>
			</div>

			<?php if ( $etapas_data || $wikiloc || $requisitos ) : ?>
				<div class="ruta-extra">
					<?php if ( $etapas_data ) : ?>
						<div class="ruta-card ruta-map-card">
							<div class="ruta-card__head">
								<h2><?php echo nevasenda_icon( 'pin' ); ?>Mapa de la ruta</h2>
								<?php if ( $wikiloc ) : ?>
									<a class="ruta-wikiloc-btn" href="<?php echo esc_url( $wikiloc ); ?>" target="_blank" rel="noopener">Ver en Wikiloc <?php echo nevasenda_icon( 'arrow' ); ?></a>
								<?php endif; ?>
							</div>
							<div id="ruta-leaflet-map" class="ruta-leaflet-map" data-etapas="<?php echo esc_attr( wp_json_encode( $etapas_data ) ); ?>"></div>
							<?php if ( count( $etapas_data ) > 1 ) : ?>
								<div class="ruta-etapas-legend">
									<?php foreach ( $etapas_data as $etapa ) : ?>
										<span class="ruta-etapa-tag"><span class="ruta-etapa-dot" style="background:<?php echo esc_attr( $etapa['color'] ); ?>"></span><?php echo esc_html( $etapa['nombre'] ); ?></span>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php elseif ( $wikiloc ) : ?>
						<div class="ruta-card ruta-map-card">
							<div class="ruta-card__head">
								<h2><?php echo nevasenda_icon( 'pin' ); ?>Mapa de la ruta</h2>
							</div>
							<div class="ruta-map-cta">
								<?php echo nevasenda_icon( 'pin' ); ?>
								<h3>Mapa interactivo en Wikiloc</h3>
								<p>Consulta el mapa, el perfil de elevación y descarga el track GPX completo de esta ruta.</p>
								<a class="btn btn-outline ruta-cta-btn" href="<?php echo esc_url( $wikiloc ); ?>" target="_blank" rel="noopener">Ver track completo en Wikiloc <?php echo nevasenda_icon( 'arrow' ); ?></a>
							</div>
						</div>
					<?php endif; ?>

					<?php if ( $requisitos ) : ?>
						<div class="ruta-card ruta-requisitos-card">
							<div class="ruta-card__head">
								<h2><?php echo nevasenda_icon( 'check' ); ?>Requisitos y material recomendado</h2>
							</div>
							<div class="ruta-requisitos-grid">
								<?php
								foreach ( preg_split( '/\r\n|\r|\n/', trim( $requisitos ) ) as $linea ) :
									$linea = trim( $linea );
									if ( '' === $linea ) {
										continue;
									}
									?>
									<span class="ruta-req-chip"><?php echo nevasenda_icon( 'check' ); ?><?php echo esc_html( $linea ); ?></span>
									<?php
								endforeach;
								?>
							</div>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php
endwhile;

get_footer();
