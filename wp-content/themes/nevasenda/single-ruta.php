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

	$rating_stats = nevasenda_ruta_rating_stats( get_the_ID() );

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

			<?php if ( $rating_stats['count'] ) : ?>
				<div class="ruta-title-rating">
					<?php echo nevasenda_render_stars( $rating_stats['avg'] ); ?>
					<strong><?php echo esc_html( number_format_i18n( $rating_stats['avg'], 1 ) ); ?></strong>
					<a href="#opiniones"><?php echo esc_html( $rating_stats['count'] ); ?> opiniones</a>
				</div>
			<?php endif; ?>

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

			<?php $reviews = get_comments( array( 'post_id' => get_the_ID(), 'status' => 'approve' ) ); ?>

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
						<div class="ruta-card ruta-reviews-card" id="opiniones">
							<div class="ruta-card__head">
								<h2><?php echo nevasenda_icon( 'star' ); ?>Opiniones</h2>
								<?php if ( $rating_stats['count'] ) : ?>
									<div class="ruta-rating-summary">
										<?php echo nevasenda_render_stars( $rating_stats['avg'] ); ?>
										<strong><?php echo esc_html( number_format_i18n( $rating_stats['avg'], 1 ) ); ?></strong>
										<span><?php echo esc_html( $rating_stats['count'] ); ?> opiniones</span>
									</div>
								<?php endif; ?>
							</div>

							<?php if ( $reviews ) : ?>
								<div class="ruta-review-list">
									<?php foreach ( $reviews as $review ) : ?>
										<?php $review_rating = (int) get_comment_meta( $review->comment_ID, 'rating', true ); ?>
										<div class="ruta-review">
											<div class="ruta-review__head">
												<span class="avatar"><?php echo esc_html( mb_substr( $review->comment_author, 0, 1 ) ); ?></span>
												<div class="ruta-review__author">
													<strong><?php echo esc_html( $review->comment_author ); ?></strong>
													<?php if ( $review_rating ) : ?><?php echo nevasenda_render_stars( $review_rating ); ?><?php endif; ?>
												</div>
												<time><?php echo esc_html( get_comment_date( 'd/m/Y', $review ) ); ?></time>
											</div>
											<p><?php echo esc_html( $review->comment_content ); ?></p>
										</div>
									<?php endforeach; ?>
								</div>
							<?php else : ?>
								<p class="ruta-reviews-empty">Todavía no hay opiniones de esta ruta. ¡Sé el primero en compartir la tuya!</p>
							<?php endif; ?>

							<?php if ( is_user_logged_in() ) : ?>
								<div class="ruta-review-form">
									<?php
									comment_form( array(
										'title_reply'   => 'Deja tu opinión',
										'class_submit'  => 'btn',
										'label_submit'  => 'Publicar opinión',
										'comment_field' => nevasenda_rating_field_html(),
									) );
									?>
								</div>
							<?php else : ?>
								<div class="ruta-review-form ruta-review-form--locked">
									<p>Inicia sesión pa dejar tu opinión sobre esta ruta.</p>
									<a class="btn" href="<?php echo esc_url( add_query_arg( 'redirect_to', rawurlencode( get_permalink() . '#opiniones' ), home_url( '/cuenta/' ) ) ); ?>">Iniciar sesión</a>
								</div>
							<?php endif; ?>
						</div>
				</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
