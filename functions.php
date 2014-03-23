<?php
/*
  Plugin Name: wpConfigure
  Plugin URI: http://quickshiftin.com/software/wp-configure
  Version: 1.1
  Author: quickshiftin 
  Author URI: http://quickshiftin.com/about
  Description: Advanced configuration system for Wordpress.
  License: GPL v3
*/
function wpConfigureFindConfig()
{
    $sWebRoot    = realpath(__DIR__ . '/../../..');
    $sConfigRoot = $sWebRoot;

    // Look for wp-config.php in the typical location
    $sConfigPath = $sWebRoot . '/wp-config.php';
    if(!file_exists($sConfigPath)) {
        // If it doesn't exist, look one level above the webroot,
        // as some people move wp-config.php up on level
        // If that doesn't exist, then the user will have to tell us where wp-config.php is
        $sConfigPath = $sWebRoot . '/../wp-config.php';
        $sConfigRoot = realpath($sWebRoot . '/..');
        if(!file_exists($sConfigPath))
            return false;

    return array($sWebRoot, $sConfigRoot, $sConfigPath);
}

function wpActivaationMessage($sType, $sMessage)
{
    echo '<div class="'. $sType . '"><p>' . $sMessage . 
        '</p></div><!-- /.updated -->';
}

function wpActivationErrorMessage($sMesssage)  { return wpActviationMessage('error', $sMessage);  }
function wpActivationUpdateMessage($sMesssage) { return wpActviationMessage('update', $sMessage); }

//------------------------------------------------------------
// Migrate a legacy Wordpress wp-config.php file to the format
// used by wpConfigure. Provision a blank wp-config-local.php
// and wp-config-local-example.php.
// Leave a copy of wp-config.php in wp-config.php.bkup.
//------------------------------------------------------------
register_activation_hook(__FILE__, function() {
    global $table_prefix;

    //------------------------------------------------------------
    // Try to determine the location of wp-config.php, bail early
    // if we can't.
    //------------------------------------------------------------
    $aConfigInfo = wpConfigureFindConfig();
    if($aConfigInfo === false) {
        wpActivationUpdateMessage(
            'Could not automaitcally migrate wp-config.php, see instructions for manual installation.');
        return;
    }

    //------------------------------------------------------------
    // Bail if APPLICATION_ENV cannot be detected. This has to be
    // configured first, so the site loads after the new
    // wp-config.php file has been generated.
    //------------------------------------------------------------
    if(getenv('APPLICATION_ENV') === false) {
        die('APPLICATION_ENV environment variable is not defined, see instructions for configuring it.');
        return;
    }

    //------------------------------------------------------------
    // Automatic generation of wp-config.php in the wpConfigure
    // format.
    //------------------------------------------------------------
    list($sWebRoot, $sConfigRoot, $sConfigPath) = $aConfigInfo;

    // Backup wp-config.php
    if(!copy($sConfigPath, $sConfigRoot . '/wp-config.php.bkup'))
        die('Failed to backup original config.');

    // Create the new wp-config.php
    ob_start();
    echo '<?php' . PHP_EOL;
    require __DIR__ . '/wp-config-sample.php';
    file_put_contents($sConfigRoot . '/wp-config.php', ob_get_clean());

    // Stub out wp-config-local.php
    copy(__DIR__ . '/wp-config-local.php', $sConfigRoot . '/wp-config-local.php');

    // Copy over a wp-config-local-example.php file
    copy(__DIR__ . '/wp-config-local.php', $sConfigRoot . '/wp-config-local-example.php');
});

//------------------------------------------------------------
// Restore the original congiguration file and nuke extra
// files that we added during activation.
//------------------------------------------------------------
register_deactivation_hook(__FILE__, function() {
    //------------------------------------------------------------
    // Try to determine the location of wp-config.php, bail early
    // if we can't.
    //------------------------------------------------------------
    $aConfigInfo = wpConfigureFindConfig();
    if($aConfigInfo === false)
        return false;

    list($sWebRoot, $sConfigRoot, $sConfigPath) = $aConfigInfo;

    // Look for the original backup, bail if it's missing
    if(!file_exists($sConfigRoot . '/wp-config.php.bkup'))
        die('No original configuration found at wp-config.php.bkup');

    // Restore the original configuration
    if(!rename($sConfigRoot . '/wp-config.php.bkup', $sConfigRoot . '/wp-config.php'))
        die('Failed to restore wp-config.php from backup wp-config.php.bkup');

    // Nuke other assets from the plugin if they exist
    if(file_exists($sConfigRoot . '/wp-config-local.php'))
        unlink($sConfigRoot . '/wp-config-local.php');
    if(file_exists($sConfigRoot . '/wp-config-local-example.php'))
        unlink($sConfigRoot . '/wp-config-local-example.php');
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

    $aConfigInfo = wpConfigureFindConfig();
    if($aConfigInfo === false)
        return false;

    list($sWebRoot, $sConfigRoot, $sConfigPath) = $aConfigInfo;

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
