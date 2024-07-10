<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'add', 'id_role' => [1]],
                ['method' => 'edit'],
                ['method' => 'get'],
                ['method' => 'get_ssp'],
                ['method' => 'get_manage_account'],
                ['method' => 'manage_account'],
                ['method' => 'unblockUser'],
            ]
        ]);

        $this->load->model('User_model', 'user');
    }

    public function index()
    {
        $this->view_user();
    }

    public function unblockUser(){
        $response=
        [
            'success'=>false,
            'message'=>'Operation failed, please try again'
        ];

        $idUser=$this->input->post('idUser');
        if($idUser!=null){
            $dataUser=$this->db->where('id_user',$idUser)->get('sys_user')->row();
            if($dataUser!=null){
                //check last user status
                $lastStatus=$this->db->query('SELECT b.status_name,a.id_usr_status 
                FROM sys_login_session a
                inner join sys_usr_status b on b.id_usr_status=a.id_usr_status  
                WHERE a.id_usr_status!=3
                AND a.identity=?
                ORDER BY a.created_at DESC
                limit 1',[$dataUser->email])->row();

                if($lastStatus!=null){
                    $response['success']=$this->db->update('sys_user',[
                        'id_usr_status'=>$lastStatus->id_usr_status,
                        'login_attempt'=>0
                    ],
                    [
                        'id_user'=>$idUser
                    ]);

                    if($response['success']) $response['message']='Operation success!';
                }
            }


        }
        echo json_encode($response);
    }

    // User
    public function view_user()
    {
        $table_name = App_Model::TBL_USER;

        $table_fields = $this->user->get_data()->get()->list_fields();

        $fields_exception = ['id_company_owner','blacklist_note','is_blacklisted','photo', 'login_attempt', 'username', 'password', 'id_user', 'id_usr_status', 'id_usr_role', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'role_name' => [
                'text' => 'Role'
            ],
            'status_name' => [
                'text' => 'Status'
            ],
            'last_login' => [
                'text' => 'Terakhir Login'
            ],
        ]);

        $this->set_page_title('pe-7s-user', 'User', [
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
                'label' => ' User'
            ]
        ]);

        $dataKey = 'id_user';
        $data['header_title'] = 'Master - User';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'user/get';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'user/edit';
        $data['add_url'] = 'user/add';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        //ServerSide
        $data['ssp'] = 'true';
        $data['ssp_url'] = 'user/get_ssp';

        // Form
        $fields_exception_form = ['blacklist_note','is_blacklisted','photo', 'username', 'id_user', 'updated_at', 'deleted_at', 'created_at'];
        $form = form_builder(
            $table_name,
            $fields_exception_form,
            [
                'email' => [
                    'data-validation' => 'required email',
                    'data-validation-error-msg' => "Email is not valid!"
                ],
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => "Name is not valid!"
                ],
                'id_company_owner' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => "Pemilik vendor is not valid!"
                ]
            ],
            [
                'id_usr_status' => [
                    'text' => 'User Status',
                ],
                'id_usr_role' => [
                    'text' => 'User Role',
                ],
                'branch_code' => [
                    'text' => 'Target Cabang',
                ],
                'login_attempt' => [
                    'text' => 'Sesi Login',
                ],
                'id_company_owner' => [
                    'text'=>'Pemilik Vendor'
                ]
            ]
        );

        $form_inflated = [];
        $i = 0;
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_usr_status') {
                $f['input_field']['html'] = '<select data-validation="required" data-validation-error-msg="Please choose user status" type="select" name="id_usr_status" id="id_usr_status" class="col-xs-10 form-control select2">
                <option value="">Pilih</option>';
                $opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_USR_STATUS)->result();

                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id_usr_status . '">' . $o->status_name . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            } else if ($f['input_field']['attr']['id'] == 'id_usr_role') {
                $f['input_field']['html'] = '<select data-validation="required" data-validation-error-msg="Please choose user role" type="select" name="id_usr_role" id="id_usr_role" class="col-xs-10 form-control select2">
                <option value="">Pilih</option>';
                $opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_USR_ROLE)->result();

                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id_usr_role . '">' . $o->role_name . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            } else if ($f['input_field']['attr']['id'] == 'password') {
                $f['input_field']['html'] = '
                <div class="input-group">
                    <input  data-validation-error-msg="Password isn\'t valid!" data-validation="required" class="form-control" style="width:90% !important;" id="password_confirmation" name="password_confirmation" type="password">
                    <div class="input-group-addon" >
                    <center>
                        <span style="height:100%;font-size:1.4em;margin-top:10px;" class="col-md-4 fa fa-fw fa-eye toggle-password" toggle="#password_confirmation"></span>
                    </center>
                    </div>
                </div>';
            } else if ($f['input_field']['attr']['id'] == 'branch_code') {
                $f['input_field']['html'] = '<select type="select" name="branch_code" id="branch_code" class="col-xs-10 form-control select2">
                <option value="0">Tidak Ada</option>';
                $opt = $this->db->where('deleted_at is null')->get('m_branch_code')->result();
                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            }
            else if ($f['input_field']['attr']['id'] == 'id_company_owner') {
                $f['input_field']['html'] = '
                <select type="select" name="id_company_owner" id="id_company_owner" class="col-xs-10 form-control select2">
                ';
                $opt = $this->db->where('deleted_at is null')->get('m_company')->result();
                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id . '">' . $o->codename . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            }
            $form_inflated[$i] = $f;
            $i++;
        }
        $form_inflated[$i] = [
            'label' => [
                'text' => 'Password Confirmation'
            ],
            'input_field' => [
                'html' => '<div class="input-group">
                <input data-validation="required confirmation" data-validation-error-msg="Password confirmation isn\'t valid!" class="form-control" style="width:90% !important;" id="password" name="password" type="password">
                <div class="input-group-addon">
                <center>
                    <span style="height:100%;font-size:1.4em;margin-top:10px;" class="col-md-4 fa fa-fw fa-eye toggle-password" toggle="#password"></span>
                </center>
                </div>
            </div>'
            ]
        ];
        $data['form'] = $form_inflated;
        $data['form_note'] = '<div style="font-size:10pt;">
        Catatan:<br>
        <b>Cara membuka blokir user</b>
        <ol>
            <li>Lihat status user sebelum terblokir</li>
            <li>Ubah status status user sesuai status sebelum terblokir</li>
            <li>Ubah sesi login menjadi 0</li>
        </ol>
        </div>';
        $data['render_column_modifier'] = '{
            status_name:{
                render:function(val){
                    var renderView=`<span>${val.status_name}</span>`;
                    if(val.id_usr_status==3){
                        renderView+=`<br><button class="btn btn-sm btn-block btn-danger" onclick="unblockUser(${val.id_user})">Unblock User</button>`;
                    }
                    return renderView;
                }
            }
        }';
        $data['add_scripts'] = [
            base_url('assets/js/page/user.js'),
        ];

        $this->load->view('pages/master/master_view', $data);
    }

    public function manage_account()
    {
        $table_name = App_Model::TBL_USER;

        $table_fields = $this->user->get_data()->get()->list_fields();

        $fields_exception = ['photo', 'login_attempt', 'username', 'password', 'id_user', 'id_usr_status', 'id_usr_role', 'updated_at', 'deleted_at', 'created_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'name' => [
                'text' => 'Nama'
            ],
            'role_name' => [
                'text' => 'Role'
            ],
            'status_name' => [
                'text' => 'Status'
            ],
            'last_login' => [
                'text' => 'Terakhir Login'
            ],
        ]);

        $this->set_page_title('pe-7s-user', 'User', [
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
                'label' => ' User'
            ]
        ]);

        $dataKey = 'id_user';
        $data['header_title'] = 'Akun Anda';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'user/get_manage_account';
        $data['delete_url'] = '';
        $data['update_url'] = 'user/edit';
        $data['add_url'] = '';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        $data['action_add'] = 'disabled';
        $data['action_delete'] = 'disabled';

        // Form
        $fields_exception_form = ['photo', 'login_attempt', 'username', 'id_user', 'updated_at', 'deleted_at', 'created_at'];
        $form = form_builder(
            $table_name,
            $fields_exception_form,
            [
                'email' => [
                    'data-validation' => 'required email',
                    'data-validation-error-msg' => "Email is not valid!"
                ],
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => "Name is not valid!"
                ]
            ],
            [
                'id_usr_status' => [
                    'text' => 'User Status',
                ],
                'id_usr_role' => [
                    'text' => 'User Role',
                ]
            ]
        );

        $form_inflated = [];
        $i = 0;
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_usr_status') {
                $f['input_field']['html'] = '<select disabled data-validation="required" data-validation-error-msg="Please choose user status" type="select" name="id_usr_status" id="id_usr_status" class="col-xs-10 form-control">
                <option value="">Pilih</option>';
                $opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_USR_STATUS)->result();

                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id_usr_status . '">' . $o->status_name . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            } else if ($f['input_field']['attr']['id'] == 'id_usr_role') {
                $f['input_field']['html'] = '<select disabled data-validation="required" data-validation-error-msg="Please choose user role" type="select" name="id_usr_role" id="id_usr_role" class="col-xs-10 form-control">
                <option value="">Pilih</option>';
                $opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_USR_ROLE)->result();

                $option = '';
                foreach ($opt as $o) {
                    $option .= '<option value="' . $o->id_usr_role . '">' . $o->role_name . '</option>';
                }
                $f['input_field']['html'] .= $option . '</select>';
            } else if ($f['input_field']['attr']['id'] == 'password') {
                $f['input_field']['html'] = '
                <div class="input-group">
                    <input  data-validation-error-msg="Password isn\'t valid!" data-validation="required" class="form-control" style="width:90% !important;" id="password_confirmation" name="password_confirmation" type="password">
                    <div class="input-group-addon" >
                    <center>
                        <span style="height:100%;font-size:1.4em;margin-top:10px;" class="col-md-4 fa fa-fw fa-eye toggle-password" toggle="#password_confirmation"></span>
                    </center>
                    </div>
                </div>';
            }
            $form_inflated[$i] = $f;
            $i++;
        }
        $form_inflated[$i] = [
            'label' => [
                'text' => 'Password Confirmation'
            ],
            'input_field' => [
                'html' => '<div class="input-group">
                <input data-validation="required confirmation" data-validation-error-msg="Password confirmation isn\'t valid!" class="form-control" style="width:90% !important;" id="password" name="password" type="password">
                <div class="input-group-addon">
                <center>
                    <span style="height:100%;font-size:1.4em;margin-top:10px;" class="col-md-4 fa fa-fw fa-eye toggle-password" toggle="#password"></span>
                </center>
                </div>
            </div>'
            ]
        ];
        $data['form'] = $form_inflated;
        $data['add_scripts'] = [
            base_url('assets/js/page/user.js'),
        ];

        $this->load->view('pages/master/master_view', $data);
    }

    public function get()
    {
        $id = $this->input->get('id_user');
        echo json_encode($this->user->get_data($id)->where('a.deleted_at is null')->get()->result());
    }

    public function get_ssp()
    {
        $id = $this->input->get('id_user');
        $sqlWhere = [];
        if ($id != null) array_push($sqlWhere, "id_user=$id");
        echo json_encode($this->user->get_data_ssp($sqlWhere));
    }

    public function get_manage_account()
    {
        $id = $this->session->userdata('user')['id_user'];
        echo json_encode($this->user->get_data($id)->where('a.deleted_at is null')->get()->result());
    }

    public function add()
    {
        echo json_encode(add_data(null, [
            'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
        ]));
    }

    public function edit()
    {
        $new_password = $this->input->post('password');
        if ($new_password != null) {
            echo json_encode(edit_data(null, [
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
            ]));
        } else {
            echo json_encode(edit_data(null, [
                'password' => null
            ]));
        }
    }
}
