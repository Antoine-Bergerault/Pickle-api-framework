<?php
namespace Pickle\Engine;

use Pickle\Engine\ApiEngine;

class Router{
    
    static $routes = ['GET' => [], 'POST' => []];//list of routes
    static $default = false;//default route

    static function get($path, $arr = []){//add a route with the get method
        $route = new Route($path, $arr);
        self::$routes['GET'][] = $route;
        return $route;
    }

    static function post($path, $arr = []){//add a route with the post method
        $route = new Route($path, $arr);
        self::$routes['POST'][] = $route;
        return $route;
    }

    static function group($path, $args){//group routes
        foreach($args as $arg){
            $arg->move($path);
        }
        return true;
    }

    static function condition(array &$routes, $condition){
        foreach($routes as $route){
            $route->only_if($condition);
        }
    }

    static function default_path($path){//add the default path
        self::$default = $path;
    }

    /**
     * @return string
     */
    static function run($url){//check if a route correspond with the url
        function startsWith ($string, $startString) { 
            $len = strlen($startString); 
            return (substr($string, 0, $len) === $startString); 
        }
        $method = $_SERVER['REQUEST_METHOD'];
        $found = false;

        if(isset(self::$routes[$method])){
            for($i = 0; $i < sizeof(self::$routes[$method]); $i++) {
                $route = self::$routes[$method][$i];
                if($route->match($url) == true){
                    $data = $route->callback();
                    $found = true;
                    if(isset($data)){
                        ApiEngine::addContentBase($data);
                    }
                }
            }
        }

        if(!$found){
            ApiEngine::setStatus(404);
        }

        return ApiEngine::toJson();

    }

    /**
     * @return Route
     */
    static function getRoute($name){
        foreach(self::$routes['GET'] as $route){
            if($route->name == $name){
                return $route;
            }
        }
        return false;
    }

}


?>