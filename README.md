# verifyreceipt

Verify mobile IAP receipts.

The file `service-account.json` must exist two directories above the code here.  It will look something like this:

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