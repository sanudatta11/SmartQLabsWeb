<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/9/17
 * Time: 7:51 AM
 */
/*
 * Status Codes
 * 200 = All Good Counter Added
 * 400 = Wrong Input Data
 * 500 = PDO Exception
 * 510 = Session Mismatch
 * 520 = Improper Session Records
 * 320 = Passwords Mismatch
 * 350 = Counter limit Reached
 * 370 = Fatal Counter Error
 * */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
$data = array();

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_POST['cemail']) && check($_POST['password']) && check($_POST['cpassword']) && check($_POST['groupid'])) {
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];

    $counter_email = $_POST['cemail'];
    $password = $_POST['password'];
    $r_password = $_POST['cpassword'];
    $groupuid = $_POST['groupid'];

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
        if ($password != $r_password) {
            //Password Donot Match
            $data['status'] = False;
            $data['status_code'] = 320;
            echo json_encode($data);
            die();
        }

        //Find Total Counters
        $sql_statement = "SELECT * FROM " . $dbname . ".store WHERE storeuid = :sid LIMIT 1";
        $find_querry = $mysql_conn->prepare($sql_statement);
        $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_querry->execute();

        if ($find_querry->rowCount() > 0) {
            $find_querry->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_querry->fetch();
            $total_counters = $temp['total_counters'];
            $total_alloted = $temp['total_alloted'];
            if ($total_alloted == $total_counters) {
                //Limit Reached
                $data['status'] = False;
                $data['status_code'] = 350;
                die();
            }
            if ($total_counters < $total_alloted) {
                //Cannot Happen
                $data['status'] = False;
                $data['status_code'] = 370;
                die();
            }

            //Find Next Counter UID
            $sql_statement = "SELECT max(counteruid) AS max FROM " . $dbname . ".counter WHERE storeuid = :sid";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 510;
                $data['type'] = 1;
                echo json_encode($data);
                die();
            }
            $next_counter = 1;
            if ($find_querry->rowCount() > 0) {
                $find_querry->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_querry->fetch();
                $next_counter = $temp['max'];
                $next_counter++;
            }

            //Find Previous Counters in there in Group or not
            //If yes, take QR Data or else Generate
            $sql_statement = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid AND groupuid = :gid LIMIT 1";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_querry->bindParam(':gid', $groupuid, PDO::PARAM_INT);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 520;
                echo json_encode($data);
                die();
            }

            $qrdata = "";
            if ($find_querry->rowCount() > 0) {
                //Take it
                $find_querry->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_querry->fetch();
                $qrdata = $temp['qrdata'];
            } else {
                //Make it
                $letter = chr(64 + rand(0, 26));
                $qrdata = sha1($storeuid . $groupuid . $letter);
            }

            $sql_statement = "INSERT INTO " . $dbname . ".counter (email,storeuid,counteruid,groupuid,qrdata) VALUES (:email,:sid,:cid,:gid,:qrdata)";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':email', $counter_email, PDO::PARAM_STR);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_querry->bindParam(':cid', $next_counter, PDO::PARAM_INT);
            $find_querry->bindParam(':gid', $groupuid, PDO::PARAM_INT);
            $find_querry->bindParam(':qrdata', $qrdata, PDO::PARAM_STR);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 530;
                echo json_encode($data);
                die();
            }

            $sql_statement = "INSERT INTO " . $dbname . ".login (email,password,admin,storeuid) VALUES (:email,:pass,:admin,:sid)";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':email', $counter_email, PDO::PARAM_STR);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_querry->bindParam(':pass', $password, PDO::PARAM_STR);
            $find_querry->bindValue(':admin', 2);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 530;
                echo json_encode($data);
                die();
            }

            $sql_statement = "UPDATE " . $dbname . ".store SET total_alloted = total_alloted + 1 WHERE storeuid = :sid";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 540;
                echo json_encode($data);
                die();
            }

            $data['status'] = True;
            $data['status_code'] = 200;
            echo json_encode($data);
            die();
        } else {
            $data['status'] = False;
            $data['status_code'] = 510;
            die();
        }

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