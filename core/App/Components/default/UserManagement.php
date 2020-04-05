<?php
namespace Pickle\Engine;
use Pickle\Tools\Config;

require_once ROOT.'/core/Tools/config.php';


trait UserManagement{

    static function connect($usr, $remind = false){//connect the app to the user passed in parameter as $usr
        if(is_object($usr)){
            $usr->devices = self::getUserDevices($usr->id ?? null);
        }
        self::$user = $usr;
        self::session();
        $_SESSION['user'] = self::$user;//store the user in session
        if ($remind) {
            self::saveusercookie($usr);
        }
        self::save('ip', $_SERVER['REMOTE_ADDR']);
        if(isset($usr->devices)){
            self::updateDevicesAddUser($usr->devices);
        }
    }

    static function getUserDevices(?int $id){
        if(!is_int($id) || empty($id)){
            return [];
        }
        $firebasemodule = App::module('firebase');
        $results = $firebasemodule->getDatabase()->getReference('token')->orderByChild('userAccount')->equalTo("$id")->getValue();
        if(App::isset_session('devices')){
            $devices = App::get('devices');
            if(!is_array($devices)) $devices = [$devices];
            $results = array_merge($results, $devices);
        }
        return $results;
    }

    static function addDevice($device){
        if(App::is_connected()){
            $user = App::get('user');
            if(!isset($user->devices)) $user->devices = [];
            $user->devices[] = $device;
            App::save('user', $user);
        }
    }

    static function updateDevicesAddUser(array $devices){
        if(App::is_connected()){
            $firebaseModule = App::module('firebase');
            $database = $firebaseModule->getDatabase();
            foreach($devices as $device){
                if(!isset($device['userAccount']) || $device['userAccount'] == false){
                    if(isset($device['token'])){
                        $device['userAccount'] = App::getid();
                        $database->getReference('token/'.$device['token'])->set($device);
                    }
                }
            }
        }
    }

    static function logout(){//logout the current user if we are connected

        self::$user = false;
        if (isset($_SESSION) && isset($_SESSION['user'])) {
            $_SESSION['user'] = false;
        }
        self::clear_session();

    }



    static function getuser($id){
        $model = new \Pickle\UserModel();
        return $model->user_info([
            'id' => $id
        ]);
    }

    static function getuserbydata($data){
        $model = new \Pickle\UserModel();
        return $model->user_info($data);
    }



    static function is_connected(){//return true if the user exist, else false

        if (self::$user != false) {

            return true;

        }

        return false;

    }

    static function is_role($role_name){
        if (self::is_connected()) {
            if (isset(self::$user->group_name) && in_array($role_name, self::$user->group_name)) {
                return true;
            }
            return false;
        }
        return false;
    }

    static function is_available($action_name){
        if (self::is_connected()) {
            $actions = self::$user->actions;
            if (in_array($action_name, $actions)) {
                return true;
            }
            return false;
        }
        return false;
    }



    static function saveusercookie($usr){
        setcookie('user', self::cookieinfo($usr), time() + 3600 * 24, '/', Config::$website, false, true);
    }

    static function cookieinfo($user){
        return $user->id . '*' . sha1($user->name . $user->pass . $_SERVER['REMOTE_ADDR']);
    }

    static function fromcookie(){
        if (isset($_COOKIE['user'])) {
            $cookie = $_COOKIE['user'];
            $cookie = explode('*', $cookie);
            $usr = self::getuser($cookie[0]);
            if ($usr == false) {
                return false;
            }
            if (self::cookieinfo($usr) == implode('*', $cookie)) {
                self::connect($usr);
                self::saveusercookie($usr);
            }
        }
        return false;
    }


    
    static function getid(){//return the id of the user and false if we are not connected

        if (self::is_connected()) {

            $usr = self::$user;
            return $usr->id;

        }

        return false;

    }

    static function getname(){//return the name of the user and false if we are not connected

        if (self::is_connected()) {

            $usr = self::$user;
            return $usr->name;

        }

        return false;

    }

    static function getteamid(){
        if (self::is_connected()) {

            $usr = self::$user;
            return $usr->team_id ?? false;

        }

        return false;
    }

    static function getteammoderator(){
        if (self::is_connected()) {

            $usr = self::$user;
            return $usr->team_moderator ?? false;

        }

        return false;
    }

}


?>