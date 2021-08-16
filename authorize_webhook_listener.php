<?php

header('Authorization: Basic CHANGEIT');
//
//
//This is a listener for the Authorize.Net payment system 
//depending on the event type (eType) different functions are run
//that manipulate data in a database
//
//R.Mccoy
//

// Read in the POST submission
$notification = file_get_contents("php://input");
// Decode JSON into object
$response = json_decode($notification);

//Needed for testing
$debug_export = var_export($response, true);
file_put_contents("webhook_response.txt", $debug_export, FILE_APPEND);
//End testing

//START DB connect, enter creds below
$db_host = 'localhost';
$db_user = 'db_username';
$db_pwd = 'yourpasshere';
$database = 'db_name';

if (!mysql_connect($db_host, $db_user, $db_pwd,MYSQL_CLIENT_SSL))
die("Can't connect to database");
if (!mysql_select_db($database))
die("Can't select database");
//END DB connect

/////////////GLOBAL NOTIFICATION VARIABLES AREA////////////////////////////////////////////////////////GLOBAL NOTIFICATION VARIABLES AREA////////////////////////////////////////////////////////////

$eType = $response->eventType;
$notify_ID = $response->notificationId;
$notify_Date = $response->eventDate;

/////////////GLOBAL NOTIFICATION VARIABLES AREA//////////////////////////////////////////////GLOBAL NOTIFICATION VARIABLES AREA////////////////////////////////////////////////////////////





//Here is where we determine which function to run depending on the type of event

switch ($eType) {
        
    case 'net.authorize.payment.authcapture.created':
        paymentcapturecreated_Verify();
        break;
    case 'net.authorize.payment.refund.created':
        paymentrefundcreated_Verify();
        break;
    case 'net.authorize.payment.authorization.created':
        paymentAuthcreated_Verify();
        break;
     case 'net.authorize.payment.capture.created':
        paymentCapturecreated_Verify();
        break;
     case 'net.authorize.payment.priorAuthCapture.created':
        paymentPriorCaptCreate_Verify();
        break;
     case 'net.authorize.payment.void.created':
        paymentVoid_Verify();
        break;
  	case 'net.authorize.customer.created':
        customercreated_Verify();
        break;
    case 'net.authorize.customer.updated':
        //echo 'net.authorize.customer.updated';
        customerUpdate_Verify();
        break;
    case 'net.authorize.customer.deleted':
        //echo 'net.authorize.customer.deleted';
        customerdeleted_Verify();
        break;
    case 'net.authorize.customer.paymentProfile.created':
        paymentprofilecreated_Verify();
        break;
    case 'net.authorize.customer.paymentProfile.deleted':
        //echo 'net.authorize.customer.paymentProfile.deleted';
         paymentprofileDeleted_Verify();
        break;
    case 'net.authorize.customer.paymentProfile.updated':
        //echo 'net.authorize.customer.paymentProfile.deleted';
          paymentprofileUpdate_Verify();
         break;
     case 'net.authorize.customer.subscription.created':
     //echo 'net.authorize.customer.subscription.created';
     subscriptioncreated_Verify();
     	break;
     case 'net.authorize.customer.subscription.cancelled':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionCanceled_Verify();
     	break;
	 case 'net.authorize.customer.subscription.expiring':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionExpiring_Verify();
     	break;
	 case 'net.authorize.customer.subscription.suspended':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionSuspend_Verify();
     	break;
	 case 'net.authorize.customer.subscription.terminated':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionTerminated_Verify();
     	break;
	 case 'net.authorize.customer.subscription.updated':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionUpdated_Verify();
     	break;
	 case 'net.authorize.customer.subscription.suspended':
     //echo 'net.authorize.customer.subscription.created';
     subscriptionSuspend_Verify();
     	break;

    default:
        echo "No action has been created for the following event type: $eType";
}

///////////FUNCTIONS////////////////////////////////////FUNCTIONS///////////////////////////////////////
///////////FUNCTIONS////////////////////////////////////FUNCTIONS///////////////////////////////////////

function paymentprofilecreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentrefundcreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentAuthcreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentCapturecreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentPriorCaptCreate_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentVoid_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentprofileDeleted_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function paymentprofileUpdate_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$cProId = $response->payload->customerProfileId;
$cPayProId = $response->payload->id;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'cProId' => "$cProId", 'cPayProId' => "$cPayProId" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}

function paymentcapturecreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$aCapId = $response->payload->id;
$Amount = $response->payload->authAmount;
$vars = array('eType' => "$eType", 'notify_ID' => "$notify_ID", 'notify_Date' => "$notify_Date", 'aCapId' => "$aCapId", 'Amount' => "$Amount" );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
    //Ryan, Need the Variables that are sent from the Notification API. Once I have those I Can finish these scripts. //
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID, Auth_Amount) VALUES ('$aCapId', 'none', 'none', 'none', '$eType', '$notify_Date', '$notify_ID', '$Amount')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function customerdeleted_Verify(){
global $eType, $notify_ID, $notify_Date, $response;    
$cPayProId = $response->payload->paymentProfile->id;
$merCusId = $response->payload->merchantCustomerId;
$cProId = $response->payload->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
    //Ryan, Need the Variables that are sent from the Notification API. Once I have those I Can finish these scripts. //
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function customerUpdate_Verify(){
global $eType, $notify_ID, $notify_Date, $response;    
$cPayProId = $response->payload->paymentProfile->id;
$merCusId = $response->payload->merchantCustomerId;
$cProId = $response->payload->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
    //Ryan, Need the Variables that are sent from the Notification API. Once I have those I Can finish these scripts. //
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}

function customercreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;    
$cPayProId = $response->payload->paymentProfile->id;
$merCusId = $response->payload->merchantCustomerId;
$cProId = $response->payload->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
    //Ryan, Need the Variables that are sent from the Notification API. Once I have those I Can finish these scripts. //
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', 'none', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}

function subscriptioncreated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}

function subscriptionCanceled_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}

function subscriptionExpiring_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function subscriptionSuspend_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function subscriptionTerminated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}
function subscriptionUpdated_Verify(){
global $eType, $notify_ID, $notify_Date, $response;
$subStatus = $response->payload->status;
$subName = $response->payload->name;
$subAmount = $response->payload->amount;
$cProId = $response->payload->profile->customerProfileId;
$cPayProId = $response->payload->profile->customerPaymentProfileId;
$subCusShipId = $response->payload->profile->customerShippingAddressId;
$subId = $response->payload->profile->id;
$vars = array('eType' => $eType, 'notify_ID' => $notify_ID, 'notify_Date' => $notify_Date, 'cProId' => $cProId, 'cPayProId' => $cPayProId, 'subId' => $subId );
file_put_contents("webhook_response.txt", print_r($vars, true), FILE_APPEND);
$sql = "INSERT INTO auth_notify (TransID, SubScripID, CustProID, PayProID, Not_Type, Not_Date, Not_ID) VALUES ('none', '$subId', '$cProId', '$cPayProId', '$eType', '$notify_Date', '$notify_ID')";
$res = mysql_query($sql) or die(mysql_error());
return;
}


///////////FUNCTIONS////////////////////////////////////FUNCTIONS///////////////////////////////////////
///////////FUNCTIONS////////////////////////////////////FUNCTIONS///////////////////////////////////////

?>


