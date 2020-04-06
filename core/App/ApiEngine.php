<?php
namespace Pickle\Engine;


class ApiEngine {

    static $content = [];
    static $status = 200;
    static $abort = false;

    static function getContent(){
        return self::$content;
    }

    static function setContent(array $content): void
    {
        self::$content = $content;
    }

    static function addToContent(string $key, $val, $abort = false): void
    {
        self::$content[$key] = $val;
        if($abort){
            echo self::toJson();
            die();
        }
    }

    static function addContentBase(array $arr): void
    {
        self::$content = array_merge(self::$content, $arr);
    }

    static function toJson(): string
    {
        if(self::$status != 200){
            // self::$content = [];
            $error_content = [];
            $error_content['status'] = self::getStatus();
            switch(self::$status){
                case 400:
                    header("HTTP/1.0 400 Bad Request");
                    $error_content['status_error'] = 'Bad Request.';
                    $error_content['error_content'] = 'Bad equest URL or parameters.';
                    break;
                case 401:
                    header("HTTP/1.0 401 Unauthorized");
                    $error_content['status_error'] = '401 Unauthorized';
                    $error_content['error_content'] = 'You can\'t access this page.';
                    break;
                case 404:
                    header("HTTP/1.0 404 Not Found");
                    $error_content['status_error'] = 'Not found';
                    $error_content['error_content'] = 'Ressource not found.';
                    break;
                case 429:
                    $error_content['status_error'] = 'Request limit reached';
                    $error_content['error_content'] = 'You reach the limit of request. Please wait';
                    break;
                case 500:
                    header("HTTP/1.0 500 Internal Server Error");
                    $error_content['status_error'] = 'Internal server error';
                    $error_content['error_content'] = 'A problem as occured on the server.';
                    break;
                case 503:
                    header("HTTP/1.0 503 Service Unavailable");
                    $error_content['status_error'] = 'Service Unavailable';
                    $error_content['error_content'] = 'The service is unavailable for the time.';
                    break;
                default:
                    $error_content['status_error'] = 'Unavailable data';
                    $error_content['error_content'] = 'The data is unavailable.';
                    break;
            }
            return json_encode(array_merge($error_content, self::$content), JSON_NUMERIC_CHECK);
        }
        return json_encode(array_merge(['status' => 200], self::$content ?? []), JSON_NUMERIC_CHECK);
    }


    /**
     * Get the value of status
     */ 
    static function getStatus(): int
    {
        return self::$status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    static function setStatus(int $status): void
    {
        self::$status = $status;
        if(self::$abort){
            echo self::toJson();
            die();
        }
    }
    
    /**
     * error
     * Adding an error and stopping script execution
     * @param  string $error
     * @return void
     */
    static function error(string $error){
        if($error == 'mustBeConnected'){
            self::setStatus(401);
            self::setContent([
                'error' => 'mustBeConnected',
                'error_message' => 'You must be connected'
            ]);
        }

        echo self::toJson();
        die();
    }

    /**
     * Get the value of abort
     */ 
    static function getAbort(): bool
    {
        return self::$abort;
    }

    /**
     * Set the value of abort
     *
     * @return  self
     */ 
    static function setAbort($abort): void
    {
        self::$abort = $abort;
    }

}