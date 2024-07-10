<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Verification_model extends App_Model
{
    public function get_profile_basic($id_user = null, $id = null)
    {
        $data = $this->db->select('c.role_name, b.email as user_email,a.*,concat(a.id,\',' . App_Model::TBL_COMPANY_PROFILE . '\') as verification_history')
            ->from(App_Model::TBL_COMPANY_PROFILE . ' a')
            ->join(App_Model::TBL_USER . ' b', 'a.id_user=b.id_user')
            ->join(App_Model::TBL_USR_ROLE . ' c', 'b.id_usr_role=c.id_usr_role')
            ->join(App_Model::TBL_USR_STATUS . ' e', 'b.id_usr_status=e.id_usr_status')
            ->where('e.id_usr_status != ' . App_Model::STAT_ACCOUNT_VERIFY_PROFILE)
            ->where('a.deleted_at is null')
            ->where('a.verification_status', 'Pending Verification')
            ->where('( c.id_usr_role=2 or c.id_usr_role=3 or c.id_usr_role=6 or c.id_usr_role=7)');
        if ($id != null) {
            $data->where('a.id', $id);
        }
        if ($id_user != null) {
            $data->where('a.id_user', $id_user);
        }
        return $data;
    }
}
