<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/17
 * Time: 9:40 AM
 */

/*
 * Status Codes
 * 200 = Succesfully done adding to Queue
 * 300 = Already Scanned
 * 402 = Wrong Data Format
 * 400 = No Counter Active for specific QR Code
 * */

/*
 * API Details-
 * This API is called by the Android and thus do not require and session variable.
 * This api will be the one called upon, when the QR code is scanned
 * Needed = Email , qr data and Name;
 * */

require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

$scan_fix = $scan_global_fix;
$digits = $scan_otp_length;

$data = array();

if (check($_GET['email']) && check($_GET['qr_data']) && check($_GET['name'])) {
    $cemail = $_GET['email'];
    $qrdata = $_GET['qr_data'];
    $cname = $_GET['name'];

    $sql_statement = "SELECT * FROM " . $dbname . ".counter WHERE qrdata = :qrdata AND serving = 1";
    $find_query = $mysql_conn->prepare($sql_statement);
    $find_query->bindParam(':qrdata', $qrdata, PDO::PARAM_STR);
    $find_query->execute();
    $total_counters = $find_query->rowCount();

    if ($total_counters > 0) {
        $added = False;
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        $comp_image_url = "";
        $comp_name = "";
        $comp_info = "";
        $counter_id = array();

        $t = 0;
        //Adding specific Counter IDs to pool
        while ($temp = $find_query->fetch()) {
            array_push($counter_id, $temp['counteruid']);
            if (!$t) {
                $storeuid = $temp['storeuid'];
                $groupuid = $temp['groupuid'];
            }
            $t++;
        }

        //Check if previously Scanned or nit
        $sql_statement = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND customer_email = :email AND groupuid = :gid LIMIT 1";
        $find_query = $mysql_conn->prepare($sql_statement);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':email', $cemail, PDO::PARAM_STR);
        $find_query->bindParam(':gid', $groupuid, PDO::PARAM_INT);
        try {
            $find_query->execute();
            if ($find_query->rowCount() > 0) {
                $data['status'] = False;
                $data['status_code'] = 310;
                if(isset($_GET['bot']) && $_GET['bot'] == 1)
                {
                    $new_data = array();
                    $new_data['messages'] = array();
                    $temp['text'] = "Sorry Previously Scanned!";
                    array_push($new_data['messages'],$temp);
                    echo json_encode($new_data);
                    die();
                }
                echo json_encode($data);
                die();
            }
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 500;
            $data['message'] = $e->getMessage();
            $data['errorinfo'] = $e->errorInfo;
            if(isset($_GET['bot']) && $_GET['bot'] == 1)
            {
                $new_data = array();
                $new_data['messages'] = array();
                $temp['text'] = "PDO Exception! Contact Admin!";
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }
            echo json_encode($data);
            die();
        }

        //Get Image URL of the store,Name of the store and other details
        $sql_statement = "SELECT * FROM " . $dbname . ".store WHERE storeuid = :sid";
        $find_query = $mysql_conn->prepare($sql_statement);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        try {
            $find_query->execute();
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            $comp_image_url = $temp['image'];
            $comp_name = $temp['storename'];
            $comp_info = $temp['info'];
            $lat = $temp['latitude'];
            $long = $temp['longitude'];

        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 500;
            $data['message'] = $e->getMessage();
            $data['errorinfo'] = $e->errorInfo;
            if(isset($_GET['bot']) && $_GET['bot'] == 1)
            {
                $new_data = array();
                $new_data['messages'] = array();
                $temp['text'] = "PDO Exception! Contact Admin!";
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }
            echo json_encode($data);
            die();
        }

        //Checking Per Queue Status
        $min_temp = PHP_INT_MAX;
        $min_id = -1;
        for ($i = 1; $i <= $total_counters; ++$i) {
            $sql_statement = "SELECT COUNT(*) AS count FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $counter_id[$i], PDO::PARAM_INT);
            $find_query->execute();
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            if ($temp['count'] < $scan_fix && $min_temp > $temp['count']) {
                $min_temp = $temp['count'];
                $min_id = $counter_id[$i - 1];
            }
        }

        //Getting Present Largest id
        $sql_query_string = "SELECT MAX(id) AS id FROM " . $dbname . ".live_queue";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->execute();
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $find_query->fetch();
        $max_id = $temp['id'];
        $max_id++;
        $mserial = $max_id * 10;

        //Inserting it in the minimum One
        if ($min_temp != PHP_INT_MAX && $min_id != -1) {
            //Add to the same queue
            $added = True;
            $sql_statement = "INSERT INTO " . $dbname . ".live_queue (id,serial,storeuid,counteruid,groupuid,customer_name,customer_email,queueuid,otp) VALUES (:max_id,:max_serial,:sid,:cid,:gid,:cname,:cemail,:qid,:otp)";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':max_id', $max_id, PDO::PARAM_INT);
            $find_query->bindParam(':max_serial', $mserial, PDO::PARAM_INT);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $min_id, PDO::PARAM_INT);
            $find_query->bindParam(':gid', $groupuid, PDO::PARAM_INT);
            $find_query->bindParam(':cname', $cname, PDO::PARAM_STR);
            $find_query->bindParam(':cemail', $cemail, PDO::PARAM_STR);
            $letter = chr(64 + rand(0, 26));
            $qid = (string)($letter . $max_id);
            $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $find_query->bindParam(':qid', $qid, PDO::PARAM_STR);
            $find_query->bindParam(':otp', $otp, PDO::PARAM_INT);
            try {
                $find_query->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 500;
                $data['message'] = $e->getMessage();
                $data['errorinfo'] = $e->errorInfo;
                if(isset($_GET['bot']) && $_GET['bot'] == 1)
                {
                    $new_data = array();
                    $new_data['messages'] = array();
                    $temp['text'] = "PDO Exception! Contact Admin!";
                    array_push($new_data['messages'],$temp);
                    echo json_encode($new_data);
                    die();
                }
                echo json_encode($data);
                die();
            }
            //Getting Queue No
            $data['queue_code'] = $qid;

            //Getting Estimated Avg time
            $sql_statement = "SELECT AVG(wait) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':cid', $min_id, PDO::PARAM_INT);
            $find_query->execute();
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            $avg_time = $temp['wait'];

            $data['status'] = True;
            $data['status_code'] = 200;
            $data['otp'] = $otp;

            $data['id'] = $max_id;

            $queue_details = array();
            $queue_details['queue_no'] = $min_temp + 1;
            $queue_details['store_id'] = $storeuid;
            $queue_details['counter_id'] = $min_id;
            if (!$min_temp)
                $queue_details['turn'] = True;
            $queue_details['time'] = ceil($avg_time / 60);
            $queue_details['group_id'] = $groupuid;

            $cmp_details = array();
            $cmp_details['comp_name'] = $comp_name;
            $cmp_details['image_url'] = $comp_image_url;
            $cmp_details['comp_info'] = $comp_info;
            $cmp_details['latitude'] = $lat;
            $cmp_details['longitude'] = $long;


            $data['queue'] = $queue_details;
            $data['company'] = $cmp_details;

            if(isset($_GET['bot']) && $_GET['bot'] == 1)
            {
                $new_data = array();
                $new_data['messages'] = array();
                $temp['text'] = "Queued Properly!";
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Queue Code is ".$data['queue_code'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Position is ".$data['queue']['queue_no'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Counter Id is ".$data['queue']['counter_id'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your OTP is ".$data['otp'];
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }

            echo json_encode($data);
            die();
        } else {

            //Total Waiting People
            $sql_statement = "SELECT COUNT(*) AS count FROM " . $dbname . ".live_queue WHERE storeuid = :sid";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->execute();
            $total_virtual = $find_query->rowCount();

            //Avg time
            //Getting Estimated Avg time
            $sql_statement = "SELECT ceil(AVG(wait)) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->execute();
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            $avg_time = $temp['wait'];

            //Add to the Virtual Queue
            $sql_statement = "INSERT INTO " . $dbname . ".live_queue (id,serial,storeuid,counteruid,groupuid,customer_name,customer_email,queueuid,otp) VALUES (:max_id,:max_serial,:sid,:cid,:gid,:cname,:cemail,:qid,:otp)";
            $find_query = $mysql_conn->prepare($sql_statement);
            $find_query->bindParam(':max_id', $max_id, PDO::PARAM_INT);
            $find_query->bindParam(':max_serial', $mserial, PDO::PARAM_INT);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindValue(':cid', -1, PDO::PARAM_INT);
            $find_query->bindParam(':gid', $groupuid, PDO::PARAM_INT);
            $find_query->bindParam(':cname', $cname, PDO::PARAM_STR);
            $find_query->bindParam(':cemail', $cemail, PDO::PARAM_STR);
            $letter = chr(64 + rand(0, 26));
            $qid = (string)($letter . $max_id);
            $otp = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $find_query->bindValue(':qid', $qid, PDO::PARAM_STR);
            $find_query->bindParam(':otp', $otp, PDO::PARAM_INT);
            try {
                $find_query->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 500;
                $data['message'] = $e->getMessage();
                $data['errorinfo'] = $e->errorInfo;
                if(isset($_GET['bot']) && $_GET['bot'] == 1)
                {
                    $new_data = array();
                    $new_data['messages'] = array();
                    array_push($new_data['messages'],"PDO Exception! Contact Admin.");
                    echo json_encode($new_data);
                    die();
                }
                echo json_encode($data);
                die();
            }
            $data['id'] = $max_id;
            $data['status'] = True;
            $data['status_code'] = 300;
            $data['otp'] = $otp;
            $data['queue_code'] = $qid;

            $queue_details['queue_no'] = $total_virtual + 1 + $scan_fix;
            $queue_details['store_id'] = $storeuid;
            $queue_details['counter_id'] = -1;
            $queue_details['time'] = ceil($avg_time / 60);
            $queue_details['group_id'] = $groupuid;

            $cmp_details = array();
            $cmp_details['comp_name'] = $comp_name;
            $cmp_details['image_url'] = $comp_image_url;
            $cmp_details['comp_info'] = $comp_info;
            $cmp_details['latitude'] = $lat;
            $cmp_details['longitude'] = $long;


            $data['queue'] = $queue_details;
            $data['company'] = $cmp_details;

            if(isset($_GET['bot']) && $_GET['bot'] == 1)
            {
                $new_data = array();
                $new_data['messages'] = array();
                $temp['text'] = "Queued Properly!";
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Queue Code is ".$data['queue_code'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Position is ".$data['queue']['queue_no'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your Counter Id is ".$data['queue']['counter_id'];
                array_push($new_data['messages'],$temp);
                $temp['text'] = "Your OTP is ".$data['otp'];
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }

            echo json_encode($data);
            die();
        }
    } else {
        $data['status'] = False;
        $data['status_code'] = 400;
        if(isset($_GET['bot']) && $_GET['bot'] == 1)
        {
            $new_data = array();
            $new_data['messages'] = array();
            $temp['text'] = "No Counter Active!";
            array_push($new_data['messages'],$temp);
            echo json_encode($new_data);
            die();
        }
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 402;
    if(isset($_GET['bot']) && $_GET['bot'] == 1)
    {
        $new_data = array();
        $new_data['messages'] = array();
        $temp['text'] = "Wrong Data Provided!";
        array_push($new_data['messages'],$temp);
        echo json_encode($new_data);
        die();
    }
    echo json_encode($data);
    die();
}