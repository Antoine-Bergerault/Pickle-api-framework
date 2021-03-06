<?php
namespace Pickle\Tools;

use PDO;

class DB{

    public function __construct($host = null, $username = null, $password = null, $database = null){
        $this->host = $host;
        $this->username = $username;
        $this->password = $password;
        $this->database = $database;
        try{
            $this->db = new \PDO('mysql:host=' . $this->host . '; dbname=' . $this->database, $this->username, $this->password, array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_WARNING
            ));
        }catch(\PDOException $e){
            die('<h1>Impossible de se connecter a la base de donnee</h1>');
        }
    }
    public function query($sql, $data = array()){
        $req = $this->db->prepare($sql);
        $req->execute($data);
        $res = $req->fetchAll(\PDO::FETCH_OBJ);
        //if(ENV == 'DEV'){
            if(!isset($GLOBALS['Pickle-DB-QueryCount'])){
                $GLOBALS['Pickle-DB-QueryCount'] = 0;
            }
            if(!isset($GLOBALS['Pickle-DB-Queries'])){
                $GLOBALS['Pickle-DB-Queries'] = [];
            }
            if(!isset($GLOBALS['Pickle-DB-Params'])){
                $GLOBALS['Pickle-DB-Params'] = [];
            }
            if(!isset($GLOBALS['Pickle-DB-ResultsCount'])){
                $GLOBALS['Pickle-DB-ResultsCount'] = [];
            }
            $GLOBALS['Pickle-DB-QueryCount'] = $GLOBALS['Pickle-DB-QueryCount'] + 1;
            $GLOBALS['Pickle-DB-Queries'][] = $sql;
            $GLOBALS['Pickle-DB-Params'][] = $data;
            $GLOBALS['Pickle-DB-ResultsCount'][] = count($res);
        //}
        return $res;
    }
    public function total($param){
        $retour_total = $this->query($param);
        return $retour_total[0]->total;
    }
    public function lastInsertId(){
        return $this->db->lastInsertId();
    }
    
}