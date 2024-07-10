<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verification extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get_data'],
                ['method' => 'get_verification_history'],
                ['method' => 'get_profile_basic'],
                ['method' => 'edit_data', 'id_role' => [1, 5]],
                ['method' => 'get_company_contact'],
                ['method' => 'get_company_pic'],
                ['method' => 'get_company_legal_born_license'],
                ['method' => 'get_company_legal_domicile'],
                ['method' => 'get_company_legal_npwp'],
                ['method' => 'get_company_legal_nib'],
                ['method' => 'get_company_legal_tdp'],
                ['method' => 'get_company_legal_siup'],
                ['method' => 'get_company_finance_bank'],
                ['method' => 'test_sap'],
                ['method' => 'data_perusahaan'],
                ['method' => 'get_data_perusahaan'],
                ['method' => 'view_data_perusahaan'],
                ['method' => 'verify_data_perusahaan'],
                ['method' => 'get_history_verification'],
                ['method' => 'update_sap']
            ]
        ]);
        $this->load->model('Company_model', 'company');
        $this->load->model('Company_type_model', 'company_type');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('SAP_model', 'sap');
        $this->load->model('Email_model', 'email_helper');
        $this->load->model('Master_model', 'master');
        $this->load->model('Verification_model', 'verification');
    }


    public function data_perusahaan()
    {
        $this->load->view('pages/required_info/verification');
    }

    public function view_data_perusahaan()
    {
        $id_user = $this->input->get('id_user');
        $user = $this->db->where('id_user', $id_user)->get(App_Model::TBL_USER)->row();
        $data['id_user'] = $user->id_user;
        if ($user->id_usr_role == App_Model::ROLE_VENDOR_PERSONAL) {
            $this->load->view('pages/required_info/registration_person', $data);
        } else {
            $this->load->view('pages/required_info/registration_company', $data);
        }
    }

    public function get_data_perusahaan()
    {
        $jenis_vendor = $this->input->get('jenis_vendor');
        $f_status = $this->input->get('f_status');
        $f_company_owner = $this->input->get('f_company_owner');

        if ($jenis_vendor != null) {
            $this->db->where('c.id_usr_role', $jenis_vendor);
        }
        
        if ($f_company_owner != null) {
            $this->db->where('a.id_company_owner', $f_company_owner);
        }
        
        if($f_status!='Rejected'){
            
            $data = $this->db->select('a.*,c.id_usr_role,c.id_usr_status,b.address, if(c.id_usr_role=2,b.email,c.email) as email
            ,b.phone,d.role_name, e.name as group_name, e.description as group_description')
                ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
                ->join(App_Model::TBL_COMPANY_CONTACT . ' b', 'a.id=b.id_company')
                ->join(App_Model::TBL_USER . ' c', 'a.id_user=c.id_user')
                ->join(App_Model::TBL_USR_ROLE . ' d', 'd.id_usr_role=c.id_usr_role')
                ->join(App_Model::TBL_GROUP_VENDOR . ' e', 'a.id_group=e.id')
                ->join(App_Model::TBL_COMPANY_LEGAL_NPWP . ' f', 'f.id_company=a.id')
                ->where('a.deleted_at is null')
                ->where('( ( c.id_usr_status=5 and a.verification_status!=\'Rejected\'  ) or (c.id_usr_status=6 and a.verification_status!=\'Verified\' and a.verification_status!=\'Rejected\' ) or ( c.id_usr_status=2 and a.verification_status!=\'Verified\' and a.verification_status!=\'Rejected\' ) )')
                ->group_by('a.id')
                ->get()
                ->result();
        }
        else{

        $data = $this->db->select('a.*,c.id_usr_role,c.id_usr_status,b.address, if(c.id_usr_role=2,b.email,c.email) as email
        ,b.phone,d.role_name, e.name as group_name, e.description as group_description')
            ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->join(App_Model::TBL_COMPANY_CONTACT . ' b', 'a.id=b.id_company')
            ->join(App_Model::TBL_USER . ' c', 'a.id_user=c.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' d', 'd.id_usr_role=c.id_usr_role')
            ->join(App_Model::TBL_GROUP_VENDOR . ' e', 'a.id_group=e.id')
            ->join(App_Model::TBL_COMPANY_LEGAL_NPWP . ' f', 'f.id_company=a.id')
            ->where('a.deleted_at is null')
            ->where('( ( c.id_usr_status=5 and a.verification_status=\'Rejected\'  ) or (c.id_usr_status=6 and a.verification_status=\'Rejected\' ) or ( c.id_usr_status=2 and a.verification_status=\'Rejected\' ) )')
            ->group_by('a.id')
            ->get()
            ->result();
        }

        echo json_encode($data);
    }

    public function verify_data_perusahaan()
    {
        $id_company = $this->input->post('id_company');
        $verification_status = $this->input->post('verification_status');
        $verification_note = $this->input->post('verification_note');

        $change_table_candidate = [
            App_Model::TBL_COMPANY_CONTACT,
            App_Model::TBL_COMPANY_PIC,
            App_Model::TBL_COMPANY_BORN_LICENSE,
            App_Model::TBL_COMPANY_LEGAL_DOMICILE,
            App_Model::TBL_COMPANY_LEGAL_NPWP,
            App_Model::TBL_COMPANY_LEGAL_SIUP,
            App_Model::TBL_COMPANY_LEGAL_TDP,
            App_Model::TBL_COMPANY_FINANCE_BANK,
        ];

        if ($verification_status == App_Model::VERIFICATION_STATUS_REJECTED) {

            $company = $this->db->where('id', $id_company)
                ->get(App_Model::TBL_COMPANY_PROFILE)->row();
            $user = $this->db->where('id_user', $company->id_user)
                ->get(App_Model::TBL_USER)->row();


            $this->db->where('id', $id_company)
                ->update(App_Model::TBL_COMPANY_PROFILE, [
                    'verification_status' => $verification_status,
                    'verification_note' => $verification_note,
                ]);

            foreach ($change_table_candidate as $tbl) {
                $this->db->where('id_company', $id_company)
                    ->update($tbl, [
                        'verification_status' => $verification_status,
                        'verification_note' => $verification_note,
                    ]);
            }

            if ($user->id_usr_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE) {

                $this->db->where('id_user', $company->id_user)->update(App_Model::TBL_USER, [
                    'id_usr_status' => App_Model::STAT_ACCOUNT_VERIFY_PROFILE,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $email_target = $user->email;
                $email_subject = 'Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $email_target,
                    $email_subject,
                    '<center>
                <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
                <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                <hr>
                <p> Terima kasih telah mendaftar dan melengkapi Informasi Wajib sebagai vendor pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                        . '. Proses verifikasi data Anda telah dilakukan dan berikut hasil verifikasi:.
                <br>
                <b>Status Verifikasi : </b>
                <span style="color:red;">' . $verification_status . '</span>
                <br/> 
                <b>Catatan Verifikasi : </b>
                <span>' . $verification_note . '</span>
                <br/>
                Anda dapat memperbaiki data Anda dalam waktu 2x24 Jam, jika Anda tidak melakukan perbaikan, sistem otomatis akan menghapus akun Anda. 
                Terima kasih.
                <br>
                <br>
                <b>Regards,
                <br><a href="' . site_url() . '">E-Procurement System</a>
                <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                </b>
                </p>'
                );

                $this->db->insert('verification_history', [
                    'verificator' => $this->session->userdata('user')['id_user'],
                    'data_id' => $id_company,
                    'data_main_table' => '-',
                    'verification_status' => $verification_status,
                    'verification_note' => $verification_note,
                ]);


                echo json_encode([
                    'success' => true,
                    'result' => 'Data verifikasi berhasil disimpan!'
                ]);
            } else {

                $this->db->insert('verification_history', [
                    'verificator' => $this->session->userdata('user')['id_user'],
                    'data_id' => $id_company,
                    'data_main_table' => '-',
                    'verification_status' => $verification_status,
                    'verification_note' => $verification_note,
                ]);
                echo json_encode([
                    'success' => true,
                    'result' => 'Data verifikasi berhasil disimpan!'
                ]);
            }
        } else if ($verification_status == App_Model::VERIFICATION_STATUS_VERIFIED) {


            $company = $this->db->where('id', $id_company)
                ->get(App_Model::TBL_COMPANY_PROFILE)->row();
            $user = $this->db->where('id_user', $company->id_user)
                ->get(App_Model::TBL_USER)->row();

            if ($user->id_usr_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE) {
                $sync_sap = $this->sap->input_sap($id_company, $user->id_usr_role);

                if ($sync_sap['success']) {


                    $this->db->where('id', $id_company)
                        ->update(App_Model::TBL_COMPANY_PROFILE, [
                            'verification_status' => $verification_status,
                            'verification_note' => $verification_note,
                        ]);

                    foreach ($change_table_candidate as $tbl) {
                        $this->db->where('id_company', $id_company)
                            ->update($tbl, [
                                'verification_status' => $verification_status,
                                'verification_note' => $verification_note,
                            ]);
                    }

                    $this->db->where('id_user', $company->id_user)->update(App_Model::TBL_USER, [
                        'id_usr_status' => App_Model::STAT_ACCOUNT_ACTIVE,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                    $email_target = $user->email;
                    $email_subject = 'Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
                    $this->email_helper->send_email(
                        $this->config->item('app_info')['identity']['name'],
                        $email_target,
                        $email_subject,
                        '<center>
                        <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
                        <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                        <hr>
                        <p> Terima kasih telah mendaftar dan melengkapi Informasi Wajib sebagai vendor pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                            . '. Proses verifikasi data Anda telah dilakukan dan berikut hasil verifikasi:.
                        <br>
                        <b>Status Verifikasi : </b>
                        <span style="color:red;">' . $verification_status . '</span>
                        <br/> 
                        <b>Catatan Verifikasi : </b>
                        <span>' . $verification_note . '</span>
                        <br/>  
                        Terima kasih.
                        <br>
                        <br>
                        <b>Regards,
                        <br><a href="' . site_url() . '">E-Procurement System</a>
                        <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                        </b>
                        </p>'
                    );

                    $this->db->insert('verification_history', [
                        'verificator' => $this->session->userdata('user')['id_user'],
                        'data_id' => $id_company,
                        'data_main_table' => '-',
                        'verification_status' => $verification_status,
                        'verification_note' => $verification_note,
                    ]);
                    $sync_sap['result'] = 'Data berhasil disimpan ke SAP!';
                    echo json_encode($sync_sap);
                } else {
                    echo json_encode($sync_sap);
                }
            } else {
                $update_sap = $this->sap->input_sap($id_company, $user->id_usr_role);
                if ($update_sap['success']) {

                    $this->db->where('id', $id_company)
                        ->update(App_Model::TBL_COMPANY_PROFILE, [
                            'verification_status' => $verification_status,
                            'verification_note' => $verification_note,
                        ]);

                    foreach ($change_table_candidate as $tbl) {
                        $this->db->where('id_company', $id_company)
                            ->update($tbl, [
                                'verification_status' => $verification_status,
                                'verification_note' => $verification_note,
                            ]);
                    }

                    $this->db->insert('verification_history', [
                        'verificator' => $this->session->userdata('user')['id_user'],
                        'data_id' => $id_company,
                        'data_main_table' => '-',
                        'verification_status' => $verification_status,
                        'verification_note' => $verification_note,
                    ]);
                    $sync_sap = $update_sap;
                } else {
                    $sync_sap = $update_sap;
                }
                echo json_encode($sync_sap);
            }
        }
    }


    public function update_sap()
    {

        $id_company = $this->input->post('id_company');
        $no_vendor = $this->input->post('no_vendor');
        $verification_status = $this->input->post('verification_status');
        $verification_note = $this->input->post('verification_note');
        $change_table_candidate = [
            App_Model::TBL_COMPANY_CONTACT,
            App_Model::TBL_COMPANY_PIC,
            App_Model::TBL_COMPANY_BORN_LICENSE,
            App_Model::TBL_COMPANY_LEGAL_DOMICILE,
            App_Model::TBL_COMPANY_LEGAL_NPWP,
            App_Model::TBL_COMPANY_LEGAL_SIUP,
            App_Model::TBL_COMPANY_LEGAL_TDP,
            App_Model::TBL_COMPANY_FINANCE_BANK,
        ];


        $profile = $this->db->where('id', $id_company)->get(App_Model::TBL_COMPANY_PROFILE)->row();
        if($profile->id_group == 1) {
            $gl_number = '2011100002';
        } else {
            $gl_number = '2011100003';
        }
        $this->db->where('id_company', $id_company)->delete(App_Model::TBL_SAP_SYNC);
        // $this->db->where('id_company', $id_company)->update(App_Model::TBL_SAP_SYNC, [
        //     'deleted_at' => date('Y-m-d H:i:s')
        // ]);
        $this->db->insert(App_Model::TBL_SAP_SYNC, [
            'id_group' => $profile->id_group,
            'id_company' => $id_company,
            'id_sap' => $no_vendor,
            'vendor_gl_number' => $gl_number
        ]);

        $company = $this->db->where('id', $id_company)
            ->get(App_Model::TBL_COMPANY_PROFILE)->row();
        $user = $this->db->where('id_user', $company->id_user)
            ->get(App_Model::TBL_USER)->row();

        $update_sap = $this->sap->update_sap($id_company, $user->id_usr_role);
        if ($update_sap['success']) {

            $this->db->where('id', $id_company)
                ->update(App_Model::TBL_COMPANY_PROFILE, [
                    'verification_status' => $verification_status,
                    'verification_note' => $verification_note,
                ]);

            foreach ($change_table_candidate as $tbl) {
                $this->db->where('id_company', $id_company)
                    ->update($tbl, [
                        'verification_status' => $verification_status,
                        'verification_note' => $verification_note,
                    ]);
            }

            $this->db->insert('verification_history', [
                'verificator' => $this->session->userdata('user')['id_user'],
                'data_id' => $id_company,
                'data_main_table' => '-',
                'verification_status' => $verification_status,
                'verification_note' => $verification_note,
            ]);

            $this->db->where('id_user', $user->id_user)
                ->update(App_Model::TBL_USER, [
                    'id_usr_status' => 2
                ]);
        }

        echo json_encode($update_sap);
    }

    public function get_history_verification()
    {
        $id_company = $this->input->get('id_company');
        echo json_encode($this->db->where('data_id', $id_company)->get('verification_history')
            ->result());
    }

    // Profile Basic
    public function profile_basic()
    {
        $table_name = App_Model::TBL_COMPANY_PROFILE;

        $table_fields = $this->verification->get_profile_basic()->get()->list_fields();
        $fields_exception = [
            'id',
            'id_group',
            'id_user',
            'updated_at',
            'logo',
            'deleted_at',
            'verification_note',
            'created_at'
        ];
        $table_header = get_header($table_fields, $fields_exception, []);

        $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Profil Perusahaan'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Profil Dasar'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Verifikasi profil dasar';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'verification/get_profile_basic';
        $data['delete_url'] = '';
        $data['update_url'] = 'verification/edit_data';
        $data['add_url'] = '';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;

        // Form

        $fields_exception = ['id', 'id_user', 'logo', 'updated_at', 'deleted_at', 'created_at', 'verification_history'];

        $form_inflated = [];
        $form = form_builder($table_name, $fields_exception, [], [
            'id_company' => [
                'text' => 'Perusahaan<span style="color:red;">*</span>'
            ],
            'no' => [
                'text' => 'NIB<span style="color:red;">*</span>'
            ],
            'active_date' => [
                'text' => 'Tanggal Berlaku NIB<span style="color:red;">*</span>'
            ],
            'attachment' => [
                'text' => 'File<span style="color:red;">*</span>'
            ],
            'verification_status' => [
                'text' => 'Status Verifikasi<span style="color:red;">*</span>'
            ],
            'id_group' => [
                'text' => 'Kategori Perusahaan<span style="color:red;">*</span>'
            ]
        ]);

        $i = 0;
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'id_company') {
                $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                data-validation-error-msg="Perusahaan tidak valid!" >';
                $data_opt = $this->company->get();
                $opt = '<option value="">Pilih</option>';
                foreach ($data_opt as $o) {
                    $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                }
                $f['input_field']['html'] .= $opt . '</select>';
            }
            if ($f['input_field']['attr']['id'] == 'verification_status') {
                if (!$this->is_as_admin()) {
                    $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                    <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                } else {

                    $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                    data-validation-error-msg="Status verifikasi tidak valid!" >';
                    $data_opt = [
                        ['id' => 'Verified', 'name' => 'Verified'],
                        ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                        ['id' => 'Rejected', 'name' => 'Rejected']
                    ];
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }
            }
            if ($f['input_field']['attr']['id'] == 'verification_note') {
                if (!$this->is_as_admin()) {
                    $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                } else {
                    $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                }
            }

            if ($f['input_field']['attr']['id'] == 'id_group') {
                $data_opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_GROUP_VENDOR)->result();
                $f['input_field']['html'] = '<select disabled type="select" id="id_group" name="id_group" class="form-control" data-validation="required"
                 >';

                $opt = '<option value="">Pilih</option>';
                foreach ($data_opt as $o) {
                    $opt .= '<option value="' . $o->id . '">' . $o->name . ' (' . $o->description . ') </option>';
                }
                $f['input_field']['html'] .= $opt . '</select>';
            }

            $form_inflated[] = $f;
            $i++;
        }

        $data['form'] = $form_inflated;

        $data['add_scripts'] = [
            base_url('assets/js/page/verification.js')
        ];

        $data['render_column_modifier'] = '{
                logo:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/logo/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                company_profile:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                }
            }';


        $data['action_add'] = 'disabled';
        $data['action_delete'] = 'disabled';
        $this->load->view('pages/master/master_view', $data);
    }
    public function get_profile_basic()
    {
        $id = $this->input->get('id');
        $id_user = $this->input->get('id_user');
        echo json_encode($this->verification->get_profile_basic($id_user, $id)->get()->result());
    }
    public function get_verification_history()
    {
        $id = $this->input->get('id');
        $table = $this->input->get('table');
        echo json_encode(
            $this->db
                ->select('verification_history.*,sys_user.name as user_name,sys_user.email as user_email,sys_usr_role.role_name')
                ->where('data_id', $id)
                ->where('data_main_table', $table)
                ->join('sys_user', 'sys_user.id_user=verification_history.verificator')
                ->join('sys_usr_role', 'sys_user.id_usr_role=sys_usr_role.id_usr_role')
                ->order_by('verification_history.created_at', 'desc')
                ->get('verification_history')
                ->result()
        );
    }

    // Contact
    public function company_contact()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_CONTACT;
            if ($company != null) {
                $table_fields = $this->company->get_contact()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_contact()->get()->list_fields();
            }
            $fields_exception = ['id', 'building_no', 'updated_at', 'id_company', 'work_area', 'id_country', 'id_country_province', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'company_name' => [
                    'text' => 'Nama Perusahaan'
                ],
                'work_area_name' => [
                    'text' => 'Area Kerja'
                ],
                'country_name' => [
                    'text' => 'Negara'
                ],
                'address' => [
                    'text' => 'Alamat'
                ],
                'city' => [
                    'text' => 'Kota'
                ],
                'pos_code' => [
                    'text' => 'Kode Pos'
                ],
                'phone' => [
                    'text' => 'Telepon'
                ],
                'phone2' => [
                    'text' => 'Telepon2'
                ],
                'province_name' => [
                    'text' => 'Provinsi'
                ],
                'building_no' => [
                    'text' => 'No Bangunan/Rumah'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Kontak Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Kontak'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - Kontak';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_contact';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'building_no', 'deleted_at', 'created_at', 'verification_history'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'address' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Alamat tidak valid!',
                    'placeholder' => 'Alamat'
                ],
                'city' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kota tidak valid!',
                    'placeholder' => 'Kota'
                ],
                'pos_code' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kode Pos tidak valid!',
                    'placeholder' => 'Kode Pos'
                ],
                'phone' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nomor Telepon tidak valid!',
                    'placeholder' => 'Telepon'
                ],
                'id_country_province' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Provinsi tidak valid!',
                    'placeholder' => 'Provinsi'
                ],
                'email' => [
                    'data-validation' => 'required email',
                    'data-validation-error-msg' => 'Email tidak valid!',
                    'placeholder' => 'Email'
                ],
                'building_no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No Bangunan/Rumah tidak valid!',
                    'placeholder' => 'No Bangunan/Rumah'
                ]
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_country' => [
                    'text' => 'Negara<span style="color:red;">*</span>'
                ],
                'work_area' => [
                    'text' => 'Area Kerja<span style="color:red;">*</span>'
                ],
                'address' => [
                    'text' => 'Alamat<span style="color:red;">*</span>'
                ],
                'city' => [
                    'text' => 'Kota<span style="color:red;">*</span>'
                ],
                'phone' => [
                    'text' => 'Telepon<span style="color:red;">*</span>'
                ],
                'pos_code' => [
                    'text' => 'Kode Pos<span style="color:red;">*</span>'
                ],
                'building_no' => [
                    'text' => 'No Bangunan/Rumah<span style="color:red;">*</span>'
                ],
                'phone2' => [
                    'text' => 'Telepon2'
                ],
                'email' => [
                    'text' => 'Email<span style="color:red;">*</span>'
                ],
                'id_country_province' => [
                    'text' => 'Provinsi<span style="color:red;">*</span>'
                ],
                'main_contact' => [
                    'text' => 'Kontak Utama<span style="color:red;">*</span>'
                ],
            ]);

            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'work_area') {
                    $f['input_field']['html'] = '<select type="select" id="work_area" name="work_area" class="form-control" data-validation="required"
                    data-validation-error-msg="Area Kerja tidak valid!" >';
                    $data_opt = $this->workarea->get_data()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }

                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'id_country') {
                    $f['input_field']['html'] = '<select type="select" id="id_country" name="id_country" class="form-control" data-validation="required"
                    data-validation-error-msg="Negara tidak valid!" >';
                    $data_opt = $this->country->get_data()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' .  $o->name . '</option>';
                    }

                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'id_country_province') {
                    $f['input_field']['html'] = '<select type="select" id="id_country_province" name="id_country_province" class="form-control" data-validation="required"
                    data-validation-error-msg="Provinsi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $f['input_field']['html'] .= $opt
                        . '</select>';
                }
                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'main_contact') {
                    $f['input_field']['html'] = '<select hidden class="form-control" id="main_contact" name="main_contact">
                        <option value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>';
                }

                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_legal_domicile.js'),
                base_url('assets/js/page/verification.js')
            ];
            $data['render_column_modifier'] = '{
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    },
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                },
                main_contact:{
                    render:function(data){
                        return (data.main_contact==1)?`<span class="badge badge-success">Ya</span>`:`<span class="badge badge-danger">Tidak</span>`;
                    }
                }
            }';


            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    //PIC
    public function company_pic()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_PIC;
            if ($company != null) {
                $table_fields = $this->company->get_pic()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_pic()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_company', 'work_area', 'id_country', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'company_name' => [
                    'text' => 'Nama Perusahaan'
                ],
                'name' => [
                    'text' => 'Nama'
                ],
                'position' => [
                    'text' => 'Posisi/Jabatan'
                ],
                'office_phone' => [
                    'text' => 'Telepon Kantor'
                ],
                'mobile_phone' => [
                    'text' => 'No. Handphone'
                ],
                'email' => [
                    'text' => 'Email'
                ],
                'attachment' => [
                    'text' => 'Surat Kuasa'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Kontak Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' PIC'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - PIC';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_pic';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at', 'verification_history'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama tidak valid!',
                    'placeholder' => 'Nama'
                ],
                'position' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Posisi/Jabatan tidak valid!',
                    'placeholder' => 'Posisi/Jabatan'
                ],
                'office_phone' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Telepon Kantor tidak valid!',
                    'placeholder' => 'Telepon Kantor'
                ],
                'mobile_phone' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nomor Hanphone tidak valid!',
                    'placeholder' => 'Nomor Handphone'
                ],
                'email' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required email',
                    'data-validation-error-msg' => 'Email tidak valid!',
                    'placeholder' => 'Email'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'name' => [
                    'text' => 'Nama<span style="color:red;">*</span>'
                ],
                'position' => [
                    'text' => 'Posisi<span style="color:red;">*</span>'
                ],
                'office_phone' => [
                    'text' => 'Telepon Kantor<span style="color:red;">*</span>'
                ],
                'mobile_phone' => [
                    'text' => 'Nomor Handphone<span style="color:red;">*</span>'
                ],
                'email' => [
                    'text' => 'Email<span style="color:red;">*</span>'
                ],
            ]);

            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select disabled type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input hidden is-mandatory="true" disabled type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"/>';
                }
                if ($f['input_field']['attr']['id'] == 'position_type') {
                    $f['input_field']['html'] = '<select disabled type="select" id="position_type" name="position_type" class="form-control" data-validation="required"
                        data-validation-error-msg="Jenis posisi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $opt .= '<option value="1">Direksi/Pemilik Usaha</option>';
                    $opt .= '<option value="2">Lainnya</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                }
                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['render_column_modifier'] = '{
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                },attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
            }';


            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';
            $data['add_scripts'] = [
                base_url('assets/js/page/verification.js'),
                base_url('assets/js/page/company_legal_pic.js'),
                base_url('assets/js/page/company_attachment.js'),
            ];
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    //Born License
    public function company_born_license()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_BORN_LICENSE;
            if ($company != null) {
                $table_fields = $this->company->get_born_license()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_born_license()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'attachment' => [
                    'text' => 'file'
                ],
                'company_name' => [
                    'text' => 'Nama Perusahaan'
                ],
                'type' => [
                    'text' => 'Tipe'
                ],
                'no' => [
                    'text' => 'Nomor Akta'
                ],
                'created_date' => [
                    'text' => 'Dibuat Tgl'
                ],
                'country_news_date' => [
                    'text' => 'Berita Negara'
                ],
                'judical_approval_date' => [
                    'text' => 'Pengesahan Hakim'
                ],
                'judical_approval_no' => [
                    'text' => 'No Pengesahan'
                ],
                'notary' => [
                    'text' => 'Notaris'
                ],
                'notary_address' => [
                    'text' => 'Alamat Notaris'
                ],
                'notary_telp' => [
                    'text' => 'No.Telp Notaris'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Akta Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Akta'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - Akta';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_born_license';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at', 'verification_history'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No Akta tidak valid!',
                    'placeholder' => 'No Akta'
                ],
                'created_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal pembuatan akta tidak valid!',
                    'placeholder' => 'Tanggal pembuatan'
                ]
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'type' => [
                    'text' => 'Tipe<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'No. Akta<span style="color:red;">*</span>'
                ],
                'created_date' => [
                    'text' => 'Tanggal Pembuatan<span style="color:red;">*</span>'
                ],
                'country_news_date' => [
                    'text' => 'Berita Negara'
                ],
                'judical_approval_date' => [
                    'text' => 'Pengesahan Hakim'
                ],
                'judical_approval_no' => [
                    'text' => 'No Pengesahan'
                ],
                'notary' => [
                    'text' => 'Notaris'
                ],
                'notary_address' => [
                    'text' => 'Alamat Notaris'
                ],
                'notary_telp' => [
                    'text' => 'No.Telp Notaris'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ]
            ]);

            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'type') {

                    $f['input_field']['html'] = '<select type="select" id="type" name="type" class="form-control" data-validation="required"
                    data-validation-error-msg="Tipe tidak valid!" >';
                    $opt = '<option value="">Pilih</option>
                    <option value="Pendirian">Pendirian</option>
                    <option value="Perubahan">Perubahan</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                }

                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                }

                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }

                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;

            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js'),
                // base_url('assets/js/page/verification.js')
            ];
            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_legal_domicile()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_DOMICILE;
            if ($company != null) {
                $table_fields = $this->company->get_legal_domicile()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_domicile()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_company', 'id_country', 'id_country_province', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'file'
                ],
                'number' => [
                    'text' => 'Nomor Domisili'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku Mulai'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berlaku Selesai'
                ],
                'country_name' => [
                    'text' => 'Negara'
                ],
                'province_name' => [
                    'text' => 'Provinsi'
                ],
                'address' => [
                    'text' => 'Alamat'
                ], 'pos_code' => [
                    'text' => 'Kode Pos'
                ], 'phone' => [
                    'text' => 'Telepon'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Legalitas Domisili'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - Legalitas Domisili';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_domicile';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;


            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'number' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nomor Domisili tidak valid!',
                    'placeholder' => 'Nomor Domisili'
                ],
                'start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Mulai tidak valid!',
                    'placeholder' => 'Tanggal Berlaku Mulai'
                ],
                'end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Selesai tidak valid!',
                    'placeholder' => 'Tanggal Berlaku Selesai'
                ],
                'id_country_province' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Provinsi tidak valid!',
                    'placeholder' => 'Provinsi'
                ],
                'address' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Alamat tidak valid!',
                    'placeholder' => 'Alamat'
                ],
                'pos_code' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kode Pos tidak valid!',
                    'placeholder' => 'Kode Pos'
                ],
                'phone' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nomor Telp tidak valid!',
                    'placeholder' => 'Telepon'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_country_province' => [
                    'text' => 'Provinsi<span style="color:red;">*</span>'
                ],
                'number' => [
                    'text' => 'Nomor Domisili<span style="color:red;">*</span>'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku Mulai<span style="color:red;">*</span>'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berlaku Selesai<span style="color:red;">*</span>'
                ],
                'pos_code' => [
                    'text' => 'Kode Pos<span style="color:red;">*</span>'
                ],
                'address' => [
                    'text' => 'Alamat<span style="color:red;">*</span>'
                ],
                'phone' => [
                    'text' => 'Telepon<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'id_country_province') {

                    $f['input_field']['html'] = '<select type="select" id="id_country_province" name="id_country_province" class="form-control" data-validation="required"
                    data-validation-error-msg="Provinsi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $f['input_field']['html'] .= $opt . '</select>';


                    $form_field = [
                        'label' => [
                            'text' => 'Negara<span style="color:red;">*</span>'
                        ],
                        'input_field' => [
                            'html' => ''
                        ]
                    ];

                    $form_field['input_field']['html'] = '<select type="select" id="id_country" name="id_country" class="form-control" data-validation="required"
                        data-validation-error-msg="Negara tidak valid!" >';
                    $data_opt = $this->country->get_data()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $form_field['input_field']['html'] .= $opt . '</select>';


                    $form_inflated[$i] = $form_field;
                    $i++;
                }

                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input type="file"  is-mandatory="true"  id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                }


                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }


                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_legal_domicile.js'),
                base_url('assets/js/page/company_attachment.js')
            ];
            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';

            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_legal_npwp()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_NPWP;
            if ($company != null) {
                $table_fields = $this->company->get_legal_npwp()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_npwp()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'pkp', 'id_company', 'id_country', 'id_country_province', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'attachment' => [
                    'text' => 'file'
                ],
                'no' => [
                    'text' => 'NPWP'
                ],
                'country_name' => [
                    'text' => 'Negara'
                ],
                'province_name' => [
                    'text' => 'Provinsi'
                ],
                'city' => [
                    'text' => 'Kota'
                ],
                'address' => [
                    'text' => 'Alamat'
                ],
                'pos_code' => [
                    'text' => 'Kode Pos'
                ],
                'pkp_name' => [
                    'text' => 'PKP'
                ],
                'no_pkp' => [
                    'text' => 'No.PKP'
                ],
                'sppkp_date' => [
                    'text' => 'Tgl SPPKP'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' NPWP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - NPWP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_npwp';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'NPWP tidak valid!',
                    'placeholder' => 'NPWP'
                ],
                'id_country_province' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Provinsi tidak valid!',
                    'placeholder' => 'Provinsi'
                ],
                'city' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kota tidak valid!',
                    'placeholder' => 'Kota'
                ],
                'address' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Alamat tidak valid!',
                    'placeholder' => 'Alamat'
                ],
                'pos_code' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kode Pos tidak valid!',
                    'placeholder' => 'Kode Pos'
                ],
                'no_pkp' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No PKP tidak valid!',
                    'placeholder' => 'No PKP'
                ],
                'sppkp_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal SPPKP tidak valid!',
                    'placeholder' => 'Tanggal SPPKP'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'NPWP<span style="color:red;">*</span>'
                ],
                'id_country_province' => [
                    'text' => 'Provinsi<span style="color:red;">*</span>'
                ],
                'city' => [
                    'text' => 'Kota<span style="color:red;">*</span>'
                ],
                'pkp' => [
                    'text' => 'PKP<span style="color:red;">*</span>'
                ],
                'pkp' => [
                    'text' => 'PKP<span style="color:red;">*</span>'
                ],
                'no_pkp' => [
                    'text' => 'No.PKP<span style="color:red;">*</span>'
                ],
                'sppkp_date' => [
                    'text' => 'Tanggal SPPKP<span style="color:red;">*</span>'
                ],
                'address' => [
                    'text' => 'Alamat<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_country_province') {

                    $f['input_field']['html'] = '<select type="select" id="id_country_province" name="id_country_province" class="form-control" data-validation="required"
                    data-validation-error-msg="Provinsi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $f['input_field']['html'] .= $opt . '</select>';


                    $form_field = [
                        'label' => [
                            'text' => 'Negara<span style="color:red;">*</span>'
                        ],
                        'input_field' => [
                            'html' => ''
                        ]
                    ];

                    $form_field['input_field']['html'] = '<select type="select" id="id_country" name="id_country" class="form-control" data-validation="required"
                        data-validation-error-msg="Negara tidak valid!" >';
                    $data_opt = $this->country->get_data()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $form_field['input_field']['html'] .= $opt . '</select>';

                    $form_inflated[$i] = $form_field;
                    $i++;
                } else if ($f['input_field']['attr']['id'] == 'pkp') {
                    $f['input_field']['html'] = '<select type="select" id="pkp" name="pkp" class="form-control" data-validation="required"
                    data-validation-error-msg="PKP tidak valid!" >';
                    $opt = '<option value="1">Ya</option>';
                    $opt .= '<option value="1">Tidak</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'attachment') {
                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                } else  if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }

                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_legal_domicile.js'),
                base_url('assets/js/page/company_attachment.js')
            ];
            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_legal_siup()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_SIUP;
            if ($company != null) {
                $table_fields = $this->company->get_legal_siup()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_siup()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_company', 'id_business_type', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'file'
                ],
                'no' => [
                    'text' => 'SIUP'
                ],
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'start_date' => [
                    'text' => 'Tgl Berlaku Mulai'
                ],
                'end_date' => [
                    'text' => 'Berlaku Sampai'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh'
                ],
                'business_type_name' => [
                    'text' => 'Jenis Usaha'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' SIUP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - SIUP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_siup';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'SIUP tidak valid!',
                    'placeholder' => 'SIUP'
                ],
                'start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Mulai tidak valid!',
                    'placeholder' => 'Tanggal Berlaku'
                ],
                'end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Akhir tidak valid!',
                    'placeholder' => 'Tanggal Berlaku Akhir'
                ],
                'publisher' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Dikeluarkan oleh, tidak valid!',
                    'placeholder' => 'Dikeluarkan Oleh'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_business_type' => [
                    'text' => 'Jenis Usaha<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'SIUP<span style="color:red;">*</span>'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku Mulai<span style="color:red;">*</span>'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berlaku Akhir<span style="color:red;">*</span>'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if (($f['input_field']['attr']['id'] == 'id_business_type')) {
                    $f['input_field']['html'] = '<select type="select" id="id_business_type" name="id_business_type" class="form-control" data-validation="required"
                        data-validation-error-msg="Jenis usaha tidak valid!" >';
                    $data_opt = $this->company->get_business_type()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                } else  if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }

                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];
            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_legal_tdp()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_TDP;
            if ($company != null) {
                $table_fields = $this->company->get_legal_tdp()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_tdp()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_company', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'attachment' => [
                    'text' => 'file'
                ],
                'no' => [
                    'text' => 'TDP'
                ],
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'start_date' => [
                    'text' => 'Tgl Berlaku Mulai'
                ],
                'end_date' => [
                    'text' => 'Berlaku Sampai'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' TDP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - TDP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_tdp';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No.TDP tidak valid!',
                    'placeholder' => 'No.TDP'
                ],
                'start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Mulai tidak valid!',
                    'placeholder' => 'Tanggal Berlaku'
                ],
                'end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku Akhir tidak valid!',
                    'placeholder' => 'Tanggal Berlaku Akhir'
                ],
                'publisher' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Dikeluarkan oleh, tidak valid!',
                    'placeholder' => 'Dikeluarkan Oleh'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'TDP<span style="color:red;">*</span>'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku Mulai<span style="color:red;">*</span>'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berlaku Akhir<span style="color:red;">*</span>'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                }

                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }
                $form_inflated[$i] = $f;
                $i++;
            }



            $data['form'] = $form_inflated;

            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];

            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_legal_nib()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_NIB;
            if ($company != null) {
                $table_fields = $this->company->get_legal_nib()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_nib()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'pkp', 'id_company', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'file'
                ],
                'no' => [
                    'text' => 'NIB'
                ],
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'active_date' => [
                    'text' => 'Tgl Berlaku NIB'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Legalistas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' NIB'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - NIB';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_legal_nib';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['index_id', 'id', 'updated_at', 'deleted_at', 'created_at', 'verificator'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'NPWP tidak valid!',
                    'placeholder' => 'NIB'
                ],
                'active_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berlaku NIB tidak valid!',
                    'placeholder' => 'Tanggal Berlaku NIB'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'NIB<span style="color:red;">*</span>'
                ],
                'active_date' => [
                    'text' => 'Tanggal Berlaku NIB<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File<span style="color:red;">*</span>'
                ],
                'verification_status' => [
                    'text' => 'Status Verifikasi<span style="color:red;">*</span>'
                ]
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . '.' . $o->name . ' (' . $o->postfix_name . ') | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                }
                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }
                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;

            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];

            $data['is_for_verification'] = 'true';
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_finance_bank()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_FINANCE_BANK;
            if ($company != null) {
                $table_fields = $this->company->get_company_finance_bank()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_finance_bank()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'id_currency', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'no' => [
                    'text' => 'No.Rekening'
                ],
                'owner' => [
                    'text' => 'Nasabah'
                ],
                'bank_name' => [
                    'text' => 'Nama Bank'
                ],
                'branch' => [
                    'text' => 'Cabang'
                ],
                'address' => [
                    'text' => 'Alamat'
                ],
                'currency_name' => [
                    'text' => 'Kurs'
                ], 'attachment' => [
                    'text' => 'Buku Tabungan'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Akun Bank Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Akun Bank'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil Perusahaan - Akun Bank';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'verification/get_company_finance_bank';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = '';
            $data['update_url'] = 'verification/edit_data';
            $data['add_url'] = '';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No.Rekening tidak valid!',
                    'placeholder' => 'No.Rekening'
                ],
                'owner' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama Nasabah tidak valid!',
                    'placeholder' => 'Nama Nasabah'
                ],
                'branch' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Cabang tidak valid!',
                    'placeholder' => 'Cabang'
                ],
                'address' => [
                    'disabled' => 'disabled',
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Alamat tidak valid!',
                    'placeholder' => 'Alamat'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'No.Rekening<span style="color:red;">*</span>'
                ],
                'owner' => [
                    'text' => 'Nama Nasabah<span style="color:red;">*</span>'
                ],
                'bank_name' => [
                    'text' => 'Nama Bank<span style="color:red;">*</span>'
                ],
                'branch' => [
                    'text' => 'Cabang<span style="color:red;">*</span>'
                ],
                'address' => [
                    'text' => 'Alamat<span style="color:red;">*</span>'
                ],
                'id_currency' => [
                    'text' => 'Kurs<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'Bukti Buku Tabungan<span style="color:red;">*</span>'
                ]

            ]);

            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select disabled type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'id_currency') {

                    $f['input_field']['html'] = '<select disabled type="select" id="id_currency" name="id_currency" class="form-control" data-validation="required"
                    data-validation-error-msg="Mata uang tidak valid!" >';
                    $data_opt = $this->master->get_currency()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }

                if ($f['input_field']['attr']['id'] == 'verification_status') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<input hidden class="form-control" id="verification_status" name="verification_status" value="Pending Verification" />
                        <input disabled class="form-control" value="Pending Verification" id="verification_status" name="verification_status" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="verification_status" name="verification_status" class="form-control" data-validation="required"
                        data-validation-error-msg="Status verifikasi tidak valid!" >';
                        $data_opt = [
                            ['id' => 'Verified', 'name' => 'Verified'],
                            ['id' => 'Pending Verification', 'name' => 'Pending Verification'],
                            ['id' => 'Rejected', 'name' => 'Rejected']
                        ];
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o['id'] . '">' . $o['name'] . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'bank_name') {

                    $f['input_field']['html'] = '<select disabled type="select" id="bank_name" name="bank_name" class="form-control" data-validation="required"
                    data-validation-error-msg="Bank tidak valid!" >';
                    $data_opt = $this->db->get('m_bank_list')->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->name . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }


                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input hidden is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />';
                }



                $form_inflated[] = $f;
            }

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
            }';

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/verification.js'),
                base_url('assets/js/page/company_attachment.js'),
            ];
            $data['action_add'] = 'disabled';
            $data['action_delete'] = 'disabled';

            $data['render_column_modifier'] = '{
                verification_status:{
                    render:"<span class=\"badge badge-default\">{val}</span>"
                    ,condition:{
                        pending_verification:"<span class=\"badge badge-warning\" style=\"color:white;\">{val}</span>",
                        rejected:"<span class=\"badge badge-danger\">{val}</span>",
                        verified:"<span class=\"badge badge-success\">{val}</span>",
                    }
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                },
                verification_history:{
                    render:"<span class=\"verification_history_look\" style=\"cursor:pointer;\" onclick=\"lookVerifyHitory(\'{val}\')\"><i class=\"fa fa-link\"></i></span>"
                }
            }';

            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }



    //Custom Get
    public function get_company_contact()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        $data = $this->company->get_contact($id, $id_company)
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE);
        $data->where('a.verification_status', 'Pending Verification');
        echo json_encode(
            $data->order_by('a.created_at', 'desc')
                ->get()
                ->result()
        );
    }
    public function get_company_pic()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_pic($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_finance_bank()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_finance_bank($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_born_license()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_born_license($id, $id_company)
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->where('a.verification_status', 'Pending Verification')
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_tdp()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_tdp($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_siup()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_siup($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_doc()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_doc($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_npwp()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_npwp($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_nib()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_nib($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_domicile()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_domicile($id, $id_company)
            ->where('a.verification_status', 'Pending Verification')
            ->join(App_Model::TBL_USER, 'b.id_user=' . App_Model::TBL_USER . '.id_user')
            ->join(App_Model::TBL_USR_STATUS, App_Model::TBL_USER . '.id_usr_status=' . App_Model::TBL_USR_STATUS . '.id_usr_status')
            ->where(App_Model::TBL_USR_STATUS . '.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    // Master CRUD
    public function add_data()
    {
        echo json_encode(add_data());
    }

    public function get_data()
    {
        echo json_encode(get_data());
    }

    public function edit_data()
    {
        $table_name = $this->input->post('_table');
        $table_id = $this->db->get($table_name)->list_fields()[0];
        //insert history
        if ($this->db->get($table_name)->row()->verification_status != $this->input->post('verification_status')) {
            $this->db->insert('verification_history', [
                'verificator' => $this->session->userdata('user')['id_user'],
                'data_main_table' => $table_name,
                'data_id' => $this->input->post($table_id),
                'verification_status' => $this->input->post('verification_status'),
                'verification_note' => $this->input->post('verification_note')
            ]);
        }
        //Add notification
        if ($table_name == 'company_profile') {
            $data = $this->db
                ->where($table_id, $this->input->post($table_id))
                ->get($table_name)
                ->row();
        } else {
            $data = $this->db
                ->where($table_id, $this->input->post($table_id))
                ->get($table_name)
                ->row();
            $data->id_user = $this->db->where('id', $data->id_company)->get(App_Model::TBL_COMPANY_PROFILE)
                ->row()->id_user;
        }

        $link = '#dashboard';
        $title_table = null;
        switch ($table_name) {
            case 'company_profile': {
                    $link = '#profile/company';
                    $title_table = "Profil Dasar";
                    break;
                }
            case 'company_contact': {
                    $link = '#profile/company_contact';
                    $title_table = "Kontak";
                    break;
                }
            case 'company_pic': {
                    $title_table = "PIC";
                    $link = '#profile/company_pic';
                    break;
                }
            case 'company_born_license': {
                    $link = '#profile/company_born_license';
                    $title_table = "Akta Perusahaan";
                    break;
                }
            case 'company_legal_domicile': {
                    $link = '#profile/company_legal_domicile';
                    $title_table = "Legalistas Domisili";
                    break;
                }
            case 'company_legal_npwp': {
                    $link = '#profile/company_legal_npwp';
                    $title_table = "NPWP";
                    break;
                }
            case 'company_legal_siup': {
                    $link = '#profile/company_legal_siup';
                    $title_table = "SIUP";
                    break;
                }
            case 'company_legal_tdp': {
                    $link = '#profile/company_legal_tdp';
                    $title_table = "TDP";
                    break;
                }
            case 'company_legal_nib': {
                    $link = '#profile/company_legal_nib';
                    $title_table = "NIB";
                    break;
                }
            case 'company_finance_bank': {
                    $link = '#profile/company_finance_bank';
                    $title_table = "AKUN BANK";
                    break;
                }
        }
        add_notification($this->session->userdata('user')['id_user'], $data->id_user, null, 'Verifikasi Data', 'Informasi Data ' . $title_table . ' Anda Telah Diverifikasi dengan Hasil Verifikasi : ' . $this->input->post('verification_status'), 'Internal', $link);
        $edit = edit_data();
        $user = $this->db->where('deleted_at is null')
            ->where('id_user', $data->id_user)
            ->get(App_Model::TBL_USER)->row();
        if (
            $user->id_usr_status == App_Model::STAT_ACCOUNT_VERIFY_PROFILE ||
            $user->id_usr_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE
        ) {
            $this->save_to_sap($table_name, $data);
        }
        echo json_encode($edit);
    }


    public function test_sap()
    {
        $sap_company = '7f7a2ae0-2978-11ea-9c8b-54b20309bff4';
        $sap_user = $this->db
            ->join(App_Model::TBL_USR_ROLE, App_Model::TBL_USR_ROLE . '.id_usr_role=' . App_Model::TBL_USER . '.id_usr_role')
            ->where('id_user', 22)
            ->get(App_Model::TBL_USER)->row();

        // $data = $this->sap->input_sap($sap_company, $sap_user->id_usr_role);

        echo json_encode([
            'check not pending' => check_required_form_validation(24, true, "!='Pending Verification'")->percentage,
            'check all verified' => check_required_form_validation(24, true, "='Verified'")->percentage,
            'check rejected' => [
                'percentage' => check_required_form_validation(24, true, "='Rejected'")->percentage,
                'last_query' => $this->db->last_query()
            ]
        ]);
    }


    private function save_to_sap($table_name, $data)
    {
        $result = [
            'success' => false,
            'result' => null
        ];

        if (check_required_form_validation($data->id_user, true, "!='Pending Verification'")->percentage == 100) {
            $sap_company = ($table_name == 'company_profile') ? $data->id : $data->id_company;
            $sap_user = $this->db
                ->join(App_Model::TBL_USR_ROLE, App_Model::TBL_USR_ROLE . '.id_usr_role=' . App_Model::TBL_USER . '.id_usr_role')
                ->where('id_user', $data->id_user)
                ->get(App_Model::TBL_USER)
                ->row();
            $sap_profile_company = $this->db
                ->where('deleted_at is null')
                ->where('id', $sap_company)
                ->get(App_Model::TBL_COMPANY_PROFILE)->row();
            if (check_required_form_validation($data->id_user, true, "='Verified'")->percentage == 100) {

                if (
                    $this->db
                    ->where('id_company', $sap_company)
                    ->where('deleted_at is null')
                    ->where('id_sap is not null')
                    ->get(App_Model::TBL_SAP_SYNC)->num_rows() <= 0
                ) {
                    $sync_with_sap = $this->sap->input_sap($sap_company, $sap_user->id_usr_role);
                    // $result = $sync_with_sap;
                    $result['success'] = true;
                    $result['result'] = " Data " . ($sap_profile_company != null) ?
                        $sap_profile_company->prefix_name . ' ' . $sap_profile_company->name . ' ' . $sap_profile_company->postfix_name
                        : null . ' Telah Diverifikasi Dan Teregistrasi Ke Dalam SAP!';
                } else {
                    $result['success'] = true;
                    $result['result'] = " Data " . ($sap_profile_company != null) ?
                        $sap_profile_company->prefix_name . ' ' . $sap_profile_company->name . ' ' . $sap_profile_company->postfix_name
                        : null . ' Telah Diverifikasi Dan Teregistrasi Ke Dalam SAP!';
                }

                $email_target = $sap_user->email;
                $email_subject = 'Konfirmasi Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
                $user_role = $sap_user->role_name;
                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $email_target,
                    $email_subject,
                    '<center>
                        <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
                        <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                        <hr>
                        <p> Terima kasih telah mencoba mendaftar sebagai ' . $user_role . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                        . '. Selamat!, Registrasi akun Anda DITERIMA. Silahkan login untuk menikmati fitur lainnya pada sistem.
                            <br>
                            <center><a href="' . site_url() . '" style=" background-color: #4CAF50; /* Green */
                                border: none;
                                color: white;
                                padding: 15px 32px;
                                text-align: center;
                                text-decoration: none;
                                display: inline-block;
                                font-size: 16px;">Login Sekarang</a></center>
                        <br>
                        Terima kasih.
                        <br>
                        <br>
                        <b>Regards,
                        <br><a href="' . site_url() . '">E-Procurement System</a>
                        <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                        </b>
                        </p>'
                );

                $this->db->where('id_user', $data->id_user)->update(App_Model::TBL_USER, [
                    'id_usr_status' => App_Model::STAT_ACCOUNT_ACTIVE
                ]);
            } else if (check_required_form_validation($data->id_user, true, "='Rejected'")->percentage > 0) {
                $list_rejected = '<ol>';
                $list = check_required_form_validation($data->id_user, true, "='Rejected'");

                foreach ($list->detail as $k => $key) {
                    if ($key['is_valid']) {
                        $list_rejected .= "<li>" . $key['form_detail']->title . "</li>";
                    }
                }
                $list_rejected .= '</ol>';
                $email_target = $sap_user->email;
                $email_subject = 'Konfirmasi Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
                $user_role = $sap_user->role_name;
                // <img src="https://bgrlogistik.id/bgr/img/bgr_logo.png" width="200px">
                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $email_target,
                    $email_subject,
                    '<center>
                    <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
                    <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                    <hr>
                    <p> Terima kasih telah mencoba mendaftar sebagai ' . $user_role . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                        . '. Mohon maaf pendaftaran Anda belum dapat diterima, dikarenakan berdasarkan validasi terdapat data yang tidak valid diantaranya:
                        <br>
                        ' . $list_rejected . '
                    <br>
                    Anda masih dapat melanjutkan registrasi dengan melakukan perbaikan Data,
                    Kami harap Anda kembali memperbaiki dan mengirim Data Perbaikan dalam kurun waktu 2x24 Jam sejak email ini dikirim, jika tidak sistem Akan Menghapus Akun Anda secara otomatis, Terima kasih.
                    <br>
                    <br>
                    <b>Regards,
                    <br><a href="' . site_url() . '">E-Procurement System</a>
                    <br>' . $this->config->item('app_info')['identity']['author_name'] . '
                    </b>
                    </p>'
                );

                $this->db->where('id_user', $data->id_user)->update(App_Model::TBL_USER, [
                    'id_usr_status' => App_Model::STAT_ACCOUNT_VERIFY_PROFILE,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $result['success'] = true;
            } else {

                $this->db->where('id_user', $data->id_user)->update(App_Model::TBL_USER, [
                    'id_usr_status' => App_Model::STAT_ACCOUNT_ACTIVE
                ]);
            }
        }



        return $result;
    }

    public function delete_data()
    {
        echo json_encode(delete_data());
    }
}
