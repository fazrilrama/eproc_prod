<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'check_profile_completition'],
                ['method' => 'get_company_profile'],
                ['method' => 'get_data'],
                ['method' => 'get_company_contact'],
                ['method' => 'get_company_pic'],
                ['method' => 'get_company_legal_domicile'],
                ['method' => 'get_company_legal_doc'],
                ['method' => 'get_company_legal_npwp'],
                ['method' => 'get_company_legal_nib'],
                ['method' => 'get_company_document'],
                ['method' => 'get_company_legal_tdp'],
                ['method' => 'get_company_legal_siup'],
                ['method' => 'get_company_legal_born_license'],
                ['method' => 'get_company_management'],
                ['method' => 'get_company_finance_bank'],
                ['method' => 'get_company_finance_report'],
                ['method' => 'get_company_certification'],
                ['method' => 'get_company_facilities'],
                ['method' => 'get_company_experience'],
                ['method' => 'get_company_competencies'],
                ['method' => 'add_data', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_company_with_attachment', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data_company_with_attachment', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal_domicile', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal_npwp', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal_nib', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal_siup', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_legal_tdp', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_company_certification', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_profile', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data_profile', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'delete_data', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data_contact', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_contact', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'add_data_competency', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data_competency', 'id_role' => [1, 2, 3, 6, 7]],
            ]
        ]);
        $this->load->model('Company_model', 'company');
        $this->load->model('Company_type_model', 'company_type');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Master_model', 'master');
    }


    public function check_profile_completition()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        echo json_encode($this->company->check_required_form_validation($id_user));
    }

    public function company()
    {
        $this->set_page_title('pe-7s-home', 'Profil', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'label' => '',
                'link' => '#dashboard'
            ],
            [
                'icon' => '',
                'label' => 'Profil',
            ], [
                'active' => true,
                'icon' => '',
                'label' => 'Perusahaan'
            ]
        ]);
        $this->load->view('pages/profile/company');
    }

    public function company_contact()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_CONTACT;
            if ($company != null) {
                $table_fields = $this->company->get_contact()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_contact()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_company', 'building_no', 'work_area', 'id_country', 'id_country_province', 'deleted_at', 'created_at'];
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
                'main_contact' => [
                    'text' => 'Kontak Utama'
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Kontak'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Kontak';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_contact';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_contact';
            $data['add_url'] = 'profile/add_data_contact';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'building_no', 'main_contact', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'address' => [
                    'data-validation' => 'required length',
                    'data-validation-length' => 'max60',
                    'placeholder' => 'Alamat'
                ],
                'city' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kota tidak valid!',
                    'placeholder' => 'Kota'
                ],
                'pos_code' => [
                    'type' => 'number',
                    'data-validation' => 'required number length',
                    'data-validation-length' => 'max10',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask': '99999'",
                    'placeholder' => 'Kode Pos'
                ],
                'phone' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'Telepon'
                ],
                'phone2' => [
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'Telepon2'
                ],
                'fax' => [
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'Fax'
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
                // 'building_no' => [
                //     'data-validation' => 'required',
                //     'data-validation-error-msg' => 'No Bangunan/Rumah tidak valid!',
                //     'placeholder' => 'No Bangunan/Rumah'
                // ],
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
                    'text' => 'Telepon2',
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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
                    $f['input_field']['html'] = '<select class="form-control" id="main_contact" name="main_contact">
                        <option selected="selected" value="1">Ya</option>
                        <option value="0">Tidak</option>
                    </select>';
                }

                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_legal_domicile.js'),
            ];
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
                main_contact:{
                    render:function(data){
                        return (data.main_contact==1)?`<span class="badge badge-success">Ya</span>`:`<span class="badge badge-danger">Tidak</span>`;
                    }
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

    public function company_pic()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_PIC;
            if ($company != null) {
                $table_fields = $this->company->get_pic()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_pic()->get()->list_fields();
            }
            $fields_exception = ['id', 'office_phone', 'updated_at', 'id_company', 'work_area', 'id_country', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'company_name' => [
                    'text' => 'Nama Perusahaan'
                ],
                'name' => [
                    'text' => 'Nama'
                ],
                'position_type' => [
                    'text' => 'Jenis Posisi'
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' PIC'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - PIC';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_pic';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_company_with_attachment';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'office_phone', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama tidak valid!',
                    'placeholder' => 'Nama'
                ],
                'position_type' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Posisi/Jabatan tidak valid!',
                    'placeholder' => 'Jenis Posisi/Jabatan'
                ],
                'position' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Posisi/Jabatan tidak valid!',
                    'placeholder' => 'Posisi/Jabatan'
                ],
                'office_phone' => [
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'Telepon Kantor'
                ],
                'mobile_phone' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'Nomor Handphone'
                ],
                'email' => [
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
                'attachment' => [
                    'text' => 'Surat Kuasa<span style="color:red;">*</span>'
                ],
                'position_type' => [
                    'text' => 'Jenis Posisi<span style="color:red;">*</span>'
                ],
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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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

                if ($f['input_field']['attr']['id'] == 'position_type') {
                    $f['input_field']['html'] = '<select type="select" id="position_type" name="position_type" class="form-control" data-validation="required"
                        data-validation-error-msg="Jenis posisi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $opt .= '<option value="1">Direksi/Pemilik Usaha</option>';
                    $opt .= '<option value="2">Lainnya</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                }
                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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
                },
                position_type:{
                    render:function(val){return (val.position_type==1)?`Direksi/Pemilik Usaha`:`Lainnya`}
                },
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
            }';

            $data['add_scripts'] = [
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


    public function company_document()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_DOCUMENT;
            if ($company != null) {
                $table_fields = $this->company->get_documents()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_documents()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'pkp', 'id_company', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'file'
                ],
                'name' => [
                    'text' => 'Nama'
                ],
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'description' => [
                    'text' => 'Deksripsi'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Dokumen Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Dokumen Lainnya'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Dokumen Lainnya';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_document';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_company_with_attachment';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama tidak valid!',
                    'placeholder' => 'Nama'
                ],
                'description' => [
                    'placeholder' => 'Deskripsi'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'name' => [
                    'text' => 'Nama<span style="color:red;">*</span>'
                ],
                'description' => [
                    'text' => 'Deskription'
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
                }
                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;

            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
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
            ->get()->row();
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Legalitas Domisili'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Legalitas Domisili';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_domicile';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_legal_domicile';
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
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask':'99999'",
                    'placeholder' => 'Kode Pos'
                ],
                'phone' => [
                    'data-validation' => 'required',
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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


            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';

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
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_LEGAL_NPWP;
            if ($company != null) {
                $table_fields = $this->company->get_legal_npwp()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_legal_npwp()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'sppkp_date', 'pkp', 'id_company', 'id_country', 'id_country_province', 'deleted_at', 'created_at'];
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' NPWP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - NPWP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_npwp';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_legal_npwp';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask': '99.999.999.9-999.999'",
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
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask': '99999'",
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
                'pos_code' => [
                    'text' => 'Kode Pos<span style="color:red;">*</span>'
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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
                    $opt .= '<option value="0">Tidak</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'attachment') {
                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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
                base_url('assets/js/page/company_attachment.js'),
                base_url('assets/js/page/company_legal_npwp.js')
            ];

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

            $data['form_note'] = 'Catatan:<ol>
                <li>File Upload NPWP Wajib Menyertakan SKT</li>
            </ol>';

            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';
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
            ->get()->row();
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' NIB'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - NIB';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_nib';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_legal_nib';
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }
                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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

            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';
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

            ->get()
            ->row();
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' TDP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - TDP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_tdp';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_legal_tdp';
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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

            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';
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

            ->get()
            ->row();
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
                    'text' => 'No.SIUP'
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' SIUP'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - SIUP';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_siup';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_legal_siup';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'SIUP tidak valid!',
                    'placeholder' => 'No.SIUP'
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
                    'text' => 'No.SIUP<span style="color:red;">*</span>'
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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
            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';

            $this->load->view('pages/master/master_view', $data);
            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_born_license()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
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
                    'text' => 'Tgl Pengesahan Kemenhum'
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
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Akta'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Akta';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_legal_born_license';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_company_with_attachment';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

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
                ],
                'notary' => [
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[a-zA-Z]+$",
                    'placeholder' => 'Notaris'
                ],
                'notary_telp' => [
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]+$",
                    'placeholder' => 'Notaris Telp'
                ],
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
                    'text' => 'Tgl Pengesahan Kemenhum'
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
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
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
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
                base_url('assets/js/page/company_attachment.js')
            ];

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

            if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) $data['action_delete'] = 'false';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_management()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_MANAGEMENT;
            if ($company != null) {
                $table_fields = $this->company->get_company_management()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_management()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [

                'company_name' => [
                    'text' => 'Nama Perusahaan'
                ],
                'type' => [
                    'text' => 'Tipe Pengurus'
                ],
                'name' => [
                    'text' => 'Nama'
                ],
                'position' => [
                    'text' => 'Posisi'
                ],
                'id_card_number' => [
                    'text' => 'No.KTP'
                ],
                'tax_registration_number' => [
                    'text' => 'NPWP'
                ],
                'phone' => [
                    'text' => 'No.Telp'
                ],
                'email' => [
                    'text' => 'Email'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Pengurus Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Pengurus'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Pengurus';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_management';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data';
            $data['add_url'] = 'profile/add_data';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama tidak valid!',
                    'placeholder' => 'Nama'
                ],
                'position' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Posisi tidak valid!',
                    'placeholder' => 'Posisi'
                ],
                'id_card_number' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask': '9999 9999 9999 9999'",
                    'placeholder' => 'No.KTP'
                ],
                'tax_registration_number' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask' => "'mask': '99.999.999.9-999.999'",
                    'placeholder' => 'NPWP'
                ],
                'phone' => [
                    'data-validation' => 'required',
                    'class' => "form-control input-mask-trigger",
                    'data-inputmask-regex' => "^[0-9]{1,15}$",
                    'placeholder' => 'No.Telp'
                ],
                'email' => [
                    'data-validation' => 'required email',
                    'data-validation-error-msg' => 'Email tidak valid!',
                    'placeholder' => 'Email'
                ]
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'type' => [
                    'text' => 'Tipe Pengurus<span style="color:red;">*</span>'
                ],
                'name' => [
                    'text' => 'Nama<span style="color:red;">*</span>'
                ],
                'position' => [
                    'text' => 'Posisi<span style="color:red;">*</span>'
                ],
                'id_card_number' => [
                    'text' => 'No.KTP<span style="color:red;">*</span>'
                ],
                'tax_registration_number' => [
                    'text' => 'NPWP<span style="color:red;">*</span>'
                ],
                'phone' => [
                    'text' => 'No.Telp'
                ],
                'email' => [
                    'text' => 'Email'
                ],
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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'type') {

                    $f['input_field']['html'] = '<select type="select" id="type" name="type" class="form-control" data-validation="required"
                    data-validation-error-msg="Tipe tidak valid!" >';
                    $opt = '<option value="">Pilih</option>
                    <option value="Dewan Direksi">Dewan Direksi</option>
                    <option value="Dewan Komisaris">Dewan Komisaris</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
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
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_FINANCE_BANK;
            if ($company != null) {
                $table_fields = $this->company->get_company_finance_bank()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_finance_bank()->get()->list_fields();
            }
            $fields_exception = ['id', 'id_company', 'id_currency', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'Buku Tabungan'
                ], 'company_name' => [
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
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Akun Bank Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Akun Bank'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Akun Bank';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_finance_bank';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_company_with_attachment';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No.Rekening tidak valid!',
                    'placeholder' => 'No.Rekening'
                ],
                'owner' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama Nasabah tidak valid!',
                    'placeholder' => 'Nama Nasabah'
                ],
                'branch' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Cabang tidak valid!',
                    'placeholder' => 'Cabang'
                ],
                'address' => [
                    'data-validation' => 'required length',
                    'data-validation-length' => 'max60',
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
                ],

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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
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

                    $f['input_field']['html'] = '<select type="select" id="id_currency" name="id_currency" class="form-control" data-validation="required"
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

                if ($f['input_field']['attr']['id'] == 'verification_note') {
                    if (!$this->is_as_admin()) {
                        $f['input_field']['html'] = '<textarea readonly class="form-control" id="verification_note" name="verification_note"></textarea>';
                    } else {
                        $f['input_field']['html'] = '<textarea class="form-control" id="verification_note" name="verification_note"></textarea>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'bank_name') {

                    $f['input_field']['html'] = '<select type="select" id="bank_name" name="bank_name" class="form-control" data-validation="required"
                    data-validation-error-msg="Bank tidak valid!" >';
                    $data_opt = $this->db->get('m_bank_list')->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->name . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }

                if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];

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
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
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

    public function company_finance_report()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_FINANCE_REPORT;
            if ($company != null) {
                $table_fields = $this->company->get_company_finance_report()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_finance_report()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'id_currency', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'type' => [
                    'text' => 'Tipe'
                ],
                'year' => [
                    'text' => 'Tahun'
                ],
                'asset_value' => [
                    'text' => 'Nilai Aset'
                ],
                'loan' => [
                    'text' => 'Hutang'
                ],
                'gross_income' => [
                    'text' => 'Pendapatan Kotor'
                ],
                'net_income' => [
                    'text' => 'Pendapatan Bersih'
                ],
                'currency_name' => [
                    'text' => 'Kurs'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Laporan keuangan Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Laporan Keuangan'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Laporan Keuangan';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_finance_report';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data';
            $data['add_url'] = 'profile/add_data';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'year' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tahun tidak valid!',
                    'placeholder' => 'Tahun'
                ],
                'type' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Jenis tidak valid!',
                    'placeholder' => 'Jenis'
                ],
                'id_currency' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kurs tidak valid!',
                    'placeholder' => 'Kurs'
                ],
                'asset_value' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nilai aset tidak valid!',
                    'placeholder' => 'Nilai Aset'
                ],
                'gross_income' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Pendapatan kotor tidak valid!',
                    'placeholder' => 'Pendapatan Kotor'
                ],
                'net_income' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Pendapatan bersih tidak valid!',
                    'placeholder' => 'Pendapatan Bersih'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_currency' => [
                    'text' => 'Kurs<span style="color:red;">*</span>'
                ],
                'year' => [
                    'text' => 'Tahun<span style="color:red;">*</span>'
                ],
                'asset_value' => [
                    'text' => 'Nilai Aset<span style="color:red;">*</span>'
                ],
                'loan' => [
                    'text' => 'Hutang<span style="color:red;">*</span>'
                ],
                'gross_income' => [
                    'text' => 'Pendapatan Kotor<span style="color:red;">*</span>'
                ],
                'net_income' => [
                    'text' => 'Pendapatan Bersih<span style="color:red;">*</span>'
                ],

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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_currency') {

                    $f['input_field']['html'] = '<select type="select" id="id_currency" name="id_currency" class="form-control" data-validation="required"
                    data-validation-error-msg="Mata uang tidak valid!" >';
                    $data_opt = $this->master->get_currency()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'type') {

                    $f['input_field']['html'] = '<select type="select" id="type" name="type" class="form-control" data-validation="required"
                    data-validation-error-msg="Tipe audit tidak valid!" >';
                    $opt = '<option value="">Pilih</option>
                    <option value="Audit">Audit</option>
                    <option value="Non Audit">Non Audit</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'year') {

                    $f['input_field']['html'] = '<input type="text" data-inputmask="\'mask\': \'9999\'" id="year" name="year" class="form-control input-mask-trigger" data-validation="required"
                    data-validation-error-msg="Tahun tidak valid!"/>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_certification()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();

        $id_role = $this->session->userdata('user')['id_usr_role'];
        if ($company != null || $this->is_as_admin() || $id_role == 3) {

            $table_name = App_Model::TBL_COMPANY_CERTIFICATION;
            if ($company != null) {
                $table_fields = $this->company->get_company_certification()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_certification()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'id_certificate_type', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'attachment' => [
                    'text' => 'file'
                ],
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'certificate_type_name' => [
                    'text' => 'Jenis Sertifikat'
                ],
                'name' => [
                    'text' => 'Nama Sertifikat'
                ],
                'type' => [
                    'text' => 'Jenis Sertifikat'
                ],
                'no' => [
                    'text' => 'Nomor Sertifikat'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berakhir'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Sertifikasi Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Sertifikasi'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Sertifikasi';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_certification';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_company_with_attachment';
            $data['add_url'] = 'profile/add_data_company_certification';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama Sertifikat tidak valid!',
                    'placeholder' => 'Nama Sertifikat'
                ],
                'no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nomor Sertifikat tidak valid!',
                    'placeholder' => 'Nomor Sertifikat'
                ],
                'publisher' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Dikeluarkan oleh tidak valid!',
                    'placeholder' => 'Dikeluarkan Oleh'
                ],
                'start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal berlaku tidak valid!',
                    'placeholder' => 'Tanggal Berlaku'
                ],
                'end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal Berakhir tidak valid!',
                    'placeholder' => 'Tanggal Berakhir'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_certificate_type' => [
                    'text' => 'Jenis Sertifikat<span style="color:red;">*</span>'
                ],
                'name' => [
                    'text' => 'Nama Sertifikat<span style="color:red;">*</span>'
                ],
                'type' => [
                    'text' => 'Jenis Sertifikat<span style="color:red;">*</span>'
                ],
                'no' => [
                    'text' => 'Nomor Sertifikat<span style="color:red;">*</span>'
                ],
                'publisher' => [
                    'text' => 'Dikeluarkan Oleh<span style="color:red;">*</span>'
                ],
                'start_date' => [
                    'text' => 'Tanggal Berlaku<span style="color:red;">*</span>'
                ],
                'end_date' => [
                    'text' => 'Tanggal Berakhir<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'File'
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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_certificate_type') {
                    $f['input_field']['html'] = '<select type="select" id="id_certificate_type" name="id_certificate_type" class="form-control" data-validation="required"
                    data-validation-error-msg="Jenis Sertifikat tidak valid!" >';
                    $data_opt = $this->master->get_certificate_type()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else  if ($f['input_field']['attr']['id'] == 'attachment') {

                    $f['input_field']['html'] = '<input is-mandatory="false " type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="50M"
                    data-validation-allowing="pdf, png, jpeg, jpg, rar, zip"  />
                    <span style="color:red">File pdf, png, jpeg, jpg, rar, zip Maksimal 50MB</span>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_attachment.js')
            ];

            $data['render_column_modifier'] = '{
                attachment:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                }
            }';


            $data['action_add'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_edit'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_delete'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_facilities()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        $id_role = $this->session->userdata('user')['id_usr_role'];
        if ($company != null || $this->is_as_admin() ||  $id_role == 3) {

            $table_name = App_Model::TBL_COMPANY_FACILITIES;
            if ($company != null) {
                $table_fields = $this->company->get_company_facilities()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_facilities()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'id_facilities_type', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'facility_type_name' => [
                    'text' => 'Tipe Fasilitas'
                ],
                'name' => [
                    'text' => 'Nama'
                ],
                'spesification' => [
                    'text' => 'Spesifikasi'
                ],
                'total' => [
                    'text' => 'Jumlah'
                ],
                'created_year' => [
                    'text' => 'Tahun Dibuat'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Fasilitas Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Fasilitas'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Fasilitas';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_facilities';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data';
            $data['add_url'] = 'profile/add_data';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama Sertifikat tidak valid!',
                    'placeholder' => 'Nama Sertifikat'
                ],
                'specification' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Spesifikasi tidak valid!',
                    'placeholder' => 'Spesifikasi'
                ],
                'total' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Total tidak valid!',
                    'placeholder' => 'Total'
                ],
                'created_year' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tanggal dibuat tidak valid!',
                    'placeholder' => 'Tanggal Dibuat'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_facilities_type' => [
                    'text' => 'Jenis Fasilitas<span style="color:red;">*</span>'
                ],
                'name' => [
                    'text' => 'Nama<span style="color:red;">*</span>'
                ],
                'spesification' => [
                    'text' => 'Spesifikasi<span style="color:red;">*</span>'
                ],
                'total' => [
                    'text' => 'Total<span style="color:red;">*</span>'
                ],
                'created_year' => [
                    'text' => 'Tahun Dibuat<span style="color:red;">*</span>'
                ],
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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_facilities_type') {
                    $f['input_field']['html'] = '<select type="select" id="id_facilities_type" name="id_facilities_type" class="form-control" data-validation="required"
                    data-validation-error-msg="Jenis Fasilitas tidak valid!" >';
                    $data_opt = $this->master->get_facilities_type()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'created_year') {

                    $f['input_field']['html'] = '<input type="text" data-inputmask="\'mask\': \'9999\'" id="created_year" name="created_year" class="form-control input-mask-trigger" data-validation="required"
                    data-validation-error-msg="Tahun tidak valid!"/>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;
            $data['action_add'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_edit'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_delete'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_experience()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();

        $id_role = $this->session->userdata('user')['id_usr_role'];
        if ($company != null || $this->is_as_admin() ||  $id_role == 3) {

            $table_name = App_Model::TBL_COMPANY_EXPERIENCE;
            if ($company != null) {
                $table_fields = $this->company->get_company_experience()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_experience()->get()->list_fields();
            }
            $fields_exception = ['id',  'id_company', 'id_currency', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'client_name' => [
                    'text' => 'Klien'
                ],
                'project_name' => [
                    'text' => 'Proyek'
                ],
                'currency_name' => [
                    'text' => 'Kurs'
                ],
                'project_value' => [
                    'text' => 'Nilai Proyek'
                ],
                'contract_no' => [
                    'text' => 'Kontrak No'
                ],
                'contact_name' => [
                    'text' => 'Kontak'
                ],
                'start_date' => [
                    'text' => 'Tgl Mulai'
                ],
                'end_date' => [
                    'text' => 'Tgl Selesai'
                ],
                'description' => [
                    'text' => 'Deskripsi'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Pengalaman Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Pengalaman'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Pengalaman';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_experience';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data';
            $data['add_url'] = 'profile/add_data';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'client_name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Klien tidak valid!',
                    'placeholder' => 'Nama Klien'
                ],
                'project_name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nama proyek tidak valid!',
                    'placeholder' => 'Nama Proyek'
                ],
                'project_value' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Nilai Proyek tidak valid!',
                    'placeholder' => 'Nilai Proyek'
                ],
                'contract_no' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No.Kontrak tidak valid!',
                    'placeholder' => 'Nomor Kontrak'
                ],
                'contact_name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Kontak tidak valid!',
                    'placeholder' => 'Kontak'
                ],
                'start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tgl Mulai tidak valid!',
                    'placeholder' => 'Tgl Mulai'
                ],
                'end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Tgl Mulai tidak valid!',
                    'placeholder' => 'Tgl Berakhir'
                ],
                'description' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Deskripsi tidak valid!',
                    'placeholder' => 'Deskripsi'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_currency' => [
                    'text' => 'Kurs<span style="color:red;">*</span>'
                ],
                'client_name' => [
                    'text' => 'Klien<span style="color:red;">*</span>'
                ],
                'project_name' => [
                    'text' => 'Proyek<span style="color:red;">*</span>'
                ],
                'currency_name' => [
                    'text' => 'Kurs<span style="color:red;">*</span>'
                ],
                'project_value' => [
                    'text' => 'Nilai Proyek<span style="color:red;">*</span>'
                ],
                'contract_no' => [
                    'text' => 'Kontrak No<span style="color:red;">*</span>'
                ],
                'contact_name' => [
                    'text' => 'Kontak<span style="color:red;">*</span>'
                ],
                'start_date' => [
                    'text' => 'Tgl Mulai<span style="color:red;">*</span>'
                ],
                'end_date' => [
                    'text' => 'Tgl Selesai<span style="color:red;">*</span>'
                ],
                'description' => [
                    'text' => 'Deskripsi<span style="color:red;">*</span>'
                ],
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

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_currency') {
                    $f['input_field']['html'] = '<select type="select" id="id_currency" name="id_currency" class="form-control" data-validation="required"
                    data-validation-error-msg="Kurs tidak valid!" >';
                    $data_opt = $this->master->get_currency()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'created_year') {

                    $f['input_field']['html'] = '<input type="text" data-inputmask="\'mask\': \'9999\'" id="created_year" name="created_year" class="form-control input-mask-trigger" data-validation="required"
                    data-validation-error-msg="Tahun tidak valid!"/>';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;

            $data['action_add'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_edit'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $data['action_delete'] = ($id_role == 3) ? 'disabled' : 'enabled';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function company_competencies()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_COMPETENCIES;
            if ($company != null) {
                $table_fields = $this->company->get_company_competencies()->get()->list_fields();
            } else {
                $table_fields = $this->company->get_company_competencies()->get()->list_fields();
            }
            $fields_exception = ['id', 'updated_at', 'id_competency', 'id_company', 'id_company_sub_competency', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'competency' => [
                    'text' => 'Kompetensi'
                ],
                'sub_competency' => [
                    'text' => 'Sub Kompetensi'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Kompetensi Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Profil'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Kompetensi'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Profil - Kompetensi';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'profile/get_company_competencies';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'profile/delete_data';
            $data['update_url'] = 'profile/edit_data_competency';
            $data['add_url'] = 'profile/add_data_competency';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_company_sub_competency' => [
                    'text' => 'Sub Kompetensi<span style="color:red;">*</span>'
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
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'id_company_sub_competency') {

                    $f['input_field']['html'] = '<select type="select" id="id_company_sub_competency" name="id_company_sub_competency" class="form-control select2" 
                    data-validation="required"
                    data-validation-error-msg="Sub Kompetensi tidak valid!" >';
                    $opt = '<option value="">Pilih</option>';
                    $f['input_field']['html'] .= $opt . '</select>';


                    $form_field = [
                        'label' => [
                            'text' => 'Kompetensi<span style="color:red;">*</span>'
                        ],
                        'input_field' => [
                            'html' => ''
                        ]
                    ];

                    $form_field['input_field']['html'] = '<select type="select" id="id_competency" name="id_competency" class="form-control select2" data-validation="required"
                        data-validation-error-msg="Kompetensi tidak valid!" >';
                    $data_opt = $this->master->get_company_competency()->get()->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $form_field['input_field']['html'] .= $opt . '</select>';

                    $form_inflated[$i] = $form_field;
                    $i++;
                }

                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;
            $data['add_scripts'] = [
                base_url('assets/js/page/company_competency.js')
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


    // Custom Get

    public function get_company_contact()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        $data = $this->company->get_contact($id, $id_company);
        echo json_encode(
            $data->order_by('a.main_contact', 'desc')
                ->order_by('a.created_at', 'desc')
                ->get()
                ->result()
        );
    }
    public function get_company_pic()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_pic($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_domicile()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_domicile($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_npwp()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_npwp($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_nib()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_nib($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_document()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_documents($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_tdp()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_tdp($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_siup()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_siup($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_legal_doc()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_legal_doc($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_legal_born_license()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_born_license($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_management()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_management($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_finance_bank()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_finance_bank($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_finance_report()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_finance_report($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }
    public function get_company_certification()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_certification($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_facilities()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_facilities($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_experience()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_experience($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }

    public function get_company_profile()
    {
        $id_user = $this->secure_input($this->input->get('id_user'));
        $id = $this->secure_input($this->input->get('id'));
        $profile = $this->company->get_profile($id_user, $id)->get()->row();

        if ($profile != null) {
            // if (is_file(self::PATH_UPLOAD_COMPANY_LOGO . $profile->logo)) {
            //     $profile->logo = [
            //         'name' => $profile->logo,
            //         'path' => self::PATH_UPLOAD_COMPANY_LOGO . $profile->logo,
            //         'size' => filesize(self::PATH_UPLOAD_COMPANY_LOGO . $profile->logo),
            //         'download_link' => base_url('/upload/company/logo/' . $profile->logo)
            //     ];
            // }
            // if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $profile->company_profile)) {
            //     $profile->company_profile = [
            //         'name' => $profile->company_profile,
            //         'path' => self::PATH_UPLOAD_COMPANY_FILE . $profile->company_profile,
            //         'size' => filesize(self::PATH_UPLOAD_COMPANY_FILE . $profile->company_profile),
            //         'download_link' => base_url('/upload/company/file/' . $profile->company_profile)
            //     ];
            // }
            $result = [
                'profile' => $profile,
                'type' => $this->company->get_type_list($profile->id)->get()->result(),
                'work_area' => $this->company->get_worka_area_list($profile->id)->get()->result(),
                'cabang_area' => $this->company->get_cabang_area_list($profile->id)->get()->result(),
                'contact' => $this->company->get_data_contact(null, $profile->id)->get()->row(),
                'pic' => $this->company->get_data_pic(null, $profile->id)->get()->row(),
                'npwp' => $this->company->get_data_npwp(null, $profile->id)->get()->row(),
                'siup' => $this->company->get_data_siup(null, $profile->id)->get()->row(),
                'tdp' => $this->company->get_data_tdp(null, $profile->id)->get()->row(),
                'nib' => $this->company->get_data_nib(null, $profile->id)->get()->row(),
                'akta' => $this->company->get_data_born(null, $profile->id)->get()->row(),
                'bank' => $this->company->get_data_bank(null, $profile->id)->get()->row(),
                'ktp' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'LAMPIRAN KTP')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'imb' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'LAMPIRAN LEGAL IMB')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'fin_report_1y' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'FINANSIAL_REPORT_1_THN')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'tdg' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'LAMPIRAN LEGAL TDG')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'domisili' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'LAMPIRAN SUKET DOMISILI')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'kebijakan_k3' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'KEBIJAKAN/KOMITMEN')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'tanggap_darurat' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'SISTEM TANGGAP DARURAT')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'iso_450001' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'stuktur_organisasi_k3' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'STUKTUR ORGANISASI K3')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
                'peralatan_k3' => $this->db->where('id_company', $profile->id)
                    ->where('name', 'PERALATAN K3')
                    ->where('deleted_at is null')->get(App_Model::TBL_COMPANY_DOCUMENT)->row(),
            ];
        } else {
            $result = [
                'profile' => null,
                'type' => []
            ];
        }
        echo json_encode($result);
    }

    public function get_company_competencies()
    {
        $id = $this->secure_input($this->input->get('id'));
        $id_company = $this->secure_input($this->input->get('id_company'));
        echo json_encode($this->company->get_company_competencies($id, $id_company)
            ->order_by('a.created_at', 'desc')
            ->get()->result());
    }


    // Custom add

    public function add_data_profile()
    {
        $company_types = $this->input->post('type');
        if (add_data(null, [
            'authorized_capital' => str_replace('.', '', str_replace('Rp', '', $this->secure_input($this->input->post('authorized_capital')))),
            'paid_up_capital' => str_replace('.', '', str_replace('Rp', '', $this->secure_input($this->input->post('paid_up_capital'))))
        ])['success']) {
            $id_company = $this->db
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_PROFILE)
                ->row();
            if ($id_company != null) {
                $id_company = $id_company->id;

                foreach ($company_types as $type) {
                    $this->db->insert(App_Model::TBL_COMPANY_TYPE_LIST, [
                        'id_company' => $id_company,
                        'id_company_type' => $type,
                    ]);
                }

                echo json_encode([
                    'success' => true,
                    'result' => 'Success adding data!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'result' => 'Failed adding data!'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Failed adding data!'
            ]);
        }
    }
    public function edit_data_profile()
    {
        $id_user = $this->input->post('id_user');
        $id_company = $this->db
            ->where('id_user', $id_user)
            ->get(App_Model::TBL_COMPANY_PROFILE)
            ->row();
        $company_types = $this->input->post('type');
        if (edit_data(
            null,
            [
                'updated_at' => date('Y-m-d H:i:s'),
                'authorized_capital' => str_replace('.', '', str_replace('Rp', '', $this->secure_input($this->input->post('authorized_capital')))),
                'paid_up_capital' => str_replace('.', '', str_replace('Rp', '', $this->secure_input($this->input->post('paid_up_capital'))))
            ],
            [
                'id_user' => $id_user
            ]
        )['success']) {
            if ($id_company != null) {
                $logo = $id_company->logo;
                $company_profile = $id_company->company_profile;
                $id_company = $id_company->id;

                if ($this->input->post('logo') != null) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_LOGO . $logo)) {
                        unlink(self::PATH_UPLOAD_COMPANY_LOGO . $logo);
                    }
                }
                if ($this->input->post('company_profile') != null) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $company_profile)) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $company_profile);
                    }
                }

                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_TYPE_LIST);
                foreach ($company_types as $type) {
                    $this->db->insert(App_Model::TBL_COMPANY_TYPE_LIST, [
                        'id_company' => $id_company,
                        'id_company_type' => $type
                    ]);
                }

                //Pending verification if update
                $table_name = $this->input->post('_table');
                $table_id = $this->db->where('id_user', $id_user)
                    ->get($table_name)
                    ->result_array();
                $table_name_id = $this->db->get($table_name)->list_fields()[0];

                $this->db->where('id_user', $id_user)->update($table_name, [
                    'verification_status' => 'Pending Verification',
                    //'verification_note' => null
                ]);
                if ($table_id != null && count($table_id) > 0) {
                    $table_id = $table_id[0];
                    if (isset($table_id['verification_status']) && $table_id['verification_status'] != 'Pending Verification') {
                        //insert verification history
                        $this->db->insert('verification_history', [
                            'verificator' => $this->session->userdata('user')['id_user'],
                            'data_main_table' => $table_name,
                            'data_id' => $table_id[$table_name_id],
                            'verification_status' => 'Pending Verification',
                            //'verification_note' => null
                        ]);
                        //Add notification
                        add_notification($id_user, null, 5, 'Verifikasi Data', 'Verifikasi Data Profil', 'Internal', '#verification/profile_basic');
                    }
                }

                echo json_encode([
                    'success' => true,
                    'result' => 'Success adding data!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'result' => 'Failed adding data!'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Failed adding data!'
            ]);
        }
    }

    public function add_data_legal_domicile()
    {
        $is_exist = false;
        $id_company = $this->secure_input($this->input->post('id_company'));
        $is_exist = $this->db->where('id_company', $id_company)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_LEGAL_DOMICILE)
            ->num_rows() >= 1;
        if ($id_company != null && !$is_exist) {
            $this->add_data_company_with_attachment();
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Data sudah ada, silahkan pilih edit untuk mengubah data.'
            ]);
        }
    }

    public function add_data_legal_npwp()
    {
        $is_exist = false;
        $id_company = $this->secure_input($this->input->post('id_company'));
        $is_exist = $this->db->where('id_company', $id_company)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_LEGAL_NPWP)
            ->num_rows() >= 1;
        if ($id_company != null && !$is_exist) {
            $this->add_data_company_with_attachment(($this->input->post('pkp') == '1'));
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Data sudah ada, silahkan pilih edit untuk mengubah data.'
            ]);
        }
    }

    public function add_data_legal_nib()
    {
        $is_exist = false;
        $id_company = $this->secure_input($this->input->post('id_company'));
        $is_exist = $this->db->where('id_company', $id_company)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_LEGAL_NIB)
            ->num_rows() >= 1;
        if ($id_company != null && !$is_exist) {
            $this->add_data_company_with_attachment();
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Data sudah ada, silahkan pilih edit untuk mengubah data.'
            ]);
        }
    }

    public function add_data_legal_tdp()
    {
        $is_exist = false;
        $id_company = $this->secure_input($this->input->post('id_company'));
        $is_exist = $this->db->where('id_company', $id_company)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_LEGAL_TDP)
            ->num_rows() >= 1;
        if ($id_company != null && !$is_exist) {
            $this->add_data_company_with_attachment();
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Data sudah ada, silahkan pilih edit untuk mengubah data.'
            ]);
        }
    }

    public function add_data_legal_siup()
    {
        $is_exist = false;
        $id_company = $this->secure_input($this->input->post('id_company'));
        $is_exist = $this->db->where('id_company', $id_company)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_LEGAL_SIUP)
            ->num_rows() >= 1;
        if ($id_company != null && !$is_exist) {
            $this->add_data_company_with_attachment();
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Data sudah ada, silahkan pilih edit untuk mengubah data.'
            ]);
        }
    }

    public function add_data_company_certification()
    {
        $this->add_data_company_with_attachment(false);
    }



    var $table_name_for_filter = null;
    var $table_verification_list = array(
        'company_contact',
        'company_pic',
        'company_legal_born_license',
        'company_legal_domicile',
        'company_legal_nib',
        'company_legal_npwp',
        'company_legal_siup',
        'company_legal_tdp',
        'company_finance_bank',
    );


    public function add_data_company_with_attachment($isAttachmentRequird = true)
    {
        $do_upload = $this->upload_company_attachment($isAttachmentRequird);
        $result = [
            'sucesss' => false,
            'result' => 'Add data failed!'
        ];
        if ($do_upload['success']) {
            if (add_data(null, ['attachment' => $do_upload['file_data']['file_name']])['success']) {

                //Pending verification if update
                $table_name = $this->input->post('_table');
                $this->table_name_for_filter = $table_name;
                $is_need_verification = count(array_filter($this->table_verification_list, function ($value) {
                    return $this->table_name_for_filter == $value;
                })) > 0;

                if ($is_need_verification) {

                    $table_name_id = $this->db->get($table_name)->list_fields()[0];

                    $id = $this->db->where('id_company', $this->input->post('id_company'))
                        ->order_by('created_at', 'desc')
                        ->limit(1)
                        ->get($table_name)
                        ->row();
                    $id = $id != null ? $id->id : '';
                    $table_id = $this->db->where($table_name_id, $id)
                        ->get($table_name)
                        ->result_array();

                    $this->db->where($table_name_id, $id)->update($table_name, [
                        'verification_status' => 'Pending Verification',
                        //'verification_note' => null
                    ]);
                    if ($table_id != null && count($table_id) > 0) {
                        $table_id = $table_id[0];
                        if (isset($table_id['verification_status']) && $table_id['verification_status'] == 'Pending Verification') {
                            //insert verification history
                            $this->db->insert('verification_history', [
                                'verificator' => $this->session->userdata('user')['id_user'],
                                'data_main_table' => $table_name,
                                'data_id' => $table_id[$table_name_id],
                                'verification_status' => 'Pending Verification',
                                'verification_note' => isset($table_id['verification_note']) ? $table_id['verification_note'] : null
                            ]);
                            //Add notification
                            $title = '';
                            $desc = '';
                            $link = '';

                            switch ($table_name) {
                                case 'company_legal_born_license': {
                                        $title = "Verifikasi Data Akta";
                                        $desc = "Mohon untuk verifikasi data akta.";
                                        $link = "#verification/company_born_license";
                                        break;
                                    }
                                case 'company_legal_domicile': {
                                        $title = "Verifikasi Data Akta Domisili";
                                        $desc = "Mohon untuk verifikasi data akta domisili.";
                                        $link = "#verification/company_legal_domicile";
                                        break;
                                    }
                                case 'company_legal_npwp': {
                                        $title = "Verifikasi Data NPWP";
                                        $desc = "Mohon untuk verifikasi data NPWP.";
                                        $link = "#verification/company_legal_npwp";
                                        break;
                                    }
                                case 'company_legal_siup': {
                                        $title = "Verifikasi Data SIUP";
                                        $desc = "Mohon untuk verifikasi data SIUP.";
                                        $link = "#verification/company_legal_siup";
                                        break;
                                    }
                                case 'company_legal_tdp': {
                                        $title = "Verifikasi Data TDP";
                                        $desc = "Mohon untuk verifikasi data TDP.";
                                        $link = "#verification/company_legal_domicile";
                                        break;
                                    }
                                case 'company_legal_nib': {
                                        $title = "Verifikasi Data NIB";
                                        $desc = "Mohon untuk verifikasi data NIB.";
                                        $link = "#verification/company_legal_nib";
                                        break;
                                    }
                            }
                            add_notification($this->session->userdata('user')['id_user'], null, 5, $title, $desc, 'Internal', $link);
                        }
                    }
                }

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
            'result' => 'Add data failed!'
        ];
        if ($do_upload['success']) {
            $field_params = ['attachment' => $do_upload['file_data']['file_name']];
            switch ($this->input->post('_table')) {
                case 'company_legal_npwp': {
                        $field_params['no_pkp'] = $this->input->post('no_pkp') == null ? '-' : $this->input->post('no_pkp');
                        $field_params['sppkp_date'] = $this->input->post('sppkp_date') == null ? '-' : $this->input->post('sppkp_date');
                        break;
                    }
                case 'company_legal_born_license': {
                        $field_params['country_news_date'] = $this->input->post('country_news_date') == null ? '-' : $this->input->post('country_news_date');
                        $field_params['no_pengesahaan_kemenhum'] = $this->input->post('no_pengesahaan_kemenhum') == null ? '-' : $this->input->post('no_pengesahaan_kemenhum');
                        $field_params['judical_approval_date'] = $this->input->post('judical_approval_date') == null ? '-' : $this->input->post('judical_approval_date');
                        $field_params['judical_approval_no'] = $this->input->post('judical_approval_no') == null ? '-' : $this->input->post('judical_approval_no');
                        $field_params['notary'] = $this->input->post('notary') == null ? '-' : $this->input->post('notary');
                        $field_params['notary_address'] = $this->input->post('notary_address') == null ? '-' : $this->input->post('notary_address');
                        $field_params['notary_telp'] = $this->input->post('notary_telp') == null ? '-' : $this->input->post('notary_telp');

                        break;
                    }
            }
            if (edit_data(null, $field_params)['success']) {

                //Pending verification if update
                $table_name = $this->input->post('_table');
                $this->table_name_for_filter = $table_name;
                $is_need_verification = count(array_filter($this->table_verification_list, function ($value) {
                    return $this->table_name_for_filter == $value;
                })) > 0;

                if ($is_need_verification) {

                    $table_name_id = $this->db->get($table_name)->list_fields()[0];
                    $table_id = $this->db->where($table_name_id, $this->input->post($table_name_id))
                        ->get($table_name)
                        ->result_array();

                    $this->db->where($table_name_id, $this->input->post($table_name_id))->update($table_name, [
                        'verification_status' => 'Pending Verification',
                        //'verification_note' => null
                    ]);
                    if ($table_id != null && count($table_id) > 0) {
                        $table_id = $table_id[0];
                        if (isset($table_id['verification_status']) && $table_id['verification_status'] != 'Pending Verification') {
                            //insert verification history
                            $this->db->insert('verification_history', [
                                'verificator' => $this->session->userdata('user')['id_user'],
                                'data_main_table' => $table_name,
                                'data_id' => $table_id[$table_name_id],
                                'verification_status' => 'Pending Verification',
                                'verification_note' => isset($table_id['verification_note']) ? $table_id['verification_note'] : null
                            ]);
                            //Add notification
                            $title = '';
                            $desc = '';
                            $link = '';

                            switch ($table_name) {
                                case 'company_legal_born_license': {
                                        $title = "Verifikasi Data Akta";
                                        $desc = "Mohon untuk verifikasi data akta.";
                                        $link = "#verification/company_born_license";
                                        break;
                                    }
                                case 'company_legal_domicile': {
                                        $title = "Verifikasi Data Akta Domisili";
                                        $desc = "Mohon untuk verifikasi data akta domisili.";
                                        $link = "#verification/company_legal_domicile";
                                        break;
                                    }
                                case 'company_legal_npwp': {
                                        $title = "Verifikasi Data NPWP";
                                        $desc = "Mohon untuk verifikasi data NPWP.";
                                        $link = "#verification/company_legal_npwp";
                                        break;
                                    }
                                case 'company_legal_siup': {
                                        $title = "Verifikasi Data SIUP";
                                        $desc = "Mohon untuk verifikasi data SIUP.";
                                        $link = "#verification/company_legal_siup";
                                        break;
                                    }
                                case 'company_legal_tdp': {
                                        $title = "Verifikasi Data TDP";
                                        $desc = "Mohon untuk verifikasi data TDP.";
                                        $link = "#verification/company_legal_domicile";
                                        break;
                                    }
                                case 'company_legal_nib': {
                                        $title = "Verifikasi Data NIB";
                                        $desc = "Mohon untuk verifikasi data NIB.";
                                        $link = "#verification/company_legal_nib";
                                        break;
                                    }
                            }
                            add_notification($this->session->userdata('user')['id_user'], null, 5, $title, $desc, 'Internal', $link);
                        }
                    }
                }

                $result['success'] = true;
                $result['result'] = 'Add data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }

    public function edit_data_contact()
    {
        $edit = $this->edit_data(false);
        $result = [
            'success' => false,
            'result' => 'Failed edit data!'
        ];
        if ($edit['success']) {
            if ($this->input->post('main_contact') == 1) {
                $result['success'] = $this->db
                    ->where('id_company', $this->input->post('id_company'))
                    ->where("id!='" . $this->input->post('id') . "'")
                    ->update(App_Model::TBL_COMPANY_CONTACT, ['main_contact' => 0]);
            } else {
                $is_exist_main_contact = $this->db
                    ->where('id_company', $this->input->post('id_company'))
                    ->where('main_contact', 1)
                    ->get(App_Model::TBL_COMPANY_CONTACT)->num_rows() > 0;

                if (!$is_exist_main_contact) {
                    $result['success'] = $this->db
                        ->where('id_company', $this->input->post('id_company'))
                        ->where("id='" . $this->input->post('id') . "'")
                        ->update(App_Model::TBL_COMPANY_CONTACT, ['main_contact' => 1]);
                } else {
                    $result['success'] = true;
                }
            }
        }

        if ($result['success'] == true) $result['result'] = 'Success edit data!';
        echo json_encode($result);
    }

    public function add_data_contact()
    {
        $edit = $this->add_data(false);
        $result = [
            'success' => false,
            'result' => 'Failed add data!',
            'id' => null
        ];
        if ($edit['success']) {

            $id = $this->db
                ->where('id_company', $this->input->post('id_company'))
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_CONTACT)->row()->id;
            $result['id'] = $id;

            if ($this->input->post('main_contact') == 1) {
                $result['success'] = $this->db
                    ->where('id_company', $this->input->post('id_company'))
                    ->where("id!='" . $id . "'")
                    ->update(App_Model::TBL_COMPANY_CONTACT, ['main_contact' => 0]);
            } else {
                $is_exist_main_contact = ($this->db
                    ->where('id_company', $this->input->post('id_company'))
                    ->where('main_contact', 1)
                    ->get(App_Model::TBL_COMPANY_CONTACT)->num_rows() <= 0);

                if ($is_exist_main_contact) {
                    $result['success'] = $this->db
                        ->where('id_company', $this->input->post('id_company'))
                        ->where("id", $id)
                        ->update(App_Model::TBL_COMPANY_CONTACT, ['main_contact' => 1]);
                } else {
                    $result['success'] = true;
                }
            }
        }

        if ($result['success'] == true) $result['result'] = 'Success add data!';
        echo json_encode($result);
    }



    // Master add
    public function add_data_competency($json_return = true)
    {

        //check_duplicate
        $id_company = $this->input->post('id_company');
        $id_competency = $this->input->post('id_competency');
        $id_company_sub_competency = $this->input->post('id_company_sub_competency');
        $is_duplicate = $this->db->where('id_company', $id_company)
            ->where('id_company_sub_competency', $id_company_sub_competency)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_COMPETENCIES)
            ->num_rows() > 0;
        if ($is_duplicate) {
            $data = [
                'success' => false,
                'result' => 'Kompetensi sudah ada'
            ];
        } else {
            $data = add_data();
        }
        if ($json_return) {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function edit_data_competency($json_return = true)
    {
        //check_duplicate
        $id_company = $this->input->post('id_company');
        $id_competency = $this->input->post('id_competency');
        $id_company_sub_competency = $this->input->post('id_company_sub_competency');
        $is_duplicate = $this->db->where('id_company', $id_company)
            ->where('id_company_sub_competency', $id_company_sub_competency)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_COMPANY_COMPETENCIES)
            ->num_rows() > 0;
        if ($is_duplicate) {
            $data = [
                'success' => false,
                'result' => 'Kompetensi sudah ada'
            ];
        } else {
            $data = edit_data();
        }
        if ($json_return) {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function add_data($json_return = true)
    {
        $data = add_data();
        //Pending verification if update
        $table_name = $this->input->post('_table');
        $this->table_name_for_filter = $table_name;
        $is_need_verification = count(array_filter($this->table_verification_list, function ($value) {
            return $this->table_name_for_filter == $value;
        })) > 0;

        if ($is_need_verification) {

            $table_name_id = $this->db->get($table_name)->list_fields()[0];
            $id = $this->db->where('id_company', $this->input->post('id_company'))
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get($table_name)
                ->row();
            $id = $id != null ? $id->id : '';
            $table_id = $this->db->where($table_name_id, $id)
                ->get($table_name)
                ->result_array();

            $this->db->where($table_name_id, $id)->update($table_name, [
                'verification_status' => 'Pending Verification',
                //'verification_note' => null
            ]);
            if ($table_id != null && count($table_id) > 0) {
                $table_id = $table_id[0];
                if (isset($table_id['verification_status']) && $table_id['verification_status'] == 'Pending Verification') {
                    //insert verification history
                    $this->db->insert('verification_history', [
                        'verificator' => $this->session->userdata('user')['id_user'],
                        'data_main_table' => $table_name,
                        'data_id' => $table_id[$table_name_id],
                        'verification_status' => 'Pending Verification',
                        'verification_note' => isset($table_id['verification_note']) ? $table_id['verification_note'] : null
                    ]);
                    //Add notification
                    $title = '';
                    $desc = '';
                    $link = '';

                    switch ($table_name) {
                        case 'company_contact': {
                                $title = "Verifikasi Data Kontak";
                                $desc = "Mohon untuk verifikasi data kontak.";
                                $link = "#verification/company_contact";
                                break;
                            }
                        case 'company_pic': {
                                $title = "Verifikasi Data PIC";
                                $desc = "Mohon untuk verifikasi data PIC.";
                                $link = "#verification/company_pic";
                                break;
                            }
                        case 'company_legal_born_license': {
                                $title = "Verifikasi Data Akta";
                                $desc = "Mohon untuk verifikasi data akta.";
                                $link = "#verification/company_born_license";
                                break;
                            }
                        case 'company_legal_domicile': {
                                $title = "Verifikasi Data Akta Domisili";
                                $desc = "Mohon untuk verifikasi data akta domisili.";
                                $link = "#verification/company_legal_domicile";
                                break;
                            }
                        case 'company_legal_npwp': {
                                $title = "Verifikasi Data NPWP";
                                $desc = "Mohon untuk verifikasi data NPWP.";
                                $link = "#verification/company_legal_npwp";
                                break;
                            }
                        case 'company_legal_siup': {
                                $title = "Verifikasi Data SIUP";
                                $desc = "Mohon untuk verifikasi data SIUP.";
                                $link = "#verification/company_legal_siup";
                                break;
                            }
                        case 'company_legal_tdp': {
                                $title = "Verifikasi Data TDP";
                                $desc = "Mohon untuk verifikasi data TDP.";
                                $link = "#verification/company_legal_domicile";
                                break;
                            }
                        case 'company_legal_nib': {
                                $title = "Verifikasi Data NIB";
                                $desc = "Mohon untuk verifikasi data NIB.";
                                $link = "#verification/company_legal_nib";
                                break;
                            }
                        case 'company_finance_bank': {
                                $title = "Verifikasi Data Akun Bank";
                                $desc = "Mohon untuk verifikasi data akun bank.";
                                $link = "#verification/company_finance_bank";
                                break;
                            }
                    }
                    add_notification($this->session->userdata('user')['id_user'], null, 5, $title, $desc, 'Internal', $link);
                }
            }
        }

        $result['success'] = true;
        $result['result'] = 'Add data success!';
        if ($json_return) {
            echo json_encode($data);
        } else {
            return $data;
        }
    }

    public function get_data()
    {
        echo json_encode(get_data());
    }

    public function edit_data($json_return = true)
    {
        $field_params = [];
        switch ($this->input->post('_table')) {
            case 'company_contact': {
                    $field_params['phone2'] = $this->input->post('phone2') == null ? '-' : $this->input->post('phone2');
                    $field_params['fax'] = $this->input->post('fax') == null ? '-' : $this->input->post('fax');
                    $field_params['website'] = $this->input->post('website') == null ? '-' : $this->input->post('website');
                    break;
                }
            case 'company_legal_born_license': {
                    $field_params['country_news_date'] = $this->input->post('country_news_date') == null ? '-' : $this->input->post('country_news_date');
                    $field_params['no_pengesahaan_kemenhum'] = $this->input->post('no_pengesahaan_kemenhum') == null ? '-' : $this->input->post('no_pengesahaan_kemenhum');
                    $field_params['judical_approval_date'] = $this->input->post('judical_approval_date') == null ? '-' : $this->input->post('judical_approval_date');
                    $field_params['judical_approval_no'] = $this->input->post('judical_approval_no') == null ? '-' : $this->input->post('judical_approval_no');
                    $field_params['notary'] = $this->input->post('notary') == null ? '-' : $this->input->post('notary');
                    $field_params['notary_address'] = $this->input->post('notary_address') == null ? '-' : $this->input->post('notary_address');
                    $field_params['notary_telp'] = $this->input->post('notary_telp') == null ? '-' : $this->input->post('notary_telp');
                    break;
                }
        }
        $do_edit = edit_data(null, $field_params);
        //Pending verification if update
        $table_name = $this->input->post('_table');
        $this->table_name_for_filter = $table_name;
        $is_need_verification = count(array_filter($this->table_verification_list, function ($value) {
            return $this->table_name_for_filter == $value;
        })) > 0;

        if ($is_need_verification) {

            $table_name_id = $this->db->get($table_name)->list_fields()[0];
            $table_id = $this->db->where($table_name_id, $this->input->post($table_name_id))
                ->get($table_name)
                ->result_array();

            $this->db->where($table_name_id, $this->input->post($table_name_id))->update($table_name, [
                'verification_status' => 'Pending Verification',
                //'verification_note' => null
            ]);
            if ($table_id != null && count($table_id) > 0) {
                $table_id = $table_id[0];
                if (isset($table_id['verification_status']) && $table_id['verification_status'] != 'Pending Verification') {
                    //insert verification history
                    $this->db->insert('verification_history', [
                        'verificator' => $this->session->userdata('user')['id_user'],
                        'data_main_table' => $table_name,
                        'data_id' => $table_id[$table_name_id],
                        'verification_status' => 'Pending Verification',
                        'verification_note' => isset($table_id['verification_note']) ? $table_id['verification_note'] : null
                    ]);
                    //Add notification
                    $title = '';
                    $desc = '';
                    $link = '';

                    switch ($table_name) {
                        case 'company_contact': {
                                $title = "Verifikasi Data Kontak";
                                $desc = "Mohon untuk verifikasi data kontak.";
                                $link = "#verification/company_contact";
                                break;
                            }
                        case 'company_pic': {
                                $title = "Verifikasi Data PIC";
                                $desc = "Mohon untuk verifikasi data PIC.";
                                $link = "#verification/company_pic";
                                break;
                            }
                        case 'company_finance_bank': {
                                $title = "Verifikasi Data Akun Bank";
                                $desc = "Mohon untuk verifikasi data akun bank.";
                                $link = "#verification/company_finance_bank";
                                break;
                            }
                    }
                    add_notification($this->session->userdata('user')['id_user'], null, 5, $title, $desc, 'Internal', $link);
                }
            }
        }

        if ($json_return) {
            echo json_encode($do_edit);
        } else {
            return $do_edit;
        }
    }

    public function delete_data()
    {
        echo json_encode(delete_data());
    }
}
