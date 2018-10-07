<?php
require_once ("OAuth.php");
require_once ('config.php');
// config.php sets all URLs and keys
//We were passed these through the callback.
$token = $_REQUEST['oauth_token'];
$token_secret = $_REQUEST['oauth_verifier'];

echo 'fitbitCallBack -<br />token = '.$token.'<br /> token_secret = '.$token_secret.'<br />';

$consumer = new OAuthConsumer($key, $secret, NULL);
$auth_token = new OAuthConsumer($token, $token_secret);
$access_token_req = new OAuthRequest("GET", $oauth_access_token_endpoint);
$access_token_req = $access_token_req->from_consumer_and_token($test_consumer,
                $auth_token, "GET", $oauth_access_token_endpoint);
echo "OAuthConsumer (consumer) - ".$consumer."<br />";
echo "OAuthToken - auth_token - ".$auth_token."<br />";
echo "OAuthRequest (access_token_req - unsigned) - ".$access_token_req."<br />";

$access_token_req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),$consumer,
                $auth_token);
echo "OAuthRequest (access_token_req - signed) - ".$access_token_req.'<br>';

$after_access_request = doHttpRequest($access_token_req->to_url());

echo "After_access_req - ".$after_access_request.'<br>';

parse_str($after_access_request,$access_tokens);

$access_token = new OAuthConsumer($access_tokens['oauth_token'], $access_tokens['oauth_token_secret']);

// we should now have all the autorisation required - lets get some data

$streamkey_req = $access_token_req->from_consumer_and_token($consumer,
                $access_token, "GET", $access_token_url);

echo "Streamkey_req $streamkey_req <br>";

$streamkey_req->sign_request(new OAuthSignatureMethod_HMAC_SHA1(),$consumer,$access_token);

echo "StreamKey_req - (signed)".$streamkey_req.'<br>';
$after_request = doHttpRequest($streamkey_req->to_url());
echo 'After request'.$after_request.' <br>';
echo '<br>';
//Get streamkey from returned XML
$stream_key =  "";//parseStream_KeyFromXML ($after_request);

if ($stream_key == '') {
    echo ("Error getting stream_key from API!");
} else {   //We got the key! Embed the broadcaster and we're done.
    ?>
    <html>
        <h1>THIS IS SUCCESFUL RETURN FROM FITBIT</h1>
    </html>
    <?php
}
    ?>
    <html>
        <h1>THIS IS Final RETURN FROM FITBIT</h1>
        <form> <input type="button" id = "holdbutton" value="submit" /></form>
    </html>
    <?php
//helper function to get the stream_key from returned XML
function parseStream_KeyFromXML($xml)
{
    $xml_parser = xml_parser_create();

    xml_parse_into_struct($xml_parser, $xml, $vals, $index);
    xml_parser_free($xml_parser);

    if ($vals[1]['tag'] == "STREAM_KEY") {
        return $vals[1]['value'];
    } else {
        return '';
    }
}
