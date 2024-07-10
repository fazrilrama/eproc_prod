<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends App_Controller
{

    var $captcha_sess_key = 'BGR_SECURITY_CAPTCHA';

    function __construct()
    {
        parent::__construct([
            'exclude_login' => ['logout', 'test_email', 'verify_by_email', 'do_register', 'generate_captcha', 'captcha_validate', 'reset_password', 'login', 'register', 'index', 'forgot_password', 'unauthorized', 'request_forgot_password'],
            // 'exclude_menu_check' => [
            //     [
            //         'method' => 'logout'
            //     ]
            // ]
        ]);
        $this->load->model('Auth_model', 'auth');
        $this->load->model('User_model', 'user');
    }

    function index()
    {
        $this->login();
    }

    function test_email()
    {
        $to = "riyansaputrai007@gmail.com";
        $this->load->model('Email_model', 'email_helper');
        $success = $this->email_helper->send_email('Test Email E-Procurment', $to, 'TEST EMAIL', 'Hello its working!');

        echo json_encode(['success' => $success]);
    }

    function login()
    {
        $identity = $this->secure_input($this->input->post('identity'));
        $password = $this->secure_input($this->input->post('password'));

        if ($identity != null && $password != null) {
            $result = $this->auth->validate_login($identity, $password);
            echo json_encode(['success' => $result['success'], 'result' => $result['result']]);
        } else {
            return $this->load->view('pages/auth/login');
        }
    }

    function register()
    {
        $this->load->view('pages/auth/register');
    }

    function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url());
    }

    function forgot_password()
    {
        return $this->load->view('pages/auth/forgot_password');
    }

    function request_forgot_password()
    {
        $identity = $this->secure_input($this->input->post('identity'));
        echo json_encode($this->auth->forgot_password($identity));
    }

    function reset_password()
    {
        $token = $this->secure_input($this->uri->segment(3));
        $token = ($token != null) ? $token : $this->input->post('token');
        $password = $this->secure_input($this->input->post('password'));
        if ($token != null) {
            $verify = $this->auth->reset_password_verify_token($token);
            if ($verify['success']) {
                if ($password != null) {
                    echo json_encode($this->auth->update_password_by_token($token, $password));
                } else {
                    $this->load->view('pages/auth/reset_password');
                }
            } else if ($password == null) {
                echo "<script>
                alert('" . $verify['result'] . "');
                window.location.href='" . site_url('auth') . "';
                </script>";
            } else {
                echo json_encode([
                    'success' => false,
                    'result' => Auth_model::RESET_TOKEN_NOT_VALID
                ]);
            }
        } else {
            redirect('auth/login');
        }
    }

    function unauthorized()
    {
        $this->load->view('pages/auth/unauthorized');
    }

    function current_logged_user()
    {
        $user = $this->session->userdata('user');
        $data = [
            'id_user' => $user['id_user'],
            'name' => $user['name'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role_id' => $user['id_usr_role'],
            'role_name' => $user['role_name'],
            'photo' => $user['photo']
        ];
        echo json_encode($data);
    }

    function generate_captcha()
    {
        // load codeigniter captcha helper
        $this->load->helper('captcha');

        $words = array_merge(range('1', '9'), range('A', 'Z'));
        shuffle($words);
        $max_length = 5;
        $words = substr(implode($words), 0, $max_length);

        $vals = array(
            'word' => $words,
            'img_path'     => './captcha/',
            'img_url'     => base_url() . 'captcha/',
            'img_width'     => 300,
            'img_height'    => 50,
            'expiration'    => 7200,
            'font_size'     => 20,
            'font_path'  => FCPATH . '/assets/fonts/Verdana.ttf',
            'colors'        => array(
                'background' => array(255, 255, 255),
                'border' => array(255, 255, 255),
                'text' => array(0, 0, 0),
                'grid' => array(255, 40, 40)
            )
        );

        // create captcha image
        $cap = create_captcha($vals);

        // store the captcha word in a session
        $this->session->set_userdata($this->captcha_sess_key, $cap['word']);
        echo $cap['image'];
    }

    function captcha_validate()
    {
        $is_valid = false;
        $words = $this->input->post('captcha_words');
        if ($this->session->userdata($this->captcha_sess_key) == $words) {
            $is_valid = true;
        }

        echo json_encode(array('is_valid' => $is_valid));
    }

    function do_register()
    {
        $email = $this->secure_input($this->input->post('email'));
        $password = $this->secure_input($this->input->post('password'));
        $register_as = $this->secure_input($this->input->post('register_as'));

        echo json_encode(
            $this->auth->register([
                'email' => $email,
                'password' => $password,
                'register_as' => $register_as
            ])
        );
    }

    function verify_by_email($token = null)
    {
        if ($token != null) {
            $verify = $this->auth->check_verify_token($token);
            if ($verify['success']) {
                $verify = $this->auth->verify_account_by_email($token);
                $data['success'] = $verify['success'];
                $data['result'] = $verify['result'];
            } else {
                $data['success'] = $verify['success'];
                $data['result'] = $verify['result'];
            }
            $this->load->view('pages/auth/verify_email_success', $data);
        } else {
            redirect('auth');
        }
    }
}
