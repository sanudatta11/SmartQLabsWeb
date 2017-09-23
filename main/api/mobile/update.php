<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/17
 * Time: 7:14 AM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/global_var.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

$data = array();
$scan_fix = $scan_global_fix;
/*
 * Status Codes:-
 * 200 = All good
 * 201 = No Update in Queue Status
 * 500 = PDO Exception
 * 400 = No Queue Found for the given data
 * 420 = Fatal Error while checking person before
 * 430 = No Counter
 * 540 = Incomplete Data Provided
 * 430 = Data Given Doesnot Match with Records
 * 700 = Counter is closed
 * */

/*
 * API Details:-
 * This API will be called in every 10 seconds from Android to update the scenario
 * */

if (check($_GET['id']) && check($_GET['store_id']) && check($_GET['counter_id']) && check($_GET['group_id'])) {

    $id = $_GET['id'];
    $storeuid = $_GET['store_id'];
    $counteruid = $_GET['counter_id'];
    $groupuid = $_GET['group_id'];

    //Counter is allocated if counteruid is given
    if ($counteruid != -1) {
        //Check if Counter is closed or not
        $sql_statement = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid AND counteruid = :cid AND serving = 0 LIMIT 1";
        $find_querry = $mysql_conn->prepare($sql_statement);
        $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
        $find_querry->bindParam(':cid', $counteruid, PDO::PARAM_INT);
        try {
            $find_querry->execute();
        } catch (PDOException $e) {
            $data['status'] = False;
            $data['status_code'] = 500;
            $data['message'] = $e->getMessage();
            $data['errorinfo'] = $e->errorInfo;
            echo json_encode($data);
            die();
        }
        if ($find_querry->rowCount() > 0) {
            $data['status'] = False;
            $data['status_code'] = 700;
            echo json_encode($data);
            die();
        }
    }


    $sql_statement = "SELECT * FROM " . $dbname . ".counter WHERE storeuid = :sid AND groupuid = :gid LIMIT 1";
    $find_querry = $mysql_conn->prepare($sql_statement);
    $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    $find_querry->bindParam(':gid', $groupuid, PDO::PARAM_INT);
    try {
        $find_querry->execute();
    } catch (PDOException $e) {
        $data['status'] = False;
        $data['status_code'] = 500;
        $data['message'] = $e->getMessage();
        $data['errorinfo'] = $e->errorInfo;
        echo json_encode($data);
        die();
    }

    $sql_statement = "SELECT * FROM " . $dbname . ".live_queue WHERE id = :id AND storeuid = :sid AND groupuid = :gid LIMIT 1";
    $test_querry = $mysql_conn->prepare($sql_statement);
    $test_querry->bindParam(':id', $id, PDO::PARAM_INT);
    $test_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
    $test_querry->bindParam(':gid', $groupuid, PDO::PARAM_INT);

    try {
        $test_querry->execute();
    } catch (PDOException $e) {
        $data['status'] = False;
        $data['status_code'] = 500;
        $data['message'] = $e->getMessage();
        $data['errorinfo'] = $e->errorInfo;
        echo json_encode($data);
        die();
    }

    if ($test_querry->rowCount() <= 0) {
        $data['status'] = False;
        $data['status_code'] = 400;
        echo json_encode($data);
        die();
    }


    if ($find_querry->rowCount() > 0) {
        $test_querry->setFetchMode(PDO::FETCH_ASSOC);
        $fall = $test_querry->fetch();
        $serial = $fall['serial'];
        $find_querry->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $find_querry->fetch();

        //Taking total counters number
        $total_counters = $find_querry->rowCount();


        if ($counteruid != -1 || $temp['counteruid'] != -1) {
            $counteruid = $temp['counteruid'];
            //Is  In a fixed Queue
            $sql_statement = "SELECT COUNT(*) AS count FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid AND serial <= :serial";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
            $find_querry->bindParam(':cid', $counteruid, PDO::PARAM_INT);
            $find_querry->bindParam(':serial', $serial, PDO::PARAM_INT);
            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 500;
                $data['message'] = $e->getMessage();
                $data['errorinfo'] = $e->errorInfo;
                echo json_encode($data);
                die();
            }

            $find_querry->setFetchMode(PDO::FETCH_ASSOC);
            $temp = $find_querry->fetch();
            $queue_no = $temp['count'];

            if ($find_querry->rowCount() > 0) {
                //Getting Estimated Avg time
                $sql_statement = "SELECT ceil(AVG(wait)) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid";
                $find_querry = $mysql_conn->prepare($sql_statement);
                $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                try {
                    $find_querry->execute();
                } catch (PDOException $e) {
                    $data['status'] = False;
                    $data['status_code'] = 500;
                    $data['message'] = $e->getMessage();
                    $data['errorinfo'] = $e->errorInfo;
                    echo json_encode($data);
                    die();
                }
                $find_querry->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_querry->fetch();
                $data['status'] = True;
                $data['status_code'] = 200;
                $data['time'] = ceil($temp['wait'] / 60) * ($queue_no - 1);
                $data['queue_no'] = $queue_no;
                $data['counter_id'] = $counteruid;
                if ($find_querry->rowCount() == 1)
                    $data['turn'] = True;
                echo json_encode($data);
                die();
            } else {
                $data['status'] = False;
                $data['status_code'] = 420;
                echo json_encode($data);
                die();
            }
        } else {
            //He is not in a Queue Fixed
            $sql_statement = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = -1 LIMIT 1";
            $find_querry = $mysql_conn->prepare($sql_statement);
            $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);

            try {
                $find_querry->execute();
            } catch (PDOException $e) {
                $data['status'] = False;
                $data['status_code'] = 500;
                $data['message'] = $e->getMessage();
                $data['errorinfo'] = $e->errorInfo;
                echo json_encode($data);
                die();
            }
            if ($find_querry->rowCount() > 0) {
                $find_querry->setFetchMode(PDO::FETCH_ASSOC);
                $temp = $find_querry->fetch();

                if ($temp['id'] == $id) {
                    //Cool You are at the top thus make it happen
                    $min_temp = PHP_INT_MAX;
                    $min_id = -1;
                    for ($i = 1; $i <= $total_counters; ++$i) {
                        $sql_statement = "SELECT COUNT(*) AS count FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid";
                        $find_query = $mysql_conn->prepare($sql_statement);
                        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                        $find_query->bindParam(':cid', $i, PDO::PARAM_INT);
                        $find_query->execute();
                        $find_query->setFetchMode(PDO::FETCH_ASSOC);
                        $temp = $find_query->fetch();
                        if ($temp['count'] < $scan_fix && $min_temp > $temp['count']) {
                            $min_temp = $temp['count'];
                            $min_id = $i;
                        }
                    }

                    if ($min_id != -1 && $min_temp != PHP_INT_MAX) {
                        //One Counter Empty
                        //Put this person there
                        $sql_statement = "UPDATE " . $dbname . ".live_queue SET counteruid = :cid WHERE id = :id";
                        $update_querry = $mysql_conn->prepare($sql_statement);
                        $update_querry->bindParam(':cid', $min_id, PDO::PARAM_INT);
                        $update_querry->bindParam(':id', $id, PDO::PARAM_INT);
                        try {
                            $update_querry->execute();
                        } catch (PDOException $e) {
                            $data['status'] = False;
                            $data['status_code'] = 500;
                            $data['message'] = $e->getMessage();
                            $data['errorinfo'] = $e->errorInfo;
                            echo json_encode($data);
                            die();
                        }

                        //Getting Estimated Avg time
                        $sql_statement = "SELECT AVG(wait) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid";
                        $find_query = $mysql_conn->prepare($sql_statement);
                        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                        $find_query->bindParam(':cid', $min_id, PDO::PARAM_INT);
                        $find_query->execute();
                        $find_query->setFetchMode(PDO::FETCH_ASSOC);
                        $temp = $find_query->fetch();
                        $avg_time = ceil($temp['wait'] / 60);
                        echo $avg_time;

                        $data['status'] = True;
                        $data['status_code'] = 200;
                        $data['time'] = $avg_time * $min_temp;
                        $data['queue_no'] = $min_temp;
                        $data['counter_id'] = $min_id;

                        echo json_encode($data);
                        die();
                    } else {
                        //No Counter Empty
                        $data['status'] = True;
                        $data['status_code'] = 201;
                        echo json_encode($data);
                        die();
                    }
                } else {
                    $other_id = $temp['id'];

                    $min_temp = PHP_INT_MAX;
                    $min_id = -1;
                    for ($i = 1; $i <= $total_counters; ++$i) {
                        $sql_statement = "SELECT COUNT(*) AS count FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND counteruid = :cid";
                        $find_query = $mysql_conn->prepare($sql_statement);
                        $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                        $find_query->bindParam(':cid', $i, PDO::PARAM_INT);
                        $find_query->execute();
                        $find_query->setFetchMode(PDO::FETCH_ASSOC);
                        $temp = $find_query->fetch();
                        if ($temp['count'] < $scan_fix && $min_temp > $temp['count']) {
                            $min_temp = $temp['count'];
                            $min_id = $i;
                        }
                    }

                    if ($min_id != -1 && $min_temp != PHP_INT_MAX) {
                        //One Counter Empty put the other person
                        $sql_statement = "UPDATE " . $dbname . ".live_queue SET counteruid = :cid WHERE id = :id";
                        $update_querry = $mysql_conn->prepare($sql_statement);
                        $update_querry->bindParam(':cid', $min_id, PDO::PARAM_INT);
                        $update_querry->bindParam(':id', $other_id, PDO::PARAM_INT);
                        try {
                            $update_querry->execute();
                        } catch (PDOException $e) {
                            $data['status'] = False;
                            $data['status_code'] = 500;
                            $data['message'] = $e->getMessage();
                            $data['errorinfo'] = $e->errorInfo;
                            echo json_encode($data);
                            die();
                        }
                    }

                    //Update the self Queue Number
                    $sql_statement = "SELECT * FROM " . $dbname . ".live_queue WHERE storeuid = :sid AND groupuid = :gid AND counteruid = -1 AND id <= :id";
                    $find_querry = $mysql_conn->prepare($sql_statement);
                    $find_querry->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                    $find_querry->bindParam(':gid', $groupuid, PDO::PARAM_INT);
                    $find_querry->bindParam(':id', $id, PDO::PARAM_INT);
                    try {
                        $find_querry->execute();
                    } catch (PDOException $e) {
                        $data['status'] = False;
                        $data['status_code'] = 500;
                        $data['message'] = $e->getMessage();
                        $data['errorinfo'] = $e->errorInfo;
                        echo json_encode($data);
                        die();
                    }
                    $queue_no = $find_querry->rowCount();

                    //Getting Estimated Avg time
                    $sql_statement = "SELECT AVG(wait) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid ";
                    $find_query = $mysql_conn->prepare($sql_statement);
                    $find_query->bindParam(':sid', $storeuid, PDO::PARAM_INT);
                    $find_query->execute();
                    $find_query->setFetchMode(PDO::FETCH_ASSOC);
                    $temp = $find_query->fetch();
                    $avg_time = $temp['wait'];

                    $data['status_code'] = 200;
                    $data['status'] = True;
                    $data['time'] = $avg_time * $min_temp;
                    $data['queue_no'] = ceil($queue_no / $total_counters) + $scan_fix;
                    $data['counter_id'] = -1;
                    echo json_encode($data);
                    die();
                }
            } else {
                $data['status'] = False;
                $data['status_code'] = 440;
                echo json_encode($data);
                die();
            }
        }
    } else {
        $data['status'] = False;
        $data['status_code'] = 430;
        echo json_encode($data);
        die();
    }
} else {
    $data['status'] = False;
    $data['status_code'] = 540;
    echo json_encode($data);
    die();
}