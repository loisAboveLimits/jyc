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

$autoload = __DIR__ . '/vendor/autoload.php';

if (file_exists($autoload)) {
    require_once $autoload;

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

define('DB_NAME', $_ENV['DB_NAME'] ?? 'wordpress');
define('DB_USER', $_ENV['DB_USER'] ?? 'wordpress');
define('DB_PASSWORD', $_ENV['DB_PASSWORD'] ?? '');
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');

define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

// // ** Database settings - You can get this info from your web host ** //
// /** The name of the database for WordPress */
// define( 'DB_NAME', 'jycdb' );

// /** Database username */
// define( 'DB_USER', 'adminuser' );

// /** Database password */
// define( 'DB_PASSWORD', 'admin_003' );

// /** Database hostname */
// define( 'DB_HOST', 'localhost' );

// /** Database charset to use in creating database tables. */
// define( 'DB_CHARSET', 'utf8mb4' );

// /** The database collate type. Don't change this if in doubt. */
// define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         '(~fG55vU}o^<)aQq.V#Rc~W70hP>^XaqMALjg(YbYKA6RE<_<5wiQaA{y=o*UB5x' );
define( 'SECURE_AUTH_KEY',  'QNo5YRg1&&:R~h*ylnV*CK|O]$,zu`lGqFiG+gxp!XEvUK&:]=6Oz<HM,~fe1kMI' );
define( 'LOGGED_IN_KEY',    ';cc/WN$W5?@Q$&KZD4s oepW-Z$sa0D ?pF:Ej~9IJ*:ObmeDGXVd~_cQf8;k}e3' );
define( 'NONCE_KEY',        'k{ee7fa0kixIsg2I[h!HKGm?+Loy=(Tq~&vKU7X1ZhR7oyEH&W|RX;PNBA0^{Wx:' );
define( 'AUTH_SALT',        '8]l1f,&/m_Pi;YQJGnM4+vE^42s;ktCDD-[<g_LybTV9:&pl}:X}sBhG8X58af_e' );
define( 'SECURE_AUTH_SALT', '/o?}~0SC:ewSr--ind)E[Y#[jqoz{hHv!RM|89:d&y,xmuc6t*GRK~Z3Zc+#mBjD' );
define( 'LOGGED_IN_SALT',   ')9.79s1KrK6nZCc{10fzI!KGH^>@_?@H-,hlMs%Q^6}w>#$8WP%#Yp*Vi&zR`OwY' );
define( 'NONCE_SALT',       'l}TPt!BTR;GnJQnI9rSwnCG~c#T[%[`EN4_*QH90>GsJ|i8XY?hHfO?/hmQb+raZ' );

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
$table_prefix = 'SERVMASK_PREFIX_';


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

define('WP_HOME', $_ENV['WP_HOME'] ?? 'http://localhost');
define('WP_SITEURL', $_ENV['WP_SITEURL'] ?? 'http://localhost');

define('WP_DEBUG', filter_var($_ENV['WP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_LOG', filter_var($_ENV['WP_DEBUG_LOG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('WP_DEBUG_DISPLAY', filter_var($_ENV['WP_DEBUG_DISPLAY'] ?? false, FILTER_VALIDATE_BOOLEAN));

// define('WP_DEBUG', true);
// define('WP_DEBUG_LOG', true);
// define('WP_DEBUG_DISPLAY', false);

/* Add any custom values between this line and the "stop editing" line. */

define('FS_METHOD', 'ssh2');
define('WP_MEMORY_LIMIT', '512M');
define('WP_MAX_MEMORY_LIMIT', '512M');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
