<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 8:57 AM
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

function check($var)
{
    return (isset($var) && !empty($var));
}

if (check($_SESSION['storeuid']) && check($_SESSION['email'])) {
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
        header('Location: /main/dash/');
        die();
    }
}

if (check($_POST['email']) && check($_POST['password'])) {
    $email = preg_replace("/[^A-Za-z0-9@.]+/", "", $_POST['email']);
    $password = $_POST['password'];

    $sql_statement = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND password = :pass LIMIT 1";
    $obj = $mysql_conn->prepare($sql_statement);
    $obj->bindParam(':email', $email, PDO::PARAM_STR);
    $obj->bindParam(':pass', $password, PDO::PARAM_STR);
    $obj->execute();

    if ($obj->rowCount() > 0) {
        $obj->setFetchMode(PDO::FETCH_ASSOC);
        $arr = $obj->fetch();
        $storeuid = $arr['storeuid'];

        #Authorized
        $_SESSION['storeuid'] = $storeuid;
        $_SESSION['email'] = $email;
        $_SESSION['admin'] = $arr['admin'];

        if ($arr['admin'] == 2) {
            $sql_statement = "SELECT counteruid FROM " . $dbname . ".counter WHERE email = :email LIMIT 1";
            $obj = $mysql_conn->prepare($sql_statement);
            $obj->bindParam(':email', $email, PDO::PARAM_STR);
            $obj->execute();

            if ($obj->rowCount() > 0) {
                $obj->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $obj->fetch();
                $_SESSION['counteruid'] = (int)$temp['counteruid'];
            } else {
                $_SESSION['error'] = "Counter ID Error!";
                header('Location: /main/');
                die();
            }
        }

        #Assign Session Id to DB
        $sql_statement = "UPDATE " . $dbname . ".login SET sessionid = :sid WHERE email = :email";
        $obj = $mysql_conn->prepare($sql_statement);
        $obj->bindParam(':sid', session_id(), PDO::PARAM_STR);
        $obj->bindParam(':email', $email, PDO::PARAM_STR);
        $obj->execute();
        header('Location: /main/dash/');
        die();
    } else {
        #Not Authorized
        session_destroy();
        session_start();
        $_SESSION['error'] = "Credentials Wrong!";
        header('Location: /main/');
        die();
    }
} else {
    session_destroy();
    session_start();
    $_SESSION['error'] = "Incomplete Data!";
    header('Location: /main/');
    die();
}