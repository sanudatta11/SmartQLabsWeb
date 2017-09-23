<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 7:28 PM
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

if (check($_POST['Aname']) && check($_POST['adImg']) && check($_POST['adInfo']) && check($_POST['typeAd']) && check($_POST['latAd']) && check($_POST['longAd'])) {
    $ad_name = $_POST['Aname'];
    $imglink = $_POST['adImg'];
    $info = $_POST['adInfo'];
    $type = $_POST['typeAd'];
    $lat = $_POST['latAd'];
    $long = $_POST['longAd'];

    $sql_statement = "INSERT INTO " . $dbname . ".suggestions (name,info,url,latitude,longitude,type) VALUES (:name,:info,:url,:lat,:long,:type) ";
    $find_query = $mysql_conn->prepare($sql_statement);
    $find_query->bindParam(':name', $ad_name, PDO::PARAM_STR);
    $find_query->bindParam(':info', $info, PDO::PARAM_STR);
    $find_query->bindParam(':url', $imglink, PDO::PARAM_STR);
    $find_query->bindParam(':lat', $lat);
    $find_query->bindParam(':long', $long);
    $find_query->bindParam(':type', $type, PDO::PARAM_STR);
    try {
        $find_query->execute();
    } catch (PDOException $e) {
        $_SESSION['error'] = "Sorry! Unknown Database Error Occured!";
        header('Location: /main/');
        die();
    }
    $_SESSION['success'] = True;
    header('Location: /add/');
    die();
} else {
    header('Location: /main/');
    die();
}