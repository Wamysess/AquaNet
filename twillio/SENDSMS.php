// Send an SMS using Twilio's REST API and PHP
<?php
// Required if your environment does not handle autoloading
require 'twilio-php-main\src\Twilio\autoload.php';

// Your Account SID and Auth Token from console.twilio.com
$sid = "ACd5f3e3e0bc2ea5622c0c7ed9f14116b2";
$token = "681e515f7b0ae4270ba7228cfe3bc8db";
$client = new Twilio\Rest\Client($sid, $token);

// Use the Client to make requests to the Twilio REST API
$client->messages->create(
    // The number you'd like to send the message to
    '+639984026173',
    [
        // A Twilio phone number you purchased at https://console.twilio.com
        'from' => '+12542218740',
        // The body of the text message you'd like to send
        'body' => "Hey Jenny! Good luck on the bar exam!"
    ]
);

