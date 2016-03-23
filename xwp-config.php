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
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'admin');

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
define('AUTH_KEY',         'rqbV/BQlo7zt#!4MpGL1*@Nm/}C$8+AX/+B~ iFVT+H|#Z]F8}*+dKqU@V{mhACF');
define('SECURE_AUTH_KEY',  '8k~I/3O,Y_6auaSG1LM!58<sVpv)RJA|-}HlbI|&Xe^:E@]=,]k,T3 ph0K]h(53');
define('LOGGED_IN_KEY',    'cLN@P<Fq<3u4pD:0%Kp&q{GR+e8_?b],CC]MV4[L{G7G5Z+k@ )B09.b&1W@B?j:');
define('NONCE_KEY',        'TjQcnKm@SV;FvJ2BL&S;kA}4Ivd/!T6Y(iP=y@FZ?JG-iB83|q+%!()uk)qO0hlP');
define('AUTH_SALT',        '5C%8LR<9_>Mwf0R}+0Y/c=*EDr_52C#:=aKv~gAq|olwV]8.wX=iXi0sN*{#N>E;');
define('SECURE_AUTH_SALT', '%6wwx5&|9H17jIxfxz-uYc%D|$,Ji[?`H6-K9NJ^-biS5R,raH@dF[1|}aj}|1w;');
define('LOGGED_IN_SALT',   'N|A8+sNhvJtW!Ng|Vo()@}!`2faBqx|d_H-pHpH.mXrT|#vljZ/dJJb|tG;/5S+F');
define('NONCE_SALT',       'lFq}xi<iTuKw#mp^=QhwnEDT,/,.}sP*_Tqzd(Hb=Nayk<xC75r#0npw#6Z=mw9h');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
