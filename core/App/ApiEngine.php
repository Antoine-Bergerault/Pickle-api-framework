<?php
namespace Pickle\Engine;


class ApiEngine {

    private $content = [];
    private $status = 200;
    private $abort = true;

    public function getContent(){
        return $this->content;
    }

    public function setContent(array $content): ApiEngine
    {
        $this->content = $content;
        return $this;
    }

    public function addToContent(string $key, $val): ApiEngine
    {
        $this->content[$key] = $val;
        return $this;
    }

    public function addContentBase(array $arr): ApiEngine
    {
        $this->content = array_merge($this->content, $arr);
        return $this;
    }

    public function toJson(): string
    {
        if($this->status != 200){
            $this->content = [];
            $this->content['status'] = $this->getStatus();
            switch($this->status){
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    $this->content['error'] = '404 Not Found';
                    $this->content['content'] = 'The requested URL was not found on this server.';
                    return json_encode($this->content, JSON_NUMERIC_CHECK);
                case 401:
                    header("HTTP/1.0 401 Unauthorized");
                    $this->content['error'] = '401 Unauthorized';
                    $this->content['content'] = 'You cannot access to this page.';
                    return json_encode($this->content, JSON_NUMERIC_CHECK);
                case 429:
                    $this->content['error'] = 'Request limit reached';
                    $this->content['content'] = 'You reached your request limit. You must wait before re-use it.';
                    return json_encode($this->content, JSON_NUMERIC_CHECK);
                default:
                    $this->content['error'] = 'Unable to provide data';
                    $this->content['content'] = 'Data cannot be provided for some reason.';
                    return json_encode($this->content, JSON_NUMERIC_CHECK);
            }
        }
        return json_encode($this->content, JSON_NUMERIC_CHECK);
    }


    /**
     * Get the value of status
     */ 
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus(int $status): ApiEngine
    {
        $this->status = $status;
        if($this->abort){
            echo $this->toJson();
            header('Content-Type: application/json');// needs improvements
            die();
        }
        return $this;
    }

    /**
     * Get the value of abort
     */ 
    public function getAbort(): bool
    {
        return $this->abort;
    }

    /**
     * Set the value of abort
     *
     * @return  self
     */ 
    public function setAbort($abort): ApiEngine
    {
        $this->abort = $abort;
        return $this;
    }

}