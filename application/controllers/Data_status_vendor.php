<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'third_party/PHPSpreadSheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Data_status_vendor extends App_Controller{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check'=>[
                ['method'=>'index'],
                ['method'=>'get_data'],
                ['method'=>'exportExcel'],
            ]
        ]);
        $this->load->model('Data_status_model', 'status');
    }

    public function index(){
        $this->set_page_title('pe-7s-edit', 'Status Vendor', [
            [
                'icon' => '<i class="fa fa-users"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Status Vendor'
            ]
        ]);
        $this->load->view('pages/data_status_vendor/main');
    }

    public function changeStatus(){
        $response=[
            'success'=>false,
            'message'=>'Operation cant be done right now, please try again later...'
        ];

        $id=$this->input->post('id');
        $currStatus=$this->input->post('currentStatus');
        $note=$this->input->post('note');
        if($id!=null && $currStatus!=null){
            $response['success']=$this->db->update('sys_user',[
                'is_blacklisted'=>($currStatus==1?0:1),
                'blacklist_note'=>$note
            ],
            [
                'id_user'=>$id
            ]
            );

            if($response['success']){
                $this->db->insert('vendor_blacklist_history',[
                    'id_user'=>$id,
                    'is_blacklisted'=>($currStatus==1?0:1),
                    'note'=>$note,
                    'executor_id'=>$this->session->userdata('user')['id_user']
                ]);
                $response['message']='Operation succeess!';
            }
        }

        echo json_encode($response);
    }


    public function get_data($useLimit=true,$returnJson=true){
        $f_kompetensi = $this->input->get('f_kompetensi');
        $f_sub_kompetensi = $this->input->get('f_sub_kompetensi');
        $f_area_kerja = $this->input->get('f_area_kerja');
        $f_bidang = $this->input->get('f_bidang');
        $f_branch = $this->input->get('f_branch');
        $f_status=$this->input->get('f_status');
        $f_jenis=$this->input->get('f_jenis_vendor');
        $f_company_owner = $this->input->get('f_company_owner');

        $orderCol=$this->input->get('order[0][column]');
        $orderDir=$this->input->get('order[0][dir]');
        $orderCols=[
            0=>'blacklist_status_name',
            1=>"concat(if(prefix_name is null,'',concat(prefix_name,' ') ),name)",
            2=>'id_company_owner',
            3=>'id_sap',
            4=>'group_name',
            5=>'email',
            6=>'phone',
            7=>'no_npwp',
            11=>'created_at',
        ];

        $data=$this->status->get([
            'useLimit'=>$useLimit,
            'limit'=>(int) $this->input->get('length'),
            'offset'=>(int) $this->input->get('start'),
            'orderBy'=>isset($orderCols[$orderCol])?$orderCols[$orderCol]:'created_at',
            'orderDirection'=>$orderDir?$orderDir:'desc',
            'search'=>$this->input->get('search[value]'),
            'f_kompetensi' => $f_kompetensi,
            'f_sub_kompetensi' => $f_sub_kompetensi,
            'f_area_kerja' => $f_area_kerja,
            'f_bidang' => $f_bidang,
            'f_branch' => $f_branch,
            'f_company_owner'=>$f_company_owner
        ],function($sql) use ($f_status,$f_jenis){
            $params=[];
            if($f_status!=null){
                $sql.=" and is_blacklisted=?";
                $params[]=(int) $f_status;
            }
            if($f_jenis!=null){
                $sql.=" and id_usr_role=?";
                $params[]=(int) $f_jenis;
            }
            return [
                'sql'=>$sql,
                'params'=>$params
            ];
        });
        if($returnJson){
            echo json_encode($data);
        }
        else{
            return $data;
        }
    }

    public function exportExcel(){
        \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
        $filename=$this->input->get('fileName');
        ob_start();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(true)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $data=[];
        $header=[
            'A'=>'Status',
            'B'=>'Nama Vendor',
            'C'=>'No Vendor',
            'D'=>'Pemilik Vendor',
            'E'=>'Grup',
            'F'=>'Email',
            'G'=>'No.Telp',
            'H'=>'NPWP No',
            'I'=>'Bidang Usaha',
            'J'=>'Area Kerja',
            'K'=>'Kompetensi',
            'L'=>'Sub Kompetensi',
            'M'=>'Note',
            'N'=>'Tgl Akun Dibuat',
            'O'=>'Nama PIC',
            'P'=>'Telp PIC',
            'Q'=>'Alamat',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
                    $sheet->setCellValue($k.$startRow,$v);
                    if($k=='H' || $k=='I' || $k=='J'){
                        $sheet->getColumnDimension($k)->setWidth(50);
                    }
                    else{
                        $sheet->getColumnDimension($k)
                        ->setAutoSize(true);
                    }
                    if($k=='C' || $k=='F'){
                        $sheet->getStyle($k.$startRow)
                        ->getNumberFormat()
                        ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    }
                    $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(12);

                    $endCol=$k;
        }
        $sheet->setCellValue('A1',$filename);
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");
        $startRow=3;

        $data=[];
        $data=$this->get_data(false,false)['data'];
        foreach($data as $d){
            $listData=[
                $d->blacklist_status_name,
                $d->name.($d->prefix_name!=null?', '.$d->prefix_name:''),
                $d->id_sap,
                $d->company_owner_name,
                $d->group_desc,
                $d->email,
                $d->phone,
                $d->no_npwp,
                str_replace('||',',',$d->company_types),
                str_replace('||',',',$d->work_area),
                str_replace('||',',',$d->competencies_name),
                str_replace('||',',',$d->sub_competencies_name),
                $d->blacklist_note,
                $d->created_at,
                $d->pic_name!=null?$d->pic_name:$d->name.($d->prefix_name!=null?', '.$d->prefix_name:''),
                $d->pic_mobile_phone,
                $d->address,
            ];
            $sheet->fromArray($listData,NULL,'A'.$startRow);
            $startRow++;
        }
                
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        $xlsData = ob_get_contents();
        ob_end_clean();
        die(json_encode(
            ['success'=>true,
            'file'=>'data:application/vnd.ms-excel;base64,'.base64_encode($xlsData)]
        ));
    }
}