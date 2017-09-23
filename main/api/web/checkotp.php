<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();
/*
 *Status Codes
 *200 = Correct OTP
 *302 = Wrong OTP
 *401 = Wrong Session Id
 *402 = Wrong Session Data
 *501 = No Counter Id in Session
 *404 = Not Found
 *
*/


if (check($_SESSION['storeuid']) && check($_SESSION['email'])) {
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];
    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_STR);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {

        if (check($_SESSION['counteruid']) && check($_GET['serial']) && check($_GET['otp'])) {
            $serial = $_GET['serial'];
            $test_otp = $_GET['otp'];

            $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid AND serial = :serial LIMIT 1";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
            $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
            $find_query->execute();

            if ($find_query->rowCount() <= 0) {
                $data['status'] = false;
                $data['status_code'] = 404;
                echo json_encode($data);
                die();
            }
            $otp = $find_query->fetch();
            if ($otp['otp'] == $_GET['otp']) {
                //Correct OTP
                $data['status'] = True;
                $data['status_code'] = 200;
                echo json_encode($data);
            } else {
                //Wrong OTP
                $data['status'] = False;
                $data['status_code'] = 302;
                echo json_encode($data);
            }
            die();
        } else {
            $data['data'] = $_GET['otp'];
            $data['status'] = False;
            $data['status_code'] = 501;
            echo json_encode($data);
            die();
        }

    }
} else {
    $data['status'] = False;
    $data['status_code'] = 402;
    echo json_encode($data);
    die();
}
