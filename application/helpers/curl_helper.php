<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 */

if (!function_exists('curl_json_get')) {
    function curl_json_get($url, $header = [])
    {
        //  Initiate curl
        $ch = curl_init();
        // Header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        // Will dump a beauty json :3
        return json_decode($result, true);
    }
}

if (!function_exists('curl_post')) {
    function curl_post($url, $header = [], $post_data)
    {
        //  Initiate curl
        $ch = curl_init();
        // Header
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);

        // Will dump a beauty json :3
        return json_decode($result, true);
    }
}


function RNI_API_WriteCurlLog($logData)
{
    $url = $logData['urlEndPoint'];
    $method = $logData['method'];
    $reqHeader = $logData['requestHeader'];
    $reqBody = $logData['requestBody'];
    $resHeader = $logData['responseHeader'];
    $resBody = $logData['responseBody'];
    $ci = &get_instance();

    $ci->load->library('user_agent');
    if ($ci->agent->is_browser()) {
        $agent = $ci->agent->browser() . ' ' . $ci->agent->version();
    } elseif ($ci->agent->is_robot()) {
        $agent = $ci->agent->robot();
    } elseif ($ci->agent->is_mobile()) {
        $agent = $ci->agent->mobile();
    } else {
        $agent = 'Unidentified User Agent';
    }
    $agent_platform = $ci->agent->platform() != null ? $ci->agent->platform() : 'Undefined Platform';
    $agent_str = $ci->agent->agent_string();

    $client_ip_address = $ci->input->ip_address();
    $executor_id = ($ci->session->userdata('ID') != null ? $ci->session->userdata('user')['id_user'] : 0);
    $log_data = [
        'app_url' => current_url(),
        'app_http_method' => $ci->input->method(FALSE),
        'request_url' => $url,
        'request_http_method' => $method,
        'request_header' => json_encode($reqHeader, JSON_UNESCAPED_SLASHES),
        'request_body' => json_encode($reqBody, JSON_UNESCAPED_SLASHES),
        'response_header' => json_encode($resHeader, JSON_UNESCAPED_SLASHES),
        'response_body' => json_encode($resBody, JSON_UNESCAPED_SLASHES),
        'ip' => $client_ip_address,
        'executor_id' => $executor_id,
        'user_agent_type' => $agent,
        'user_agent_platform' => $agent_platform,
        'user_agent_str' => $agent_str,
        'timezone' => date_default_timezone_get()
    ];

    $ci->db->insert('api_log', $log_data);
}