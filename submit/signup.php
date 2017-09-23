<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/10/17
 * Time: 6:45 AM
 */
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';

if (check($_POST)) {
    if (check($_POST['cimage']) && check($_POST['cname']) && check($_POST['ctype']) && check($_POST['email']) && check($_POST['counter']) && check($_POST['admin_first_name']) && check($_POST['admin_last_name']) && check($_POST['contactnumber']) && check($_POST['info']) && check($_POST['password']) && check($_POST['Cpassword']) && check($_POST['latitude']) && check($_POST['longitude'])) {
        $c_name = preg_replace("/[^A-Za-z0-9\"\' ]+/", "", $_POST['cname']);
        $c_type = preg_replace("/[^A-Za-z0-9-\"\' ]+/", "", $_POST['ctype']);
        $email = preg_replace("/[^A-Za-z0-9@.]+/", "", $_POST['email']);
        $counter = preg_replace("/[^0-9]+/", "", $_POST['counter']);
        $first_name = preg_replace("/[^A-Za-z ]+/", "", $_POST['admin_first_name']);
        $last_name = preg_replace("/[^A-Za-z ]+/", "", $_POST['admin_last_name']);
        $contactnumber = preg_replace("/[^0-9]+/", "", $_POST['contactnumber']);
        $latitude = $_POST['latitude'];
        $longitude = $_POST['longitude'];
        $info = ($_POST['info']);
        $password = $_POST['password'];
        $cpassword = $_POST['Cpassword'];
        $image_url = $_POST['cimage'];

        #Check for the email already present or not
        $sql_query_string = "SELECT * FROM " . $dbname . ".login WHERE email = :email LIMIT 1";
        $find_query = $mysql_conn->prepare($sql_query_string);
        $find_query->bindParam(':email', $email, PDO::PARAM_STR);
        $find_query->execute();

        if ($find_query->rowCount() > 0) {
            #Already Registered Store
            $_SESSION['error'] = "The email address is already linked with a Store! Please use a different one.";
            header('Location: /signup/');
            die();
        } else {
            #Check for password same
            if ($password != $cpassword) {
                $_SESSION['error'] = "Passwords don't Match!";
                header('Location: /signup/');
                die();
            }
            #Generate StoreUID
            $storeuid = 0;
            do {

                $storeuid = rand(2, 10000);
                $sql_query_string = "SELECT * FROM " . $dbname . ".store WHERE storeuid = :storeuid LIMIT 1";
                $find_query = $mysql_conn->prepare($sql_query_string);
                $find_query->bindParam(':storeuid', $storeuid, PDO::PARAM_INT);
                $find_query->execute();
            } while ($find_query->rowCount() > 0);

            $sql_query_string = "INSERT INTO " . $dbname . ".login (email,password,storeuid) VALUES(:email,:pass,:storeuid)";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $find_query->bindParam(':email', $email, PDO::PARAM_STR);
            $find_query->bindParam(':pass', $password, PDO::PARAM_STR);
            $find_query->bindParam(':storeuid', $storeuid, PDO::PARAM_INT);
            $find_query->execute();

            $sql_query_string = "INSERT INTO " . $dbname . ".store (storeuid,adminname,storename,image,info,latitude,longitude,email,shoptype,total_counters) VALUES(:storeuid,:adminname,:storename,:image,:info,:latitude,:longitude,:email,:shoptype,:t_counters)";
            $find_query = $mysql_conn->prepare($sql_query_string);
            $name = $first_name . " " . $last_name;

            $find_query->bindParam(':storeuid', $storeuid);
            $find_query->bindParam(':adminname', $name, PDO::PARAM_STR);
            $find_query->bindParam(':storename', $c_name, PDO::PARAM_STR);
            $find_query->bindParam(':image', $image_url, PDO::PARAM_STR);
            $find_query->bindParam(':info', $info, PDO::PARAM_STR);
            $find_query->bindParam(':latitude', $latitude, PDO::PARAM_STR);
            $find_query->bindParam(':longitude', $longitude, PDO::PARAM_STR);
            $find_query->bindParam(':email', $email, PDO::PARAM_STR);
            $find_query->bindParam(':shoptype', $c_type, PDO::PARAM_STR);
            $find_query->bindParam(':t_counters', $counter);

            $find_query->execute();

            $_SESSION['success'] = "Successfully created account!";
            header('Location: /main/success/#success');
            die();
        }
    } else {
        $_SESSION['error'] = "Incomplete Data Received";
        header('Location: /signup/');
        die();
    }
} else {
    $_SESSION['error'] = "Please fill the form again";
    header('Location: /signup/');
    die();
}