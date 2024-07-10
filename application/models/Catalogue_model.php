<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Catalogue_model extends App_Model
{

    public function get_budget($params = [])
    {
        if (count($params) > 0) {

            foreach ($params as $key => $val) {
                $this->db->where($key, $val);
            }
            $this->db->where('type', 1);
            $data_ops = $this->db->get('m_budget')->row();


            foreach ($params as $key => $val) {
                $this->db->where($key, $val);
            }
            $this->db->where('type', 2);
            $data_nonops = $this->db->get('m_budget')->row();

            return [
                'non_ops' => $data_nonops,
                'ops' => $data_ops
            ];
        } else {
            return [
                'non_ops' => [
                    'available' => 0
                ],
                'ops' => [
                    'available' => 0
                ]
            ];
        }
    }

    public function get_data_ssp($sqlWhere = [])
    {
        $sql = "SELECT  concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as kompetensi,a.* 
        FROM " . App_Model::TBL_COMPANY_CATALOGUE . " a
        INNER JOIN " . App_Model::TBL_COMPANY_PROFILE . " b ON a.id_company=b.id 
        INNER JOIN " . App_Model::TBL_COMPANY_SUB_COMPETENCY . " c ON a.id_sub_competencies=c.id 
        INNER JOIN " . App_Model::TBL_COMPANY_COMPETENCY . " d ON c.id_company_competency=d.id
        WHERE a.deleted_at is null";
        $data = $this->db->query($sql)->list_fields();

        $table = '(' . $this->db->last_query() . ') AS DATA';

        // Table's primary key
        $primaryKey = 'id';

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

    public function get_data($id = null, $id_user = null)
    {
        if ($id != null) $this->db->where('a.id', $id);
        if ($id_user != null) $this->db->where('b.id', $id_user);
        return $this->db
            ->select(" concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as kompetensi,a.*")
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' c', 'a.id_sub_competencies=c.id')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' d', 'c.id_company_competency=d.id')
            ->where('a.deleted_at is null')
            ->from(App_Model::TBL_COMPANY_CATALOGUE . ' a');
    }

    public function get_search($id = null)
    {
        if ($id != null) $this->db->where('a.id', $id);
        return $this->db
            ->select(" concat(if(b.prefix_name is null,'',concat(b.prefix_name,' ') ),b.name) as company_name,c.name as kompetensi,a.*,
            (select GROUP_CONCAT(m_city.name) 
            from m_city 
            inner join company_work_area on company_work_area.id_city=m_city.id 
            WHERE company_work_area.id_company=a.id_company) as cities_networking ")
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' c', 'a.id_sub_competencies=c.id')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' d', 'c.id_company_competency=d.id')
            ->from(App_Model::TBL_COMPANY_CATALOGUE . ' a')
            ->where('a.deleted_at is null');
    }
    public function get_search_count($id = null)
    {
        if ($id != null) $this->db->where('a.id', $id);
        return $this->db
            ->select("b.name")
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' c', 'a.id_sub_competencies=c.id')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' d', 'c.id_company_competency=d.id')
            ->from(App_Model::TBL_COMPANY_CATALOGUE . ' a')
            ->where('a.deleted_at is null');
    }
}
