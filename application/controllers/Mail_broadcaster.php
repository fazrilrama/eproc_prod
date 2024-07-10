<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Mail_broadcaster extends App_Controller
{
    function __construct()
    {
        parent::__construct([
            'exclude_login' => ['reminder_vendor_update'],
        ]);
        $this->load->model('mail_broadcaster_model','broadcast');
        $this->load->model('Email_model', 'email_helper');
    }

    function index(){
        $this->set_page_title('pe-7s-mail', 'Email Broadcaster', [
            [
                'icon' => '<i class="fa fa-dashboard"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '',
                'active' => false,
                'label' => ' Log'
            ]
        ]);
        $this->load->view('/pages/mail_broadcaster/list');
    }

    function reminder_vendor_update(){
        if(ENVIRONMENT=='development'){
            $this->db->where('created_at>=','2021-08-10 00:00:00');
        }
        $dataVendor=$this->db
        ->where('id_usr_status',2)
        ->where('(id_usr_role=2 or id_usr_role=6 or id_usr_role=7)')
        ->where('id_company_owner',1)
        ->where('email is not null')
        ->where("lower(email) like '%@%'")
        ->get('sys_user')
        ->result();
        if(ENVIRONMENT=='development'){
            $dataVendor[]=(object)[
                'name'=>'CV.Ryan IT Consultant',
                'email'=>'riyansaputrai007@gmail.com'
            ];
        }
        foreach($dataVendor as $d){
            $data['user']=$d;
            $msg=$this->load->view('/pages/mail_broadcaster/template_email_reminder',$data,true);
            $this->email_helper->send_email(
                $this->config->item('app_info')['identity']['name'],
                $d->email,
                'Pengingat Pembaruan Legalitas'
                ,$msg
            );
        }
        echo json_encode(count($dataVendor));
    }
}