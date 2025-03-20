<?php

// Google authentication and verification
use Google\Service\AndroidPublisher;
use ReceiptValidator\GooglePlay\Validator as PlayValidator;
$scopes = ['https://www.googleapis.com/auth/androidpublisher'];
$client = new \Google_Client();
$client->setApplicationName($applicationName);
$client->setAuthConfig($serviceAccountJsonFile);
$client->setScopes($scopes);
$validator = new PlayValidator(new \Google\Service\AndroidPublisher($client));

?>

