<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'b}(<wmlf^RS3jV8<VV-$2P}Y6+w<nJl@my$p<!ak4~E=G,=1KWe,PGSA0#rtuKOQ' );
define( 'SECURE_AUTH_KEY',   'ScI^D OseUs)Wwg w_{RwN$:vs(yu+0~q{8/[-_h|K ;bfdrYc)Uvab^:X(VbjlK' );
define( 'LOGGED_IN_KEY',     'cS*keQbnCGRzm#k9S.?{+>16J%S@Exir.KZEz/3 ;16vBzsDKuV(?.s5%Eh;,b_l' );
define( 'NONCE_KEY',         'bc!`)AlASZQic-rTYig<88tn`**[A&kg&53@xU7QZ~FrSf ~Wp?Y%g/(MJt+U9`q' );
define( 'AUTH_SALT',         'aif V!9O6|(,7y@y&Em{97Yxd8AtM!TtN$2UKSgLAu n?OUY4r&XsjTiI@Qs/jrN' );
define( 'SECURE_AUTH_SALT',  'CqtW*SjbOT5PK<R-&*MNb]53bknL}f`9Uw2gLc%{>x:5TVeSU]8 abTY0(Uumw$$' );
define( 'LOGGED_IN_SALT',    '[l=x_85]RVk;,wmTK<HRQGlX}GSnkQ#.Hh1*(M68K)KK/S=m~&`v{}EM3l<0tO1I' );
define( 'NONCE_SALT',        '+)za`ND@(oc$oh}~^JUmjbqH]`<LVtim46(,Kq 3yQ,VJ:|3iVlpnhk.;rlWQ=vj' );
define( 'WP_CACHE_KEY_SALT', '8?N+&f`{^m7Mi),D:xA>@A/,]6!]_(r.t9j NVfl!?)J]:6u]9yyO~8i:Q]<~.#|' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
