<?php

/*
 * State:
 * 1- Serving
 * 2- Closed
 * */

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_SESSION['counteruid'])) {
    $storeuid = $_SESSION['storeuid'];
    $counteruid = $_SESSION['counteruid'];
    $email = $_SESSION['email'];

    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid AND admin = 2 LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_STR);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {

        #Authorized
        $sql_query_string = "SELECT * FROM " . $dbname . ".counter WHERE email = :email AND storeuid = :sid AND counteruid = :cid";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':email', $email, PDO::PARAM_STR);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
        $find_query->execute();
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $find_query->fetch();

        if ($temp['serving'] == 1)
            $data['serving'] = 1;
        else
            $data['serving'] = 0;

        $data['status'] = True;
        $data['status_code'] = 200;
        echo json_encode($data);
        die();

    } else {
        $data['status'] = False;
        $data['status_code'] = 501;
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 402;
    echo json_encode($data);
    die();
}