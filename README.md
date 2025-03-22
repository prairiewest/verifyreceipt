# verifyreceipt

Verify mobile IAP receipts.  This code currently only supports Google subscription verification.

This is the back end (server) code for verifying receipts from in-app purchases. You must already be running a server and it must already have proper DNS and SSL certificates. You will also need PHP 8.1 or higher and Composer installed.

You supply the file `service-account.json` and it must exist above the code here.  You can read about [setting up a Google Service Account](https://prairiewest.net/2025/03/verifying-iap-subscription-receipts-for-google-play/) on my blog.

It will look something like this:

```
{
  "type": "service_account",
  "project_id": "some_name",
  "private_key_id": "some_value",
  "private_key": "some_key_value",
  "client_email": "your_service_account_email",
  "client_id": "some_client_id",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "some_cert_url",
  "universe_domain": "googleapis.com"
}
```

Check out this code into your server web root, so that it lives at a URL like `https://example.com/verifyreceipt/`

Edit the file config.php and replace with the path to your service account credentials file.

You can load the URL in your web browser to test it, it should normally give an error about some missing parameters.

This is the back end code for [IAP Badger 2](https://github.com/prairiewest/iap_badger2).