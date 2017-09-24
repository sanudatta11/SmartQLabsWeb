<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 24/9/17
 * Time: 8:11 AM
 */
require_once $_SERVER['DOCUMENT_ROOT'].'/confidential/connector.php';
require_once $_SERVER['DOCUMENT_ROOT'].'/dependencies/check.php';
$new_data = array();
$new_data['messages'] = array();
if(check($_GET['qr_data']) && check($_GET['email']) && check($_GET['name']) && check($_GET['otp']))
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
   $sql_update = $mysql_conn->prepare('SELECT * FROM '.$dbname.'.live_queue WHERE storeuid = :sid AND customer_name = :cname AND customer_email = :cmail AND otp = :otp LIMIT 1');
   $sql_update->bindParam(':sid',$storeuid,PDO::PARAM_INT);
   $sql_update->bindParam(':cname',$_GET['name'],PDO::PARAM_STR);
   $sql_update->bindParam(':cmail',$_GET['email'],PDO::PARAM_STR);
   $sql_update->bindParam(':otp',$_GET['otp'],PDO::PARAM_INT);
   try{
       $sql_update->execute();
       $temp['stat'] = $sql_update;
       if($sql_update->rowCount() > 0)
       {
           $data_cust = $sql_update->fetch();
            //Get Queue Status
           $sql_update = $mysql_conn->prepare('SELECT COUNT(*) as count FROM '.$dbname.'.live_queue WHERE serial <= :serial AND storeuid = :sid AND counteruid = :cid');
           $sql_update->bindParam(':sid',$data_cust['storeuid'],PDO::PARAM_INT);
           $sql_update->bindParam(':cid',$data_cust['counteruid'],PDO::PARAM_INT);
           $sql_update->bindParam(':serial',$data_cust['serial'],PDO::PARAM_INT);
           try{
                $sql_update->execute();
                $pos = $sql_update->fetch();
                $queue_pos = $pos['count'];

               //Getting Estimated Avg time
               $sql_statement = "SELECT AVG(wait) AS wait FROM " . $dbname . ".analytics WHERE storeuid = :sid AND counteruid = :cid";
               $find_query = $mysql_conn->prepare($sql_statement);
               $find_query->bindParam(':sid', $data_cust['storeuid'], PDO::PARAM_INT);
               $find_query->bindParam(':cid', $data_cust['counteruid'], PDO::PARAM_INT);
               $find_query->execute();
               $find_query->setFetchMode(PDO::FETCH_ASSOC);
               $avg_time = 0;
               try{
                   $temp_d = $find_query->fetch();
                   $avg_time = $temp_d['wait'];
               }catch (PDOException $e)
               {
                   //PDO Exception
                   $temp['text'] = "PDO Exception.Contact Admin!";
                   array_push($new_data['messages'],$temp);
                   echo json_encode($new_data);
                   die();
               }

               $temp['text']= "Your Queue Position is ".$queue_pos.".";
               array_push($new_data['messages'],$temp);
               $temp['text']= "Your Average Time is ".$avg_time.".";
               array_push($new_data['messages'],$temp);
               echo json_encode($new_data);
               die();

           }catch (PDOException $e)
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