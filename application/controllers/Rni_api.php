<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rni_api extends App_Controller
{
    function __construct()
    {
        parent::__construct([
            'exclude_login'=>['data_procurement','readProcurement','addProcurement','deleteProcurment','getDataProcurement']
        ]);
        $this->load->model('RNI_API_model','api');
    }

    function data_procurement(){
        $this->set_page_title('pe-7s-menu', 'Pengadaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => null,
                'active' => false,
                'label' => ' API EIS(RNI)'
            ],
            [
                'icon' => '<i class="fa fa-check"></i>',
                'active' => true,
                'label' => ' Data Procurment'
            ]
        ]);
        $data['dataCompany'] = $this->db->get('m_company')->result();
        $this->load->view('pages/rni_api/data_procurement', $data);
    }

    function getDataProcurement(){
        $compID=$this->input->get('f_company');
        $startDate=$this->input->get('startDate')? $this->input->get('startDate') : date('Y-m-d',strtotime("-1 month",time()));
        $endDate=$this->input->get('endDate')? $this->input->get('endDate') : date('Y-m-d');
        if($compID!=null){
            $compData=$this->db->where('id',$compID)->get('m_company')->row();
            if($compData!=null){
                $this->api->setCompanyID($compData->rni_api_company);
                $this->api->setAPIKey($compData->rni_api_key);
            }
        }
        $data=$this->api->readListProcurment(['start_date'=>$startDate,'end_date'=>$endDate]);
        echo json_encode($data['responseBody']?$data['responseBody']:[
            'data_count'=>0,
            'data'=>array()
        ]);
    }

    function readProcurement(){
        echo json_encode($this->api->readListProcurment([
            'start_date'=>date('Y-m-d',strtotime("-1 month",time())),
            'end_date'=>date('Y-m-d')
        ]));
    }

    function deleteProcurment(){
        echo json_encode($this->api->deleteListProcurment([
            'start_date'=>date('Y-m-d',strtotime("-1 month",time())),
            'end_date'=>date('Y-m-d')
        ]));
    }

    function addProcurement(){
        
        echo json_encode($this
        ->api
        ->insertProcurment([
            [
                'division'=>'IT',
                'id_project'=>'1',
                'project_name'=>'Testing',
                'project_status'=>'req',
                'date'=>date('Y-m-d'),
                'oe_price'=>0,
                'deal_price'=>null,
                'id_supplier'=>null,
                'supplier_name'=>null
            ],
            [
                'division'=>'IT',
                'id_project'=>'1',
                'project_name'=>'Testing 1',
                'project_status'=>'process',
                'date'=>date('Y-m-d'),
                'oe_price'=>0,
                'deal_price'=>null,
                'id_supplier'=>null,
                'supplier_name'=>null
            ],
            [
                'division'=>'IT',
                'id_project'=>'1',
                'project_name'=>'Testing 2',
                'project_status'=>'done',
                'date'=>date('Y-m-d'),
                'oe_price'=>0,
                'deal_price'=>null,
                'id_supplier'=>null,
                'supplier_name'=>null
            ],
        ]));
    }
}