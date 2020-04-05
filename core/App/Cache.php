<?php
namespace Pickle\Engine;

require_once 'config.php';
\AppConfig::cache();

class Cache {

    public $obj = null;

    public function __construct(){
        $args = \func_get_args();
        $this->obj = new \Pickle\Components\Cache(...$args);
        return $this->obj;
    }

    public function __call($method, $args){
        return call_user_func_array(array($this->obj, $method), $args);
    }

}