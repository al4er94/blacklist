<?php

class DB{
    private $db; 
    
    public function __construct(){ 
        $config = parse_ini_file('configDB.ini', true);
        $this->db = new PDO('mysql:host=' . $config['dbHost'] . ';dbname=' . $config['dbName'], $config['dblogin'], $config['dbPass']);
    }
    
    public function query($sql){
        $res = $this->db->prepare($sql);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    
}

