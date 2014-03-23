Overview
========
`wpConfigure` brings support for multiple environments, like *development*, *staging* and *production* to WordPress.

Multi-environment support
-------------------------
The main thing `wpConfigure` does is allow you to define seperate configuration values for different environments. Your `wp-config.php` file probably looks something like this right now
```php
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'mydb_wp');

/** MySQL database username */
define('DB_USER', 'my_app_db_user');

/** MySQL database password */
define('DB_PASSWORD', 'my_app_db_pass');

/** MySQL hostname */
// define('DB_HOST', '10.180.14.138'); // production ...
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');
```

Notice the commented out line for the production database IP address. There's no support for multiple environments in Wordpress. Let's take a quick look at how `wpConfigure` comes to the rescue.

```php
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
```

This is just an example. The development configuration actually lives outside of `wp-config.php`, so it can live outside of your SCM. This makes it possible to keep `wp-config.php` under version control and the same for all sites! [Read more](https://github.com/quickshiftin/wp-env-config#scm-conscience) about development configuration below.

Setting the environment
-----------------------
Now that you're able to have different configurations for different environments, you need to tell `wpConfigure` which environment it's in. In order to do that you need to pass the `APPLICATION_ENV` [environment variable](http://www.php.net/manual/en/reserved.variables.environment.php). You can do this in different ways depending on how you're running php.

**IMPORTANT: You must configure `APPLICATION_ENV` *before* you can activate the wpConfigure plugin.**

**Apache**
```
SetEnv APPLICATION_ENV "development"
```
**Nginx (php-fpm)**
```
env[APPLICATION_ENV] = development
```
**Nginx (CGI)**
```
location / {
    fastcgi_param APPLICATION_ENV development; 
}
```
**IIS**
```xml
<serverVariables>
    <set name="APPLICATION_ENV" value="development" />
</serverVariables>
```
**SHELL**
```bash
export APPLICATION_ENV=development
```


Inheritance
-----------
You can see how `wpConfigure` provides support to define configuration for each environment. Each key in the array becomes a constant with the value from the array. Here we show a contrived example with two environments, **development** and **production**. In this case **development** *extends* **production**. That means you only need to define things in **development** that are *different* than **production**.
Currently `wpConfigure` only supports one *base* environment, which is an environment with no parent. All the other environments must specify a parent. There are two ways to define environments

**Base Environment**

`<environment>`

**Child Environment**

`<child-environment>:<parent-environment>`

Look back at [Multi-environment support](https://github.com/quickshiftin/wpConfigure#multi-environment-support) for an example.

SCM-Conscious
-------------
Development configurations can vary between programmers. Imagine two people developing a WordPress site, one using Windows, the other using OSX. There's a good chance there will be some differences in their configuration. They don't want to step on each others toes. `wp-config.php` is under version control, so if each developer wants to modify it for their development site, they have to remember not to commit it.
`wpConfigure` handles this by allowing for a `wp-config-local.php` file to define the ***development*** environment. By configuring your SCM to ignore this file, developers can each have their own `wp-config-local.php` file outside of version control.

Backreferences
--------------
Backreferences allow you to use previous configuration entries to compose new configuration entries. Given an existing configuration value like `DB_NAME`, you can define a new value such as `DB_NAME_LABEL`, embedding `DB_NAME` by defining it in the following manor
```php
'DB_NAME_LABEL' => 'DB Name: {DB_NAME}',
```
So if `DB_NAME` is **'mydb_wp'** like the above example, `DB_NAME_LABEL` would become **'DB Name: mydb_wp'**. This feature is usefull when you want to build a set of configuration values that are paths, where subsequent values aggregate previous values.

Theme & Plugin Support
----------------------
Beyond database configuration, there might not be a whole lot of other values to enter in your project's `wp-config.php` file. `wpConfigure` is readily available for theme and plugin development too! This is where you might find some more milage because now you can easily configure your theme and plugin code for multiple environments.
To configure a module all you need to do is come up with an identifier (usually based on the theme or plugin name).
```php
wpConfigurePlugin('my-plugin', array(
  'production' => array(
    'MY_PLUGIN_CONFIG_VAR' => 'Production value',
    'MY_PLUGIN_SECOND_VAR' => 'Common value',
  ),
  'development:production' => array(
    'MY_PLUGIN_CONFIG_VAR' => 'Development value',  
  )
));
```
The first argument has to the name of your theme, specifically the name of the folder for your theme in the plugins directory. To configure a theme, the same rules apply, the only difference is you call `wpConfigureTheme`.
```php
wpConfigureTheme('my-theme', array(
  'production' => array(
    'MY_THEME_CONFIG_VAR' => 'Production value',
    'MY_THEME_SECOND_VAR' => 'Common value',
  ),
  'development:production' => array(
    'MY_THEME_CONFIG_VAR' => 'Development value',  
  )
));
```

Backwards Compatibility
-----------------------
You may still be wondering how to get access to the new configuration values. The system is backwards compatible with how WordPress works today. That means all the values you define (the keys in the arrays for each environment) become constants. So if you have a configuration like in the [**Theme & Plugin Support**](https://github.com/quickshiftin/wp-env-config/blob/master/README.md#theme--plugin-support) example, `MY_PLUGIN_CONFIG_VAR` will be available as a constant. Its value depends on the running environment, [set by `APPLICATION_ENV`](https://github.com/quickshiftin/wpConfigure#setting-the-environment).

The cool thing is your configuration is also available as an array which might prove handy since you can perform array operations on the entire configuration now. In order to get ahold of the configuration as an array, just assign the result of `wpConfigure` to a variable. Going back to our **Theme & Plugin Support** example

```php
$myPluginConfig = wpConfigure('my-plugin', array(
  'production' => array(
    'MY_PLUGIN_CONFIG_VAR' => 'Production value',
    'MY_PLUGIN_SECOND_VAR' => 'Common value',
  ),
  'development:production' => array(
    'MY_PLUGIN_CONFIG_VAR' => 'Development value',  
  )
));
```
After the above code runs, the following expression evaluates to `true`
```php
MY_PLUGIN_CONFIG_VAR === $myPluginConfig['MY_PLUGIN_CONFIG_VAR']
```

Caching
-------
`wpConfigure` only *compiles* configuration for a given key the first time it runs for a given key. Subsequent invocations of `wpConfigure` for the same key result in cached responses from `wpConfigure`. This makes it fast and also has the effect of making the configuration array globally accessible. You don't have to pass the raw configuration array on cache-calls to `wpConfigure`, so after you've run it the first time, you can get the same configuration anywhere like so
```php
$myPluginConfig = wpConfigure('my-plugin');
echo $myPluginConfig['MY_PLUGIN_CONFIG_VAR'];
```
With php 5.4+ you can immediately access the value from the function call. Note this is slow, so save to an variable if you plan to access a lot of variables from the array.
```php
echo wpConfigure('my-plugin')['MY_PLUGIN_CONFIG_VAR'];
```

API
===
```php
/**
 * Configure a component of WordPress.
 * $aWpConfig is optional, however it is required the first time you call
 * wpConfigure for a given $sConfigKey. Subsequent calls to wpCofigure for
 * the same key need not pass $aWpConfig. On subsequent calls, wpConfigure
 * will return the compiled configuration for $sConfigKey.
 *
 * $sConfigKey string The key to identify which component to configure.
 * $aWpConfig array Raw configuration for the component.
 */
function wpConfigure($sConfigKey, array $aWpConfig=array())
```
```php
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
```
```php
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
```
Motivation
==========

If you've ever written a webapp using a framework before you are familiar with the notion of different *environments* for a given application. Typically developers have a local installation of the site, then you may have a pre-production site to demo features to clients and iron out bugs, and hopefully a separate production environment where the live site is deployed.

WordPress really suffers for lack of tools in this area. One thing I find is a desire for multi-environment support. Better yet, I don't want to re-define the same value for each environment if it's the same for all environments. Some other things would be nice, like access to the configuration as an array, and support to use the configuration system for more than just WordPress itself.

About
=====
`wpConfigure` is a relatively simple configuration system compared to the kinds of systems used in frameworks like Symfony and Zend Framework et al. It strikes a balance between complexity and robustness that I hope Wordpress users will find enticing.
