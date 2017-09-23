<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 22/09/17
 * Time: 10:25 AM
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/dependencies/check.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/confidential/connector.php';

/*
 * Status Codes
 * 400 = Invalid or No Data
 * 200 = All good
 * 300 = No Suggestions
 * */

$data = array();
$radius = 0.5;

if(check($_GET['latitude']) && check($_GET['longitude']))
{
    $latitude = $_GET['latitude'];
    $longitude = $_GET['longitude'];

    //Calculating Boundaries
    $latitude_small = $latitude - $radius;
    $latitude_big = $latitude + $radius;

    $longitude_small = $longitude - $radius;
    $longitude_big = $longitude + $radius;

    //Performing Querry
    $sql_query_string = "SELECT * FROM " . $dbname . ".suggestions WHERE latitude >= :lat_small AND latitude <= :lat_big AND longitude >= :long_small AND longitude <= :long_big LIMIT 100";
    $find_query = $mysql_conn->prepare($sql_query_string);
    $find_query->bindParam(':lat_small', $latitude_small);
    $find_query->bindParam(':lat_big', $latitude_big);
    $find_query->bindParam(':long_small', $longitude_small);
    $find_query->bindParam(':long_big', $longitude_big);
    $find_query->execute();

    if($find_query->rowCount() > 0)
    {
        $data['status'] = True;
        $data['status_code'] = 200;
        $i = 0;
        $find_query->setFetchMode(PDO::FETCH_ASSOC);
        while ($fetched = $find_query->fetch())
        {
            array_push($data,$fetched);
            ++$i;
        }
        $data['size'] = $i;
        echo json_encode($data);
        die();
    }
    else
    {
        $data['status'] = False;
        $data['status_code'] = 400;
        echo json_encode($data);
        die();
    }

}
else
{
    $data['status'] = False;
    $data['status_code'] = 300;
    echo json_encode($data);
    die();
}