<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function randomPassword()
{
    $alphabet    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass        = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 6; $i++) {
        $n      = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}

if (isset($_SERVER['PHP_AUTH_USER']) && $_SERVER['PHP_AUTH_USER'] == 'basic' && $_SERVER['PHP_AUTH_PW'] == '1234') {
    require_once 'config.php';
    $response = array();
    if (isset($_GET['apicall'])) {
        switch ($_GET['apicall']) {
            
            //======================appVersion Api=======================
            
            case 'forgotPassword':
                
                

                require 'PHPMailer/Exception.php';
                require 'PHPMailer/PHPMailer.php';
                require 'PHPMailer/SMTP.php';
                
                //Instantiation and passing `true` enables exceptions
                $mail = new PHPMailer(true);
                
                try {
                    
                    $password         = randomPassword();
                    $email            = $_POST['email'];
                    $confirm_password = $password;
                    
                    //Server settings
                    //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                    // $mail->isSMTP();                                            //Send using SMTP
                    $mail->Host       = 'smtp.gmail.com'; //Set the SMTP server to send through
                    $mail->SMTPAuth   = true; //Enable SMTP authentication
                    $mail->Username   = 'mitalsilverfox@gmail.com'; //SMTP username
                    $mail->Password   = 'mital@bronisquadnew'; //SMTP password
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                    $mail->Port       = 587; //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                    
                    //Recipients
                    $mail->setFrom('mabronisquad@gmail.com', '');
                    $mail->addAddress($email, ''); //Add a recipient
                    //$mail->addAddress('madhaviajudiya199581@gmail.com');               //Name is optional
                    $mail->addReplyTo($email, '');
                    $mail->addCC($email);
                    $mail->addBCC($email);
                    
                    //Attachments
                    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
                    //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name
                    
                    //Content
                    $mail->isHTML(true); //Set email format to HTML
                    $mail->Subject = 'katching';
                    $mail->Body    = 'We have received a request to forgot password associated with your katching account <br><b>Your New Password is : ' . $password . "</b></br>";
                    // $mail->AltBody = 'This is the body server new madhavi in plain text for non-HTML mail clients';
                    
                    $mail->send();
                    //echo 'Message has been sent';
                    //  $response['status']      = 1;
                    //$response['message']    = 'Message has been sent';
                    // $response['password']    = randomPassword();
                    
                    
                    
                    
                    $stmt = $conn->prepare("UPDATE users SET password = '$password' , confirm_password = '$confirm_password'  WHERE email = '$email' ");
                    $stmt->bind_param("sss", $password, $confirm_password, $email);
                    if ($stmt->execute()) {
                        
                        $response['status']  = 1;
                        $response['message'] = 'Message has been sent';
                        
                    } else {
                       
                        $response['status']  = 0;
                        $response['message'] = 'Message could not be sent';
                        
                    }
                    
                    
                    
                }
                catch (Exception $e) {
                    // "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    $response['status']  = 0;
                    $response['message'] = 'Message could not be sent';
                }
                
                break;
                
                
                
                
                
           // ============ add_update_ticket start  ================================
   case 'add_update_ticket':
$results = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '" . $_POST['user_id'] . "' ");
$num_rows = mysqli_num_rows($results);
if(!empty($num_rows == 1)){
     $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
     while ($rowd= mysqli_fetch_assoc($select_query_a))
                   {
                    $sumrecord = $rowd['add_update_ticket'];
                   }
     $total_coin_to = $_POST['add_update_ticket'] + $sumrecord;
    mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");
    $select_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
               $UserArrya = array();
               while ($row= mysqli_fetch_assoc($select_query))
                   {
                        $temp                           = array();
                        $temp['user_id']                = $row['user_id'];
                        $temp['email']                  = $row['email'];
                        $temp['active']                 = $row['active'];
                        $temp['type']                   = $row['type'];
                        $temp['social_id']              = $row['social_id'];
                        $temp['device_type']            = $row['device_type'];
                        $temp['device_token']           = $row['device_token'];
                        $temp['referral_code']          = $row['referral_code'];
                        $temp['apply_referral_code']    = $row['apply_referral_code'];
                        $temp['pendding_coin']          = $row['pendding_coin'];
                        $temp['total_coins']            = $row['total_coins'];
                        $temp['add_update_ticket']      = $row['add_update_ticket'];
                        array_push($UserArrya, $temp);
                    }
                    $response['status']  = 1;
                    $response['message'] = 'User List Avaliable and Update successfully!';
                    $response['result'] = $UserArrya;
            }
            else{
                    $response['status']  = 0;
                    $response['message'] = 'User invalid please try again';
            }
      break;
      // ============ add_update_ticket end ================================ 

                  //======================Last_jackpot_winner Api=======================

            case 'lastJackpotWinner':
              
              
              if (isset($_POST['jackpot_id'])) {
                    $status = 2;
                    
                    
                    $jackpot_id = $_POST['jackpot_id'];
                    
                    $stmt = $conn->prepare("SELECT `jackpot_id`, `product_id`, `jackpot_start_time`, `jackpot_end_time`,  `status`, `winner` FROM `jackpot` WHERE jackpot_id = ? AND  status = ? ");
                    $stmt->bind_param("ss", $jackpot_id,$status);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($jackpot_id,$product_id,$jackpot_start_time,$jackpot_end_time,$status,$winner);
                    $stmt->fetch();
                    
                  //  echo "==true==".$jackpot_id."::".$winner;
                  
                    $user_id = $winner;
                    
                    if ($stmt->num_rows > 0) {
                  
                     $stmt = $conn->prepare("SELECT user_id, email, total_coins FROM users WHERE user_id = ?");
                                                $stmt->bind_param("s", $user_id);
                                                $stmt->execute();
                                                $stmt->store_result();
                                                $stmt->bind_result($user_id, $email, $total_coins);
                                                $stmt->fetch();
                                                
                                                
                                                if ($stmt->num_rows != 0) {
                                                     $jackpot_winner = array(
                                                            'product_id' => $product_id,
                                                            'jackpot_id' => $jackpot_id,
                                                            'jackpot_start_time' => $jackpot_start_time,
                                                            'jackpot_end_time' => $jackpot_end_time,
                                                            'status' => $status,
                                            
                                                            'winner' => $winner,
                                                            'email' => $email
                                                        );
                                                        
                                                       
                                                        $response['status']         =  1 ;
                                                        $response['message']        = 'active jackpot get successfully';
                                                        $response['jackpotWinner'] = $jackpot_winner;
                                                }else{
                                                    $response['status']         =  0 ;
                                                    $response['message']        = 'No data Avaliable!!';

                                                    
                                                }
                    } else {
                        $response['status']         =  0 ;
                        $response['message']        = 'No data Avaliable!!';
                    }
                    
                    
                    
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                    
            break;
            
            
            // ======================= open_case_product_list api start =======================
            
           case 'open_case_product_win_percent_chance':

        $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE open_case_id = '" . $_POST['open_case_id'] . "' ");

        $results = mysqli_query($conn, "SELECT * FROM open_case_product  
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_id = '" . $_POST['open_case_id'] . "'");

        $results_all = mysqli_query($conn, "SELECT * FROM open_case_product  
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_id = '" . $_POST['open_case_id'] . "'");

            $one_hundreds = array();

            $i = 0;
    
    $UserArrya = array();  

    $temp  = array();
        while ($rowd = mysqli_fetch_array($results))
        {
            $rowdVB= mysqli_fetch_array($resultsVB);
            $temp['id']                         = $rowdVB['id'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['products_id']                = $rowd['products_id'];

            $arrays                             = $rowd['products_id'];
            $array_size                         = $rowd['win_percent_chance'];

            for ($i = 1; $i <= $array_size; $i++) {
                $one_hundreds[] = $arrays;
            
            }
            $temp['position_win']               = $i;
            $i++;
       } 

        $random_keys = rand(1,100000);
      
        while ($rowd_all = mysqli_fetch_array($results_all))
            {
                $temp['products_id']  = $rowd_all['products_id'];
                $temp['min']          = $rowd_all['win_percent_minimum'];
                $temp['max']          = $rowd_all['win_percent_maximum'];

                if ($temp['min'] < $random_keys && $random_keys < $temp['max'] ) {
                    $min_difference = $rowd_all['win_percent_minimum'];
                    $max_difference = $rowd_all['win_percent_maximum'];

                     $difference = $max_difference-$min_difference;
                    
                     if ($difference > 1000) {
                         $temp['win_percent_chance']   = $rowd_all['products_id'];
                      
                      break;    
                    // }else{
            
                     }
                    // break; 
                    
                }else{
               
                     $temp['win_percent_chance'] = 1;
                }
           
        }
 array_push($UserArrya, $temp);
        
            $response['status']  = 1;
            $response['message'] = 'Open Case Product List Avaliable!';
            $response['result'] = $UserArrya;
    
    break;               
 // ======== open_case_product_win_percent_chance api end ==========  
            
            
            // 14-06-2021 Start
            
            
            
             case 'open_case_product_win_percent_chance_odldd':

        $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE open_case_id = '" . $_POST['open_case_id'] . "' ");

        $results = mysqli_query($conn, "SELECT * FROM open_case_product  
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_id = '" . $_POST['open_case_id'] . "'");

// print_r($resultsVB);
// exit();
          
            $one_hundreds = array();

            // for ($i = 1; $i <= 100; $i++) {
            //     $one_to_hundred[] = $i;
            // }

            // $a = $one_to_hundred;

            $i = 0;
    
    $UserArrya = array();   
$temp  = array();
        while ($rowd = mysqli_fetch_array($results))
        {
            //
            //exit();
            $rowdVB= mysqli_fetch_array($resultsVB);
            // print_r($rowdVB['id']);
            
            $temp['id']                         = $rowdVB['id'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['products_id']                = $rowd['products_id'];

            $arrays                             = $rowd['products_id'];
            $array_size                         = $rowd['win_percent_chance'];
        // $array_with_range                   = range(1, 100);
         // print_r($arrays);
         // exit();
            for ($i = 1; $i <= $array_size; $i++) {
                $one_hundreds[] = $arrays;
              //  echo $i;
               // array_push($one_hundreds[$i], $arrays);
            }
            //print_r($one_hundreds);
 //exit();

           
 
            $temp['position_win']               = $i;

            $i++;
            

        } 
//exit();
         $random_keys = rand(0,sizeof($one_hundreds));
         
          //print_r("\n".$random_keys);
          //exit();
        // print_r("VB:::::::::".$one_hundreds[$random_keys]);
        
         $win_pro_id=$one_hundreds[$random_keys];
 $temp['win_percent_chance']                = $win_pro_id;
        //array_push($UserArrya, $random_keys);
        array_push($UserArrya, $temp);
            $response['status']  = 1;
            $response['message'] = 'Open Case Product List Avaliable!';
            $response['result'] = $UserArrya;
    

    break;
            // 14-06-2021 End
            
            
            
            
            
            
  case 'open_case_product_list':
      $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE open_case_id = '" . $_POST['open_case_id'] . "' ");

       $results = mysqli_query($conn, "SELECT * FROM open_case_product  
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_id = '" . $_POST['open_case_id'] . "'");

// print_r($results);
// exit();  
  $i = 0; 
       // $rowd= mysqli_fetch_assoc($results);
       // print_r($rowd);
    $UserArrya = array();   
        while ($rowd= mysqli_fetch_array($results))
        {
            //exit();
            $rowdVB= mysqli_fetch_array($resultsVB);
            // print_r($rowdVB['id']);
            $temp  = array();
            $temp['id']                         = $rowdVB['id'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['open_case_name']             = $rowd['open_case_name'];
            $temp['open_case_desc']             = $rowd['open_case_desc'];
            $temp['open_case_image']            = $rowd['open_case_image'];
            $temp['open_case_price']            = $rowd['open_case_price'];

            $temp['products_id']                = $rowd['products_id'];
            $temp['product_name']               = $rowd['product_name'];
            $temp['product_desc']               = $rowd['product_desc'];
            $temp['product_image']              = $rowd['product_image'];
            $temp['price']                      = $rowd['price'];
            $temp['status']                     = $rowd['status'];
            $temp['is_complete']                = $rowd['is_complete'];
            $temp['product_rent']                = $rowd['product_rent'];
            $temp['win_percent_chance']          = $rowd['win_percent_chance'];
            $temp['position_win']               = $i;
            $i++;
            array_push($UserArrya, $temp);
        } 
            $response['status']  = 1;
            $response['message'] = 'Open Case Product List Avaliable!';
            $response['result'] = $UserArrya;
    

    break;
// ======================= open_case_product_list api end =======================






  case 'open_case_product_update':
   mysqli_query($conn, "UPDATE open_case_product SET is_complete='".$_POST['is_complete']."' WHERE id = '".$_POST['id']."' ");

   // echo $update_recoard;
   // exit();

   $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE id = '" . $_POST['id'] . "' ");
   $results = mysqli_query($conn, "SELECT * FROM open_case_product
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_product.id = '" . $_POST['id'] . "'
                ");

    $UserArrya = array();   
        while ($rowd= mysqli_fetch_assoc($results))
        {
            if ($rowd['is_complete'] == 1) {

            mysqli_query($conn, "INSERT INTO orders(`user_id`,`product_id`) "
     . "VALUES ('" . $_POST['user_id'] . "','" . $rowd['products_id'] . "')");
            // print_r($_POST['open_case_pricess']);
            // exit();
            $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
            
                while ($rowdsss= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowdsss['add_update_ticket'];
                    // print_r($sumrecord);
                    // exit();
                   }

                  // $total_coin_to = $sumrecord - $_POST['open_case_pricess'];
                // print_r($total_coin_to);
                // exit();

              //mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");  


            } else{

            //     print_r($_POST['open_case_pricess']);
            // exit();

                 $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
            
                while ($rowdsss= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowdsss['add_update_ticket'];
                    // print_r($sumrecord);
                    // exit();
                   }

                //   $total_coin_to = $sumrecord - $_POST['open_case_pricess'];
                   $total_coin_to = $sumrecord;
                // print_r($total_coin_to);
                // exit();

             if ($total_coin_to < 0) {

                 $response['status']  = 0;
                 $response['message'] = 'You do not have enough tickets for this case!';
                 break;
             }

            mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");  



            }
            

            $rowdVB= mysqli_fetch_array($resultsVB);

            $temp                               = array();
            $temp['id']                         = $rowdVB['id'];

            $temp['is_complete']                = $rowd['is_complete'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['open_case_name']               = $rowd['open_case_name'];
            $temp['open_case_desc']               = $rowd['open_case_desc'];
            $temp['open_case_image']              = $rowd['open_case_image'];
            $temp['open_case_price']              = $rowd['open_case_price'];

            $temp['products_id']                = $rowd['products_id'];
            $temp['product_name']               = $rowd['product_name'];
            $temp['product_desc']               = $rowd['product_desc'];
            $temp['product_image']              = $rowd['product_image'];
            $temp['price']                      = $rowd['price'];
            $temp['status']                     = $rowd['status'];
            $temp['product_rent']              = $rowd['product_rent'];
            $temp['win_percent_chance']        = $rowd['win_percent_chance'];
            // $temp['add_update_ticket']         = $total_coin_to;
            array_push($UserArrya, $temp);
        } 
            $response['status']  = 1;
            $response['message'] = 'Open Case Product Update List Avaliable!';
            $response['result'] = $UserArrya;

    break;








 case 'open_case_product_update_old workigds':
   mysqli_query($conn, "UPDATE open_case_product SET is_complete='".$_POST['is_complete']."' WHERE id = '".$_POST['id']."' ");

   // echo $update_recoard;
   // exit();

   $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE id = '" . $_POST['id'] . "' ");
   $results = mysqli_query($conn, "SELECT * FROM open_case_product
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_product.id = '" . $_POST['id'] . "'
                ");

    $UserArrya = array();   
        while ($rowd= mysqli_fetch_assoc($results))
        {
            if ($rowd['is_complete'] == 1) {

            mysqli_query($conn, "INSERT INTO orders(`user_id`,`product_id`) "
     . "VALUES ('" . $_POST['user_id'] . "','" . $rowd['products_id'] . "')");
            // print_r($_POST['open_case_pricess']);
            // exit();
            $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
            
                while ($rowdsss= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowdsss['add_update_ticket'];
                    // print_r($sumrecord);
                    // exit();
                   }

                   $total_coin_to = $sumrecord - $_POST['open_case_pricess'];
                // print_r($total_coin_to);
                // exit();

              mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");  


            } else{

            //     print_r($_POST['open_case_pricess']);
            // exit();

                 $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
            
                while ($rowdsss= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowdsss['add_update_ticket'];
                    // print_r($sumrecord);
                    // exit();
                   }

                   $total_coin_to = $sumrecord - $_POST['open_case_pricess'];
                // print_r($total_coin_to);
                // exit();

             if ($total_coin_to < 0) {

                 $response['status']  = 0;
                 $response['message'] = 'You do not have enough tickets for this case!';
                 break;
             }

              mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");  



            }
            

            $rowdVB= mysqli_fetch_array($resultsVB);

            $temp                               = array();
            $temp['id']                         = $rowdVB['id'];

            $temp['is_complete']                = $rowd['is_complete'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['open_case_name']               = $rowd['open_case_name'];
            $temp['open_case_desc']               = $rowd['open_case_desc'];
            $temp['open_case_image']              = $rowd['open_case_image'];
            $temp['open_case_price']              = $rowd['open_case_price'];

            $temp['products_id']                = $rowd['products_id'];
            $temp['product_name']               = $rowd['product_name'];
            $temp['product_desc']               = $rowd['product_desc'];
            $temp['product_image']              = $rowd['product_image'];
            $temp['price']                      = $rowd['price'];
            $temp['status']                     = $rowd['status'];
            $temp['product_rent']              = $rowd['product_rent'];
            $temp['win_percent_chance']        = $rowd['win_percent_chance'];
            array_push($UserArrya, $temp);
        } 
            $response['status']  = 1;
            $response['message'] = 'Open Case Product Update List Avaliable!';
            $response['result'] = $UserArrya;

    break;
// ============== open_case_product_update api start =============================

    // ==========================
    
     case 'open_case_product_update_old_codw_working':

  mysqli_query($conn, "UPDATE open_case_product SET is_complete='".$_POST['is_complete']."' WHERE id = '".$_POST['id']."' ");

   // echo $update_recoard;
   // exit();

   $resultsVB = mysqli_query($conn, "SELECT * FROM open_case_product WHERE id = '" . $_POST['id'] . "' ");
   $results = mysqli_query($conn, "SELECT * FROM open_case_product
                INNER JOIN product ON open_case_product.products_id = product.product_id
                INNER JOIN open_case ON open_case_product.open_case_id = open_case.id
                WHERE open_case_product.id = '" . $_POST['id'] . "'
                ");


   
   // print_r($_POST['open_case_pricess']);
   // exit();

    $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    while ($rowd= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowd['add_update_ticket'];
                    //  print_r($sumrecord);
                    // exit();
                   }
    // print_r($sumrecord);
    // exit();                  
     $total_coin_to = $sumrecord - $_POST['open_case_pricess'];
   
     if ($total_coin_to < 0) {

         $response['status']  = 0;
         $response['message'] = 'You do not have enough tickets for this case!';
         break;
     }
    // print_r($total_coin_to);
    // exit();
  mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");

    $UserArrya = array();   
        while ($rowd= mysqli_fetch_assoc($results))
        {
            mysqli_query($conn, "INSERT INTO orders(`user_id`,`product_id`) "
     . "VALUES ('" . $_POST['user_id'] . "','" . $rowd['products_id'] . "')");

            $rowdVB= mysqli_fetch_array($resultsVB);

            $temp                               = array();
            $temp['id']                         = $rowdVB['id'];

            $temp['is_complete']                = $rowd['is_complete'];
            $temp['open_case_id']               = $rowd['open_case_id'];
            $temp['open_case_name']             = $rowd['open_case_name'];
            $temp['open_case_desc']             = $rowd['open_case_desc'];
            $temp['open_case_image']            = $rowd['open_case_image'];
            $temp['open_case_price']            = $rowd['open_case_price'];

            $temp['products_id']                = $rowd['products_id'];
            $temp['product_name']               = $rowd['product_name'];
            $temp['product_desc']               = $rowd['product_desc'];
            $temp['product_image']              = $rowd['product_image'];
            $temp['price']               = $rowd['price'];
            $temp['status']               = $rowd['status'];

           $temp['add_update_ticket'] = $total_coin_to;
           array_push($UserArrya, $temp);
        } 
            $response['status']  = 1;
            $response['message'] = 'Open Case Product Update List Avaliable!';
            $response['result'] = $UserArrya;

    break;
    
    
            
            //============ Start open_case List API=======================================================
            
            case 'open_case_list':
       $result_open_case = mysqli_query($conn, "SELECT * FROM open_case");
        //$result_open_case= mysqli_query($conn, "select * from open_case
     //     INNER JOIN product ON open_case.products_id = product.product_id
       //     ");   
        $open_case_result = array();   
        while($row_open_case = mysqli_fetch_assoc($result_open_case)){
           

            $data = array();


            $data['id'] = $row_open_case['id'];
            $data['open_case_name']     = $row_open_case['open_case_name'];
            $data['open_case_desc']     = $row_open_case['open_case_desc'];
            $data['open_case_image']    = $row_open_case['open_case_image'];
            $data['open_case_price']    = $row_open_case['open_case_price'];

            array_push($open_case_result, $data);
        }
        $response['status']  = 1;
        $response['message'] = 'Open Case  List Avaliable!';
        $response['open_case_result'] = $open_case_result;
    break;
    
    // end
         case 'open_case_list_working':
        // echo "string";
        // exit();
    $result = mysqli_query($conn, "SELECT MAX(win_percent_chance) FROM open_case");
    $get_open_casess = mysqli_fetch_array($result);
    //echo $get_open_case[0];
    // exit();
       //  $get_open_case = mysqli_query($conn, "select * from open_case  ");
        $get_open_case= mysqli_query($conn, "select * from open_case
           INNER JOIN product ON open_case.products_id = product.product_id
            ");




        $open_case_result= array();
            while ($row_open_case = mysqli_fetch_assoc($get_open_case))
               {


                // print_r($row_open_case['win_percent_chance']);
                // print_r($get_open_casess[0]);
                  // exit();
           if($row_open_case['win_percent_chance'] == $get_open_casess[0]){

         mysqli_query($conn, "INSERT INTO orders(`user_id`,`product_id`) "
     . "VALUES ('" . $_POST['user_id'] . "','" . $row_open_case['products_id'] . "')");

                $data                           =  array();
                $data['id']                     =  $row_open_case['id'];
                $data['products_id']            =  $row_open_case['products_id'];
                $data['win_percent_chance']     =  $row_open_case['win_percent_chance'];
                // product table Start
                $data['product_name']           =  $row_open_case['product_name'];
                $data['price']                  =  $row_open_case['price'];
                $data['product_desc']           =  $row_open_case['product_desc'];
                $data['product_image']          =  $row_open_case['product_image'];
                // product table End
                array_push($open_case_result, $data);
                // print_r($row_open_case);
                // exit();
           }
        }
        $response['status']  = 1;
        $response['message'] = 'Open Case  List Avaliable!';
        $response['open_case_result'] = $open_case_result;
    break;

 //============ End open_case List API=======================================================
 
            
            //============ Start open_case List API=======================================================
            
            
             case 'open_case_list_old_working_code':
                 
                    $result = mysqli_query($conn, "SELECT MAX(win_percent_chance) FROM open_case");
    $get_open_casess = mysqli_fetch_array($result);
    //echo $get_open_case[0];
    // exit();
    
      //  $get_open_case = mysqli_query($conn, "select * from open_case  ");

         $get_open_case= mysqli_query($conn, "select * from open_case 
           INNER JOIN product ON open_case.products_id = product.product_id

            ");        

        $open_case_result= array();
            while ($row_open_case = mysqli_fetch_assoc($get_open_case))
               {
                // print_r($row_open_case['win_percent_chance']);
                // print_r($get_open_casess[0]);
                  // exit();
           if($row_open_case['win_percent_chance'] == $get_open_casess[0]){

                $data                           =  array();
                $data['id']                     =  $row_open_case['id'];
                $data['products_id']            =  $row_open_case['products_id'];
                $data['win_percent_chance']     =  $row_open_case['win_percent_chance'];
                // product table Start
                $data['product_name']           =  $row_open_case['product_name'];
                $data['price']                  =  $row_open_case['price'];
                $data['product_desc']           =  $row_open_case['product_desc'];
                $data['product_image']          =  $row_open_case['product_image'];
                // product table End
                
                array_push($open_case_result, $data);
                // print_r($row_open_case);
                // exit();
           }


               }
                $response['status']  = 1;
                $response['message'] = 'Open Case  List Avaliable!';
                $response['open_case_result'] = $open_case_result;

            break;

             
       
  //============ End open_case List API=======================================================
  
   // ============ add_update_ticket start  ================================
    case 'add_update_ticket_old_codeworking':
    // echo "string";
    // exit();
$results = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '" . $_POST['user_id'] . "' ");
// print_r($results);
// exit();
$num_rows = mysqli_num_rows($results);

if(!empty($num_rows == 1)){

     $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
     while ($rowd= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowd['add_update_ticket'];
                    //  print_r($sumrecord);
                    // exit();
                   }
    //  print_r($sumrecord);
    //  $total_coins_add = $_POST['add_update_ticket'];
    //  print_r($total_coins_add);
    //  exit();
     $total_coin_to = $_POST['add_update_ticket'] + $sumrecord;

    // print_r($total_coin_to);
    // exit();
    mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
    $select_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    //  $row_image = mysqli_fetch_assoc($select_query);
    // print_r($row_image);
    // exit();
    
               $UserArrya = array();
               while ($row= mysqli_fetch_assoc($select_query))
                   {
                        $temp                           = array();
                        $temp['user_id']                = $row['user_id'];
                        $temp['email']                  = $row['email'];
                        $temp['active']                 = $row['active'];
                        $temp['type']                   = $row['type'];
                        $temp['social_id']              = $row['social_id'];
                        $temp['device_type']            = $row['device_type'];
                        $temp['device_token']           = $row['device_token'];
                        $temp['referral_code']          = $row['referral_code'];
                        $temp['apply_referral_code']    = $row['apply_referral_code'];
                        $temp['pendding_coin']          = $row['pendding_coin'];
                        $temp['total_coins']            = $row['total_coins'];
                        $temp['add_update_ticket']      = $row['add_update_ticket'];
                     
                        array_push($UserArrya, $temp);
                    } 
                    $response['status']  = 1;
                    $response['message'] = 'User List Avaliable and Update successfully!';
                    $response['result'] = $UserArrya;
            }
            else{
                    $response['status']  = 0;
                    $response['message'] = 'User invalid please try again';
            }

      break;

            // ============ add_update_ticket end ================================ 
  
  
  
  
  
  
  
  
  
  
            //===============================appVersion=====================
            case 'appVersion':
                
                $stmt = $conn->prepare("SELECT version_id, app_version, last_active_jackpot_id FROM version WHERE 1");
                $stmt->execute();
                $stmt->store_result();
                
                $stmt->bind_result($version_id, $app_version ,$last_active_jackpot_id);
                $stmt->fetch();
                
                if ($stmt->num_rows != 0) {
                   
                    
                    $appVersion = array(
                        'version_id' => $version_id,
                        'app_version' => $app_version,
                        'last_active_jackpot_id' =>$last_active_jackpot_id
                        
                    );
                    
                    $response['status']     = 1;
                    $response['message']    = 'get version successfully';
                    $response['appVersion'] = $appVersion;
                } else {
                   
                    $response['status']  = 0;
                    $response['message'] = 'version not avliable';
                    
                }
                
                
                break;
            //======================updateCoins Api=======================
            case 'updateCoins':
                
                
                if (isset($_POST['user_id']) && isset($_POST['total_coins'])) {
                    $user_id         = $_POST['user_id'];
                    $total_coins_add = $_POST['total_coins'];
                    
                    
                    $stmt = $conn->prepare("SELECT user_id,pendding_coin, total_coins, apply_referral_code,add_update_ticket FROM users WHERE user_id = ?");
                    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($user_id, $pendding_coin, $total_coins, $apply_referral_code,$add_update_ticket);
                    $stmt->fetch();
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    if ($total_coins >= 50) {
                        
                        
                        
                        if ($pendding_coin > 0) {
                            
                            
                            $total_coin_to = $total_coins + $total_coins_add + $pendding_coin;
                            $pendding_coin = 0;
                            
                            
                            $stmt = $conn->prepare("SELECT user_id,pendding_coin,total_coins FROM users WHERE referral_code = '$apply_referral_code' ");
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($user_ids, $pendding_coin_from_refrral_total, $total_coins_from_refferal);
                            $stmt->fetch();
                            
                           
                            
                            if ($stmt->num_rows > 0) {
                               
                                $pendding_coin_from_refferal = $pendding_coin_from_refrral_total - 15;
                                
                                $total_coins_reffral = $total_coins_from_refferal + 15;
                                
                                $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coins_reffral' , pendding_coin = '$pendding_coin_from_refferal'  WHERE user_id = '$user_ids' ");
                                $stmt->bind_param("ssi", $total_coins_from_refferal, $pendding_coin_from_refferal, $user_ids);
                                if ($stmt->execute()) {
                                    
                                } else {
                                   
                                    
                                }
                                
                            } else {
                                
                            }
                            
                            
                            
                        } else {
                            
                            $total_coin_to = $total_coins + $total_coins_add;
                        }
                        
                        
                        
                    } else {
                        
                        $total_coin_to = $total_coins + $total_coins_add;
                       
                        
                    }
                    
                    $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to' , pendding_coin = '$pendding_coin'  WHERE user_id = '$user_id' ");
                    $stmt->bind_param("ssi", $total_coins, $pendding_coin, $user_id);
                    if ($stmt->execute()) {
                        
                        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
                        $stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins,$add_update_ticket);
                        $stmt->fetch();
                        $user                = array(
                            'user_id' => $user_id,
                            'email' => $email,
                            'password' => $password,
                            'confirm_password' => $confirm_password,
                            'active' => $active,
                            'type' => $type,
                            'social_id' => $social_id,
                            'device_type' => $device_type,
                            'device_token' => $device_token,
                            'referral_code' => $referral_code,
                            'apply_referral_code' => $apply_referral_code,
                            'total_coins' => $total_coins,
                            'pendding_coin' => $pendding_coin,
                            'add_update_ticket' => $add_update_ticket
                        );
                        $response['status']  = 1;
                        $response['message'] = 'Coin updated successfully';
                        $response['user']    = $user;
                    }
                    else {
                       
                    }
                    
                    
                    
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                
                
                break;
            
            //======================jackpot Api=======================
            case 'jackpot':
                
                
                // if (isset($_POST['user_id'])) {
                //     $user_id = $_POST['user_id'];
                    
                //     $chance = 0;
             
                    
                    
                    
                    
                    
                //     $stmt = $conn->prepare("SELECT jackpot_id, product_id, jackpot_start_time, jackpot_end_time, status, winner FROM jackpot WHERE status = '1' ");
                //     $stmt->execute();
                //     $stmt->store_result();
                    
                //     $stmt->bind_result($jackpot_id, $product_id, $jackpot_start_time, $jackpot_end_time, $status, $winner);
                //     $stmt->fetch();
                    
                //     if ($stmt->num_rows > 0) {
                       
                       
                              
                    
                    
                //     $stmt = $conn->prepare("SELECT SUM(plot_point) FROM plot_coins WHERE jackpot_id='$jackpot_id'");
                //     $stmt->execute();
                //     $stmt->store_result();
                    
                //     $stmt->bind_result($plot_point_max);
                //     $stmt->fetch();
                    
                //     if ($stmt->num_rows != 0) {
                        
                        
                       
                        
                        
                //         $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = '$user_id' AND jackpot_id='$jackpot_id' ");
                //         $stmt->bind_param("s", $user_id);
                //         $stmt->execute();
                //         $stmt->store_result();
                        
                //         $stmt->bind_result($plot_coin_id, $plot_point_old);
                //         $stmt->fetch();
                        
                //         if ($stmt->num_rows != 0) {
                            
                            
                //             //echo "plot_point_old:".$plot_point_old;
                //             //echo "plot_point_max:".$plot_point_max;
                //           // echo "user_id:".$user_id;
                //           // echo "jackpot_id:".$jackpot_id;
                            
                //             $chance = round($plot_point_old * 100 / $plot_point_max);
                //           // echo "chance:".$chance;
                            
                            
                //         } else {
                           
                            
                //         }
                        
                        
                //     } else {
                       
                //     }
                        
                //         $stmt = $conn->prepare("SELECT product_image,product_name,product_desc,price  FROM product WHERE product_id = '$product_id'");
                        
                //         $stmt->execute();
                //         $stmt->store_result();
                        
                //         $stmt->bind_result($product_image, $product_name,$product_desc,$price);
                //         $stmt->fetch();
                        
                //         if ($stmt->num_rows != 0) {
                            
                            
                            
                //         } else {
                            
                            
                //         }
                        
                        
                //         $jackpot = array(
                //             'product_id' => $product_id,
                //             'product_image' => $product_image,
                //             'product_name' => $product_name,
                //             'product_desc' => $product_desc,
                //             'price'=>$price,
                //             'jackpot_id' => $jackpot_id,
                //             'jackpot_start_time' => $jackpot_start_time,
                //             'jackpot_end_time' => $jackpot_end_time,
                //             'status' => $status,
                //             'winner' => $winner,
                //             'chance' => $chance
                //         );
                        
                //         $response['status']  = 1;
                //         $response['message'] = 'active jackpot get successfully';
                //         $response['jackpot'] = $jackpot;
                //     } else {
                        
                //         $response['status']  = 0;
                //         $response['message'] = 'active jackpot are not avliable';
                        
                //     }
                    
                // } else {
                //     $response['status']  = 0;
                //     $response['message'] = 'required parameters are not available';
                // }
                  if (isset($_POST['user_id'])) {
                    $user_id = $_POST['user_id'];
                    $chance = 0;
                    $stmt = $conn->prepare("SELECT jackpot_id, product_id, jackpot_start_time, jackpot_end_time, status, winner,jackpot_desp,product_image FROM jackpot WHERE status = '1' ");
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($jackpot_id, $product_id, $jackpot_start_time, $jackpot_end_time, $status, $winner, $jackpot_desp,$product_image);
                    $stmt->fetch();
                    if ($stmt->num_rows > 0) {
                    $stmt = $conn->prepare("SELECT SUM(plot_point) FROM plot_coins WHERE jackpot_id='$jackpot_id'");
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($plot_point_max);
                    $stmt->fetch();
                    if ($stmt->num_rows != 0) {
                        $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = '$user_id' AND jackpot_id='$jackpot_id' ");
                        //$stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($plot_coin_id, $plot_point_old);
                        $stmt->fetch();
                        if ($stmt->num_rows != 0) {
                            //echo "plot_point_old:".$plot_point_old;
                            //echo "plot_point_max:".$plot_point_max;
                           // echo "user_id:".$user_id;
                           // echo "jackpot_id:".$jackpot_id;
                            $chance = round($plot_point_old * 100 / $plot_point_max);
                           // echo "chance:".$chance;
                        } else {
                        }
                    } else {
                    }
                        $stmt = $conn->prepare("SELECT * FROM product WHERE product_id = '$product_id'");
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($product_image, $product_name,$jackpot_desp,$product_desc,$price);
                        $stmt->fetch();
                        if ($stmt->num_rows != 0) {
                            
                        } else {
                        }
                        $jackpot = array(
                            'product_id' => $product_id,
                            'product_image' => $product_image,
                            'product_name' => $product_name,
                            'jackpot_desp' => $jackpot_desp,
                            'product_desc' => $product_desc,
                            'price'=>$price,
                            'jackpot_id' => $jackpot_id,
                            'jackpot_start_time' => $jackpot_start_time,
                            'jackpot_end_time' => $jackpot_end_time,
                            'status' => $status,
                            'winner' => $winner,
                            'chance' => $chance
                        );
                        $response['status']  = 1;
                        $response['message'] = 'active jackpot get successfully';
                        $response['jackpot'] = $jackpot;
                    } else {
                        $response['status']  = 0;
                        $response['message'] = 'active jackpot are not avliable';
                    }
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                
                break;
            
            //======================plotcoin Api=======================
            case 'plotcoinbkp':
                
                
                if (isset($_POST['user_id']) && isset($_POST['plot_point']) && isset($_POST['jackpot_id'])) {
                    
                    $user_id    = $_POST['user_id'];
                    $plot_point = $_POST['plot_point'];
                    $jackpot_id = $_POST['jackpot_id'];
                    
                   
                    
                    
                    
                    $stmt = $conn->prepare("SELECT user_id,total_coins FROM users WHERE user_id = ? ");
                    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($user_id, $total_coins);
                    
                    $stmt->fetch();
                    
                    
                    
                   
                    
                    if ($stmt->num_rows == 0) {
                        $response['status']  = 0;
                        $response['message'] = 'User is not exits!';
                    } else {
                        
                        if ($total_coins > $plot_point) {
                            
                            
                            $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = ? AND jackpot_id = ?");
                            $stmt->bind_param("ss", $user_id, $jackpot_id);
                            $stmt->execute();
                            $stmt->store_result();
                            
                            $stmt->bind_result($plot_coin_id, $plot_point_old);
                            $stmt->fetch();
                            
                            if ($stmt->num_rows != 0) {
                                
                                if ($total_coins > $plot_point) {
                                    
                                    $plot_point_to = $plot_point + $plot_point_old;
                                    
                                    
                                   
                                    $stmt = $conn->prepare("UPDATE plot_coins SET plot_point = '$plot_point_to'  WHERE plot_coin_id = '$plot_coin_id' ");
                                    $stmt->bind_param("i", $plot_coin_id);
                                    if ($stmt->execute()) {
                                       
                                        
                                        $total_coin_to = $total_coins - $plot_point;
                                        
                                        
                                        $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                                        $stmt->bind_param("si", $total_coins, $user_id);
                                        if ($stmt->execute()) {
                                           
                                            $stmt = $conn->prepare("SELECT plot_coin_id,user_id,plot_point FROM plot_coins ORDER BY plot_coin_id DESC LIMIT 0 , 1");
                                            $stmt->execute();
                                            $stmt->bind_result($plot_coin_id, $user_id, $plot_point_old_new);
                                            $stmt->fetch();
                                            $plotcoin             = array(
                                                'plot_coin_id' => $plot_coin_id,
                                                'user_id' => $user_id,
                                                'plot_point' => $plot_point_old_new
                                            );
                                           
                                            $response['status']   = 1;
                                            $response['message']  = 'plot coin successfully';
                                            $response['plotcoin'] = $plotcoin;
                                        }
                                        else {
                                            
                                        }
                                        
                                    } 
                                    else {
                                        
                                    }
                                } else {
                                    $response['status']  = 0;
                                    $response['message'] = 'User have not enough coins to plot coin in jackpot!';
                                   
                                }
                                
                            } else {
                                
                              
                                
                                
                                
                                
                                $stmt = $conn->prepare("INSERT INTO plot_coins(plot_point, user_id, jackpot_id) VALUES (?, ?, ?)");
                                $stmt->bind_param("sss", $plot_point, $user_id, $jackpot_id);
                                if ($stmt->execute()) {
                                    
                                    $total_coin_to = $total_coins - $plot_point;
                                    
                                   
                                    $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                                    $stmt->bind_param("si", $total_coins, $user_id);
                                    if ($stmt->execute()) {
                                       
                                        $stmt = $conn->prepare("SELECT plot_coin_id,user_id,plot_point FROM plot_coins ORDER BY plot_coin_id DESC LIMIT 0 , 1");
                                        $stmt->execute();
                                        $stmt->bind_result($plot_coin_id, $user_id, $plot_point_old_new);
                                        $stmt->fetch();
                                        $plotcoin             = array(
                                            'plot_coin_id' => $plot_coin_id,
                                            'user_id' => $user_id,
                                            'plot_point' => $plot_point_old_new
                                        );
                                        
                                        $response['status']   = 1;
                                        $response['message']  = 'plot coin successfully';
                                        $response['plotcoin'] = $plotcoin;
                                    } 
                                    
                                    else {
                                       
                                        $response['status']  = 0;
                                        $response['message'] = 'coin not ploted';
                                    }
                                    
                              
                                    
                                } else {
                                    
                                    
                                    
                                    $response['status']  = 0;
                                    $response['message'] = 'coin not ploted';
                                }
                                
                            }
                            
                        } else {
                            $response['status']  = 0;
                            $response['message'] = 'User have not enough coins to plot coin';
                        }
                    }
                    
                }
                break;
                
                
                
                //======================plotcoinbkp Api=======================
            case 'plotcoin':
                //echo "===plotcoinbkp";
                 $chance = 0;
                
                if (isset($_POST['user_id']) && isset($_POST['plot_point']) && isset($_POST['jackpot_id'])) {
                    
                    $user_id    = $_POST['user_id'];
                    $plot_point = $_POST['plot_point'];
                    $jackpot_id = $_POST['jackpot_id'];
                    
                   
                    
                    
                    
                    $stmt = $conn->prepare("SELECT user_id,total_coins FROM users WHERE user_id = ? ");
                    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($user_id, $total_coins);
                    
                    $stmt->fetch();
                    
                    
                    
                   
                    
                    if ($stmt->num_rows == 0) {
                        $response['status']  = 0;
                        $response['message'] = 'User is not exits!';
                    } else {
                        
                        if ($total_coins >= $plot_point) {
                            
                            
                            $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = ? AND jackpot_id = ?");
                            $stmt->bind_param("ss", $user_id, $jackpot_id);
                            $stmt->execute();
                            $stmt->store_result();
                            
                            $stmt->bind_result($plot_coin_id, $plot_point_old);
                            $stmt->fetch();
                            
                            if ($stmt->num_rows != 0) {
                                
                                if ($total_coins >= $plot_point) {
                                    
                                    $plot_point_to = $plot_point + $plot_point_old;
                                    
                                    
                                  
                                    $stmt = $conn->prepare("UPDATE plot_coins SET plot_point = '$plot_point_to'  WHERE plot_coin_id = '$plot_coin_id' ");
                                    $stmt->bind_param("i", $plot_coin_id);
                                    if ($stmt->execute()) {
                                       
                                        
                                        $total_coin_to = $total_coins - $plot_point;
                                        
                                        
                                        $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                                        $stmt->bind_param("si", $total_coins, $user_id);
                                        if ($stmt->execute()) {
                                         
                    
                    
                    
                    $stmt = $conn->prepare("SELECT SUM(plot_point) FROM plot_coins WHERE jackpot_id='$jackpot_id'");
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($plot_point_max);
                    $stmt->fetch();
                    
                    if ($stmt->num_rows != 0) {
                        
                        
                       
                        
                        
                        $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = '$user_id' AND jackpot_id='$jackpot_id' ");
                        $stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $stmt->store_result();
                        
                        $stmt->bind_result($plot_coin_id, $plot_point_old);
                        $stmt->fetch();
                        
                        if ($stmt->num_rows != 0) {
                            
                            
                            //echo "plot_point_old:".$plot_point_old;
                           // echo "plot_point_max:".$plot_point_max;
                           // echo "user_id:".$user_id;
                          // echo "jackpot_id:".$jackpot_id;
                            
                            $chance = round($plot_point_old * 100 / $plot_point_max);
                          //  echo "chance:".$chance;
                            
                            
                        } else {
                           
                            
                        }
                        
                        
                    } else {
                       
                    }             
                                            
                                            
                                            $stmt = $conn->prepare("SELECT plot_coin_id,user_id,plot_point FROM plot_coins WHERE user_id = '$user_id'");
                                            $stmt->execute();
                                            $stmt->bind_result($plot_coin_id, $user_id, $plot_point_old_new);
                                            $stmt->fetch();
                                            $plotcoin             = array(
                                                'plot_coin_id' => $plot_coin_id,
                                                'user_id' => $user_id,
                                                'plot_point' => $plot_point_old_new,
                                                'chance' => $chance
                                            );
                                           
                                            $response['status']   = 1;
                                            $response['message']  = 'plot coin successfully';
                                            $response['plotcoin'] = $plotcoin;
                                           
                                           
                                        } 
                                        else {
                                            
                                        }
                                        
                                    } 
                                    else {
                                       
                                    }
                                } else {
                                    $response['status']  = 0;
                                    $response['message'] = 'User have not enough coins to plot coin in jackpot!';
                                  
                                }
                                
                            } else {
                                
                                
                                
                                
                                
                                
                                $stmt = $conn->prepare("INSERT INTO plot_coins(plot_point, user_id, jackpot_id) VALUES (?, ?, ?)");
                                $stmt->bind_param("sss", $plot_point, $user_id, $jackpot_id);
                                if ($stmt->execute()) {
                                    
                                    $total_coin_to = $total_coins - $plot_point;
                                    
                                   
                                    $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                                    $stmt->bind_param("si", $total_coins, $user_id);
                                    if ($stmt->execute()) {
                     $stmt = $conn->prepare("SELECT SUM(plot_point) FROM plot_coins WHERE jackpot_id='$jackpot_id'");
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($plot_point_max);
                    $stmt->fetch();
                    
                    if ($stmt->num_rows != 0) {
                        
                        
                       
                        
                        
                        $stmt = $conn->prepare("SELECT plot_coin_id,plot_point  FROM plot_coins WHERE user_id = '$user_id' AND jackpot_id='$jackpot_id' ");
                        $stmt->bind_param("s", $user_id);
                        $stmt->execute();
                        $stmt->store_result();
                        
                        $stmt->bind_result($plot_coin_id, $plot_point_old);
                        $stmt->fetch();
                        
                        if ($stmt->num_rows != 0) {
                            
                            
                            //echo "plot_point_old:".$plot_point_old;
                           // echo "plot_point_max:".$plot_point_max;
                            //echo "user_id:".$user_id;
                           // echo "jackpot_id:".$jackpot_id;
                            
                            $chance = round($plot_point_old * 100 / $plot_point_max);
                           // echo "chance:".$chance;
                            
                            
                        } else {
                           
                            
                        }
                        
                        
                    } else {
                       
                    }  
                      
                                        
                                        
                                        $stmt = $conn->prepare("SELECT plot_coin_id,user_id,plot_point FROM plot_coins WHERE user_id = '$user_id'");
                                        $stmt->execute();
                                        $stmt->bind_result($plot_coin_id, $user_id, $plot_point_old_new);
                                        $stmt->fetch();
                                        $plotcoin             = array(
                                            'plot_coin_id' => $plot_coin_id,
                                            'user_id' => $user_id,
                                            'plot_point' => $plot_point_old_new,
                                            'chance' => $chance
                                            
                                        );
                                        
                                        $response['status']   = 1;
                                        $response['message']  = 'plot coin successfully';
                                        $response['plotcoin'] = $plotcoin;
                                    } 
                                    
                                    else {
                                        
                                        $response['status']  = 0;
                                        $response['message'] = 'coin not ploted';
                                    }
                                
                                } else {
                                    
                                    
                                    
                                    $response['status']  = 0;
                                    $response['message'] = 'coin not ploted';
                                }
                                
                            }
                            
                        } else {
                            $response['status']  = 0;
                            $response['message'] = 'User have not enough coins to plot coin';
                        }
                    }
                    
                }
                break;
            
            //======================socialLogin Api=======================
            
            case 'socialLogin':
                if (isset($_POST['email']) && isset($_POST['active']) && isset($_POST['type']) && isset($_POST['social_id']) && isset($_POST['device_type']) && isset($_POST['device_token']) && isset($_POST['referral_code']) && isset($_POST['apply_referral_code']) && isset($_POST['total_coins']) && isset($_POST['pendding_coin'])) {
                    $email               = $_POST['email'];
                    $active              = $_POST['active'];
                    $type                = $_POST['type'];
                    $password            = "";
                    $confirm_password    = "";
                    $social_id           = $_POST['social_id'];
                    $device_type         = $_POST['device_type'];
                    $device_token        = $_POST['device_token'];
                    $referral_code       = $_POST['referral_code'];
                    $apply_referral_code = $_POST['apply_referral_code'];
                    $total_coins         = $_POST['total_coins'];
                    $pendding_coin       = $_POST['pendding_coin'];
                    $stmt                = $conn->prepare("SELECT social_id FROM users WHERE social_id = ?");
                    $stmt->bind_param("s", $social_id);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows > 0) {
                        
                       
                        
                        $stmt = $conn->prepare("SELECT * FROM users WHERE social_id = ?");
                        $stmt->bind_param("ss", $social_id);
                        $stmt->execute();
                        $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins);
                        $stmt->fetch();
                        $stmt->store_result();
                        
                        
                       
                        $active = 1;
                        $stmt   = $conn->prepare("SELECT * FROM users WHERE social_id = ? AND active = ? ");
                        $stmt->bind_param("ss", $social_id, $active);
                        
                        $stmt->execute();
                        $stmt->store_result();
                        
                        $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                        $stmt->fetch();
                        if ($stmt->num_rows > 0) {
                           
                            $user                = array(
                                'user_id' => $user_id,
                                'email' => $email,
                                'password' => $password,
                                'confirm_password' => $confirm_password,
                                'active' => $active,
                                'type' => $type,
                                'social_id' => $social_id,
                                'device_type' => $device_type,
                                'device_token' => $device_token,
                                'referral_code' => $referral_code,
                                'apply_referral_code' => $apply_referral_code,
                                'total_coins' => $total_coins,
                                'pendding_coin' => $pendding_coin,
                                'add_update_ticket' => $add_update_ticket
                              
                            );
                            $response['status']  = 1;
                            $response['message'] = 'User already registered';
                            $response['user']    = $user;
                            
                        } else {
                            
                            $response['status']  = 2;
                            $response['message'] = 'Your account has been suspended!';
                            
                        }
                        
                        
                        
                        
                        
                        
                    } 
                    else {
                        $stmt = $conn->prepare("SELECT user_id, total_coins FROM users WHERE referral_code = '$apply_referral_code' ");
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($user_id, $total_coins_sel);
                        $stmt->fetch();
                        
                        if ($stmt->num_rows > 0) {
                            
                            $stmt = $conn->prepare("INSERT INTO users(email, password, confirm_password, active, type, social_id, device_type, device_token, 
 referral_code, apply_referral_code,pendding_coin, total_coins)
 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssssssssss", $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins);
                            if ($stmt->execute()) {
                                $stmt = $conn->prepare("SELECT user_id, total_coins FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->store_result();
                                $stmt->bind_result($user_ids, $total_coins);
                                $stmt->fetch();
                                
                                $total_coin_to = $total_coins + 15;
                                
                                $stmt          = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_ids' ");
                                $stmt->bind_param("si", $total_coins, $user_ids);
                                if ($stmt->execute()) {
                                  
                                } 
                                else {
                                    
                                }
                                
                              
                                
                                
                                $total_coin_from = $total_coins_sel + 15;
                                
                                
                                $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_from'  WHERE user_id = '$user_id' ");
                                $stmt->bind_param("si", $total_coin_from, $user_id);
                                if ($stmt->execute()) {
                                  
                                } 
                                else {
                                   
                                }
                                
                                
                                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                                $stmt->fetch();
                                $user                = array(
                                    'user_id' => $user_id,
                                    'email' => $email,
                                    'password' => $password,
                                    'confirm_password' => $confirm_password,
                                    'active' => $active,
                                    'type' => $type,
                                    'social_id' => $social_id,
                                    'device_type' => $device_type,
                                    'device_token' => $device_token,
                                    'referral_code' => $referral_code,
                                    'apply_referral_code' => $apply_referral_code,
                                    'total_coins' => $total_coins,
                                    'pendding_coin' => $pendding_coin,
                                    'add_update_ticket' => $add_update_ticket
                                   
                                   
                                );
                                $response['status']  = 1;
                                $response['message'] = 'User registered successfully';
                                $response['user']    = $user;
                            }
                        } else {
                            $response['status']  = 1;
                            $response['message'] = 'User Not registered successfully';
                            
                            
                            
                            
                            $stmt = $conn->prepare("INSERT INTO users(email, password, confirm_password, active, type, social_id, device_type, device_token, 
 referral_code, apply_referral_code,pendding_coin, total_coins)
 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssssssssss", $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins);
                            if ($stmt->execute()) {
                                
                                
                                
                                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                                $stmt->fetch();
                                $user                = array(
                                    'user_id' => $user_id,
                                    'email' => $email,
                                    'password' => $password,
                                    'confirm_password' => $confirm_password,
                                    'active' => $active,
                                    'type' => $type,
                                    'social_id' => $social_id,
                                    'device_type' => $device_type,
                                    'device_token' => $device_token,
                                    'referral_code' => $referral_code,
                                    'apply_referral_code' => $apply_referral_code,
                                    'total_coins' => $total_coins,
                                    'pendding_coin' => $pendding_coin,
                                    'add_update_ticket' => $add_update_ticket
                                 
                                );
                                $response['status']  = 1;
                                $response['message'] = 'User registered successfully';
                                $response['user']    = $user;
                            }
                            
                        }
                    }
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                break;
            //======================signup Api=======================
            
            
            case 'signup':
                
                
                
                if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']) && isset($_POST['active']) && isset($_POST['type']) && isset($_POST['social_id']) && isset($_POST['device_type']) && isset($_POST['device_token']) && isset($_POST['referral_code']) && isset($_POST['apply_referral_code']) && isset($_POST['total_coins']) && isset($_POST['pendding_coin'])) {
                    $email               = $_POST['email'];
                    $active              = $_POST['active'];
                    $type                = $_POST['type'];
                    $password            = $_POST['password'];
                    $confirm_password    = $_POST['confirm_password'];
                    $social_id           = $_POST['social_id'];
                    $device_type         = $_POST['device_type'];
                    $device_token        = $_POST['device_token'];
                    $referral_code       = $_POST['referral_code'];
                    $apply_referral_code = $_POST['apply_referral_code'];
                    $total_coins         = $_POST['total_coins'];
                    $pendding_coin       = $_POST['pendding_coin'];
                    
                   
                    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
                    $stmt->bind_param("s", $email);
                    $stmt->execute();
                    $stmt->store_result();
                    if ($stmt->num_rows > 0) {
                       
                        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                        $stmt->bind_param("s", $email);
                        $stmt->execute();
                        $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                        $stmt->fetch();
                        $user                = array(
                            'user_id' => $user_id,
                            'email' => $email,
                            'password' => $password,
                            'confirm_password' => $confirm_password,
                            'active' => $active,
                            'type' => $type,
                            'social_id' => $social_id,
                            'device_type' => $device_type,
                            'device_token' => $device_token,
                            'referral_code' => $referral_code,
                            'apply_referral_code' => $apply_referral_code,
                            'total_coins' => $total_coins,
                            'pendding_coin' => $pendding_coin,
                            'add_update_ticket' => $add_update_ticket
                           
                        );
                        $response['status']  = 1;
                        $response['message'] = 'User registered successfully';
                        $response['user']    = $user;
                        
                    } 
                    
                    elseif ($password != $confirm_password) {
                       
                        $response['status']  = 0;
                        $response['message'] = "passwords doesn't match";
                    } else {
                        $stmt = $conn->prepare("SELECT user_id, pendding_coin,total_coins FROM users WHERE referral_code = '$apply_referral_code' ");
                        $stmt->execute();
                        $stmt->store_result();
                        $stmt->bind_result($user_id, $pendding_coin_sel, $total_coins_sel);
                        $stmt->fetch();
                        
                       
                        
                        if ($stmt->num_rows > 0) {
                            
                            $stmt = $conn->prepare("INSERT INTO users(email, password, confirm_password, active, type, social_id, device_type, device_token, 
 referral_code, apply_referral_code, pendding_coin, total_coins)
 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssssssssss", $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins);
                            if ($stmt->execute()) {
                                $stmt = $conn->prepare("SELECT user_id,pendding_coin, total_coins FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->store_result();
                                $stmt->bind_result($user_ids, $pendding_coin, $total_coins);
                                $stmt->fetch();
                        
                                $pendding_coin_to = $pendding_coin + 15;
                               
                                $stmt             = $conn->prepare("UPDATE users SET pendding_coin = '$pendding_coin_to'  WHERE user_id = '$user_ids' ");
                                $stmt->bind_param("si", $pendding_coin, $user_ids);
                                if ($stmt->execute()) {
                                    
                                } 
                                else {
                                    
                                }
                                
                               
                                $pendding_coin_from = $pendding_coin_sel + 15;
                                $stmt               = $conn->prepare("UPDATE users SET pendding_coin = '$pendding_coin_from'  WHERE user_id = '$user_id' ");
                                $stmt->bind_param("si", $pendding_coin, $user_ids);
                                if ($stmt->execute()) {
                                } 
                                else {
                                   
                                }
                              
                                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                                $stmt->fetch();
                                $user                = array(
                                    'user_id' => $user_id,
                                    'email' => $email,
                                    'password' => $password,
                                    'confirm_password' => $confirm_password,
                                    'active' => $active,
                                    'type' => $type,
                                    'social_id' => $social_id,
                                    'device_type' => $device_type,
                                    'device_token' => $device_token,
                                    'referral_code' => $referral_code,
                                    'apply_referral_code' => $apply_referral_code,
                                    'total_coins' => $total_coins,
                                    'pendding_coin' => $pendding_coin,
                                    'add_update_ticket' => $add_update_ticket
                                    
                                );
                                $response['status']  = 1;
                                $response['message'] = 'User registered successfully';
                                $response['user']    = $user;
                            }
                        } else {
                            $response['status']  = 0;
                            $response['message'] = 'User Not registered successfully';
                            
                            
                            
                            $stmt = $conn->prepare("INSERT INTO users(email, password, confirm_password, active, type, social_id, device_type,
              device_token, referral_code, apply_referral_code,pendding_coin, total_coins)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssssssssssss", $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins);
                            if ($stmt->execute()) {
                                
                                
                                
                                $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
                                $stmt->bind_param("s", $email);
                                $stmt->execute();
                                $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                                $stmt->fetch();
                                $user                = array(
                                    'user_id' => $user_id,
                                    'email' => $email,
                                    'password' => $password,
                                    'confirm_password' => $confirm_password,
                                    'active' => $active,
                                    'type' => $type,
                                    'social_id' => $social_id,
                                    'device_type' => $device_type,
                                    'device_token' => $device_token,
                                    'referral_code' => $referral_code,
                                    'apply_referral_code' => $apply_referral_code,
                                    'total_coins' => $total_coins,
                                    'pendding_coin' => $pendding_coin,
                                     'add_update_ticket' => $add_update_ticket
                                     
                                );
                                $response['status']  = 1;
                                $response['message'] = 'User registered successfully';
                                $response['user']    = $user;
                            }
                            
                        }
                    }
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                
                
                
                
                
                break;
            
            //======================Login Api=======================
            case 'login':
                if (isset($_POST['email']) && isset($_POST['password'])) {
                    $email    = $_POST['email'];
                    $password = $_POST['password'];
                    
                    
                    
                    
                    $stmt = $conn->prepare("SELECT *  FROM users WHERE email = ? AND password = ? ");
                  
                    $stmt->bind_param("ss", $email, $password);
                    
                    
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                    $stmt->fetch();
                    
                    if ($stmt->num_rows > 0) {
                       
                        $active = 1;
                        $stmt   = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ? AND active = ? ");
                        $stmt->bind_param("sss", $email, $password, $active);
                       
                        $stmt->execute();
                        $stmt->store_result();
                        if ($stmt->num_rows > 0) {
                            $stmt->bind_result($user_id, $email, $password, $confirm_password, $active, $type, $social_id, $device_type, $device_token, $referral_code, $apply_referral_code, $pendding_coin, $total_coins, $add_update_ticket);
                            
                            
                            $stmt->fetch();
                            $user     = array(
                                'user_id' => $user_id,
                                'email' => $email,
                                'password' => $password,
                                'confirm_password' => $confirm_password,
                                'active' => $active,
                                'type' => $type,
                                'social_id' => $social_id,
                                'device_type' => $device_type,
                                'device_token' => $device_token,
                                'referral_code' => $referral_code,
                                'apply_referral_code' => $apply_referral_code,
                                'total_coins' => $total_coins,
                                'pendding_coin' => $pendding_coin,
                                'add_update_ticket' => $add_update_ticket
                                
                            );
                            $response['status']  = 1;
                            $response['message'] = 'Login successfully';
                            $response['user']    = $user;
                        } 
                        else {
                            $response['status']  = 2;
                            $response['message'] = 'Your account has been suspended!';
                            
                        }
                    } else {
                       
                        $response['status']  = 0;
                        $response['message'] = 'Invalid email or password';
                        
                        
                    }
                    
                    
                    
                    
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                break;
                
                
                 //======================open_case_ticket Api=======================
                  case 'open_case_ticket':
                      
                   $results = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '" . $_POST['user_id'] . "' ");
// print_r($results);
// exit();
$num_rows = mysqli_num_rows($results);

if(!empty($num_rows == 1)){

     $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
     while ($rowd= mysqli_fetch_assoc($select_query_a))
                   {

                    $sumrecord = $rowd['add_update_ticket'];
                    //  print_r($sumrecord);
                    // exit();
                   }
    //  print_r($sumrecord);
    //  $total_coins_add = $_POST['add_update_ticket'];
    //  print_r($total_coins_add);
    //  exit();
     $total_coin_to = $sumrecord - $_POST['add_update_ticket'];

    // print_r($total_coin_to);
    // exit();
    if ($total_coin_to < 0) {

         $response['status']  = 0;
         $response['message'] = 'You do not have enough tickets for this case!';
         break;
     }
     
    mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
    $select_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    //  $row_image = mysqli_fetch_assoc($select_query);
    // print_r($row_image);
    // exit();
    
            
               $UserArrya = array();
               while ($row= mysqli_fetch_assoc($select_query))
                   {
              
                        $temp                           = array();
                        $temp['user_id']                = $row['user_id'];
                        $temp['email']                  = $row['email'];
                        $temp['active']                 = $row['active'];
                        $temp['type']                   = $row['type'];
                        $temp['social_id']              = $row['social_id'];
                        $temp['device_type']            = $row['device_type'];
                        $temp['device_token']           = $row['device_token'];
                        $temp['referral_code']          = $row['referral_code'];
                        $temp['apply_referral_code']    = $row['apply_referral_code'];
                        $temp['pendding_coin']          = $row['pendding_coin'];
                        $temp['total_coins']            = $row['total_coins'];
                        $temp['add_update_ticket']      = $row['add_update_ticket'];
                     
                        array_push($UserArrya, $temp);
                    } 
                    $response['status']  = 1;
                    $response['message'] = 'User List Avaliable and Update successfully!';
                    $response['result'] = $UserArrya;
            }
            else{
                    $response['status']  = 0;
                    $response['message'] = 'User invalid please try again';
            }

      break;
                      
                  //======================open_case_ticket Api=======================
                  
                  
                  
                  
            
            //======================productList Api=======================
            
            
            case 'productList':
                
                $stmt = $conn->prepare("SELECT product_id,product_name,product_desc,product_image,price FROM `product` WHERE product.status !=1 order by  product_id desc");
                $stmt->execute();
                $stmt->bind_result($product_id, $product_name, $product_desc, $product_image, $price);
                $stmt->store_result();
                if ($stmt->num_rows < 0) {
                    $response['status']  = 0;
                    $response['message'] = 'No Product Avaliable!';
                    $stmt->close();
                } 
                else {
                    $product = array();
                    while ($row = $stmt->fetch()) {
                        $temp                  = array();
                        $temp['product_id']    = $product_id;
                        $temp['product_name']  = $product_name;
                        $temp['product_desc']  = $product_desc;
                        $temp['product_image'] = $product_image;
                        $temp['price']         = $price;
                        $temp['user_id']       = $user_id;
                        $temp['status']        = $status;
                        array_push($product, $temp);
                    } 
                    $response['status']  = 1;
                    $response['message'] = 'Product List Avaliable!';
                    $response['product'] = $product;
                }
                break;
            
            //======================order Api=======================
            
           
           
                case 'jackpotList':
                //SELECT `jackpot_id`, `product_id`, `jackpot_start_time`, `jackpot_end_time`, `product_image`, `product_name`, `status`, `winner` FROM `jackpot` WHERE 1    
                // $query_jackpot = "SELECT jackpot.jackpot_id,jackpot.product_id,jackpot.jackpot_start_time,jackpot.jackpot_end_time,
                // jackpot.status,jackpot.winner,
                // b.product_name,b.product_image,b.price FROM `jackpot` inner join product b on b.product_id=jackpot.product_id  WHERE jackpot.status !=1 order by  jackpot.jackpot_id desc";
                // $result_jackpot_active = mysqli_query($conn, $query_jackpot);
                
                // $rowcount=mysqli_num_rows($result_jackpot_active);
                // if($rowcount > 0){
                //  $jackpot= array();
                // while ($row= mysqli_fetch_assoc($result_jackpot_active))
                //   {
                //         $temp               =  array();
                //         $temp['jackpot_id']    =  $row['jackpot_id'];
                //         $temp['product_id']    =  $row['product_id'];
                //         $temp['product_name']  =  $row['product_name'];
                //         $temp['product_desc']  =  $row['product_desc'];
                //         $temp['product_image'] =  $row['product_image'];
                //          $temp['jackpot_start_time'] =  $row['jackpot_start_time'];
                //          $temp['jackpot_end_time'] =  $row['jackpot_end_time'];
                //         $temp['price']         =  $row['price'];
                //         $temp['winner']        =  $row['winner'];
                //         $temp['status']        =  $row['status']; 
                //         array_push($jackpot, $temp);
                //   }
                   
                //     $response['status']  = 1;
                //     $response['message'] = 'jackpot  List Avaliable!';
                //     $response['jackpot'] = $jackpot;
                // }else{
                    
                //     $response['status']  = 0;
                //     $response['message'] = 'No jackpot List Avaliable!';
                    
                // }

                
               
                // break;
               
                //SELECT `jackpot_id`, `product_id`, `jackpot_start_time`, `jackpot_end_time`, `product_image`, `product_name`, `status`, `winner` FROM `jackpot` WHERE 1
                $query_jackpot = "SELECT jackpot.jackpot_id,jackpot.product_id,jackpot.jackpot_start_time,jackpot.jackpot_end_time,
                jackpot.status,jackpot.winner,
                b.product_name,b.product_image,b.price FROM `jackpot` inner join product b on b.product_id=jackpot.product_id
                 WHERE jackpot.status !=1 order by  jackpot.jackpot_id desc";
                $result_jackpot_active = mysqli_query($conn, $query_jackpot);
                $rowcount=mysqli_num_rows($result_jackpot_active);
                if($rowcount > 0){
                 $jackpot= array();
                while ($row= mysqli_fetch_assoc($result_jackpot_active))
                   {
                        $temp               =  array();
                  //  $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = ?");
                   // $stmt->bind_param("s", $row['winner']);
                   // $stmt->execute();
                   // $stmt->store_result();
                    $sss = $row['winner'];
                    $stmt = $conn->prepare("SELECT email FROM users WHERE user_id = '".$sss."'");
                   // $stmt->bind_param("s",$row['winner'] );
                   $stmt->execute();
                  $stmt->store_result();
                   $stmt->bind_result($email);
                  //$stmt->bind_result($sss, $email);
                    $stmt->fetch();
                    // $stmt->execute();
                    //print_r($stmt);
                     //die();
                      $temp['jackpot_id']    =  $row['jackpot_id'];
                       $temp['product_id']    =  $row['product_id'];
                       $temp['product_name']  =  $row['product_name'];
                       // $temp['product_desc']  =  $row['product_desc'];
                       $temp['product_image'] =  $row['product_image'];
                        $temp['jackpot_start_time'] =  $row['jackpot_start_time'];
                        $temp['jackpot_end_time'] =  $row['jackpot_end_time'];
                       $temp['price']         =  $row['price'];
                       $temp['winner']        = $email;
                        $temp['status']        =  $row['status']; 
                        array_push($jackpot, $temp);
                   }
                    $response['status']  = 1;
                    $response['message'] = 'jackpot  List Avaliable!';
                    $response['jackpot'] = $jackpot;
                }else{
                    $response['status']  = 0;
                    $response['message'] = 'No jackpot List Avaliable!';
                }
                break;
            
            //======================order Api=======================
            
            
           
            
            case 'order':
                
               
                
                if (isset($_POST['user_id']) && isset($_POST['product_id']) && isset($_POST['price']) && isset($_POST['status'])) {
                    
                    $user_id    = $_POST['user_id'];
                    $product_id = $_POST['product_id'];
                    $status     = $_POST['status'];
                    $price      = $_POST['price'];
                    
                    $stmt = $conn->prepare("SELECT user_id,total_coins FROM users WHERE user_id = ? ");
                    $stmt->bind_param("s", $user_id);
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($user_id, $total_coins);
                    
                    $stmt->fetch();
                    
                    
                    
                  
                    
                    if ($stmt->num_rows == 0) {
                        $response['status']  = 0;
                        $response['message'] = 'User is not exits!';
                    } else {
                        
                        if ($total_coins > $price) {
                            
                            $total_coin_to = $total_coins - $price;
                            
                           
                            $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                            $stmt->bind_param("si", $total_coins, $user_id);
                            if ($stmt->execute()) {
                                
                                $stmt = $conn->prepare("INSERT INTO orders(user_id, product_id, status) VALUES (?, ?, ?)");
                                $stmt->bind_param("sss", $user_id, $product_id, $status);
                                if ($stmt->execute()) {
                                    $stmt = $conn->prepare("SELECT * FROM orders order by order_id desc limit 1");
                                    $stmt->execute();
                                    $stmt->bind_result($order_id, $user_id, $product_id, $status);
                                    $stmt->fetch();
                                    $order = array(
                                        'order_id' => $order_id,
                                        'user_id' => $user_id,
                                        'product_id' => $product_id,
                                        'status' => $status
                                    );
                                    $stmt->close();
                                    $response['status']  = 1;
                                    $response['message'] = 'Order Place Successfully';
                                    $response['order']   = $order;
                                } 
                                else {
                                    $response['status']  = 0;
                                    $response['message'] = 'Failed to Order Place!';
                                }
                                
                                
                            } else {
                                $response['status']  = 0;
                                $response['message'] = 'Failed to Order Place1!';
                            }
                            
                        } else {
                            $response['status']  = 0;
                            $response['message'] = 'User hDu har inte tillräckligt med mynt';
                        }
                        
                    }
                    
                } else {
                    $response['status']  = true;
                    $response['message'] = 'required parameters are not available';
                }
                
                break;
            
            //======================jackPotWinner Api=======================
            case 'jackPotWinner':
                
                if (isset($_POST['jackpot_id'])) {
                    $status = 2;
                    
                    
                    $jackpot_id = $_POST['jackpot_id'];
                    
                    $stmt = $conn->prepare("SELECT jackpot_id FROM jackpot WHERE status = '1' ");
                    $stmt->execute();
                    $stmt->store_result();
                    
                    $stmt->bind_result($jackpot_id);
                    $stmt->fetch();
                    
                    if ($stmt->num_rows > 0) {
                        
                        $stmt = $conn->prepare("SELECT user_id,plot_coin_id,plot_point FROM plot_coins WHERE jackpot_id = ?");
                        $stmt->bind_param("s", $jackpot_id);
                        $stmt->execute();
                        $stmt->bind_result($user_id, $plot_coin_id, $plot_point);
                        $stmt->store_result();
                        if ($stmt->num_rows < 0) {
                            $response['status']  = 0;
                            $response['message'] = 'All jackpot plot userlist not Avaliable!';
                            $stmt->close();
                        } 
                        else {
                            
                            $stmt_max_plot_coin = $conn->prepare("SELECT SUM(plot_point) FROM plot_coins WHERE jackpot_id = ?");
                            $stmt_max_plot_coin->bind_param("s", $jackpot_id);
                            $stmt_max_plot_coin->execute();
                            $stmt_max_plot_coin->store_result();
                            
                            $stmt_max_plot_coin->bind_result($plot_point_max);
                            $stmt_max_plot_coin->fetch();
                            
                            if ($stmt_max_plot_coin->num_rows > 0) {
                                
                                $stmt_chance = $conn->prepare("SELECT plot_coin_id,plot_point,user_id  FROM plot_coins WHERE plot_point = '$plot_point_max'");
                                $stmt_chance->execute();
                                $stmt_chance->store_result();
                                
                                $stmt_chance->bind_result($plot_coin_id, $plot_point_old, $user_id);
                                $stmt_chance->fetch();
                                
                                if ($stmt_chance->num_rows > 0) {
                                    
                                    
                                    
                                    
                                    $stmt = $conn->prepare("UPDATE jackpot SET winner = '$user_id' , status= '$status' WHERE jackpot_id = '$jackpot_id' ");
                                    $stmt->bind_param("sss", $user_id, $status, $jackpot_id);
                                    if ($stmt->execute()) {
                                        
                                        
                                        $stmt = $conn->prepare("SELECT jackpot_id, product_id, jackpot_start_time, jackpot_end_time, status, winner FROM jackpot WHERE jackpot_id = ? ");
                                        $stmt->bind_param("s", $jackpot_id);
                                        $stmt->execute();
                                        $stmt->store_result();
                                        
                                        $stmt->bind_result($jackpot_id, $product_id, $jackpot_start_time, $jackpot_end_time, $status, $winner);
                                        $stmt->fetch();
                                        
                                        if ($stmt->num_rows > 0) {
                                            
                                            
                                            
                                            $stmt = $conn->prepare("SELECT product_image,product_name,price  FROM product WHERE product_id = '$product_id'");
                                            $stmt->execute();
                                            $stmt->store_result();
                                            
                                            $stmt->bind_result($product_image, $product_name, $price);
                                            $stmt->fetch();
                                            
                                            if ($stmt->num_rows != 0) {
                                                
                                              
                                                
                                                $stmt = $conn->prepare("SELECT user_id, email, total_coins FROM users WHERE user_id = ?");
                                                $stmt->bind_param("s", $user_id);
                                                $stmt->execute();
                                                $stmt->store_result();
                                                $stmt->bind_result($user_id, $email, $total_coins);
                                                $stmt->fetch();
                                                
                                                
                                                if ($stmt->num_rows != 0) {
                                                    
                                                    $total_coin_to = $total_coins + $price;
                                                    
                                                    $stmt = $conn->prepare("UPDATE users SET total_coins = '$total_coin_to'  WHERE user_id = '$user_id' ");
                                                    $stmt->bind_param("si", $total_coins, $user_id);
                                                    if ($stmt->execute()) {
                                                        
                                                        
                                               
                                                        
                                                        $jackpot_winner = array(
                                                            'product_id' => $product_id,
                                                            'jackpot_id' => $jackpot_id,
                                                            'jackpot_start_time' => $jackpot_start_time,
                                                            'jackpot_end_time' => $jackpot_end_time,
                                                            'status' => $status,
                                                            'winner' => $winner,
                                                            'email' => $email
                                                        );
                                                        
                                                        $response['status']         =  1 ;
                                                        $response['message']        = 'active jackpot get successfully';
                                                        $response['jackpotWinner'] = $jackpot_winner;
                                                        
                                                    } else {
                                                       
                                                    }
                                                } else {
                                       
                                                }
                                                
                                          
                                                
                                            } else {
                                             
                                            }
                                            
                                        } else {
                                           
                                        }
                                        
                                        
                                    } else {
                                  
                                    }
                                    
                                    
                                } else {
                                    
                                    
                                     $stmt = $conn->prepare("UPDATE jackpot SET status= '$status' WHERE jackpot_id = '$jackpot_id' ");
                                     $stmt->bind_param("ss",  $status, $jackpot_id);
                                    if ($stmt->execute()) {
                                    
                               
                                    }else{
                                        
                                    }
                                    
                                     $response['status']  = 0;
                                     $response['message'] = 'jackpot Complete!';  
                                    
                                }
                                
                                
                                
                            } else {
                                $response['status']  = 0;
                                $response['message'] = 'jackpot plot coin is not Avaliable!'; 
                            }
                            
                            
                        }
                        
                        
                    } else {
                        $response['status']  = 0;
                        $response['message'] = 'No Active jackpot Avaliable!';
                    }
                    
                    
                    
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                
                break;
            
            
            
            
            
               
            //=========== buy_open_case End API =================
        case 'buy_open_case':
    // echo "string";
    // exit();
$results = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '" . $_POST['user_id'] . "' ");
// print_r($results);
// exit();
$num_rows = mysqli_num_rows($results);

if(!empty($num_rows == 1)){

     $select_query_a = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
     while ($rowd= mysqli_fetch_assoc($select_query_a))
       {

        $sumrecord = $rowd['add_update_ticket'];
        //  print_r($sumrecord);
        // exit();
       }
    //  print_r($sumrecord);
    //  $total_coins_add = $_POST['add_update_ticket'];
    //  print_r($total_coins_add);
    //  exit();
     $total_coin_to = $sumrecord - $_POST['open_case_price'];

    // print_r($total_coin_to);
    // exit();

     if ($total_coin_to < 0) {

         $response['status']  = 0;
         $response['message'] = 'You do not have enough tickets for this case!';
         break;
     }
     
    mysqli_query($conn, "UPDATE users SET add_update_ticket='".$total_coin_to."' WHERE user_id = '".$_POST['user_id']."' ");
    // print_r($_POST['add_update_ticket']);
    // exit();
    $select_query = mysqli_query($conn, "SELECT * FROM `users` WHERE user_id = '" . $_POST['user_id'] . "' ");
    //  $row_image = mysqli_fetch_assoc($select_query);
    // print_r($row_image);
    // exit();
    
               $UserArrya = array();
               while ($row= mysqli_fetch_assoc($select_query))
                   {
                        $temp                           = array();
                        $temp['user_id']                = $row['user_id'];
                        $temp['email']                  = $row['email'];
                        $temp['active']                 = $row['active'];
                        $temp['type']                   = $row['type'];
                        $temp['social_id']              = $row['social_id'];
                        $temp['device_type']            = $row['device_type'];
                        $temp['device_token']           = $row['device_token'];
                        $temp['referral_code']          = $row['referral_code'];
                        $temp['apply_referral_code']    = $row['apply_referral_code'];
                        $temp['pendding_coin']          = $row['pendding_coin'];
                        $temp['total_coins']            = $row['total_coins'];
                        $temp['add_update_ticket']      = $row['add_update_ticket'];
                     
                        array_push($UserArrya, $temp);
                    } 
                    $response['status']  = 1;
                    $response['message'] = 'Buy Open Case Avaliable and Update successfully!';
                    $response['result'] = $UserArrya;
            }
            else{
                    $response['status']  = 0;
                    $response['message'] = 'User invalid please try again';
            }

      break;

      
            //================ open_case_list Api start ======================
            
            //======================orderList Api=======================
            case 'orderList':
                
                if (isset($_POST['user_id'])) {
                    
                    $user_id = $_POST['user_id'];
                   
                    
                    $stmt = $conn->prepare("SELECT orders.order_id,orders.user_id,orders.product_id,orders.status,product.product_name,product.product_desc,
     product.product_image,product.price
     FROM orders
     INNER JOIN product ON orders.product_id = product.product_id WHERE  orders.user_id='$user_id' ORDER BY orders.order_id DESC");
                    $stmt->execute();
                    $stmt->bind_result($order_id, $user_id, $product_id, $status, $product_name, $product_desc, $product_image, $price);
                    $stmt->store_result();
                    if ($stmt->num_rows < 0) {
                        $response['status']  = 0;
                        $response['message'] = 'No Order Avaliable!';
                        $stmt->close();
                    }
                    else {
                        $order = array();
                        while ($row = $stmt->fetch()) {
                            $temp                  = array();
                            $temp['order_id']      = $order_id;
                            $temp['product_id']    = $product_id;
                            $temp['product_name']  = $product_name;
                            $temp['product_desc']  = $product_desc;
                            $temp['product_image'] = $product_image;
                            $temp['price']         = $price;
                            $temp['user_id']       = $user_id;
                            $temp['status']        = $status;
                            array_push($order, $temp);
                        } 
                        $response['status']  = 1;
                        $response['message'] = 'Order List Avaliable!';
                        $response['order']   = $order;
                    }
                } else {
                    $response['status']  = 0;
                    $response['message'] = 'required parameters are not available';
                }
                break;
            default:
                $response['status']  = 0;
                $response['message'] = 'Invalid Operation Called';
        } 
    } 
    else {
        $response['status']  = 0;
        $response['message'] = 'Invalid API Call';
    }
    echo json_encode($response);
} 
else {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Header Authenticate Faild!';
    exit;
}