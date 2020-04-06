<?php
namespace Pickle\Tools;

class Config{

    static $host = null;
    static $username = null;
    static $password = null;
    static $database = null;


    static $CacheDirectory = "temp";//the directory where the cache will be stored

    static $env = 'PROD';//DEV or PROD or RESTRICTED

    static $devmail = null;

    static $release = 1;

    static $website = null;

}

Config::$host = $GLOBALS['settings']['host'];
Config::$username = $GLOBALS['settings']['username'];
Config::$password = $GLOBALS['settings']['password'];
Config::$database = $GLOBALS['settings']['database'];

Config::$website = $GLOBALS['settings']['site'];
