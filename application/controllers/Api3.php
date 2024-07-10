<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api3 extends CI_Controller
{
    //ZUludjBpYzMhOmVJbnZvaWNlQVBJRXByMGMhQCM2NjY=
    var $AUTH_PASS='eInvoiceAPIEpr0c!@#666';
    var $AUTH_UNAME='eInv0ic3!';
    var $encryptResponse=true;
    var $encryptMethod='AES-256-CBC';
    var $encryptKey='9y$B&E(H+MbQeThWmZq4t7w!z%C*F-J@';
    var $encryptIV='8x/A?D(G+KbPdSgV';

    public function __construct(){
       parent::__construct();
       $this->load->model('Auth_model','auth');
       $this->basicAuth();
       header('Content-Type:application/json');
        
    }
    function index()
    {
        echo json_encode([
            'success'=>true,
            'msg'=>'Auth valid'
        ]);
    }

    private function dataResponse($data=[]){
        return $this->encryptResponse?openssl_encrypt(json_encode($data),$this->encryptMethod,$this->encryptKey,0,$this->encryptIV):$data;
    }

    function doLogin(){
        $response=(object)[
            'success'=>false,
            'msg'=>'Credentials not valid!'
        ];
        $username=$this->input->post('usrname');
        $password=$this->input->post('passwd');
        if($username!=null && $password!=null){
            $user=$this->db
            ->select('user.*,role.role_name,status.status_name,profile.id as id_company
            ,sap.id_sap, sap.id_group, sap.vendor_gl_number,grp.name as group_name,grp.description as group_desc, npwp.no as npwp, contact.phone, profile.name as name')
            ->where('user.email',$username)
            ->join('sys_usr_role role','role.id_usr_role=user.id_usr_role', 'left')
            ->join('sys_usr_status status','status.id_usr_status=user.id_usr_status', 'left')
            ->join('company_profile profile','profile.id_user=user.id_user', 'left')
            ->join('company_legal_npwp npwp','npwp.id_company=profile.id', 'left')
            ->join('company_contact contact','contact.id_company=profile.id', 'left')
            ->join('tbl_sync_sap sap','sap.id_company=profile.id', 'left')
            ->join('m_group grp','grp.id=sap.id_group', 'left')
            ->where('user.deleted_at is null')
            ->where('user.id_usr_status!=',4)
            ->get('sys_user user')->row();
            if($user && password_verify($password,$user->password)){
                $response->success=true;
                $response->msg='User found';
                $response->data=$this->dataResponse($user);
            }
        }

        echo json_encode($response);
    }

    public function getUserDataByUID($uid=null){
        $response=(object)[
            'success'=>false,
            'msg'=>'UID Not Valid or Data Not Found'
        ];
        if($uid!=null){
            $user=$this->db
            ->select('role.role_name,status.status_name')
            ->select('user.*')
            ->where('user.id_user',$uid)
            ->join('sys_usr_role role','role.id_usr_role=user.id_usr_role')
            ->join('sys_usr_status status','status.id_usr_status=user.id_usr_status')
            ->where('user.deleted_at is null')
            ->where('user.id_usr_status!=',4)
            ->get('sys_user user')->row();
            if($user){
                $profile=$this->db->where('deleted_at is null')
                ->where('id_user',$user->id_user)->get('company_profile')->row();
                $user->profile=$profile;
                if($profile!=null){
                    $types=$this->db
                    ->where('id_company',$profile->id)
                    ->where('type.deleted_at is null')
                    ->join('m_company_type m_type','m_type.id=type.id_company_type')
                    ->get('company_type type')->result();
                    $user->type=$types;

                    $workArea=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_city city','city.id=a.id_city')
                    ->get('company_work_area a')->result();
                    $user->workArea=$workArea;

                    $pic=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_pic a')->result();
                    $user->pesonInCharge=$pic;

                    $competencies=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_company_sub_competency subcom','subcom.id=a.id_company_sub_competency')
                    ->join('m_company_competency com','subcom.id_company_competency=com.id')
                    ->get('company_competencies a')->result();
                    $user->competencies=$competencies;

                    $contact=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_contact a')->result();
                    $user->contact=$contact;

                    $doc=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_document a')->result();
                    $user->document=$doc;

                    $bankkAcc=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_finance_bank a')->result();
                    $user->bankAccount=$bankkAcc;

                    $bornLicense=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_born_license a')->result();
                    $user->bornLicense=$bornLicense;

                    $SAPNumber=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_group','a.id_group=m_group.id')
                    ->get('tbl_sync_sap a')->result();
                    $user->SAPNumber=$SAPNumber;

                    $npwp=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_npwp a')->result();
                    $user->legalNPWP=$npwp;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_nib a')->result();
                    $user->legalNIB=$legalData;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_siup a')->result();
                    $user->legalSIUP=$legalData;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_tdp a')->result();
                    $user->legalTDP=$legalData;

                }

                $response->success=true;
                $response->msg='Data found';
                $response->data=$this->dataResponse($user);
            }
        }

        echo json_encode($response);
    }

    public function getUserDataBySAPVendorCode($uid=null){
        $response=(object)[
            'success'=>false,
            'msg'=>'UID Not Valid or Data Not Found'
        ];
        if($uid!=null){
            $user=$this->db
            ->select('role.role_name,status.status_name')
            ->select('user.*')
            ->where('sap.id_sap',$uid)
            ->join('company_profile profile','profile.id_user=user.id_user')
            ->join('tbl_sync_sap sap','sap.id_company=profile.id')
            ->join('sys_usr_role role','role.id_usr_role=user.id_usr_role')
            ->join('sys_usr_status status','status.id_usr_status=user.id_usr_status')
            ->where('user.deleted_at is null')
            ->where('user.id_usr_status!=',4)
            ->get('sys_user user')->row();
            if($user){
                $profile=$this->db->where('deleted_at is null')
                ->where('id_user',$user->id_user)->get('company_profile')->row();
                $user->profile=$profile;
                if($profile!=null){
                    $types=$this->db
                    ->where('id_company',$profile->id)
                    ->where('type.deleted_at is null')
                    ->join('m_company_type m_type','m_type.id=type.id_company_type')
                    ->get('company_type type')->result();
                    $user->type=$types;

                    $workArea=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_city city','city.id=a.id_city')
                    ->get('company_work_area a')->result();
                    $user->workArea=$workArea;

                    $pic=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_pic a')->result();
                    $user->pesonInCharge=$pic;

                    $competencies=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_company_sub_competency subcom','subcom.id=a.id_company_sub_competency')
                    ->join('m_company_competency com','subcom.id_company_competency=com.id')
                    ->get('company_competencies a')->result();
                    $user->competencies=$competencies;

                    $contact=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_contact a')->result();
                    $user->contact=$contact;

                    $doc=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_document a')->result();
                    $user->document=$doc;

                    $bankkAcc=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_finance_bank a')->result();
                    $user->bankAccount=$bankkAcc;

                    $bornLicense=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_born_license a')->result();
                    $user->bornLicense=$bornLicense;

                    $SAPNumber=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_group','a.id_group=m_group.id')
                    ->get('tbl_sync_sap a')->result();
                    $user->SAPNumber=$SAPNumber;

                    $npwp=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_npwp a')->result();
                    $user->legalNPWP=$npwp;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_nib a')->result();
                    $user->legalNIB=$legalData;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_siup a')->result();
                    $user->legalSIUP=$legalData;

                    $legalData=$this->db
                    ->where('id_company',$profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_tdp a')->result();
                    $user->legalTDP=$legalData;

                }

                $response->success=true;
                $response->msg='Data found';
                $response->data=$this->dataResponse($user);
            }
        }

        echo json_encode($response);
    }

    private function basicAuth(){
        $authValid=false;
        $auth=$this->input->get_request_header('Authorization');
        if($auth !=null && strpos($auth,'Basic')!==FALSE && strpos( base64_decode(str_replace('Basic','',$auth)),':')!==FALSE ){
            $authStr=base64_decode(str_replace('Basic','',$auth));
            $authData=explode(':',$authStr);
            $authValid = count($authData)>1 && $authData[0]==$this->AUTH_UNAME && $authData[1]==$this->AUTH_PASS;
        }


        if(!$authValid){
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode([
                'success'=>false,
                'msg'=>'Unauthorized'
            ]);
            exit;
        }
    }
}