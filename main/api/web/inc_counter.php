<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

if (check($_SESSION['storeuid']) && check($_SESSION['email']) && check($_GET['increase']) && check($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    $storeuid = $_SESSION['storeuid'];
    $email = $_SESSION['email'];
    $inc = $_GET['increase'];

    $sql_statement = "UPDATE " . $dbname . ".store SET total_counters = total_counters + :add WHERE storeuid = :sid";
    $add_querry = $mysql_conn->prepare($sql_statement);
    $add_querry->bindParam(':add', $inc, PDO::PARAM_INT);
    $add_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    try {
        $add_querry->execute();
    } catch (PDOException $e) {
        $data['status'] = False;
        $data['status_code'] = 500;
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
    $data['status_code'] = 510;
    die();
}