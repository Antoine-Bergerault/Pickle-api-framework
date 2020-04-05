<?php

interface CacheInterface {

    public function __construct($duration = 10);

    public function read($filename, $serialize = false);

    public function write($filename, $content);

    public function delete($filename);

    public function clear();

    public function clearfolder($name);

}

?>