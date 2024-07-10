<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Email_model extends CI_Model
{
    public function get()
    {
        return $this->db
            ->order_by('id', 'desc')
            ->limit(1)
            ->get(App_Model::TBL_SETTINGS_EMAIL)
            ->row();
    }

    public function edit($id, $params = array())
    {
        return $this->db
            ->where('id', $id)
            ->update(App_Model::TBL_SETTINGS_EMAIL, $params);
    }

    public function send_email($from_name, $to, $subject, $message)
    {
        $email = $this->get($this);
        $config['useragent'] = $this->config->item('app_info')['identity']['name'];
        $config['protocol'] = $email->protocol;
        //$config['mailpath'] = '/usr/sbin/sendmail';
        $config['smtp_host'] = $email->host;
        $config['smtp_user'] = $email->user;
        $config['smtp_pass'] = $email->password;
        $config['smtp_port'] = $email->port;
        $config['smtp_timeout'] = $email->timeout;
        $config['smtp_crypto'] = $email->crypto;
        $config['wordwrap'] = TRUE;
        $config['wrapchars'] = 76;
        $config['mailtype'] = 'html';
        $config['charset'] = 'utf-8';
        $config['validate'] = FALSE;
        $config['priority'] = $email->priority;
        $config['crlf'] = "\r\n";
        $config['newline'] = "\r\n";
        $config['bcc_batch_mode'] = FALSE;
        $config['bcc_batch_size'] = 200;


        $this->email->initialize($config);
        $this->email->from($email->user, $from_name);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->set_newline('\r\n');
        $this->email->message($message);
        $resMailer=$this->email->send();
        //add to log
        $this->db->insert('email_broadcaster',[
                'from'=>$from_name,
                'to'=>$to,
                'subject'=>$subject,
                'body'=>$message,
                'mailer_config'=>json_encode([
                    'host'=>$email->host
                    ,'port'=>$email->port
                    ,'user'=>$email->user
                ],JSON_UNESCAPED_SLASHES),
                'mailer_response'=>json_encode(['res'=>$resMailer],JSON_UNESCAPED_SLASHES)
                ,'executor_id'=>$this->session->userdata('user')!==null?$this->session->userdata('user')['id_user']:0
        ]);
        return $resMailer;
    }
}
