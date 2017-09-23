<?php

session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();
/*
 * Status Codes
 * 402 = Incomplete Session data
 * 501 = Not Logged In, Incorrect Data
 * 502 = IDs dont match
 * 200 = All done
 * */

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_SESSION['counteruid']) && check($_GET['queueuid']) && check($_GET['serial'])) {
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];
    $counteruid = $_SESSION['counteruid'];

    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_STR);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();
    if ($find_query->rowCount() > 0) {

        $queueuid = $_GET['queueuid'];
        $serial = $_GET['serial'];
        $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid AND serial = :serial AND queueuid = :qid LIMIT 1";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':cid', $_SESSION['counteruid'], PDO::PARAM_INT);
        $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
        $find_query->bindParam(':qid', $queueuid, PDO::PARAM_STR);
        $find_query->execute();
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        if ($find_query->rowCount() > 0) {
            $temp = $find_query->fetch();
            $del_id = $temp['id'];
            $groupuid = $temp['groupuid'];
            $time = "'" . $temp['time'] . "'";
//            Getting Wait Value
            $sql_query_string = "SELECT HOUR(timediff(now()," . $time . ")) as hour,MINUTE(timediff(now()," . $time . ")) as minute";
            $find_query_2 = $mysql_conn->prepare($sql_query_string);
            $find_query_2->execute();
            $find_query_2->setFetchMode(PDO::FETCH_ASSOC);
            $temp_2 = $find_query_2->fetch();
            $wait = ((int)$temp_2['hour']) * 60 + (int)$temp_2['minute'];
//            Inserting to Analytics
            $sql_query_string = "INSERT INTO " . $dbname . ".analytics (storeuid,counteruid,customer_email,customer_name,wait) VALUES(:sid,:cid,:cemail,:cname,:wait)";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
            $find_query->bindParam(':cemail', $temp['customer_email'], PDO::PARAM_STR);
            $find_query->bindParam(':cname', $temp['customer_name'], PDO::PARAM_STR);
            $find_query->bindParam(':wait', $wait, PDO::PARAM_INT);
            $find_query->execute();

            //   Deleting
            $sql_query_string = "DELETE FROM " . $dbname . ".live_queue WHERE id = :id";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':id', $del_id, PDO::PARAM_INT);
            $find_query->execute();

            //Add the top to this Queue
            $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND groupuid = :gid AND counteruid = -1 LIMIT 1";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':gid', $groupuid, PDO::PARAM_INT);
            try {
                $find_query->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 500;
                $data['message'] = $e->getMessage();
                $data['errorinfo'] = $e->errorInfo;
                echo json_encode($data);
                die();
            }
            if ($find_query->rowCount() > 0) {
                //Take his detail
                $find_query->setFetchMode(PDO::FETCH_ASSOC);
                $temp_id = $find_query->fetch();

                $sql_query_string = "UPDATE " . $dbname . ".live_queue SET counteruid = :cid WHERE id=:id";
                $find_query = $mysql_conn->prepare($sql_query_string);
                $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
                $find_query->bindParam(':id', $temp_id['id'], PDO::PARAM_INT);
                try {
                    $find_query->execute();
                } catch (PDOException $e) {
                    $data['status'] = False;
                    $data['status_code'] = 500;
                    $data['message'] = $e->getMessage();
                    $data['errorinfo'] = $e->errorInfo;
                    echo json_encode($data);
                    die();
                }
            }

            $data['status'] = True;
            $data['status_code'] = 200;
            echo json_encode($data);
            die();
        } else {
            $data['status'] = False;
            $data['status_code'] = 502;
            echo json_encode($data);
            die();
        }

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