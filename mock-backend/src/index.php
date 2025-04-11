<?php
header('Content-Type: application/json; charset=utf-8');

function _log($msg) {
    $msg = "mock-backend - " . date("c") . ": " . $msg."\n";
    $out = fopen('php://stdout', 'w');
    fputs($out, $msg);
    fclose($out);
}

//get data
$data = json_decode(file_get_contents('php://input'), true);
_log("Received incoming : \"".$data."\"");
$mail=$data['person']['mail'];
$course=$data['offering']['abbreviation'];
$offering=$data['offering']['offeringId'];
$person=$data['person']['personId'];

//post new associstion
$url = 'http://intekenontvanger-generiek:8092/associations/external/'.$person;
$username = 'sis';
$password = 'secret';

// JSON data
$association = [
    "role" => "student",
    "state" => "associated",
    "remoteState" => "associated",
    "offering" => $data['offering']
    ];
// Convert the array to JSON
$jsonData = json_encode($association);

// Prepare the HTTP request with basic authentication
$options = [
    'http' => [
        'header' => [
            "Content-Type: application/json",
            "Authorization: Basic " . base64_encode("$username:$password")
        ],
        'method' => 'POST',
        'content' => $jsonData
    ]
];

$context = stream_context_create($options);

_log("POSTing to $url : \"".$jsondata."\"");

// Send the request and get the response
$response = file_get_contents($url, false, $context);

_log("Received response from $url : \"".$response."\"");

if ($response === FALSE) {
    die('Error sending request');
}

$associationresponse = json_decode($response, true);

$associationId=$associationresponse['associationId'];

$response = [
  "result" => "ok", 
  "code" => 200, 
  "message" => "Yay!  It actually worked! You are now enrolled for $course as $mail with associationId $associationId!", 
  "oo-api-offering-id" =>  $offering,
  //"redirect" => "https://optional.redirect/for-extra-information"
];
//$response = [
//  "result" => "ok", 
//  "code" => 400, 
//  "message" => "You were denied", 
//  "oo-api-offering-id" =>  $offering,
  //"redirect" => "https://optional.redirect/for-extra-information"
//];

_log("Replying to incoming request with : \"".$response."\"");

echo json_encode($response);
?>

