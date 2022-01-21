<?php

class Blacklists{
    
    private static $db = null;
    
    public function __construct() {
        self::$db = new DB();
    }

    public function save($blacklist, $id){
        try{
            if (!self::checkAdvertisers($id)){
                throw new Exception("Advertiser whith id=$id nor found"); 
            }
            
            if (!self::checkSrtingFormat($blacklist)){
                throw new Exception("blacklist does not match format"); 
            }
            
            $blacklitArr = explode(',', trim(str_replace(' ', '', $blacklist)));

            $publishIdArr = array();
            $sitesIdArr = array();
            foreach ($blacklitArr as $blacklistAtr){
                if (strpos($blacklistAtr, 's') !== false){
                    $sitesIdArr[] = str_replace('s', '', $blacklistAtr);
                } else if(strpos($blacklistAtr, 'p') !== false){
                    $publishIdArr[] = str_replace('p', '', $blacklistAtr);
                }
            }
            
            $checkSiteList = self::checkList($sitesIdArr, 'sites');
            if(is_array($checkSiteList) && !empty($checkSiteList)){
                throw new Exception("The next sites id is not in the database: ". implode(',', $checkSiteList)); 
            }
            
            $checkPublishersList = self::checkList($publishIdArr, 'publishers');
            if(is_array($checkPublishersList) && !empty($checkPublishersList)){
                throw new Exception("The next publishers id is not in the database: ". implode(',', $checkPublishersList)); 
            }
            
            $res = self::saveIntoBd($id, $sitesIdArr, $publishIdArr);
            return $res;
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    public function get($id){
        try{
            if(!self::checkAdvertisers($id)){
                throw new Exception("Advertiser whith id=$id nor found"); 
            }
            
            $line = self::getBlackList($id);
            file_put_contents('blacklistOut.txt', $line);
            
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    

    // Проверка существования рекламодателя
    public static function checkAdvertisers($id){
        $res = self::$db->query("SELECT * FROM `advertisers` WHERE `id`=".intval($id));
        return (empty($res) ? false : $res);
    }
    
    /*Проверка на соответсвие формату блэклиста
     * В блэклисте могут быть только символы p,s 
     * а так же числа от 1 до 9, пробелы и запятые
     */
    public static function checkSrtingFormat($blacklist){
        if(preg_match('/^[ps1-9,\s]+$/m', $blacklist)){
            return true;
        }else{
            return false;
        }
    }
    
    /* Проверка наличия id-шников сайтов и паблтишеров в БД
     * 
     */
    public static function checkList($idArr, $bdTable){
        if($bdTable == 'sites'){
            $bdField = 'site_id';
        }else if($bdTable == 'publishers'){
            $bdField = 'publisher_id';
        }
        $res = self::$db->query("SELECT `".$bdField."` FROM `".$bdTable."` WHERE `".$bdField."` IN (". implode(',', $idArr).")");
        return array_diff($idArr, array_column($res, $bdField));
    }
    /*
     * Вставка непосредственно в БД
     */
    public static function saveIntoBd($id, $sitesIdArr, $publishIdArr){
        $sql = "INSERT INTO `blacklist` (`id`, `advertisers_id`, `essence`, `essence_id`) VALUES ";
        foreach ($sitesIdArr as $siteId){
            $res = self::$db->query("SELECT `id` FROM `blacklist` WHERE `advertisers_id` = ".intval($id)." AND `essence` = 's' AND `essence_id` = ".intval($siteId));
            if(empty($res)){
                $sql .= "(NULL, ".intval($id)." , 's', ".intval($siteId)."), ";
            }
        }
        foreach ($publishIdArr as $publishId){
            $res = self::$db->query("SELECT `id` FROM `blacklist` WHERE `advertisers_id` = ".intval($id)." AND `essence` = 'p' AND `essence_id` = ".intval($publishId));
            if(empty($res)){
                $sql .= "(NULL, ".intval($id)." , 'p', ".intval($publishId)."),";
            }      
        }
        $sql = mb_substr($sql, 0, -1).';';
        
        if(is_array(self::$db->query($sql))){
            return true;
        }else{
            return false;
        }

    }
    
    /*
     *  Выгрузка блэклиста из БД
     */
    public static function getBlackList($id){
        $res = self::$db->query("SELECT * FROM `blacklist` WHERE `advertisers_id` = ".intval($id));
        $str = '';
        foreach ($res as $line){
            $str .= $line['essence'].$line['essence_id'].', ';
        }
        return mb_substr($str, 0, -2);
    }
}
