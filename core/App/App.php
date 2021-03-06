<?php
namespace Pickle\Engine;
use Pickle\Tools\Config;

require_once 'config.php';
\AppConfig::load();

class App extends Session{

    use UserManagement;
    use SessionChecker;
    use StyleManagement;

    static $user = false;//variable to store the user
    static $url = null;//variable to store the url
    static $middlewares = [];
    static $modules = [];
    static $components = true;
    
    static function load(){//equivalent of __construct
        $header = isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] : null;
        define('ENV', ($header === 'XMLHttpRequest') ? null : Config::$env);

        self::session();//create a session if doesn't exist

        /*$command = self::getSessionCommand('update');
        $command->setUpdateMethod('sql', 'SELECT * FROM `users` WHERE id = ?', [2], true);
        $command->filter('user_id', [1, 2]);
        self::addSessionCheckdata('aaa', $command);*/

        if(isset($_SESSION) && isset($_SESSION['user'])){
            $usr = $_SESSION['user'];//set the variable with the session content
            self::connect($usr);//connect to the user
        }
        if(self::is_connected()){
            if(self::get('ip') != $_SERVER['REMOTE_ADDR']){
                self::logout();
                self::destroy('ip');
            }
        }
        if(isset($_COOKIE['user']) && !self::is_connected()){
            self::fromcookie();
        }
        self::seturl();//initialize the $url
        
        $GLOBALS['scripts'] = [];
        $GLOBALS['styles'] = [];

        self::checkSession();
    }

    static function back(){//a shortcut to go to the prevent page

        echo '<script>history.back()</script>';

    }

    static function seturl($nurl = null){//set the url

        if($nurl != null){
            self::$url = $nurl;
        }else{
            self::$url = (isset($_SERVER['HTTPS']) ? "https://" : "http://"). $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }

    }

    static function debug($v){
        self::save('debug', debug($v));
    }

    /**
     * @param $name
     * @return mixed
     */
    static function middleware($name){
        if(!isset(self::$middlewares[$name])){
            require_once(ROOT.'/src/Middlewares/'.ucfirst($name).'Middleware.php');
            $name = ucfirst($name).'Middleware';
            $middleware = new $name();
            self::$middlewares[$name] = $middleware;
            return $middleware;
        }else{
            return self::$middlewares[$name];
        }
    }

    /**
     * @param $name
     * @return mixed
     */
    static function module($name){
        if (!isset(self::$modules[$name])) {
            require_once(__DIR__ . '/../Modules/' . ucfirst($name) . 'Module.php');
            $name = ucfirst($name) . 'Module';
            $module = new $name();
            self::$modules[$name] = $module;
            return $module;
        } else {
            return self::$modules[$name];
        }
    }

    static function activeMiddlewares($arr){
        if(!is_array($arr)){
            $arr = [$arr];
        }
        foreach($arr as $m){
            self::middleware($m);
        }
    }

    static function getMethod(){
        return $_SERVER['REQUEST_METHOD'];
    }

}

App::load();

?>