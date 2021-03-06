<?php

class SecureContentMiddleware{


    public function no_script(string $html){
        $doc = new DOMDocument();

        // load the HTML string we want to strip
        $doc->loadHTML($html);

        // get all the script tags
        $script_tags = $doc->getElementsByTagName('script');

        $length = $script_tags->length;

        // for each tag, remove it from the DOM
        for ($i = 0; $i < $length; $i++) {
            $script_tags->item($i)->parentNode->removeChild($script_tags->item($i));
        }

        // get the HTML string back
        $no_script_html_string = $doc->saveHTML();

        return $no_script_html_string;
    }

    public function to_html(string $html){
        $doc = new DOMDocument();

        // load the HTML string we want to strip
        $doc->loadHTML($html);

        $html = $doc->saveHTML();

        return $html;
    }


}