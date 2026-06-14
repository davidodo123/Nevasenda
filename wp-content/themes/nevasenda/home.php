<?php
if ( ! defined( 'ABSPATH' ) ) exit;
get_header();
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Blog</h1>
		<p class="section-subtitle">Noticias, reportajes y consejos de senderismo</p>

		<div class="cards-grid">
			<?php
			if ( have_posts() ) :
				while ( have_posts() ) :
					the_post();
					?>
					<article class="card">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="card-img">
								<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail( 'medium_large' ); ?></a>
							</div>
						<?php endif; ?>
						<div class="card-body">
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<div class="card-excerpt"><?php the_excerpt(); ?></div>
							<a class="card-link" href="<?php the_permalink(); ?>">Leer más &rarr;</a>
						</div>
					</article>
					<?php
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
