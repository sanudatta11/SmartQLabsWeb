<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 1/9/17
 * Time: 6:50 AM
 */

/*
 * Status Codes
 *501 = Wrong Session Data
 *402 = Wrong Data Format
 *550 = Serial Not Present
 *200 = All Done
 * */

/*
 * API Detail:-
 * This API will reallocate the person on the Basis of its time to a new queue Serial number
 * */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
$data = array();

//Down Variable
$down = $late_global_down;  //Must Be Less than or equal to the Threshold of the Insert One in Scan API

if ((check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_SESSION['counteruid'])) && (check($_GET['serial']) && check($_GET['queueuid']))) {
    $storeuid = $_SESSION['storeuid'];
    $counteruid = $_SESSION['counteruid'];
    $email = $_SESSION['email'];

    $serial = $_GET['serial'];
    $qid = $_GET['queueuid'];

    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid AND admin = 2 LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {

        $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid AND serial = :serial";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
        $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
        $find_query->execute();

        if ($find_query->rowCount() <= 0) {
            $data['status'] = True;
            $data['status_code'] = 550;
            echo json_encode($data);
            die();
        }


        //Check if the Person is once waited or Twice
        //If Twice then remove the person from list
        if ($serial % 10 != 0) {
            //Not First Time
            //Get Details to save in Bounced Table
            $sql_query_string = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid AND serial = :serial";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
            $find_query->bindParam(':serial', $serial, PDO::PARAM_STR);
            $find_query->execute();
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            $cname = $temp['customer_name'];
            $cemail = $temp['customer_email'];
            $id = $temp['id'];

            //Delete
            $sql_query_string = "DELETE FROM " . $dbname . ".live_queue WHERE id = :id";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':id', $id, PDO::PARAM_INT);
            $find_query->execute();

            //Save in Bounced Table
            $sql_query_string = "INSERT INTO " . $dbname . ".bounced (storeuid,counteruid,customer_name,customer_email) VALUES (?,?,?,?)";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(1, $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(2, $counteruid, PDO::PARAM_INT);
            $find_query->bindParam(3, $cname, PDO::PARAM_STR);
            $find_query->bindParam(4, $cemail, PDO::PARAM_STR);
            $find_query->execute();

            $data['status'] = True;
            $data['status_code'] = 200;
            echo json_encode($data);
            die();
        } else {
            //First Time
            //Get nth Record
            $sql_query_string = "SELECT serial FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid ORDER BY serial LIMIT :dminusone,1";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
            $t = ($down - 1);
            $find_query->bindParam(':dminusone', $t, PDO::PARAM_INT);
            $find_query->execute();

            if ($find_query->rowCount() > 0) {
                //Found
                $find_query->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_query->fetch();

                $nserial = $temp['serial'];
                $nserial++;

                $sql_query_string = "UPDATE " . $dbname . ".live_queue SET serial = :nserial WHERE storeuid = :sid AND counteruid = :cid AND serial = :serial";
                $find_query = $mysql_conn->prepare($sql_query_string);
                $find_query->bindParam(':nserial', $nserial, PDO::PARAM_INT);
                $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
                $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
                $find_query->execute();

                $data['status'] = True;
                $data['status_code'] = 200;
                echo json_encode($data);
                die();

            } else {
                //Less than down marked persons in queue. Putting in last

                //Getting Present Largest id
                $sql_query_string = "SELECT MAX(id) AS id FROM " . $dbname . ".live_queue";
                $find_query = $mysql_conn->prepare($sql_query_string);
                $find_query->execute();
                $find_query->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_query->fetch();
                $max_id = $temp['id'];
                $max_id++;
                $mserial = $max_id * 10;

                $sql_query_string = "UPDATE " . $dbname . ".live_queue SET serial = :mserial WHERE storeuid = :sid AND serial = :serial AND queueuid = :qid AND counteruid = :cid";
                $find_query = $mysql_conn->prepare($sql_query_string);
                $mserial = $max_id * 10;
                $find_query->bindParam(':mserial', $mserial, PDO::PARAM_INT);
                $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                $find_query->bindParam(':serial', $serial, PDO::PARAM_INT);
                $find_query->bindParam(':qid', $qid, PDO::PARAM_STR);
                $find_query->bindParam(':cid', $counteruid, PDO::PARAM_INT);
                $find_query->execute();


            }

            $data['status'] = True;
            $data['status_code'] = 200;
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

