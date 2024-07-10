<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sap_sync extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'add_data'],
                ['method' => 'edit_data'],
                ['method' => 'delete_data'],
                ['method' => 'get_data'],
                ['method' => 'add_data_vendor'],
                ['method' => 'edit_data_vendor'],
                ['method' => 'delete_data_vendor'],
                ['method' => 'get_data_vendor'],
                ['method' => 'vendor'],
            ]
        ]);

        $this->load->model('Company_competency_model', 'competency');
        $this->load->model('Company_type_model', 'type');
        $this->load->model('Company_subcompetency_model', 'sub_competency');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Company_model', 'company');
        $this->load->model('Country_province_model', 'province');

        $this->load->model('User_status_model', 'user_status');
        $this->load->model('User_role_model', 'user_role');
        $this->load->model('User_model', 'user');
        $this->load->model('Master_model', 'master');
        $this->load->model('SAP_model', 'sap');
    }


    public function vendor()
    {
        if ($this->is_as_admin()) {

            $table_name = App_Model::TBL_SAP_SYNC;
            $table_fields = $this->sap->get_data()->get()->list_fields();
            $fields_exception = ['id', 'id_company', 'id_group', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'updated_at' => [
                    'text' => 'Last Sync'
                ],
                'id_sap' => [
                    'text' => 'SAP Partner ID'
                ]
            ]);

            $this->set_page_title('pe-7s-user', 'SAP Sync Vendor', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'SAP Sync'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Vendor'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'SAP Sync - Vendor';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'sap_sync/get_data_vendor';
            $data['delete_url'] = 'sap_sync/delete_data';
            $data['update_url'] = 'sap_sync/edit_data_vendor';
            $data['add_url'] = 'sap_sync/add_data_vendor';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at', 'id_sap'];

            $form_inflated = [];
            $form = form_builder($table_name, $fields_exception, [
                'vendor_gl_number' => [
                    'data-validation' => 'required',
                    'data-validation-error-msg' => 'No GL Vendor tidak valid!',
                    'placeholder' => 'No GL Vendor'
                ],
            ], [
                'vendor_gl_number' => [
                    'text' => 'No.GL Vendor<span style="color:red;">*</span>'
                ],
                'id_company' => [
                    'text' => 'Vendor<span style="color:red;">*</span>'
                ],
                'id_group' => [
                    'text' => 'Grup<span style="color:red;">*</span>'
                ],
            ]);

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                    data-validation-error-msg="Perusahaan tidak valid!" >';
                    $data_opt = $this->company->get(null, null, [], [
                        '(c.id_usr_role=2 or c.id_usr_role=6 or c.id_usr_role=7)',
                        'a.id not in (select id_company from tbl_sync_sap where deleted_at is null)'
                    ]);
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }
                if ($f['input_field']['attr']['id'] == 'id_group') {
                    $f['input_field']['html'] = '<select type="select" id="id_group" name="id_group" class="form-control" data-validation="required"
                    data-validation-error-msg="Grup tidak valid!" >';
                    $data_opt = $this->db->where('deleted_at is null')->get(App_Model::TBL_GROUP_VENDOR)->result();
                    $opt = '<option value="">Pilih</option>';
                    foreach ($data_opt as $o) {
                        $opt .= '<option value="' . $o->id . '">' . $o->name . '</option>';
                    }
                    $f['input_field']['html'] .= $opt . '</select>';
                }
                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;

            $data['add_scripts'] = [];
            $data['action_delete'] = 'false';

            $data['render_column_modifier'] = '{}';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Not Authorized!');
            window.location.href='" . site_url('app') . "';
            location.reload();
            </script>";
        }
    }



    public function add_data_vendor()
    {
        $profile = $this->db
            ->where('a.id', $this->input->post('id_company'))
            ->join(App_Model::TBL_USER . ' b', 'a.id_user=b.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' c', 'b.id_usr_role=c.id_usr_role')
            ->get(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->row();
        $insert_sap = $this->sap->input_sap($this->input->post('id_company'), $profile->id_usr_role);
        echo json_encode($insert_sap);
    }
    public function edit_data_vendor()
    {
        echo json_encode(edit_data());
    }
    public function delete_data_vendor()
    {
        echo json_encode(delete_data());
    }
    public function get_data_vendor()
    {
        echo json_encode($this->sap->get_data()->get()->result());
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
}
