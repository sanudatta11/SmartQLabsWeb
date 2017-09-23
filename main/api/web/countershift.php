<?php

/*
 *Status Codes
 *200 = Changed Counter
 *402 = Wrong Data Format
 *501 = Wrong Session Data
 *502 = Counter Closed
 *400 = Counter not Present
 *
*/

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_SESSION['counteruid']) && check($_GET['shift_counteruid']) && check($_GET['serial']) && check($_GET['queueuid'])) {
    $storeuid = $_SESSION['storeuid'];
    $counteruid = $_SESSION['counteruid'];
    $email = $_SESSION['email'];

    $shift_counter = $_GET['shift_counteruid'];
    $serial = $_GET['serial'];
    $qid = $_GET['queueuid'];

    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid AND admin = 2 LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_STR);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {

//        Authorized

//        Check Counter Present Or not
        // Fetching Group UID of new Counter
        //Checking if serving or not
        $sql_query_string = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid AND counteruid = :cid LIMIT 1";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':cid', $shift_counter, PDO::PARAM_INT);
        $find_query->execute();
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $find_query->fetch();

        if ($find_query->rowCount() <= 0) {
            //Counter Not Present
            $data['status'] = False;
            $data['status_code'] = 400;
            echo json_encode($data);
            die();
        }

        $groupuid = $temp['groupuid'];
        if ($temp['serving'] == 0) {
            //Counter Closed
            $data['status'] = False;
            $data['status_code'] = 502;
            echo json_encode($data);
            die();
        }

        //Getting Present Largest id
        $sql_query_string = "SELECT MAX(id) AS id FROM " . $dbname . ".live_queue";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->execute();
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $find_query->fetch();
        $max_id = $temp['id'];
        $max_id++;

        //Updating new queue
        $sql_query_string = "UPDATE " . $dbname . ".live_queue SET id = :max_id,serial = :mserial,counteruid=:ncid,groupuid=:gid WHERE storeuid = :sid AND serial = :serial AND queueuid = :qid AND counteruid = :cid";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $mserial = $max_id * 10;
        $find_query->bindParam(':max_id', $max_id, PDO::PARAM_INT);
        $find_query->bindParam(':mserial', $mserial, PDO::PARAM_INT);
        $find_query->bindParam(':ncid', $shift_counter, PDO::PARAM_INT);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
        $find_query->bindParam(':qid', $qid, PDO::PARAM_STR);
        $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
        $find_query->bindParam(':gid', $groupuid, PDO::PARAM_INT);
        $find_query->execute();

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