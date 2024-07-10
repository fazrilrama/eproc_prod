<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends App_Controller
{
    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'blank'],
                ['method' => 'get_most_trx'],
                ['method' => 'not_found'],
                ['method' => 'unauthorized'],
                ['method' => 'index'],
                ['method' => 'getManualBook'],
            ]
        ]);
    }

    public function getManualBook(){
        $src=$this->input->get('s');
        echo '
        <div class="row">
            <div class="col-md-12">
                <iframe src="'.base_url($src).'" style="width:100%;height:100vh;"></iframe>
            </div>
        </div>';
    }

    public function get_most_trx()
    {
        $length = $this->input->post('fmost_trx_length');
        $start_date = $this->input->post('fmost_trx_date_start');
        $end_date = $this->input->post('fmost_trx_date_end');
        $branch = $this->input->post('fmost_trx_branch');
        $params = [];

        // $filter = "
        //         SELECT COUNT(*)
        //         FROM shopping_cart cart
        //         INNER JOIN company_catalogue product ON product.id=cart.product_id
        //         INNER JOIN m_company_sub_competency sub_comp ON sub_comp.id=product.id_sub_competencies
        //         WHERE cart.`status`=7
        //         AND sub_comp.id=sub.id
        //         AND ( MONTH(cart.created_at)>=? AND MONTH(cart.created_at)<=? AND YEAR(cart.created_at)>=? AND YEAR(cart.created_at)<=? )
        //         ";
        // if ($branch != null) $filter .= " AND (SELECT branch_code FROM sys_user buyer WHERE cart.buyer_id=buyer.id_user) = ?";

        // $sql = "SELECT * FROM ( SELECT id,`name`, ($filter) as count_trx 
        //     FROM m_company_sub_competency sub
        //     WHERE
        //     (
        //         $filter
        //     ) > 0

        //     ) as sub_comp
        //     ORDER BY count_trx desc
        //     LIMIT ?
        //     ";

        $filter = " AND ( MONTH(cart.created_at)>=? AND MONTH(cart.created_at)<=? AND YEAR(cart.created_at)>=? AND YEAR(cart.created_at)<=? )";
        if ($branch != null) $filter .= " AND (SELECT branch_code FROM sys_user buyer WHERE cart.buyer_id=buyer.id_user) = ?";

        $sql = "SELECT product.id
        ,product.product_name
        ,comp.`name` as comp
        ,sub_comp.`name` as sub_comp_name
        ,(SELECT CONCAT( if(prefix_name is null,'',concat(prefix_name,' ') ),name) FROM company_profile WHERE id=product.id_company) as vendor_name 
        ,(SELECT count(*) FROM shopping_cart WHERE product_id=cart.product_id) as count_trx from shopping_cart cart
        INNER JOIN company_catalogue product ON product.id=cart.product_id
        INNER JOIN m_company_sub_competency sub_comp ON sub_comp.id=product.id_sub_competencies
        INNER JOIN m_company_competency comp ON comp.id=sub_comp.id_company_competency
        WHERE cart.`status`=7
        $filter
        AND (SELECT count(*) FROM shopping_cart WHERE product_id=cart.product_id)>0
        GROUP BY cart.product_id
        ORDER BY (SELECT count(*) FROM shopping_cart WHERE product_id=cart.product_id
        $filter) DESC
        LIMIT ?";

        $params[] =  (int) explode("-", $start_date)[1];
        $params[] = (int) explode("-", $end_date)[1];
        $params[] =  (int) explode("-", $start_date)[0];
        $params[] = (int) explode("-", $end_date)[0];
        if ($branch != null) {
            $params[] = (int) $branch;
        }

        $params[] =  (int) explode("-", $start_date)[1];
        $params[] = (int) explode("-", $end_date)[1];
        $params[] =  (int) explode("-", $start_date)[0];
        $params[] = (int) explode("-", $end_date)[0];
        if ($branch != null) {
            $params[] = (int) $branch;
        }


        $params[] = (int) $length;

        $data = $this->db->query($sql, $params)->result();
        echo json_encode($data);
    }

    public function index()
    {
        $this->set_page_title('pe-7s-home', 'Dashboard', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'label' => ' Dashboard'
            ]
        ]);

        $view = 'pages/dashboard/administrator';
        switch ($this->session->userdata('user')['id_usr_role']) {
            case App_Model::ROLE_VENDOR: {
                    $view = 'pages/dashboard/administrator_vendor';
                    break;
                }
            case App_Model::ROLE_VENDOR_PERSONAL: {
                    $view = 'pages/dashboard/administrator_vendor';
                    break;
                }
            case App_Model::ROLE_VERIFICATOR: {
                    $view = 'pages/dashboard/administrator_verificator';
                    break;
                }
            case App_Model::ROLE_ADMIN: {
                    $view = 'pages/dashboard/administrator_admin';
                    break;
                }
            case 13:{
                $view = 'pages/dashboard/operasional';
                break;
            }
            default: {
                    $view = 'pages/dashboard/customer';
                }
        }

        return $this->load->view($view);
    }

    public function not_found()
    {
        return $this->load->view('templates/dashboard/not_found');
    }

    public function unauthorized()
    {
        $this->load->view('templates/dashboard/unauthorized');
    }

    public function blank()
    {
        $this->load->view('pages/dashboard/blank');
    }
}
