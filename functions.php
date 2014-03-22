<?php
/*
  Plugin Name: WP-Config
  Version: 1
  Author: Quickshiftin http://quickshiftin.com
  Description: Advanced configuration system for Wordpress.
*/

register_activation_hook(function() {
    //------------------------------------------------------------
    // Migrate a legacy Wordpress wp-config.php file to the format used
    // by wpConfigure. Provision a blank wp-config-local.php and wp-config-local-example.php.
    // Leave a copy of wp-config.php in wp-config.php.bkup.
    //------------------------------------------------------------

    $sWebRoot = realpath(__DIR__ . '/../..');

    // Backup wp-config.php
    copy($sWebRoot . '/wp-config.php', $sWebRoot . '/wp-config.php.bkup');

    // Create the new wp-config.php
    ob_start();
    echo '<?php' . PHP_EOL;
    include 'wp-content/plugins/wpConfigure/wp-config-sample.php';
    file_put_contents($sWebRoot . '/wp-config.php', ob_get_clean());

    // Stub out wp-config-local.php
    copy($sWebRoot . '/wp-content/plugins/wpConfigure/wp-config-local.php', $sWebRoot . '/wp-config-local.php');

    // Copy over a wp-config-local-example.php file
    copy($sWebRoot . '/wp-content/plugins/wpConfigure/wp-config-local.php', $sWebRoot . '/wp-config-local-example.php');
});

register_deactivation_hook(__FILE__, function() {
    $sWebRoot = realpath(__DIR__ . '/../..');

    // Look for the original backup, bail if it's missing
    if(!file_exists($sWebRoot . '/wp-config.php.bkup'))
        die('No original configuration found at wp-config.php.bkup');

    // Restore the original configuration
    if(!move($sWebRoot . '/wp-config.php.bkup', $sWebRoot . '/wp-config.php'))
        die('Failed to restore wp-config.php from backup wp-config.php.bkup');

    // Nuke other assets from the plugin
    if(file_exists($sWebRoot . '/wp-config-local.php'))
        unlink($sWebRoot . '/wp-config-local.php');
    if(file_exists($sWebRoot . '/wp-config-local-example.php'))
        unlink($sWebRoot . '/wp-config-local-example.php');
});

/**
 * This is a tool you can use to stub out a new configuration
 * template. You may want to use the configuration system on
 * your own plugin or theme for example. This will help you
 * create the skeleton.
 */
function wpConfigureStub($sType, $sName)
{
    if($sType != 'plugin' && $sType != 'theme')
        return false;

    $sWebRoot = realpath(__DIR__ . '/../..');
    if($sType == 'plugin') {
        if(!is_dir($sWebRoot . '/wp-content/plugins/' . $sName))
            return false;
        copy(
            $sWebRoot . '/wp-content/plugins/wpConfigure/wp-config-local.php',
            $sWebRoot . '/wp-content/plugins/' . $sName . '-config-local.php');

        $sSampleCode = <<<CODE
\$aPluginConfig = wpConfigurePlugin(array(
    'production' => array(
     ),
    'staging:production' => array(
    ),
));
CODE;
    } else {

        if(!is_dir($sWebRoot . '/wp-content/themes/' . $sName))
            return false;
        copy(
            $sWebRoot . '/wp-content/plugins/wpConfigure/wp-config-local.php',
            $sWebRoot . '/wp-content/plugins/' . $sName . '-config-local.php');

        $sSampleCode = <<<CODE
\$aThemeConfig = wpConfigureTheme(array(
    'production' => array(
     ),
    'staging:production' => array(
    ),
));
CODE;
    }

    return $sSampleCode;
}
