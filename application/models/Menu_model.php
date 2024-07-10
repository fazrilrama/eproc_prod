<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu_model extends CI_Model
{

    //Tabel Config
    var $table_name = App_Model::TBL_MENU;
    var $table_attr = array(
        'id' => 'id_menu',
        '1' => 'label',
        '2' => 'link',
        '3' => 'icon',
        '4' => 'have_crud',
        '5' => 'parent',
        '6' => 'is_head_section',
        '7' => 'sort',
        '8' => 'create_at'
    );

    public function get_menu($params = array())
    {
        $this->db
            ->select('a.*, (SELECT COUNT(*) FROM sys_menu WHERE sys_menu.parent=a.id_menu) as count_child, 
        (SELECT sys_menu.is_head_section FROM sys_menu WHERE sys_menu.id_menu=a.parent) as parent_head_section')
            ->from('sys_menu a');
        if (isset($params['id_role'])) {
            if ($params['id_role'] != null) $this->db->join('sys_menu_privilege b', 'b.id_menu=a.id_menu');
        }
        if (isset($params['id_role'])) {
            if ($params['id_role'] != null) $this->db->where('b.id_usr_role', $params['id_role']);
        }
        if (isset($params['id_menu'])) {
            if ($params['id_menu'] != null) $this->db->where('a.id_menu', $params['id_menu']);
        }
        $this->db->where('(a.app_segment=0 or a.app_segment=1)');
        return $this->db->order_by('sort ASC')
            ->get()
            ->result_array();
    }

    public function get_menu_privilege($id_role = null)
    {
        $this->db->select('a.*,b.*')
            ->from('sys_menu a')
            ->join('sys_menu_privilege b', 'a.id_menu=b.id_menu');
        return ($id_role != null) ? $this->db->where('b.id_usr_role', $id_role)->get()->result_array() : $this->db->get()->result_array();
    }

    public function get_menu_role_privilege()
    {
        $id_role = $this->input->get('id_role');
        $sql = "SELECT a.*
        , (SELECT COUNT(*) FROM sys_menu WHERE sys_menu.parent=a.id_menu) as count_child
        , CASE when (SELECT sys_menu_privilege.id
        FROM sys_menu_privilege 
        WHERE sys_menu_privilege.id_usr_role=$id_role
        AND sys_menu_privilege.id_menu=a.id_menu limit 1
        ) is null then 0 else 1 end as is_checked
        FROM sys_menu a
        ORDER BY a.sort ASC";
        return $this->db->query($sql)->result_array();
    }

    public function add()
    {

        $data = array(
            $this->table_attr['1'] => $this->input->post($this->table_attr['1']),
            $this->table_attr['2'] => ($this->input->post($this->table_attr['2']) != NULL) ? $this->input->post($this->table_attr['2']) : 'javascript:void(0)',
            $this->table_attr['3'] => ($this->input->post($this->table_attr['3']) != NULL) ? $this->input->post($this->table_attr['3']) : 'pe-7s-home',
            $this->table_attr['6'] => $this->input->post($this->table_attr['6']),
            'app_segment'=>1
        );

        $this->db->trans_start();
        $this->db->insert($this->table_name, $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function edit()
    {
        $id = $this->input->post('id');

        $data = array(
            $this->table_attr['1'] => $this->input->post($this->table_attr['1']),
            $this->table_attr['2'] => ($this->input->post($this->table_attr['2']) != NULL) ? $this->input->post($this->table_attr['2']) : 'javascript:void(0)',
            $this->table_attr['3'] => ($this->input->post($this->table_attr['3']) != NULL) ? $this->input->post($this->table_attr['3']) : 'pe-7s-home',
            $this->table_attr['6'] => $this->input->post($this->table_attr['6'])
        );

        $this->db->trans_start();
        $this->db
            ->where($this->table_attr['id'], $id)
            ->update($this->table_name, $data);
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function delete()
    {
        $this->db->trans_start();

        //find child
        $children = $this->db
            ->where($this->table_attr['5'], $this->input->post('id'))
            ->get($this->table_name)
            ->result_array();

        foreach ($children as $child) {
            $this->db->where($this->table_attr['id'], $child[$this->table_attr['id']])
                ->delete('sys_menu_privilege');
        }

        $this->db->where($this->table_attr['id'], $this->input->post('id'))
            ->delete('sys_menu_privilege');

        $this->db->where($this->table_attr['5'], $this->input->post('id'))
            ->delete($this->table_name);

        $this->db->where($this->table_attr['id'], $this->input->post('id'))
            ->delete($this->table_name);

        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }

    public function save_map_menu($data)
    {
        $this->db->trans_start();
        for ($i = 1; $i <= count($data); $i++) {
            $update = array(
                $this->table_attr['5'] => $data[$i - 1]['parent'],
                $this->table_attr['7'] => $i
            );
            $this->db
                ->where($this->table_attr['id'], $data[$i - 1]['id'])
                ->update($this->table_name, $update);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() === TRUE) {
            $this->db->trans_commit();
            return true;
        } else {
            $this->db->trans_rollback();
            return false;
        }
    }
}
