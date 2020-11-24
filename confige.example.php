<?php

class Config {
    const DEBUG_MODE = false;
    const USAGE_INFO_INTERVAL = 600.0;
    const EMAIL_API_NOTIFICATION_INTERVAL = 300.0;
    const EMAIL_API_ENDPOINT = "https://email.pekand.loc";
    const EMAIL_API_SENDEMAIL_ENDPOINT = "/sendemail/TOKEN";
    const EMAIL_API_CERTIFICATE = ROOT_PATH.'/storage/cert/certificate.crt';
    const EMAIL_API_TIMEOUT = 10;
    const EMAIL_API_SKIP_SSL_VERIFICATION = false;
}

