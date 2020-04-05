<?php
namespace Pickle\Components;
include ROOT.'/core/App/Interfaces/CacheInterface.php';

class Cache implements \CacheInterface{

    public $dirname;
    public $duration;//in minutes
    private $folders = [];

    public function __construct($duration = 10){

        $this->duration = $duration;
        $this->dirname = \Pickle\Tools\Config::$CacheDirectory;
        $this->folders = $this->getfolders();

    }

    public function read($filename, $serialize = false){

        $file = ROOT."/$this->dirname/".$filename;
        if(!file_exists($file)){
            return false;
        }
        $lifetime = (time() - filemtime($file)) / 60;
        if($this->duration != false && $lifetime > $this->duration){
            if($filename == 'home'){
                $this->clearfolder('load');
            }
            return false;
        }
        return $serialize ? unserialize(file_get_contents($file) ?? '') : file_get_contents($file) ?? '';
        
    }

    public function write($filename, $content){

        return file_put_contents(ROOT."/$this->dirname/".$filename, $content);

    }

    public function delete($filename){
        $file = ROOT."/$this->dirname/".$filename;
        if(file_exists($file)){
            unlink($file);
        }
    }

    public function clear(){
        $files = glob(ROOT."/$this->dirname/*");
        foreach($files as $file){
            unlink($file);
        }

        foreach($this->folders as $folder){
            $this->clearfolder($folder);
        }

    }

    public function clearfolder($name){
        $files = glob(ROOT."/$this->dirname/$name/*");
        foreach($files as $file){
            unlink($file);
        }
    }

    public function folder($dirname){
        if (!file_exists(ROOT."/$this->dirname/$dirname")) {
            mkdir(ROOT."/$this->dirname/$dirname");
        }
        $this->folders[] = $dirname;
    }

    public function getfolders(){

        $dir = $this->dirname;
        $path = ROOT."/$dir/";
        $dirs = array();
    
        foreach (new \DirectoryIterator($path) as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $dirs[] = $file->getFilename();
            }
        }
    
        return $dirs;
    }

}

?>