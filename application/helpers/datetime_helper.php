<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 */


if (!function_exists('current_datetime')) {
    function current_datetime($params = array())
    {
        $timezone = isset($params['timezone']) ? $params['timezone'] : "Asia/Jakarta";
        $format = isset($params['format']) ? $params['format'] : "Y-m-d H:i:s";

        $dt = new DateTime("now", new DateTimeZone($timezone));
        return $dt->format($format);
    }
}
