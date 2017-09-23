<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/17
 * Time: 1:55 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/confidential/mysql_login.php';

try{
    $mysql_conn=new PDO("mysql:host=$host;port=3306;sdbname=$dbname",$username_db,$password_db);
    $mysql_conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $mysql_conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
//    echo "Connection established";
}
catch(PDOException $e){
    echo 'Connect Object Error:'.$e->getmessage();
}
