<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Country_model extends App_Model
{
    public function get_data($id = null)
    {
        if ($id != null) $this->db->where('id', $id);
        return $this->db
            ->where('deleted_at is null')
            ->from(App_Model::TBL_COUNTRY);
    }
}
