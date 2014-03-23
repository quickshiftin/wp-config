<?php
/**
 * XXX I'm ashamed of this implementation, but it's v1.0, just trying
 *     to get something out the door!
 */
$wpConfigType = 'site';

/**
 * Configure a Wordpress plugin.
 *
 * $sPluginName must be the name of the plugin. Specifically the name
 * used for the plugin directory in the wp-content/plugins directory.
 *
 * $aWpConfig is optional, however it is required the first time you call
 * wpConfigure for a given $sConfigKey. Subsequent calls to wpCofigure for
 * the same key need not pass $aWpConfig. On subsequent calls, wpConfigure
 * will return the compiled configuration for $sConfigKey.
 *
 * $sPluginName string The name of the plugin to configure.
 * $aWpConfig array Raw configuration for the component.
 */
function wpConfigurePlugin($sPluginName, array $aWpConfig=array())
{
    global $wpConfigType;
    $wpConfigType = 'plugin';

    $result = wpConfigure($sPluginName, $aWpConfig);
    $wpConfigType = 'site';

    return $result;
}

/**
 * Configure a Wordpress theme.
 *
 * $sPluginName must be the name of the plugin. Specifically the name
 * used for the plugin directory in the wp-content/plugins directory.
 *
 * $aWpConfig is optional, however it is required the first time you call
 * wpConfigure for a given $sConfigKey. Subsequent calls to wpCofigure for
 * the same key need not pass $aWpConfig. On subsequent calls, wpConfigure
 * will return the compiled configuration for $sConfigKey.
 *
 * $sThemeName string The name of the theme to configure.
 * $aWpConfig array Raw configuration for the component.
 */
function wpConfigureTheme($sThemeName, array $aWpConfig=array())
{
    global $wpConfigType;
    $wpConfigType = 'theme';

    $result = wpConfigure($sThemeName, $aWpConfig);
    $wpConfigType = 'site';

    return $result;
}

/**
 * Configure Wordpress.
 * $aWpConfig is optional, however it is required the first time you call
 * wpConfigure for a given $sConfigKey. Subsequent calls to wpCofigure for
 * the same key need not pass $aWpConfig. On subsequent calls, wpConfigure
 * will return the compiled configuration for $sConfigKey.
 *
 * $sConfigKey string The key to identify which component to configure.
 * $aWpConfig array Raw configuration for the component.
 */
function wpConfigure($sConfigKey, array $aWpConfig=array())
{
    global $wpConfigType;
    static $aSiteConfigured = array();
    static $aConfig         = array();

    //----------------------------------------------------------------------------
    // Bail if the site has already been configured
    // The compiled config is cached in $aConfig so we just return that here
    //----------------------------------------------------------------------------
    if(isset($aSiteConfigured[$sConfigKey]))
        return $aConfig[$sConfigKey];

    //----------------------------------------------------------------------------
    // Bail if we have no raw config at this point. If we're here that means we
    // can't serve a result from cache, which means we need to build the config
    // for the given key, which means we need the raw config to build from...
    //----------------------------------------------------------------------------
    if(empty($aWpConfig))
        return false;

    //----------------------------------------------------------------------------
    // Try to read current envrionment from envrionment variable (Apache or CLI)
    //----------------------------------------------------------------------------
    $sServerEnv = getenv('APPLICATION_ENV');

    //----------------------------------------------------------------------------
    // Define the current env in APPLICATION_ENV
    // default is development (in case we find nothing in APPLICATION_ENV env var)
    //----------------------------------------------------------------------------
    if(empty($sServerEnv))
        define('APPLICATION_ENV', 'development');
    else
        define('APPLICATION_ENV', $sServerEnv);

    //---------------------------------------------------------------------
    // Bail if we're in development and there's no local configuration file
    //---------------------------------------------------------------------
    if($wpConfigType == 'site') {
        if(APPLICATION_ENV == 'development' &&
           !file_exists(__DIR__ . '/wp-config-local.php'))
            die('Please define a development configuration file. ' .
                'See example file local-config-example.php');
    } elseif($wpConfigType == 'plugin') {
        // Bail if the plugin path DNE
        $sPluginPath = __DIR__ . '/wp-content/plugins/' . $sConfigKey;
        if(!is_dir($sPluginPath))
            return false;

        if(APPLICATION_ENV == 'development' &&
           !file_exists($sPluginPath . '/' . $sConfigKey . '-config-local.php'))
            die('Please define a development configuration file. ' .
                'See example file local-config-example.php');
    } elseif($wpConfigType == 'theme') {
        // Bail if the theme path DNE
        $sThemePath = __DIR__ . '/wp-content/themes/' . $sConfigKey;
        if(!is_dir($sThemePath))
            return false;

        if(APPLICATION_ENV == 'development' &&
           !file_exists($sThemePath . '/' . $sConfigKey . '-config-local.php'))
            die('Please define a development configuration file. ' .
                'See example file local-config-example.php');
    } else {
        // Bail if unrecognized type
        die('Unrecognized wpConfigType: ' . $wpConfigType);
    }

    //------------------------------------------------------------
    // Only load development config if this is development
    // XXX This code has horrible duplication,
    //     it needs to be cleaned up.
    //------------------------------------------------------------
    $aEnvs['development'] = array();
    $sDevKey              = '';
    if(APPLICATION_ENV == 'development') {
        if($wpConfigType == 'site') {
            // A simple security measure, wp-config-local.php expects LOCAL_CONFIG to
            // be defined and set to true or it will exit
            define('LOCAL_CONFIG', true);

            // The array must have one key, 'development:<extends>', where <extends>
            // specifies the environment development extends
            $a = require __DIR__ . '/wp-config-local.php';
            if(!is_array($a))
                die('Invalid configuraiton file (1) ' . __DIR__ . '/wp-config-local.php');
            $aKeys = array_keys($a);
            if(count($aKeys) != 1)
                die('Invalid configuraiton file (2) ' . __DIR__ . '/wp-config-local.php');
            $sDevKey = array_shift($aKeys);
            if(strpos($sDevKey, 'development:') === false)
                die('Invalid configuraiton file (3) ' . __DIR__ . '/wp-config-local.php');
            $aDevData = $a[$sDevKey];
        } elseif($wpConfigType == 'plugin') {
            // A simple security measure, wp-config-local.php expects LOCAL_CONFIG to
            // be defined and set to true or it will exit
            define('LOCAL_CONFIG', true);

            // The array must have one key, 'development:<extends>', where <extends>
            // specifies the environment development extends
            $a = require $sPluginPath . '/' . $sConfigKey . '-config-local.php';
            if(!is_array($a))
                die('Invalid configuraiton file (1) ' . '/' . $sConfigKey . '-config-local.php');
            $aKeys = array_keys($a);
            if(count($aKeys) != 1)
                die('Invalid configuraiton file (2) ' . '/' . $sConfigKey . '-config-local.php');
            $sDevKey = array_shift($aKeys);
            if(strpos($sDevKey, 'development:') === false)
                die('Invalid configuraiton file (3) ' . '/' . $sConfigKey . '-config-local.php');
            $aDevData = $a[$sDevKey];
        } elseif($wpConfigType == 'theme') {
            // A simple security measure, wp-config-local.php expects LOCAL_CONFIG to
            // be defined and set to true or it will exit
            define('LOCAL_CONFIG', true);

            // The array must have one key, 'development:<extends>', where <extends>
            // specifies the environment development extends
            $a = require $sThemePath . '/' . $sConfigKey . '-config-local.php';
            if(!is_array($a))
                die('Invalid configuraiton file (1) ' . '/' . $sConfigKey . '-config-local.php');
            $aKeys = array_keys($a);
            if(count($aKeys) != 1)
                die('Invalid configuraiton file (2) ' . '/' . $sConfigKey . '-config-local.php');
            $sDevKey = array_shift($aKeys);
            if(strpos($sDevKey, 'development:') === false)
                die('Invalid configuraiton file (3) ' . '/' . $sConfigKey . '-config-local.php');
            $aDevData = $a[$sDevKey];
        }
    }

    // array_merge the various envionments into our final configuration
    // in this simple paradigm, declarations with a colon denote an 'extends'
    // relationship. the raw keys are those that declare an environment and one
    // it extends. <new-env>:<extends-env>
    $aRawEnvs = array_keys($aWpConfig);

    // Currently we only support a single base key, however in the future
    // we might introduce support for any number of base nodes.
    // Right now the base node needs to be the first one defined.
    $sBaseKey = array_shift($aRawEnvs);

    // A list of all the environments
    $aEnvKeys = array($sBaseKey);
    $aEnvs    = array($sBaseKey => $aWpConfig[$sBaseKey]);

    // If there is a development key place it into the list of raw keys
    if(!empty($sDevKey)) {
        $aRawEnvs[]           = $sDevKey;
        $aEnvKeys[]           = 'development';
        $aEnvs['development'] = $aDevData;
    }

    // A list of what extends what, where concrete environments are keys.
    // The enviornments they extend are values.
    $aExtends = array();

    //------------------------------------------------------------
    // Process the configuration tree
    //------------------------------------------------------------
    // Iterate over the raw environment keys building two lists,
    // the exlusive list of environments, $aEnvKeys, and the list of
    // what-extends-what, $aExtends
    foreach($aRawEnvs as $sKey) {
        // Ignore config block if we can't determine what it extends
        // @todo Consider other ways of dealing with this
        if(substr_count($sKey, ':') != 1) {
            trigger_error(
                "Cannot parse config key '$sKey', skipping",
                E_USER_NOTICE
            );
            continue;
        }

        // Load the name of the new environment and the one it extends
        list($env, $extends) = explode(':', $sKey);
        $aExtends[$env] = $extends;

        // Load the name of the new env into the list
        if($env != 'development') {
            $aEnvKeys[]  = $env;
            $aEnvs[$env] = $aWpConfig[$sKey];
        }

        // Ignore config block if the extends env doesn't exist
        // @todo Consider other ways of dealing with this
        if(!in_array($extends, $aEnvKeys)) {
            trigger_error(
                "Cannot extend unknown environment '$extends', skipping '$sKey'",
                E_USER_NOTICE
            );
            continue;
        }
    }

    //------------------------------------------------------------
    // bail if unrecognized environment
    // @note consider something more graceful here, but really it 
    //       should only happen during site setup
    //------------------------------------------------------------
    if(!in_array(APPLICATION_ENV, $aEnvKeys))
        throw new UnexpectedValueException(
            'There is no [' . APPLICATION_ENV . '] environment in the configuration');

    //------------------------------------------------------------
    // Produce flat list to merge final config from
    //------------------------------------------------------------
    // Since the base env needs to be the leftmost in an array_merge
    // but we know the current env, let's crawl the $aExtends array
    // from the bottom up to the base env
    $_sEnv  = APPLICATION_ENV;
    $aMerge = array($aEnvs[APPLICATION_ENV]);

    // As long as there is another env to extend from..
    while(isset($aExtends[$_sEnv])) {
        // Increment the environment stack
        $_sEnv    = $aExtends[$_sEnv];

        // Append the config for the new environment to
        // the final list of data to be merged into the final configuration
        $aMerge[] = $aEnvs[$_sEnv];
    }
    $aMerge[] = $aEnvs[$sBaseKey];
    $aMerge   = array_reverse($aMerge);

    //------------------------------------------------------------
    // Merge the env chain to produce the final configuration
    //------------------------------------------------------------
    $_aConfig = call_user_func_array('array_merge', $aMerge);

    //------------------------------------------------------------
    // Provision an entry in the cache for the new config key
    //
    // By now it's safe to provision an entry for $sConfigKey in
    // $aConfig, because we've made it through the raw config
    // processing from above. Doing it before processing the
    // config would allow garbage entries into the cache.
    //------------------------------------------------------------
    $aConfig[$sConfigKey] = array();

    //------------------------------------------------------------
    // Get the config for current env & define it, oldschool style
    //------------------------------------------------------------
    foreach($_aConfig as $k => $v) {
        //-----------------------------------------------------------------
        // Backreferences
        // support for using already defined components in other components
        // these are denoted by curly braces
        // @note consider strtr instead of preg_match_all here
        //-----------------------------------------------------------------
        if(preg_match_all('/\{.*?\}/', $v, $aMatches))
            foreach($aMatches[0] as $_sMatch) {
                // build the match witout the curly braces
                $sMatch = str_replace(array('{', '}'), array('', ''), $_sMatch);

                // if the value is an already defined constant, replace that portion of the
                // new constant before defining it
                if(defined($sMatch))
                    $v = str_replace($_sMatch, constant($sMatch), $v);
            }

        // finally, define the new constant
        define($k, $v);
        $aConfig[$sConfigKey][$k] = $v;
    }

    // indicate the site has been configured
    $aSiteConfigured[$sConfigKey] = true;

    return $aConfig[$sConfigKey];
}
