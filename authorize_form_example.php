<?php
//
//Ingest info from a form, enter customer info into the website DB and pass
//the info to Authorize.Net payment system 
//
//R.Mccoy

include('admin/db_connect.php');
//include '/../shopper-session.php';
include '../example_cart2/shopper-session.php';

$testing = True;

$ses = $_POST['sesid']; 
$sfname = $_POST['shipfname'];
$slname = $_POST['shiplname'];
$sadd = $_POST['shipaddress1'];
$sadd1 = $_POST['shipaddress2'];
$scity = $_POST['shipcity'];
$sstate = $_POST['shipstate'];
$szip = $_POST['shipzip'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$bilfname = $_POST['billfname'];
$billname = $_POST['billlname'];
$biladd = $_POST['billaddress1'];
$biladd1 = $_POST['billaddress2'];
$bilcity = $_POST['billcity'];
$bilstate = $_POST['billstate'];
$bilzip = $_POST['billzip'];
$upname = $_POST['upname'];
$billint = $_POST['upint'];
$tripr = $_POST['prodtriprice'];
$upsprice = $_POST['upsellp'];
$purtotal = $_POST['purtotal'];
$submit = $_POST['finalcheck'];  



    require 'vendor/autoload.php';
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;
    use net\authorize\api\constants as AnetConstants;
    date_default_timezone_set('America/New_York');

  define("AUTHORIZENET_LOG_FILE", "phplog");
  
  
if(isset($submit) && $submit == 'Checkout Now' && $_POST['pspam'] == '' && $_POST['shipfname'] != '' && $_POST['shiplname'] != '' && $_POST['shipaddress1'] != '' && $_POST['shipcity'] != '' && $_POST['shipstate'] != '' && $_POST['shipzip'] != '' && $_POST['email'] != '' && $_POST['phone'] != '' && $ses != ''){
$sql = "INSERT INTO cust_orders (SesID, ShipFname, ShipLname, ShipAddress1, ShipAddress2, ShipCity, ShipState, ShipZip, CustEmail, CustPhone, BillFname, BillLname, BillAddress1, BillAddress2, BillCity, BillState, BillZip, ProdPurName, BillInterval, Purchase_Price, ProdUpsell) VALUES ('$ses', '$sfname', '$slname', '$sadd', '$sadd1', '$scity', '$sstate', '$szip', '$email', '$phone', '$bilfname', '$billname', '$biladd', '$biladd1', '$bilcity', '$bilstate', '$bilzip', '$upname', '$billint', '$purtotal', 'Yes')";
$resc = mysql_query($sql) or die(mysql_error());

//$custadd = $sadd $sadd1;
$custadd = $sadd . " " . $sadd1;
$sql2 = "INSERT INTO customers_pro (FName, Lname, Address_St, City, State, Zip, Phone, Email) VALUES ('$sfname', '$slname', '$custadd', '$scity', '$sstate', '$szip', '$phone', '$email')";
$resc2 = mysql_query($sql2) or die(mysql_error());


  function createSubscription($intervalLength) {
//Need interval for 30 60 90 days, NEED SELECTION IN FORM --RYAN

$custinfo = array("fname" => $_POST['fname'], "lname" => $_POST['lname'], "address" => $_POST['address'], "city" => $_POST['city'], "state" => $_POST['billstate'], "country" => 'USA', "zip" => $_POST['zip'], "email" => $_POST['email'], "phone" => $_POST['phone']);
$shippinginfo = array("shipfname" => $_POST['shipfname'], "shiplname" => $_POST['shiplname'], "shipaddress" => $_POST['shipaddress'], "shipcity" => $_POST['shipcity'], "shipstate" => $_POST['shipstate'], "shipcountry" => 'USA', "shipzip" => $_POST['shipzip']);

//customer cc info 
if($testing) {
$paymentinfo = array("ccnumber" => '4111111111111111', "ccexpr" => '2020-12');
} else {
$paymentinfo = array("ccnumber" => $_POST['ccnum'], "ccexpr" => $_POST['expyr'] . '-' . $_POST['expmon']); --RYAN
}

$today = new DateTime('now');


    // Common Set Up for API Credentials
    $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
    $merchantAuthentication->setName(AnetConstants\ANetEnvironment::MERCHANT_LOGIN_ID);
    $merchantAuthentication->setTransactionKey(AnetConstants\ANetEnvironment::MERCHANT_TRANSACTION_KEY);

    $refId = 'ref' . time();
//CREATE CUSTOMER PROFILE
        // Create the payment data for a credit card
      $creditCard = new AnetAPI\CreditCardType();
      $creditCard->setCardNumber($paymentinfo['ccnumber']);
      $creditCard->setExpirationDate($paymentinfo['ccexpr']);
      $paymentCreditCard = new AnetAPI\PaymentType();
      $paymentCreditCard->setCreditCard($creditCard);

      // Create the Bill To info
      $billto = new AnetAPI\CustomerAddressType();
      $billto->setFirstName($custinfo['fname']);
      $billto->setLastName($custinfo['lname']);
      $billto->setCompany('N/A');
      $billto->setAddress($custinfo['address']);
      $billto->setCity($custinfo['city']);
      $billto->setState($custinfo['state']);
      $billto->setZip($custinfo['zip']);
      $billto->setCountry($custinfo['country']);

      $paymentprofile = new AnetAPI\CustomerPaymentProfileType();

      $paymentprofile->setCustomerType('individual');
      $paymentprofile->setBillTo($billto);
      $paymentprofile->setPayment($paymentCreditCard);
      $paymentprofiles[] = $paymentprofile;
      $customerprofile = new AnetAPI\CustomerProfileType();
      $customerprofile->setDescription("Customer 2 Test PHP");

      $customerprofile->setMerchantCustomerId("M_".$custinfo['email']);
      $customerprofile->setEmail($custinfo['email']);
      $customerprofile->setPaymentProfiles($paymentprofiles);

      $requesta = new AnetAPI\CreateCustomerProfileRequest();
      $requesta->setMerchantAuthentication($merchantAuthentication);
      $requesta->setRefId($refId);
      $requesta->setProfile($customerprofile);
      $controllera = new AnetController\CreateCustomerProfileController($requesta);
      $responsea = $controllera->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
      if (($responsea != null) && ($responsea->getMessages()->getResultCode() == "Ok"))
      {
        //  echo "Succesfully create customer profile : " . $responsea->getCustomerProfileId() . "\n";
          //STORING CUSTOMER PROFILE ID
          $existingcustomerprofileid = $responsea->getCustomerProfileId();
          
          $sql0 = "UPDATE cust_orders SET CustProID = '$existingcustomerprofileid' WHERE SesID = '" . $_POST['sesid'] . "'";
          $result0 = mysql_query($sql0) or die(mysql_error());
          

          $paymentProfiles = $responsea->getCustomerPaymentProfileIdList();

          //STORING CUSTOMER  PAYMENT PROFILE ID
          $existingpaymentprofileid = $paymentProfiles[0];
          
          $sql1 = "UPDATE cust_orders SET PayProID = '$existingpaymentprofileid' WHERE SesID = '" . $_POST['sesid'] . "'";
          $result1 = mysql_query($sql1) or die(mysql_error());

          //echo "SUCCESS: PAYMENT PROFILE ID : " . $paymentProfiles[0] . "\n";
       }
      else
      {
          //echo "ERROR :  Invalid response\n";
          $errorMessagesa = $responsea->getMessages()->getMessage();
         // echo "Response : " . $errorMessagesa[0]->getCode() . "  " .$errorMessagesa[0]->getText() . "\n";
      }
      
////////////////////////////////////////////////////////////////////////////
          // Create the customer shipping address
      $customershippingaddress = new AnetAPI\CustomerAddressType();
      $customershippingaddress->setFirstName($shippinginfo['shipfname']);
      $customershippingaddress->setLastName($shippinginfo['shiplname']);
      $customershippingaddress->setCompany("N/A");
      $customershippingaddress->setAddress($shippinginfo['shipaddress']);
      $customershippingaddress->setCity($shippinginfo['shipcity']);
      $customershippingaddress->setState($shippinginfo['shipstate']);
      $customershippingaddress->setZip($shippinginfo['shipzip']);
      $customershippingaddress->setCountry($shippinginfo['shipcountry']);
      $customershippingaddress->setPhoneNumber($custinfo['phone']);
      $customershippingaddress->setFaxNumber($custinfo['fax']);

      // Create a new customer shipping address for an existing customer profile

      $requestb = new AnetAPI\CreateCustomerShippingAddressRequest();
      $requestb->setMerchantAuthentication($merchantAuthentication);
      $requestb->setCustomerProfileId($existingcustomerprofileid);
      $requestb->setRefId($refId);
      $requestb->setAddress($customershippingaddress);
      $controllerb = new AnetController\CreateCustomerShippingAddressController($requestb);
      $responseb = $controllerb->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
      if (($responseb != null) && ($responseb->getMessages()->getResultCode() == "Ok") )
      {
       //   echo "Create Customer Shipping Address SUCCESS: ADDRESS ID : " . $responseb-> getCustomerAddressId() . "\n";
          //STORING ADDRESS ID
          $existingcustomeraddressid = $responseb-> getCustomerAddressId();
       }
      else
      {
   //       echo "Create Customer Shipping Address  ERROR :  Invalid response\n";
          $errorMessagesb = $responseb->getMessages()->getMessage();
     //     echo "Response : " . $errorMessagesb[0]->getCode() . "  " .$errorMessagesb[0]->getText() . "\n";
      }

//CREATE SUB FROM CUSTOMER ID

    // Subscription Type Info
    $subscription = new AnetAPI\ARBSubscriptionType();
    $subscription->setName("GreenValley Monthly Subscription");

    $interval = new AnetAPI\PaymentScheduleType\IntervalAType();
    $interval->setLength($intervalLength);
    $interval->setUnit("days");

    $paymentSchedule = new AnetAPI\PaymentScheduleType();
    $paymentSchedule->setInterval($interval);
    $paymentSchedule->setStartDate($today);
    $paymentSchedule->setTotalOccurrences("12");
    $paymentSchedule->setTrialOccurrences("0");

    $subscription->setPaymentSchedule($paymentSchedule);
    $subscription->setAmount("19.99");
    $subscription->setTrialAmount("0.00");
    
    $profile = new AnetAPI\CustomerProfileIdType();
    $profile->setCustomerProfileId($existingcustomerprofileid);
    $profile->setCustomerPaymentProfileId($existingpaymentprofileid);
    $profile->setCustomerAddressId($existingcustomeraddressid);

    $subscription->setProfile($profile);

    $requestc = new AnetAPI\ARBCreateSubscriptionRequest();
    $requestc->setmerchantAuthentication($merchantAuthentication);
    $requestc->setRefId($refId);
    $requestc->setSubscription($subscription);
    $controllerc = new AnetController\ARBCreateSubscriptionController($requestc);

    $responsec = $controllerc->executeWithApiResponse( \net\authorize\api\constants\ANetEnvironment::SANDBOX);
    
    if (($responsec != null) && ($responsec->getMessages()->getResultCode() == "Ok") )
    {
 //       echo "SUCCESS: Subscription ID : " . $responsec->getSubscriptionId() . "\n";
     }
    else
    {
//        echo "ERROR :  Invalid response\n";
        $errorMessagesc = $responsec->getMessages()->getMessage();
  //      echo "Response : " . $errorMessagesc[0]->getCode() . "  " .$errorMessagesc[0]->getText() . "\n";
    }

    //return array($responsea);
    return;
    
  }

if(!defined('DONT_RUN_SAMPLES')) {
//createSubscription(23);
createSubscription(1);
}
header('Location:example_cart.php?succ=true');
}
else{
header('Location:example_cart.php?mess=err');
}
