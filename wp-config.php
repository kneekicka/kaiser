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
define('DB_NAME', 'd02585be');

/** MySQL database username */
define('DB_USER', 'd02585be');

/** MySQL database password */
define('DB_PASSWORD', 'Uwa6wEtWWU8xGUU9wvAa');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

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
define('AUTH_KEY',         'KFD+5>)kxQFd4{@P2h`jnfGlCg*8Kl^u4$}F@)3;XW}jT?=d*zPubud(_l>,Yz!l');
define('SECURE_AUTH_KEY',  '|!5>)FW`{VKRtg9|cgsG_YM?GiH#eHncI%#OIY2G3[x)D3&)wxMXTxuzd?{Jfl01');
define('LOGGED_IN_KEY',    'o2_:in`yKY$nj*td$D>V?6R$SPM;>[J0k,HVx*RFbT6N[,5^NP7lego::>SVx{df');
define('NONCE_KEY',        '*/>#7nGvEZF!eX_6,s#n]p+t1[M,Y4oa@0bT|,LCeqDLL$`VubFTTOJd-vmd,^FI');
define('AUTH_SALT',        'l+{j5J0w>QFhT(@Lhe!>6K#ACn&LNKt=Tr*g?7aZg)D=.cgtIg4^56|U&%$SNvn$');
define('SECURE_AUTH_SALT', 'juj+/LdsR*3_kxT6agrt{pK#tMTDFEG3<3<I]}>2)OnqIE*-/z!,mK-cw`)?&MO@');
define('LOGGED_IN_SALT',   '_jg>i(MpU>d|ak+mh+wVT%;MdBkhm=+N)pjov9aMTFK84S(y]|Q3$k_g/KPzfYJm');
define('NONCE_SALT',       'K6^CMAn$e<;wn@rL?F^B3>+@hY<O5d?^xz#H=pJ$.dyy%nHC7(cHDlf.G#[Yfx9a');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'r3RSz_';
define( 'WP_POST_REVISIONS', 10 );

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