<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shopping extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'submit_order'],
                ['method' => 'order_list'],
                ['method' => 'get_order_list'],
                ['method' => 'order_approval'],
                ['method' => 'submit_approval'],
                ['method' => 'get_history'],
                ['method' => 'delete_order'],
            ]
        ]);
    }

    public function delete_order(){
        $id=$this->input->post('id');
        $success=false;
        if($id!=null){
            $success=$this->db->where('id',$id)->update('shopping_cart',['deleted_at'=>date('Y-m-d H:i:s')]);
        }
        echo json_encode(
            [
                'success'=>$success
            ]
        );
    }

    public function submit_order()
    {
        $buyer_id = $this->input->post('buyer_id');
        $product_id = $this->input->post('product_id');
        $qty = $this->input->post('qty');
        $note = $this->input->post('note');
        $order_type = $this->input->post('order_type');

        $catalogue = $this->db->where('id', $product_id)
            ->get('company_catalogue')->row();

        $success = $this->db->insert('shopping_cart', [
            'buyer_id' => $buyer_id,
            'product_id' => $product_id,
            'qty' => $qty,
            'note' => $note,
            'order_type' => $order_type,
            'status' => 2,
            'product_name' => ($catalogue != null) ? $catalogue->product_name : null,
            'main_price' => ($catalogue != null) ? $catalogue->main_price : 0,
            'total_price' => ($catalogue != null) ? $catalogue->main_price * $qty : 0,
            'verificator' => $this->session->userdata('user')['id_user']
        ]);

        $transaction = $this->db
            ->select('a.*,b.id_company,c.id_user')
            ->join('company_catalogue b', 'a.product_id=b.id')
            ->join('company_profile c', 'c.id=b.id_company')
            ->where('a.deleted_at is null')
            ->where('a.buyer_id', $buyer_id)
            ->where('a.product_id', $product_id)
            ->order_by('created_at', 'desc')
            ->limit(1)
            ->get('shopping_cart a')
            ->row();
        if ($transaction != null) {
            $count = $this->db->select('a.*')
                ->from('shopping_cart a')
                ->where('a.buyer_id', $buyer_id)
                ->where("Month(a.created_at)='" . date('m') . "'")
                ->get()
                ->num_rows();
            if ($count <= 0) $count = 1;

            $transaction_no = 'TRX.' . $buyer_id . '.' . date('m') . '.' . date('Y') . '.' . $count;
            $this->db->where('id', $transaction->id)
                ->update('shopping_cart', [
                    'purchase_order' => $transaction_no,
                ]);

            //change available budget
            $buyer = $this->db->where('a.id_user', $buyer_id)
                ->join('m_branch_code b', 'b.id=a.branch_code')
                ->get('sys_user a')
                ->row();
            if ($buyer != null) {
                $product = $this->db
                    ->where('id', $product_id)
                    ->get(App_Model::TBL_COMPANY_CATALOGUE)
                    ->row();

                $budget = $this->db
                    ->where('owner_code', $buyer->no_fund_center)
                    ->where('type', $order_type)
                    ->where('time', date('Y'))
                    ->get('m_budget')
                    ->row();

                if ($product != null && $budget != null) {
                    $this->db
                        ->where('id', $budget->id)
                        ->update('m_budget', [
                            'available' => ($budget->available - ($product->main_price * $qty))
                        ]);

                    $budget_data = [];
                    foreach ($budget as $key => $data) {
                        if ($key != 'id') $budget_data[$key] = $data;
                    }
                    $budget_data['transaction_id'] = $transaction_no;
                    $this->db->insert('m_budget_tracking', $budget_data);
                }
            }
        }



        add_notification($buyer_id, null, 3, 'Permintaan Pemesanan', 'Anda mendapatkan permintaan pemesanan baru', 'Internal', '#shopping/order_list');

        echo json_encode(['success' => $success]);
    }

    public function submit_approval($params=[])
    {
        $success = false;
        $id = $this->input->post('id');
        $status = isset($params['status'])?$params['status']:$this->input->post('status');
        $note = $this->input->post('note');

        $config['upload_path']          = self::PATH_UPLOAD_SHOPPING_FILE;
        $config['allowed_types']        = 'pdf|png|jpg|jpeg|rar|zip';
        $config['max_size']             = 51200;
        $config['remove_spaces']        = true;
        $config['encrypt_name']         = true;

        $file_do = $this->upload_company_attachment(true, 'file_do', $config);
        $file_gr = $this->upload_company_attachment(true, 'file_gr', $config);

        $this->db->trans_begin();
        for ($i = 0; $i < count($id); $i++) {

            $update_data = [
                'status' => $status[$i],
                'approval_note' => $note[$i],
                'verificator' => $this->session->userdata('user')['id_user']
            ];
            if ($status[$i] == '4') {
                $update_data['file_do'] = ($file_do['success']) ? $file_do['file_data']['file_name'] : null;
            } else if ($status[$i] == '5') {
                $update_data['file_gr'] = ($file_gr['success']) ? $file_gr['file_data']['file_name'] : null;
            }
            $this->db->where('id', $id[$i])
                ->update('shopping_cart', $update_data);
            $item = $this->db->where('id', $id[$i])
                ->get('shopping_cart')->row();

            $catalogue = $this->db->where('id', $item->product_id)->get('company_catalogue')->row();
            if ($status[$i] == 2) {
                if ($item != null) {
                    if ($catalogue != null) {
                        $company = $this->db->where('id', $catalogue->id_company)->get('company_profile')->row();
                        // $sap = $this->db->where('id_company', $company->id)->get('tbl_sync_sap')->row();

                        // if ($sap != null) {
                        //     $count = $this->db->select('a.*')
                        //         ->from('shopping_cart a')
                        //         ->join('company_catalogue b', 'b.id=a.product_id')
                        //         ->join('company_profile c', 'c.id=b.id_company')
                        //         ->where('c.id', $company->id)
                        //         ->where('( a.status=2 or a.status=4 or a.status=5 )')
                        //         ->get()
                        //         ->num_rows();

                        //     $this->db->where('id', $id[$i])
                        //         ->update('shopping_cart', [
                        //             'purchase_order' => 'TRX.' . $sap->id_sap . '.' . $count,
                        //         ]);
                        // }
                        add_notification($this->session->userdata('user')['id_user'], $company->id_user, null, 'Permintaan Pemesanan', 'Selamat!, Anda mendapatkan permintaan pemesanan baru', 'Internal', '#shopping/order_list');
                    }
                }
            }
            if ($item != null) {
                $status_name = '';
                switch ($status[$i]) {
                    case 1: {
                            $status_name = 'Menunggu Persetujuan';
                            break;
                        }
                    case 2: {
                            $status_name = 'Diterima GA/Proc Kantor Pusat';
                            break;
                        }
                    case 3: {
                            $status_name = 'Ditolak GA/Proc Kantor Pusat';
                            break;
                        }
                    case 4: {
                            $status_name = 'Diproses Vendor';
                            break;
                        }
                    case 5: {
                            $status_name = 'Diterima Pemesan';
                            break;
                        }
                    case 6: {
                            $status_name = 'Ditolak Vendor';
                            break;
                        }
                    case 7: {
                            $status_name = 'Pemesanan Selesai';
                            break;
                        }
                    case 8: {
                            $status_name = 'Ditolak Pemesan';
                            break;
                        }
                }

                if ($status[$i] == '6' || $status[$i] == '3') {
                    $shopping = $this->db->where('a.id', $id[$i])
                        ->join('company_catalogue b', 'b.id=a.product_id')
                        ->join('sys_user c', 'c.id_user=a.buyer_id')
                        ->join('m_branch_code d', 'd.id=c.branch_code')
                        ->get('shopping_cart a')->row();
                    if ($shopping != null) {
                        $budget = $this->db->where('type', $shopping->order_type)
                            ->where('time', date('Y'))
                            ->where('owner_code', $shopping->no_fund_center)
                            ->get('m_budget')->row();

                        if ($budget != null) {
                            $return_budget = $budget->available + ($shopping->qty * $shopping->main_price);
                            $this->db->where('id', $budget->id)
                                ->update('m_budget', ['available' => $return_budget]);
                        }
                    }
                }
                add_notification(
                    $this->session->userdata('user')['id_user'],
                    $item->buyer_id,
                    null,
                    'Konfirmasi Permintaan Pemesanan',
                    'Permintaan pemesanan ' . $catalogue->product_name . ', sebanyak ' . $item->qty . ', tgl ' . $item->created_at
                        . ' <b>' . $status_name . '</b>',
                    'Internal',
                    '#shopping/order_list'
                );
            }
        }
        if ($this->db->trans_status() !== FALSE) {
            $this->db->trans_commit();
            $success = true;
        } else {
            $this->db->trans_rollback();
            $success = false;
        }
        $this->db->trans_complete();

        if($status[0]==5){
            $this->submit_approval([
                'status'=>[7]
            ]);
        }
        else{

            echo json_encode([
                'success' => $success
            ]);
        }

    }

    public function get_order_list()
    {
        $buyer_id = $this->input->get('buyer_id');
        $status = $this->input->get('status');
        $branch = $this->input->get('branch');
        $id_company = $this->input->get('id_company');

        $role = $this->session->userdata('user')['id_usr_role'];
        if ($role == App_Model::ROLE_VENDOR || $role == App_Model::ROLE_VENDOR_PERSONAL || $role == App_Model::ROLE_VENDOR_GROUP) {
            $company = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                ->get(App_Model::TBL_COMPANY_PROFILE)->row();
            if ($company != null) $id_company = $company->id;
        }

        if ($buyer_id != null) $this->db->where('a.buyer_id', $buyer_id);
        if ($branch != null) $this->db->where('b.branch_code', $branch);
        if ($status != null) $this->db->where('a.status', $status);
        if ($id_company != null) $this->db->where('e.id', $id_company);

        $this->db->select('a.*
        ,b.name as user_name
        ,b.email as user_email
        ,b.phone as user_phone
        , c.name as branch_name,
        d.unit as product_unit,
        ,concat(if(e.prefix_name is null,"",concat(e.prefix_name," ") ),e.name) as company_name
        ,e.prefix_name as vendor_prefix_name
        ,e.name as vendor_name
        ,e.name as vendor_type
        ,f.id as vendor_type_id
        ,a.created_at as order_date
        ,a.status as order_status
        ,e.id_user as vendor_id_user
        ,a.approval_note as approval_notes
        ,a.purchase_order as no_po
        ,c.no_fund_center')
            ->from('shopping_cart a')
            ->join('sys_user b', 'a.buyer_id=b.id_user')
            ->join('m_branch_code c', 'b.branch_code=c.id')
            ->join('company_catalogue d', 'd.id=a.product_id')
            ->join('company_profile e', 'e.id=d.id_company')
            ->join('m_group f', 'f.id=e.id_group')
            ->where('a.deleted_at is null')
            ->order_by('a.created_at desc')
            ->get();

        $table = '(' . $this->db->last_query() . ') AS DATA';

        // Table's primary key
        $primaryKey = 'id';

        // Array of database columns which should be read and sent back to DataTables.
        // The `db` parameter represents the column name in the database, while the `dt`
        // parameter represents the DataTables column identifier. In this case simple
        // indexes
        $columns = array(
            array('db' => 'id', 'dt' => 0),
            array('db' => 'company_name',     'dt' => 1),
            array(
                'db' => 'product_name',
                'dt' => 2,
            ),
            array(
                'db' => 'main_price',
                'dt' => 3,
            ),
            array('db' => 'qty', 'dt' => 4),
            array(
                'db' => 'branch_name',
                'dt' => 5,
            ),
            array('db' => 'user_name',  'dt' => 6),
            array('db' => 'user_email',  'dt' => 7),
            array('db' => 'user_phone',  'dt' => 8),
            array('db' => 'created_at',  'dt' => 9),
            array('db' => 'vendor_type',  'dt' => 10),
            array('db' => 'vendor_type_id',  'dt' => 11),
            array('db' => 'vendor_prefix_name',  'dt' => 12),
            array('db' => 'vendor_name',  'dt' => 13),
            array('db' => 'product_unit',  'dt' => 14),
            array('db' => 'order_date',  'dt' => 15),
            array('db' => 'order_status',  'dt' => 16),
            array('db' => 'vendor_id_user',  'dt' => 17),
            array('db' => 'approval_notes',  'dt' => 18),
            array('db' => 'no_po',  'dt' => 19),
            array('db' => 'note',  'dt' => 20),
            array('db' => 'no_fund_center',  'dt' => 21),
            array('db' => 'file_do',  'dt' => 22),
            array('db' => 'file_gr',  'dt' => 23),
        );

        // SQL server connection information
        $sql_details = ssp_default_db();

        $sql = [];


        echo json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $sql));
    }

    public function order_list()
    {
        $this->set_page_title('pe-7s-user', 'Daftar Pemesanan', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Pemesanan'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Daftar Pemesanan'
            ]
        ]);
        $this->load->view('pages/shopping/order_list');
    }

    public function get_history()
    {
        $id = $this->input->get('id');

        echo json_encode(
            $this->db->select('a.*,b.email,c.role_name')
                ->where('a.order_id', $id)
                ->from('shopping_cart_history a')
                ->join('sys_user b', 'b.id_user=a.verificator')
                ->join('sys_usr_role c', 'b.id_usr_role=c.id_usr_role')
                ->order_by('created_at', 'desc')
                ->get()
                ->result()
        );
    }
}
