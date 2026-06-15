<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Blog</h1>
		<p class="section-subtitle">Noticias, reportajes y consejos de senderismo</p>

		<div class="cards-grid cards-grid--blog">
			<?php
			$nevasenda_is_first = ! is_paged();
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					$nevasenda_featured = $nevasenda_is_first;
					?>
					<article class="card<?php echo $nevasenda_featured ? ' card--featured' : ''; ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-img">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( $nevasenda_featured ? 'large' : 'medium_large' ); ?></a>
								<div class="card-date">
									<span><?php echo esc_html( get_the_date( 'd' ) ); ?></span>
									<small><?php echo esc_html( date_i18n( 'M', get_the_time( 'U' ) ) ); ?></small>
								</div>
							</div>
						<?php endif; ?>
						<div class="card-body">
							<div class="card-meta-row">
								<span><?php echo esc_html( get_the_date() ); ?></span>
								<span class="dot"></span>
								<span><?php echo esc_html( nevasenda_reading_time() ); ?> min de lectura</span>
							</div>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div class="card-excerpt"><?php the_excerpt(); ?></div>
							<a class="card-link" href="<?php the_permalink(); ?>">Leer más &rarr;</a>
						</div>
					</article>
					<?php
					$nevasenda_is_first = false;
				endwhile;
			else :
				?>
				<p>Todavía no hay entradas publicadas.</p>
				<?php
			endif;
			?>
		</div>

		<?php the_posts_pagination(); ?>
	</div>
</section>

<?php get_footer(); ?>
