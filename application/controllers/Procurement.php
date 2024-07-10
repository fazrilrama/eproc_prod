<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Procurement extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get_data'],
                ['method' => 'get_bidding'],
                ['method' => 'get_bidding_list'],
                ['method' => 'submit_bidding'],
                ['method' => 'search_vendor_by'],
                ['method' => 'get_pr'],
                ['method' => 'save_data'],
                ['method' => 'publish_data'],
                ['method' => 'delete_data'],
                ['method' => 'get_data_only_vendor'],
                ['method' => 'cancel_procurement'],
                ['method' => 'get_real_budget_sap'],
                ['method' => 'changedFrontShowed'],
                ['method' => 'checkBlacklistedVendor'],
            ],
            'exclude_login' => ['get_data']
        ]);

        $this->load->model('Company_competency_model', 'competency');
        $this->load->model('Company_type_model', 'type');
        $this->load->model('Company_subcompetency_model', 'sub_competency');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Country_province_model', 'province');

        $this->load->model('User_status_model', 'user_status');
        $this->load->model('User_role_model', 'user_role');
        $this->load->model('User_model', 'user');
        $this->load->model('Master_model', 'master');
        $this->load->model('SAP_model', 'sap');

        $this->load->model('Email_model', 'email_helper');

        $this->load->model('RNI_API_model','rni_api');
    }

    public function get_real_budget_sap()
    {
        $year = $this->input->get('year');
        $no_fund_center = $this->input->get('no_fund_center');
        echo json_encode($this->sap->get_real_budget_sap($year, $no_fund_center));
    }

    public function search_vendor_by()
    {
        $field = $this->input->get('field');
        $competency = $this->input->get('competency');
        $sub_competency = $this->input->get('sub_competency');
        $withBlacklisted= $this->input->get('withBlacklisted');

        $conditions = '';

        $sql = "select a.*,d.id_user,d.id_usr_status,d.id_usr_role from 
        company_profile a 
        inner join tbl_sync_sap b on a.id=b.id_company
        inner join sys_user d on d.id_user=a.id_user";

        if ($field != null && count($field) > 0) {

            $conditions .= " and ( ";
            $i = 0;
            foreach ($field as $f) {
                $conditions .= ($i > 0 ? " or" : " ") . " $f in (select id_company_type from company_type where deleted_at is null and id_company=a.id) ";
                $i++;
            }
            $conditions .= " )";
        }

        if ($competency != null) {
            $conditions .= " and $competency in ( select f.id_company_competency 
            from company_competencies e
            inner join m_company_sub_competency f on f.id=e.id_company_sub_competency
            where e.id_company=a.id
            )";
        }

        if ($sub_competency != null) {
            $conditions .= " and $sub_competency in ( select e.id_company_sub_competency 
            from company_competencies e
            inner join m_company_sub_competency f on f.id=e.id_company_sub_competency
            where e.id_company=a.id
            )";
        }

        if($withBlacklisted==0){
            $sql.=" and d.is_blacklisted!=1";
        }

        $sql .= " and a.deleted_at is null 
        and d.id_usr_status!=4
        $conditions";

        $data = $this->db->query($sql)->result();

        echo json_encode([
            'total' => count($data),
            'data' => $data
        ]);
    }


    public function index()
    {
        $this->set_page_title('pe-7s-menu', 'Pengadaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '<i class="fa fa-check"></i>',
                'active' => true,
                'label' => ' Pengadaan'
            ]
        ]);
        $data['get_url'] = 'procurement/get_data';
        $data['delete_url'] = 'procurement/delete_data';
        $data['update_url'] = 'procurement/edit_data';
        $data['save_url'] = 'procurement/save_data';
        $data['publish_url'] = 'procurement/publish_data';
        $this->load->view('pages/procurement/main', $data);
    }

    public function procurement_list()
    {
        $this->set_page_title('pe-7s-menu', 'Pengadaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'icon' => '<i class="fa fa-check"></i>',
                'active' => true,
                'label' => ' Pengadaan'
            ]
        ]);
        $data['get_url'] = 'procurement/get_data_only_vendor';
        $data['delete_url'] = '#';
        $data['update_url'] = '#';
        $data['save_url'] = '#';
        $data['publish_url'] = '#';
        $this->load->view('pages/procurement/main_vendor', $data);
    }

    public function get_bidding()
    {
        $id_project = $this->input->get('id_project');
        $id_company = $this->input->get('id_company');
        $mode = $this->input->get('mode');

        if ($id_company == null) {
            $company = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                ->get('company_profile')
                ->row();
            if ($company != null) {
                $id_company = $company->id;
            }
        }

        $data = [];

        if ($id_company != null) {
            $data = $this->db->where('id_project', $id_project)
                ->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->get('project_bidding');
            if ($mode == 'single') {
                $data = $data->row();
            } else {
                $data = $data->result();
            }
        }

        echo json_encode(['success' => ($data != []), 'data' => $data]);
    }

    public function get_bidding_list()
    {
        $id_project = $this->input->get('id_project');

        $data = [];
        $data = $this->db
            ->select("a.*, ( SELECT id_user FROM company_profile where id=a.id_company limit 1 ) as id_user,
        ( SELECT concat(if(prefix_name is null,'',prefix_name),name) FROM company_profile where id=a.id_company limit 1 ) as vendor_name
        , (
            SELECT price FROM project_bidding where id_project=a.id_project and id_company=a.id_company
            order by created_at desc limit 1
        ) as last_price, (
            SELECT created_at FROM project_bidding where id_project=a.id_project and id_company=a.id_company
            order by created_at desc limit 1
        ) as last_update, (
            SELECT note FROM project_bidding where id_project=a.id_project and id_company=a.id_company
            order by created_at desc limit 1
        ) as last_note,
        (
            a.id_company=(select winner from project where id=$id_project limit 1)
        ) as is_winner
         ")
            ->where('a.id_project', $id_project)
            ->group_by('id_company')
            ->get('project_bidding a')->result();

        echo json_encode(['success' => ($data != []), 'data' => $data]);
    }

    public function submit_bidding()
    {
        $id_project = $this->input->post('id_project');
        $id_company = $this->input->post('id_company');
        $price = $this->input->post('price');
        $note = $this->input->post('note');

        if ($id_company == null) {
            $company = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                ->get('company_profile')
                ->row();
            if ($company != null) {
                $id_company = $company->id;
            }
        }
        $data = false;

        if ($id_company != null) {
            $data = $this->db->insert('project_bidding', [
                'id_project' => $id_project,
                'id_company' => $id_company,
                'price' => $price,
                'note' => $note,
                'recomend_status' => 0,
            ]);

            $project = $this->db->where('id', $id_project)
                ->get('project')->row();
            if ($project != null) {
                add_notification(
                    $this->session->userdata('user')['id_user'],
                    $project->id_user,
                    null,
                    'Penawaran Dari Vendor',
                    'Hallo, terdapat PENAWARAN terbaru untuk pengadaan dengan judul'
                        . $project->name . ' dengan No.PR ' . $project->contract_no,
                    'Internal',
                    '#procurement',
                    true
                );
            }
        }

        echo json_encode(['success' => $data]);
    }

    public function checkBlacklistedVendor($vendors=[]){
        $is_all_valid=true;
        $listBlacklistVendor=[];
        foreach($vendors as $v){
            $vendor=$this->db
            ->select('profile.*,is_blacklisted,id_sap')
            ->where('profile.id',$v)
            ->join('sys_user user','user.id_user=profile.id_user')
            ->join('tbl_sync_sap sap','sap.id_company=profile.id')
            ->get('company_profile profile')
            ->row();
            if($vendor->is_blacklisted==1){
                $listBlacklistVendor[]=$vendor;
                $is_all_valid=false;
                break;
            }
        }

        $blacklistedVendor='';
        $i=0;
        foreach($listBlacklistVendor as $v){
            if($i>0) $blacklistedVendor.=", ";
            $blacklistedVendor.=($v->prefix_name!=null?$v->prefix_name.' ':'').$v->name.'( No.SAP:'.$v->id_sap.')';
            $i++;
        }

        return [
            'valid'=>$is_all_valid,
            'blacklisted_vendor'=>$listBlacklistVendor,
            'message'=>'Vendor Terblacklist : '.$blacklistedVendor
        ];

    }

    public function save_data($return_json = true)
    {
        $targetVendors=$this->input->post('target_vendors');
        $targetVendors=$targetVendors?$targetVendors:[];
        $checkBlacklist=$this->checkBlacklistedVendor($targetVendors);
        if(!$checkBlacklist['valid']){
            echo json_encode([
                'success'=>false,
                'message'=>'Operasi tidak dapat dilakukan, terdapat vendor terblacklist: '.$checkBlacklist['message'],
                'blacklisted_vendor'=>$checkBlacklist['blacklisted_vendor']
            ]);
            die();
        }
        $config['upload_path']          = self::PATH_UPLOAD_PROCUREMENT;
        $config['allowed_types']        = 'pdf|png|jpg|jpeg';
        $config['max_size']             = 51200;
        $config['remove_spaces']        = true;
        $config['encrypt_name']         = true;
        $attachment = $this->upload_company_attachment(false, 'attachment', $config);
        $id = $this->input->post('id');
        $for_copy = $this->input->post('for_copy');
        $existing_attachment = $this->input->post('existing_attachment');

        $attachment_file_name = null;
        if ($for_copy != null && $for_copy == 'true' && $attachment['file_data']['file_name'] == null) {
            $attachment_file_name = $existing_attachment == 'null' ? null : $existing_attachment;
        } else {
            $attachment_file_name = ($attachment['success']) ? $attachment['file_data']['file_name'] : null;
        }

        //check if exist
        if ($id == null) {
            $save = add_data('project', [
                'attachment' => $attachment_file_name,
                'target_vendors' => $this->input->post('vendor_value'),
                'id_company_type' => $this->input->post('field_value'),
                'work_area' => $this->input->post('work_area_value'),
                'status' => 1,
                'division'=>$this->input->post('division'),
                'oe_price'=>$this->input->post('oe_price'),
            ]);
        } else {
            $save = edit_data('project', [
                'attachment' => $attachment_file_name,
                'target_vendors' => $this->input->post('vendor_value'),
                'id_company_type' => $this->input->post('field_value'),
                'work_area' => $this->input->post('work_area_value'),
                'status' => 1,
                'division'=>$this->input->post('division'),
                'oe_price'=>$this->input->post('oe_price'),
            ]);
        }

        if ($return_json) {
            echo json_encode($save);
        } else {
            return $save;
        }
    }

    public function publish_data()
    {
        $saved = $this->save_data(false);
        $success = false;
        if ($saved['success']) {
            //Search Vendor Depend on Type

            $type = $this->input->post('project_type');

            switch ($type) {
                case 1: {
                        add_notification(
                            $this->session->userdata('user')['id_user'],
                            null,
                            2,
                            'Informasi Pengadaan',
                            'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                            'Internal',
                            '#procurement/procurement_list',
                            false
                        );
                        add_notification(
                            $this->session->userdata('user')['id_user'],
                            null,
                            6,
                            'Informasi Pengadaan',
                            'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                            'Internal',
                            '#procurement/procurement_list',
                            false
                        );
                        add_notification(
                            $this->session->userdata('user')['id_user'],
                            null,
                            7,
                            'Informasi Pengadaan',
                            'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                            'Internal',
                            '#procurement/procurement_list',
                            false
                        );
                        break;
                    }
                case 2: {
                        $field_value = $this->input->post('field_value');
                        $work_area_value = $this->input->post('work_area_value');
                        $sql = "select a.* from company_profile a
                        inner join sys_user b on a.id_user=b.id_user
                        inner join tbl_sync_sap c on a.id=c.id_company
                        where a.deleted_at is null
                        and (b.id_usr_status=2 or b.id_usr_status=3)
                        and (b.id_usr_role=2 or b.id_usr_role=6 or b.id_usr_role=7)
                        and ( (select count(id) from company_type 
                        where id_company=a.id
                        and find_in_set(id_company_type,'" . $field_value . "') )>=1
                             and
                             (select count(id) from company_work_area 
                        where id_company=a.id
                        and find_in_set(id_city,'" . $work_area_value . "') )>=1
                        )";
                        $data = $this->db->query($sql)->result();

                        foreach ($data as $d) {
                            add_notification(
                                $this->session->userdata('user')['id_user'],
                                $d->id_user,
                                null,
                                'Informasi Pengadaan',
                                'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                    . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                                'Internal',
                                '#procurement/procurement_list',
                                true
                            );
                        }

                        break;
                    }
                case 3: {
                        $data = $this->input->post('vendor_value');
                        if (strpos($data, ',') !== FALSE) {
                            $vendors = explode(',', $data);
                            foreach ($vendors as $v) {
                                $vendor = $this->db->where('id', $v)->get('company_profile')
                                    ->row();
                                if ($vendor != null) {
                                    add_notification(
                                        $this->session->userdata('user')['id_user'],
                                        $vendor->id_user,
                                        null,
                                        'Informasi Pengadaan',
                                        'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                            . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                                        'Internal',
                                        '#procurement/procurement_list',
                                        true
                                    );
                                }
                            }
                        } else {
                            $vendor = $this->db->where('id', $data)->get('company_profile')
                                ->row();
                            if ($vendor != null) {
                                add_notification(
                                    $this->session->userdata('user')['id_user'],
                                    $vendor->id_user,
                                    null,
                                    'Informasi Pengadaan',
                                    'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                        . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                                    'Internal',
                                    '#procurement/procurement_list',
                                    true
                                );
                            }
                        }
                        break;
                    }
                case 4: {
                        $vendor = $this->db->where('id', $this->input->post('vendor_value'))->get('company_profile')
                            ->row();
                        if ($vendor != null) {
                            add_notification(
                                $this->session->userdata('user')['id_user'],
                                $vendor->id_user,
                                null,
                                'Informasi Pengadaan',
                                'Halo , terdapat pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                                    . ' yang sesuai dengan Kompetensi Anda/Perusahaan Anda, Silahkan cek daftar pengadaan di ' . site_url(),
                                'Internal',
                                '#procurement/procurement_list',
                                true
                            );
                        }
                        break;
                    }
            }

            //Change status
            $id = $this->input->post('id');
            if ($id != null) {
                $success = $this->db->where('id', $id)
                    ->update('project', ['status' => 2]);
            } else {
                $data = $this->db->where('deleted_at is null')
                    ->order_by('created_at', 'desc')
                    ->limit(1)
                    ->get('project')->row();

                if ($data != null) {
                    $success = $this->db->where('id', $data->id)
                        ->update('project', ['status' => 2]);
                }
                $id=$data->id;
            }

            $data = $this->db->where('id',$id)
                    ->order_by('created_at', 'desc')
                    ->get('project')->row();
            if($data!=null && $data->oe_price>500000000){
                //API RNI
                $status=RNI_API_PROCURMENT_STATUS_REQ;
                switch($data->status){
                    case 1:{
                        $status=RNI_API_PROCURMENT_STATUS_REQ;
                        break;
                    }
                    case 2:{
                        $status=RNI_API_PROCURMENT_STATUS_PROCESS;
                        break;
                    }
                    case 3:{
                        $status=RNI_API_PROCURMENT_STATUS_DONE;
                        break;
                    }
                }
                $this->rni_api->insertProcurment(
                    [
                        [
                            'division'=>$data->division,
                            'id_project'=>$data->id,
                            'project_name'=>$data->name,
                            'project_status'=>$status,
                            'date'=>date_format(date_create($data->start_date),'Y-m-d'),
                            'oe_price'=>$data->oe_price,
                            'deal_price'=>0,
                            'id_supplier'=>null,
                            'supplier_name'=>null
                        ]
                    ]
                );
            }
        }

        echo json_encode([
            'success' => $success,
        ]);
    }

    public function changedFrontShowed(){
        $res=[
            'success'=>false,
            'message'=>'Operation failed'
        ];

        $id=$this->input->post('id');
        $isShowed=$this->input->post('showed');
        if($id!=null){
            $res['success']=$this->db->update('project',[
                'showed_infront'=>$isShowed
            ],
            [
                'id'=>$id
            ]);

            $res['message']='Operation success';
        }

        echo json_encode($res);
    }

    public function get_data()
    {
        $f_id_user = $this->input->get('f_id_user');
        $f_status = $this->input->get('f_status');
        $f_project_type = $this->input->get('f_project_type');
        $f_start_date = $this->input->get('f_start_date');
        $f_end_date = $this->input->get('f_end_date');
        $f_showed_infront = $this->input->get('f_showed_infront');
        $f_company_owner = $this->input->get('f_company_owner');

        $sql = "SELECT a.id,
        a.contract_no,
        a.project_type,
        c.name as project_type_name,
        a.name as project_name,
        a.start_date,
        a.end_date,
        a.description,
        a.status,
        a.attachment,
        a.id_user,
        a.id_company_type,
        a.id_company_competency,
        a.id_company_sub_competency,
        a.target_vendors,
        a.work_area,
        a.created_at,
        a.updated_at,
        a.deleted_at
		,b.name as customer_name
        ,b.email as customer_email
        ,a.field_value_name
        ,a.vendor_value_name
        ,a.work_area_name
        ,( CURRENT_TIMESTAMP>=a.start_date && CURRENT_TIMESTAMP<=a.end_date ) as in_active
        ,a.showed_infront
        ,a.division
        ,a.oe_price
        FROM project a
        INNER join sys_user b on b.id_user=a.id_user
        INNER JOIN m_project_type c on c.id=a.project_type
        WHERE a.deleted_at is null";
        if (($f_start_date == null && $f_end_date == null) && $f_status == 2) $sql .= " and ( CURRENT_TIMESTAMP>=a.start_date && CURRENT_TIMESTAMP<=a.end_date )";
        if($f_showed_infront!=null) $sql.=" and showed_infront=$f_showed_infront";
        if($f_company_owner!=null) $sql.=" and b.id_company_owner=$f_company_owner";

        $data = $this->db->query($sql)->list_fields();

        $table = '(' . $this->db->last_query() . ' order by start_date desc) AS DATA';

        // Table's primary key
        $primaryKey = 'id';

        $columns = array();

        foreach ($data as $key => $value) {
            $columns[] = ['db' => $value, 'dt' => $key];
        }

        // SQL server connection information
        $sql_details = ssp_default_db();

        $sqlWhere = [];
        if ($f_status != null) array_push($sqlWhere, "status=$f_status");
        if ($f_id_user != null) array_push($sqlWhere, "id_user=$f_id_user");
        if ($f_project_type != null) array_push($sqlWhere, "project_type=$f_project_type");
        if ($f_start_date != null && $f_end_date == null) array_push($sqlWhere, "start_date>='$f_start_date:00'");
        if ($f_end_date != null && $f_start_date == null) array_push($sqlWhere, "end_date <= '$f_end_date:00'");
        if ($f_end_date != null && $f_start_date != null) array_push($sqlWhere, "(start_date>='$f_start_date:00' AND end_date <= '$f_end_date:00')");


        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere));
    }

    public function get_data_only_vendor()
    {
        $f_id_user = $this->input->get('f_id_user');
        $f_project_type = $this->input->get('f_project_type');
        $f_status = $this->input->get('f_status');
        $f_start_date = $this->input->get('f_start_date');
        $f_end_date = $this->input->get('f_end_date');

        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->db
            ->select('a.id,a.deleted_at,a.id_user')
            ->join('sys_user ', 'sys_user.id_user=a.id_user')
            ->where('a.id_user', $id_user)
            ->where('a.deleted_at is null')
            ->get('company_profile a')
            ->row();

        $sql = "SELECT a.id,
        a.contract_no,
        a.project_type,
        c.name as project_type_name,
        a.name as project_name,
        a.start_date,
        a.end_date,
        a.description,
        a.status,
        a.attachment,
        a.id_user,
        a.id_company_type,
        a.id_company_competency,
        a.id_company_sub_competency,
        a.target_vendors,
        a.work_area,
        a.created_at,
        a.updated_at,
        a.deleted_at,
		b.name as customer_name
        ,b.email as customer_email
        ,a.field_value_name
        ,a.vendor_value_name
        ,a.work_area_name
        ,( CURRENT_TIMESTAMP>=a.start_date && CURRENT_TIMESTAMP<=a.end_date ) as in_active
        FROM project a
        INNER join sys_user b on b.id_user=a.id_user
        INNER JOIN m_project_type c on c.id=a.project_type
        WHERE a.deleted_at is null
        ";
        if ($this->session->userdata('user')['id_usr_role'] != 1) $sql .= "
        AND 
        (
            a.project_type=1 
            or 
            (
                a.project_type=2
                AND ( 
                        (select count(id_company_type) from company_type 
                        where id_company=" . ($company != null ? "'" . $company->id . "'" : "'undefined'") . "
                        and find_in_set(id_company_type,a.id_company_type) )>=1
                        or
                        (select count(id_city) from company_work_area 
                        where id_company=" . ($company != null ? "'" . $company->id . "'" : "'undefined'") . "
                        and find_in_set(id_city,a.work_area) )>=1
                    ) 
            )
            or
            (
                ( a.project_type=3
                AND a.target_vendors like " . ($company != null ? "'%" . $company->id . "%'" : "'%undefined%'") . " )
                or 
                ( a.project_type=4
                AND a.target_vendors like " . ($company != null ? "'%" . $company->id . "%'" : "'%undefined%'") . " )
            )
        ) ";
        if (($f_start_date == null && $f_end_date == null) && $f_status == 2) $sql .= " and ( CURRENT_TIMESTAMP>=a.start_date && CURRENT_TIMESTAMP<=a.end_date )";

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
        if ($f_status != null) array_push($sqlWhere, "status=$f_status");
        if ($f_id_user != null) array_push($sqlWhere, "id_user=$f_id_user");
        if ($f_project_type != null) array_push($sqlWhere, "project_type=$f_project_type");
        if ($f_start_date != null && $f_end_date == null) array_push($sqlWhere, "start_date>='$f_start_date:00'");
        if ($f_end_date != null && $f_start_date == null) array_push($sqlWhere, "end_date <= '$f_end_date:00'");
        if ($f_end_date != null && $f_start_date != null) array_push($sqlWhere, "(start_date>='$f_start_date:00' AND end_date <= '$f_end_date:00')");


        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere));
    }

    public function delete_data()
    {
        echo json_encode(delete_data('project', 'id'));
    }

    public function cancel_procurement()
    {
        $success = $this->db->where('id', $this->input->post('id'))
            ->update('project', ['status' => 4]);

        echo json_encode([
            'success' => $success
        ]);
    }

    public function choose_winner()
    {
        $success = $this->db->where('id', $this->input->post('id'))
            ->update('project', [
                'status' => 3,
                'updated_at' => date('Y-m-d H:i:s'),
                'winner' => $this->input->post('id_company')
            ]);

        $user = $this->db->where('id', $this->input->post('id_company'))
            ->get('company_profile')->row();
        $project = $this->db->where('id', $this->input->post('id'))
            ->get('project')->row();
        $bidding=$this->db
        ->where('id_project', $this->input->post('id'))
        ->where('id_company', $this->input->post('id_company'))
        ->order_by('created_at','desc')
        ->get('project_bidding')
        ->row();
        if ($success) {

            if($project!=null && $user!=null && $bidding!=null ){
                //API RNI
                $status=RNI_API_PROCURMENT_STATUS_REQ;
                switch($project->status){
                    case 1:{
                        $status=RNI_API_PROCURMENT_STATUS_REQ;
                        break;
                    }
                    case 2:{
                        $status=RNI_API_PROCURMENT_STATUS_PROCESS;
                        break;
                    }
                    case 3:{
                        $status=RNI_API_PROCURMENT_STATUS_DONE;
                        break;
                    }
                }
                $this->rni_api->insertProcurment(
                    [
                        [
                            'division'=>$project->division,
                            'id_project'=>$project->id,
                            'project_name'=>$project->name,
                            'project_status'=>$status,
                            'date'=>date_format(date_create($project->start_date),'Y-m-d'),
                            'oe_price'=>$project->oe_price,
                            'deal_price'=>$bidding->price,
                            'id_supplier'=>$user->id_user,
                            'supplier_name'=>$user->prefix_name." ".$user->name." ".$user->postfix_name
                        ]
                    ]
                );
            }
            add_notification(
                $this->session->userdata('user')['id_user'],
                $user->id_user,
                null,
                'Informasi Pemenang Pengadaan',
                'Halo , 
                            Selamat Anda telah menjadi PEMENANG pada pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                    . '. Berikut rincian pengadaan yang Anda Menangkan:<br>
                            Nama : ' . $project->name . '<br/>'
                    . 'No.PR : ' . $project->contract_no . '<br/>'
                    . 'Waktu Pengadaan : ' . $project->start_date . ' s/d ' . $project->end_date . '<br/>'
                    . 'Deskripsi : ' . $project->description . '<br/>',
                'Internal',
                '#procurement/procurement_list',
                false
            );

            if(ENVIRONMENT==='production'){

                $success = $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $user->email,
                    'Informasi Pemenang Pengadaan',
                    'Halo , 
                                Selamat Anda telah menjadi PEMENANG pada pengadaan terbaru dari ' . $this->config->item('app_info')['identity']['name']
                        . '. Berikut rincian pengadaan yang Anda Menangkan:<br>
                                Nama : ' . $project->name . '<br/>'
                        . 'No.PR : ' . $project->contract_no . '<br/>'
                        . 'Waktu Pengadaan : ' . $project->start_date . ' s/d ' . $project->end_date . '<br/>'
                        . 'Deskripsi : ' . $project->description . '<br/>'
                );
            }
            else{
                $success=true;
            }
        }

        echo json_encode([
            'success' => $success
        ]);
    }
}
