<?php

error_reporting(0);
ini_set('display_errors', 0);
header('Content-type: application/json');

require_once("./vendor/autoload.php");
require_once("config.php")
require_once("util.php")

// Details to retrieve
$error = 0;
$errorMsg = "";
$endDate = 0;

// App purchase details
$targetStore = purify(@$_REQUEST["store"],0);       // eg: google, apple, amazon
$appPackage = purify(@$_REQUEST["app"],0);          // eg: com.example.myapp
$productID = purify(@$_REQUEST["product"],0);       // eg: com.example.myapp.product1
$purchaseToken = purify(@$_REQUEST["token"],0);     // Some long string of text
$purchaseType = purify(@$_REQUEST["type"],0);       // eg: subscription, product

// Set up the validator for the appropriate store
if ($targetStore == "google") {
    require_once("setup_google.php")
} elseif ($targetStore == "apple") {
    require_once("setup_apple.php")
} elseif ($targetStore == "amazon") {
    require_once("setup_amazon.php")
} else {
    $error = 1;
    $errorMsg = "Target store unknown: " . $targetStore;
}

// Check that all parameters were received
if ($appPackage == "" || $productID == "" || $purchaseToken == "" || $purchaseType == "") {
    $error = 1;
}

// Proceed if no errors
if ($error == 0) {
    try {
        if ($purchaseType == "subscription") {
            $response = $validator->setPackageName($appPackage)
                ->setProductId($productID)
                ->setPurchaseToken($purchaseToken)
                ->validateSubscription();
        } else {
            $response = $validator->setPackageName($appPackage)
                ->setProductId($productID)
                ->setPurchaseToken($purchaseToken)
                ->validatePurchase();
        }

        if ($response->getStartTimeMillis() > 0) {
            // Convert milliseconds to seconds
            $endDate = round($response->getStartTimeMillis() / 1000, 0);
        }

    } catch (Exception $e){
        $error = 1;
        $errorMsg = $e->getMessage();
    }

}

$results = new stdClass;
$results->error = $error;
$results->error_msg = $errorMsg;
$results->end_date = $endDate;

echo json_encode($results);

?>

