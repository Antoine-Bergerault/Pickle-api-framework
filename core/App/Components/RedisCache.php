<?php
namespace Pickle\Components;
include ROOT.'/core/App/Interfaces/CacheInterface.php';

class Cache  implements \CacheInterface{

    public $dirname;
    public $duration;//in minutes
    private $folders = [];
    private $redis = null;

    public function __construct($duration = 10){
        $this->duration = $duration === false ? false : $duration * 60;
    }

    public function read($filename, $serialize = false){
        $c = $this->redis->get($filename);
        return $serialize ? \unserialize($c) : $c;
    }

    public function write($filename, $content){
        $this->redis->set($filename, $content);
        if($this->duration !== false){
            $this->redis->expire($filename, $this->duration);
        }
    }

    public function delete($filename){
        $this->redis->del($filename);
    }

    public function clear(){
        return $this;
    }

    public function clearfolder($name){
        return $this;
    }

    public function redis(){
        return $this->redis;
    }

}

?>