<?php
function curlProcessor($url = null, $method = "GET", $header = [], $return_type = "raw", $data = [], $sendDataModifier = null)
{

    //Global Curl Log
    global $RNI_API_CURL_RESPONSE;

    $exec_time = date('Y:m:d H:i:s');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_VERBOSE, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_ENCODING, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

    if (strtolower($method) == "post" || strtolower($method) == "put") {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, (($sendDataModifier != null) ? $sendDataModifier($data) : $data));
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    // Retudn headers seperatly from the Response Body
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $headers = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);
    $headers = explode("\r\n", $headers); // The seperator used in the Response Header is CRLF (Aka. \r\n) 
    $headers = array_filter($headers);

    $res_time = date('Y:m:d H:i:s');
    $res_data = null;
    switch ($return_type) {
        case 'array':
            $res_data = ['execTime' => $exec_time, 'resTime' => $res_time, 'urlEndPoint' => $url, 'method' => $method, 'requestHeader' => $header, 'requestBody' => $data, 'responseHeader' => $headers, 'responseBody' => json_decode($body, FALSE)];
            break;
        case 'object':
            $res_data = ['execTime' => $exec_time, 'resTime' => $res_time, 'urlEndPoint' => $url, 'method' => $method, 'requestHeader' => $header, 'requestBody' => $data, 'responseHeader' => $headers, 'responseBody' => json_decode($body, TRUE)];
            break;
        default:
            $res_data = ['execTime' => $exec_time, 'resTime' => $res_time, 'urlEndPoint' => $url, 'method' => $method, 'requestHeader' => $header, 'requestBody' => $data, 'responseHeader' => $headers, 'responseBody' => $body];
            break;
    }
    $RNI_API_CURL_RESPONSE = $res_data;
    if ($GLOBALS['RNI_API_CURL_LOG_HANDLER'] != null && !empty($GLOBALS['RNI_API_CURL_LOG_HANDLER'])) $GLOBALS['RNI_API_CURL_LOG_HANDLER']($res_data);
    return $res_data;
}
