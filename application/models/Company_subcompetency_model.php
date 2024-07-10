<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Company_subcompetency_model extends App_Model
{
    public function get_data($where = [])
    {
        foreach ($where as $key => $val) {
            if ($val != null) {
                $this->db->where($key, $val);
            }
        }
        $data = $this->db->select('b.name as competency_name,a.*')
            ->from(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' a')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' b', 'b.id=a.id_company_competency')
            ->where('a.deleted_at is null');
        return $data;
    }
}
