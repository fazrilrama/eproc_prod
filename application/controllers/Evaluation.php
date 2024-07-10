<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Evaluation extends App_Controller
{

    const PATH_UPLOAD_COMPANY_FILE = FCPATH . '/upload/company/file/';

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get_data'],
                ['method' => 'add_data'],
                ['method' => 'edit_data'],
                ['method' => 'delete_data'],
                ['method' => 'get'],
                ['method' => 'project_feedback'],

                ['method' => 'add_data_feedback'],
                ['method' => 'edit_data_feedback'],
                ['method' => 'delete_data_feedback'],
                ['method' => 'get_feedback'],
            ]
        ]);
        $this->load->model('Company_model', 'company');
        $this->load->model('Company_type_model', 'company_type');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Master_model', 'master');
    }

    //vendor valuation new
    public function project_feedback()
    {
        $this->set_page_title('pe-7s-user', 'Penilaian Vendor', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Evaluasi'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Penilaian Vendor'
            ]
        ]);
        $this->load->view('pages/project_feedback/main');
    }

    public function get_feedback()
    {
        if($this->session->userdata('user')['id_usr_role']==3){
            $this->db->where('company.id_company_owner', $this->session->userdata('user')['id_company_owner'] );
        }
        
        $data = $this->db
            ->select('feedback.*,project.name as project_name,project.contract_no as project_contract_no
            ,concat(if(company.prefix_name is null,"",concat(company.prefix_name," ") ),company.name) as vendor_name
            ,if(company_contact.email is null,company_user.email,company_user.email) as vendor_email
            ,customer.name as cust_name
            ,customer.email as cust_email
            ,project.start_date as project_start_date
            ,project.end_date as project_end_date')
            ->where('feedback.deleted_at is null')
            ->join('project', 'project.id=feedback.id_project')
            ->join('sys_user customer', 'customer.id_user=feedback.id_user')
            ->join('company_profile company', 'company.id=project.winner')
            ->join('sys_user company_user', 'company.id_user=company_user.id_user')
            ->join('company_contact company_contact', 'company_contact.id_company=company.id')
            ->get('tbl_vendor_valuation feedback')
            ->result();

        echo json_encode($data);
    }

    
    public function add_data_feedback()
    {
        $result = [
            'sucesss' => false,
            'result' => 'Add data failed!'
        ];
        if (add_data('tbl_vendor_valuation')) {
            $result['result'] = "Add data success!";
            $result['success'] = true;
        }

        echo json_encode($result);
    }

    public function edit_data_feedback()
    {
        $result = [
            'sucesss' => false,
            'result' => 'Edit data failed!'
        ];
        if (edit_data('tbl_vendor_valuation')) {
            $result['result'] = "Edit data success!";
            $result['success'] = true;
        }

        echo json_encode($result);
    }

    public function delete_data_feedback()
    {
        echo json_encode(delete_data('tbl_vendor_valuation', 'id'));
    }
    //vendor valuation new


    public function vendor_valuation()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company
            ->get_profile($id_user)
            ->where('a.verification_status', App_Model::VERIFICATION_STATUS_VERIFIED)
            ->get()
            ->row();

        if ($company != '-1' || $this->is_as_admin()) {

            $table_name = 'tbl_vendor_valuation';
            if ($company != null) {
                $table_fields = $this->get_vendor_valuation()->get()->list_fields();
            } else {
                $table_fields = $this->get_vendor_valuation()->get()->list_fields();
            }
            $fields_exception = ['id', 'id_user', 'id_company', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'id_user' => [
                    'text' => 'Customer/User'
                ],
                'id_company' => [
                    'text' => 'Vendor'
                ],
                'project_name' => [
                    'text' => 'Proyek'
                ],
                'project_start_date' => [
                    'text' => 'Waktu Mulai'
                ],
                'project_end_date' => [
                    'text' => 'Waktu Selesai'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'Penilaian Vendor', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Evaluasi'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Penilaian Vendor'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Evaluasi - Penilaian Vendor';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'evaluation/get';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'evaluation/delete_data';
            $data['update_url'] = 'evaluation/edit_data';
            $data['add_url'] = 'evaluation/add_data';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'project_name' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Deskripsi Proyek tidak valid!',
                    'placeholder' => 'Proyek'
                ],
                'project_start_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Waktu mulai proyek tidak valid!',
                    'placeholder' => 'Waktu mulai'
                ],
                'project_end_date' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'Waktu selesai proyek tidak valid!',
                    'placeholder' => 'Waktu selesai'
                ],
            ], [
                'id_company' => [
                    'text' => 'Perusahaan<span style="color:red;">*</span>'
                ],
                'id_user' => [
                    'text' => 'Customer/User<span style="color:red;">*</span>'
                ],
                'project_name' => [
                    'text' => 'Deskripsi Proyek<span style="color:red;">*</span>'
                ],
                'project_start_date' => [
                    'text' => 'Waktu mulai<span style="color:red;">*</span>'
                ],
                'project_end_date' => [
                    'text' => 'Waktu selesai<span style="color:red;">*</span>'
                ],
                'aspek_mutu' => [
                    'text' => 'Nilai Aspek Mutu<span style="color:red;">*</span>'
                ],
                'aspek_harga' => [
                    'text' => 'Nilai Aspek Harga<span style="color:red;">*</span>'
                ],
                'aspek_waktu_pekerjaan' => [
                    'text' => 'Nilai Aspek Waktu Pekerjaan<span style="color:red;">*</span>'
                ],
                'aspek_pembayaran' => [
                    'text' => 'Nilai Aspek Pembayaran<span style="color:red;">*</span>'
                ],
                'aspek_k3ll' => [
                    'text' => 'Nilai Aspek K3LL<span style="color:red;">*</span>'
                ],
                'attachment' => [
                    'text' => 'Lampiran File (optional)'
                ],

            ]);

            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_user') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_user" name="id_user" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_user" name="id_user" class="form-control select2" data-validation="required"
                        data-validation-error-msg="Customer/User tidak valid!" >';
                        $data_opt = $this->db->where('id_usr_role', 3)->or_where('id_usr_role', 8)->get('sys_user')->result();
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id_user . '">' . $o->name . ' | ' . $o->email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                } else if ($f['input_field']['attr']['id'] == 'id_company') {
                    $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control select2" data-validation="required"
                        data-validation-error-msg="Target Perusahaan tidak valid!" >';
                    $data_opt = $this->db
                        ->select('a.*,c.role_name, b.email as user_email')
                        ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
                        ->join(App_Model::TBL_USER . ' b', 'a.id_user=b.id_user')
                        ->join(App_Model::TBL_USR_ROLE . ' c', 'b.id_usr_role=c.id_usr_role')
                        ->join(App_Model::TBL_SAP_SYNC . ' d', 'd.id_company=a.id')
                        ->get()
                        ->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                } else if ($f['input_field']['attr']['id'] == 'aspek_mutu') {
                    $form_id = "aspek_mutu";
                    $form_error = "Aspek Mutu tidak valid!";
                    $f['input_field']['html'] = '
                    <select type="select" id="' . $form_id . '" name="' . $form_id . '" class="form-control" data-validation="required"
                        data-validation-error-msg="' . $form_error . '" >
                        <option value="1">1 (Kerusakan Diatas 10%)</option>
                        <option value="2">2 (Terdapat Kerusakan 6 s/d 10%)</option>
                        <option value="3">3 (Terdapat Kerusakan 1 s/d 5%)</option>
                        <option value="4">4 (Tidak Ada Kerusakan/Zero Defect)</option>
                        </select>

                    ';
                } else if ($f['input_field']['attr']['id'] == 'aspek_harga') {
                    $form_id = "aspek_harga";
                    $form_error = "Aspek Harga tidak valid!";
                    $f['input_field']['html'] = '<select type="select" id="' . $form_id . '" name="' . $form_id . '" class="form-control" data-validation="required"
                        data-validation-error-msg="' . $form_error . '" >
                        <option value="1">1 (Diatas 20% lebih murah dari rekanan pembanding)</option>
                        <option value="2">2 (10 s/d 15% lebih murah dari rekanan pembanding)</option>
                        <option value="3">3 (16 s/d 20% lebih murah dari rekanan pembanding)</option>
                        <option value="4">4 (1 s/d 9% lebih murah dari rekanan pembanding atau sama, dan/atau jika tidak ada pembanding)</option>
                        </select>';
                } else if ($f['input_field']['attr']['id'] == 'aspek_waktu_pekerjaan') {
                    $form_id = "aspek_waktu_pekerjaan";
                    $form_error = "Aspek Waktu Pekerjaan tidak valid!";
                    $f['input_field']['html'] = '<select type="select" id="' . $form_id . '" name="' . $form_id . '" class="form-control" data-validation="required"
                        data-validation-error-msg="' . $form_error . '" >
                        <option value="1">1 (Terlambat lebih dari 4 hari)</option>
                        <option value="2">2 (Terlambat 3 s/d 4 hari)</option>
                        <option value="3">3 (Terlambat 1 s/d 2 hari)</option>
                        <option value="4">4 (Pengiriman/Pekerjaan tepat waktu)</option>
                        </select>';
                } else if ($f['input_field']['attr']['id'] == 'aspek_pembayaran') {
                    $form_id = "aspek_pembayaran";
                    $form_error = "Aspek Pembayaran tidak valid!";
                    $f['input_field']['html'] = '<select type="select" id="' . $form_id . '" name="' . $form_id . '" class="form-control" data-validation="required"
                        data-validation-error-msg="' . $form_error . '" >
                        <option value="1">1 (Pembayaran dibawah 2 minggu)</option>
                        <option value="2">2 (Pembayaran 2 s/d 3 minggu)</option>
                        <option value="3">3 (Pembayaran diatas 3 minggu s/d 4 minggu)</option>
                        <option value="4">4 (Pembayaran lebih dari 4 minggu)</option>
                        </select>';
                } else if ($f['input_field']['attr']['id'] == 'aspek_k3ll') {
                    $form_id = "aspek_k3ll";
                    $form_error = "Aspek K3LL tidak valid!";
                    $f['input_field']['html'] = '<select type="select" id="' . $form_id . '" name="' . $form_id . '" class="form-control" data-validation="required"
                        data-validation-error-msg="' . $form_error . '" >
                        <option value="1">1 (Ada pencemaran dan kecelakaan kerja)</option>
                        <option value="2">2 (Ada pencemaran atau kecelakaan kerja)</option>
                        <option value="3">3 (Ada ketentuan peraturan K3LL perusahaan yang dilanggar tetapi tidak ada pencemaran dan kecelakaan kerja)</option>
                        <option value="4">4 (Memenuhi peraturan K3LL perusahaan dan tidak ada pencemaran maupun kecelakaan kerja)</option>
                        </select>';
                } else if ($f['input_field']['attr']['id'] == 'attachment') {
                    $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="attachment" name="attachment" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="pdf, png, jpeg, jpg"  />';
                }


                $form_inflated[] = $f;
            }

            $data['form'] = $form_inflated;

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
                }
            }';
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

    private function get_vendor_valuation()
    {
        return $this->db->select('concat(b.name," ",b.email) as customer_name, concat(if(c.prefix_name is null,"",concat(c.prefix_name," ") ),c.name) as company_name,a.*')
            ->from('tbl_vendor_valuation a')
            ->join('sys_user b', 'a.id_user=b.id_user')
            ->join('company_profile c', 'a.id_company=c.id')
            ->where('a.deleted_at is null');
    }

    public function get()
    {
        $data = $this->get_vendor_valuation();
        $id_company = $this->input->get('id_company');
        $id = $this->input->get('id');
        if ($id_company != null) $data->where('b.id', $id_company);
        if ($id != null) $data->where('a.id', $id);

        echo json_encode(
            $data->get()->result()
        );
    }


    // Master add
    public function add_data()
    {

        $do_upload = $this->upload_company_attachment(false);
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

    public function get_data()
    {
        echo json_encode(get_data());
    }

    public function edit_data()
    {
        $do_upload = $this->upload_company_attachment(false);
        $result = [
            'sucesss' => false,
            'result' => 'Add data failed!'
        ];
        if ($do_upload['success']) {
            if (edit_data(null, ['attachment' => $do_upload['file_data']['file_name']])['success']) {
                $result['success'] = true;
                $result['result'] = 'Add data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }

    public function delete_data()
    {
        echo json_encode(delete_data());
    }
}
