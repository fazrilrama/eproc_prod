<?php
defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH . 'third_party/PHPSpreadSheet/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Padi_umkm extends App_Controller
{
    
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'list_umkm'],
                ['method' => 'list_transaksi'],
                ['method' => 'getListUMKM'],
                ['method' => 'getListUMKMDB'],
                ['method' => 'searchVendor'],
                ['method' => 'searchVendorUMKM'],
                ['method' => 'postDataUMKM'],
                ['method' => 'deleteDataUMKM'],
                ['method' => 'getFormatDataUMKM'],
                ['method'=>'readDataUMKMFromExcel'],
                ['method' => 'getListTransaksi'],
                ['method' => 'getListTransaksiDB'],
                ['method' => 'postDataTransaksi'],
                ['method' => 'deleteDataTransaksi'],
                ['method' => 'getFormatDataTransaksi'],
                ['method'=>'readDataTransaksiFromExcel'],
                ['method'=>'searchProject']

            ]
        ]);

        $this->load->model('Padiumkm_model','padiumkm');
        $this->load->library('upload');
    }


    public function searchVendor(){
        echo json_encode($this->padiumkm->searchVendor(
            $this->input->get('limit')
            ,$this->input->get('search')));
    }
    public function searchProject(){
        echo json_encode($this->padiumkm->searchProject(
            $this->input->get('limit')
            ,$this->input->get('search')));
    }


    // START UMKM

    public function list_umkm(){
        $this->set_page_title('pe-7s-link', 'PADI UMKM', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Data UMKM'
            ]
        ]);
        $data['kat_usaha']=$this->padiumkm->JENIS_KATEGORI_USAHA();
        $data['keg_usaha']=$this->padiumkm->JENIS_KEG_USAHA();

        $this->load->view('pages/padi_umkm/list_umkm',$data);
    }

    public function getListUMKM(){
        $page=$this->input->post('page');
        $size=$this->input->post('length');

        $data=$this->padiumkm->getListUMKM((int)$page,(int)$size);
        $mPadi=$this->padiumkm;
        $data['data']=array_map(function($d) use ($mPadi){
            if(is_array($mPadi->JENIS_KATEGORI_USAHA($d['kategori_usaha']))){
                $d['kat_usaha_name']=$mPadi->JENIS_KATEGORI_USAHA($d['kategori_usaha'])[0]->name;
            }
            if(is_array($mPadi->JENIS_KEG_USAHA($d['jenis_kegiatan_usaha'])) && count($mPadi->JENIS_KEG_USAHA($d['jenis_kegiatan_usaha']))>0 ){
                $d['keg_usaha_name']=$mPadi->JENIS_KEG_USAHA($d['jenis_kegiatan_usaha'])[0]->name;
            }
            return $d;
        },$data['data']);
        $data['recordsTotal']=(int)$data['count'];
        $data['recordsFiltered']=(int)$data['count'];
        echo json_encode($data);
    }

    public function getListUMKMDB(){
        $page=$this->input->post('page');
        $size=$this->input->post('length');
        $offset=$this->input->post('start');
        $search=$this->input->post('search[value]');

        $records=$this->padiumkm->getListUMKMFromDB(true,$size,$offset,function() use($search){
            $sql="";
            $params=[];
            if($search!=null){
                $sql.="
                AND (
                       lower(padiumkm_data_umkm.vendor_sap_id) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.uid'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.nama_umkm'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.alamat'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.blok_no_kav'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kode_pos'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kota'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.provinsi'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.no_telp'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.hp'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.email'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kategori_usaha'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.jenis_kegiatan_usaha'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.npwp'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.nama_bank'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.country_bank'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.no_rekening'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.nama_pemilik_rekening'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.longitute'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.latitute'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.total_project'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.total_revenue'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.ontime_rate'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.average_rating'),'\"','') ) like ?
                    OR lower(padiumkm_data_umkm.created_at) like ?
                    OR lower('itdev_bgr') like ?
                    OR lower(kat_usaha.name) like ?
                    OR lower(keg_usaha.name) like ?
                )
                ";
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
            }
            return [
                'sql'=>$sql,
                'params'=>$params
            ];
        });
        
        $data['data']=$records['data'];
        $data['recordsTotal']=(int)$records['count'];
        $data['recordsFiltered']=(int)$records['count'];
        echo json_encode($data);
    }

    public function postDataUMKM(){
        $dataInsert=[ "uid",
        "nama_umkm",
        "alamat",
        "blok_no_kav",
        "kode_pos",
        "kota",
        "provinsi",
        "no_telp",
        "hp",
        "email",
        "kategori_usaha",
        "jenis_kegiatan_usaha",
        "npwp",
        "nama_bank",
        "country_bank",
        "no_rekening",
        "nama_pemilik_rekening",
        "longitute",
        "latitute",
        "total_project",
        "total_revenue",
        "ontime_rate",
        "average_rating"];
        $data=[];
        foreach($dataInsert as $d){
            $data[$d]=$this->input->post($d);
            // if($d=='ontime_rate' || $d=='average_rating'){
            //     $data[$d].='%';
            // }
        }
        $inputHasNull=array_filter($data,function($item){
            return $item==null || $item=="";
        });
        if(count($inputHasNull)>0){
            echo json_encode(['status'=>'error']);
        }
        else{
            echo json_encode($this->padiumkm->addListUMKM($data));
        }
    }

    public function deleteDataUMKM(){
        $uid=$this->input->post('uid');
        $data=[
            'uid'=>[$uid]
        ];
        echo json_encode($this->padiumkm->deleteDataUMKM($data));
    }

    public function getFormatDataUMKM(){
        \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
        $filename="FORMAT UPLOAD EXCEL PADI UMKM";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(false)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $formatData='{
            "uid": "0020000137",
            "nama_umkm": "Toko UMKM Sejahtera",
            "alamat": "Jl. damai",
            "blok_no_kav": "BB24",
            "kode_pos": "12345",
            "kota": "Bekasi",
            "provinsi": "Jawa Barat",
            "no_telp": "021-222222",
            "hp": "175004087",
            "email": "damai@gmail.com",
            "kategori_usaha": 1,
            "jenis_kegiatan_usaha": "1",
            "npwp": "12312312312312",
            "nama_bank": "Mandiri",
            "country_bank": "ID",
            "no_rekening": "123345567890",
            "nama_pemilik_rekening": "Mahfuz",
            "longitute": "3.333333",
            "latitute": "3.333333",
            "total_project": "100000000",
            "total_revenue": "100000",
            "ontime_rate": "85%",
            "average_rating": "87%"
          }';
        $formatData=json_decode($formatData,TRUE);
        
        // START FORMAT INPUT
        $sheet->setTitle("FORMAT INPUT");
        $header=[
            'A'=>'UID(SAP VENDOR NO)',
            'B'=>'Nama UMKM',
            'C'=>'Alamat',
            'D'=>'Blok No Kav',
            'E'=>'Kode POS',
            'F'=>'Kota',
            'G'=>'Provinsi',
            'H'=>'No Telp',
            'I'=>'No HP',
            'J'=>'Email',
            'K'=>'ID Kategori Usaha',
            'L'=>'ID Kegiatan Usaha',
            'M'=>'NPWP',
            'N'=>'Nama Bank',
            'O'=>'Country Bank',
            'P'=>'No. Rekening',
            'Q'=>'Nama Pemeilik Rekening',
            'R'=>'Longitude',
            'S'=>'Latitude',
            'T'=>'Total Project(Rp)',
            'U'=>'Total Revenue(Rp)',
            'V'=>'On Time Rate(0-100%)',
            'W'=>'Average Rating(0-100%)'
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $sheet->getStyle($k)
            ->getNumberFormat()
            ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
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
        $listData=array();
        foreach($formatData as $v){
            array_push($listData,(string)$v);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        $sheet->setCellValue('X'.$startRow,'<--- CONTOH DATA');
        // END FORMAT INPUT

        //START JENIS KEG
        $spreadsheet->createSheet(1);
        $sheet=$spreadsheet->getSheet(1);
        $sheet->setTitle("JENIS KATEGORI USAHA");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"JENIS KATEGORI USAHA");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->JENIS_KATEGORI_USAHA();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START JENIS KEG
        $spreadsheet->createSheet(2);
        $sheet=$spreadsheet->getSheet(2);
        $sheet->setTitle("JENIS KEGIATAN USAHA");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"JENIS KEGIATAN USAHA");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->JENIS_KEG_USAHA();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START DATA VENDOR TERDAFTAR
        $spreadsheet->createSheet(3);
        $sheet=$spreadsheet->getSheet(3);
        $sheet->setTitle("VENDOR");
        $dataField=$this->padiumkm->searchVendor(1,null);
        if($dataField!=null && count($dataField)>0){
            $dataField=$dataField[0];
        }
        $headerCol=[];
        foreach($dataField as $k=>$v){
            $headerCol[]=$k;
        }
        $header=[];
        $i=0;
        $range=range('A','Z');
        for($i=0;$i<26;$i++){
            $header[$range[$i]]=$headerCol[$i];
        }
        $iRange=0;
        for($j=$i;$j<count($headerCol);$j++){
            $header['A'.$range[$iRange]]=$headerCol[$j];
            $i++;
            $iRange++;
        }

        $sheet->getStyle('A:AG')
            ->getNumberFormat()
            ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // echo json_encode($header);
        // die();
        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,strtoupper(str_replace("_"," ",$v)));
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"VENDOR");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->searchVendor(-1,null);;
        $listData=[];
        foreach($data as $v){
            $v=(array)$v;
            $colsData=[];
            foreach($header as $h){
                array_push($colsData,$v[$h]);
            }
            array_push($listData,$colsData);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END DATA VENDOR TERDAFTAR

        $spreadsheet->setActiveSheetIndex(0);
                
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename.".xlsx").'"');
        $writer->save('php://output');
    }

    public function readDataUMKMFromExcel(){
        $res=[
            'status'=>'error',
            'message'=>'file not valid!'
        ];
        $config=[
            'upload_path'=>'./assets/file/',
            'allowed_types'=>'xlsx',
            'max_size'=>10240,
            'overwrite'=>true,
            'file_name'=>'UploadedDataPADIUMKM.xlsx'
        ];
        $this->upload->initialize($config);
        if(!$this->upload->do_upload('file')){
            $res['message']=strip_tags($this->upload->display_errors());
        }
        else{
            try{
                $fileData=$this->upload->data();
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setLoadSheetsOnly('FORMAT INPUT');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($fileData['full_path']);
                $data=$spreadsheet->getActiveSheet()->toArray();

                $dataInsertIndex=[ "uid",
                            "nama_umkm",
                            "alamat",
                            "blok_no_kav",
                            "kode_pos",
                            "kota",
                            "provinsi",
                            "no_telp",
                            "hp",
                            "email",
                            "kategori_usaha",
                            "jenis_kegiatan_usaha",
                            "npwp",
                            "nama_bank",
                            "country_bank",
                            "no_rekening",
                            "nama_pemilik_rekening",
                            "longitute",
                            "latitute",
                            "total_project",
                            "total_revenue",
                            "ontime_rate",
                            "average_rating"];

                $isValid=false;
                if(count($data)>2){
                    if( count($data[1])>=count($dataInsertIndex) ){
                        $isValid=
                        $data[1][0]=='UID(SAP VENDOR NO)'
                        &&$data[1][1]=='Nama UMKM'
                        &&$data[1][2]=='Alamat'
                        &&$data[1][3]=='Blok No Kav'
                        &&$data[1][4]=='Kode POS'
                        &&$data[1][5]=='Kota'
                        &&$data[1][6]=='Provinsi'
                        &&$data[1][7]=='No Telp'
                        &&$data[1][8]=='No HP'
                        &&$data[1][9]=='Email'
                        &&$data[1][10]=='ID Kategori Usaha'
                        &&$data[1][11]=='ID Kegiatan Usaha'
                        &&$data[1][12]=='NPWP'
                        &&$data[1][13]=='Nama Bank'
                        &&$data[1][14]=='Country Bank'
                        &&$data[1][15]=='No. Rekening'
                        &&$data[1][16]=='Nama Pemeilik Rekening'
                        &&$data[1][17]=='Longitude'
                        &&$data[1][18]=='Latitude'
                        &&$data[1][19]=='Total Project(Rp)'
                        &&$data[1][20]=='Total Revenue(Rp)'
                        &&$data[1][21]=='On Time Rate(0-100%)'
                        &&$data[1][22]=='Average Rating(0-100%)' ;
                    }
                }
                if($isValid){
                    $startIndex=3;
                    $results=[];
                    $idx=0;
                    foreach($data as $d){
                        if($idx>=$startIndex){
                            
                            $dataInsert=[];
                            for($i=0;$i<count($dataInsertIndex);$i++){
                                $dataInsert[$dataInsertIndex[$i]]=$d[$i];
                            }
                            $results[]=$this->padiumkm->addListUMKM($dataInsert);
                        }
                        $idx++;
                    }
                    $statistics=[
                        'error'=>count(array_filter($results,function($item){
                            return $item['status']=='error';
                        })),
                        'success'=>count(array_filter($results,function($item){
                            return $item['status']=='success';
                        })),
                        'already_exist'=>count(array_filter($results,function($item){
                            return $item['status']=='already_exist';
                        })),
                    ];
                    $res['status']='success';
                    $res['message']='Result is has '.$statistics['success'].' succeed(s)'
                    .', has '.$statistics['already_exist'].' already exist(s)'
                    .', has '.$statistics['error'].' error(s)';
                }
                else{
                    $res['message']="Excel format is not valid!";
                }

            }catch(Exception $e){
                $res['message']="Excel format is not valid!";
            }
            

        }

        echo json_encode($res);
    }
    // END UMKM



    // START TRANSAKSI
    public function searchVendorUMKM(){
        echo json_encode($this->padiumkm->searchVendor(
            $this->input->get('limit')
            ,$this->input->get('search'),
        function(){
            $res=[
                'sql'=>'',
                'params'=>[]
            ];
            $res['sql']=" AND sap.id_sap in (select vendor_sap_id from padiumkm_data_umkm)";

            return $res;
        }));
    }
    public function list_transaksi(){
        $this->set_page_title('pe-7s-link', 'PADI UMKM', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Data Transaksi'
            ]
        ]);
        $data['kat_usaha']=$this->padiumkm->JENIS_KATEGORI_USAHA();
        $data['keg_usaha']=$this->padiumkm->JENIS_KEG_USAHA();

        $this->load->view('pages/padi_umkm/list_transaksi',$data);
    }
    public function getListTransaksi(){
        $page=$this->input->post('page');
        $size=$this->input->post('length');

        $data=$this->padiumkm->getListTransaksi((int)$page,(int)$size);
        $mPadi=$this->padiumkm;
        $data['data']=array_map(function($d) use ($mPadi){
            // $d['kategori_umkm']=$mPadi->JENIS_KATEGORI_USAHA($d['kategori_umkm'])[0]->name;
            $d['kat_usaha_name']=$mPadi->JENIS_KATEGORI_USAHA($d['kategori_umkm'])[0]->name;
            $d['kat_project_name']=$mPadi->KATEGORI_PROJECT($d['kategori_project'])[0]->name;
            // $d['kategori_delivery_time']=$mPadi->KATEGORI_DELIVERY_TIME($d['kategori_delivery_time'])[0]->name;
            $d['delivery_time_name']=$mPadi->KATEGORI_DELIVERY_TIME($d['kategori_delivery_time'])[0]->name;
            // $d['rating']=$mPadi->RATING_PROJECT($d['rating'])[0]->name;
            $d['rating_name']=$mPadi->RATING_PROJECT($d['rating'])[0]->name;
            $d['tanggal_transaksi']=$d['Tanggal_transaksi'];
            $d['transaksi_id_text']=str_pad($d['transaksi_id'],10,"0",STR_PAD_LEFT);
            return $d;
        },$data['data']);
        $data['recordsTotal']=(int)$data['count'];
        $data['recordsFiltered']=(int)$data['count'];
        echo json_encode($data);
    }
    public function getListTransaksiDB(){
        $page=$this->input->post('page');
        $size=$this->input->post('length');
        $offset=$this->input->post('start');
        $search=$this->input->post('search[value]');

        $records=$this->padiumkm->getListTransaksiFromDB(true,$size,$offset,function() use($search){
            $sql="";
            $params=[];
            if($search!=null){
                $sql.="
                AND (
                     lower(padiumkm_data_transaksi.id ) like ?
                    OR lower(padiumkm_data_transaksi.vendor_sap_id ) like ?
                    OR lower(padiumkm_data_transaksi.project_id ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tanggal_transaksi'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.transaksi_id'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.bumn_id'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.nama_project'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kategori_project'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.total_nilai_project'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tipe_nilai_project'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kategori_umkm'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.uid_umkm'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.nama_umkm'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.target_penyelesaian'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tanggal_order_placement'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tanggal_confirmation'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tanggal_delivery'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.tannggal_invoice'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.total_cycle_time'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.kategori_delivery_time'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.rating'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.feedback'),'\"','') ) like ?
                    OR lower(REPLACE(JSON_EXTRACT(data_json, '$.deskripsi_project'),'\"','') ) like ?
                    OR lower(padiumkm_data_transaksi.created_at like ? )
                    OR lower('itdev_bgr' ) like ?
                    OR lower(kat_usaha.name ) like ?
                    OR lower(kat_project.id ) like ?
                    OR lower(delivery_time.name ) like ?
                    OR lower(rating.name ) like ?
                )
                ";
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
                $params[]='%'.strtolower($search).'%';
            }
            return [
                'sql'=>$sql,
                'params'=>$params
            ];
        });
        $records['data']=array_map(function($d){
            $d->transaksi_id_text=str_pad($d->transaksi_id,10,"0",STR_PAD_LEFT);
            return $d;
        },$records['data']);
        
        $data['data']=$records['data'];
        $data['recordsTotal']=(int)$records['count'];
        $data['recordsFiltered']=(int)$records['count'];
        echo json_encode($data);
    }
    public function deleteDataTransaksi(){
        $uid=$this->input->post('uid');
        $data=[
            'transaksi_id'=>[$uid]
        ];
        echo json_encode($this->padiumkm->deleteDataTransaksi($data));
    }
    public function postDataTransaksi(){
        $dataInsert=[ "tanggal_transaksi",
        "transaksi_id",
        "nama_project",
        "kategori_project",
        "total_nilai_project",
        "tipe_nilai_project",
        "kategori_umkm",
        "uid_umkm",
        "nama_umkm",
        "target_penyelesaian",
        "tanggal_order_placement",
        "tanggal_confirmation",
        "tanggal_delivery",
        "tannggal_invoice",
        "total_cycle_time",
        "kategori_delivery_time",
        "rating",
        "feedback",
        "deskripsi_project"];
        $data=[];
        foreach($dataInsert as $d){
            $data[$d]=$this->input->post($d);
            // if($d=='target_penyelesaian' || $d=='total_cycle_time'){
            //     $data[$d].=' Hari';
            // }
            // if($d=='kategori_project'){
            //     $data[$d]=$this->padiumkm->KATEGORI_PROJECT($data[$d])[0]->name;
            // }
        }
        $inputHasNull=array_filter($data,function($item){
            return $item==null || $item=="";
        });
        $data['bumn_id']='BGR';
        if(count($inputHasNull)>0){
            echo json_encode(['status'=>'error','null'=>$inputHasNull]);
        }
        else{
            echo json_encode($this->padiumkm->addListTransaksi($data));
        }
    }
    public function getFormatDataTransaksi(){
        \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
        $filename="FORMAT UPLOAD EXCEL PADI UMKM - TRANSAKSI";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $spreadsheet->getDefaultStyle()->getAlignment()->setWrapText(false)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $formatData='{
            "tanggal_transaksi":"2020-06-21"
            ,"transaksi_id":"123"
            ,"nama_project":"Bangun Desa"
            ,"kategori_project":"1"
            ,"total_nilai_project":"50000000"
            ,"tipe_nilai_project":"CAPEX"
            ,"kategori_umkm":"1"
            ,"uid_umkm":"0020001231"
            ,"nama_umkm":"PT. Damai Sejahtera"
            ,"target_penyelesaian":"30"
            ,"tanggal_order_placement":"2020-06-21"
            ,"tanggal_confirmation":"2020-06-25"
            ,"tanggal_delivery":"2020-07-10"
            ,"tannggal_invoice":"2020-07-17"
            ,"total_cycle_time":"27"
            ,"kategori_delivery_time":"1"
            ,"rating":"4"
            ,"feedback":"OK"
            ,"deskripsi_project":"Project Membangun Jembatan"
        }';
        $formatData=json_decode($formatData,TRUE);
        
        // START FORMAT INPUT
        $sheet->setTitle("FORMAT INPUT");
        $header=[
            'A'=>'Tanggal Transaksi',
            'B'=>'Transaksi ID',
            'C'=>'Nama Project',
            'D'=>'Kategori Project',
            'E'=>'Total Nilai Project',
            'F'=>'Tipe Nilai Project',
            'G'=>'Kategori UMKM',
            'H'=>'UID UMKM',
            'I'=>'Nama UMKM',
            'J'=>'Target Penyelesaian',
            'K'=>'Tanggal Order Placement',
            'L'=>'Tanggal Confirmation',
            'M'=>'Tanggal Delivery',
            'N'=>'Tanggal Invoice',
            'O'=>'Total Cycle Time',
            'P'=>'Kategori Delivery Time',
            'Q'=>'Rating',
            'R'=>'Feedback',
            'S'=>'Deskripsi Project',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $sheet->getStyle($k)
            ->getNumberFormat()
            ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
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
        $listData=array();
        foreach($formatData as $v){
            array_push($listData,(string)$v);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        $sheet->setCellValue('T'.$startRow,'<--- CONTOH DATA');
        // END FORMAT INPUT

        //START JENIS KEG
        $spreadsheet->createSheet(1);
        $sheet=$spreadsheet->getSheet(1);
        $sheet->setTitle("KATEGORI PROJECT");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"KATEGORI PROJECT");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->KATEGORI_PROJECT();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START JENIS KEG
        $spreadsheet->createSheet(2);
        $sheet=$spreadsheet->getSheet(2);
        $sheet->setTitle("KATEGORI UMKM");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"KATEGORI UMKM");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->JENIS_KATEGORI_USAHA();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START JENIS KEG
        $spreadsheet->createSheet(3);
        $sheet=$spreadsheet->getSheet(3);
        $sheet->setTitle("KATEGORI DELIVERY TIME");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"KATEGORI DELIVERY TIME");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->KATEGORI_DELIVERY_TIME();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START JENIS KEG
        $spreadsheet->createSheet(4);
        $sheet=$spreadsheet->getSheet(4);
        $sheet->setTitle("PROJECT RATING");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"PROJECT RATING");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->RATING_PROJECT();
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START JENIS KEG
        $spreadsheet->createSheet(5);
        $sheet=$spreadsheet->getSheet(5);
        $sheet->setTitle("TIPE NILAI PROJECT");
        $header=[
            'A'=>'ID',
            'B'=>'Desc',
        ];

        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,$v);
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"TIPE NILAI PROJECT");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=[
            (object)[
                'id'=>'CAPEX',
                'name'=>'CAPEX'
            ],
            (object)[
                'id'=>'OPEX',
                'name'=>'OPEX'
            ]
        ];
        $listData=[];
        foreach($data as $v){
            array_push($listData,[(string)$v->id,$v->name]);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        //END JENIS KEG

        //START DATA PROJECT
        $spreadsheet->createSheet(6);
        $sheet=$spreadsheet->getSheet(6);
        $sheet->setTitle("PROJECT");
        $dataField=$this->padiumkm->searchProject(1,null);
        if($dataField!=null && count($dataField)>0){
            $dataField=$dataField[0];
        }
        $headerCol=[];
        foreach($dataField as $k=>$v){
            $headerCol[]=$k;
        }
        $header=[];
        $i=0;
        $range=range('A','Z');
        for($i=0;$i<26;$i++){
            $header[$range[$i]]=$headerCol[$i];
        }
        $iRange=0;
        for($j=$i;$j<count($headerCol);$j++){
            $header['A'.$range[$iRange]]=$headerCol[$j];
            $i++;
            $iRange++;
        }

        $sheet->getStyle('A:AG')
            ->getNumberFormat()
            ->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // echo json_encode($header);
        // die();
        $startRow=2;
        $startCol='A';
        foreach($header as $k=>$v){
            $sheet->setCellValue($k.$startRow,strtoupper(str_replace("_"," ",$v)));
            $sheet->getColumnDimension($k)
            ->setAutoSize(true);
            $sheet->getStyle($k.$startRow)->getFont()->setBold(TRUE)->setSize(11);
            $endCol=$k;
        }
        $sheet->setCellValue('A1',"PROJECT");
        $sheet->getStyle('A1')->getFont()->setBold(TRUE)->setSize(14);
        $sheet->getStyle('A1')
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')
        ->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->mergeCells($startCol."1:".$endCol."1");

        $startRow=3;
        $data=$this->padiumkm->searchProject(-1,null);
        $listData=[];
        foreach($data as $v){
            $v=(array)$v;
            $colsData=[];
            foreach($header as $h){
                array_push($colsData,$v[$h]);
            }
            array_push($listData,$colsData);
        }
        $sheet->fromArray($listData,NULL,'A'.$startRow);
        $sheet->removeColumn('B');
        $sheet->removeColumn('B');
        $sheet->removeColumn('B');
        $sheet->removeColumn('C');
        $sheet->removeColumn('C');
        $sheet->removeColumn('C');
        $sheet->removeColumn('D');
        $sheet->removeColumn('D');
        $sheet->removeColumn('H');
        $sheet->removeColumn('I');
        $sheet->removeColumn('I');
        $sheet->removeColumn('K');
        $sheet->removeColumn('K');
        //END DATA VENDOR TERDAFTAR

        $spreadsheet->setActiveSheetIndex(0);
                
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode($filename.".xlsx").'"');
        $writer->save('php://output');
    }
    public function readDataTransaksiFromExcel(){
        $res=[
            'status'=>'error',
            'message'=>'file not valid!'
        ];
        $config=[
            'upload_path'=>'./assets/file/',
            'allowed_types'=>'xlsx',
            'max_size'=>10240,
            'overwrite'=>true,
            'file_name'=>'UploadedDataPADITransaksi.xlsx'
        ];
        $this->upload->initialize($config);
        if(!$this->upload->do_upload('file')){
            $res['message']=strip_tags($this->upload->display_errors());
        }
        else{
            try{
                $fileData=$this->upload->data();
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setLoadSheetsOnly('FORMAT INPUT');
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($fileData['full_path']);
                $data=$spreadsheet->getActiveSheet()->toArray();

                $dataInsertIndex=[ "tanggal_transaksi"
                ,"transaksi_id"
                ,"nama_project"
                ,"kategori_project"
                ,"total_nilai_project"
                ,"tipe_nilai_project"
                ,"kategori_umkm"
                ,"uid_umkm"
                ,"nama_umkm"
                ,"target_penyelesaian"
                ,"tanggal_order_placement"
                ,"tanggal_confirmation"
                ,"tanggal_delivery"
                ,"tannggal_invoice"
                ,"total_cycle_time"
                ,"kategori_delivery_time"
                ,"rating"
                ,"feedback"
                ,"deskripsi_project"];

                $isValid=false;
                $header=[
                    'Tanggal Transaksi',
                    'Transaksi ID',
                    'Nama Project',
                    'Kategori Project',
                    'Total Nilai Project',
                    'Tipe Nilai Project',
                    'Kategori UMKM',
                    'UID UMKM',
                    'Nama UMKM',
                    'Target Penyelesaian',
                    'Tanggal Order Placement',
                    'Tanggal Confirmation',
                    'Tanggal Delivery',
                    'Tanggal Invoice',
                    'Total Cycle Time',
                    'Kategori Delivery Time',
                    'Rating',
                    'Feedback',
                    'Deskripsi Project',
                ];
        
                if(count($data)>2){
                    if( count($data[1])>=count($dataInsertIndex) ){
                        $isValid=
                        $data[1][0]==$header[0]
                        &&$data[1][1]==$header[1]
                        &&$data[1][2]==$header[2]
                        &&$data[1][3]==$header[3]
                        &&$data[1][4]==$header[4]
                        &&$data[1][5]==$header[5]
                        &&$data[1][6]==$header[6]
                        &&$data[1][7]==$header[7]
                        &&$data[1][8]==$header[8]
                        &&$data[1][9]==$header[9]
                        &&$data[1][10]==$header[10]
                        &&$data[1][11]==$header[11]
                        &&$data[1][12]==$header[12]
                        &&$data[1][13]==$header[13]
                        &&$data[1][14]==$header[14]
                        &&$data[1][15]==$header[15]
                        &&$data[1][16]==$header[16]
                        &&$data[1][17]==$header[17];
                    }
                }
                if($isValid){
                    $startIndex=3;
                    $results=[];
                    $idx=0;
                    foreach($data as $d){
                        if($idx>=$startIndex){
                            
                            $dataInsert=[];
                            for($i=0;$i<count($dataInsertIndex);$i++){
                                $dataInsert[$dataInsertIndex[$i]]=$d[$i];
                            }
                            $dataInsert['bumn_id']='BGR';
                            $results[]=$this->padiumkm->addListTransaksi($dataInsert);
                        }
                        $idx++;
                    }
                    $statistics=[
                        'error'=>count(array_filter($results,function($item){
                            return $item['status']=='error';
                        })),
                        'success'=>count(array_filter($results,function($item){
                            return $item['status']=='success';
                        })),
                        'already_exist'=>count(array_filter($results,function($item){
                            return $item['status']=='already_exist';
                        })),
                    ];
                    $res['status']='success';
                    $res['message']='Result is has '.$statistics['success'].' succeed(s)'
                    .', has '.$statistics['already_exist'].' already exist(s)'
                    .', has '.$statistics['error'].' error(s)';
                }
                else{
                    $res['message']="Excel format is not valid!";
                }

            }catch(Exception $e){
                $res['message']="Excel format is not valid!";
            }
            

        }

        echo json_encode($res);
    }
    // END TRANSAKSI
}