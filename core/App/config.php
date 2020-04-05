<?php

class AppConfig{

    static $uses = [
        /**
         * Class(es)
         */
        'Session' => 'default/Session',

        /**
         * Trait(s)
         */
        'UserManagement'  => 'default/UserManagement',
        'SessionChecker'  => 'SessionChecker',
        'StyleManagement' => 'StyleManagement'
    ];

    static $cacheclass = 'default/Cache';

    static function load(){

        foreach(self::$uses as $class => $file){
            require "Components/$file.php";
        }

    }

    static function cache(){
        $c = self::$cacheclass;
        require "Components/$c.php";
    }

    static $access = true;
    static $access_token = '9JS2998S7GS92GS72S28801SH82';

    static $accessElements = ['user', 'lesson', 'quizz|quizz', 'opinion'];
    static $search_url = '/search';
    static $search_max_stats = 40;

    static $login_google = 'https://accounts.google.com/o/oauth2/v2/auth?scope=email%20profile&access_type=online&redirect_uri=https%3A%2F%2Fcoursonline.iconia.dev%2Fconnect&response_type=code&client_id=414856425179-l1d7acq99k5bft0j01vgrq3d2ik9p9ik.apps.googleusercontent.com';

}

?>