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
define('DB_NAME', 'voberhat');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'Fg0,XzHRzHyXL6)@&CutkM/49GV_@]Y!mCDzMN)5h6K_~gGQCs6denf0I_EiqP}9');
define('SECURE_AUTH_KEY',  'Kl{LDV,0W@7.u$7poHBs,aE~g{$NbrN05muVA6m|hl-Z{mAhY_ka&_#As3FGMZ,K');
define('LOGGED_IN_KEY',    'CEqqJkB4k(!e1|*+($fwRfXjgg(b6N|mE_dy*5oVp,]QL <ZFTDK$eGcHjSb]Cik');
define('NONCE_KEY',        'aLR%tOMDN?)7tU;WD/h<c?1=x-p-FuM{y4}&FtL&Ri;B<G59<,>-S)d=_t;hpMt}');
define('AUTH_SALT',        'M;qw1@l;GTEOQp60zG->O?Q&0rwbwFI3RBIl0tXFPozcEq_H#FN/6#yP/1 ;Ugb<');
define('SECURE_AUTH_SALT', 'Qf3.T g.(MoCD0E=;qhkVL+rZ((xg{-Ufm[Nq:DSN{Bbi&)j|(cPsb,E5WdZ(l6K');
define('LOGGED_IN_SALT',   ':$5Lz@gYcU<E`$B)+v&3rxtpjE5o{CRW&fMMLw5:9(7>j}W`FmY#MfgOP.<m`HE9');
define('NONCE_SALT',       'R<T}TK;wn~l:V$c8aXJx.FQ(!7h;^YBgzOycSvkn_rb!bQa+rpbx3TE-a(~T50V#');

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
