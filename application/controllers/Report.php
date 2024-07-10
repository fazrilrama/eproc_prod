<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Report extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get_report_vendor'],
                ['method' => 'get_report_feedback'],
                ['method' => 'get_city_per_branch']
            ]
        ]);

        $this->load->model('Company_competency_model', 'competency');
        $this->load->model('Company_type_model', 'type');
        $this->load->model('Company_model', 'company');
        $this->load->model('Company_subcompetency_model', 'sub_competency');
        $this->load->model('Company_workarea_model', 'workarea');
        $this->load->model('Country_model', 'country');
        $this->load->model('Country_province_model', 'province');

        $this->load->model('User_status_model', 'user_status');
        $this->load->model('User_role_model', 'user_role');
        $this->load->model('User_model', 'user');
        $this->load->model('Master_model', 'master');
    }


    public function vendor()
    {
        $this->load->view('pages/report/vendor');
    }

    public function get_report_vendor()
    {
        $f_kompetensi = $this->input->get('f_kompetensi');
        $f_sub_kompetensi = $this->input->get('f_sub_kompetensi');
        $f_area_kerja = $this->input->get('f_area_kerja');
        $f_bidang = $this->input->get('f_bidang');
        $f_branch = $this->input->get('f_branch');

        $kompetensi_name_condition = "";
        $kompetensi_name_where = "";
        $sub_kompetensi_name_condition = "";
        $sub_kompetensi_name_where = "";
        $area_kerja = "";
        $bidang_where = "";
        $branch_where = "";

        if ($f_kompetensi != null) {
            $kompetensi_name_condition = " AND h.id=$f_kompetensi";
            $kompetensi_name_where = " AND $f_kompetensi in (SELECT h.id FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id )";
        }

        if ($f_sub_kompetensi != null) {
            $sub_kompetensi_name_condition = " AND g.id=$f_sub_kompetensi";
            $sub_kompetensi_name_where = " AND $f_sub_kompetensi in (SELECT h.id FROM company_competencies f 
            JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
            JOIN m_company_competency h on h.id=g.id_company_competency
            WHERE f.deleted_at is null AND f.id_company=a.id )";
        }

        if ($f_bidang != null) {
            $bidang_where = " AND $f_bidang in (SELECT i.id_company_type FROM company_type i
            WHERE i.deleted_at is null AND i.id_company=a.id)";
        }

        if ($f_area_kerja != null) {
            $area_kerja = " AND $f_area_kerja in (select id_city from company_work_area where id_company=a.id )";
        }

        if ($f_branch != null && $f_branch != "1000") {
            $branch_where = " AND  (
                    SELECT count(id_city) FROM company_work_area 
                    WHERE id_company=a.id
                    AND id_city in (SELECT id from m_city WHERE branch_id='$f_branch') 
                )>=1
            ";
        }

        $sql = "SELECT a.*,
        b.address,
        b.email,    
        b.phone,
        c.no as no_npwp,
        d.id_sap,
        d.id_group,
        e.name as group_name,
        e.description as group_desc
        , (SELECT GROUP_CONCAT(DISTINCT(h.name)) FROM company_competencies f 
           JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
           JOIN m_company_competency h on h.id=g.id_company_competency
           WHERE f.deleted_at is null AND f.id_company=a.id $kompetensi_name_condition ) as competencies_name
        , (SELECT GROUP_CONCAT(DISTINCT(g.name)) FROM company_competencies f 
           JOIN m_company_sub_competency g on g.id=f.id_company_sub_competency
           JOIN m_company_competency h on h.id=g.id_company_competency
           WHERE f.deleted_at is null AND f.id_company=a.id $sub_kompetensi_name_condition ) as sub_competencies_name
        , (SELECT GROUP_CONCAT(DISTINCT(i.name)) FROM company_work_area j
            inner join m_city i on i.id=j.id_city 
            where j.id_Company=a.id) as work_area
        FROM company_profile a
        JOIN company_contact b on (a.id=b.id_company and b.deleted_at is null)
        JOIN company_legal_npwp c on (a.id=c.id_company and c.deleted_at is null)
        JOIN tbl_sync_sap d on (a.id=d.id_company and d.deleted_at is null)
        JOIN m_group e on d.id_group=e.id
        JOIN sys_user f on f.id_user=a.id_user
        WHERE a.deleted_at is null
        AND f.id_usr_status=2
        AND f.is_blacklisted!=1
        $kompetensi_name_where
        $sub_kompetensi_name_where
        $area_kerja
        $bidang_where
        $branch_where";

        echo json_encode($this->db->query($sql)->result());
    }

    public function get_city_per_branch()
    {
        $branch = $this->input->get('branch');
        if ($branch != '1000') $this->db->like('branch_id', $branch);
        // if ($branch != '1000') $this->db->where('branch_id', $branch);
        echo json_encode($this
            ->db
            ->where('deleted_at is null')
            ->get('m_city')->result());
    }

    public function get_report_feedback()
    {

        $f_company = $this->input->get('f_company');
        $f_start_date = $this->input->get('f_start_date');
        $f_end_date = $this->input->get('f_end_date');

        $sql = "SELECT concat(if(a.prefix_name is null,'',concat(a.prefix_name,' ') ),a.name) as company_name, b.email,b.phone,c.id_sap,e.name as group_name, e.description as group_desc
        ,d.*, MONTH(d.created_at) as feedback_month, YEAR(d.created_at) as feedback_year 
        FROM company_profile a
        JOIN company_contact b ON (b.id_company=a.id and b.main_contact=1)
        JOIN tbl_sync_sap c on c.id_company=a.id
        JOIN tbl_vendor_valuation d on d.id_company=a.id
        JOIN m_group e on e.id=c.id_group
        where a.deleted_at is null
        AND a.id=?
        AND ( d.created_at >= ? AND d.created_at <= ? )";

        $data = $this->db->query($sql, [$f_company, $f_start_date . '-01', $f_end_date . '-01'])->result();

        echo json_encode($data);
    }

    public function customer_feedback()
    {

        $this->load->view('pages/report/customer_feedback');
    }
}
