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

    static $release = 43;

    static $name = 'Coursonline';
    static $website = null;

    static $google_id = '414856425179-l1d7acq99k5bft0j01vgrq3d2ik9p9ik.apps.googleusercontent.com';
    static $google_secret = 'ZISgXQhlzLfrEw0IwakKCEN4';

    static $twitter = null;

}

Config::$host = $GLOBALS['settings']['host'] ?? 'localhost';
Config::$username = $GLOBALS['settings']['username'] ?? 'main_u';
Config::$password = $GLOBALS['settings']['password'] ?? 'ktO20^y4';
Config::$database = $GLOBALS['settings']['database'] ?? 'test_coursonline';

Config::$website = $GLOBALS['settings']['site'] ?? 'https://coursonline.iconia.dev';