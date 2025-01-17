<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Required_info extends App_Controller
{
    public function __construct()
    {
        $allowed_user = [
            1, 2, 6, 7, 3
        ];
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'check_required_form_validation', 'id_role' => $allowed_user],
                ['method' => 'block_after_timeout', 'id_role' => $allowed_user],
                ['method' => 'submit_registration', 'id_role' => $allowed_user],
                ['method' => 'registration_company', 'id_role' => $allowed_user],
                ['method' => 'registration_personal', 'id_role' => $allowed_user],
                ['method' => 'edit_data_profile', 'id_role' => $allowed_user],
                ['method' => 'data_perusahaan'],
                ['method' => 'view_data_perusahaan'],
                ['method' => 'get_data_perusahaan'],
                ['method' => 'edit_data_profile_dashboard', 'id_role' => $allowed_user],

            ]
        ]);

        $this->load->model('Email_model', 'email_helper');
    }


    public function data_perusahaan()
    {
        $this->load->view('pages/required_info/data_perusahaan');
    }

    public function view_data_perusahaan()
    {
        $id_user = $this->input->get('id_user');
        $user = $this->db->where('id_user', $id_user)->get(App_Model::TBL_USER)->row();
        $data['id_user'] = $user->id_user;
        if ($user->id_usr_role == App_Model::ROLE_VENDOR_PERSONAL) {
            $this->load->view('pages/required_info/registration_person_dashboard', $data);
        } else {
            $this->load->view('pages/required_info/registration_company_dashboard', $data);
        }
    }

    public function getDataPakta($id_company = null) {
        $this->db->select('*');
        $this->db->from('company_pakta');
        $this->db->where('company_id', $id_company);
        $query = $this->db->get();
        return $query;
    }

    public function get_data_perusahaan()
    {
        $jenis_vendor = $this->input->get('jenis_vendor');
        $id_user = $this->input->get('id_user');
        $role_id = $this->session->userdata('user')['id_usr_role'];
        $company_owner = $this->session->userdata('user')['id_company_owner'];

        if ($jenis_vendor != null) {
            $this->db->where('c.id_usr_role', $jenis_vendor);
        }
        if ($id_user != null) {
            $this->db->where('c.id_user', $id_user);
        }
        if ($role_id==3){
            $this->db->where('a.id_company_owner',$company_owner);
        }

        echo json_encode($this->db->select('a.*,c.id_usr_role,c.id_usr_status,b.address,b.email,b.phone,d.role_name, e.name as group_name, e.description as group_description, f.id_sap')
            ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->join(App_Model::TBL_COMPANY_CONTACT . ' b', 'a.id=b.id_company')
            ->join(App_Model::TBL_SAP_SYNC . ' f', 'a.id=f.id_company')
            ->join(App_Model::TBL_USER . ' c', 'a.id_user=c.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' d', 'd.id_usr_role=c.id_usr_role')
            ->join(App_Model::TBL_GROUP_VENDOR . ' e', 'a.id_group=e.id')
            ->where('a.deleted_at is null')
            ->where('c.id_usr_status!=5')
            ->where('c.id_usr_status!=6')
            ->group_by('a.id')
            ->get()
            ->result());
    }

    public function check_required_form_validation()
    {
        echo json_encode(check_required_form_validation($this->session->userdata('user')['id_user'], true, "!='Rejected'"));
    }


    public function block_after_timeout()
    {
        $email_target = $this->session->userdata('user')['email'];
        $email_subject = 'Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
        $user_role = $this->session->userdata('user')['role_name'];
        $this->email_helper->send_email(
            $this->config->item('app_info')['identity']['name'],
            $email_target,
            $email_subject,
            '<center>
        <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
        <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
        <hr>
        <p> Terima kasih telah mencoba mendaftar sebagai ' . $user_role . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                . '. Mohon maaf saat ini pendaftaran Anda tidak dapat dilanjutkan dan akun Anda telah dihapus dari sistem karena tidak memenuhi Informasi Wajib
                yang harus diisi dalam kurun waktu ' . get_settings()->token_verify_lifetime . ' jam setelah pendaftaran Akun.
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

        echo json_encode([
            'success' => $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                ->update(App_Model::TBL_USER, [
                    'deleted_at' => date('Y-m-d H:i:s')
                ])
        ]);
    }

    public function submit_registration()
    {
        $result = json_encode([
            'success' => false,
            'msg' => 'Data anda belum menyelesaikan semua form!'
        ]);
        if (check_required_form_validation($this->session->userdata('user')['id_user'])->percentage == 100) {

            $email_target = $this->session->userdata('user')['email'];
            $email_subject = 'Registrasi Vendor E-Procurement PT.BGR LOGISTIK INDONESIA';
            $user_role = $this->session->userdata('user')['role_name'];
            $this->email_helper->send_email(
                $this->config->item('app_info')['identity']['name'],
                $email_target,
                $email_subject,
                '<center>
            <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
            <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
            <hr>
            <p> Terima kasih telah mendaftar dan melengkapi Informasi Wajib sebagai ' . $user_role . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                    . '. Mohon untuk menunggu verifikasi data Anda, informasi hasil verifikasi akan dikirimkan melalui Email.
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
            $submit = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                ->update(App_Model::TBL_USER, [
                    'id_usr_status' => App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE
                ]);

            $this->db
                ->where('from', $this->session->userdata('user')['id_user'])
                ->update(App_Model::TBL_NOTIFICATION, [
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            $result = json_encode([
                'success' => $submit,
                'msg' => 'Terima kasih telah melengkapi data, Kami akan segera mengirim informasi konfirmasi validasi data melalui email Anda.'
            ]);
        }

        echo $result;
    }


    public function registration_company()
    {
        $this->load->view('pages/required_info/registration_company');
    }

    public function registration_personal()
    {
        $this->load->view('pages/required_info/registration_person');
    }

    public function edit_data_profile()
    {
        $id_user = $this->input->post('id_user');
        $company_types = $this->input->post('type');

        $field_params_cp['updated_at'] = date('Y-m-d H:i:s');
        $do_upload = $this->upload_company_attachment(true, "company_profile");
        if ($do_upload['success']) {
            $field_params_cp['company_profile'] = $do_upload['file_data']['file_name'];
        }

        //Change jenis vendor
        $this->db->where('id_user', $id_user)
            ->update(App_Model::TBL_USER, [
                'id_usr_role' => $this->input->post('id_usr_role'),
                'id_usr_status' => 6,
            ]);


        if ($this->input->post("action_profile") == "edit") {
            $id_company = $this->db
                ->where('id_user', $id_user)
                ->get(App_Model::TBL_COMPANY_PROFILE)
                ->row();
            $save_compro = edit_data(
                'company_profile',
                $field_params_cp,
                [
                    'id_user' => $id_user
                ]
            );
        } else {
            $save_compro = add_data(
                'company_profile',
                $field_params_cp,
                [
                    'id_user' => $id_user
                ]
            );
            $id_company = $this->db
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_PROFILE)
                ->row();
        }


        if ($save_compro['success']) {
            if ($id_company != null) {
                $logo = $id_company->logo;
                $company_profile = $id_company->company_profile;
                $id_company = $id_company->id;

                // if ($company_profile != null && isset($field_params_cp['company_profile'])) {
                //     if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $company_profile)) {
                //         unlink(self::PATH_UPLOAD_COMPANY_FILE . $company_profile);
                //     }
                // }

                //update bidang
                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_TYPE_LIST);
                foreach ($company_types as $type) {
                    $this->db->insert(App_Model::TBL_COMPANY_TYPE_LIST, [
                        'id_company' => $id_company,
                        'id_company_type' => $type
                    ]);
                }

                //update contact
                if($this->input->post("action_contact") == "edit") {
                    $params_contact = [
                        'id_company' => $id_company,
                        'id_country' => $this->input->post('id_country'),
                        'id_country_province' => $this->input->post('id_country_province'),
                        'id_city' => $this->input->post('id_city'),
                        'address' => $this->input->post('address'),
                        'pos_code' => $this->input->post('pos_code'),
                        'phone' => $this->input->post('phone'),
                        'email' => $this->input->post('email'),
                        'verification_status' => 'Pending Verification',
                        'tanggal_berlaku' => $this->input->post('tanggal_berlaku'),
                        'tanggal_berlaku_npwp' => $this->input->post('tanggal_berlaku_npwp'),
                        'tanggal_berlaku_siup' => $this->input->post('tanggal_berlaku_siup'),
                        'tanggal_berlaku_tdg' => $this->input->post('tanggal_berlaku_tdg'),
                        'tanggal_berlaku_tdp' => $this->input->post('tanggal_berlaku_tdp'),
                        'tanggal_berlaku_nib' => $this->input->post('tanggal_berlaku_nib')
                        //'verification_note' => null
                    ];
                } else {
                    $params_contact = [
                        'id_company' => $id_company,
                        'id_country' => $this->input->post('id_country'),
                        'id_country_province' => $this->input->post('id_country_province'),
                        'id_city' => $this->input->post('id_city'),
                        'address' => $this->input->post('address'),
                        'pos_code' => $this->input->post('pos_code'),
                        'phone' => $this->input->post('phone'),
                        'email' => $this->input->post('email'),
                        'verification_status' => 'Pending Verification',
                        'tanggal_berlaku' => $this->input->post('tanggal_berlaku'),
                        'tanggal_berlaku_npwp' => $this->input->post('tanggal_berlaku_npwp'),
                        'tanggal_berlaku_siup' => $this->input->post('tanggal_berlaku_siup'),
                        'tanggal_berlaku_tdg' => $this->input->post('tanggal_berlaku_tdg'),
                        'tanggal_berlaku_tdp' => $this->input->post('tanggal_berlaku_tdp'),
                        'tanggal_berlaku_nib' => $this->input->post('tanggal_berlaku_nib')
                        //'verification_note' => null
                    ];
                }
                if ($this->input->post("action_contact") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_contact", $params_contact);
                } else {
                    $this->db->insert("company_contact", $params_contact);
                }

                if ($this->input->post('id_usr_role') == App_Model::ROLE_VENDOR_PERSONAL) {
                    $email_fix = $this->db->where('id_user', $id_user)->get(App_Model::TBL_USER)->row()->email;
                    $this->db->where('id_company', $id_company)->update("company_contact", [
                        'email' => $email_fix
                    ]);
                }


                //update work area
                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_WORK_AREA);
                foreach ($this->input->post('work_area') as $wa) {
                    $this->db->insert(App_Model::TBL_COMPANY_WORK_AREA, [
                        'id_company' => $id_company,
                        'id_city' => $wa
                    ]);
                }

                if ($this->input->post('id_usr_role') != App_Model::ROLE_VENDOR_PERSONAL) {
                    //update PIC
                    $param_pic = [
                        'id_company' => $id_company,
                        'name' => $this->input->post('name_pic'),
                        'position' => $this->input->post('position'),
                        'position_type' => $this->input->post('position_type'),
                        'mobile_phone' => $this->input->post('mobile_phone'),
                        'email' => $this->input->post('email_pic'),
                        'verification_status' => 'Pending Verification',
                        //'verification_note' => null
                    ];
                    $do_upload_pic = $this->upload_company_attachment(true, "attachment_surat_kuasa");
                    if ($do_upload_pic['success']) {
                        $param_pic['attachment'] = $do_upload_pic['file_data']['file_name'];
                    }

                    if ($this->input->post("action_pic") == "edit") {
                        $this->db->where('id_company', $id_company)->update("company_pic", $param_pic);
                    } else {
                        $this->db->insert("company_pic", $param_pic);
                    }
                    if ($this->input->post('attachment_surat_kuasa_old') != null && isset($param_pic['attachment'])) {
                        if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_surat_kuasa_old'))) {
                            unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_surat_kuasa_old'));
                        }
                    }


                    //update Domisili
                    $param_npwp = [
                        'id_company' => $id_company,
                        'name' => 'LAMPIRAN SUKET DOMISILI'
                    ];
                    $do_upload_npwp = $this->upload_company_attachment(true, "attachment_domisili");
                    if ($do_upload_npwp['success']) {
                        $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                    }

                    if ($this->input->post("action_domisili") == "edit") {
                        $this->db->where('id_company', $id_company)
                            ->where('name', 'LAMPIRAN SUKET DOMISILI')
                            ->update("company_document", $param_npwp);
                    } else {
                        $this->db->insert("company_document", $param_npwp);
                    }
                    if ($this->input->post('attachment_domisili_old') != null && isset($param_npwp['attachment'])) {
                        if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_domisili_old'))) {
                            unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_domisili_old'));
                        }
                    }
                }

                //update KTP
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN KTP'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_ktp");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_ktp") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'LAMPIRAN KTP')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_ktp_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_ktp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_ktp_old'));
                    }
                }

                //update FINANSIAL REPORT
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'FINANSIAL_REPORT_1_THN'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_fin_report_1y");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_fin_report_1y") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'FINANSIAL_REPORT_1_THN')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_fin_report_1y_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_fin_report_1y_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_fin_report_1y_old'));
                    }
                }

                //update KEBIJAKAN/KOMITMEN
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'KEBIJAKAN/KOMITMEN'
                ];
                $do_upload_kebijakan_k3 = $this->upload_company_attachment(true, "attachment_kebijakan_k3");
                if ($do_upload_kebijakan_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_kebijakan_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_kebijakan_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'KEBIJAKAN/KOMITMEN')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_kebijakan_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_kebijakan_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_kebijakan_k3_old'));
                    }
                }

                // update SISTEM TANGGAP DARURAT
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'SISTEM TANGGAP DARURAT'
                ];
                $do_upload_sistem_tanggap_darurat = $this->upload_company_attachment(true, "attachment_tanggap_darurat");
                if ($do_upload_sistem_tanggap_darurat['success']) {
                    $param_npwp['attachment'] = $do_upload_sistem_tanggap_darurat['file_data']['file_name'];
                }

                if ($this->input->post("attachment_tanggap_darurat") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'SISTEM TANGGAP DARURAT')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_tanggap_darurat_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tanggap_darurat_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tanggap_darurat_old'));
                    }
                }

                // update SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA'
                ];
                $do_upload_iso_450001 = $this->upload_company_attachment(true, "attachment_iso_450001");
                if ($do_upload_iso_450001['success']) {
                    $param_npwp['attachment'] = $do_upload_iso_450001['file_data']['file_name'];
                }

                if ($this->input->post("attachment_iso_450001") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_iso_450001_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_iso_450001_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_iso_450001_old'));
                    }
                }

                //update STUKTUR ORGANISASI K3
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'STUKTUR ORGANISASI K3'
                ];
                $do_upload_stuktur_organisasi_k3 = $this->upload_company_attachment(true, "attachment_stuktur_organisasi_k3");
                if ($do_upload_stuktur_organisasi_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_stuktur_organisasi_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_stuktur_organisasi_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'STUKTUR ORGANISASI K3')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_stuktur_organisasi_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_stuktur_organisasi_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_stuktur_organisasi_k3_old'));
                    }
                }

                //update PERALATAN K3
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'PERALATAN K3'
                ];
                $do_upload_peralatan_k3 = $this->upload_company_attachment(true, "attachment_peralatan_k3");
                if ($do_upload_peralatan_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_peralatan_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_peralatan_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'PERALATAN K3')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_peralatan_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_peralatan_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_peralatan_k3_old'));
                    }
                }


                //update IMB
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN LEGAL IMB'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_imb");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_imb") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'LAMPIRAN LEGAL IMB')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_imb_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_imb_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_imb_old'));
                    }
                }

                //update TDG
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN LEGAL TDG'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_tdg");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_tdg") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name','LAMPIRAN LEGAL TDG')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_tdg_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdg_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdg_old'));
                    }
                }




                //update NPWP
                $param_npwp = [
                    'id_company' => $id_company,
                    'no' => $this->input->post('no_npwp'),
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_npwp");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_npwp") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_npwp", $param_npwp);
                } else {
                    $this->db->insert("company_legal_npwp", $param_npwp);
                }
                if ($this->input->post('attachment_npwp_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_npwp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_npwp_old'));
                    }
                }

                //update bank
                $param_bank = [
                    'id_company' => $id_company,
                    'no' => $this->input->post('no_rekening'),
                    'owner' => $this->input->post('owner'),
                    'bank_name' => $this->input->post('bank_name'),
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_bank = $this->upload_company_attachment(true, "attachment_bank");
                if ($do_upload_bank['success']) {
                    $param_bank['attachment'] = $do_upload_bank['file_data']['file_name'];
                }

                if ($this->input->post("action_bank") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_finance_bank", $param_bank);
                } else {
                    $this->db->insert("company_finance_bank", $param_bank);
                }
                if ($this->input->post('attachment_bank_old') != null && isset($param_bank['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_bank_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_bank_old'));
                    }
                }




                //update siup
                $param_siup = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_siup = $this->upload_company_attachment(true, "attachment_siup");
                if ($do_upload_siup['success']) {
                    $param_siup['attachment'] = $do_upload_siup['file_data']['file_name'];
                }

                if ($this->input->post("action_siup") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_siup", $param_siup);
                } else {
                    $this->db->insert("company_legal_siup", $param_siup);
                }
                if ($this->input->post('attachment_siup_old') != null && isset($param_siup['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_siup_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_siup_old'));
                    }
                }

                //update tdp
                $param_tdp = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_tdp = $this->upload_company_attachment(true, "attachment_tdp");
                if ($do_upload_tdp['success']) {
                    $param_tdp['attachment'] = $do_upload_tdp['file_data']['file_name'];
                }
                if ($this->input->post("action_tdp") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_tdp", $param_tdp);
                } else {
                    $this->db->insert("company_legal_tdp", $param_tdp);
                }
                if ($this->input->post('attachment_tdp_old') != null && isset($param_tdp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdp_old'));
                    }
                }

                //update nib
                $param_nib = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_nib = $this->upload_company_attachment(true, "attachment_nib");
                if ($do_upload_nib['success']) {
                    $param_nib['attachment'] = $do_upload_nib['file_data']['file_name'];
                }
                if ($this->input->post("action_nib") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_nib", $param_nib);
                } else {
                    $this->db->insert("company_legal_nib", $param_nib);
                }
                if ($this->input->post('attachment_nib_old') != null && isset($param_nib['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_nib_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_nib_old'));
                    }
                }


                //update akta
                $param_akta = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_akta = $this->upload_company_attachment(true, "attachment_akta");
                if ($do_upload_akta['success']) {
                    $param_akta['attachment'] = $do_upload_akta['file_data']['file_name'];
                }
                if ($this->input->post("action_akta") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_born_license", $param_akta);
                } else {
                    $this->db->insert("company_legal_born_license", $param_akta);
                }
                if ($this->input->post('attachment_akta_old') != null && isset($param_akta['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_akta_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_akta_old'));
                    }
                }



                //Pending verification if update
                $table_name = "company_profile";
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
                        // $this->db->insert('verification_history', [
                        //     'verificator' => $this->session->userdata('user')['id_user'],
                        //     'data_main_table' => $table_name,
                        //     'data_id' => $table_id[$table_name_id],
                        //     'verification_status' => 'Pending Verification',
                        //     //'verification_note' => null
                        // ]);
                        //Add notification
                        add_notification($id_user, null, 5, 'Verifikasi Data', 'Verifikasi Data Profil', 'Internal', '#verification/profile_basic');
                    }
                }

                $user_data = [];
                $exception_user_data = ['password'];
                $user = $this->db
                    ->where('id_user', $this->session->userdata('user')['id_user'])
                    ->where(App_Model::FIELD_DELETED_AT . App_Model::SQL_IS_NULL)
                    ->get(App_Model::TBL_USER)
                    ->row();
                foreach ($user as $key => $val) {
                    $is_not_exception = true;
                    for ($i = 0; $i < count($exception_user_data); $i++) {
                        if ($key == $exception_user_data[$i]) {
                            $is_not_exception = false;
                            break;
                        }
                    }
                    if ($is_not_exception) {
                        $user_data[$key] = $val;
                    }
                }
                $user_role = $this->db->where('id_usr_role', $user->id_usr_role)
                    ->get(App_Model::TBL_USR_ROLE)->row();
                $user_data['role_name'] = $user_role->role_name;

                $session_data = [
                    App_Model::SESS_KEY_LOGIN => true,
                    App_Model::SESS_KEY_USER_DATA => $user_data
                ];
                $this->session->set_userdata($session_data);

                $email_target = $this->session->userdata('user')['email'];
                $email_subject = 'Registrasi Vendor E-Procurement PT. BGR LOGISTIK INDONESIA';
                $user_role = $this->session->userdata('user')['role_name'];
                // <img src="https://bgrlogistik.id/bgr/img/bgr_logo.png" width="200px">
                $this->email_helper->send_email(
                    $this->config->item('app_info')['identity']['name'],
                    $email_target,
                    $email_subject,
                    '<center>
                <img src="https://eproc.bgrlogistik.id/assets/BGRLI.png" width="200px">
                <br>' . $this->config->item('app_info')['identity']['name'] . '</center>
                <hr>
                <p> Terima kasih telah mendaftar dan melengkapi Informasi Wajib sebagai ' . $user_role . ' pada E-Procurement ' . $this->config->item('app_info')['identity']['author_name']
                        . '. Mohon untuk menunggu verifikasi data Anda, informasi hasil verifikasi akan dikirimkan melalui Email.
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


                add_notification($id_user, null, App_Model::ROLE_VERIFICATOR, "Verifikasi Data Vendor", "Mohon Verifikasi Data Vendor "
                    . $this->input->post('name') . ', ' . $this->input->post('prefix_name'), "Internal", "#verification/data_perusahaan");

                $this->db->insert('verification_history', [
                    'verificator' => $id_user,
                    'data_id' => $id_company,
                    'data_main_table' => '-',
                    'verification_status' => 'Pending Verification',
                    'verification_note' => 'Submit Data',
                ]);

                echo json_encode([
                    'success' => true,
                    'result' => 'Success adding data!'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'result' => 'Failed adding data 1!'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Failed adding data 2!'
            ]);
        }
    }

    public function edit_data_profile_dashboard()
    {
        $id_user = $this->input->post('id_user');
        $company_types = $this->input->post('type');

        $field_params_cp['updated_at'] = date('Y-m-d H:i:s');
        $do_upload = $this->upload_company_attachment(true, "company_profile");
        if ($do_upload['success']) {
            $field_params_cp['company_profile'] = $do_upload['file_data']['file_name'];
        }

        $id_company = null;
        if ($this->input->post("action_profile") == "edit") {
            $id_company = $this->db
                ->where('id_user', $id_user)
                ->get(App_Model::TBL_COMPANY_PROFILE)
                ->row();
            $save_compro = edit_data(
                'company_profile',
                $field_params_cp,
                [
                    'id_user' => $id_user
                ]
            );
        } else {
            $save_compro = add_data(
                'company_profile',
                $field_params_cp,
                [
                    'id_user' => $id_user
                ]
            );
            $id_company = $this->db
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_PROFILE)
                ->row();
        }



        if ($save_compro['success']) {
            if ($id_company != null) {
                $logo = $id_company->logo;
                $company_profile = $id_company->company_profile;
                $id_company = $id_company->id;

                // if ($company_profile != null && isset($field_params_cp['company_profile'])) {
                //     if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $company_profile)) {
                //         unlink(self::PATH_UPLOAD_COMPANY_FILE . $company_profile);
                //     }
                // }

                //update bidang
                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_TYPE_LIST);
                foreach ($company_types as $type) {
                    $this->db->insert(App_Model::TBL_COMPANY_TYPE_LIST, [
                        'id_company' => $id_company,
                        'id_company_type' => $type
                    ]);
                }

                //update contact
                $params_contact = [
                    'id_company' => $id_company,
                    'id_country' => $this->input->post('id_country'),
                    'id_country_province' => $this->input->post('id_country_province'),
                    'id_city' => $this->input->post('id_city'),
                    'address' => $this->input->post('address'),
                    'pos_code' => $this->input->post('pos_code'),
                    'phone' => $this->input->post('phone'),
                    'email' => $this->input->post('email'),
                    'verification_status' => 'Pending Verification',
                    //'verification_note' => null
                ];
                if ($this->input->post("action_contact") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_contact", $params_contact);
                } else {
                    $this->db->insert("company_contact", $params_contact);
                }


                //update work area
                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_WORK_AREA);
                foreach ($this->input->post('work_area') as $wa) {
                    $this->db->insert(App_Model::TBL_COMPANY_WORK_AREA, [
                        'id_company' => $id_company,
                        'id_city' => $wa
                    ]);
                }

                $this->db->where('id_company', $id_company)->delete(App_Model::TBL_COMPANY_CABANG_AREA);
                foreach ($this->input->post('cabang_area') as $cabang) {
                    $this->db->insert(App_Model::TBL_COMPANY_CABANG_AREA, [
                        'id_company' => $id_company,
                        'id_cabang' => $cabang
                    ]);
                }

                $id_usr_role = ($this->input->post('id_usr_role') != null) ? $this->input->post('id_usr_role') : $this->session->userdata('user')['id_usr_role'];

                if ($id_usr_role != App_Model::ROLE_VENDOR_PERSONAL) {
                    //update PIC
                    $param_pic = [
                        'id_company' => $id_company,
                        'name' => $this->input->post('name_pic'),
                        'position' => $this->input->post('position'),
                        'position_type' => $this->input->post('position_type'),
                        'mobile_phone' => $this->input->post('mobile_phone'),
                        'email' => $this->input->post('email_pic'),
                        'verification_status' => 'Pending Verification',
                        //'verification_note' => null
                    ];
                    $do_upload_pic = $this->upload_company_attachment(true, "attachment_surat_kuasa");
                    if ($do_upload_pic['success']) {
                        $param_pic['attachment'] = $do_upload_pic['file_data']['file_name'];
                    }

                    if ($this->input->post("action_pic") == "edit") {
                        $this->db->where('id_company', $id_company)->update("company_pic", $param_pic);
                    } else {
                        $this->db->insert("company_pic", $param_pic);
                    }
                    if ($this->input->post('attachment_surat_kuasa_old') != null && isset($param_pic['attachment'])) {
                        if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_surat_kuasa_old'))) {
                            unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_surat_kuasa_old'));
                        }
                    }

                    //update Domisili
                    $param_npwp = [
                        'id_company' => $id_company,
                        'name' => 'LAMPIRAN SUKET DOMISILI'
                    ];
                    $do_upload_npwp = $this->upload_company_attachment(true, "attachment_domisili");
                    if ($do_upload_npwp['success']) {
                        $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                    }

                    $param_pakta = [
                        'company_id' => $id_company
                    ];
                    $do_upload_pakta = $this->upload_company_attachment(true, "attachment_pakta");
                    if ($do_upload_pakta['success']) {
                        $param_npwp['nm_file'] = $do_upload_pakta['file_data']['file_name'];
                    }

                    $getDataPakta = $this->getDataPakta($id_company);

                    if(count($getDataPakta) > 0) {
                        $this->db->where('company_id', $id_company);
                        $this->db->delete('company_pakta');

                        $this->db->insert('company_pakta', $param_pakta);
                    } else {
                        $this->db->insert('company_pakta', $param_pakta);
                    }

                    // if ($this->input->post("action_domisili") == "edit") {
                    //     $this->db
                    //         ->where('id_company', $id_company)
                    //         ->where('name', 'LAMPIRAN SUKET DOMISILI')
                    //         ->update("company_document", $param_npwp);
                    // } else {
                    //     $this->db->insert("company_document", $param_npwp);
                    // }


                    if ($this->input->post("action_domisili") == "edit") {
                        $this->db
                            ->where('id_company', $id_company)
                            ->where('name', 'LAMPIRAN SUKET DOMISILI')
                            ->update("company_document", $param_npwp);
                    } else {
                        $this->db->insert("company_document", $param_npwp);
                    }
                    if ($this->input->post('attachment_domisili_old') != null && isset($param_npwp['attachment'])) {
                        if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_domisili_old'))) {
                            unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_domisili_old'));
                        }
                    }
                }

                //update KTP
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN KTP'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_ktp");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_ktp") == "edit") {
                    $this->db
                        ->where('id_company', $id_company)
                        ->where('name', 'LAMPIRAN KTP')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_ktp_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_ktp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_ktp_old'));
                    }
                }


                //update FINANSIAL REPORT
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'FINANSIAL_REPORT_1_THN'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_fin_report_1y");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_fin_report_1y") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'FINANSIAL_REPORT_1_THN')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_fin_report_1y_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_fin_report_1y_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_fin_report_1y_old'));
                    }
                }

                //update KEBIJAKAN/KOMITMEN
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'KEBIJAKAN/KOMITMEN'
                ];
                $do_upload_kebijakan_k3 = $this->upload_company_attachment(true, "attachment_kebijakan_k3");
                if ($do_upload_kebijakan_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_kebijakan_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_kebijakan_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'KEBIJAKAN/KOMITMEN')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_kebijakan_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_kebijakan_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_kebijakan_k3_old'));
                    }
                }

                // update SISTEM TANGGAP DARURAT
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'SISTEM TANGGAP DARURAT'
                ];
                $do_upload_sistem_tanggap_darurat = $this->upload_company_attachment(true, "attachment_tanggap_darurat");
                if ($do_upload_sistem_tanggap_darurat['success']) {
                    $param_npwp['attachment'] = $do_upload_sistem_tanggap_darurat['file_data']['file_name'];
                }

                if ($this->input->post("attachment_tanggap_darurat") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'SISTEM TANGGAP DARURAT')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_tanggap_darurat_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tanggap_darurat_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tanggap_darurat_old'));
                    }
                }

                // update SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA'
                ];
                $do_upload_iso_450001 = $this->upload_company_attachment(true, "attachment_iso_450001");
                if ($do_upload_iso_450001['success']) {
                    $param_npwp['attachment'] = $do_upload_iso_450001['file_data']['file_name'];
                }

                if ($this->input->post("attachment_iso_450001") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'SERTIFIKASI ISO 45001/9001 ATAU DOKUMEN YANG RELEVAN LAINNYA')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_iso_450001_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_iso_450001_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_iso_450001_old'));
                    }
                }

                //update STUKTUR ORGANISASI K3
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'STUKTUR ORGANISASI K3'
                ];
                $do_upload_stuktur_organisasi_k3 = $this->upload_company_attachment(true, "attachment_stuktur_organisasi_k3");
                if ($do_upload_stuktur_organisasi_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_stuktur_organisasi_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_stuktur_organisasi_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'STUKTUR ORGANISASI K3')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_stuktur_organisasi_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_stuktur_organisasi_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_stuktur_organisasi_k3_old'));
                    }
                }

                //update PERALATAN K3
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'PERALATAN K3'
                ];
                $do_upload_peralatan_k3 = $this->upload_company_attachment(true, "attachment_peralatan_k3");
                if ($do_upload_peralatan_k3['success']) {
                    $param_npwp['attachment'] = $do_upload_peralatan_k3['file_data']['file_name'];
                }

                if ($this->input->post("action_peralatan_k3") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'PERALATAN K3')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_peralatan_k3_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_peralatan_k3_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_peralatan_k3_old'));
                    }
                }


                //update IMB
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN LEGAL IMB'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_imb");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }
                if ($this->input->post("action_imb") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'LAMPIRAN LEGAL IMB')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_imb_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_imb_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_imb_old'));
                    }
                }

                //update TDG
                $param_npwp = [
                    'id_company' => $id_company,
                    'name' => 'LAMPIRAN LEGAL TDG'
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_tdg");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_tdg") == "edit") {
                    $this->db->where('id_company', $id_company)
                        ->where('name', 'LAMPIRAN LEGAL TDG')
                        ->update("company_document", $param_npwp);
                } else {
                    $this->db->insert("company_document", $param_npwp);
                }
                if ($this->input->post('attachment_tdg_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdg_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdg_old'));
                    }
                }

                //update NPWP
                $param_npwp = [
                    'id_company' => $id_company,
                    'no' => $this->input->post('no_npwp'),
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_npwp = $this->upload_company_attachment(true, "attachment_npwp");
                if ($do_upload_npwp['success']) {
                    $param_npwp['attachment'] = $do_upload_npwp['file_data']['file_name'];
                }

                if ($this->input->post("action_npwp") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_npwp", $param_npwp);
                } else {
                    $this->db->insert("company_legal_npwp", $param_npwp);
                }
                if ($this->input->post('attachment_npwp_old') != null && isset($param_npwp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_npwp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_npwp_old'));
                    }
                }

                //update bank
                $param_bank = [
                    'id_company' => $id_company,
                    'no' => $this->input->post('no_rekening'),
                    'owner' => $this->input->post('owner'),
                    'bank_name' => $this->input->post('bank_name'),
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_bank = $this->upload_company_attachment(true, "attachment_bank");
                if ($do_upload_bank['success']) {
                    $param_bank['attachment'] = $do_upload_bank['file_data']['file_name'];
                }

                if ($this->input->post("action_bank") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_finance_bank", $param_bank);
                } else {
                    $this->db->insert("company_finance_bank", $param_bank);
                }
                if ($this->input->post('attachment_bank_old') != null && isset($param_bank['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_bank_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_bank_old'));
                    }
                }




                //update siup
                $param_siup = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_siup = $this->upload_company_attachment(true, "attachment_siup");
                if ($do_upload_siup['success']) {
                    $param_siup['attachment'] = $do_upload_siup['file_data']['file_name'];
                }

                if ($this->input->post("action_siup") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_siup", $param_siup);
                } else {
                    $this->db->insert("company_legal_siup", $param_siup);
                }
                if ($this->input->post('attachment_siup_old') != null && isset($param_siup['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_siup_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_siup_old'));
                    }
                }

                //update tdp
                $param_tdp = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_tdp = $this->upload_company_attachment(true, "attachment_tdp");
                if ($do_upload_tdp['success']) {
                    $param_tdp['attachment'] = $do_upload_tdp['file_data']['file_name'];
                }
                if ($this->input->post("action_tdp") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_tdp", $param_tdp);
                } else {
                    $this->db->insert("company_legal_tdp", $param_tdp);
                }
                if ($this->input->post('attachment_tdp_old') != null && isset($param_tdp['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdp_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_tdp_old'));
                    }
                }

                //update nib
                $param_nib = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_nib = $this->upload_company_attachment(true, "attachment_nib");
                if ($do_upload_nib['success']) {
                    $param_nib['attachment'] = $do_upload_nib['file_data']['file_name'];
                }
                if ($this->input->post("action_nib") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_nib", $param_nib);
                } else {
                    $this->db->insert("company_legal_nib", $param_nib);
                }
                if ($this->input->post('attachment_nib_old') != null && isset($param_nib['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_nib_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_nib_old'));
                    }
                }


                //update akta
                $param_akta = [
                    'id_company' => $id_company,
                    'verification_status' => 'Pending Verification'
                    //'verification_note' => null
                ];
                $do_upload_akta = $this->upload_company_attachment(true, "attachment_akta");
                if ($do_upload_akta['success']) {
                    $param_akta['attachment'] = $do_upload_akta['file_data']['file_name'];
                }
                if ($this->input->post("action_akta") == "edit") {
                    $this->db->where('id_company', $id_company)->update("company_legal_born_license", $param_akta);
                } else {
                    $this->db->insert("company_legal_born_license", $param_akta);
                }
                if ($this->input->post('attachment_akta_old') != null && isset($param_akta['attachment'])) {
                    if (is_file(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_akta_old'))) {
                        unlink(self::PATH_UPLOAD_COMPANY_FILE . $this->input->post('attachment_akta_old'));
                    }
                }



                //Pending verification if update
                $table_name = "company_profile";
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
                        // $this->db->insert('verification_history', [
                        //     'verificator' => $this->session->userdata('user')['id_user'],
                        //     'data_main_table' => $table_name,
                        //     'data_id' => $table_id[$table_name_id],
                        //     'verification_status' => 'Pending Verification',
                        //     //'verification_note' => null
                        // ]);
                        //Add notification
                        add_notification($id_user, null, 5, 'Verifikasi Data', 'Verifikasi Data Profil', 'Internal', '#verification/profile_basic');
                    }
                }

                add_notification($id_user, null, 3, "Verifikasi Data Vendor", "Mohon Verifikasi Perubahan Data Vendor "
                    . $this->input->post('name') . ', ' . $this->input->post('prefix_name'), "Internal", "#verification/data_perusahaan");

                $this->db->insert('verification_history', [
                    'verificator' => $id_user,
                    'data_id' => $id_company,
                    'data_main_table' => '-',
                    'verification_status' => 'Pending Verification',
                    'verification_note' => 'Submit Data',
                ]);

                echo json_encode([
                    'success' => true,
                    'result' => 'Success edit data!',
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'result' => 'Failed adding data 1!'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'result' => 'Failed adding data 2!'
            ]);
        }
    }
}
