<?php
	// common settings
	ini_set('display_errors', 1);
	error_reporting(E_ALL);

//parameters for database and connecting
class Db
{
    public static function getConnection()
    {
        $params = array('host' => 'localhost',
            'dbname' => 'tasks',
            'user' => 'root',
            'password' => '',
        );

        $dsn = "mysql:host={$params['host']};dbname={$params['dbname']}";
        $db = new PDO($dsn, $params['user'], $params['password']);
        $db->exec("set names utf8"); //cp1251

        return $db;
    }
}
//comm
final class Init
{
    private function generateRandomString($length = 25){
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $charsLength = strlen($chars);
        $result = '';
        for($i = 0; $i < $length; $i++){
            $rand = rand(0, $charsLength);
            $result.= substr($chars, $rand, $rand-1);
        }
        return $result;
    }

    function __construct($quantity)
    {
        $this->create();
//        $this->fill($quantity); //enable if you need to fill a database
    }

    private function create()
    {
        $db = Db::getConnection();

        $tableExists = "SHOW TABLES LIKE 'test'";
        $check = $db->prepare($tableExists);
        $check->execute();

        if(!$check->fetch()){
            $sql = "CREATE TABLE `tasks`.`test`(
                `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `script_name` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
                `start_time` INT NOT NULL,
                `end_time` INT NOT NULL,
                `result` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL) ENGINE = InnoDB CHARSET = utf8 COLLATE utf8_general_ci;";
            $result = $db->prepare($sql);
            $result->execute();
        }
    }


    private function fill($quantity)
    {
        for($i = 0; $i < $quantity; $i++){
            $scriptName = self::generateRandomString();
            $resultArray = array('normal', 'illegal', 'failed', 'success');
            $resultValue = $resultArray[rand(0, 3)];
            $startTime = rand(1, 50);
            $endTime = rand(51, 100);

            $db = Db::getConnection();
            $sql = "INSERT INTO `test`(`script_name`, `start_time`, `end_time`, `result`) 
                    VALUES(:scriptName, :startTime, :endTime, :result)";
            $result = $db->prepare($sql);
            $result->bindParam(':scriptName', $scriptName);
            $result->bindParam(':startTime', $startTime);
            $result->bindParam(':endTime', $endTime);
            $result->bindParam(':result', $resultValue);
            $result->execute();
        }
    }

    public function get()
    {
        $db = Db::getConnection();
        $sql = "SELECT * FROM `test` WHERE `result` = 'normal' OR `result` = 'success'";
        $result = $db->prepare($sql);
        $result->setFetchMode(PDO::FETCH_ASSOC);
        $result->execute();
        $list = array();
        $i = 0;
        while($row = $result->fetch()){
            $list[$i]['id'] = $row['id'];
            $list[$i]['script_name'] = $row['script_name'];
            $list[$i]['start_time'] = $row['start_time'];
            $list[$i]['end_time'] = $row['end_time'];
            $list[$i]['result'] = $row['result'];
            $i++;
        }
        return print_r($list);
    }
}

$obj = new Init(10);
$obj->get();
?>