<?php
//data dev token 6d025e12db0635fe8de35bc983502c135f02fcb9a5c9ccbaefc33fb57a92e7e4
// Put your device token here (without spaces):
$deviceToken = 'b2008d4baa724b2e8c4b71bc4797fdb4974d1556dfea971036568208db3cc36f';

// Put your private key's passphrase here:
$passphrase = 'simtestapp123';
$badgeCnt = '101';
// Put your alert message here:
$message = 'My first push notification!';
$msgID = 'ABCD_'.$badgeCnt;
$emoji = json_decode('"\u263a \ue415\ue056\ue057\ue414\ue405\ue106\ue418 \ud83d\ude04\ud83d\ude0a\ud83d\ude03\ud83d\ude09\ud83d\ude0d\ud83d\ude18"');

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
stream_context_set_option($ctx, 'ssl', 'cafile', 'entrust_2048_ca.cer');

// USE ssl://gateway.sandbox.push.apple.com:2195  for development Profile.
// USE ssl://sandbox.push.apple.com:2195 for ADHOC and Production Profile.
// Open a connection to the APNS server
$fp = stream_socket_client(
	'ssl://gateway.sandbox.push.apple.com:2195', $err,
	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
    //'badge' => $badgeCnt,
      'alert' => 'Hey! You got an offer from shipit.mobi.... <Country|India>'.$emoji,
     'sound' => 'default',
     'category'=>'SIM_categ_02',
     'content-available' => '0'
	);
	
	$body['inData'] = array(
		'inBuilt'=>'false',
		);
	// Special case of silent notification
	$body['msgInfo']   = '3';	
	$body['msgTxt']    = 'Hey! You got an offer from shipit.mobi.... <Country|Brazil>'.$emoji;
	$body['msgFmt']    = '1';
	$body['msgTitle']  = 'Simple Push Messg Title';
	$body['msgSound']  = 'default';
	$body['msgBadge']  = $badgeCnt;
	$body['msgID']     = $msgID;
	$body['msgTyp']    = '0';


// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

echo $result;

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo 'Message successfully delivered' . PHP_EOL;

// Close the connection to the server
//fclose($fp);
