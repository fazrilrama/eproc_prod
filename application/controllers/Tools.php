<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'third_party/PHPSpreadSheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tools extends App_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function import_vendor(){
        switch($this->input->method(TRUE)){
            case 'POST':{
                $this->upload_read_from_excel();
                break;
            }
            default:{

                $this->set_page_title('pe-7s-download', 'Import Vendor', [
                    [
                        'icon' => '<i class="fa fa-dashboard"></i>',
                        'link' => '#dashboard',
                        'label' => ''
                    ],
                    [
                        'icon' => '',
                        'active' => false,
                        'label' => ' Tools'
                    ]
                    ,
                    [
                        'icon' => '',
                        'active' => true,
                        'label' => ' Import Vendor'
                    ]
                ]);
                $this->load->view('pages/tools/import_vendor');
            }
        }
    }

    private function excel_reader($file=null,$excelFormat=null){
        
        $res=[
            'success'=>false,
            'message'=>'File to parsed is not valid'
        ];
        if($file!=null && $excelFormat!=null){
            try{

                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setReadDataOnly(true);
    
                $spreadsheet = $reader->load($file);

                //format validation
                $i=0;
                foreach($excelFormat['sheets'] as $f){
                    $sheet=$spreadsheet->setActiveSheetIndexByName($f);
                    foreach($excelFormat['headers'][$i] as $col=>$val){
                        if($sheet->getCell($col)->getValue()!==$val){
                            throw new Exception('Format on sheet '.$f." cell ".$col." is not valid, operation terminated.");
                        }
                    }
                    $i++;
                }

                //detecting values
                $i=0;
                foreach($excelFormat['sheets'] as $f){
                    $sheet=$spreadsheet->setActiveSheetIndexByName($f);
                    $values=[];
                    for($r=$excelFormat['start_read_row'][$i];$r<=$sheet->getHighestRow();$r++) {
                        $cellValues=[];
                        foreach($excelFormat['start_read_cols'][$i] as $c){
                            $cellValues[]=$sheet->getCell($c.$r)->getValue();
                        }
                        if(count($cellValues)<=0 || $cellValues[0]==null) break;
                        $values[]=$cellValues;
                    }
                    $excelFormat['values'][$i]=$values;
                    $i++;
                }
                $res['values']=$excelFormat['values'];
                $res['success']=true;
                $res['message']='Parsing succeed';

            }catch(Exception $e){
                $res['message']=$e->getMessage();
            }
        }
        return $res;

    }

    private function import_users($data=[],$definedData=[]){
        $res=[
            'success'=>false,
            'result'=>[]
        ];

        $this->db->trans_start();
        $result=[];
        foreach($data as $d){

            $isUpdate=$this->db
            ->where('email',$d[1])
            ->where('id_usr_status',2)
            ->get('sys_user')->row();
            $comOwnerData=$this->db
            ->where('codename',$d[0])
            ->get('m_company')
            ->row();
            if($comOwnerData!=null){

                $branchCode=$this->db
                ->where('id_company_owner',$comOwnerData->id)
                ->order_by('id','asc')
                ->get('m_branch_code')
                ->row();
                
                $branchCode=$branchCode!=null?$branchCode->id:null;

                if($isUpdate!=null){

                    $this->db->update('sys_user',array_merge(
                        [
                            'id_company_owner'=>$comOwnerData->id,
                            'name'=>$d[1],
                            'username'=>$d[1],
                            'email'=>$d[1],
                            'branch_code'=>$branchCode,
                            'password'=>password_hash($d[2],PASSWORD_DEFAULT,['cost'=>10])
                        ],
                        $definedData
                    ),[
                        'email'=>$d[1]
                    ]);

                    $result[]=['success'=>true,'message'=>'[Akun PIC Admin]Update data','data'=>$d];
                }
                else{
    
                    $this->db->insert('sys_user',array_merge(
                        [
                            'id_company_owner'=>$comOwnerData->id,
                            'email'=>$d[1],
                            'name'=>$d[1],
                            'username'=>$d[1],
                            'branch_code'=>$branchCode,
                            'password'=>password_hash($d[2],PASSWORD_DEFAULT,['cost'=>10])
                        ],
                        $definedData
                    ));
                    $result[]=['success'=>true,'message'=>'[Akun PIC Admin]Insert Data','data'=>$d];
                }

            }
            else{
                $result[]=['success'=>false,'message'=>'kode perusahaan tidak valid!','data'=>$d];
            }
            

        }
        $this->db->trans_complete();
        
        if($this->db->trans_status()!==FALSE){
            $this->db->trans_commit();
            $res['success']=true;
            $res['result']=$result;
        }
        else{
            $res['result']=[];
            $this->db->trans_rollback();
        }
        
        return $res;
    }
    private function import_vendors($data=[]){
        $res=[
            'success'=>false,
            'result'=>[]
        ];

        $this->db->trans_start();
        $result=[];
        for($i=0;$i<count($data);$i++){
            $d=$data[$i];
            $comOwnerData=$this->db
            ->where('codename',$d[0])
            ->get('m_company')
            ->row();
            
            if($comOwnerData==null){
                $result[]=['success'=>false,'message'=>'kode perusahaan tidak valid!','data'=>$d];
                continue;
            }

            //account
            $jenisVendor=['Perorangan'=>6,'Perusahaan'=>2];
            $groupData=['BUMN'=>1,'NON BUMN & SWASTA'=>4];

            if(!isset($jenisVendor[$d[2]]) || !isset($groupData[$d[3]]) ){
                $result[]=['success'=>false,'message'=>'Jenis Vendor / Group Vendor tidak valid','data'=>$d];
                continue;
            }

            $isUpdate=$this->db
            ->where('email',$d[4])
            ->where('id_usr_status',2)
            ->get('sys_user')->row();

            if($isUpdate!=null){

                $this->db->update('sys_user',[
                    'id_company_owner'=>$comOwnerData->id,
                    'name'=>$d[8],
                    'username'=>$d[4],
                    'email'=>$d[4],
                    'password'=>password_hash($d[6],PASSWORD_DEFAULT,['cost'=>10]),
                    'id_usr_role'=>$jenisVendor[$d[2]],
                    'id_usr_status'=>2,
                ],[
                    'id_user'=>$isUpdate->id_user
                ]);

                $idUser=$isUpdate->id_user;

                $result[]=['success'=>true,'message'=>'[Akun Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('sys_user',[
                    'id_company_owner'=>$comOwnerData->id,
                    'name'=>$d[8],
                    'username'=>$d[4],
                    'email'=>$d[4],
                    'password'=>password_hash($d[6],PASSWORD_DEFAULT,['cost'=>10]),
                    'id_usr_role'=>$jenisVendor[$d[2]],
                    'id_usr_status'=>2,
                ]);

                $idUser=$this->db->insert_id();
                $result[]=['success'=>true,'message'=>'[Akun Vendor]Insert Data','data'=>$d];
            }

            //profile
            $isUpdate=$this->db
            ->where('id_user',$idUser)
            ->get('company_profile')->row();

            if($isUpdate!=null){

                $this->db->update('company_profile',[
                    'id_user'=>$idUser,
                    'id_company_owner'=>$comOwnerData->id,
                    'name'=>$d[8],
                    'id_group'=>$groupData[$d[3]],
                    'verification_status'=>'Verified',
                    'highest_project_value'=>str_replace(['.',','],['',''],$d[9])
                ],[
                    'id'=>$isUpdate->id
                ]);
                $idCompany=$isUpdate->id;

                $result[]=['success'=>true,'message'=>'[Profil Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_profile',[
                    'id_user'=>$idUser,
                    'id_company_owner'=>$comOwnerData->id,
                    'name'=>$d[8],
                    'id_group'=>$groupData[$d[3]],
                    'verification_status'=>'Verified',
                    'highest_project_value'=>str_replace(['.',','],['',''],$d[9])
                ]);

                $idCompany=$this->db->order_by('created_at','desc')->limit(1)->get('company_profile')->row()->id;
                $result[]=['success'=>true,'message'=>'[Profil Vendor]Insert Data','data'=>$d];
            }
            
            //kontak
            $isUpdate=$this->db
            ->where('id_company',$idCompany)
            ->get('company_contact')->row();
            $cityData=$this->db->where('name',$d[12])->get('m_city')->row();
            if($cityData==null){
                $result[]=['success'=>false,'message'=>'Data kota tidak valid','data'=>$d];
                continue;
            }

            if($isUpdate!=null){

                $this->db->update('company_contact',[
                    'id_company'=>$idCompany,
                    'address'=>$d[10],
                    'id_country'=>1,
                    'id_country_province'=>$cityData->province_id,
                    'id_city'=>$cityData->id,
                    'email'=>$d[4],
                    'pos_code'=>$d[13],
                    'phone'=>$d[14],
                    'verification_status'=>'Verified'
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[Kontak Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_contact',[
                    'id_company'=>$idCompany,
                    'address'=>$d[10],
                    'id_country'=>1,
                    'id_country_province'=>$cityData->province_id,
                    'id_city'=>$cityData->id,
                    'email'=>$d[4],
                    'pos_code'=>$d[13],
                    'phone'=>$d[14],
                    'verification_status'=>'Verified'
                ]);
                $result[]=['success'=>true,'message'=>'[Kontak Vendor]Insert Data','data'=>$d];
            }

            //pic
            $isUpdate=$this->db
            ->where('id_company',$idCompany)
            ->get('company_pic')->row();

            if($isUpdate!=null){

                $this->db->update('company_pic',[
                    'id_company'=>$idCompany,
                    'name'=>$d[15],
                    'position'=>$d[16],
                    'position_type'=>1,
                    'mobile_phone'=>$d[17],
                    'email'=>$d[18],
                    'verification_status'=>'Verified'
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[PIC Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_pic',[
                    'id_company'=>$idCompany,
                    'name'=>$d[15],
                    'position'=>$d[16],
                    'position_type'=>1,
                    'mobile_phone'=>$d[17],
                    'email'=>$d[18],
                    'verification_status'=>'Verified'
                ]);
                $result[]=['success'=>true,'message'=>'[PIC Vendor]Insert Data','data'=>$d];
            }

            //sap
            $isUpdate=$this->db
            ->where('id_company',$idCompany)
            ->get('tbl_sync_sap')->row();

            $dataCompany=$this->db->where('id',$idCompany)->get('company_profile')->row();
            if($dataCompany==null){
                $result[]=['success'=>false,'message'=>'Data Vendor tidak valid','data'=>$d];
                continue;
            }
            $idSAP=$dataCompany->id_company_owner.'_'.$d[1];

            if($isUpdate!=null){

                $this->db->update('tbl_sync_sap',[
                    'id_company'=>$idCompany,
                    'id_sap'=>$idSAP,
                    'id_group'=>$dataCompany->id_group,
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'vendor_gl_number'=>'glnum_'.$idSAP
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[No Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('tbl_sync_sap',[
                    'id_company'=>$idCompany,
                    'id_sap'=>$idSAP,
                    'id_group'=>$dataCompany->id_group,
                    'vendor_gl_number'=>'glnum_'.$idSAP
                ]);
                $result[]=['success'=>true,'message'=>'[No Vendor]Insert Data','data'=>$d];
            }

            //bank
            $isUpdate=$this->db
            ->where('id_company',$idCompany)
            ->get('company_finance_bank')->row();
            $bankData=$this->db->where('name',$d[19])->get('m_bank_list')->row();
            if($bankData==null){
                $result[]=['success'=>false,'message'=>'Data bank tidak valid','data'=>$d];
                continue;
            }

            if($isUpdate!=null){

                $this->db->update('company_finance_bank',[
                    'id_company'=>$idCompany,
                    'bank_name'=>$d[19],
                    'owner'=>$d[20],
                    'no'=>$d[21],
                    'updated_at'=>date('Y-m-d H:i:s'),
                    'verification_status'=>'Verified'
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[Bank Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_finance_bank',[
                    'id_company'=>$idCompany,
                    'bank_name'=>$d[19],
                    'owner'=>$d[20],
                    'no'=>$d[21],
                    'verification_status'=>'Verified'
                ]);
                $result[]=['success'=>true,'message'=>'[Bank Vendor]Insert Data','data'=>$d];
            }

            //npwp
            $isUpdate=$this->db
            ->where('id_company',$idCompany)
            ->get('company_legal_npwp')->row();

            if($isUpdate!=null){

                $this->db->update('company_legal_npwp',[
                    'id_company'=>$idCompany,
                    'no'=>$d[5],
                    'verification_status'=>'Verified',
                    'updated_at'=>date('Y-m-d H:i:s')
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[NPWP Vendor]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_legal_npwp',[
                    'id_company'=>$idCompany,
                    'no'=>$d[5],
                    'verification_status'=>'Verified'
                ]);
                $result[]=['success'=>true,'message'=>'[NPWP Vendor]Insert Data','data'=>$d];
            }


        }
        $this->db->trans_complete();
        
        if($this->db->trans_status()!==FALSE){
            $this->db->trans_commit();
            $res['success']=true;
            $res['result']=$result;
        }
        else{
            $res['result']=[];
            $this->db->trans_rollback();
        }
        
        return $res;
    }
    private function import_company_type($data=[]){
        $res=[
            'success'=>false,
            'result'=>[]
        ];

        $this->db->trans_start();
        $result=[];
        for($i=0;$i<count($data);$i++){
            $d=$data[$i];
            $dataCompany=$this->db
            ->join('company_profile p','p.id_user=u.id_user')
            ->where('u.email',$d[0])
            ->get('sys_user u')
            ->row();
            
            if($dataCompany==null){
                $result[]=['success'=>false,'message'=>'Data vendor tidak valid!','data'=>$d];
                continue;
            }

            //bidang usaha
            $dataComType=$this->db->where('name',$d[1])->get('m_company_type')->row();
            if($dataComType==null){
                $result[]=['success'=>false,'message'=>'Data bidang usaha tidak valid!','data'=>$d];
                continue;
            }
            if($i==0) $this->db->where('id_company',$dataCompany->id)->delete('company_type');

            $isUpdate=$this->db
            ->where('id_company',$dataCompany->id)
            ->where('id_company_type',$dataComType->id)
            ->get('company_type')->row();

            if($isUpdate!=null){

                $this->db->update('company_type',[
                    'id_company'=>$dataCompany->id,
                    'id_company_type'=>$dataComType->id
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[Bidang Usaha]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_type',[
                    'id_company'=>$dataCompany->id,
                    'id_company_type'=>$dataComType->id
                ]);
                $result[]=['success'=>true,'message'=>'[Bidang Usaha]Insert Data','data'=>$d];
            }

        }
        $this->db->trans_complete();
        
        if($this->db->trans_status()!==FALSE){
            $this->db->trans_commit();
            $res['success']=true;
            $res['result']=$result;
        }
        else{
            $res['result']=[];
            $this->db->trans_rollback();
        }
        
        return $res;
    }
    private function import_company_work_area($data=[]){
        $res=[
            'success'=>false,
            'result'=>[]
        ];

        $this->db->trans_start();
        $result=[];
        for($i=0;$i<count($data);$i++){
            $d=$data[$i];
            $dataCompany=$this->db
            ->join('company_profile p','p.id_user=u.id_user')
            ->where('u.email',$d[0])
            ->get('sys_user u')
            ->row();
            
            if($dataCompany==null){
                $result[]=['success'=>false,'message'=>'Data vendor tidak valid!','data'=>$d];
                continue;
            }

            //area kerja
            $dataCity=$this->db->where('name',$d[1])->get('m_city')->row();
            if($dataCity==null){
                $result[]=['success'=>false,'message'=>'Data area kerja tidak valid!','data'=>$d];
                continue;
            }

            if($i==0) $this->db->where('id_company',$dataCompany->id)->delete('company_work_area');

            $isUpdate=$this->db
            ->where('id_company',$dataCompany->id)
            ->where('id_city',$dataCity->id)
            ->get('company_work_area')->row();

            if($isUpdate!=null){

                $this->db->update('company_work_area',[
                    'id_company'=>$dataCompany->id,
                    'id_city'=>$dataCity->id
                ],[
                    'id'=>$isUpdate->id
                ]);
                $result[]=['success'=>true,'message'=>'[Bidang Usaha]Update data','data'=>$d];
            }
            else{

                $this->db->insert('company_work_area',[
                    'id_company'=>$dataCompany->id,
                    'id_city'=>$dataCity->id
                ]);
                $result[]=['success'=>true,'message'=>'[Bidang Usaha]Insert Data','data'=>$d];
            }

        }
        $this->db->trans_complete();
        
        if($this->db->trans_status()!==FALSE){
            $this->db->trans_commit();
            $res['success']=true;
            $res['result']=$result;
        }
        else{
            $res['result']=[];
            $this->db->trans_rollback();
        }
        
        return $res;
    }

    private function upload_read_from_excel(){
        $res=[
            'success'=>false,
            'message'=>'Input not valid, please try again'
        ];

        $this->form_validation->set_rules('import_from','Import Method','required');

        if($this->form_validation->run()){
            $uploadPath='./upload/temp/';
            $file_config=[
                'upload_path'=>$uploadPath,
                'allowed_types'=>'xlsx',
                'max_size'=>1024*20,
                'encrypt_name'=>true,
            ];
            $this->load->library('upload');
            $this->upload->initialize($file_config);
            if($this->upload->do_upload('file_upload')){
                $fileData=$this->upload->data();

                //parsing data
                $parsingResult=$this->excel_reader($uploadPath.$fileData['file_name']
                ,[
                    'sheets'=>['Akun PIC Admin','Akun Vendor','Bidang Usaha','Area Kerja'],
                    'headers'=>[
                        [
                            'A1'=>'Kode Perusahaan',
                            'B1'=>'Email',
                            'C1'=>'Password'
                        ],
                        [
                            'A2'=>'Kode Perusahaan',
                            'B2'=>'No Vendor',
                            'C2'=>'Jenis Vendor',
                            'D2'=>'Grup Vendor',
                            'E2'=>'Email',
                            'F2'=>'NPWP',
                            'G2'=>'Password',
                            'H2'=>'Divre',
                            'I2'=>'Nama',
                            'J2'=>'Nilai Proyek Tertinggi(Rp)',
                            'K2'=>'Alamat',
                            'L2'=>'Provinsi',
                            'M2'=>'Kota',
                            'N2'=>'Kode Pos',
                            'O2'=>'Telp Perusahaan',
                            'P2'=>'Nama PIC',
                            'Q2'=>'Posisi',
                            'R2'=>'Telp PIC',
                            'S2'=>'Email PIC',
                            'T2'=>'Tipe Bank',
                            'U2'=>'Nama Nasabah',
                            'V2'=>'No Rek',
                        ],
                        [
                            'A1'=>'Email',
                            'B1'=>'Bidang Usaha',
                        ],
                        [
                            'A1'=>'Email',
                            'B1'=>'Kota',
                        ],
                    ],
                    'start_read_row'=>[2,3,2,2],
                    'start_read_cols'=>[
                        ['A','B','C'],
                        ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V'],
                        ['A','B'],
                        ['A','B'],
                    ],
                    'values'=>[[],[],[],[]]
                ]);

                $res['importResult']=[];
                if($parsingResult['success']){
                    if( isset($parsingResult['values']) && count($parsingResult['values'])>=4 ){
                        $importRes=$this->import_users($parsingResult['values'][0]
                        ,[
                            'id_usr_role'=>3
                            ,'id_usr_status'=>2
                        ]);
                        $res['importResult'][]=$importRes;
                        
                        $importRes=$this->import_vendors($parsingResult['values'][1]);
                        $res['importResult'][]=$importRes;

                        $importRes=$this->import_company_type($parsingResult['values'][2]);
                        $res['importResult'][]=$importRes;

                        $importRes=$this->import_company_work_area($parsingResult['values'][3]);
                        $res['importResult'][]=$importRes;
                    }
                }

                $res['success']=true;
                $res['message']='Import succeed!';

                if(file_exists($uploadPath.$fileData['file_name'])){
                    unlink($uploadPath.$fileData['file_name']);
                }
                
            }
            else{
                $res['message']=strip_tags($this->upload->display_errors());
            }
        }
        else{
            $res['message']=str_replace(['<p>','</p>'],['',''],validation_errors());
        }

        echo json_encode($res);
    }
}