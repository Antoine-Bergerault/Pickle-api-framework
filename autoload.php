<?php

if(!defined('ROOT')){
    define('ROOT', __DIR__);
    require ROOT.'/vendor/autoload.php';
    $settings = \Yalesov\Yaml\Yaml::parse(file_get_contents(ROOT.'/environment.yml'));
    $GLOBALS['settings'] = $settings;
}

require('core/Tools/config.php');//load the configuration
require('core/App/Cache.php');//load cache
require('core/App/App.php');//load app
require('core/Router/Class/Route.php');//class for the routes
require('core/Router/Class/Router.php');//class for the router
require('core/Router/web.php');//assign the url
require('core/Tools/function.php');//functions