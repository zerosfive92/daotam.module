<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'tusachdaidao' );

/** MySQL database username */
define( 'DB_USER', 'quantri' );

/** MySQL database password */
define( 'DB_PASSWORD', 'quantri123456' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '7`jD5GA08CQI~Ih*8$8tdpU&2;ZMa~|+X>g1PwwfxzH5r5O/Sq_QQQ,kO%xFYDY[' );
define( 'SECURE_AUTH_KEY',  'x@h5c%PvDbItP0ywE%H{qWC_uK(iKM_RTux9ejM3s6#O3R]yhE@v{0%/<Z1VCW(v' );
define( 'LOGGED_IN_KEY',    '@9^OHiMJ`eh,:,?7!t%sF*Lfv/r-^Dkpc[TGm~k_hA%psd})k[X^83d<.~cVk){R' );
define( 'NONCE_KEY',        'J$o#EJtYyR,0n#l@iOl^f[tPj^!C?RTOutiwZY <Y-.c{-NZyC<h2)[6FlcI=+!6' );
define( 'AUTH_SALT',        's a)}nE=jvi3J&-gX9Jw32%$0_d>jGXRGI|@3>,Xz*5:loIy+/Q[:Q`Mhpi(LnMn' );
define( 'SECURE_AUTH_SALT', 'uv~}.FgdBXn2CAD9:R.nGUe4EV@;DjR1gStXDwr(HMt+b(F;ZfegX(-LEvY*0lN/' );
define( 'LOGGED_IN_SALT',   '/%q4_W4`;xW;D<O4:Z,FW84eZt])#U!4D741kj%bC[ `.O1=sIOot=zTDX?Gs3iv' );
define( 'NONCE_SALT',       '+AQB.BG++$hF fD/Tt^HJ%X)km[p-6z^m3#Qs):vQpAqLx*TN,x|.UyL|b[nO;H]' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'tsdd_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );

@ini_set('upload_max_size' , '256M' );
