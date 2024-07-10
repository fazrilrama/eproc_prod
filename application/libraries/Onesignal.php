<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Onesignal
{
    var $content = [];
    var $web_buttons = [];
    var $data = [];
    var $to_segments = [];
    var $to_player_ids = [];
    var $url = '';
    const config = [
        'app_id' => '8cfc4bab-d7ef-4c3a-8110-f653dd1252a6',
        'api' => [
            'send_notification' =>
            [
                'url' => 'https://onesignal.com/api/v1/notifications',
                'header' => [
                    'Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic NmU5MGQ3MDQtMWQ5Ni00N2NhLWFhZTYtOTQ5ODkzMzBmZDQ4'
                ],
                'method' => 'POST',
                'return_type' => 'JSON'
            ]
        ]
    ];

    function setContent($content = [])
    {
        $this->content = $content;
        return $this;
    }

    function setWebButtons($buttons = [])
    {
        $this->web_buttons = $buttons;
        return $this;
    }

    function setData($data = [])
    {
        $this->data = $data;
        return $this;
    }

    function setToSegments($segments = [])
    {
        $this->to_segments = $segments;
        return $this;
    }

    function setToPlayerIds($ids = [])
    {
        $this->to_player_ids = $ids;
        return $this;
    }

    function setClickUrl($url = '')
    {
        $this->url = $url;
        return $this;
    }


    function sendNotif()
    {
        $fields = array(
            'app_id' => self::config['app_id'],
            'included_segments' => $this->to_segments,
            'include_player_ids' => $this->to_player_ids,
            'data' => $this->data,
            'contents' => $this->content,
            'web_buttons' => $this->web_buttons,
            'url' => $this->url
        );

        $fields = json_encode($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::config['api']['send_notification']['url']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::config['api']['send_notification']['header']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    function getJson($response)
    {
        $return = [];
        $return["allresponses"] = $response;
        $return = json_encode($return);
        return $return;
    }

    function getArray($response)
    {
        $return = [];
        $return["allresponses"] = $response;
        $return = json_encode($return);
        $data = json_decode($response, true);
        return $data;
    }
}
