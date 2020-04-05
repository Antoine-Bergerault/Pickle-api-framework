<?php

class YoutubeMiddleware{

    const API = 'AIzaSyAUAaeqPGutfDEuPJWN5zlfwDaHx7i8ig8';

    public function data($channelId){
        if(!is_array($channelId)){
            $channelId = [$channelId];
        }
        if(empty($channelId)){
            return [];
        }
        $channelId = implode(',', $channelId);
        $url = "https://www.googleapis.com/youtube/v3/channels?part=snippet&fields=items&id={$channelId}&key=".self::API."";

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $channelOBJ = json_decode( curl_exec( $ch ) );
        if(!is_object($channelOBJ)){
            return [];
        };
        $data = [];
        foreach($channelOBJ->items as $index => $i){
            $data[] = [
                'name' => $i->snippet->title,
                'description' => $i->snippet->description,
                'thumbnail' => $i->snippet->thumbnails->default->url,
                'url' => 'https://www.youtube.com/channel/' . $i->id,
                'id' => $i->id
            ];
        }

        return $data;
    }

}