<?php 
//include 'Blacklists.php';
//include 'DB.php';
require_once 'vendor/autoload.php';
$file = 'blacklist.txt';

try{
    if(!file_exists($file)){
        throw new Exception('FIle Not Found');
    }
    $blacklist = file_get_contents($file);
    if(!class_exists('Blacklists')){
        throw new Exception('Class Blacklists Not Found');
    }
    $blacklistObj = new Blacklists();
    if($blacklistObj->save($blacklist, 1)){
        echo 'Blacklist saved';
    }else{
        throw new Exception('Blacklist not saved'); 
    }
    $blacklistObj->get(1);
} catch (Exception $ex) {
    echo $ex->getMessage();
}