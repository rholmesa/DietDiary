<?php

$key = '4041b6386e714492a1507fdfb11b6dd7';//'<your app's API key>';
$secret = '658615947cc848a7b2a8a62b186db765';//'<your app's secret>';

$base_url = "http://rwgholmes.com";
$request_token_endpoint = 'http://api.fitbit.com/oauth/request_token';
$authorize_endpoint = 'http://www.fitbit.com/oauth/authorize';
$access_token_url = 'http://api.fitbit.com/oauth/access_token';


function doHttpRequest($urlreq)
{
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, "$urlreq");
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

// grab URL and pass it to the browser
$request_result = curl_exec($ch);

// close cURL resource, and free up system resources
curl_close($ch);

return $request_result;
}

