<?php
use Pickle\Engine\Router;
use Pickle\Tools\Config;

function redirect($path){//redirection
    //echo '<script>window.location = "'.$path.'";</script>';
    header('Location: '.$path);
}

function url($path = false, $params = false){//get the route for the root of the website
    if(Router::$default != false && $path == false){
        $path = Router::$default;
    }else if(Router::$default == false && $path == false){
        return false;
    }

    $url = null;
    if(isset($_GET['picklerewriteurl'])){
        $url = '/'.$_GET['picklerewriteurl'];
    }

    $url = str_replace(' ','%20',$url);
    $url = explode('?', $url);
    $url = $url[0];

    $root = (isset($_SERVER['HTTPS']) ? "https://" : "http://"). "$_SERVER[HTTP_HOST]".parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    if($url != null){
        $root = explode($url, $root);
        $root = $root[0];
    }
    $root = trim($root, '/');
    $route_path = $root.$path;
    if($params){
        $route_path.= (str_replace(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '', $_SERVER['REQUEST_URI']));
    }

    return $route_path;
}

function root(){//return the root of the website
    return trim(url('/'));
}

function route($name){
    return url(Router::getRoute($name)->path);
}

function speaking_format($date){
    $datetime = date_diff(new DateTime(date("Y-m-d H:i:s")), new DateTime($date));
    $date = new stdClass();
    $date->years = $datetime->y;
    $date->months = $datetime->m;
    $date->days = $datetime->d;
    $date->hours = $datetime->h;
    $date->minutes = $datetime->i;
    $date->seconds = $datetime->s;

    $str = "";
    if($date->years == 0){
        if($date->months == 0){
            if($date->days == 0){
                if($date->hours == 0){
                    if($date->minutes == 0){
                        $str = "$date->seconds s ago";
                    }else{
                        $str = "$date->minutes m ago";
                    }
                }else{
                    $str = "$date->hours h". ($date->minutes > 0)?"$date->minutes m":"" ." ago";
                }
            }else{
                $str = "$date->days d". ($date->hours > 0)?"$date->hours h":"" ." ago";
            }
        }else{
            $str = "$date->months month(s)". ($date->days > 0)?"$date->days d":"" ." ago";
        }
    }else{
        $str = "$date->years year(s)". ($date->months > 0)?"$date->months month(s)":"" ." ago";
    }
    return $str;
}

function str_random($lenght, $alphabet = "0123456789azertyuiopqsdfghjklmwxcvbnAZERTYUIOPQSDFGHJKLMWXCVBN"){
    return substr(str_shuffle(str_repeat($alphabet, $lenght)), 0, $lenght);
}

function standard_format($date){
    return date_format(new DateTime($date), 'jS F Y');
}

/**
 * name
 * Retourne par exemple : (histoire) => d'histoire, (maths) => de maths
 * @param string $string
 *
 * @return string
 */
function name(string $string): string{
    $tag = 'de ';
    $letters = ['a', 'e', 'i', 'o', 'u', 'h'];
    if(in_array(lcfirst($string[0]), $letters)){
        $tag = 'd\'';
    }
    return $tag . $string;
}

function slugify($text){
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
  
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  
    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
  
    // trim
    $text = trim($text, '-');
  
    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
  
    // lowercase
    $text = strtolower($text);
  
    if (empty($text)) {
      return 'n-a';
    }
  
    return $text;
}

function keywords($string){
    $list = ['le', 'la', 'du', 'que', 'pour', 'de', 'du', 'des', 'dans', 'les', 'et', 'd\'', 'à', 'l\'', ':', ',', ':', ';'];
    $string = strtolower($string);
    foreach($list as $l){
        $l = strtolower($l);
        $string = str_replace(' ' .$l . ' ', ' ', $string);
        if(strpos($l, $string) === 0){
            $string = ltrim($string, $l.' ');
        }
        if(strpos($l, '\'') !== false){
            $string = str_replace(" $l", ' ', $string);
        }
    }
    $string = str_replace(',', '', $string);
    $string = str_replace(':', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('.', '', $string);
    $string = str_replace('!', '', $string);
    $string = str_replace('?', '', $string);
    $string = str_replace(' Comment ', ' ', $string);
    $string = str_replace(' Pourquoi ', ' ', $string);
    return str_replace(' ', ',', $string);
}

function between_dates($begin, $end, $year = false){
    $now = new DateTime();
    if(!$year){
        $begin = date("Y") . '-' . $begin;
        $end = date("Y") . '-' . $end;
    }
    $startdate = new DateTime($begin);//"2014-11-20"
    $enddate = new DateTime($end);//"2015-01-20"

    return $startdate <= $now && $now <= $enddate;
}

?>