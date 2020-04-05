<?php
namespace Pickle\Engine;
include ROOT.'/core/App/Interfaces/SessionInterface.php';

class Session implements \SessionInterface{

    static function session(){//start a session if it is not already done

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION;

    }

    static function clear_session(){
        $_SESSION = [];
    }

    /**
     * @param key the key of the session
     */
    static function destroy($key){
        self::session();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * @param key the key of the session
     * @param val the value to assign to the session key
     */
    static function save($key, $val = true){
        self::session();
        if(strlen(str_replace('.', '', $key)) != strlen($key)){
            $arr = explode('.', $key);
            $key = $arr[0];
            $index = $arr[1];
            if(isset($_SESSION[$key]) && !is_array($_SESSION[$key])){
                return false;
            }else if(isset($_SESSION[$key])){
                $_SESSION[$key][$index] = $val;
            }else{
                $_SESSION[$key] = [$index => $val];
            }
            return true;
        }
        $_SESSION[$key] = $val;
        return true;
    }

    /**
     * @param key the key of the session
     * @param val the value to assign to the session key
     */
    static function add($key, $val){
        self::session();
        if (!self::isset_session($key)) {
            return self::save($key, [$val]);
        }
        if (!is_array(self::get($key))) {
            return self::save($key, [self::get($key), $val]);
        }
        $arr = self::get($key);
        $arr[] = $val;
        self::save($key, $arr);
    }

    /**
     * @param key the key of the session
     */
    static function isset_session($key){
        if(strlen(str_replace('.', '', $key)) != strlen($key)){
            $arr = explode('.', $key);
            $key = $arr[0];
            $index = $arr[1];
            return isset($_SESSION[$key][$index]);
        }
        return isset($_SESSION[$key]);
    }


    static function get($key){
        self::session();
        if(strlen(str_replace('.', '', $key)) != strlen($key)){
            $arr = explode('.', $key);
            $key = $arr[0];
            $index = $arr[1];
        }
        if (isset($_SESSION[$key])) {
            if(isset($index) && !is_array($_SESSION[$key])){
                return false;
            }else if(isset($index)){
                return $_SESSION[$key][$index] ?? false;
            }
            return $_SESSION[$key];
        }
        return false;
    }


}


?>