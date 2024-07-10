<?php
defined('BASEPATH') or exit('No direct script access allowed');

class RNI_API_model extends CI_Model
{
    var $RNIAPI;
    function __construct()
    {
        parent::__construct();
        $this->load->library('API_rni_lib');
        $dataCompany=$this->db
        ->where('id',$this->session->userdata('user')['id_company_owner'])
        ->get('m_company')
        ->row();
        
        if($dataCompany!=null){
            $this->RNIAPI=new RNI_API_Helper($dataCompany->rni_api_company,$dataCompany->rni_api_key);
        }
        else{
            $this->RNIAPI=new RNI_API_Helper(-1,'');
        }
    }
    public function readListProcurment($filter=[]){
        return $this->RNIAPI->RNIAPIReadData(RNI_API_TYPE_trans_procurement_real,$filter);
    }
    public function deleteListProcurment($filter=[]){
        return $this->RNIAPI->RNIAPIDeleteData(RNI_API_TYPE_trans_procurement_real,$filter);
    }

    public function insertProcurment($data=[]){
        return $this->RNIAPI->RNIAPIInsertProcurement($data);
    }

    public function setCompanyID($companyID){
        $this->RNIAPI->setCompanyID($companyID);
    }

    public function setAPIKey($APIKey){
        $this->RNIAPI->setAPIKey($APIKey);
    }
}