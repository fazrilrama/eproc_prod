<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Country_province_model extends App_Model
{
    public function get_data($id = null, $id_country = null)
    {
        $data = $this->db->select('b.name as country_name,a.*')
            ->from(App_Model::TBL_COUNTRY_PROVINCE . ' a')
            ->join(App_Model::TBL_COUNTRY . ' b', 'b.id=a.id_country')
            ->where('a.deleted_at is null');

        if ($id != null) $data = $this->db->where('a.id', $id);
        if ($id_country != null) $data = $this->db->where('a.id_country', $id_country);

        return $data;
    }
}
