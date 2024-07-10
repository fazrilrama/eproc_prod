<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth_model extends App_Model
{
    const MSG_LOGIN_SUCCESS = 'Login success!';
    const MSG_LOGIN_FAILED = 'Email or password is wrong!';
    const MSG_LOGIN_FAIL_ATTEMPT = ', your remaining login attempt is ';
    const MSG_LOGIN_BLOCKED = 'Your account has been blocked, unblock your account <a target="_blank" style="font-size:12pt" href="https://helpdesk.bgrlogistics.id/" style="cursor:pointer;text-decoration:underline;">here<a>!';
    const MSG_LOGIN_INACTIVE = 'Your account wasn\'t found or registered!';
    const MSG_FORGOT_PASS_SUCCESS = 'Reset password confirmation sended to';
    const MSG_FORGOT_PASS_FAIL = 'Reset password confirmation failed to send';
    const EMAIL_SUBJECT_CHANGE_PASS = 'CHANGE PASSWORD';
    const EMAIL_SUBJECT_REGISTRATION_VERIFY = 'VERIFIKASI EMAIL';
    const RESET_TOKEN_NOT_VALID = "Token is no longer valid!";
    const RESET_TOKEN_VALID = "Token is valid!";
    const RESET_PASSWORD_SUCCESS = "Reset password success!";
    const VERIFY_EMAIL_SUCCESS = "Email berhasil terverifikasi, Terima Kasih!.";

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_model', 'email_helper');
    }

    function validate_login_sso($identity)
    {
        $result = [
            'success' => false,
            'result' => self::MSG_LOGIN_INACTIVE
        ];
        $max_attempt = get_settings()->login_attempt;

        $user = $this->db
            ->where(self::FIELD_IDENTITY, $identity)
            ->where(App_Model::FIELD_DELETED_AT . App_Model::SQL_IS_NULL)
            ->get(App_Model::TBL_USER)
            ->row();

        if ($user != null) {
            $attempt_remaining = $max_attempt - $user->login_attempt;

            if ($attempt_remaining > 0) {
                //check user status
                if (
                    $user->id_usr_status == App_Model::STAT_ACCOUNT_ACTIVE
                    || $user->id_usr_status == App_Model::STAT_ACCOUNT_VERIFY_PROFILE
                    || $user->id_usr_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE
                ) {

                    //reset login attempt
                    $this->db->where('id_user', $user->id_user)
                        ->update(App_Model::TBL_USER, [
                            // 'id_usr_status' => App_Model::STAT_ACCOUNT_ACTIVE,
                            'login_attempt' => 0
                        ]);

                    // insert into session
                    $user_data = [];
                    $exception_user_data = ['password'];
                    foreach ($user as $key => $val) {
                        $is_not_exception = true;
                        for ($i = 0; $i < count($exception_user_data); $i++) {
                            if ($key == $exception_user_data[$i]) {
                                $is_not_exception = false;
                                break;
                            }
                        }
                        if ($is_not_exception) {
                            $user_data[$key] = $val;
                        }
                    }
                    $user_role = $this->db->where('id_usr_role', $user->id_usr_role)
                        ->get(App_Model::TBL_USR_ROLE)->row();
                    $user_data['role_name'] = $user_role->role_name;

                    $session_data = [
                        App_Model::SESS_KEY_LOGIN => true,
                        App_Model::SESS_KEY_USER_DATA => $user_data
                    ];
                    $this->session->set_userdata($session_data);

                    $result = [
                        'success' => true,
                        'result' => self::MSG_LOGIN_SUCCESS,
                        'user' => $user
                    ];
                } else {

                    if ($user->id_usr_status == App_Model::STAT_ACCOUNT_VERIFY) {
                        $result = [
                            'success' => false,
                            'result' => self::MSG_LOGIN_INACTIVE
                        ];
                    } else if ($user->id_usr_status == App_Model::STAT_ACCOUNT_BLOCKED) {
                        $result = [
                            'success' => false,
                            'result' => self::MSG_LOGIN_BLOCKED
                        ];
                    }
                }
            } else {
                $result = [
                    'success' => false,
                    'result' => self::MSG_LOGIN_BLOCKED
                ];
            }
            $this->login_log($identity, $result['success'], $user->id_usr_status);
        }


        return $result;
    }

    function validate_login($identity, $password)
    {
        $result = [
            'success' => false,
            'result' => self::MSG_LOGIN_INACTIVE
        ];
        $max_attempt = get_settings()->login_attempt;

        $user = $this->db
            ->where(self::FIELD_IDENTITY, $identity)
            ->where(App_Model::FIELD_DELETED_AT . App_Model::SQL_IS_NULL)
            ->get(App_Model::TBL_USER)
            ->row();

        if ($user != null) {
            $attempt_remaining = $max_attempt - $user->login_attempt;

            if ($attempt_remaining > 0) {
                if (password_verify($password, $user->password)) {
                    //check user status
                    if (
                        $user->id_usr_status == App_Model::STAT_ACCOUNT_ACTIVE
                        || $user->id_usr_status == App_Model::STAT_ACCOUNT_VERIFY_PROFILE
                        || $user->id_usr_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE
                    ) {

                        //reset login attempt
                        $this->db->where('id_user', $user->id_user)
                            ->update(App_Model::TBL_USER, [
                                // 'id_usr_status' => App_Model::STAT_ACCOUNT_ACTIVE,
                                'login_attempt' => 0
                            ]);

                        // insert into session
                        $user_data = [];
                        $exception_user_data = ['password'];
                        foreach ($user as $key => $val) {
                            $is_not_exception = true;
                            for ($i = 0; $i < count($exception_user_data); $i++) {
                                if ($key == $exception_user_data[$i]) {
                                    $is_not_exception = false;
                                    break;
                                }
                            }
                            if ($is_not_exception) {
                                $user_data[$key] = $val;
                            }
                        }
                        $user_role = $this->db->where('id_usr_role', $user->id_usr_role)
                            ->get(App_Model::TBL_USR_ROLE)->row();
                        $user_data['role_name'] = $user_role->role_name;

                        $session_data = [
                            App_Model::SESS_KEY_LOGIN => true,
                            App_Model::SESS_KEY_USER_DATA => $user_data
                        ];
                        $this->session->set_userdata($session_data);

                        $result = [
                            'success' => true,
                            'result' => self::MSG_LOGIN_SUCCESS,
                            'user' => $user
                        ];
                    } else {

                        if ($user->id_usr_status == App_Model::STAT_ACCOUNT_VERIFY) {
                            $result = [
                                'success' => false,
                                'result' => self::MSG_LOGIN_INACTIVE
                            ];
                        } else if ($user->id_usr_status == App_Model::STAT_ACCOUNT_BLOCKED) {
                            $result = [
                                'success' => false,
                                'result' => self::MSG_LOGIN_BLOCKED
                            ];
                        }
                    }
                } else {
                    // start counting fail attempt
                    $update = $this->db->where('id_user', $user->id_user)
                        ->update(App_Model::TBL_USER, [
                            'login_attempt' => ($user->login_attempt += 1)
                        ]);
                    if ($update) {
                        if (($attempt_remaining - 1) == 0) {
                            $this->db->where('id_user', $user->id_user)
                                ->update(App_Model::TBL_USER, [
                                    'id_usr_status' => App_Model::STAT_ACCOUNT_BLOCKED
                                ]);
                            $result = [
                                'success' => false,
                                'result' => self::MSG_LOGIN_BLOCKED
                            ];
                        } else {
                            $result = [
                                'success' => false,
                                'result' => self::MSG_LOGIN_FAILED . self::MSG_LOGIN_FAIL_ATTEMPT . ($attempt_remaining - 1)
                            ];
                        }
                    } else {

                        $result = [
                            'success' => false,
                            'result' => self::MSG_LOGIN_FAILED
                        ];
                    }
                }
            } else {
                $result = [
                    'success' => false,
                    'result' => self::MSG_LOGIN_BLOCKED
                ];
            }
            $this->login_log($identity, $result['success'], $user->id_usr_status);
        }


        return $result;
    }

    private function login_log($identity, $status, $id_usr_status = 0)
    {
        if (get_settings()->login_log_enabled) {
            $client = App_Controller::get_client_data($this);
            return $this->db->insert(App_Model::TBL_LOGIN_SESSION, [
                'identity' => $identity,
                'status' => $status,
                'ip' => $client['ip'],
                'browser' => $client['browser'],
                'browser_version' => $client['browser_version'],
                'platform' => $client['platform'],
                'user_agent' => $client['user_agent'],
                'id_usr_status' => $id_usr_status
            ]);
        }
    }

    public function forgot_password($identity)
    {
        $result = ['success' => false, 'result' => self::MSG_LOGIN_INACTIVE];

        $user = $this->db->where(self::FIELD_IDENTITY, $identity)
            ->get(App_Model::TBL_USER)->row();

        if ($user != null) {
            //check status
            if ($user->id_usr_status != App_Model::STAT_ACCOUNT_ACTIVE) {
                $result = [
                    'success' => false,
                    'result' => ($user->id_usr_status == App_Model::STAT_ACCOUNT_BLOCKED) ? self::MSG_LOGIN_BLOCKED : self::MSG_LOGIN_INACTIVE
                ];
            } else {
                $password_change = $this->password_change($identity, $user->password, true);
                if ($password_change['success']) {

                    if ($password_change['result']['status']) {
                        $this->db->where(self::FIELD_IDENTITY, $identity)
                            ->update(App_Model::TBL_USER, [
                                'password' => $password_change['result']['new_password'],
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                    }
                    $result = [
                        'success' => true,
                        'result' => self::MSG_FORGOT_PASS_SUCCESS . ' ' . $identity
                    ];
                } else {
                    $result = [
                        'success' => false,
                        'result' => self::MSG_FORGOT_PASS_FAIL
                    ];
                }
            }
        }

        return $result;
    }


    public function password_change($identity, $old_pass, $is_auto_generate = true, $defined_generate = null)
    {
        $client = App_Controller::get_client_data($this);
        $result = [
            'success' => false,
            'result' => null
        ];
        if ($identity != null && $old_pass != null) {
            if ($is_auto_generate) {
                $plain_pass = ($defined_generate != null) ? $defined_generate : $this->generate_password();
                $new_pass = password_hash($plain_pass, PASSWORD_DEFAULT);
                $this->db->insert(App_Model::TBL_PASSWORD_CHANGE, [
                    'identity' => $identity,
                    'token' => sha1($identity . 'password_change' . date('Y-m-d H:i:s')),
                    'old_password' => $old_pass,
                    'new_password' => $new_pass,
                    'plain_password_generate' => $plain_pass,
                    'status' => true,
                    'ip' => $client['ip'],
                    'browser' => $client['browser'],
                    'browser_version' => $client['browser_version'],
                    'platform' => $client['platform'],
                    'user_agent' => $client['user_agent']
                ]);


                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $identity,
                    self::EMAIL_SUBJECT_CHANGE_PASS,
                    '<center> 
                    <img src="https://bgrlogistics.id/bgr/img/bgr_logo.png" width="200px">
                    <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                    <hr>
                    <p> Password change request has been made, this system is generating new password for your account. Below is your account detail :
                        <br><br><b>Email    :</b>' . $identity . '
                        <br><b>Password :</b>' . $plain_pass . '
                        <br><br>
                        Please login into your account, and change this generated password.
                    </p>'
                );

                $result = [
                    'success' => true,
                    'result' => [
                        'identity' => $identity,
                        'token' => sha1($identity . 'password_change' . date('Y-m-d H:i:s')),
                        'old_password' => $old_pass,
                        'new_password' => $new_pass,
                        'plain_password_generate' => $plain_pass,
                        'status' => true,
                    ]
                ];
            } else {
                $token = sha1($identity . 'password_change' . date('Y-m-d H:i:s'));
                $this->db->insert(App_Model::TBL_PASSWORD_CHANGE, [
                    'identity' => $identity,
                    'token' => $token,
                    'token_lifetime' => date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s') . ' +' . get_settings()->token_reset_pass_lifetime . ' hours')),
                    'old_password' => $old_pass,
                    'status' => false,
                    'ip' => $client['ip'],
                    'browser' => $client['browser'],
                    'browser_version' => $client['browser_version'],
                    'platform' => $client['platform'],
                    'user_agent' => $client['user_agent']
                ]);

                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $identity,
                    self::EMAIL_SUBJECT_CHANGE_PASS,
                    '<center>
                    <img src="https://bgrlogistics.id/bgr/img/bgr_logo.png" width="200px">
                    <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                    <hr>
                    <p> Password change request has been made, click link below to change your password
                        <br><br>
                        <a href="' . site_url('auth/reset_password/') . $token . '" style="">Change Password Here</a>
                    </p>'
                );

                $result = [
                    'success' => true,
                    'result' => [
                        'identity' => $identity,
                        'token' => sha1($identity . 'password_change' . date('Y-m-d H:i:s')),
                        'old_password' => $old_pass,
                        'status' => false,
                    ]
                ];
            }
        }

        return $result;
    }

    public function reset_password_verify_token($token)
    {
        $result = [
            'success' => false,
            'result' => self::RESET_TOKEN_NOT_VALID
        ];

        if ($token != null) {
            $change_pass_data = $this->db->where('token', $token)
                ->where("token_lifetime >= '" . date('Y-m-d H:i:s') . "'")
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_PASSWORD_CHANGE)->result_array();

            if (count($change_pass_data) >= 1) {
                $result = [
                    'success' => true,
                    'result' => $change_pass_data[0]
                ];
            }
        }

        return $result;
    }

    public function check_verify_token($token)
    {
        $result = [
            'success' => false,
            'result' => self::RESET_TOKEN_NOT_VALID
        ];

        if ($token != null) {
            $change_pass_data = $this->db->where('token', $token)
                ->where("token_lifetime >= '" . date('Y-m-d H:i:s') . "'")
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_USR_VERIFICATION)->result_array();

            if (count($change_pass_data) >= 1) {
                $result = [
                    'success' => true,
                    'result' => $change_pass_data[0]
                ];
            }
        }

        return $result;
    }

    public function update_password_by_token($token, $password)
    {
        $result = [
            'success' => false,
            'result' => self::RESET_TOKEN_NOT_VALID
        ];

        $token_data = $this->db->where('token', $token)
            ->get(App_Model::TBL_PASSWORD_CHANGE)->row();
        if ($token_data != null) {

            $user = $this->db->where(App_Model::FIELD_IDENTITY, $token_data->identity)
                ->get(App_Model::TBL_USER)->row();

            if ($user != null) {
                $new_pass = password_hash($password, PASSWORD_DEFAULT);
                $this->db->where('id_user', $user->id_user)
                    ->update(App_Model::TBL_USER, [
                        'password' => $new_pass,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $this->db->where('token', $token)
                    ->update(App_Model::TBL_PASSWORD_CHANGE, [
                        'token_lifetime' => date('Y-m-d H:i:s'),
                        'new_password' => $new_pass,
                        'status' => 1
                    ]);

                $result = [
                    'success' => true,
                    'result' => self::RESET_PASSWORD_SUCCESS
                ];
            }
        }

        return $result;
    }

    public function verify_account_by_email($token)
    {
        $result = [
            'success' => false,
            'result' => self::RESET_TOKEN_NOT_VALID
        ];

        $token_data = $this->db->where('token', $token)
            ->get(App_Model::TBL_USR_VERIFICATION)->row();
        if ($token_data != null) {

            $user = $this->db->where('id_user', $token_data->id_user)
                ->get(App_Model::TBL_USER)->row();

            if ($user != null) {

                $this->db->where('id_user', $user->id_user)
                    ->update(App_Model::TBL_USER, [
                        'id_usr_status' => App_Model::STAT_ACCOUNT_VERIFY_PROFILE,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                $this->db->where('token', $token)
                    ->update(App_Model::TBL_USR_VERIFICATION, [
                        'token_lifetime' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'status' => 1
                    ]);

                $result = [
                    'success' => true,
                    'result' => self::VERIFY_EMAIL_SUCCESS
                ];
            }
        }

        return $result;
    }


    public static function generate_password($length = 12)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+=~';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }

    public function register($params)
    {
        $result = [
            'success' => false,
            'result' => 'Register tidak berhasil, silahkan coba lagi.'
        ];

        //check if email duplicate

        $user = $this->db
            ->where(self::FIELD_IDENTITY, $params['email'])
            ->where(App_Model::FIELD_DELETED_AT . App_Model::SQL_IS_NULL)
            ->get(App_Model::TBL_USER)
            ->num_rows();
        if ($user > 0) {
            $result['success'] = false;
            $result['result'] = "Register gagal, email sudah terdaftar, silahkan ganti email dan coba lagi!.";
        } else {

            $data_user = [
                // 'id_usr_status' => App_Model::STAT_ACCOUNT_VERIFY,
                'id_usr_status' => 5,
                'id_usr_role' => $params['register_as'],
                'name' => $params['email'],
                'email' => $params['email'],
                'username' => $params['email'],
                'password' => password_hash($params['password'], PASSWORD_DEFAULT)
            ];

            $insert_user = $this->db->insert(App_Model::TBL_USER, $data_user);
            if ($insert_user) {
                $user = $this->db->order_by('created_at', 'desc')->get(App_Model::TBL_USER)->row();
                if ($user != null) {

                    $token = sha1($user->id_user . 'verify' . date('Y-m-d H:i:s'));
                    $data_verify = [
                        'id_user' => $user->id_user,
                        'status' => 0,
                        'token' => $token,
                        'token_lifetime' => date("Y-m-d H:i:s", strtotime(date('Y-m-d H:i:s') . ' +' . get_settings()->token_verify_lifetime . ' hours')),
                    ];

                    $insert_verify = $this->db->insert(App_Model::TBL_USR_VERIFICATION, $data_verify);
                    if ($insert_verify) {

                        $user_role = $this->db->where('id_usr_role', $user->id_usr_role)->get(App_Model::TBL_USR_ROLE)->row();

                        $this->email_helper->send_email(
                            $this->config->item('app_info')['identity']['name'],
                            $user->email,
                            self::EMAIL_SUBJECT_REGISTRATION_VERIFY,
                            '<center>
                        <img src="https://www.bgrlogistics.id/bgr/img/bgr_logo.png" width="200px">
                        <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                        <hr>
                        <p> Terima kasih telah mendaftar sebagai ' . $user_role->role_name . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                                . ', silahkan verifikasi email Anda dengan mengklik link dibawah dan segera lengkapi data Anda untuk menggunakan sistem secara penuh.
                        Email verifikasi ini hanya bisa digunakan ' . get_settings()->token_verify_lifetime . ' jam setelah email pemberitahuan ini terkirim. Akun Anda akan
                        dihapus otomatis dari sistem jika dalam jangka waktu verifikasi tidak melakukan verifikasi.
                        <br>
                        Terima kasih.
                        <br>
                        <br>
                        <center><a href="' . site_url('auth/verify_by_email/') . $token . '" style=" background-color: #4CAF50; /* Green */
                        border: none;
                        color: white;
                        padding: 15px 32px;
                        text-align: center;
                        text-decoration: none;
                        display: inline-block;
                        font-size: 16px;">Verifikasi Email</a></center>
                        <br>
                        <br>
                        <b>Regards,
                        <br>E-Procurement System
                        <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                        </b>
                        </p>'
                        );

                        $result = [
                            'success' => true,
                            'result' => 'Register berhasil, silahkan login.'
                            // 'result' => 'Register berhasil, silahkan cek email Anda.'
                        ];
                    }
                }
            }
        }

        return $result;
    }
}
