<?php
require_once __DIR__ . '/../vendor/autoload.php';

$googleClient = new Google_Client();
$googleClient->setClientId('833493999764-t6km2crkjlb8nne93bdnip2kg0mcr10s.apps.googleusercontent.com');
$googleClient->setClientSecret('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');
$googleClient->setRedirectUri('http://localhost/authentication/google_auth.php');
$googleClient->addScope('email');
$googleClient->addScope('profile');
?>