<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'get_data'],
                ['method' => 'get_data_ssp'],
                ['method' => 'update'],
                ['method' => 'delete_data'],
                ['method' => 'index'],
                ['method'=>'get_count_data']
            ]
        ]);

        $this->load->model('Master_model', 'notif');
    }

    public function index()
    {
        $table_name = 'tbl_notification';
        $table_fields = $this->notif->get_notification()->list_fields();
        $fields_exception = ['to_role_name','id', 'fromUser', 'to_role', 'to_user', 'is_readed', 'from', 'link_type', 'updated_at', 'deleted_at'];
        $table_header = get_header($table_fields, $fields_exception, [
            'created_at' => [
                'text' => 'Dikirim Tgl',
            ],
            'from_name' => [
                'text' => 'Nama Pengirim',
            ],
            'from_email' => [
                'text' => 'Email Pengirim',
            ],
            'to_user_name' => [
                'text' => 'Akun Tujuan',
            ],
            'to_user_email' => [
                'text' => 'Email Akun Tujuan',
            ],
            'to_role_name' => [
                'text' => 'Role Tujuan',
            ],
            'title' => [
                'text' => 'Judul',
            ],
            'description' => [
                'text' => 'Deskripsi',
            ],
            'link_on_click' => [
                'text' => 'Aksi',
            ],
            'deleted_at' => [
                'text' => 'Aksi',
            ]
        ]);

        $this->set_page_title('pe-7s-check', 'Notifikasi', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => ' Pengguna'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Notifikasi'
            ]
        ]);

        $dataKey = 'id';

        $data['header_title'] = 'Notifikasi';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'notification/get_data';
        $data['delete_url'] = 'notification/delete_data';
        $data['update_url'] = '';
        $data['add_url'] = '';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;
        $data['action_delete'] = 'enabled';
        $data['action_add'] = 'false';
        $data['action_edit'] = 'false';
        $data['ssp'] = 'true';
        $data['ssp_url'] = 'notification/get_data_ssp';

        $data['render_column_modifier'] = '{
            link_on_click:{
                render:function(data){
                    return `<span
                    data-id="${data.id}"
                    link="${data.link_on_click}"
                    link-type="${data.link_type}"
                    onclick="toNotifLink(\'${data.link_on_click}\',\'${data.link_type}\',\'${data.id}\')" class="notif_click" style="cursor:pointer;color:blue;">
                        Selengkapnya
                    </span>`;
                }
            },
            description:{
                render:function(data){
                    return `
                    <a data-toggle="collapse" href="#descnotif-${data.id}" role="button" aria-expanded="false" aria-controls="descnotif-${data.id}">
                        Lihat Deskripsi
                    </a>
                    <div class="collapse" id="descnotif-${data.id}" style="border-radius:5px;border:1px solid lightgrey;padding:10px;background-color:white;">
                        <div style="height:200px; width:200px;overflow:auto;overflow-wrap:break-word;color:black !important;">
                            ${data.description}
                        </div>
                    </div>`;
                }
            }
        }';
        $data['add_scripts'] = [
            base_url('assets/js/page/notification.js?v=1.0.0'),
        ];

        $this->load->view('pages/master/master_view', $data);
    }

    public function get_data()
    {
        $where = [];
        if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) {
            array_push($where, " ( (a.to_user='" . $this->session->userdata('user')['id_user'] . "' and a.to_role is null) or (a.to_role='" . $this->session->userdata('user')['id_usr_role'] . "' and a.to_user is null) ) ");
        }

        echo json_encode($this->notif->get_notification($where)->result());
    }
    public function get_count_data()
    {
        $where = [];
        if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) {
            array_push($where, " ( (a.to_user='" . $this->session->userdata('user')['id_user'] . "' and a.to_role is null) or (a.to_role='" . $this->session->userdata('user')['id_usr_role'] . "' and a.to_user is null) ) ");
        }
        $data=$this->notif->get_notification($where,function($sql){
            $sql->limit(5);
            return $sql;
        })->result();

        $dataCount=$this->notif->get_notification($where)->num_rows();

        echo json_encode(
            [
                'data'=>$data,
                'length'=>$dataCount
            ]
        );
    }
    public function get_data_ssp_bak()
    {
        $where = [];
        if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) {
            array_push($where, " ( (a.to_user='" . $this->session->userdata('user')['id_user'] . "' and a.to_role is null) or (a.to_role='" . $this->session->userdata('user')['id_usr_role'] . "' and a.to_user is null) ) ");
        }
        $this->notif->get_notification($where)->free_result();

        $data = $this->db->query($this->db->last_query())->list_fields();

        $table = '(' . $this->db->last_query() . ') AS DATA order by created_at desc';

        // Table's primary key
        $primaryKey = 'id';

        // $limit=$this->input->get('length');
        // $offset=$this->input->get('start');
        // $search=$this->input->get('search[value]');

        // $sqlCount=$this->db->query($this->db->last_query())->num_rows();
        // $data=$this->db->query("SELECT (".$this->db->last_query().") as dt limit ".$limit." offset ".$offset)->result();
        // echo json_encode([
        //     'data'=>$data,
        //     'recordsTotal'=>$sqlCount,
        //     'recordsFiltered'=>$sqlCount
        // ]);
        // die();

        $columns = array();

        $i=0;
        foreach ($data as $key => $value) {
            $columns[] = ['db' => $value, 'dt' => $value];
            $columns[] = ['db' => $value, 'dt' => $i];
            $i++;
        }

        // SQL server connection information
        $sql_details = ssp_default_db();

        $sqlWhere = [];
        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sqlWhere));
    }

    public function get_data_ssp()
    {
        $where = [];
        if ($this->session->userdata('user')['id_usr_role'] != App_Model::ROLE_ADMIN) {
            array_push($where, " ( (a.to_user='" . $this->session->userdata('user')['id_user'] . "' and a.to_role is null) or (a.to_role='" . $this->session->userdata('user')['id_usr_role'] . "' and a.to_user is null) ) ");
        }
        $search=$this->input->get('search[value]');
        $limit=$this->input->get('length');
        $offset=$this->input->get('start');

        $countData=$this->notif->get_notification($where)->num_rows();
        $data=$this->notif->get_notification($where,function($sql) use($search,$limit,$offset){
            if($search!=null){
                $sql->group_start();
                    $sql->or_like('lower(a.created_at)',strtolower($search));
                    $sql->or_like('lower(b.name)',strtolower($search));
                    $sql->or_like('lower(c.name)',strtolower($search));
                    $sql->or_like('lower(a.title)',strtolower($search));
                    $sql->or_like('lower(a.description)',strtolower($search));
                    $sql->or_like('lower(c.email)',strtolower($search));
                    $sql->or_like('lower(a.from)',strtolower($search));
                    $sql->or_like('lower(a.to_user)',strtolower($search));
                $sql->group_end();
            }
            $sql->limit($limit);
            $sql->offset($offset);
            return $sql;
        })->result();
        echo json_encode([
            'data'=>$data,
            'recordsFiltered'=>$countData,
            'recordsTotal'=>$countData,
        ]);
    }

    public function delete_data()
    {
        echo json_encode(delete_data());
    }

    public function update()
    {
        $id = $this->input->post('id');

        echo json_encode([
            'success' => $this->db->where('id', $id)->update('tbl_notification', ['is_readed' => 1])
        ]);
    }
}
