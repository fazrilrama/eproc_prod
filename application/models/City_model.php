<?php
defined('BASEPATH') or exit('No direct script access allowed');

class City_model extends App_Model
{
    public function get_data($id = null, $province_id = null)
    {
        $data = $this->db->select('a.*')
            ->from(App_Model::TBL_CITY . ' a')
            ->join(App_Model::TBL_COUNTRY_PROVINCE . ' b', 'b.id=a.province_id')
            ->where('a.deleted_at is null');

        if ($id != null) $data = $this->db->where('a.id', $id);
        if ($province_id != null) $data = $this->db->where('a.province_id', $province_id);

        return $data;
    }
}
