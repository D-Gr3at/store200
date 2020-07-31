<?php
function hmac_sign($message, $key)
{
    return hash_hmac('sha256', $message, $key) . $message;
}
function hmac_verify($bundle, $key)
{
    $msgMAC = mb_substr($bundle, 0, 64, '8bit');
    $message = mb_substr($bundle, 64, null, '8bit');
    return hash_compare(hash_hmac('sha256', $message, $key),$msgMAC);
}
//
//echo $ee = hmac_sign("123456", "me@gmail.com");
//echo "<br>".hmac_verify($ee, "me@gmail.com");

$MAC = exec('getmac'); 
  
// Storing 'getmac' value in $MAC 
$MAC = strtok($MAC, ' '); 
echo "MAC address of client is: $MAC"; 
?>
