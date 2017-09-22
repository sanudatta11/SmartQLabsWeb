<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 4/9/17
 * Time: 9:00 AM
 */


session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();

if (check($_SESSION['storeuid']) && check($_SESSION['email'])) {
    $storeuid = $_SESSION['storeuid'];
    $counteruid = $_SESSION['counteruid'];
    $email = $_SESSION['email'];

    #Check for the email already present or not
    $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email AND storeuid = :sid AND sessionid = :sessid AND admin = 1 LIMIT 1";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':email', $email, PDO::PARAM_STR);
    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    $find_query->bindParam(':sessid', session_id(), PDO::PARAM_STR);
    $find_query->execute();

    if ($find_query->rowCount() > 0) {
        #Check for the email already present or not
        $sql_query_string = "SELECT * FROM " . $dbname . ".store WHERE storeuid = :sid AND email = :email LIMIT 1";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_query->bindParam(':email', $email, PDO::PARAM_STR);

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
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_query->fetch();
            $data['total_counters'] = $temp['total_counters'];
            $tot = $temp['total_alloted'];
            $data['counters_allocated'] = $tot;

            $counter_details = array();

            $sql_query_string = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid LIMIT :tot";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_query->bindParam(':tot', $tot, PDO::PARAM_INT);
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
            $find_query->setFetchMode(PDO::FETCH_ASSOC);
            while ($temp = $find_query->fetch())
                array_push($counter_details, $temp);

            $data['counter'] = $counter_details;
            $data['status'] = True;
            $data['status_code'] = 200;
            echo json_encode($data);
            die();
        } else {
            //FATAL ERROR
            $data['status'] = False;
            $data['status_code'] = 700;
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