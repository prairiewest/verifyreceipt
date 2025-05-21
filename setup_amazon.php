<?php

// Amazon authentication and verification
use ReceiptValidator\Amazon\Validator as AmazonValidator;
use ReceiptValidator\Amazon\Response as ValidatorResponse;

$validator = new AmazonValidator;
$amazonDeveloperSecret = trim(file_get_contents($amazonDeveloperSecretFile));

?>