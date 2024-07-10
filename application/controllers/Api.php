<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
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

    public function tryDecrypt(){
        if(ENVIRONMENT==='development'){
            $data=$this->input->post('data');
            $res=[
                'success'=>false,
                'data'=>null
            ];
            if($data!=null){
                $res['data']=json_decode(openssl_decrypt($data,$this->encryptMethod,$this->encryptKey,0,$this->encryptIV));
                $res['success']=$res['data']!=null;
            }

            echo json_encode($res);
        }
        else{
            echo json_encode(['message'=>'Only available in development only']);
        }

    }

    public function getVerifiedVendor(){
        $search=$this->input->get('search');
        $withLimit=$this->input->get('withLimit')=='true';
        $limit=$this->input->get('limit');
        $limit=$limit?$limit:10;
        $offset=$this->input->get('offset');
        $offset=$offset?$offset:0;

        $params=[];
        $sql="select 
        sap.id_sap
        ,profile.prefix_name
        ,profile.name
        ,profile.postfix_name
        ,g.name as group_name
        ,usr.email as login_email
        ,contact.email as contact_email
        ,contact.phone
        from sys_user usr
        inner join company_profile profile on profile.id_user=usr.id_user
        inner join tbl_sync_sap sap on sap.id_company=profile.id
        inner join m_group g on g.id=profile.id_group
        inner join company_contact contact on contact.id_company=profile.id
        WHERE sap.id_sap is not null";
        
        if($search){
            $searchField=[
                'sap.id_sap',
                'profile.prefix_name',
                'profile.name',
                'profile.postfix_name',
                'g.name',
                'usr.email',
                'contact.email',
                'contact.phone'
            ];
            $sql.=" AND (";
            $i=0;
            foreach($searchField as $s){
                $sql.=($i>0?' OR':'')." lower(".$s.") like ?";
                $params[]=strtolower('%'.$search.'%');
                $i++;
            }
            $sql.=" )";
        }
        
        $totalData=$this->db->query($sql,$params)->num_rows();
        if($withLimit){
            $sql.=" LIMIT ? OFFSET ?";
            $params[]=(int) $limit;
            $params[]=(int) $offset;
        }
        $data=$this->db->query($sql,$params)->result();

        echo json_encode([
            'data'=>$this->dataResponse($data),
            'totalRecords'=>$totalData
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

    function vendor_login()
    {
        $response = (object)[
            'success' => false,
            'msg' => 'Credentials not valid!'
        ];

        $email = $this->input->post('email');
        $password = $this->input->post('password');

        if ($email != null && $password != null) {
            $user = $this->db
                ->select('user.*,role.role_name,status.status_name,profile.id as id_company
            ,sap.id_sap, sap.id_group, sap.vendor_gl_number,grp.name as group_name,grp.description as group_desc, npwp.no as npwp, contact.phone, profile.name as name')
                ->where('user.email', $email)
                ->join('sys_usr_role role', 'role.id_usr_role=user.id_usr_role', 'left')
                ->join('sys_usr_status status', 'status.id_usr_status=user.id_usr_status', 'left')
                ->join('company_profile profile', 'profile.id_user=user.id_user', 'left')
                ->join('company_legal_npwp npwp', 'npwp.id_company=profile.id', 'left')
                ->join('company_contact contact', 'contact.id_company=profile.id', 'left')
                ->join('tbl_sync_sap sap', 'sap.id_company=profile.id', 'left')
                ->join('m_group grp', 'grp.id=sap.id_group', 'left')
                ->where('user.deleted_at is null')
                ->where('user.id_usr_status', 2)
                ->where('user.id_usr_role', 2)
                ->like('user.email', '@')
                ->get('sys_user user')->row();

            if ($user && password_verify($password, $user->password)) {
                $response->success = true;
                $response->msg = 'Vendor found';
                $response->data = $user;
            } else {
                $response->success = false;
                $response->msg = 'Vendor not found';
            }
        }

        echo json_encode($response);
    }

    public function getAllUserVendor()
    {
        $response = (object)[
            'success' => false,
            'msg' => 'Credentials not valid!'
        ];

        $limit = $this->input->post('limit') !== null ? $this->input->post('limit') : 10;
        $offset = $this->input->post('offset') !== null ? $this->input->post('offset') : 0;

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $users = $this->db
            ->select('role.role_name,status.status_name')
            ->select('user.*')
            ->join('sys_usr_role role', 'role.id_usr_role=user.id_usr_role')
            ->join('sys_usr_status status', 'status.id_usr_status=user.id_usr_status')
            ->where('user.deleted_at is null')
            ->where('user.id_usr_status', 2)
            ->where('user.id_usr_role', 2)
            ->like('user.email', '@')
            ->get('sys_user user')->result_array();

        $data = array();
        foreach ($users as $u) {
            $profile = $this->db->where('deleted_at is null')->where('id_user', $u['id_user'])->get('company_profile')->row();

            if ($profile != null) {
                $types = $this->db
                    ->where('id_company', $profile->id)
                    ->where('type.deleted_at is null')
                    ->join('m_company_type m_type', 'm_type.id=type.id_company_type')
                    ->get('company_type type')->result();

                $workArea = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_city city', 'city.id=a.id_city')
                    ->get('company_work_area a')->result();

                $pic = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_pic a')->result();

                $competencies = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_company_sub_competency subcom', 'subcom.id=a.id_company_sub_competency')
                    ->join('m_company_competency com', 'subcom.id_company_competency=com.id')
                    ->get('company_competencies a')->result();

                $contact = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_contact a')->result();

                $doc = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_document a')->result();

                $bankkAcc = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_finance_bank a')->result();

                $bornLicense = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_born_license a')->result();

                $SAPNumber = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->join('m_group', 'a.id_group=m_group.id')
                    ->get('tbl_sync_sap a')->result();

                $npwp = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_npwp a')->result();

                $legalNIB = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_nib a')->result();

                $legalSIUP = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_siup a')->result();

                $legalTDP = $this->db
                    ->where('id_company', $profile->id)
                    ->where('a.deleted_at is null')
                    ->get('company_legal_tdp a')->result();

                $vendor = array(
                    'user' => $u,
                    'profile' => $profile,
                    'type' => $types,
                    'WorkArea' => $workArea,
                    'pesonInCharge' => $pic,
                    'competencies' => $competencies,
                    'contact' => $contact,
                    'document' => $doc,
                    'bankAccount' => $bankkAcc,
                    'bornLicense' => $bornLicense,
                    'SAPNumber' => $SAPNumber,
                    'legalNPWP' => $npwp,
                    'legalNIB' => $legalNIB,
                    'legalSIUP' => $legalSIUP,
                    'legalTDP' => $legalTDP
                );
            }

            $data[] = $vendor;
        }

        $response = array('status' => true, 'data' => $data);
        echo json_encode($response);
    }
}