<?php

interface EmailInterface {


    /**
     * @param subject subject of the email
     * @param message content of the email
     * @param mailto address to send the email
     * @param from the email address to use
     */
    static function send($subject, $data, $name, $mailto = null, $from = null);


    /**
     * @param from set the email address to use
     * @return string
     */
    static function generate_header($from);

}




?>