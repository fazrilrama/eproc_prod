<?php

class Async_task extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_model', 'email_task');
    }

    public function send_email()
    {
        $from_name = $this->input->post('from_name');
        $to = $this->input->post('to');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $return = false;
        if ($from_name != null && $to != null) {
            $return = $this->email_task->send_email($from_name, $to, $subject, $message);
        }

        echo json_encode(array('success' => $return));
    }
}
