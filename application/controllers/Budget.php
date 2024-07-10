<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Budget extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check'=>[
                ['method'=>'sync_yearly_sap_data']
            ]
        ]);
        $this->load->model('SAP_model', 'sap');
        $this->load->model('Budget_model', 'budget');
    }

    public function addBudget(){
       $res=[
            'success'=>false,
            'message'=>'Operation cant complete, please try again'
       ];
       $this->form_validation->set_rules('time','Tahun','required');
       $this->form_validation->set_rules('no_fund_center','Area Kerja','required');
       $this->form_validation->set_rules('available','Area Kerja','required');

       if($this->form_validation->run()){
           $hasAdded=$this->db
           ->where('owner_code',$this->input->post('no_fund_center'))
           ->where('time',$this->input->post('time'))
           ->get('m_budget')->row();
           if(!$hasAdded){
               $this->db->insert('m_budget',[
                   'owner_code'=>$this->input->post('no_fund_center'),
                   'fixed_total'=>$this->input->post('available'),
                   'available'=>$this->input->post('available'),
                   'interval_time'=>1,
                   'type'=>2,
                   'time'=>$this->input->post('time'),
               ]);
               $res['success']=true;
               $res['message']='Data berhasil disubmit';

           }
           else{
               $res['message']='Data budget sudah ada';
           }
       }
       else{
        $res['message']=str_replace(['<p>','</p>'],['',''],validation_errors());
       }

       echo json_encode($res);
    }

    public function index()
    {
        $this->set_page_title('pe-7s-cash', 'Master', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Budget'
            ]
        ]);

        $data['get_url'] = 'budget/get_data';
        $data['update_url'] = 'budget/edit_data';
        $this->load->view('pages/budget/main', $data);
    }

    public function get_data(){
        $res=$this->budget->getList(
            true,
            [
                'limit'=>$this->input->get('length')??10,
                'offset'=>$this->input->get('start')??0,
                'search'=>$this->input->get('search[value]')??null,
                'orderBy'=>$this->input->get('order[0][column]')??0,
                'orderDir'=>$this->input->get('order[0][dir]')??'asc',
            ],
            [
                0=>'b.name',
                1=>'b.no_fund_center',
                2=>'a.time',
                3=>'a.type',
                4=>'a.available',
                5=>'a.updated_at',
            ],
            function(){

                $f_branch = $this->input->get('f_branch');
                $f_type = $this->input->get('f_type');
                $f_year = $this->input->get('f_year');
                $f_company_owner = $this->input->get('f_company_owner');
                
                $params=[];
                $sql='';

                if ($f_branch != null){
                    $sql.=" AND no_fund_center=?";
                    $params[]=$f_branch;
                }
                if ($f_type != null){
                    $sql.=" AND `type`=?";
                    $params[]=$f_type;
                }
                if ($f_year != null){
                    $sql.=" AND `time`=?";
                    $params[]=$f_year;
                }
                if ($f_company_owner != null){
                    $sql.=" AND id_company_owner=?";
                    $params[]=$f_company_owner;
                }
                return [
                    'params'=>$params,
                    'sql'=>$sql
                ];
            }
        );

        $resFix=[];
        foreach($res['data'] as $d){
            $vals=[];
            foreach($d as $v){
                $vals[]=$v;
            }
            $resFix[]=$vals;
        }

        $res['data']=$resFix;

        echo json_encode($res);

    }


    public function get_data_bak()
    {
        $f_branch = $this->input->get('f_branch');
        $f_type = $this->input->get('f_type');
        $f_year = $this->input->get('f_year');
        $f_company_type = $this->input->get('f_company_type');

        $sql = "SELECT a.*
            , b.name as branch_name
            , b.official_code as branch_code
            , IF(type=1,'Operasional','Non-Operasional') as type_name
            , (SELECT COUNT(id) from shopping_cart where buyer_id in (
                SELECT id_user from sys_user where branch_code=b.id
                )
                and status !=13
                and status !=14
                and status !=8
                and status !=9
            ) as pending_order
            ,b.no_fund_center
            ,b.id_company_owner
        FROM m_budget a
        INNER JOIN m_branch_code b ON a.owner_code=b.no_fund_center
        WHERE 
        a.deleted_at is null";

        $data = $this->db->query($sql)->list_fields();

        $table = '(' . $this->db->last_query() . ') AS DATA';

        // Table's primary key
        $primaryKey = 'id';

        $columns = array();

        foreach ($data as $key => $value) {
            $columns[] = ['db' => $value, 'dt' => $key];
        }

        // SQL server connection information
        $sql_details = ssp_default_db();

        $sqlWhere = [];
        if ($f_branch != null) array_push($sqlWhere, "no_fund_center='$f_branch'");
        if ($f_type != null) array_push($sqlWhere, "type=$f_type");
        if ($f_year != null) array_push($sqlWhere, "time=$f_year");
        if ($f_company_type != null) array_push($sqlWhere, "id_company_owner=$f_company_type");

        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere));
    }

    public function get_sap_data($selectedYear=null,$fundID=null,$returnJson=true)
    {
        $year = $this->input->post('year');
        $fund_id = $this->input->post('fund_id');
        if($selectedYear!=null) $year=$selectedYear;
        if($fundID!=null) $fund_id=$fundID;

        //get coa
        $bcode=$this->db->where('no_fund_center',$fund_id)->get('m_branch_code')->row();

        if($returnJson){
            echo json_encode($this->sap->get_real_budget_sap($year, $fund_id,$bcode->coa));
        }
        else{
            return $this->sap->get_real_budget_sap($year, $fund_id,$bcode->coa);
        }
    }

    public function sync_yearly_sap_data($year=null,$type=null,$returnJson=true){
        $selectedYear=$year==null?date('Y'):$year;
        if($type!=null){
            $this->db->where('type',$type);
        }
        $data=$this->db
        ->where('time',$selectedYear-1)
        ->where('deleted_at is null')
        ->get('m_budget')
        ->result();

        $dataChanged=[];

        foreach($data as $d){
            $oldData=$this->db->where('time',$selectedYear)
            ->where('type',$d->type)
            ->where('owner_code',$d->owner_code)
            ->where('deleted_at is null')
            ->get('m_budget')->row();
            if($oldData==null){
                if($d->type==2){

                    $sapData=(object) $this->get_sap_data($selectedYear,$d->owner_code,false);
                    $sapData=$sapData!=null?$sapData->data:null;

                    $dataInsert=[
                        'type'=>$d->type,
                        'owner_code'=>$d->owner_code,
                        'fixed_total'=>$sapData!=null?$sapData->total:0,
                        'realization'=>$sapData!=null?$sapData->realisasi:0,
                        'available'=>$sapData!=null?$sapData->available:0,
                        'time'=>$selectedYear
                    ];
                    $this->db->insert('m_budget',$dataInsert);
                    $dataChanged[]=[
                        'action'=>'insert new',
                        'data'=>$dataInsert
                    ];
                }
                else{
                    $dataInsert=[
                        'type'=>$d->type,
                        'owner_code'=>$d->owner_code,
                        'fixed_total'=>0,
                        'realization'=>0,
                        'available'=>0,
                        'time'=>$selectedYear
                    ];
                    $this->db->insert('m_budget',$dataInsert);
                    $dataChanged[]=[
                        'action'=>'insert new',
                        'data'=>$dataInsert
                    ];
                }
            }
            else{
                if($d->type==2){

                    $sapData=(object) $this->get_sap_data($selectedYear,$d->owner_code,false);
                    $sapData=$sapData!=null?$sapData->data:null;
                    $dataUpdate=[
                        'type'=>$d->type,
                        'owner_code'=>$d->owner_code,
                        'fixed_total'=>$sapData!=null?$sapData->total:0,
                        'realization'=>$sapData!=null?$sapData->realisasi:0,
                        'available'=>$sapData!=null?$sapData->available:0,
                        'time'=>$selectedYear,
                        'updated_at'=>date('Y-m-d H:i:s')
                    ];
                    $this->db->where('id',$oldData->id)->update('m_budget',$dataUpdate);
                    $dataChanged[]=[
                        'action'=>'update',
                        'data'=>$dataUpdate
                    ];
                }
                else{
                    $dataUpdate=[
                        'type'=>$d->type,
                        'owner_code'=>$d->owner_code,
                        'fixed_total'=>0,
                        'realization'=>0,
                        'available'=>0,
                        'time'=>$selectedYear,
                        'updated_at'=>date('Y-m-d H:i:s')
                    ];
                    $this->db->where('id',$oldData->id)->update('m_budget',$dataUpdate);
                    $dataChanged[]=[
                        'action'=>'update',
                        'data'=>$dataUpdate
                    ];

                }
            }
        }
        
        if($returnJson){
            echo json_encode($dataChanged);
        }
        else{
            return $dataChanged;
        }
    }

    public function get_undone_transaction()
    {
        $fund_id = $this->input->get('fund_id');
        $sql = "SELECT a.*,c.prefix_name,c.name,e.no_fund_center from shopping_cart a
        inner join company_catalogue b on a.product_id=b.id
        inner join company_profile c on c.id=b.id_company
        inner join sys_user d on d.id_user=a.buyer_id
        inner join m_branch_code e on e.id=d.branch_code
        where a.deleted_at is null
        and a.status !=13
        and a.status !=14
        and a.status !=8
        and a.status !=9
        and a.status !=7
        and a.status !=6
        ";

        $data = $this->db->query($sql)->list_fields();

        $table = '(' . $this->db->last_query() . ') AS DATA';

        // Table's primary key
        $primaryKey = 'id';

        $columns = array();

        foreach ($data as $key => $value) {
            $columns[] = ['db' => $value, 'dt' => $key];
        }

        // SQL server connection information
        $sql_details = ssp_default_db();

        $sqlWhere = [];
        if ($fund_id != null) array_push($sqlWhere, "no_fund_center='$fund_id'");

        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere));
    }

    public function edit_data()
    {
        echo json_encode(
            edit_data('m_budget')
        );
    }
}
