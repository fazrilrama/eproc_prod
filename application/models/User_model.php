<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends App_Model
{
    public function get_data($id = null)
    {
        $data = $this->db->select("a.*,b.role_name,c.status_name,
        ( SELECT created_at FROM `sys_login_session` WHERE identity=a.email
        AND status=1
        ORDER BY created_at DESC
        limit 1) as last_login,
        ( SELECT b.status_name FROM sys_login_session a
        inner join sys_usr_status b on b.id_usr_status=a.id_usr_status  
        WHERE a.identity=a.email
        AND a.id_usr_status!=3
        ORDER BY a.created_at DESC
        limit 1) as last_status,
        mc.codename as company_owner_name
        ")
            ->from(App_Model::TBL_USER . ' a')
            ->join(App_Model::TBL_USR_ROLE . ' b', 'b.id_usr_role=a.id_usr_role')
            ->join(App_Model::TBL_USR_STATUS . ' c', 'c.id_usr_status=a.id_usr_status')
            ->join('m_company mc','mc.id=a.id_company_owner')
            ->where('a.deleted_at is null');

        if ($id != null) $data = $this->db->where('a.id_user', $id);

        return $data;
    }

    public function get_data_ssp($sqlWhere = [])
    {
        $sql = "SELECT a.*,b.role_name,c.status_name,
        ( SELECT created_at FROM `sys_login_session` WHERE identity=a.email
        AND status=1
        ORDER BY created_at DESC
        limit 1) as last_login,
        ( SELECT b.status_name FROM sys_login_session a
        inner join sys_usr_status b on b.id_usr_status=a.id_usr_status  
        WHERE a.identity=a.email
        AND a.id_usr_status!=3
        ORDER BY a.created_at DESC
        limit 1) as last_status,
        mc.codename as company_owner_name
        FROM " . App_Model::TBL_USER . " a 
        INNER JOIN " . App_Model::TBL_USR_ROLE . " b ON b.id_usr_role=a.id_usr_role
        INNER JOIN " . App_Model::TBL_USR_STATUS . " c ON c.id_usr_status=a.id_usr_status 
        INNER JOIN m_company mc on mc.id=a.id_company_owner
        WHERE a.deleted_at is null";


        $data = $this->db->query($sql)->list_fields();

        $table = '(' . $this->db->last_query() . ') AS DATA';

        // Table's primary key
        $primaryKey = 'id_user';

        $columns = array();

        foreach ($data as $key => $value) {
            $columns[] = ['db' => $value, 'dt' => $key];
        }

        // SQL server connection information
        $sql_details = ssp_default_db();

        $result = SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere);

        $final_result = $result;
        $final_result['data'] = [];
        $i = 0;
        foreach ($result['data'] as $d) {
            $j = 0;
            foreach ($data as $key => $value) {
                $final_result['data'][$i][$value] = $d[$j];
                $j++;
            }
            $i++;
        }

        return $final_result;
    }

    public function edit($params = [], $where = [])
    {
        rcrud_edit(App_Model::TBL_USER, $params, $where);
    }

    public function get_all($start = 1, $length = 10, $sort_column = 0, $sort_rule = "asc", $search = null)
    {
        $url = $this->config->item('api_host') . 'employee/all';
        $header = [
            'X-API-KEY:' . $this->config->item('api_key')
        ];
        $post_data = 'start=' . $start
            . '&length=' . $length
            . '&sort_column=' . $sort_column
            . '&sort_rule=' . $sort_rule
            . '&search=' . $search;

        $user = curl_post($url, $header, $post_data);

        $data = [];
        if ($user != null && $user['status']) {

            $i = 0;
            foreach ($user['data'] as $d) {
                $data_own = $this->db->where('nik', $d['nik'])->get('m_user')->result_array();
                $is_exist = count($data_own) >= 1;
                if ($is_exist) {
                    $url = $this->config->item('api_host') . 'employee/detail/' . $d['nik'];
                    $header = [
                        'X-API-KEY:' . $this->config->item('api_key')
                    ];
                    $data_siska = curl_json_get($url, $header);

                    $data[$i] = $this->db
                        ->select('m_user.nik
                        ,m_user.created_at
                        ,m_user.updated_at
                        ,m_user.deleted_at
                        ,m_user_role.id_usr_role
                        ,m_user_role.role_name
                        ,m_user_role.role_desc
                        ,sys_user_session.id_usr_session
                        ,sys_user_session.login_time
                        ,sys_user_session.usr_login_ip
                        ,sys_user_session.logout_time
                        ,sys_user_session.usr_logout_ip
                        ,sys_user_session.is_logged
                        ,m_user_status.id_usr_status
                        ,m_user_status.status_desc
                        ,m_user_status.status_name
                        ')
                        ->from('m_user')
                        ->join('m_user_role', 'm_user_role.id_usr_role=m_user.role_id')
                        ->join('sys_user_session', 'sys_user_session.id_user=m_user.nik')
                        ->join('m_user_status', 'm_user_status.id_usr_status=m_user.id_status')
                        ->where('m_user.deleted_at is null')
                        ->where('m_user.nik', $d['nik'])
                        ->get()
                        ->row();
                    $data[$i]->created_at = date('d M Y H:i:s', strtotime($data[$i]->created_at));
                    $data[$i]->login_time = date('d M Y H:i:s', strtotime($data[$i]->login_time));
                    $data[$i]->logout_time = date('d M Y H:i:s', strtotime($data[$i]->logout_time));

                    if ($data_siska['result'] != null) {
                        $data_siska['result']['emp_dob'] = date('d, M Y', strtotime($data_siska['result']['emp_dob']));
                        $data[$i]->siska = $data_siska['result'];
                    }
                } else {
                    $data[$i] = [
                        'nik' => null, 'created_at' => null, 'deleted_at' => null, 'updated_at' => null, 'id_usr_role' => null, 'role_name' => null, 'role_desc' => null, 'id_usr_session' => null, 'login_time' => null, 'usr_login_ip' => null, 'logout_time' => null, 'usr_logout_ip' => null, 'is_logged' => 'f', 'id_usr_status' => 'f', 'status_name' => null, 'status_desc' => null, 'siska' => null
                    ];

                    $data[$i]['nik'] = $d['nik'];
                    // Default Role
                    $role = $this->db->where('id_usr_role', $this->db->get('sys_settings')->row()
                        ->default_usr_role)->get('m_user_role')->row();
                    $data[$i]['id_usr_role'] = $role->id_usr_role;
                    $data[$i]['role_name'] = $role->role_name;
                    $data[$i]['role_desc'] = $role->role_desc;

                    // Default Status
                    $status = $this->db->where('id_usr_status', $this->db->get('sys_settings')->row()
                        ->default_usr_status)->get('m_user_status')->row();
                    $data[$i]['id_usr_status'] = $status->id_usr_status;
                    $data[$i]['status_name'] = $status->status_name;
                    $data[$i]['status_desc'] = $status->status_desc;

                    $d['emp_dob'] = date('d, M Y', strtotime($d['emp_dob']));
                    $data[$i]['siska'] = $d;
                }
                $i++;
            }
        }

        return [
            'status' => $user['status'], 'recordsFiltered' => $user['recordsFiltered'], 'recordsTotal' => $user['recordsTotal'], 'data' => $data
        ];
    }
}
