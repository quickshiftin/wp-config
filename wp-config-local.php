<?php
// Some simple security to prevent direct access
if(!defined('LOCAL_CONFIG') || LOCAL_CONFIG !== true)
    die();

// Return the configuration for development
// @note The key defines the environment development extends from
return array(
    'development:staging' => array(
));