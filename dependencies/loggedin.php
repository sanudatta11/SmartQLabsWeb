<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 7:28 AM
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

function check($var)
{
    return (isset($var) && !empty($var));
}

if(check($_SESSION['storeuid']) && check($_SESSION['email']))
{
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];
    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :storeuid AND sessionid = :sid LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':storeuid', $storeuid, PDO::PARAM_STR);
    $find_query->bindParam(':sid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {

    } else {
        session_destroy();
        $_SESSION = array();
        header('Location: /main/');
        die();
    }
}
else{
    session_destroy();
    $_SESSION = array();
    header('Location: /main/');
    die();
}

