<?php
// common settings
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('ROOT', dirname(__FILE__));

class ixtFiles
{
    public function filterByExtension($var)
    {
        return preg_match("/^[a-zа-яё\d]{1}[a-zа-яё\d\s]*\.{1}\ixt$/i", $var);
    }
    public function isDir($var)
    {
        return !is_dir(ROOT."/ixtfolder/$var");
    }

    public function findFiles()
    {
        $scanned_directory = array_diff(scandir(ROOT . '/ixtfolder'), array('..', '.'));
        $result = array_filter($scanned_directory, "self::filterByExtension");
        $result = array_filter($result, "self::isDir");
        sort($result);

        foreach ($result as $key => $value) {
            echo $value;
            echo "<br>";
        }
    }
}

$a = new ixtFiles();
$a->findFiles();
?>