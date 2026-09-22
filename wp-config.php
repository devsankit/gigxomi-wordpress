<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'blogdb' );

/** Database username */
define( 'DB_USER', 'bloguser' );

/** Database password */
define( 'DB_PASSWORD', 'strongpassword' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         ']Dh~BG}|m#t|s4gF=X:-DS pZ/s363<Kh1.wIX-)$k+#S6wwfQ-=5-8L`, cFJJy' );
define( 'SECURE_AUTH_KEY',  'zT;]zSygoa/0zAoM|*}>L~C[2/0&NN&k4+raSLd}PISHB?{uQp~t<{|97;6GrHl.' );
define( 'LOGGED_IN_KEY',    '24b+ezwSmW|1;[k}n#@._*V]6<3.X@D|$*{7HUP#N:`(yMr`3fj~{r}3TJ/`fH=F' );
define( 'NONCE_KEY',        'vb)W]gxy(]],%M,2Iao0RX-wo21Latruc4M[,4AA[tUQi*XK-OJRYea$}.xbt/a~' );
define( 'AUTH_SALT',        ')/5P xul@+peMD2ro8Q72m)_Q#72wz$VKnMd)s]xA$`7Ul,3y^&2}j>h&v`v3e<q' );
define( 'SECURE_AUTH_SALT', 'su6l6~jl9?{XMI1N|Z0S~ucASITmdIf1q7FXBM95q2Rfy^=uBfn!AE1gKsJGmY%!' );
define( 'LOGGED_IN_SALT',   'y5+Svp!kHv1+G*OF/?)(N2/iSwRdE3|d-Kc~1 u+:q.+gEV^|9]m~[?:TKAG;-+(' );
define( 'NONCE_SALT',       '-Es51tDaN&)Apd?7zK::]G0k|46AEu$uWGpUk}fqvA3a;t*c8mk?ii4v#<IM]7mi' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
