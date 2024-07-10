<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Menu extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get']
            ]
        ]);
        $this->load->model('Menu_model', 'menu');
    }

    public function index()
    {
        $this->main();
    }

    public function main()
    {
        $this->set_page_title('pe-7s-menu', 'Role Menu Privilege', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'label' => '',
                'link' => '#dashboard'
            ],
            [
                'icon' => '',
                'label' => 'System',
            ], [
                'active' => true,
                'icon' => '',
                'label' => 'Role Menu Privilege'
            ]
        ]);
        $this->load->view('pages/menu/main');
    }

    public function get()
    {
        $params = array(
            'id_menu' => $this->input->get('id_menu'),
            'id_role' => $this->input->get('id_role'),
        );
        echo json_encode($this->menu->get_menu($params));
    }

    public function add()
    {
        if ($this->menu->add() == true) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    }

    public function edit()
    {
        if ($this->menu->edit()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    }

    public function delete()
    {
        if ($this->menu->delete()) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    }

    public function get_role_privilege()
    {
        $data = $this->menu->get_menu_role_privilege();
        echo json_encode($this->map_menu_role($data, 0));
    }

    function map_menu_role($menus, $parent_id)
    {
        $mapMenu = array();
        foreach ($menus as $menu) {
            if ($menu['parent'] == $parent_id) {

                $mapMenu[] = array(
                    'id' => $menu['id_menu'],
                    'text' => $menu['label'],
                    'data' => '',

                    'type' => ($menu['count_child'] > 0) ? "default" : "file",
                    'state' => array('opened' => true, 'checked' => ($menu['is_checked'] == 0) ? false : true),
                    'children' => $this->map_menu_role($menus, $menu['id_menu'])
                );
            }
        }

        return $mapMenu;
    }

    function save_map_menu_role()
    {
        $menu_data = $this->input->post('menu_data');
        $id_role = $this->input->post('id_role');

        $task = $id_role != null;
        if ($task) $task = $this->db->query('delete from sys_menu_privilege where id_usr_role=?', [$id_role]);

        if ($menu_data != null && $task) {
            if (count($menu_data) > 0) {
                $sql = "INSERT INTO sys_menu_privilege(id_usr_role,id_menu) VALUES ";
                $is_need_comma = false;
                for ($i = 0; $i < count($menu_data); $i++) {
                    $menu = $menu_data[$i];

                    if ($is_need_comma) {
                        $sql .= ", ";
                    }
                    $sql .= " (" . $id_role . "," . $menu['id_menu'] . ")";
                    $is_need_comma = true;
                }
                $task = $this->db->query($sql);

                // Delete duplicate
                for ($i = 0; $i < count($menu_data); $i++) {
                    $menu = $menu_data[$i];
                    $sql = "SELECT count(*) as total from sys_menu_privilege where id_menu=? and id_usr_role=?";
                    $data = $this->db->query($sql, [$menu['id_menu'], $id_role])->row();
                    if ($data->total > 1) {
                        $sql = "SELECT * from sys_menu_privilege where id_menu=? and id_usr_role=? limit 1";
                        $data = $this->db->query($sql, [$menu['id_menu'], $id_role])->row();
                        $sql = "DELETE from sys_menu_privilege where id_menu=? and id_usr_role=? and id!=?";
                        $this->db->query($sql, [$menu['id_menu'], $id_role, $data->id]);
                    }
                }
            }
        }


        if ($task) {
            echo json_encode(array('status' => true, 'success' => true));
        } else {
            echo json_encode(array('status' => false, 'success' => false));
        }
    }

    function save_map_menu()
    {
        $menu_data = $this->input->post('menu_data');
        $task = $this->menu->save_map_menu($menu_data);
        if ($task) {
            echo json_encode(array('status' => true, 'success' => true));
        } else {
            echo json_encode(array('status' => false, 'success' => false));
        }
    }

    function map_menu($menus, $parent_id)
    {
        $map = array();
        $menu_parent = array();
        $children = array();
        foreach ($menus as $menu) {
            if (isset($menu->children) == true) {
                $children = $this->map_menu($menu->children, $menu->id);
            }
            $menu_parent[] = array('id' => $menu->id, 'parent_id' => $parent_id);
            $map = array_merge($menu_parent, $children);
        }
        return $map;
    }
}
