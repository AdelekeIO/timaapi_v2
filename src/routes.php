<?php

/* require_once __DIR__ . '/vendor/autoload.php'; */

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', function ($request, $response, $args) {
    echo 'welcome to Tima Api Version 2.';
    echo '\n';
    print_r(date("Y-m-d"));
});

/* * *-----------------------------------Registration Module --------------------------------------------------------* */
$app->post('/users', function (Request $request, Response $response, array $args) {
    $db = new database();
    $table1 = 'users';
    $input = $request->getParsedBody();
    $resp = verifyRequiredParams(array('email', 'password', 'user_type', 'username', 'fullname', 'gender', 'date_of_birth', 'mobile_number', 'country_id', 'state_id', 'longitude', 'latitude', 'address'), $response, $this);
    if ($resp['error'] === TRUE) {
        $errormessage = $resp['message'];
        return ErrorMessage($response, $errormessage);
    }
    $email = $input['email'];
    $password = $input['password'];
    $user_type = $input['user_type'];
    $username = $input['username'];
    $fullname = $input['fullname'];
    $gender = $input['gender'];
    $date_of_birth = $input['date_of_birth'];
    $mobile_number = $input['mobile_number'];
    $country_id = $input['country_id'];
    $state_id = $input['state_id'];
    $longitude = $input['longitude'];
    $latitude = $input['latitude'];
    $address = $input['address'];
     $SignupToken = generatesToken('account_verifications', 'token',$db, 36);
    $Email_exist = didFieldExist($db, $table1, 'email', $email);
    $Username_exist = didFieldExist($db, $table1, 'username', $username);
    $Mobile_exist = didFieldExist($db, 'profiles', 'mobile_number', $mobile_number);
                 $generatedLink=URL.'confirmemail/'.$SignupToken;

         

    if ($Email_exist === TRUE) {
        return ErrorMessage($response, "Email Address already exist");
    } else if ($Username_exist === TRUE) {

        return ErrorMessage($response, "This Username '$username' already exist");
    } 
    else if ($Mobile_exist === TRUE) {

        return ErrorMessage($response, "This Mobile Number  '$mobile_number' already exist");
    }
      
 
    else {
        $sendmail=  Sendmail($email,$fullname, $generatedLink);
           if ($sendmail !== TRUE) {

        return ErrorMessage($response, "Unable to send Mail ".$sendmail);
    }
        $logintoken = generatesToken($table1, 'login_token', $db,40);
        $dateTime = date('d/m/Y g:i:s A');

        $data1 = array(
            'email' => $email,
            'username' => $username,
//            'password' => base64_decode(base64_decode($password)),
            'password' => sha1($password),
            'user_type' => $user_type,
            'login_token' => $logintoken,
            'last_seen' => $dateTime,
            'email_verification' => 1,
            'status' => 0,
        );
        $last_id = $db->insert($table1, $data1);
   
        if ($last_id !== FALSE) {
           // print_r($last_id);
     $user_id = $last_id[0]['id'];
$profile_pix= generatesGravater($email);
            $profile_data = array(
                'user_id' => $user_id,
                'influencer_type' => 0,
                'fullname' => ucwords($fullname),
                'gender' => $gender,
                'languages' => '',
                'age_group' => '',
                'industry' => '',
                'profile_pix' => $profile_pix,
                'mobile_number' => $mobile_number,
                'date_of_birth' => $date_of_birth,
                'longitude' => $longitude,
                'latitude' => $latitude,
                'address' => $address,
                'company_name' => '',
                'company_industry' => '',
                'company_location' => '',
                'company_website' => '',
                'country_id' => $country_id,
                'state_id' => $state_id,
                'account_balance' => 0,
            );
            $db->insert('profiles', $profile_data);

            $account_verifications = array(
                'user_id' => $user_id,
                'token' => $SignupToken,
                'platform' => 'email_Verification',
                'status' => 0,
            );
            $db->insert('account_verifications', $account_verifications);

            $account_infos = array(
                'user_id' => $user_id,
                'bank_name' => '',
                'account_name' => '',
                'account_number' => '',
                'status' => 0,
            );
            $resp = $db->insert('account_infos', $account_infos);
            if ($resp !== FALSE) {
                return SuccessMessage($response, 'User signup Successful \n Please verify Email.');
            }
        } else {
            return ErrorMessage($response, "An error occured");
        }
    }
});
/* * *---------------------------------- Registration Module  end---------------------------------------------------------* */
/* * *-----------------------------------post payment for student --------------------------------------------------------* */



require_once 'utils.php';
