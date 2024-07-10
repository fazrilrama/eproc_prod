<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SSO extends CI_Controller
{
    public function __construct()
	{
		parent::__construct();
        $this->load->model('Auth_model', 'auth');
	}

    private function Response($resCode=401,$isSucceed=false,$msg=null,$data=null){
        header('Content-Type:application/json');
        http_response_code($resCode);
        return [
            'success'=>$isSucceed,
            'message'=>$msg,
            'data'=>$data
        ];
    }
    private function base64SafeUrlEncode($str){
        return strtr(base64_encode($str), '+/=', '._-');
    }
    private function base64SafeUrlDecode($str){
        return base64_decode(strtr($str, '._-', '+/='));
    }
    private function decryptSafeURL($encStr=null){
        try{
            $decStr=openssl_decrypt($this->base64SafeUrlDecode($encStr)
            ,$this->config->item('sso_enc_method')
            ,$this->config->item('sso_enc_key')
            ,0
            ,$this->config->item('sso_enc_salt')
            );
            $decStr=json_decode($decStr);
            if($this->SSODataValidation($decStr)){
                return $this->Response(200,true,"SSO data is valid",$decStr);
            }else{
                return $this->Response(400,false,"SSO data is no longer valid!",null);
            }
        }catch(Exception $e){
            return $this->Response(500,false,"An error occured",['error'=>$e->getMessage()]);
        }
    }
    private function checkUserExist($identity){
        return $this->db->get_where('sys_user', ['email' => $identity,'id_usr_status'=>2])->num_rows()>0;
    }
    private function SSODataValidation($data){
        return isset($data->identity) 
        && isset($data->signature)
        && isset($data->timestamp)
        && $data->signature === $this->config->item('sso_app_signature')
        && $data->timestamp+$this->config->item('sso_direct_link_expire')>=time();
    }

    private function TokenDataValidation($data){
        return isset($data->appid) 
        && isset($data->passwd)
        && isset($data->timestamp)
        && $data->timestamp+$this->config->item('sso_direct_link_token_expire')>=time();
    }
    
    private function validateAcessToken(){
        $token=$this->input->get_request_header($this->config->item('sso_direct_link_token_key'), TRUE);
        $decStr=openssl_decrypt($token
            ,$this->config->item('sso_enc_method')
            ,$this->config->item('sso_enc_key')
            ,0
            ,$this->config->item('sso_enc_salt')
            );
        $data=json_decode($decStr);
        if(!$this->TokenDataValidation($data)){ 
            echo json_encode($this->Response(400,false,"Token is not valid!",null)); die();
        }
        $app_id=$data->appid;
        $pass=$data->passwd;
        $isValid=array_filter($this->config->item('sso_direct_link_token_acc'),function($d) use ($app_id,$pass){
            return $d['appid']===$app_id && $d['passwd']===$pass;
        });
        if(!$isValid){
            echo json_encode($this->Response(401,false,"Unauthorized",null)); die();
        }
    }


    //end points
    public function getAccessToken(){
        $app_id=$this->input->post('appid');
        $pass=$this->input->post('passwd');
        $timestamp=time();
        $isValid=array_filter($this->config->item('sso_direct_link_token_acc'),function($d) use ($app_id,$pass){
            return $d['appid']===$app_id && $d['passwd']===$pass;
        });
        if($isValid!=null){
            $encStr=openssl_encrypt(json_encode([
                'appid'=>$app_id,
                'passwd'=>$pass,
                'timestamp'=>$timestamp
            ],JSON_UNESCAPED_SLASHES)
            ,$this->config->item('sso_enc_method')
            ,$this->config->item('sso_enc_key')
            ,0
            ,$this->config->item('sso_enc_salt'));
            echo json_encode($this->Response(200,true,"OK",['accessToken'=>$encStr])); die();
        }else{
            echo json_encode($this->Response(401,false,"Unauthorized",null)); die();
        }
    }
    public function createSSOLoginLink(){
        $this->validateAcessToken();
        $data=$this->input->raw_input_stream;
        $data=json_decode($data);
        if($data==null){
            echo json_encode($this->Response(400,false,"Data is required",null)); die();
        }
        
        $data->timestamp=time();
        if(!$this->SSODataValidation($data)){
            echo json_encode($this->Response(400,false,"SSO data is not valid format",null)); die();
        }
        
        if(!$this->checkUserExist($data->identity)){
            echo json_encode($this->Response(400,false,"User not exist or not allowed to login into system",null)); die();
        }

        $encStr=openssl_encrypt(json_encode($data)
        ,$this->config->item('sso_enc_method')
        ,$this->config->item('sso_enc_key')
        ,0
        ,$this->config->item('sso_enc_salt'));
            
        $safeEncStr=$this->base64SafeUrlEncode($encStr);
        echo json_encode($this->Response(200,true,"SSO link successfuly generated",
        site_url('SSO/login/'.$safeEncStr)),JSON_UNESCAPED_SLASHES);
    }

    public function login($encStr=null){
        $data=$this->decryptSafeURL($encStr);
        if($data['success']){
            $data=$data['data'];
            $result=$this->auth->validate_login_sso($data->identity);
            if($result['success']){
                redirect(site_url('/app'));
            }
        }
        else{
            redirect(site_url('/app'));
        }
    }
}