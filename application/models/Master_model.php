<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Master_model extends CI_Model
{
    public function get_currency($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->where('deleted_at is null')
            ->from(App_Model::TBL_CURRENCY);
    }
    public function get_certificate_type($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->where('deleted_at is null')
            ->from(App_Model::TBL_CERTIFICATE_TYPE);
    }

    public function get_facilities_type($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->where('deleted_at is null')
            ->from(App_Model::TBL_FACILITIES_TYPE);
    }

    public function get_company_competency($id = null)
    {
        $data = $this->db->select('b.name as company_type,a.*')
            ->from(App_Model::TBL_COMPANY_COMPETENCY . ' a')
            ->join(App_Model::TBL_COMPANY_TYPE . ' b', 'b.id=a.id_company_type')
            ->where('a.deleted_at is null');
        if ($id != null) {
            $data->where('a.id', $id);
        }
        return $data;
    }

    public function get_company_sub_competency($id = null)
    {
        $data = $this->db->select('b.name as competency_name,a.*')
            ->from(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' a')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' b', 'a.id_company_competency=b.id')
            ->where('a.deleted_at is null');
        if ($id != null) {
            $data->where('a.id', $id);
        }
        return $data;
    }

    public function get_notification($where = [],$modifier=null)
    {
        foreach ($where as $w) {
            $this->db->where($w);
        }
        $data = $this->db
            ->select('
            a.created_at,
            b.name as from_name
            , b.email as from_email
            , c.name as to_user_name
            , c.email as to_user_email,
            d.role_name as to_role_name,
            a.from as fromUser,
            a.id,
            a.to_user,
            a.to_role,
            a.title,
            a.description,
            a.link_on_click,
            a.link_type,
            a.is_readed,
            a.updated_at,
            a.deleted_at')
            ->join('sys_user b', 'a.from=b.id_user')
            ->join('sys_user c', 'a.to_user=c.id_user', 'left')
            ->join('sys_usr_role d', 'a.to_role=d.id_usr_role', 'left')
            ->join(App_Model::TBL_USR_STATUS . ' e', 'b.id_usr_status=e.id_usr_status')
            ->where('e.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->where('a.deleted_at is null')
            ->where('is_readed=0')
            ->order_by('a.created_at', 'desc');
        
        if($modifier!=null){
            $data=$modifier($data);
        }
        

        return $data->get(App_Model::TBL_NOTIFICATION . ' a');
    }


    public function get_required_form($where = [], $rawWhere = [])
    {
        foreach ($where as $key => $val) {
            $this->db->where($key, $val);
        }
        foreach ($rawWhere as $w) {
            $this->db->where($w);
        }

        return $this->db
            ->select('b.role_name,a.*')
            ->join(App_Model::TBL_USR_ROLE . ' b', 'b.id_usr_role=a.id_usr_role')
            ->where('a.deleted_at is null')
            ->get(App_Model::TBL_REQUIRED_FORM . ' a');
    }

    public function get_db_tables($db, $where = [], $rawWhere = [])
    {
        foreach ($where as $key => $val) {
            $this->db->where($key, $val);
        }
        foreach ($rawWhere as $w) {
            $this->db->where($w);
        }

        return $this->db->select('table_name')
            ->where('table_schema', $db)
            ->get('information_schema.tables');
    }
}
