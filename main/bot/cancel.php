<?php
/**
 * Created by PhpStorm.
 * User: sanu
 * Date: 13/3/18
 * Time: 3:42 PM
 */

require_once $_SERVER['DOCUMENT_ROOT'].'/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/dependencies/check.php';
$new_data = array();
$new_data['messages'] = array();
if(check($_GET['qr_data']) && check($_GET['email']) && check($_GET['otp']))
{

    /*
    $temp['text'] = "Sorry Previously Scanned!";
    array_push($new_data['messages'],$temp);
    echo json_encode($new_data);
    */
    $sql_update = $mysql_conn->prepare('SELECT storeuid FROM '.$dbname.'.counter WHERE qrdata = :qdata');
    $sql_update->bindParam(':qdata',$_GET['qr_data'],PDO::PARAM_STR);
    $storeuid = "";
    try{
        $sql_update->execute();
        if($sql_update->rowCount() > 0)
        {
            $temp_d = $sql_update->fetch();
            $storeuid = $temp_d['storeuid'];
        }
        else{
            //No Counter or Store Found
            $temp['text'] = "Sorry didn't found any Data on that code.Try Again!";
            array_push($new_data['messages'],$temp);
            echo json_encode($new_data);
            die();
        }
    }catch (PDOException $e)
    {
        //PDO Exception
        $temp['text'] = "PDO Exception.Contact Admin!";
        array_push($new_data['messages'],$temp);
        echo json_encode($new_data);
        die();
    }
    $sql_update = $mysql_conn->prepare('SELECT * FROM '.$dbname.'.live_queue WHERE storeuid = :sid AND customer_email = :cmail AND otp = :otp LIMIT 1');
    $sql_update->bindParam(':sid',$storeuid,PDO::PARAM_INT);
    $sql_update->bindParam(':cmail',$_GET['email'],PDO::PARAM_STR);
    $sql_update->bindParam(':otp',$_GET['otp'],PDO::PARAM_INT);
    try{
        $sql_update->execute();
        $temp['stat'] = $sql_update;
        if($sql_update->rowCount() > 0)
        {
            $sql_update->setFetchMode(PDO::FETCH_ASSOC);
            $data = $sql_update->fetch();
            $queueId = $data['id'];
            $sql_update = $mysql_conn->prepare('DELETE FROM '.$dbname.'.live_queue WHERE id = :id');
            $sql_update->bindParam(':id',$queueId,PDO::PARAM_INT);
            try{
                $sql_update->execute();
                $temp['text']= "Your Queue Booking is removed";
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }
            catch (PDOException $e)
            {
                //PDO Exception
                $temp['text'] = "PDO Exception.Contact Admin!";
                array_push($new_data['messages'],$temp);
                echo json_encode($new_data);
                die();
            }
        }
        else{
            //UnAuthorized
            $temp['text'] = "Sorry didn't found any Data on that Email or Wrong OTP.Try Again!";
            array_push($new_data['messages'],$temp);
            echo json_encode($new_data);
            die();
        }
    }catch (PDOException $e)
    {
        //PDO Exception
        $temp['text'] = "PDO Exception.Contact Admin!";
        array_push($new_data['messages'],$temp);
        echo json_encode($new_data);
        die();
    }
}
else
{
    //PDO Exception
    $temp['text'] = "Incomplete Data!";
    array_push($new_data['messages'],$temp);
    echo json_encode($new_data);
    die();
}