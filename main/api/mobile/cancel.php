<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 2:30 AM
 */


/*
 * Status Codes
 * 200 = Succesfully done deleting
 * 402 = Incomplete Data Format
 * 320 = Invalid Data
 * 500 = Verification Error
 * 520 = PDO Error
*/


require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
$data = array();

if (check($_GET['id']) && check($_GET['store_id']) && check($_GET['counter_id']) && check($_GET['email'])) {
    $sql_statement = "SELECT * FROM " . $dbname . ".live_queue WHERE id=:id AND storeuid = :sid AND counteruid = :cid AND customer_email=:email";
    $find_query = $mysql_conn->prepare($sql_statement);
    $find_query->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $find_query->bindParam(':sid', $_GET['store_id'], PDO::PARAM_INT);
    $find_query->bindParam(':cid', $_GET['counter_id'], PDO::PARAM_INT);
    $find_query->bindParam(':email', $_GET['email'], PDO::PARAM_STR);
    try {
        $find_query->execute();
    } catch (PDOException $e) {
        $data['status'] = False;
        $data['status_code'] = 500;
        $data['errorinfo'] = $e->errorInfo;
        $data['message'] = $e->getMessage();
        echo json_encode($data);
        die();
    }


    if ($find_query->rowCount() > 0) {

        $temp = $find_query->fetch();
        //Inserting IN bounced
        $sql_statement = "INSERT INTO " . $dbname . ".bounced (storeuid,counteruid,customer_name,customer_email) VALUES (:sid,:cid,:cname,:cemail)";
        $find_query = $mysql_conn->prepare($sql_statement);
        $find_query->bindParam(':cname', $temp['customer_name'], PDO::PARAM_STR);
        $find_query->bindParam(':sid', $_GET['store_id'], PDO::PARAM_INT);
        $find_query->bindParam(':cid', $_GET['counter_id'], PDO::PARAM_INT);
        $find_query->bindParam(':cemail', $_GET['email'], PDO::PARAM_STR);
        try {
            $find_query->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 500;
            $data['errorinfo'] = $e->errorInfo;
            $data['message'] = $e->getMessage();
            echo json_encode($data);
            die();
        }

        $sql_statement = "DELETE FROM " . $dbname . ".live_queue WHERE id=:id";
        $find_query = $mysql_conn->prepare($sql_statement);
        $find_query->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        try {
            $find_query->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 500;
            $data['errorinfo'] = $e->errorInfo;
            $data['message'] = $e->getMessage();
            echo json_encode($data);
            die();
        }

        $data['status'] = True;
        $data['status_code'] = 200;
        echo json_encode($data);
        die();
    } else {
        $data['status'] = False;
        $data['status_code'] = 400;
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 540;
    echo json_encode($data);
    die();
}