<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function default_limit() {
    return 10;
}

 

function default_comment_limit() {
    return 10;
}

function feedtype_number() {
    return 1;
}

function followtype_number() {
    return 2;
}

function connectiontype_number() {
    return 3;
}

function feed_media_type_number() {
    return 4;
}

function verifyRequiredParams($required_fields, $response, $app) {
    //Assuming there is no error
    $error = false;

    //Error fields are blank
    $error_fields = "";

    //Getting the request parameters
    $request_params = $_REQUEST;

    //Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        //Getting the app instance
        $app = \Slim\Slim::getInstance();

        //Getting put parameters in request params variable
        parse_str($app->request()->getBody(), $request_params);
    }
//    print_r($required_fields);
    //Looping through all the parameters
    foreach ($required_fields as $field) {

        //if any requred parameter is missing
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            //error is true
            $error = true;

            //Concatnating the missing parameters in error fields
            $error_fields .= $field . ', ';
        }
    }

    //if there is a parameter missing then error is true
    if ($error) {
        //Creating response array
        $resp = array();

        //Getting app instance
//        $app = \Slim\Slim::getInstance();
        //Adding values to response array
        $resp["error"] = true;
        $resp["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        return $resp;
//       return ErrorMessage($response, $resp);
//        die();
    }
}

function SuccessMessage($response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'message' => $info,
    );
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageWithData($param, $response, $info, $resp_type) {

    if ($resp_type === 'XML') {
//        $xmldata=json2Xml($param);
//        print_r($xmldata);
        
        $xmldata=array2xml($param, false);
        return $xmldata;
    } else {
        $resp = array();
        $res = array('status' => TRUE,
            'data' => $param,
            'message' => $info,
        );
//    $returndata= json_encode($res);
        $returndata = $response->withJson($res, 201);
        return $returndata;
//    print_r(gettype($returndata));
//  return $response->withJson($res, 201);  
    }
}

function SuccessMessageWithDoubleData($param, $totalcount, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        'total_count' => $totalcount,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageWithCustomDoubleData($param, $data, $fieldsname, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        $fieldsname => $data,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function SuccessMessageForConnection($param, $mentee_details, $mentor_details, $response, $info) {
    $resp = array();
    $res = array('status' => TRUE,
        'data' => $param,
        'mentee_details' => $mentee_details,
        'mentor_details' => $mentor_details,
        'message' => $info,
    );
//    $returndata= json_encode($res);
    $returndata = $response->withJson($res);
    return $returndata;
}

function ErrorMessage($response, $info) {
    $resp = array();
    $res = array('status' => FALSE,
        'message' => $info,
//        'status_code' => $response->withStatus(302),
    );
//    print_r($res);

    $returndata = $response->withJson($res);
    return $returndata;
//    echo $returndata;
//    echo json_encode($res);
}

function ErrorMessagewithData($response, $data, $info) {
    $resp = array();
    $res = array('status' => FALSE,
        'message' => $info,
        'data' => $data,
    );

    $returndata = $response->withJson($res);
    return $returndata;
}

function response_with_id($param, $state, $userid) {
    $resp = array();
    $res = array('status' => $state,
        'message' => $param,
        'details' => $userid,
    );
    $returndata = $response->withJson($res);
    return $returndata;
}

function generatesToken($table, $field, $db, $size = 10) {
    $token = "";

    for ($i = 0; $i < $size; $i++) {
        $key = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
        $token .= $key [array_rand($key)];
    }
    if (istokenExist($table, $field, $token, $db) > 0) {
        generatesToken($table, $field, $db, $size);
    } else {

        return $token;
    }
}
function generatesGravater($userMail) {
 
$imageWidth = '600'; //The image size

$imgUrl = 'https://secure.gravatar.com/avatar/'.md5($userMail).'?size='.$imageWidth;
 return $imgUrl;
}

   function Sendmail($user_email,$name,$link) {
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("noreply@tima.com.ng", "Account Confirmaton");
        $email->setSubject("Tima Account Confirmation");
        $email->addTo($user_email, $name);
        $email->addBcc("hashdavies@gmail.com", $name);
        $email->setReplyTo("info@tima.com.ng", "Don not Reply");
//        $email->setSendAt(1461775051);
//        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
                "text/html", MailContent($name,$link)
        );
//        $email->setFooter(true, "Footer", "<strong>Footer</strong>");
//        $sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
//            print $response->statusCode() . "\n";
//            print_r($response->headers());
//            print $response->body() . "\n";
            $statuscode=$response->statusCode();
            if ($statuscode===202) {
                return TRUE;
            }
            else{
                 return FALSE;
            }
        } catch (Exception $e) {
//            echo 'Caught exception: ' . $e->getMessage() . "\n";
               return FALSE;
        }
    }

     function MailContent($name,$link) {
        
            $mailcontent='<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Congratulations! | MAP</title>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet">
        <style>
            /* -------------------------------------
                INLINED WITH htmlemail.io/inline
            ------------------------------------- */
            /* -------------------------------------
                RESPONSIVE AND MOBILE FRIENDLY STYLES
            ------------------------------------- */
            body {

                color: black !important;

            }
            @media only screen and (max-width: 620px) {
                table[class=body] h1 {
                    font-size: 28px !important;
                    margin-bottom: 10px !important;
                }
                table[class=body] p,
                table[class=body] ul,
                table[class=body] ol,
                table[class=body] td,
                table[class=body] span,
                table[class=body] a {
                    font-size: 16px !important;
                }
                table[class=body] .wrapper,
                table[class=body] .article {
                    padding: 10px !important;
                }
                table[class=body] .content {
                    padding: 0 !important;
                }
                table[class=body] .container {
                    padding: 0 !important;
                    width: 100% !important;
                }
                table[class=body] .main {
                    border-left-width: 0 !important;
                    border-radius: 0 !important;
                    border-right-width: 0 !important;
                }
                table[class=body] .btn table {
                    width: 100% !important;
                }
                table[class=body] .btn a {
                    width: 100% !important;
                }
                table[class=body] .img-responsive {
                    height: auto !important;
                    max-width: 100% !important;
                    width: auto !important;
                }
            }

            /* -------------------------------------
                PRESERVE THESE STYLES IN THE HEAD
            ------------------------------------- */
            @media all {
                .ExternalClass {
                    width: 100%;
                }
                .ExternalClass,
                .ExternalClass p,
                .ExternalClass span,
                .ExternalClass font,
                .ExternalClass td,
                .ExternalClass div {
                    line-height: 100%;
                }
                .apple-link a {
                    color: inherit !important;
                    font-family: inherit !important;
                    font-size: inherit !important;
                    font-weight: inherit !important;
                    line-height: inherit !important;
                    text-decoration: none !important;
                }
                .btn-primary table td:hover {
                    background-color: #34495e !important;
                }
                .btn-primary a:hover {
                    background-color: #34495e !important;
                    border-color: #34495e !important;
                }
            }
            a.link2{
                text-decoration:none;  
                font-family:Helvetica, Arial, sans-serif;
                font-size:16px;
                color:#fff;
                border-radius:4px;
            }
        </style>
    </head>
    <!--<body class="" style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">-->
    <body class="" style="background-color: #f6f6f6; font-family: \'Roboto\', sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
        <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
            <tr>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
                <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">

                    <div class="content" style="box-sizing: border-box; display: block; margin: 0 auto; max-width: 580px; padding: 10px;">

                        <!-- START CENTERED WHITE CONTAINER -->
                        <span class="preheader" style="color: transparent; display: none; height: 0; max-height: 0; max-width: 0; opacity: 0; overflow: hidden; mso-hide: all; visibility: hidden; width: 0;">Tima </span>
                        <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px;">
                            <tr style="border-bottom: 1px solid #ccc;">
                                <td class="innerpadding borderbottom">
                                    <img src="https://res.cloudinary.com/custocrypt/image/upload/v1557283509/logo.025e8dea.jpg" width="100%" border="0" alt="Wakamax Logo" />
                                </td>
                            </tr>
                            <!-- START MAIN CONTENT AREA -->
                            <tr>
                                <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                    <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                        <tr>
                                            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;margin-bottom: 15px;
font-weight: bold;
font-size: 20px;">Hey <b>'.$name.'</b>!</p>
                                                <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; margin-bottom: 15px;">
                                                    We are ready to activate your account. All we need to do is make sure this is your email address. 
                                                </p> 
                                            </td>

                                        </tr>


                                        <table cellpadding="0" cellspacing="0" border="0" align="center" width="600">
                                            <tr>
                                                <td width="100">&nbsp;</td>
                                                <td width="400" align="center" style="padding-top:25px;padding-bottom:115px;">
                                                    <table cellpadding="0" cellspacing="0" border="0" align="center" width="200" height="50">
                                                        <tr>
                                                            <td bgcolor="#ED006F" align="center" style="border-radius:4px;" width="200" height="50">
                                                                <div class="contentEditableContainer contentTextEditable">
                                                                    <div class="contentEditable" >
                                                                        <a target="_blank" href="'.$link.'" class="link2">Verify Email Address</a>
                                                                    </div>
                                                                </div>

                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td width="100">&nbsp;</td>
                                            </tr>



                                
                                        </table>
                                </td>
                            </tr>

                            <!-- END MAIN CONTENT AREA -->
                        </table>

                        <!-- START FOOTER -->
                        <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;margin-left: 29px;">
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                          
                                <tr  >

                                    <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 2px; font-size: 12px; color: #999999; text-align: justify;">
                                        <span class="apple-link" style="color: #999999; font-size: 12px; text-align: justify;">Phone:+234-90502267</span>
                                    </td>

                                </tr>
                                <tr  >

                                    <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 2px; font-size: 12px; color: #999999; text-align: justify;">
                                        <span class="apple-link" style="color: #999999; font-size: 12px; text-align: justify;">Email: connect@wakamax.com</span>
                                    </td>
                                </tr>
                                <tr  >

                                    <td class="content-block" style="font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 2px; font-size: 12px; color: #999999; text-align: justify;">
                                        <span class="apple-link" style="color: #999999; font-size: 12px; text-align: justify;">If you didnt create a Wakamax account, just delete this email and everything will go back to the way it was. </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <!-- START FOOTER -->
                        <div class="footer" style="clear: both; margin-top: 10px; text-align: center; width: 100%;  background-color: black;">
                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                <tr  >
                                    <td class="content-block" style="background-color:\'#000\';padding: 12px;font-family: sans-serif; vertical-align: top; padding-bottom: 10px; padding-top: 2px; font-size: 12px; color: #999999; ">
                                        <span class="apple-link" style="  color: #f39a08;font-size: 14px;  text-align: center;  font-family: sans-serif;">...have Fun</span>
                                    </td>

                                </tr>

                            </table>
                        </div>
                        <!-- END FOOTER -->

                        <!-- END CENTERED WHITE CONTAINER -->
                    </div>
                </td>
                <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
            </tr>
        </table>
    </body>
</html>
';
       
            return $mailcontent;
    }

  
function istokenExist($table, $field, $token, $db) {
    $sth = $db->select("Select * from $table WHERE $field = :field", array(":field" => $token));
    return count($sth);
}














 
function recordUsage($db, $route, $token) {
    $dateTime = date('d/m/Y g:i:s A');
    $apibox_usage_data = array(
        'apibox_detail_id' => getCompanyId($db, $token),
        'route' => $route,
        'datetime' => $dateTime,
    );
    $db->insert('apibox_usage', $apibox_usage_data);
}

function getCompanyId($db, $token) {

    $sth = $db->select("SELECT `apibox`.`idx` as companyid FROM `apibox`,`apibox_detail` WHERE `apibox_detail`.`apibox_idx`=`apibox`.`idx` AND `apibox_detail`.`status`=1 AND `apibox`.`status`=1 AND `apibox_detail`.`thekey`='" . $token . "' ");

    if (isset($sth[0]["companyid"]) && !empty($sth)) {
        return $sth[0]["companyid"];
    } else {
        return FALSE;
    }
}
function json2Xml($jsonFile) {
   // print_r($jsonFile);
//read the JSON file
//$jsonFile = file_get_contents('members.json');

//decode the data
$jsonFile_decoded = json_decode($jsonFile);

//create a new xml object
$xml = new SimpleXMLElement('<studentDatas/>');

//loop through the data, and add each record to the xml object
foreach($jsonFile_decoded AS $students){
	foreach($students as $studentdata){
		$studentdata = $xml->addChild('student');
		$studentdata->addChild('lastName', 'jinad');
		$studentdata->addChild('firstName', 'Olawakld');
		$studentdata->addChild('age', '90');
		$studentdata->addChild('sex', 'male');
		$studentdata->addChild('location', 'ibadan');
	}
}

//set header content type
//Header('Content-type: text/xml');

//output the xml file
// print($xml->asXML());
return $xml->asXML();
//print($xml->asXML());
}

function array2xml($array, $xml = false){

    if($xml === false){
        $xml = new SimpleXMLElement('<result/>');
    }

    foreach($array as $key => $value){
     //   print_r($value);
        if(is_array($value)){
            array2xml($value, $xml->addChild($key));
        } else {
            $xml->addChild($key, $value);
        }
    }

    return $xml->asXML();
}






















function didFieldExist($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function didFieldExist2($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field AND status=0", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function haveIrequestTokenBefore($db, $table, $field, $value) {

    $sth = $db->select("Select * from $table WHERE $field = :field AND status=0 ", array(":field" => $value));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function userToken2id($db, $login_token) {

    $sth = $db->select("Select * from login WHERE login_token = :login_token", array(":login_token" => $login_token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function postToken2id($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function postdetails($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function postid2postToken($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["feed_id"]) && !empty($sth)) {
        return $sth[0]["feed_id"];
    } else {
        return FALSE;
    }
}

function postToken2posterid($db, $table, $field, $token) {

    $sth = $db->select("Select * from $table WHERE $field = :token", array(":token" => $token));

    if (isset($sth[0]["poster"]) && !empty($sth)) {
        return $sth[0]["poster"];
    } else {
        return FALSE;
    }
}

function attachmentId2Postid($db, $attchment_id) {

    $sth = $db->select("Select * from feed_attachment WHERE id = :attachment_id", array(":attachment_id" => $attchment_id));

    if (isset($sth[0]["feed_id"]) && !empty($sth)) {
        return $sth[0]["feed_id"];
    } else {
        return FALSE;
    }
}

function didPasswordMatch($db, $userid, $password) {

    $sth = $db->select("Select * from login WHERE password = :password and id =:id", array(":password" => $password, ":id" => $userid));
//    $sth = $db->select("Select * from login WHERE id = ".$userid." ");
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIlikeBefore($db, $table, $feed_id, $liked_by, $value1, $myid) {

    $sth = $db->select("Select * from $table WHERE $feed_id = :field1 AND $liked_by = :myid AND status =0", array(":field1" => $value1, ":myid" => $myid));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIfollowBefore($db, $myid, $user2) {

    $sth = $db->select("Select * from following WHERE user1_id = :myid AND user2_id = :user2 AND status =0", array(":myid" => $myid, ":user2" => $user2));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIaddintrestBefore($db, $myid, $intrest_id) {

    $sth = $db->select("Select * from myareaofintrest WHERE userid = :userid AND intrest_id = :intrest_id AND status =0", array(":userid" => $myid, ":intrest_id" => $intrest_id));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIUnaddintrestBefore($db, $myid, $intrest_id) {

    $sth = $db->select("Select * from myareaofintrest WHERE userid = :userid AND intrest_id = :intrest_id AND status =1", array(":userid" => $myid, ":intrest_id" => $intrest_id));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function haveIunfollowBefore($db, $myid, $user2) {

    $sth = $db->select("Select * from following WHERE user1_id = :myid AND user2_id = :user2 AND status =1", array(":myid" => $myid, ":user2" => $user2));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeInConnectionBefore($db, $myid, $user2, $concode) {

    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND con_code = :concode AND status =3", array(":myid" => $myid, ":user2" => $user2, ":concode" => $concode));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function con_code2ids($db, $concode) {

    $sth = $db->select("Select * from connect WHERE  con_code = :concode AND status =0", array(":concode" => $concode));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function amCommentOwner($db, $feed_id, $comment_by, $comment_id) {

    $sth = $db->select("Select id from comment WHERE  feed_id = :feed_id AND comment_by = :comment_by AND id = :comment_id AND status =0", array(":feed_id" => $feed_id, ":comment_by" => $comment_by, ":comment_id" => $comment_id));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]['id'];
    } else {
        return FALSE;
    }
}

function amPostOwner($db, $feed_id, $post_by) {

    $sth = $db->select("Select id from feeds WHERE  feed_id = :feed_id AND poster = :poster  AND status =0", array(":feed_id" => $feed_id, ":poster" => $post_by));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0]['id'];
    } else {
        return FALSE;
    }
}

function isConnectionGoing($db, $myid, $user2, $concode) {

    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND con_code = :concode AND status =0", array(":myid" => $myid, ":user2" => $user2, ":concode" => $concode));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeInConnectionBefore1($db, $myid, $user2) {

//    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND (status =3 OR status =0)", array(":myid" => $myid,":user2" => $user2));
//    $sth = $db->select("Select * from connect WHERE mentor_id = :myid AND mentee_id = :user2 AND  status =3", array(":myid" => $myid,":user2" => $user2));
    $sth = $db->select("Select * from connect WHERE mentor_id = :user2 AND mentee_id = :myid AND status =3", array(":myid" => $myid, ":user2" => $user2));

//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function AREWeConnected($db, $myid, $con_code) {

    $sth = $db->select("Select * from connect WHERE (mentor_id = :myid OR  mentee_id = :myid) AND  con_code = :concode AND status =0", array(":myid" => $myid, ":concode" => $con_code));

//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function getareaofintrest($db, $myid) {
    $sql = "SELECT `intrest_categories`.`name` AS intrest FROM `myareaofintrest`,`intrest_categories` WHERE `intrest_categories`.`id`=`myareaofintrest`.`intrest_id` AND `myareaofintrest`.`status`='0' AND `intrest_categories`.`status`='0' AND `myareaofintrest`.`userid`='" . $myid . "' ";
    $result = $db->select($sql);

    if (isset($result) && !empty($result)) {

        return $result;
    } else {
        return $result;
    }
}

function haveIUnlikeBefore($db, $table, $feed_id, $liked_by, $value1, $myid) {
    $sth = $db->select("Select * from $table WHERE $feed_id = :field1 AND $liked_by = :myid AND status =1", array(":field1" => $value1, ":myid" => $myid));
//     print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function amIWhoAmI($db, $response, $login_token) {

    $sth = $db->select("Select * from login WHERE login_token = :login_token", array(":login_token" => $login_token));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
//        die();
        return ErrorMessage($response, "Your id is not correct please logout and login again");
    }
}

function id2name($db, $uid) {

    $sth = $db->select("Select fullname from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["fullname"]) && !empty($sth)) {
        return $sth[0]["fullname"];
    } else {
        return FALSE;
    }
}

function NotificationCount($db, $myid) {


    $sql = "SELECT message,token,type FROM `notifications` where  status =0 and userid ='" . $myid . "'";
    $result = $db->select($sql);

//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return count($sth);
    } else {
        return 0;
    }
}

function id2type($db, $uid) {

    $sth = $db->select("Select user_type from login WHERE id = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["user_type"]) && !empty($sth)) {
        return $sth[0]["user_type"];
    } else {
        return FALSE;
    }
}

function getBriefDetails($db, $uid) {

    $sth = $db->select("Select description from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["description"]) && !empty($sth)) {
        return $sth[0]["description"];
    } else {
        return 'No description';
    }
}

function myOtherDetails($db, $uid) {

    $sth = $db->select("Select * from profile WHERE userid = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function countComment($db, $table, $field, $value) {

    $sth = $db->select("Select COUNT(*) as commentcount  from $table WHERE $field = :value AND `$table`.`status`='0'", array(":value" => $value));

    if (isset($sth[0]["commentcount"]) && !empty($sth)) {
        return $sth[0]["commentcount"];
    } else {
        return 0;
    }
}

function countlikes($db, $table, $field, $value) {

    $sth = $db->select("Select COUNT(*) as likecount  from $table WHERE $field = :value AND `$table`.`status`='0'", array(":value" => $value));

    if (isset($sth[0]["likecount"]) && !empty($sth)) {
        return $sth[0]["likecount"];
    } else {
        return 0;
    }
}

function countAllfunding($db) {

    $sth = $db->select("SELECT COUNT(*) as TotalFunding FROM `fundings`  where status=0 ");

    if (isset($sth[0]["TotalFunding"]) && !empty($sth)) {
        return $sth[0]["TotalFunding"];
    } else {
        return 0;
    }
}

function counttotalCourseCount($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as Totalclick FROM `course_clicks`  where status=0 AND course_id='" . $id . "'");

    if (isset($sth[0]["Totalclick"]) && !empty($sth)) {
        return $sth[0]["Totalclick"];
    } else {
        return 0;
    }
}

function GettotalFollower($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as TotalFollower FROM `following`  where status=0 AND user2_id='" . $id . "'");

    if (isset($sth[0]["TotalFollower"]) && !empty($sth)) {
        return $sth[0]["TotalFollower"];
    } else {
        return 0;
    }
}

function counttotalFundingClick($db, $id) {

    $sth = $db->select("SELECT COUNT(*) as Totalclick FROM `funding_clicks`  where status=0 AND funding_id='" . $id . "'");

    if (isset($sth[0]["Totalclick"]) && !empty($sth)) {
        return $sth[0]["Totalclick"];
    } else {
        return 0;
    }
}

function countAllcourses($db) {

    $sth = $db->select("SELECT COUNT(*) as TotalShort_courses FROM `short_courses`  where status=0 ");

    if (isset($sth[0]["TotalShort_courses"]) && !empty($sth)) {
        return $sth[0]["TotalShort_courses"];
    } else {
        return 0;
    }
}

function countAlljobPosted($db) {

    $sth = $db->select("SELECT COUNT(*) as Totaljobs FROM `jobs`  where status=0 ");

    if (isset($sth[0]["Totaljobs"]) && !empty($sth)) {
        return $sth[0]["Totaljobs"];
    } else {
        return 0;
    }
}

function CheckIfIlike($db, $table, $field, $value, $field2, $myid) {

    $sth = $db->select("Select id from $table WHERE $field = :value AND $field2 = :myid AND status=0", array(":value" => $value, ":myid" => $myid));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function id2username($db, $uid) {

    $sth = $db->select("Select username from login WHERE id = :uid", array(":uid" => $uid));
//    print_r($sth);
    if (isset($sth[0]["username"]) && !empty($sth)) {
        return $sth[0]["username"];
    } else {
        return FALSE;
    }
}

function getlastmessage($db, $myid, $concode, $table, $field) {

    $sth = $db->select("Select  *  FROM $table WHERE ((sender='" . $myid . "') OR (reciever='" . $myid . "')) AND $field='" . $concode . "'  ORDER BY `$table`.`id` DESC LIMIT 1");
//    print_r($sth);
    if (isset($sth[0]["message"]) && !empty($sth)) {
        return $sth[0]["message"];
    } else {
        return 'nill';
    }
}

function msgCounter($db, $myid, $concode, $table) {

    $sth = $db->select("Select count(*) as msgcont from  `$table` WHERE status=:status AND ((reciever=:reciever) AND (con_code=:con_code)) ", array(":reciever" => $myid, ":con_code" => $concode, ":status" => '0'));
    if (!empty($sth)) {
        return $sth[0]['msgcont'];
    } else {
        return 0;
    }
}

function timestamp2time($timestamp) {
    $timestamp_inseconds = $timestamp / 1000;
    return date('h:i a', $timestamp_inseconds);
}

function concode2duration($db, $con_code) {

    $sth = $db->select("Select duration from connect_request WHERE con_code = :con_code", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth[0]["duration"]) && !empty($sth)) {
        return $sth[0]["duration"];
    } else {
        return FALSE;
    }
}

function sortBy($field, &$array, $direction = 'asc') {
    usort($array, create_function('$a, $b', '
        $a = $a["' . $field . '"];
        $b = $b["' . $field . '"];

        if ($a == $b)
        {
            return 0;
        }

        return ($a ' . ($direction == 'desc' ? '>' : '<') . ' $b) ? -1 : 1;
    '));

    return true;
}

function getconnectiondetails($db, $con_code, $mentor_id) {

    $sth = $db->select("Select * from connect_request WHERE con_code = :con_code AND user_id='" . $mentor_id . "'", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function getconnectiondetails_from_mentee($db, $con_code) {

    $sth = $db->select("Select * from connect_request WHERE con_code = :con_code", array(":con_code" => $con_code));
//    print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function durationid2name($db, $id) {

    $sth = $db->select("Select * from connect_duration WHERE id = :id", array(":id" => $id));
//    print_r($sth);
//    if (isset($sth[0]["name"]) && !empty($sth)) {
//        return $sth[0]["name"];
//    }
    if (isset($sth) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function add2date($days2add) {
    $today = date("Y-m-d"); // Or can put $today = date ("Y-m-d");
//$days2add=6;

    $newt = strtotime($today . "+" . $days2add . " days");
    return $newt;
//print_r("\n");
//print_r($newt);
//print_r("\n");
//print_r(date("c",$newt));
}

function username2id($db, $uname) {

    $sth = $db->select("Select id from login WHERE username = :uname", array(":uname" => $uname));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function email2id($db, $email) {

    $sth = $db->select("Select id from login WHERE email = :email", array(":email" => $email));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function id2email($db, $id) {

    $sth = $db->select("Select email from login WHERE id = :id", array(":id" => $id));
//    print_r($sth);
    if (isset($sth[0]["email"]) && !empty($sth)) {
        return $sth[0]["email"];
    } else {
        return FALSE;
    }
}

function checkifclickbefore($db, $table, $userid, $field1, $value, $Field2) {

    $sth = $db->select("Select id from $table WHERE $field1 = :value AND $Field2 = :value2", array(":value" => $value, ":value2" => $userid));
//    print_r($sth);
    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0]["id"];
    } else {
        return FALSE;
    }
}

function recordNotification($db, $message, $token, $type, $uid, $otherparty) {

    $notifications_data = array(
        'message' => $message,
        'token' => $token,
        'type' => $type,
        'time' => time(),
        'userid' => $uid,
        'otherid' => $otherparty,
        'status' => 0,
    );
    $db->insert('notifications', $notifications_data);
}

function startRegularChat($db, $sender, $reciever, $name) {
    $dateTime = date('d/m/Y g:i:s A');

    $handshake = generatesToken('regular_chat', 'handshake', $db);
    $message = default_follow_chatmessage($name);
    $regular_chat_data = array(
        'sender' => $sender,
        'reciever' => $reciever,
        'handshake' => $handshake,
        'message' => $message,
        'date' => time(),
        'datetime' => $dateTime,
        'attachement' => '999',
        'status' => 0,
    );
    $db->insert('regular_chat', $regular_chat_data);
}

function getPostMedia($db, $table, $condition, $value) {

    $sth = $db->select("Select * from $table WHERE $condition = :value", array(":value" => $value));

    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return FALSE;
    }
}

function LoadExistingData($db, $email, $table) {

    $sth = $db->select("Select * from $table WHERE email = :email", array(":email" => $email));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return $sth[0];
    } else {
        return FALSE;
    }
}

function loadMoreComments($db, $response, $page, $table, $field, $value) {
    $limit = default_comment_limit();
    $currentpage = $page - 1;
    $startingpoint = $currentpage * $limit;
    $totalcount = countComment($db, $table, $field, $value);
    if ($startingpoint > $totalcount) {
        return ErrorMessage($response, "No data to load again");
    } else {
        $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `$table` AS `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`$field`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC  LIMIT " . $startingpoint . ',' . $limit);

        $temp_array = [];
        if (isset($sth) && !empty($sth)) {
            foreach ($sth as $rs) {
                $rs["datetime2"] = date("c", $rs["datetime"]);
                array_push($temp_array, $rs);
            }
//            return $temp_array;
            return SuccessMessageWithData($temp_array, $response, 'Comment for page' . $page);
        } else {
//            return $sth;
            return SuccessMessageWithData($sth, $response, 'Comment for page>>' . $page);
        }
    }
}

function loadSmallComment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`feed_id`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC LIMIT " . default_comment_limit());
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);


            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function loadSmallComment_Attachment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`, `comment`.`id` AS comment_id FROM `feed_attachment_comment` AS `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`attachment_id`=" . $value . " AND `comment`.`status`='0'  ORDER BY `comment`.`id` DESC LIMIT " . default_comment_limit());
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);
            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function loadAllComment_Feeds($db, $value) {

    $sth = $db->select("SELECT `profile`.`fullname`,`profile`.`profile_pix` ,`login`.`username`, `comment`.`comment`, `comment`.`datetime`,`comment`.`id` AS comment_id FROM `comment`,`profile`,`login` WHERE `comment`.`comment_by`=`profile`.`userid` AND `login`.`id`=`comment`.`comment_by` AND `comment`.`feed_id`=" . $value . " AND `comment`.`status`='0'");
    $temp_array = [];
    if (isset($sth) && !empty($sth)) {
        foreach ($sth as $rs) {
            $rs["datetime2"] = date("c", $rs["datetime"]);


            array_push($temp_array, $rs);
        }
        return $temp_array;
    } else {
        return $sth;
    }
}

function generateUsername($db, $email) {
    $emailArray = explode('@', $email);

    $tempusername = $emailArray[0];

    while (usernameExist($db, $tempusername)) {
        $pin = generatepin();
        $tempusername = $tempusername . $pin;
    }
    return $tempusername;
}

function usernameExist($db, $tempusername) {


    $sth = $db->select("Select * from login WHERE username = :username", array(":username" => $tempusername));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function didEmailExist($db, $email, $table) {

    $sth = $db->select("Select * from login WHERE email = :email", array(":email" => $email));

    if (isset($sth[0]["id"]) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function haveIchatToday($db, $con_code, $day) {

    $sth = $db->select("Select * from mentorship_activities WHERE con_code = :con_code AND day = :day", array(":con_code" => $con_code, ":day" => $day));

    if (isset($sth) && !empty($sth)) {
        return TRUE;
    } else {
        return FALSE;
    }
}




function generates_Reset_Token($table, $field, $db, $size = 10) {
    $token = "";

    for ($i = 0; $i < $size; $i++) {
        $key = array_merge(range('a', 'z'), range('A', 'Z'));
        $token .= $key [array_rand($key)];
    }
    if (istokenExist($table, $field, $token, $db) > 0) {
        generatesToken($table, $field, $db, $size);
    } else {

        return $token;
    }
}

function generatepin() {
    $pin = "";

    for ($i = 0; $i < 3; $i++) {
        $key = array_merge(range(0, 9));
        $pin .= $key [array_rand($key)];
    }

    return $pin;
}

function get_time_ago($time) {
    $time_difference = time() - $time;

    if ($time_difference < 1) {
        return 'less than 1 second ago';
    }
    $condition = array(12 * 30 * 24 * 60 * 60 => 'year',
        30 * 24 * 60 * 60 => 'month',
        24 * 60 * 60 => 'day',
        60 * 60 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($condition as $secs => $str) {
        $d = $time_difference / $secs;

        if ($d >= 1) {
            $t = round($d);
            return 'about ' . $t . ' ' . $str . ( $t > 1 ? 's' : '' ) . ' ago';
        }
    }
}

function fetchawards($db, $uid) {

    $sth = $db->select("Select * from awards WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function getcountryFlag($country) {
    $url = 'https://restcountries.eu/rest/v2/name/' . $country;
//    print_r($url);
    $service_url = $url;
    $response = loadURL($service_url);
//    return $response[1]["flag"] || $response[0]["flag"];
    if ($country == 'India') {
        return $response[1]["flag"];
    } else {
        return $response[0]["flag"];
    }
}

function getmycountry($db, $myid) {

    $sth = $db->select("Select country from profile WHERE userid = :id", array(":id" => $myid));
//    print_r($sth);
    if (isset($sth[0]["country"]) && !empty($sth)) {
        return $sth[0]["country"];
    } else {
        return FALSE;
    }
}

function fetchPlacesVisited($db, $uid) {

    $sth = $db->select("Select * from placevisited WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchVertificate($db, $uid) {

    $sth = $db->select("Select * from certificate WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchexperience($db, $uid) {

    $sth = $db->select("Select * from experience WHERE user_id = :user_id AND status = 0", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchactivities($db, $uid) {

    $sth = $db->select("Select * from activities WHERE user_id = :user_id AND status = 0 order by `id` DESC LIMIT 6", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function fetchSkills($db, $uid) {

    $sth = $db->select("Select * from skillNendorsement WHERE user_id = :user_id AND status = 0 order by `id` DESC LIMIT 6", array(":user_id" => $uid));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function getmemberInTeam($db, $positionId) {

    $sth = $db->select("Select * from `ourteam` WHERE position =:positionid AND status = 0  ORDER BY `ourteam`.`id` ASC ", array(":positionid" => $positionId));
//        print_r($sth);
    if (isset($sth) && !empty($sth)) {
        return $sth;
    } else {
        return [];
    }
}

function savecopytodb($db, $sender, $reciever, $message, $con_code) {
    $dateTime = date('d/m/Y g:i:s A');
    $connect_data = array(
        'sender' => $sender,
        'reciever' => $reciever,
        'message' => $message,
        'con_code' => $con_code,
        'date' => time(),
        'datetime' => $dateTime,
        'attachement' => 0,
        'status' => 0,
    );
    $db->insert('connection_chat', $connect_data);
}

function updateactivites_tbl($db, $con_code) {
    $dateex = date("Y-m-d");
    $months = explode('-', $dateex);
//         print_r($months);
//         $month=$months[1];
//         $year=$months[0];
    $day = $months[2];
    $res = haveIchatToday($db, $con_code, $day);
    if ($res === FALSE) {
        $mentorship_activities_data = array(
            'con_code' => $con_code,
            'activities' => "Start search session",
            'start_time' => time(),
            'end_time' => time(),
            'date' => time(),
            'day' => $day,
            'status' => 0
        );
        $db->insert('mentorship_activities', $mentorship_activities_data);
    } else {
        $mentorship_activities_data = array(
//                'con_code ' => $con_code,
//                 'activities' => "Start search session",
//                'start_time' => time(),
            'end_time' => time(),
//                'date' => time(),
//                'day' => $day,
            'status' => 0
        );
        $condition = "con_code='" . $con_code . "' AND day='" . $day . "'";
        $db->update("mentorship_activities", $mentorship_activities_data, $condition);
    }
}

function getlastinsertfeed($db, $myid, $feed_id) {

    $sql = "SELECT `profile`.`fullname`,`profile`.`profile_pix`,`login`.`username`,`feeds`.`id`,`feeds`.`feed_id`,`feeds`.`content`,`feeds`.`has_attachment`, `feeds`.`datetime` ,`feeds`.`poster` FROM `feeds`,`login`,`profile` where `feeds`.`status`=0 AND `profile`.`userid`=`feeds`.`poster` AND `login`.`id` =`feeds`.`poster` AND `feeds`.`id`=" . $feed_id . " ORDER BY RAND() limit 4 ";
    $result = $db->select($sql);

    if (isset($result) && !empty($result)) {
//        print_r($result);
        $temp_array = [];
        foreach ($result as $rs) {
//                print_r($rs["poster"]);
            $hasmedia = $rs["has_attachment"];
            if ($hasmedia === '1') {
                $rs["post_media"] = getPostMedia($db, 'feed_attachment', 'feed_id', $rs["id"]);
            }
            $rs["poster"] = id2username($db, $rs["poster"]);
//                $rs["coment_count"] = countComment($db, $rs["poster"]);
            $rs["coment_count"] = countComment($db, 'comment', 'feed_id', $rs["id"]);
            $rs["like_count"] = countlikes($db, 'feeds_like', 'feed_id', $rs["id"]);
            $rs["DidILike"] = CheckIfIlike($db, 'feeds_like', 'feed_id', $rs["id"], 'liked_by', $myid);
            $rs["partcomment"] = loadSmallComment_Feeds($db, $rs["id"]);

            $date = date("c", $rs["datetime"]);
//                $rs["timeago"] =  get_time_ago($date);
//$rs["datetime"] = date("c",$rs["datetime"]);
            array_push($temp_array, $rs);
        }
        return $temp_array;
//            return SuccessMessageWithData($temp_array, $response, 'Feeds data');
    } else {
        return false;
//            return ErrorMessage($response, "No post to load");
    }
}

function getallpositions($db) {
    $sql = "SELECT * FROM `ourteam_position` where status='0' ORDER BY `ourteam_position`.`priority` ASC";
    $result = $db->select($sql);
    if (!empty($result)) {
        return $result;
    } else {
        return null;
    }
}

function gettotalMember($db) {
    $sql = "SELECT COUNT(*) as total FROM `ourteam` where status='0' ORDER BY `ourteam`.`id` ASC ";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result[0]["total"];
    } else {
        return 0;
    }
}

function gettotalMentee($db) {
    $sql = "SELECT COUNT(*) as total FROM `login`,`email_token` where `login`.`email` = `email_token`.`user_email` AND `email_token`.`status`=1 AND `login`.`status`=0 AND `email_token`.`usertype`='mentee' AND `login`.`user_type`='mentee' ";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result[0]["total"];
    } else {
        return 0;
    }
}

function getincompleteRegMentor($db) {
    $sql = "SELECT COUNT(*) as total FROM email_token t1 LEFT JOIN mentor_application t2 ON t2.email = t1.user_email WHERE t2.email IS NULL AND t1.usertype='mentor' ORDER BY `usertype` ASC ";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result[0]["total"];
    } else {
        return 0;
    }
}

function getfeedpost_likedby($db, $feed_id, $user_id) {
    $sql = "SELECT `feeds_like`.`feed_id`, `feeds_like`.`liked_by`, `profile`.`fullname`,`profile`.`profile_pix`,`login`.`username` FROM `feeds_like`,`login`,`profile` WHERE `login`.`id`=`feeds_like`.`liked_by` AND `profile`.`userid`=`feeds_like`.`liked_by` AND `feeds_like`.`feed_id`=" . $feed_id . " AND `feeds_like`.`liked_by` !=" . $user_id . " AND `feeds_like`.`status` ='0' LIMIT " . default_limit();
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result;
    } else {
        return $result;
    }
}

function getfeedAttachment_likedby($db, $feed_id, $user_id) {
    $sql = "SELECT `feeds_attachment_like`.`attachment_id`, `feeds_attachment_like`.`liked_by`, `profile`.`fullname`,`profile`.`profile_pix`,`login`.`username` FROM `feeds_attachment_like`,`login`,`profile` WHERE `login`.`id`=`feeds_attachment_like`.`liked_by` AND `profile`.`userid`=`feeds_attachment_like`.`liked_by` AND `feeds_attachment_like`.`attachment_id`=" . $feed_id . " AND `feeds_attachment_like`.`liked_by` !=" . $user_id . " AND `feeds_attachment_like`.`status` ='0' LIMIT " . default_limit();
//    print_r($sql) ;
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result;
    } else {
        return $result;
    }
}

function checkif_following($db, $user_id, $other_id) {
//    $sql = "SELECT * FROM `following` where ((user1_id='".$user_id."' AND user2_id='".$other_id."') OR (user1_id='".$other_id."' AND user2_id='".$user_id."')) AND status =0 ";
    $sql = "SELECT * FROM `following` where (user1_id='" . $user_id . "' AND user2_id='" . $other_id . "')  AND status =0 ";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return True;
    } else {
        return FALSE;
    }
}

function getincompleteRegMentee($db) {
    $sql = "SELECT COUNT(*) as total FROM email_token t1 LEFT JOIN mentee_application t2 ON t2.email = t1.user_email WHERE t2.email IS NULL AND t1.usertype='mentee' ORDER BY `usertype` ASC";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result[0]["total"];
    } else {
        return 0;
    }
}

function gettotalMentor($db) {
    $sql = "SELECT COUNT(*) as total FROM `login`,`email_token` where `login`.`email` = `email_token`.`user_email` AND `email_token`.`status`=1 AND `login`.`status`=0 AND `email_token`.`usertype`='mentor' AND `login`.`user_type`='mentor' ";
    $result = $db->select($sql);
    if (isset($result) && !empty($result)) {
        return $result[0]["total"];
    } else {
        return 0;
    }
}

function likePostFeeds($db, $response, $feed_token, $myid) {


    $feed_id = postToken2id($db, 'feeds', 'feed_id', $feed_token);


    $haveIlikeBefore = haveIlikeBefore($db, 'feeds_like', 'feed_id', 'liked_by', $feed_id, $myid);

    if ($haveIlikeBefore === FALSE) {

        $resp = haveIUnlikeBefore($db, 'feeds_like', 'feed_id', 'liked_by', $feed_id, $myid);
        if ($resp === FALSE) {
            $feeds_like_data = array(
                'feed_id' => $feed_id,
                'liked_by' => $myid,
                'datestime' => time(),
                'status' => 0,
            );
            $db->insert('feeds_like', $feeds_like_data);
            $posterid = postToken2posterid($db, 'feeds', 'feed_id', $feed_token);
            $liker_name = id2name($db, $myid);
            $message = $liker_name . "   " . "like your post";
            $feed_token = postid2postToken($db, 'feeds', 'id', $feed_id);
            recordNotification($db, $message, $feed_token, feedtype_number(), $posterid, $myid);
            return SuccessMessage($response, 'Liked');
//            echo 'not like before';
        } else {
            $feeds_like_data = array(
                'status' => 0,
            );

            $condition = "feed_id='" . $feed_id . "' AND liked_by='" . $myid . "' ";
            $db->update("feeds_like", $feeds_like_data, $condition);
            return SuccessMessage($response, 'Liked');
//               echo 've like but unlike wanna like now';
        }
    } else {
        $feeds_like_data = array(
            'status' => 1,
        );

        $condition = "feed_id='" . $feed_id . "' AND liked_by='" . $myid . "' ";
        $db->update("feeds_like", $feeds_like_data, $condition);
        return SuccessMessage($response, 'UnLiked');
//        echo 've like before for the first time wanna unlike now';
    }
}

function likePostAttachment($db, $response, $attachment_id, $myid) {


//    $feed_id = postToken2id($db, 'feeds', 'feed_id', $feed_token);

    $check = didFieldExist2($db, 'feed_attachment', 'id', $attachment_id);
    if ($check === FALSE) {
        return ErrorMessage($response, "Post does not exist");
    }
    $haveIlikeBefore = haveIlikeBefore($db, 'feeds_attachment_like', 'attachment_id', 'liked_by', $attachment_id, $myid);

    if ($haveIlikeBefore === FALSE) {

        $resp = haveIUnlikeBefore($db, 'feeds_attachment_like', 'attachment_id', 'liked_by', $attachment_id, $myid);
        if ($resp === FALSE) {
            $feeds_attachment_like_data = array(
                'attachment_id' => $attachment_id,
                'liked_by' => $myid,
                'datestime' => time(),
                'status' => 0,
            );
            $db->insert('feeds_attachment_like', $feeds_attachment_like_data);
            $feed_token = postid2postToken($db, 'feeds', 'id', attachmentId2Postid($db, $attachment_id));
            $posterid = postToken2posterid($db, 'feeds', 'feed_id', $feed_token);
            $liker_name = id2name($db, $myid);
            $message = $liker_name . "   " . "like your  post attachment";
            recordNotification($db, $message, $attachment_id, feed_media_type_number(), $posterid, $myid);
            return SuccessMessage($response, 'Liked');
        } else {
            $feeds_attachment_like_data = array(
                'status' => 0,
            );

            $condition = "attachment_id='" . $attachment_id . "' AND liked_by='" . $myid . "' ";
            $db->update("feeds_attachment_like", $feeds_attachment_like_data, $condition);
            return SuccessMessage($response, 'Liked');
//               echo 've like but unlike wanna like now';
        }
    } else {
        $feeds_attachment_like_data = array(
            'status' => 1,
        );

        $condition = "attachment_id='" . $attachment_id . "' AND liked_by='" . $myid . "' ";
        $db->update("feeds_attachment_like", $feeds_attachment_like_data, $condition);
        return SuccessMessage($response, 'UnLiked');
//        echo 've like before for the first time wanna unlike now';
    }
}

function commentPostFeeds($db, $response, $feed_id, $comment, $myid) {

    $feeds_comment_data = array(
        'feed_id' => $feed_id,
        'comment' => $comment,
        'comment_by' => $myid,
        'datetime' => time(),
        'status' => 0,
    );
    $resp = $db->insert('comment', $feeds_comment_data);
    if ($resp === FALSE) {
        return ErrorMessage($response, 'error occured');
    } else {

        $liker_name = id2name($db, $myid);
        $feed_token = postid2postToken($db, 'feeds', 'id', $feed_id);
        $posterid = postToken2posterid($db, 'feeds', 'feed_id', $feed_token);
        $message = $liker_name . "   " . "comment on  your  post";
        recordNotification($db, $message, $feed_token, feedtype_number(), $posterid, $myid);
        return SuccessMessageWithData($resp, $response, 'Comment Successful');
    }
}

function commentPostAttachment($db, $response, $attachment_id, $comment, $myid) {
    $check = didFieldExist2($db, 'feed_attachment', 'id', $attachment_id);
    if ($check === FALSE) {
        return ErrorMessage($response, "Post does not exist");
    }
    $feeds_comment_data = array(
        'attachment_id' => $attachment_id,
        'comment' => $comment,
        'comment_by' => $myid,
        'datetime' => time(),
        'status' => 0,
    );
    $resp = $db->insert('feed_attachment_comment', $feeds_comment_data);
    if ($resp === FALSE) {
        return ErrorMessage($response, 'error occured');
    } else {
        $feed_token = postid2postToken($db, 'feeds', 'id', attachmentId2Postid($db, $attachment_id));
        $posterid = postToken2posterid($db, 'feeds', 'feed_id', $feed_token);
        $liker_name = id2name($db, $myid);
        $message = $liker_name . "   " . "comment on  your  post attachment";
        recordNotification($db, $message, $attachment_id, feed_media_type_number(), $posterid, $myid);
        return SuccessMessageWithData($resp, $response, 'Comment Successful');
    }
}

function DeletePostComment_feeds($db, $response, $comment_id, $feed_id, $myid) {
    $feeds_comment_delete_data = array(
        'status' => 1,
    );
    $condition = "id='$comment_id' AND feed_id='$feed_id' AND comment_by='$myid' AND status='0'";
    $resp = $db->update("comment", $feeds_comment_delete_data, $condition);
//       print_r($resp);
    if ($resp === FALSE) {
        return ErrorMessage($response, 'error occured ');
    } else {
        return SuccessMessage($response, 'Comment deleted Successfully');
    }
}

function DeletePost($db, $response, $feed_id, $myid) {
    $feeds_comment_delete_data = array(
        'status' => 1,
    );
    $condition = "feed_id='$feed_id' AND poster='$myid' AND status='0'";
    $resp = $db->update("feeds", $feeds_comment_delete_data, $condition);
//       print_r($resp);
    if ($resp === FALSE) {
        return ErrorMessage($response, 'error occured ');
    } else {
        return SuccessMessage($response, 'Post deleted Successfully');
    }
}

function DeletePostcomment_Attachment($db, $response, $comment_id, $attachment_id, $myid) {
    $check = didFieldExist2($db, 'feed_attachment', 'id', $attachment_id);
    if ($check === FALSE) {
        return ErrorMessage($response, "Post does not exist");
    }
    $feeds_comment_delete_data = array(
        'status' => 1,
    );
    $condition = "id='$comment_id' AND attachment_id='$attachment_id' AND comment_by='$myid' AND status='0'";
    $resp = $db->update("feed_attachment_comment", $feeds_comment_delete_data, $condition);
//       print_r($resp);
    if ($resp === FALSE) {
        return ErrorMessage($response, 'error occured ');
    } else {
        return SuccessMessage($response, 'Comment deleted Successfully');
    }
}

function processNotificationArray($db, $sql, $myid, $type) {



    $sql = $db->prepare($sql);
    $sql->execute($param);
    $fetchMode = PDO::FETCH_ASSOC;
    $data = $sql->fetchAll($fetchMode);

//        print_r($data);
    $newarr = array();
    $temp_sender = array();
    $newarr2 = array();

    foreach ($data as $value) {
//        print_r($data);

        if ($type === 'connectchat') {
            $concode = $value['con_code'];
            $table = "connection_chat";
            $field = "con_code";
        } elseif ($type === 'regular_chat') {
            $concode = $value['handshake'];
            $table = "regular_chat";
            $field = "handshake";
        }


        $sender = $value['sender'];
        $resp = $value['reciever'];


        $timestamp = $value['date'];
        $me = NULL;
        $other = NULL;
        if ($myid === $sender) {
            $me = $sender;
            $other = $resp;
        } else {
            $me = $resp;
            $other = $sender;
        }
//        $mId = id2email($db, $other);
        $mId = $concode;
        $otherdatails = myOtherDetails($db, $other);
//                $rs["datetime"] = date("c", $rs["datetime"]);
        $rs["username"] = id2username($db, $rs["id"]);
        $fullname = $otherdatails["fullname"];
        $rs["Decription"] = $otherdatails["description"];
        $profile_pix = $otherdatails["profile_pix"];
//                print_r($curr);
//                $pprofile_pix = getprofpix($db, $curr);
////                $con_code = getConcode($curr);
        $prev_msg[0]['username'] = id2username($db, $other);
        $prev_msg[0]['profile_pix'] = $profile_pix;
        $prev_msg[0]['fullname'] = $fullname;
        $prev_msg[0]['lastmessage'] = getlastmessage($db, $myid, $concode, $table, $field);
        $prev_msg[0]['msgCounter'] = msgCounter($db, $myid, $concode, $table);
        $prev_msg[0]['time'] = timestamp2time($timestamp);
        $prev_msg[0]['type'] = $type;
        $newarr[$mId] = $prev_msg;
    }
//    foreach ($data as $value) {
//         $sender = $value['sender'];
//        $resp = $value['reciever'];
//        if ($sender == $myid) {   
//        
//            if (!in_array($resp, $temp_sender)) {
//                   echo 'hey not';
//                array_push($temp_sender, $resp);
//            }
//        } else {
//             if (!in_array($sender, $temp_sender)) {
//                 echo 'hey2 not';
//                array_push($temp_sender, $sender);
//            }
//        }
//         foreach ($temp_sender as $curr) {
//
//            $mId = id2email($db, $curr);
//             $otherdatails = myOtherDetails($db, $curr);
////                $rs["datetime"] = date("c", $rs["datetime"]);
//                $rs["username"] = id2username($db, $rs["id"]);
//               $fullname = $otherdatails["fullname"];
//                $rs["Decription"] = $otherdatails["description"];
//                $profile_pix= $otherdatails["profile_pix"];
////                print_r($curr);
////                $pprofile_pix = getprofpix($db, $curr);
//////                $con_code = getConcode($curr);
//            $prev_msg[0]['user1dav'] = $curr;
////                $prev_msg = loadPrevchat($db, $curr, $admin_id);
//                $prev_msg[0]['profile_pix'] = $profile_pix;
//                $prev_msg[0]['fullname'] = $fullname;
////                $prev_msg[0]['msgCounter'] = msgCounter($db, $curr, $admin_id);
////                $in_array['user1dav'] = $curr;
////                $in_array['user2dav'] = $user2;
////                $prev_msg[0]['con_code'] = getid($db, $curr);
//            $newarr[$mId] = $prev_msg;
//        }
//    }
    //    print_r($newarr);
    foreach ($newarr as $key => $value) {
        $value[0]["handshake"] = $key;
        array_push($newarr2, $value[0]);
    }
    return $newarr2;
}

function processNotificationArrayold($db, $sql, $myid) {
    print_r($myid);
    $sql = $db->prepare($sql);
    $sql->execute($param);
    $fetchMode = PDO::FETCH_ASSOC;
    $data = $sql->fetchAll($fetchMode);

//        print_r($data);
    $newarr = array();
    $temp_sender = array();
    $newarr2 = array();
    foreach ($data as $value) {
        $sender = $value['sender'];
        $resp = $value['reciever'];
        if ($sender == $myid) {

            if (!in_array($resp, $temp_sender)) {
                echo 'hey not';
                array_push($temp_sender, $resp);
            }
        } else {
            if (!in_array($sender, $temp_sender)) {
                echo 'hey2 not';
                array_push($temp_sender, $sender);
            }
        }
        foreach ($temp_sender as $curr) {

            $mId = id2email($db, $curr);
            $otherdatails = myOtherDetails($db, $curr);
//                $rs["datetime"] = date("c", $rs["datetime"]);
            $rs["username"] = id2username($db, $rs["id"]);
            $fullname = $otherdatails["fullname"];
            $rs["Decription"] = $otherdatails["description"];
            $profile_pix = $otherdatails["profile_pix"];
//                print_r($curr);
//                $pprofile_pix = getprofpix($db, $curr);
////                $con_code = getConcode($curr);
            $prev_msg[0]['user1dav'] = $curr;
//                $prev_msg = loadPrevchat($db, $curr, $admin_id);
            $prev_msg[0]['profile_pix'] = $profile_pix;
            $prev_msg[0]['fullname'] = $fullname;
//                $prev_msg[0]['msgCounter'] = msgCounter($db, $curr, $admin_id);
//                $in_array['user1dav'] = $curr;
//                $in_array['user2dav'] = $user2;
//                $prev_msg[0]['con_code'] = getid($db, $curr);
            $newarr[$mId] = $prev_msg;
        }
    }
    print_r('$newarr');
//    print_r($newarr);
    foreach ($newarr as $key => $value) {
        $value[0]["Uname"] = $key;
        array_push($newarr2, $value[0]);
    }
    return $newarr2;
}

function loadURL($service_url) {
    $curl = curl_init($service_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $curl_response = curl_exec($curl);
    if ($curl_response === false) {
        $info = curl_getinfo($curl);
        curl_close($curl);
//        die('error occured during curl exec. Additioanl info: ' . var_export($info));
        die('<div class="alert alert-danger outPopUp" style="font-size: 17px;color: red;position: absolute;height: 200px;z-index: 15;top: 60%;left: 50%;margin: -100px 0 0 -150px;">An error occure please \n check your network connection and try again</div>');
    }
    curl_close($curl);

    $decoded = json_decode($curl_response);
    $t = objectToArray($decoded);
    return $t;
}

function objectToArray($d) {
    if (is_object($d)) {
        // Gets the properties of the given object
        // with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        /*
         * Return array converted to object
         * Using __FUNCTION__ (Magic constant)
         * for recursive call
         */
        return array_map(__FUNCTION__, $d);
    } else {
        // Return array
        return $d;
    }
}
