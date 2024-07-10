<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 */

function add_notification($from, $to_user, $to_role, $title, $description, $link_type, $link_on_click, $send_to_email = false)
{
    $CI = &get_instance();

    $asyncTask = new AsyncTask();

    //if to user_role
    if ($to_role != null) {
        $user = $CI->db->where('id_usr_role', $to_role)
            ->where('deleted_at is null')
            ->where('id_usr_status!=4 and id_usr_status!=3')
            ->get('sys_user')
            ->result();

        $emails = [];

        $CI->db->query("call sendNotificationByRole(?,?,?,?,?,?)",[
            $to_role,
            $CI->session->userdata('user')['id_company_owner'],
            $title,
            $description,
            $link_type,
            $link_on_click
        ]);

        foreach ($user as $u) {
            // $CI->db->insert('tbl_notification', [
            //     'from' => $from,
            //     'to_user' => $u->id_user,
            //     'to_role' => null,
            //     'title' => $title,
            //     'description' => $description,
            //     'link_type' => $link_type,
            //     'link_on_click' => $link_on_click
            // ]);
            if ($send_to_email) {
                $to = ENVIRONMENT==='production'? $u->email : 'riyan.cr007@gmail.com';
                $subject = 'Pemberitahuan ' . $CI->config->item('app_info')['identity']['author_name'];
                $from = $CI->config->item('app_info')['identity']['name'];
                $msg = '<center>
                <img src="https://www.bgrlogistics.id/bgr/img/bgr_logo.png" width="200px">
                <br>' . $CI->config->item('app_info')['identity']['name'] . '</center>
                <hr>
                <h6>Prihal: ' . $title . '</h6>
                <br>
                <p>
                    ' . $description . '
                </p>
                <br>
                <br>
                <b>Regards,
                <br><a href="' . site_url() . '">'.$CI->config->item('app_info')['identity']['name'].'</a>
                <br>' . $CI->config->item('app_info')['identity']['author_name'] . '
                </b>
                </p>';

                $emails[0] = [
                    'from_name' => $from,
                    'to' => $to,
                    'subject' => $subject,
                    'message' => $msg,
                ];
            }
        }
        if ($send_to_email) $asyncTask->emailer($emails);
        return true;
    } else {
        $CI->db->insert('tbl_notification', [
            'from' => $from,
            'to_user' => $to_user,
            'to_role' => $to_role,
            'title' => $title,
            'description' => $description,
            'link_type' => $link_type,
            'link_on_click' => $link_on_click
        ]);
        if ($send_to_email) {
            $user = $CI->db->where('id_user', $to_user)
                ->where('deleted_at is null')
                ->where('id_usr_status!=4 and id_usr_status!=3')
                ->get('sys_user')
                ->row();

            if ($user != null) {
                $emails = [];
                // $to = $user->email;
                $to = ENVIRONMENT==='production'? $user->email : 'riyan.cr007@gmail.com';
                $subject = 'Pemberitahuan ' . $CI->config->item('app_info')['identity']['author_name'];
                $from = $CI->config->item('app_info')['identity']['name'];
                $msg = '<center>
                    <img src="https://www.bgrlogistics.id/bgr/img/bgr_logo.png" width="200px">
                    <br>' . $CI->config->item('app_info')['identity']['name'] . '</center>
                    <hr>
                    <h6>Prihal: ' . $title . '</h6>
                    <br>
                    <p>
                        ' . $description . '
                    </p>
                    <br>
                    <br>
                    <b>Regards,
                    <br><a href="' . site_url() . '">'.$CI->config->item('app_info')['identity']['name'].'</a>
                    <br>' . $CI->config->item('app_info')['identity']['author_name'] . '
                    </b>
                    </p>';

                $emails[0] = [
                    'from_name' => $from,
                    'to' => $to,
                    'subject' => $subject,
                    'message' => $msg,
                ];
                $asyncTask->emailer($emails);
            }
        }
        return true;
    }
}
