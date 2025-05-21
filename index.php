<?php

error_reporting(0);
ini_set('display_errors', 0);
ob_start();
header('Content-type: application/json');

require_once("./vendor/autoload.php");
require_once("config.php");
require_once("util.php");

// Details to retrieve
$error = 0;
$errorMsg = "";
$subEndDate = 0;
$isValid = 0;
$purchaseToken = "";
$termSku = "";

// App purchase details common to all stores
$targetStore = purify(@$_REQUEST["store"],0);       // eg: google, apple, amazon
$appPackage = purify(@$_REQUEST["app"],0);          // eg: com.example.myapp
$productID = purify(@$_REQUEST["product"],0);       // eg: com.example.myapp.product1
$purchaseType = purify(@$_REQUEST["type"],0);       // eg: subscription, product

// Set up the validator for the appropriate store
if ($targetStore == "google") {
    require_once("setup_google.php");
    $purchaseToken = purify(@$_REQUEST["token"],0);     // Some long string of text

} elseif ($targetStore == "apple") {
    require_once("setup_apple.php");

} elseif ($targetStore == "amazon") {
    require_once("setup_amazon.php");
    $receiptid = purify(@$_REQUEST["receiptid"],0);    // The receipt ID from the Amazon receipt
    $userid = purify(@$_REQUEST["userid"],0);          // The user ID from the Amazon receipt

} else {
    $error = 1;
    $errorMsg = "Target store unknown: " . $targetStore;
}

// Check that all parameters were received
if ($appPackage == "" || $productID == "" || $purchaseType == "") {
    $error = 1;
    $errorMsg = "One or more required parameters is missing";
}

// Proceed if no errors
if ($error == 0) {
    try {
        if ($targetStore == "google") {
            if ($purchaseType == "subscription") {
                $response = $validator->setPackageName($appPackage)
                    ->setProductId($productID)
                    ->setPurchaseToken($purchaseToken)
                    ->validateSubscription();

                $paymentState = $response->getPaymentState();

                if ($response->getExpiryTimeMillis() > 0) {
                    $isValid = 1;
                    $subEndDate = intval($response->getExpiryTimeMillis() / 1000); // Convert ms to seconds
                }

            } else if ($purchaseType == "product") {
                $response = $validator->setPackageName($appPackage)
                    ->setProductId($productID)
                    ->setPurchaseToken($purchaseToken)
                    ->validatePurchase();
                if ($response->isValid()) {
                    $isValid = 1;
                }

            } else {
                $error = 1;
                $errorMsg = "Unknown purchase type: " . $purchaseType;
            }
        } // End Google

        if ($targetStore == "amazon") {
            $response = null;
            try {
              $response = $validator->setDeveloperSecret($amazonDeveloperSecret)
                  ->setReceiptId($receiptid)
                  ->setUserId($userid)
                  ->validate();

            } catch (Exception $e) {
                $error = 1;
                $errorMsg = $e->getMessage();
            }

            if ($response->isValid()) {
                $isValid = 1;

                foreach ($response->getPurchases() as $purchase) {
                    $rawResponse = $purchase->getRawResponse();
                    if (array_key_exists("renewalDate", $rawResponse)) {
                        if (intval($rawResponse["renewalDate"] / 1000) > $subEndDate) {
                            $subEndDate = intval($rawResponse["renewalDate"] / 1000); // Convert ms to seconds
                        }
                    }
                    if (array_key_exists("termSku", $rawResponse)) {
                        // Return what was actually purchased for the subscription term
                        $termSku = $rawResponse["termSku"];
                    }
              }
            } else {
                $error = 1;
                $errorMsg = "Receipt " . $receiptid . " is not valid. Result code = " . $response->getResultCode();
            }
        } // End Amazon

    } catch (Exception $e) {
        $error = 1;
        $errorMsg = $e->getMessage();
    }

}

$results = new stdClass;
$results->error = $error;
$results->error_msg = $errorMsg;
$results->package = $appPackage;
$results->product_id = $productID;
$results->valid = $isValid;
if ($termSku != "") {
    $results->term_sku = $termSku;
}
if ($purchaseToken != "") {
    $results->token = $purchaseToken;
}
if ($subEndDate > 0) {
    $results->sub_end_date = $subEndDate;
}

// Flush output buffers and output only the needed JSON
ob_clean();
echo json_encode($results);
ob_end_flush();
?>
