<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'add_data', 'id_role' => [1]],
                ['method' => 'edit_data', 'id_role' => [1]],
                ['method' => 'delete_data', 'id_role' => [1]],
                ['method' => 'get_data'],
                ['method' => 'get_branch'],
                ['method' => 'get_data_province'],
                ['method' => 'get_data_city'],
                ['method' => 'get_data_competency'],
                ['method' => 'get_data_sub_competency'],
                ['method' => 'get_user_public'],
                ['method' => 'get_company_competency'],
                ['method' => 'get_company_sub_competency'],
                ['method' => 'get_company_type_public'],
                ['method' => 'get_data_required_form'],
                ['method' => 'add_data_required_form', 'id_role' => [1]],
                ['method' => 'edit_data_required_form', 'id_role' => [1]],
                ['method' => 'add_data_company_with_attachment', 'id_role' => [1]],
                ['method' => 'edit_data_company_with_attachment', 'id_role' => [1]],
            ]
        ]);

        $this->load->model('Company_competency_model', 'competency');
        $this->load->model('Company_type_model', 'type');
        $this->load->model('Company_subcompetency_model', 'sub_competency');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Country_province_model', 'province');
        $this->load->model('City_model', 'city');

        $this->load->model('User_status_model', 'user_status');
        $this->load->model('User_role_model', 'user_role');
        $this->load->model('User_model', 'user');
        $this->load->model('Master_model', 'master');
    }

    public function get_company_competency()
    {
        $id = $this->input->get('id');
        echo json_encode($this->master->get_company_competency($id)->get()->result());
    }

    public function get_company_sub_competency()
    {
        $id = $this->input->get('id');
        $id_company_competency = $this->input->get('id_company_competency');
        echo json_encode($this->master->get_company_sub_competency($id)
            ->where('id_company_competency', $id_company_competency)
            ->get()
            ->result());
    }

    // Public Get Data

    public function get_user_public()
    {
        $users = $this->user->get_data()->get()->result();
        $result = [
            'success' => true,
            'result' => []
        ];

        foreach ($users as $u) {
            if ($u->id_usr_role != 1 && $u->id_usr_role != 4 && $u->id_usr_role != 5) {

                $allowed_data = [
                    'id_user' => $u->id_user,
                    'id_usr_status' => $u->id_usr_status,
                    'id_usr_role' => $u->id_usr_role,
                    'status_name' => $u->status_name,
                    'role_name' => $u->role_name,
                    'email' => $u->email,
                    'name' => $u->name
                ];

                $result['result'][] = $allowed_data;
            }
        }
        echo json_encode($result);
    }

    public function get_company_type_public()
    {
        echo json_encode($this->type->get_data()->get()->result());
    }


    // User Status
    public function user_status()
    {
        $table_name = App_Model::TBL_USR_STATUS;
        $table_fields = $this->user_status->get_data()->get()->list_fields();
        $fields_exception = ['id_usr_status', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'status_name' => [
                'text' => 'Name', 'id' => 'status_name'
            ],
            'status_desc' => [
                'text' => 'Description', 'id' => 'status_desc'
            ]
        ]);

        $this->set_page_title('pe-7s-check', 'User Status', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' User Status'
            ]
        ]);

        $dataKey = 'id_usr_status';

        $data['header_title'] = 'Master - User Status';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $data['form'] = form_builder($table_name, $fields_exception, [
            'status_name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Status name is not valid!'
            ]
        ], [
            'status_name' => [
                'text' => 'Name<span style="color:red;">*</span>'
            ]
        ]);

        $this->load->view('pages/master/master_view', $data);
    }

    // Branch
    public function get_branch(){
        $id = $this->secure_input($this->input->get('id'));
        $params=[];
        $sql="select b.codename,a.* from m_branch_code a 
        inner join m_company b on b.id=a.id_company_owner
        where a.deleted_at is null";
        if($id!=null){
            $sql.=" AND a.id=?";
            $params[]=$id;
        }
        if($this->session->userdata('user')['id_usr_role']==3){
            $sql.=" AND a.id_company_owner=?";
            $params[]=$this->session->userdata('user')['id_company_owner'];
        }

        echo json_encode($this->db->query($sql,$params)
            ->result()
        );
    }
    public function branch()
    {
        $table_name = 'm_branch_code';
        $table_fields = $this->db->query(
            "select b.codename,a.* from m_branch_code a 
            inner join m_company b on b.id=a.id_company_owner"
        )
        ->list_fields();
        $fields_exception = ['id','id_company_owner', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'codename' => [
                'text' => 'Vendor Owner', 'id' => 'codename'
            ],
        ]);

        $this->set_page_title('pe-7s-check', 'User Status', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Cabang'
            ]
        ]);

        $dataKey = 'id';

        $data['header_title'] = 'Master - Cabang';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_branch';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];
        $form = form_builder($table_name, $fields_exception, [

            'id_company_owner' => [
                'data-validation' => 'required',
            ],
            'official_code' => [
                'data-validation' => 'required',
            ],
            'name' => [
                'data-validation' => 'required',
            ],
            'no_fund_center' => [
                'data-validation' => 'required',
            ],
            'coa' => [
                'data-validation' => 'required',
            ]
        ], [
            'id_company_owner' => [
                'text' => 'Company Owner<span style="color:red;">*</span>'
            ],
            'name' => [
                'text' => 'Name<span style="color:red;">*</span>'
            ],
            'official_code' => [
                'text' => 'Official Code<span style="color:red;">*</span>'
            ],
            'no_fund_center' => [
                'text' => 'No Fund Center<span style="color:red;">*</span>'
            ],
            'coa' => [
                'text' => 'Coa<span style="color:red;">*</span>'
            ]
        ]);

        $form_inflated=[];
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_company_owner') {
                $f['input_field']['html'] = '<select id="id_company_owner" name="id_company_owner" 
                data-validation="required" 
                class="form-control" type="select">';
                $opt = '<option value="">Pilih</option>';
                $data_opt = $this->db->where('deleted_at is null')->get('m_company')->result();
                foreach ($data_opt as $o) {
                    if($this->session->userdata('user')['id_usr_role']!=1 ){
                        if($o->id==$this->session->userdata('user')['id_company_owner']){
                            $opt .= '<option value="' . $o->id . '">' . $o->codename . '</option>';
                        }
                    }else{
                        $opt .= '<option value="' . $o->id . '">' . $o->codename . '</option>';
                    }
                }
                $f['input_field']['html'] .=  $opt . ' </select>';
            }

            $form_inflated[] = $f;
        }

        $data['form']=$form_inflated;

        $this->load->view('pages/master/master_view', $data);
    }

    // User Role
    public function user_role()
    {
        $table_name = App_Model::TBL_USR_ROLE;
        $table_fields = $this->user_role->get_data()->get()->list_fields();
        $fields_exception = ['id_usr_role', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'role_name' => [
                'text' => 'Name'
            ],
            'role_desc' => [
                'text' => 'Description'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'User Role', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' User Role'
            ]
        ]);

        $dataKey = 'id_usr_role';
        $data['header_title'] = 'Master - User Role';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $data['form'] = form_builder($table_name, $fields_exception, [
            'role_name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Role name is not valid!'
            ]
        ], [
            'role_name' => [
                'text' => 'Name<span style="color:red;">*</span>'
            ]
        ]);

        $this->load->view('pages/master/master_view', $data);
    }

    //Company Type
    public function company_type()
    {
        $table_name = App_Model::TBL_COMPANY_TYPE;
        $table_fields = $this->type->get_data()->get()->list_fields();
        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Tipe Perusahaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Tipe Perusahaan'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master -Tipe Perusahaan';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $form = form_builder($table_name, $fields_exception, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $form_inflated=[];
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'is_for_elmira') {
                $f['input_field']['html'] = '<select id="is_for_elmira" name="is_for_elmira" data-validation="required"
                data-validation-error-msg="Is for elmira tidak valid!" class="form-control" type="select">';
                $opt = '<option value="">Pilih</option>';
                $data_opt = [
                    [
                        'id'=>1,
                        'name'=>'YA'
                    ],
                    [
                        'id'=>0,
                        'name'=>'TIDAK'
                    ],
                ];

                foreach ($data_opt as $o) {
                    $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                }
                $f['input_field']['html'] .=  $opt . ' </select>';
            }

            $form_inflated[] = $f;
        }

        $data['form'] = $form_inflated;

        $this->load->view('pages/master/master_view', $data);
    }

    // Company Work Area
    public function company_work_area()
    {
        $table_name = App_Model::TBL_WORK_AREA;
        $table_fields = $this->workarea->get_data()->get()->list_fields();
        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Wilayah Kerja Perusahaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Tipe Perusahaan'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master -Tipe Wilayah Perusahaan';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $data['form'] = form_builder($table_name, $fields_exception, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $this->load->view('pages/master/master_view', $data);
    }

    // Country
    public function country()
    {
        $table_name = App_Model::TBL_COUNTRY;
        $table_fields = $this->country->get_data()->get()->list_fields();
        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Negara', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Negara'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master - Negara';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form
        $data['form'] = form_builder($table_name, $fields_exception, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ]
        ]);

        $this->load->view('pages/master/master_view', $data);
    }

    // Country Province
    public function country_province()
    {
        $table_name = App_Model::TBL_COUNTRY_PROVINCE;
        $table_fields = $this->province->get_data()->get()->list_fields();
        $fields_exception = ['id', 'id_country', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama Provinsi'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'country_name' => [
                'text' => 'Negara'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Provinsi', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Negara'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master - Provinsi';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data_province';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;


        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];
        // Form
        $form_inflated = [];
        $form = form_builder($table_name, $fields_exception, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama Provinsi'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'id_country' => [
                'text' => 'Negara<span style="color:red;">*</span>'
            ]
        ]);

        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_country') {
                $f['input_field']['html'] = '<select id="id_country" name="id_country" data-validation="required"
                data-validation-error-msg="Negara tidak valid!" class="form-control" type="select">';
                $opt = '<option value="">Pilih</option>';
                $data_opt = $this->country->get_data()->get()->result();

                foreach ($data_opt as $o) {
                    $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                }
                $f['input_field']['html'] .=  $opt . ' </select>';
            }

            $form_inflated[] = $f;
        }

        $data['form'] = $form_inflated;

        $this->load->view('pages/master/master_view', $data);
    }

    // Company Competency
    public function company_competency()
    {
        $table_name = App_Model::TBL_COMPANY_COMPETENCY;
        $table_fields = $this->competency->get_data()->get()->list_fields();
        $fields_exception = ['id', 'id_company_type', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'company_type' => [
                'text' => 'Tipe Perusahaan'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Kompetensi', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Tipe Perusahaan'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master -Tipe Perusahaan';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data_competency';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data';
        $data['add_url'] = 'master/add_data';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;


        $fields_exception1 = ['id', 'updated_at', 'deleted_at', 'created_at'];
        // Form
        $form = form_builder($table_name, $fields_exception1, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'id_company_type' => [
                'text' => 'Tipe Perusahaan<span style="color:red;">*</span>'
            ]
        ]);

        $form_inflated = [];
        $data_opt = $this->type->get_data()->get()->result();
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_company_type') {
                $f['input_field']['html'] = '<select
                data-validation="required" data-validation-error-msg="Tipe perusahaan tidak valid!" class="form-control" type="select" id="id_company_type" name="id_company_type" >
                <option value="">Pilih</option>';
                if ($data_opt != null) {
                    $opt = '';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt;
                }
                $f['input_field']['html'] .= '</select>';
            }
            $form_inflated[] = $f;
        }

        $data['form'] = $form_inflated;

        $this->load->view('pages/master/master_view', $data);
    }

    //Company Sub Competency
    public function company_sub_competency()
    {
        $table_name = App_Model::TBL_COMPANY_SUB_COMPETENCY;
        $table_fields = $this->sub_competency->get_data()->get()->list_fields();
        $fields_exception = ['id', 'id_company_competency', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Sub Kompetensi'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'competency_name' => [
                'text' => 'Kompetensi'
            ], 'id_group' => [
                'text' => 'Grup'
            ]
        ]);

        $this->set_page_title('pe-7s-user', 'Sub Kompetensi', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Sub Kompetensi'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master -Sub Kompetensi';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data_sub_competency';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'master/edit_data_company_with_attachment';
        $data['add_url'] = 'master/add_data_company_with_attachment';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;


        $fields_exception1 = ['id', 'updated_at', 'deleted_at', 'created_at'];
        // Form
        $form = form_builder($table_name, $fields_exception1, [
            'name' => [
                'data-validation' => 'required',
                'data-validation-error-msg' => 'Nama tidak valid!',
                'placeholder' => 'Nama'
            ],
            'description' => [
                'placeholder' => 'Deskripsi'
            ]
        ], [
            'name' => [
                'text' => 'Nama<span style="color:red;">*</span>'
            ],
            'description' => [
                'text' => 'Deskripsi'
            ],
            'id_company_competency' => [
                'text' => 'Kompetensi<span style="color:red;">*</span>'
            ],
            'id_group' => [
                'text' => 'Grup<span style="color:red;">*</span>'
            ],
            'attachment' => [
                'text' => 'Logo<span style="color:red;">*</span>'
            ]
        ]);

        $form_inflated = [];
        $data_opt = $this->competency->get_data()->get()->result();
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_company_competency') {
                $f['input_field']['html'] = '<select
                data-validation="required" data-validation-error-msg="Sub Kompetensi tidak valid!" class="form-control" type="select" id="id_company_competency" name="id_company_competency" >
                <option value="">Pilih</option>';
                if ($data_opt != null) {
                    $opt = '';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt;
                }
                $f['input_field']['html'] .= '</select>';
            }
            if ($f['input_field']['attr']['id'] == 'id_group') {
                $f['input_field']['html'] = '<select
                data-validation="required" class="form-control" type="select" id="id_group" name="id_group" >
                <option value="">Pilih</option>
                <option value="1">Barang</option>
                <option value="2">Jasa</option>
                <option value="3">Warung Pangan</option>';
                $f['input_field']['html'] .= '</select>';
            }

            if ($f['input_field']['attr']['id'] == 'attachment') {
                $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                data-validation="required mime size"
                data-validation-max-size="2M"
                data-validation-allowing="png, jpeg, jpg"  />
                <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
            }
            $form_inflated[] = $f;
        }
        $data['render_column_modifier'] = '{
            id_group:{
                render:function(e){
                    var view=`Barang`;
                    if(e.id_group==1){
                        view=`Barang`;
                    }else if(e.id_group==2){
                        view=`Jasa`;
                    }else if(e.id_group==3){
                        view=`Warung Pangan`;
                    }
                    return view;
                }
            },
            attachment:{
                render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
            }
        }';

        $data['add_scripts'] = [
            base_url('assets/js/page/company_attachment.js')
        ];


        $data['form'] = $form_inflated;

        $this->load->view('pages/master/master_view', $data);
    }

    public function required_form_rule()
    {
        if ($this->is_as_admin()) {

            $table_name = App_Model::TBL_REQUIRED_FORM;
            $table_fields = $this->master->get_required_form()->list_fields();
            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at', 'id_usr_role'];
            $table_header = get_header($table_fields, $fields_exception, [
                'role_name' => [
                    'text' => 'Role'
                ],
                'tbl_name' => [
                    'text' => 'Tabel'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Required Form Rule', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Required Form Rule'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Master - Rerquired Form Rule';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'master/get_data_required_form';
            $data['delete_url'] = 'master/delete_data';
            $data['update_url'] = 'master/edit_data_required_form';
            $data['add_url'] = 'master/add_data_required_form';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'minimum' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Minimum Data tidak valid!',
                    'placeholder' => 'Minimum Data'
                ],
                'title' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Title tidak valid!',
                    'placeholder' => 'Title'
                ],
            ], [
                'id_usr_role' => [
                    'text' => 'Role<span style="color:red;">*</span>'
                ],
                'tbl_name' => [
                    'text' => 'Tabel<span style="color:red;">*</span>'
                ],
                'minimum' => [
                    'text' => 'Minimum Data<span style="color:red;">*</span>'
                ],
                'title' => [
                    'text' => 'Title<span style="color:red;">*</span>'
                ],
                'description' => [
                    'text' => 'Deskripsi'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_usr_role') {

                    $f['input_field']['html'] = '<select type="select" id="id_usr_role" name="id_usr_role" class="form-control select2" data-validation="required"
                    data-validation-error-msg="Role tidak valid!" >';
                    $data_opt = $this->user_role->get_data()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id_usr_role . '">' . $o->role_name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }
                if ($f['input_field']['attr']['id'] == 'tbl_name') {

                    $f['input_field']['html'] = '<select type="select" id="tbl_name" name="tbl_name" class="form-control select2" data-validation="required"
                    data-validation-error-msg="Tabel tidak valid!" >';
                    $data_opt = $this->master->get_db_tables($this->db->database, [], [
                        "table_name like 'company%'",
                    ])->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->table_name . '">' . ucwords(str_replace('company', '', str_replace('_', ' ', $o->table_name)))   . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }

                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;
            $data['render_column_modifier'] = '{
                tbl_name:{
                    render:function(data){
                        return `<span style="text-transform:capitalize;">${data.tbl_name.replace("company","").replace(/_/g," ")}</span>`;
                    }
                },
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Not Authorized!');
            window.location.href='" . site_url('app') . "';
            location.reload();
            </script>";
        }
    }




    // Custom get_data
    public function get_data_province()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_country = $this->secure_input($this->input->get('id_country'));
        echo json_encode($this->province->get_data($id, $id_country)->get()->result());
    }
    public function get_data_city()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_country_province = $this->secure_input($this->input->get('id_country_province'));
        echo json_encode($this->city->get_data($id, $id_country_province)->get()->result());
    }
    public function get_data_required_form()
    {
        $id = $this->secure_input($this->input->get('id'));
        $where = [];
        if ($id != null) {
            $where = [
                'id' => $id
            ];
        }
        echo json_encode($this->master->get_required_form($where)->result());
    }

    public function get_data_competency()
    {
        $id = $this->secure_input($this->input->get('id'));
        echo json_encode($this->competency->get_data($id)->get()->result());
    }

    public function get_data_sub_competency()
    {
        $id_competency = $this->secure_input($this->input->get('id_competency'));
        $id = $this->secure_input($this->input->get('id'));
        $where = [
            'a.id' => $id,
            'id_company_competency' => $id_competency
        ];
        echo json_encode($this->sub_competency->get_data($where)->get()->result());
    }

    public function add_data_required_form()
    {
        $is_duplicate = $this->db
            ->where('id_usr_role', $this->input->post('id_usr_role'))
            ->where('tbl_name', $this->input->post('tbl_name'))
            ->where('deleted_at is null')
            ->get(App_Model::TBL_REQUIRED_FORM)
            ->num_rows() > 0;
        if ($is_duplicate) {
            echo json_encode([
                'success' => false,
                'result' => 'Data tidak boleh duplikat!'
            ]);
        } else {
            $this->add_data();
        }
    }

    public function edit_data_required_form()
    {
        $this_data = $this->db->where('id', $this->input->post('id'))
            ->get(App_Model::TBL_REQUIRED_FORM)->row();

        $edit = $this->edit_data(false);
        if ($edit['success']) {
            $is_duplicate = $this->db
                ->where('id_usr_role', $this->input->post('id_usr_role'))
                ->where('tbl_name', $this->input->post('tbl_name'))
                ->get(App_Model::TBL_REQUIRED_FORM)
                ->num_rows() > 1;
            if ($is_duplicate) {

                $this->db->where('id', $this->input->post('id'))
                    ->update(
                        App_Model::TBL_REQUIRED_FORM,
                        [
                            'id_usr_role' => $this_data->id_usr_role,
                            'tbl_name' => $this_data->tbl_name,
                            'minimum' => $this_data->minimum,
                            'title' => $this_data->title,
                            'description' => $this_data->description
                        ]
                    );
                echo json_encode([
                    'success' => false,
                    'result' => 'Gagal simpan, data setelah diedit menjadi duplilkat!'
                ]);
            } else {
                echo json_encode($edit);
            }
        } else {
            echo json_encode([
                'success' => false
            ]);
        }
    }


    public function add_data()
    {
        echo json_encode(add_data());
    }

    public function get_data()
    {
        echo json_encode(get_data());
    }

    public function edit_data($return_json = true)
    {
        if ($return_json) {
            echo json_encode(edit_data());
        } else {
            return edit_data();
        }
    }

    public function delete_data()
    {
        echo json_encode(delete_data());
    }

    public function add_data_company_with_attachment($isAttachmentRequird = true)
    {
        $do_upload = $this->upload_company_attachment($isAttachmentRequird);
        $result = [
            'sucesss' => false,
            'result' => 'Add data failed!'
        ];
        if ($do_upload['success']) {
            if (add_data(null, ['attachment' => $do_upload['file_data']['file_name']])['success']) {
                $result['success'] = true;
                $result['result'] = 'Add data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }

    public function edit_data_company_with_attachment($isAttachmentRequird = false)
    {
        $do_upload = $this->upload_company_attachment($isAttachmentRequird);
        $result = [
            'success' => false,
            'result' => 'Edit data failed!'
        ];
        if ($do_upload['success']) {
            if (edit_data(null, ['attachment' => $do_upload['file_data']['file_name']])['success']) {
                $result['success'] = true;
                $result['result'] = 'Edit data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }
}
