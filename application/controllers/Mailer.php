<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mailer extends App_Controller
{
    public function __construct()
    {
        parent::__construct(
            [
                'exclude_menu_check' => [
                    ['method' => 'index', 'id_role' => [1]],
                    ['method' => 'get_all_vendor', 'id_role' => [1]],
                    ['method' => 'send_email', 'id_role' => [1]],
                ],
                'exclude_login' => ['test_email']
            ]
        );

        $this->load->model('Email_model', 'email_helper');
    }

    public function test_email()
    {
        // $email_target='riyan.cr007@gmail.com';
        // $email_subject='test';
        // $success = $this->email_helper->send_email(
        //         $this->config->item('app_info')['identity']['name'],
        //         $email_target,
        //         $email_subject,
        //         'Test dulu mamang!'
        //     );

        // echo json_encode([
        //     'email_target' => $email_target,
        //     'email_subject' => $email_subject,
        //     'execution_time' => date('Y-m-d H:i:s'),
        //     'success' => $success
        // ]);

        $asyncTask = new AsyncTask();

        $emails = [];
        for ($i = 0; $i < 5; $i++) {

            $emails[$i] = [
                'to' => 'riyan.cr007@gmail.com',
                'from_name' => 'Eproc',
                'subject' => 'Coba coba curl async ' . ($i + 1),
                'message' => 'Coba dlu ajah 1' . ($i + 1)
            ];
        }


        echo json_encode(['success' => $asyncTask->emailer($emails)]);
    }


    public function index()
    {
        $this->load->view('pages/tools/mailer');
    }

    public function get_all_vendor()
    {
        echo json_encode(
            $this->db->select('*')
                ->from('sys_user a')
                ->where("( a.id_usr_role='" . App_Model::ROLE_VENDOR . "' or a.id_usr_role='" . App_Model::ROLE_VENDOR_GROUP . "' or a.id_usr_role='" . App_Model::ROLE_VENDOR_PERSONAL . "')")
                ->where('a.deleted_at is null')
                ->get()
                ->result()
        );
    }

    public function send_email()
    {
        $email_target = $this->input->post('email_target');
        $user = $this->db->where('email', $email_target)->get('sys_user')->row();
        $email_subject = 'Pemberitahuan Vendor E-Procurement ' . $this->config->item('app_info')['identity']['author_name'];
        $success = false;

        if ($user != null) {
            $success = $this->email_helper->send_email(
                $this->config->item('app_info')['identity']['name'],
                $email_target,
                $email_subject,
                '<center>
                <img src="https://bgrlogistik.id/bgr/img/bgr_logo.png" width="200px">
                <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                <hr>
                <p> Terima kasih telah menjadi Vendor Rekanan di ' . $this->config->item('app_info')['identity']['author_name']
                    . '. Perkenankan Kami memperkenalkan sistem baru kami yaitu E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                    . ' yang telah tersedia pada link berikut; ' . site_url() . '. Email ini dikirim secara otomatis oleh sistem karena Anda telah terdaftar,
                    berikut akun yang dapat Anda gunakan untuk login kedalam sistem. Silahkan merubah password ketika berhasil masuk kedalam sistem.
                <br>
                <br>
                Username/email: ' . $user->email . '
                <br>
                Password: P4ssw0rd
                <br>
                Terima kasih.
                <br>
                <br>
                <b>Regards,
                <br><a href="' . site_url() . '">E-Procurement System</a>
                <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                </b>
                </p>'
            );
        }

        echo json_encode([
            'email_target' => $email_target,
            'email_subject' => $email_subject,
            'execution_time' => date('Y-m-d H:i:s'),
            'success' => $success
        ]);
    }
}
