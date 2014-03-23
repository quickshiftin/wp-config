=== wpConfigure ===
Contributors: quickshiftin
Tags: WordPress Plugin, congiruation, environments, config
Requires at least: 2.5
Tested up to: 3.5.1
Stable tag: 1.0

== Description ==
`wpConfigure` brings support for multiple environments, like *development*, *staging* and *production* to WordPress.
It has a number of other advanced features for your configuration, but isn't overwhelming to understand. You can take
your wp-config.php files from something like this

define('DB_NAME', 'mydb_wp');
define('DB_USER', 'my_app_db_user');
define('DB_PASSWORD', 'my_app_db_pass');
// define('DB_HOST', '10.180.14.138'); // production ...
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

to something like this

wpConfigure('site', array(
  'production' => array(
    'DB_NAME'     => 'mydb_wp',
    'DB_USER'     => 'my_app_db_user',
    'DB_PASSWORD' => 'my_app_db_pass',
    'DB_HOST'     => '10.180.14.138',
    'DB_CHARSET'  => 'utf8',
    'DB_COLLATE'  => ''
    ),
  'development:production' => array(
    'DB_HOST' => 'localhost'
  )
));

and much more! Read the full documentation at the plgugin homepage http://quickshiftin.com/software/wp-configure/

== Installation ==

To install the plugin, all you have to do is upload it to your site and activate it. The wpConfigure plugin will try
to find your site's wp-config.php file during activation. If it can't you can go to the Tools -> WpConfigure part of
the admin after activation. This page will have the contents you can paste into wp-config.php yourself. Make sure to
backup wp-config.php to wp-config.php.bkup before pasting in the new version though!
