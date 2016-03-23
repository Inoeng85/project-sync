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
define('DB_NAME', 'project_sync');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'admin');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ':0Gyfze!66g{__(j70SeoW?>!$70cwMX/D-ES&ain*gU!tr>4y*!*5wD+Xz:Rq50');
define('SECURE_AUTH_KEY',  '?yNg.bvv5[-FX1y)XL%LHp5hF}fGYfOr#1Jsjb;Vs5e^VdL6*^_pH7l!+0[2@?kg');
define('LOGGED_IN_KEY',    '!3Y+E=M9iJPc7%BiMEcFm&;8[$`*/LI?.Anw64);ehapq~YEoXY;IC7dN^9K/kWs');
define('NONCE_KEY',        'rVH|HyLr0pPI0BGgDsv]b(G;x/X2wES3L]`]N1D[D5G>e$e{WiV|1:Ha}+QAyRQ_');
define('AUTH_SALT',        '-*#LK|n;;3%XD|0IiG7c|:8`0P,`Yr@^4}iQ()2zSkXL:=ok=0x(ZrP5 @,o(0=`');
define('SECURE_AUTH_SALT', 's#-nQA)gmF[ueSG+9}@f{7V@<DR!.o/K@HpnP8r{j2nE3D90+rWp=9)>|!/grH(j');
define('LOGGED_IN_SALT',   'vF!B :/ f0wDATj0~l^R|&(ekB$ZjO0EN$5 *L|+}$gy3D 7S#He#X+jWt/Xty4<');
define('NONCE_SALT',       '6VU[;HK<50|4q=n_VvXSaEYEj56A5@mkY-&>2bc]jt]L:Oq<m^ezzH=,u=@@I#HT');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'noel001_';

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
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
