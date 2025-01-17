<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company_model extends App_Model
{


    public function get($id = null, $id_company = null, $otherWheres = [], $rawWheres = [])
    {

        if ($id != null) {
            $this->db->where('id', $id);
        }
        if ($id_company != null) {
            $this->db->where('id_company', $id_company);
        }

        foreach ($otherWheres as $key => $val) {
            $this->db->where($key, $val);
        }

        foreach ($rawWheres as $w) {
            $this->db->where($w);
        }

        return $this->db
            ->select('a.*,c.role_name, b.email as user_email')
            ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->join(App_Model::TBL_USER . ' b', 'a.id_user=b.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' c', 'b.id_usr_role=c.id_usr_role')
            ->get()
            ->result();
    }

    public function get_profile($id_user = null, $id = null)
    {
        $data = $this->db->select('a.*,c.role_name, b.email as user_email')
            ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->join(App_Model::TBL_USER . ' b', 'a.id_user=b.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' c', 'b.id_usr_role=c.id_usr_role')
            ->where('a.deleted_at is null');
        if ($id != null) {
            $data->where('a.id', $id);
        }
        $data->where('a.id_user', $id_user);
        $data->order_by('created_at', 'desc')->limit(1);
        return $data;
    }

    public function get_type_list($id_company)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_TYPE_LIST)
            ->where('id_company', $id_company);
    }

    public function get_worka_area_list($id_company)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_WORK_AREA)
            ->where('id_company', $id_company);
    }

    public function get_cabang_area_list($id_company)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_CABANG_AREA)
            ->where('id_company', $id_company);
    }

    public function get_data_contact($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_CONTACT)
            ->where('id_company', $id_company);
    }

    public function get_data_npwp($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_LEGAL_NPWP)
            ->where('id_company', $id_company);
    }

    public function get_data_bank($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_FINANCE_BANK)
            ->where('id_company', $id_company);
    }

    public function get_data_pic($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_PIC)
            ->where('id_company', $id_company);
    }
    public function get_data_siup($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_LEGAL_SIUP)
            ->where('id_company', $id_company);
    }
    public function get_data_tdp($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_LEGAL_TDP)
            ->where('id_company', $id_company);
    }
    public function get_data_nib($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_LEGAL_NIB)
            ->where('id_company', $id_company);
    }
    public function get_data_born($id = null, $id_company = null)
    {
        return $this->db
            ->from(App_Model::TBL_COMPANY_BORN_LICENSE)
            ->where('id_company', $id_company);
    }


    public function get_contact($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(b.prefix_name,' ',b.name,' ',b.postfix_name) as company_name,c.name as work_area_name, d.name as country_name
            , e.name as province_name,a.*
            , concat(a.id,'," . App_Model::TBL_COMPANY_CONTACT . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_CONTACT . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_WORK_AREA . ' c', 'a.work_area=c.id')
            ->join(App_Model::TBL_COUNTRY . ' d', 'a.id_country=d.id')
            ->join(App_Model::TBL_COUNTRY_PROVINCE . ' e', 'a.id_country_province=e.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }



    public function get_pic($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,a.*
            , concat(a.id,'," . App_Model::TBL_COMPANY_PIC . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_PIC . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_legal_domicile($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,d.id as id_country
            ,d.name as country_name
            ,c.name as province_name
            ,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_LEGAL_DOMICILE . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_LEGAL_DOMICILE . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COUNTRY_PROVINCE . ' c', 'a.id_country_province=c.id')
            ->join(App_Model::TBL_COUNTRY . ' d', 'c.id_country=d.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_legal_npwp($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,d.id as id_country
            ,d.name as country_name
            ,c.name as province_name
            ,(case when a.pkp=1 then 'Ya' else 'Tidak' end )as pkp_name
            ,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_LEGAL_NPWP . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_LEGAL_NPWP . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COUNTRY_PROVINCE . ' c', 'a.id_country_province=c.id')
            ->join(App_Model::TBL_COUNTRY . ' d', 'c.id_country=d.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_legal_nib($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_LEGAL_NIB . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_LEGAL_NIB . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_documents($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,a.*")
            ->from(App_Model::TBL_COMPANY_DOCUMENT . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }
    public function get_legal_tdp($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_LEGAL_TDP . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_LEGAL_TDP . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }
    public function get_legal_siup($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as business_type_name
            ,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_LEGAL_SIUP . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_LEGAL_SIUP . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_BUSINESS_TYPE . ' c', 'a.id_business_type=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_born_license($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,a.*
            ,concat(a.id,'," . App_Model::TBL_COMPANY_BORN_LICENSE . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_BORN_LICENSE . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_management($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,a.*")
            ->from(App_Model::TBL_COMPANY_MANAGEMENT . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_finance_bank($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,a.*,c.name as currency_name
            ,concat(a.id,'," . App_Model::TBL_COMPANY_FINANCE_BANK . "') as verification_history")
            ->from(App_Model::TBL_COMPANY_FINANCE_BANK . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_CURRENCY . ' c', 'a.id_currency=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_finance_report($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,a.*,c.name as currency_name")
            ->from(App_Model::TBL_COMPANY_FINANCE_REPORT . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_CURRENCY . ' c', 'a.id_currency=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_certification($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as certificate_name ,a.*")
            ->from(App_Model::TBL_COMPANY_CERTIFICATION . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_CERTIFICATE_TYPE . ' c', 'a.id_certificate_type=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_facilities($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as facility_type_name,a.*")
            ->from(App_Model::TBL_COMPANY_FACILITIES . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_FACILITIES_TYPE . ' c', 'a.id_facilities_type=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_experience($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as currency_name,a.*")
            ->from(App_Model::TBL_COMPANY_EXPERIENCE . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_CURRENCY . ' c', 'a.id_currency=c.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_company_competencies($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,'.')),b.name,' ',b.postfix_name) as company_name,d.name as competency,c.name as sub_competency,a.*,d.id as id_competency")
            ->from(App_Model::TBL_COMPANY_COMPETENCIES . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' c', 'a.id_company_sub_competency=c.id')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' d', 'c.id_company_competency=d.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }




    public function get_legal_doc($id = null, $id_company = null)
    {
        $data = $this->db
            ->select("concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name
            ,a.*")
            ->from(App_Model::TBL_COMPANY_LEGAL_DOC . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->where('a.deleted_at is null');


        if ($id != null) {
            $data->where('a.id', $id);
        }

        if ($id_company != null) {
            $data->where('a.id_company', $id_company);
        }

        return $data;
    }

    public function get_business_type($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->where('deleted_at is null')
            ->from(App_Model::TBL_BUSINESS_TYPE);
    }


    public function check_required_form_validation($id_user)
    {

        $result = (object) ['detail' => [], 'form_completed' => 0, 'form_incomplete' => 0, 'form_required_total' => 0, 'percentage' => 0];

        $user_role = $this->db->where('id_user', $id_user)
            ->join(App_Model::TBL_USR_ROLE . ' b', 'a.id_usr_role=b.id_usr_role')
            ->get(App_Model::TBL_USER . ' a')->row();

        $required_form = $this->db
            ->where('id_usr_role', $user_role->id_usr_role)
            ->where('deleted_at is null')
            ->get(App_Model::TBL_REQUIRED_FORM)
            ->result();

        $result->form_required_total = count($required_form);

        $id_company = null;
        $result->form_completed = 0;
        $result->form_incomplete = $result->form_required_total;

        foreach ($required_form as $f) {
            $result->detail[$f->tbl_name] = ['is_valid' => false, 'form_detail' => $f];
            if ($f->tbl_name == App_Model::TBL_COMPANY_PROFILE) {

                $company_profile = $this->db
                    ->where('id_user', $id_user)
                    ->get($f->tbl_name);
                if ($company_profile->num_rows() >= $f->minimum) {
                    $result->detail[$f->tbl_name] = ['is_valid' => true, 'form_detail' => $f];
                    $company_profile = $company_profile->row();
                    $id_company = $company_profile->id;

                    $result->form_completed += 1;
                    $result->form_incomplete -= 1;
                }
            } else {

                $company_data = $this->db
                    ->where('id_company', $id_company)
                    ->get($f->tbl_name);

                if ($company_data->num_rows() >= $f->minimum) {
                    $result->detail[$f->tbl_name] = ['is_valid' => true, 'form_detail' => $f];

                    $result->form_completed += 1;
                    $result->form_incomplete -= 1;
                }
            }
        }

        $result->percentage = ($result->form_completed <= 0) ? 100 : ($result->form_completed / $result->form_required_total) * 100;

        return $result;
    }
}
