<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 30/8/17
 * Time: 11:02 PM
 */

/*
 *Status Codes
 *200 = Request Successfully Completed with data
 *401 = Wrong Session Id
 *402 = Wrong Session Data
 *501 = No Counter Id in Session
 *503 = No Live Queues
 *
*/

/*
 * API Details :-
 * This API Provides Details of the queue to the counter in an array format when the storeuid and
 * the counterUID is served to the API
 * */


session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();

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
        if (check($_SESSION['counteruid'])) {
            $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid ORDER BY serial LIMIT 5";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_STR);
            $find_query->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_STR);
            $find_query->execute();

            $total = $find_query->rowCount();
            if ($find_query->rowCount() == 0) {
                $data['status'] = false;
                $data['status_code'] = 503;
                echo json_encode($data);
                die();
            }
            $data['size'] = $find_query->rowCount();
            while ($fetched = $find_query->fetch()) {
                $temp = array();
                $temp['serial'] = $fetched['serial'];
                $temp['customer_name'] = $fetched['customer_name'];
                $temp['queueuid'] = $fetched['queueuid'];
                array_push($data, $temp);
            }
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
        $data['status_code'] = 401;
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 402;
    echo json_encode($data);
    die();
}
