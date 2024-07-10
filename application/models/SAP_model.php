<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SAP_model extends App_Model
{
    public function get_data($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name, c.name as group_name, a.*")
            ->where('a.deleted_at is null')
            ->from(App_Model::TBL_SAP_SYNC . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_GROUP_VENDOR . ' c', 'a.id_group=c.id');
    }

    public function get_real_budget_sap($year, $no_fund_center, $coa = null)
    {
        if ($year != null && $no_fund_center != null) {

            $data["data"][] = array(
                "year"           => $year,
                "fundscenter"    => $no_fund_center, // fundcenter
                "coa"            => $coa
            );
            $data_post["data"] = $data; // parameter yang dikirim
            $data_post["url"] = "EKATALOG/Budget"; //url lanjutan
            $data_post["username"] = "it_dev"; //tidak berubah
            $data_post["password"] = "BGR2020oke"; //tidak berubah
            $data_post["action_to"] = ENVIRONMENT==='production'?'prod':'dev'; // dev: development, prod: production

            $url = 'http://10.66.0.47/SAP/integrasi.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_post));

            $result = curl_exec($ch);
            curl_close($ch);

            return [
                'success' => true,
                'data' =>  json_decode($result)
            ];
        } else {
            return [
                'success' => false,
                'data' => null
            ];
        }
    }

    public function input_sap($id_company, $id_role)
    {
        $result_final = [
            'success' => false,
            'result' => 'Cant sync with SAP!',
            'res_sap' => null
        ];


        $profile = $this->db->where('id', $id_company)->get(App_Model::TBL_COMPANY_PROFILE)->row();
        $check_form_valid = check_required_form_validation($profile->id_user);

        if($profile!=null && $profile->id_company_owner!=1){
            $result_final = [
                'success' => true,
                'result' => 'Data updated!',
                'res_sap' => null
            ];
            return $result_final;
        }

        if ((int) $check_form_valid->percentage >= 100) {
            $gl_number = '2011100003';
            $this->db->where('id_company', $id_company)->delete(App_Model::TBL_SAP_SYNC);
            // $this->db->where('id_company', $id_company)->update(App_Model::TBL_SAP_SYNC, [
            //     'deleted_at' => date('Y-m-d H:i:s')
            // ]);
            $this->db->insert(App_Model::TBL_SAP_SYNC, [
                'id_group' => $profile->id_group,
                'id_company' => $id_company,
                'id_sap' => null,
                'vendor_gl_number' => $gl_number
            ]);
            // $this->load->model('Company_model', 'company');

            $contact = $this->db->where('id_company', $id_company)->get(App_Model::TBL_COMPANY_CONTACT)->row();
            $npwp = $this->db->where('id_company', $id_company)->order_by('created_at', 'desc')->limit(1)->get(App_Model::TBL_COMPANY_LEGAL_NPWP)->row();
            $bank = $this->db->where('id_company', $id_company)->order_by('created_at', 'desc')->limit(1)->get(App_Model::TBL_COMPANY_FINANCE_BANK)->row();
            $sap_sync = $this->db->where('id_company', $id_company)->order_by('created_at', 'desc')->limit(1)->get(App_Model::TBL_SAP_SYNC)->row();
            $city = $this->db->where('id', $contact->id_city)->get('m_city')->row();
            $city = ($city != null) ? $city->name : "";



            // add_data();
            if ($id_role == App_Model::ROLE_VENDOR) {

                $data["data"][] = array(
                    "category"        => '2', // kategori (1 Personal, 2 Company)
                    "flag"           => 'I', // Operation insert
                    "group"          => $this->db->where('id', $sap_sync->id_group)->get(App_Model::TBL_GROUP_VENDOR)->row()->name, // grup 
                    "search1"          => '.', // titik
                    "search2"          => '.', // sama dengan kota
                    "title"    => '0003', //title (0003 Company, 0001 Ms, 0002 Mr)
                    "name"    => str_replace(' ', '', ($profile->prefix_name . ' ' . $profile->name . ' ' . $profile->postfix_name)), //Nama perusahaan (hanya diisi jika category = 2)
                    "firstname" => '', // nama awal vendor personal (hanya diisi jika category = 1)
                    "lastname"  => '', // nama akhir vendor personal (hanya diisi jika category = 1)
                    "sex" => '', // Diisi jika category = 1 ( 1 Female, 2 Male)
                    "legalform"    => '01', // (diisi default 01 jika category = 2)
                    "nat_person" => '', // (diisi default X jika category = 1)
                    "street"    => $contact->address, //nama jalan
                    "house_no"    => $contact->building_no, // no bangunan
                    "city"     => $city, // Kota
                    "postal_code"     => $contact->pos_code, // Kode pos
                    "region"      =>  $this->db->where('id', $contact->id_country_province)->get(App_Model::TBL_COUNTRY_PROVINCE)->row()->official_id, // Kode provinsi
                    "nation"      => 'ID', // Kode Negara
                    "telp"        => $contact->phone, // telp
                    "email"       => $contact->email, // email
                    "f_tax"      =>  'X', // Flag tax (isikan jika nomor npwp diisi)
                    "tax_number"      =>  str_replace(' ', '', str_replace('-', '', str_replace('.', '', $npwp->no))), // NPWP (No NPWP tidak boleh sm)
                    "f_bank"      =>  'X', // Flag bank (isian jika bank ada)
                    "data_key"      =>  '0001', // Data negara bank
                    "bank_ctry"      =>  'ID', // Data Negara bank
                    "bank_key"      =>  $bank->bank_name, // Key Bank (nama bank)
                    "bank_acct"      =>  str_replace(' ', '', str_replace('-', '', str_replace('.', '', $bank->no))), // No rekening
                    "bank_acctname"      =>  $bank->owner, // Nama Pemilik rekening
                    "f_vendor"      =>  'X', // Flag vendor (centang bila yang diinput adalah vendor)
                    "vend_akont"      =>  $sap_sync->vendor_gl_number, // Nomor GL Pembebanan Vendor
                    "f_cust"      =>  '', // Flag Customer (Centang bila yang diinput adalah customer)
                    "cust_akont"      =>  '' // Nomor GL Pembebanan customer
                );
            } else if ($id_role == App_Model::ROLE_VENDOR_PERSONAL) {
                $data["data"][] = array(
                    "category"        => '1', // kategori (1 Personal, 2 Company)
                    "flag"           => 'I', // Operation insert
                    "group"          => $this->db->where('id', $sap_sync->id_group)->get(App_Model::TBL_GROUP_VENDOR)->row()->name, // grup 
                    "search1"          => '.', // samakan dengan last name
                    "search2"          => '.', // kosongkan
                    "title"    => '0002', //title (0003 Company, 0001 Ms, 0002 Mr)
                    "name"    =>  ' ', //Nama perusahaan (hanya diisi jika category = 2)
                    "firstname" => $profile->prefix_name, // nama awal vendor personal (hanya diisi jika category = 1)
                    "lastname"  => $profile->name, // nama akhir vendor personal (hanya diisi jika category = 1)
                    "sex" => '2', // Diisi jika category = 1 ( 1 Female, 2 Male)
                    "legalform"    => '', // (diisi default 01 jika category = 2)
                    "nat_person" => 'X', // (diisi default X jika category = 1)
                    "street"    => $contact->address, //nama jalan
                    "house_no"    => $contact->building_no, // no bangunan
                    "city"     => $city, // Kota
                    "postal_code"     => $contact->pos_code, // Kode pos
                    "region"      =>  $this->db->where('id', $contact->id_country_province)->get(App_Model::TBL_COUNTRY_PROVINCE)->row()->official_id, // Kode provinsi
                    "nation"      => 'ID', // Kode Negara
                    "telp"        => $contact->phone, // telp
                    "email"       => $contact->email, // email
                    "f_tax"      =>  'X', // Flag tax (isikan jika nomor npwp diisi)
                    "tax_number"      =>  str_replace(' ', '', str_replace('-', '', str_replace('.', '', $npwp->no))), // NPWP (No NPWP tidak boleh sm)
                    "f_bank"      =>  'X', // Flag bank (isian jika bank ada)
                    "data_key"      =>  '0001', // Data negara bank
                    "bank_ctry"      =>  'ID', // Data Negara bank
                    "bank_key"      =>  $bank->bank_name, // Key Bank (nama bank)
                    "bank_acct"      =>  str_replace(' ', '', str_replace('-', '', str_replace('.', '', $bank->no))), // No rekening
                    "bank_acctname"      =>  $bank->owner, // Nama Pemilik rekening
                    "f_vendor"      =>  'X', // Flag vendor (centang bila yang diinput adalah vendor)
                    "vend_akont"      =>  $sap_sync->vendor_gl_number, // Nomor GL Pembebanan Vendor
                    "f_cust"      =>  '', // Flag Customer (Centang bila yang diinput adalah customer)
                    "cust_akont"      =>  '' // Nomor GL Pembebanan customer
                );
            }

            // $login = 'PO_BGR';
            // $password = 'initial01';
            // $url = 'http://10.66.64.33:53500/RESTAdapter/EPROC/BP';
            // $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            // curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

            // $res_sap = curl_exec($ch);
            // curl_close($ch);

            $data_post["data"] = $data; // parameter yang dikirim
            $data_post["url"] = "EPROC/BP"; //url lanjutan
            $data_post["username"] = "it_dev"; //tidak berubah
            $data_post["password"] = "BGR2020oke"; //tidak berubah
            $data_post["action_to"] = ENVIRONMENT==='production'?'prod':'dev'; // dev: development, prod: production

            $url = 'http://10.66.0.47/SAP/integrasi.php';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_post));

            $res_sap = curl_exec($ch);
            $res_sap_raw = $res_sap;
            curl_close($ch);

            $res_sap_json = json_decode($res_sap);
            if (!empty($res_sap_json)) {
                $res_sap = $res_sap_json;
            } else {
                $res_sap = strip_tags($res_sap_json);
            }

            if ($res_sap_json != null) {
                if ($res_sap_json->Data->Response->Type == 'E') {
                    $result_final = [
                        'success' => false,
                        'result' => 'Cant sync with SAP, ' . $res_sap_json->Data->Response->Message,
                        'res_sap' => $res_sap,
                        'data' => $data,
                    ];
                } else if ($res_sap_json->Data->Response->Type == 'D') {
                    $result_final = [
                        'success' => false,
                        'result' => 'Cant sync with SAP, ' . $res_sap_json->Data->Response->Message,
                        'res_sap' => $res_sap,
                        'data' => $data,
                    ];
                } else {
                    $result_final = [
                        'success' => true,
                        'result' => 'Successful sync with SAP, ' . $res_sap_json->Data->Response->Message,
                        'res_sap' => $res_sap,
                        'data' => $data,
                    ];
                    $this->db->where('id_company', $sap_sync->id_company)
                        ->update(App_Model::TBL_SAP_SYNC, [
                            'id_sap' => $res_sap_json->Data->Partner
                        ]);
                }
            } else {
                $result_final = [
                    'success' => false,
                    'result' => 'Cant sync with SAP!, SAP Error Occured: ' . strip_tags($res_sap_raw),
                    'res_sap' => $res_sap,
                    'data' => $data,
                ];
            }
        } else {
            $result_final = [
                'success' => false,
                'result' => 'Cant sync with SAP, check your required form!',
                'data' => $check_form_valid
            ];
        }

        if ($result_final['success'] == false) {
            $this->db->where('id_company', $id_company)->delete(App_Model::TBL_SAP_SYNC);
        }

        return $result_final;
    }

    public function update_sap($id_company, $id_role)
    {
        $result_final = [
            'success' => false,
            'result' => "Can't update to SAP!"
        ];

        $company = $this->db->where('id', $id_company)->get(App_Model::TBL_COMPANY_PROFILE)->row();

        if($company!=null && $company->id_company_owner!=1){
            $result_final = [
                'success' => true,
                'result' => 'Data updated!',
                'res_sap' => null
            ];
            return $result_final;
        }


        if ($id_company != null) {
            $user = $this->db->where('id_user', $company->id_user)->get(App_Model::TBL_USER)->row();
            $sap = $this->db->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_SAP_SYNC)
                ->row();
            $contact = $this->db->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_CONTACT)
                ->row();
            $pic = $this->db->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_PIC)
                ->row();
            $npwp = $this->db->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_LEGAL_NPWP)
                ->row();
            $finance_bank = $this->db->where('id_company', $id_company)
                ->order_by('created_at', 'desc')
                ->limit(1)
                ->get(App_Model::TBL_COMPANY_FINANCE_BANK)
                ->row();
            $city = $this->db->where('id', $contact->id_city)->get('m_city')->row();
            $city = ($city != null) ? $city->name : "";
        }

        //Update BP
        $data["data"][] = array(
            "no_bp"        => $sap->id_sap, // Kode Vendor
            "flag"           => 'U', // Operation update
            "street"          => $contact->address, // grup 
            "house_no"          => $contact->building_no, // nomor gedung atau rumah
            "city"          => $city, // Kota
            "postal_code"          => $contact->pos_code, // kode pos
            "region"          => $this->db->where('id', $contact->id_country_province)->get(App_Model::TBL_COUNTRY_PROVINCE)->row()->official_id, // provinsi
            "nation"          => 'ID', // negara
            "telp"          => $contact->phone, // telpon
            "email"          => $contact->email // grup 
        );

        // $login = 'PO_BGR';
        // $password = 'initial01';
        // $url = 'http://10.66.64.33:53500/RESTAdapter/EPROC/BPAddress';
        // $ch = curl_init();
        // curl_setopt($ch, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        // $result = curl_exec($ch);
        // curl_close($ch);
        $data_post["data"] = $data; // parameter yang dikirim
        $data_post["url"] = "EPROC/BPAddress"; //url lanjutan
        $data_post["username"] = "it_dev"; //tidak berubah
        $data_post["password"] = "BGR2020oke"; //tidak berubah
        $data_post["action_to"] = ENVIRONMENT==='production'?'prod':'dev'; // dev: development, prod: production

        $url = 'http://10.66.0.47/SAP/integrasi.php';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_post));

        $result = curl_exec($ch);
        curl_close($ch);

        $res_sap_json = json_decode($result);
        if (!empty($res_sap_json)) {
            $res_sap = $res_sap_json;
        } else {
            $res_sap = strip_tags($res_sap_json);
        }

        if ($res_sap_json != null) {
            if ($res_sap_json->Data->Response->Type == 'E') {
                $result_final = [
                    'success' => false,
                    'result' => 'Cant sync with SAP, ' . $res_sap_json->Data->Response->Message,
                    'res_sap' => $res_sap,
                    'data' => $data,
                ];
            } else {

                //Update BANK
                $data_bank["data"][] = array(
                    "no_bp"        => $sap->id_sap, // Kode Vendor
                    "flag"           => 'U', // Operation update
                    "bank_id"          => '0001', // nomor id bank (incremement berdasarkan jumlah baris)
                    "bank_ctry"          => 'ID', // negara bank
                    "bank_key"          => $finance_bank->bank_name, // bank key
                    "bank_acct"          => $finance_bank->no, // no rek
                    "bankaccountname"    => $finance_bank->owner // nama pemilik rek
                );

                // $login = 'PO_BGR';
                // $password = 'initial01';
                // $url = 'http://10.66.64.33:53500/RESTAdapter/EPROC/BPBank';
                // $ch = curl_init();
                // curl_setopt($ch, CURLOPT_URL, $url);
                // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                // curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                // curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                // curl_setopt($ch, CURLOPT_POST, true);
                // curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_bank));

                // $result = curl_exec($ch);
                // curl_close($ch);

                $data_post["data"] = $data_bank; // parameter yang dikirim
                $data_post["url"] = "EPROC/BPBank"; //url lanjutan
                $data_post["username"] = "it_dev"; //tidak berubah
                $data_post["password"] = "BGR2020oke"; //tidak berubah
                $data_post["action_to"] = ENVIRONMENT==='production'?'prod':'dev'; // dev: development, prod: production

                $url = 'http://10.66.0.47/SAP/integrasi.php';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_post));

                $result = curl_exec($ch);
                curl_close($ch);

                $res_sap_json = json_decode($result);
                if (!empty($res_sap_json)) {
                    $res_sap = $res_sap_json;
                } else {
                    $res_sap = strip_tags($res_sap_json);
                }

                if ($res_sap_json != null) {
                    if ($res_sap_json->Data->Response->Type == 'E') {
                        $result_final = [
                            'success' => false,
                            'result' => 'Cant sync with SAP, ' . $res_sap_json->Data->Response->Message,
                            'res_sap' => $res_sap,
                            'data' => $data_bank,
                        ];
                    } else {
                        $result_final = [
                            'success' => true,
                            'result' => 'Successful sync with SAP, ' . $res_sap_json->Data->Response->Message,
                            'res_sap' => $res_sap,
                            'data' => $data_bank,
                        ];
                    }
                } else {
                    $result_final = [
                        'success' => false,
                        'result' => 'Cant sync with SAP!',
                        'res_sap' => $res_sap,
                        'data' => $data_bank,
                    ];
                }
            }
        } else {
            $result_final = [
                'success' => false,
                'result' => 'Cant sync with SAP!',
                'res_sap' => $res_sap,
                'data' => $data,
            ];
        }

        return $result_final;
    }
}
