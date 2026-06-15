<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_user_logged_in() ) {
	wp_safe_redirect( home_url( '/' ) );
	exit;
}

get_header();

$panel       = ( isset( $_GET['panel'] ) && 'register' === $_GET['panel'] ) ? 'register' : 'login';
$error       = isset( $_GET['error'] ) ? sanitize_text_field( wp_unslash( $_GET['error'] ) ) : '';
$redirect_to = isset( $_GET['redirect_to'] ) ? sanitize_text_field( wp_unslash( $_GET['redirect_to'] ) ) : home_url( '/' );
?>

<section class="section">
	<div class="container">
		<h1 class="section-title">Mi cuenta</h1>
		<p class="section-subtitle">Inicia sesión o crea una cuenta pa dejar tu opinión sobre las rutas</p>

		<div class="auth-card">
			<div class="auth-tabs">
				<a href="<?php echo esc_url( add_query_arg( 'panel', 'login' ) ); ?>" class="auth-tab<?php echo 'login' === $panel ? ' is-active' : ''; ?>">Iniciar sesión</a>
				<a href="<?php echo esc_url( add_query_arg( 'panel', 'register' ) ); ?>" class="auth-tab<?php echo 'register' === $panel ? ' is-active' : ''; ?>">Crear cuenta</a>
			</div>

			<?php if ( $error ) : ?>
				<p class="auth-error"><?php echo esc_html( $error ); ?></p>
			<?php endif; ?>

			<?php if ( 'login' === $panel ) : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="auth-form">
					<input type="hidden" name="action" value="nevasenda_login">
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
					<?php wp_nonce_field( 'nevasenda_login', 'nevasenda_login_nonce' ); ?>
					<p><label for="login_email">Email</label><input type="email" id="login_email" name="email" required></p>
					<p><label for="login_password">Contraseña</label><input type="password" id="login_password" name="password" required></p>
					<button type="submit" class="btn">Entrar</button>
				</form>
				<p class="auth-switch">¿Todavía no tienes cuenta? <a href="<?php echo esc_url( add_query_arg( 'panel', 'register' ) ); ?>">Regístrate</a></p>
			<?php else : ?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="auth-form">
					<input type="hidden" name="action" value="nevasenda_register">
					<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
					<?php wp_nonce_field( 'nevasenda_register', 'nevasenda_register_nonce' ); ?>
					<p><label for="reg_nombre">Nombre</label><input type="text" id="reg_nombre" name="nombre" required></p>
					<p><label for="reg_email">Email</label><input type="email" id="reg_email" name="email" required></p>
					<p><label for="reg_password">Contraseña</label><input type="password" id="reg_password" name="password" minlength="6" required></p>
					<button type="submit" class="btn">Crear cuenta</button>
				</form>
				<p class="auth-switch">¿Ya tienes cuenta? <a href="<?php echo esc_url( add_query_arg( 'panel', 'login' ) ); ?>">Inicia sesión</a></p>
			<?php endif; ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>
