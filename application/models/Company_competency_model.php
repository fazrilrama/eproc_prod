<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company_competency_model extends App_Model
{
    public function get_data($id = null)
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
}
