<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

require_once __DIR__ . '/config.php';
wpConfigureSite(array(
    'production' => array(
        'DB_NAME'         => 'chirp',
        'DB_USER'         => 'chirp_admin',
        'DB_PASSWORD'     => '^Ch1rPa4m1n$',
        'DB_HOST'         => 'localhost',
        'DB_CHARSET'      => 'utf8',
        'DB_COLLATE'      => '',
        'WP_CLI_API_USER' => 'nathan',
        'WP_CLI_API_PASS' => 'L1ghtn1ng',
        'WP_CLI_API_URL'  => 'https://chirpsystems.com',
    ),

    'preprod:production' => array(
        'DB_NAME'        => 'chirp_2013',
        'DB_USER'        => 'chirp_2013_admin',
        'DB_PASSWORD'    => '$Ch1Rp2013^',
        'WP_CLI_API_URL' => 'http://chirp-2013.moxune.net',
    ),

    'int:preprod' => array(
        'DB_NAME'        => 'chirp_2013_int',
        'DB_USER'        => 'chirp_2013_int',
        'WP_CLI_API_URL' => 'http://int.chirp-2013.moxune.net',
    ),

    //------------------------------------------------------------
    // development configuration loaded from wp-config-local.php
    // Copy from wp-config-local-example.php to get
    // started.
    //------------------------------------------------------------
));

//------------------------------------------------------------
// For WP CLI API, the server side executor needs to fake out
// wp-config.php, by setting an arbitrary APPLICATION_ENV
// first. In order for that to work, we need a way to indicate
// to wp-config.php when that particular situation arises, so
// it can return after just processing the environmental
// component of the configuration.
//------------------------------------------------------------
if(defined('WP_CLI_LOAD_ONLY') && WP_CLI_LOAD_ONLY === true)
    return;

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '<<|>8U<-BjgG1Kt-``w[C{,>1S)(?6JG_i4,Ve}=V=Jh<qg#8)V0ouA-pB-]/h&[');
define('SECURE_AUTH_KEY',  '!?.<Y|C?3^o|L#q17[/&qw+WU%42;cy+_#W:4gvx-|N_VmK|z>_8|N7,}B:yBQrO');
define('LOGGED_IN_KEY',    '&Q7u`MK>`DN|C+2@.d+[a*tkEH?:[OHhxJ7g`XiRDkl+.$0mJ!reQBL21qpt1 BN');
define('NONCE_KEY',        'F}rMTzfJLm22;FNV(|i)5-1rR9kUHoiVMtEWSKc<VbHKCkAh x{j8X|1`BPDg93|');
define('AUTH_SALT',        '$K-!Fu|2UM8FEh-QUwxL~SVcZDhba5,jMbz`a?d]jSzJ)s&q]A`X&;sgo>|dvDu2');
define('SECURE_AUTH_SALT', '|7]w1 4kBP-Y#!3&+}2;^[ AshGnmpL[$Y0`!Y{|p!.K^@DZx*T4%,B-Yc;/xfN|');
define('LOGGED_IN_SALT',   '%,eik?PI*`]H[GT .DI|q;5szC)jBY|VD]LP20#MDofLE;RPd&M/4;(O( `WE8f+');
define('NONCE_SALT',       'ctbX`+(tA}<ilXwf])r</zdJb-d~#(DE8@ni^9_lPXf`FfOv@l-M1|b{UC2w-N#`');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
