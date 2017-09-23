<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 9:51 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
session_start();
$email = $_SESSION['email'];
if (isset($email) && !empty($email)) {
    $obj = $mysql_conn->prepare("UPDATE " . $dbname . ".login SET sessionid=NULL WHERE email = :email");
    $obj->bindParam(':email', $email);
    $obj->execute();
}
session_destroy();
$_SESSION = array();
header('Location: /main/');
die();