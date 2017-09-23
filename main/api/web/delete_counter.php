<?php

/*
 * Status Codes
 * 200 = All Good Counter Added
 * 500 = PDO Exception
 * 510 = Session Mismatch
 * 520 = Improper Session Records
 * */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
$data = array();

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_POST['cemail']) && check($_POST['cid'])) {
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];

    $counter_email = $_POST['cemail'];
    $counter_id = $_POST['cid'];

    $sql_statement = "SELECT * FROM " . $dbname . ".store WHERE storeuid = :sid AND email = :email LIMIT 1";
    $find_querry = $mysql_conn->prepare($sql_statement);
    $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    $find_querry->bindParam(':email', $email, PDO::PARAM_STR);
    try {
        $find_querry->execute();
    } catch (PDOException $e) {
        $data['status'] = False;
        $data['status_code'] = 510;
        $data['errorinfo'] = $e->getMessage();
        $data['errorcode'] = $e->errorInfo;
        echo json_encode($data);
        die();
    }
    if ($find_querry->rowCount() > 0) {
        $sql_statement = "DELETE FROM " . $dbname . ".counter WHERE storeuid = :sid AND email = :email AND counteruid = :cid LIMIT 1";
        $find_querry = $mysql_conn->prepare($sql_statement);
        $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_querry->bindParam(':cid', $counter_id, PDO::PARAM_INT);
        $find_querry->bindParam(':email', $counter_email, PDO::PARAM_INT);
        try {
            $find_querry->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 530;
            $data['errorinfo'] = $e->getMessage();
            $data['errorcode'] = $e->errorInfo;
            echo json_encode($data);
            die();
        }

        $sql_statement = "DELETE FROM " . $dbname . ".login WHERE storeuid = :sid AND email = :email";
        $find_querry = $mysql_conn->prepare($sql_statement);
        $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_querry->bindParam(':email', $counter_email, PDO::PARAM_INT);
        try {
            $find_querry->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 530;
            $data['errorinfo'] = $e->getMessage();
            $data['errorcode'] = $e->errorInfo;
            echo json_encode($data);
            die();
        }

        $sql_statement = "UPDATE " . $dbname . ".store SET total_alloted = total_alloted - 1 WHERE storeuid = :sid";
        $find_querry = $mysql_conn->prepare($sql_statement);
        $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        try {
            $find_querry->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 530;
            $data['errorinfo'] = $e->getMessage();
            $data['errorcode'] = $e->errorInfo;
            echo json_encode($data);
            die();
        }
        $data['status'] = True;
        $data['status_code'] = 200;
        echo json_encode($data);
        die();
    } else {
        $data['status'] = False;
        $data['status_code'] = 520;
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 510;
    die();
}