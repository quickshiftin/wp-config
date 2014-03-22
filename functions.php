<?php
/*
  Plugin Name: WP-Config
  Version: 1
  Author: Moxune LLC
  Description: Advanced configuration system for Wordpress.
*/

register_activation_hook(function() {
    // Copy config.php to the webroot

    // Copy config-local-example.php to the webroot
});


/**
 * A function to convert an existing wp-config.php file
 * to the new format using WP-Config.
 * Leave a backup of the original wp-config.php.
 */
function config_port_site()
{

}

/**
 * This is a tool you can use to stub out a new configuration
 * template. You may want to use the configuration system on
 * your own plugin or theme for example. This will help you
 * create the skeleton.
 */
function stub_config()
{

}
