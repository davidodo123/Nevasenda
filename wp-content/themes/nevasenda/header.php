<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
	<div class="container">
		<p class="site-branding">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Neva<span>senda</span></a>
		</p>

		<button class="menu-toggle" aria-label="<?php esc_attr_e( 'Abrir menú', 'nevasenda' ); ?>" aria-expanded="false">
			<span></span><span></span><span></span>
		</button>

		<?php
		wp_nav_menu( array(
			'theme_location' => 'primary',
			'container'      => false,
			'menu_class'     => 'primary-menu',
			'fallback_cb'    => 'nevasenda_fallback_menu',
		) );
		?>
	</div>
</header>
