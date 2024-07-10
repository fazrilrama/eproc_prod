<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if($this->session->userdata('user')!=null && $this->session->userdata('user')['id_user']!=null ){
            redirect(site_url('/app'),'refresh');
        }
        else{
            template_view('pages/home/template', 'pages/home/index.php');   
        }
    }

    public function list_pengadaan()
    {
        template_view('pages/home/template', 'pages/home/list_pengadaan.php');
    }

    public function pengadaan()
    {
        $data['detail'] = $this->db
            ->select('project.*,(now()>end_date) as is_expired ')
            ->where('id', $this->uri->segment(3))
            ->order_by('start_date','desc')
            ->get('project')->row();
        template_view('pages/home/template', 'pages/home/pengadaan.php', $data);
    }

    private function set_page_title($icon = null, $label = null, $breadcumb = [])
	{
		$global_data = [
			'page_title_icon' => (($icon == null) ? 'pe-7s-home' : $icon),
			'page_title_label' => (($label == null) ? 'Dashboard' : $label),
			'page_title_breadcumb' => $breadcumb
		];

		$this->load->vars($global_data);
    }
    
    private function upload_company_attachment($isRequiredUpload = false, $field_name = 'attachment', $upload_config = null)
	{
		$file_data = null;
		$error_msg = null;
		$is_success = false;

		if (empty($_FILES[$field_name]['name']) && $isRequiredUpload == true) {
			$is_success = false;
			$error_msg = "You have to upload at least 1 file!";
			$file_data = ['file_name' => null];
		} else if (empty($_FILES[$field_name]['name']) && $isRequiredUpload == false) {
			$is_success = true;
			$error_msg = null;
			$file_data = ['file_name' => null];
		} else {
			if ($upload_config == null) {
				$config['upload_path']          = FCPATH . 'upload/home_content/';
				$config['allowed_types']        = 'png|jpg|jpeg';
				$config['max_size']             = 2048;
				$config['remove_spaces']        = true;
				$config['encrypt_name']         = true;
			} else {
				$config = $upload_config;
			}
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload($field_name)) {
				$is_success = false;
				$error_msg = $this->upload->display_errors();
			} else {
				$is_success = true;
				$file_data = $this->upload->data();
			}
		}

		return ['success' => $is_success, 'file_data' => $file_data, 'error' => $error_msg];
	}


    public function sliderManage()
    {   
        $table_name = 'home_slider';
        $table_fields = $this->db->get($table_name)->list_fields();
        $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at','id_company_owner'];
        $table_header = get_header($table_fields, $fields_exception, []);

        $this->set_page_title('pe-7s-user', 'Home Slider', [
            [
                'icon' => '<i class="fa fa-home"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Master Data'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Home Slider'
            ]
        ]);

        $dataKey = 'id';
        $data['header_title'] = 'Master - Home Slider';
        $data['table_header'] = $table_header['header_text'];
        $data['table_header_arr'] = implode(',', $table_header['header_id']);
        $data['get_url'] = 'master/get_data';
        $data['delete_url'] = 'master/delete_data';
        $data['update_url'] = 'home/editSlider';
        $data['add_url'] = 'home/addSlider';
        $data['data_key'] = $dataKey;
        $data['data_table'] = $table_name;


        $fields_exception1 = ['id', 'updated_at', 'deleted_at', 'created_at','id_company_owner'];
        // Form
        $form = form_builder($table_name, $fields_exception1, [], []);

        $form_inflated = [];
        foreach ($form as $f) {
            if ($f['input_field']['attr']['id'] == 'image') {
                $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="image" name="image" class="form-control" 
                data-validation="mime size"
                data-validation-max-size="2M"
                data-validation-allowing="png, jpeg, jpg"  />
                <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
            }
            $form_inflated[] = $f;
        }
        $data['render_column_modifier'] = '{
            image:{
                render: function(val){
                    var view=``;
                    var imgURL=`'.base_url('upload/home_content').'/${val.image}`;
                    if(val.image.includes("http")) imgURL=val.image;
                    view=`<img src="${imgURL}" style="width:150px;height:80px;object-fit:contain;"></img>`;
                    return view;
                }
            }
        }';

        $data['add_scripts'] = [];

        $data['form'] = $form_inflated;

        //ServerSide
        $data['ssp'] = 'true';
        $data['ssp_url'] = 'home/getSlider';

        $this->load->view('pages/master/master_view', $data);
    }

    public function addSlider(){
        $isAttachmentRequird=true;
        $do_upload = $this->upload_company_attachment($isAttachmentRequird,'image');
        $result = [
            'sucesss' => false,
            'result' => 'Add data failed!'
        ];
        if ($do_upload['success']) {
            if (add_data(null, ['image' => $do_upload['file_data']['file_name']])['success']) {
                $result['success'] = true;
                $result['result'] = 'Add data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }

    public function editSlider(){
        $isAttachmentRequird=false;
        $do_upload = $this->upload_company_attachment($isAttachmentRequird,'image');
        $result = [
            'success' => false,
            'result' => 'Edit data failed!'
        ];
        if ($do_upload['success']) {
            if (edit_data(null, ['image' => $do_upload['file_data']['file_name']])['success']) {
                $result['success'] = true;
                $result['result'] = 'Edit data success!';
            }
        } else {
            $result['result'] = $do_upload['error'];
        }

        echo json_encode($result);
    }
    
    public function getSlider()
    {
        $offset = $this->input->get('start');
        $limit = $this->input->get('length');
        $search = $this->input->get('search[value]');
        $data = $this->db
            ->select('*')
            ->where('deleted_at is null');
        if ($search != null) {
            $data->group_start();
            $data->or_like('lower(subtitle)', strtolower($search), 'both');
            $data->or_like('lower(title)', strtolower($search), 'both');
            $data->group_end();
        }
        $totalData = $data
            ->get('home_slider')->num_rows();
        $params = [];
        $addSql = "";
        $addSql .= " ORDER BY sort,created_at asc";
        if ($limit != null && $offset != null) {
            $params[] = (int)$limit;
            $params[] = (int)$offset;
            $addSql .= ' LIMIT ? OFFSET ?';
        }
        $data = $this->db->query($this->db->last_query() . ' ' . $addSql, $params)->result();
        echo json_encode([
            'recordsFiltered' => $totalData,
            'recordsTotal' => $totalData,
            'data' => $data
        ]);
    }

    public function getKecamatan(){
        $idKab=$this->input->get('idKab');
        $data=$this->db
        ->where('id_kab',$idKab)
        ->order_by('nama','asc')
        ->get('m_kecamatan')->result();
        echo json_encode($data);
    }

    public function getCity(){
        $idProv=$this->input->get('idProv');
        $data=$this->db
        ->where('province_id',$idProv)
        ->order_by('name','asc')
        ->get('m_city')->result();
        echo json_encode($data);
    }

    public function addMitra(){
        $res=[
            'success'=>false,
            'message'=>'Something went wrong, please try again!'
        ];
        $this->form_validation->set_rules('bidang_usaha','Jenis Usaha','required');
        $this->form_validation->set_rules('id_usr_role','Jenis Perusahaan','required');
        $this->form_validation->set_rules('name','Nama','required');
        $this->form_validation->set_rules('no_hp','Telepon','required');
        $this->form_validation->set_rules('id_province','Provinsi','required');
        $this->form_validation->set_rules('id_city','Kab/Kota','required');
        $this->form_validation->set_rules('id_kec','Kecamatan','required');
        $this->form_validation->set_rules('address','Alamat','required');
        $this->form_validation->set_rules('latitude','Latitude','required');
        $this->form_validation->set_rules('longitude','Longitude','required');
        $this->form_validation->set_rules('kode_perusahaan','Longitude','required');
        $this->form_validation->set_rules('password','Kata Sandi','trim|required|regex_match[/'.REGREX_COLLETION['STRENGTH_PASS'].'/]');
        $this->form_validation->set_rules('password_confirmation','Konfirmasi Kata Sandi','trim|required|matches[password]');

        if($this->form_validation->run()!==FALSE){

            //create user
            $this->db->trans_begin();
            $dataUser=[
                'id_usr_status'=>6,
                'id_usr_role'=>$this->input->post('id_usr_role'),
                'name'=>$this->input->post('name'),
                'email'=>$this->input->post('no_hp'),
                'phone'=>$this->input->post('no_hp'),
                'username'=>$this->input->post('no_hp'),
                'id_company_owner'=>$this->input->post('kode_perusahaan'),
                'password'=>password_hash($this->input->post('password'),PASSWORD_DEFAULT,['cost'=>10])
            ];
            $this->db->insert('sys_user',$dataUser);
            $userID=$this->db->insert_id();

            // create company
            $dataComp=[
                'id_user'=>$userID,
                'id_company_owner'=>$this->input->post('kode_perusahaan'),
                'name'=>$this->input->post('name'),
                'id_group'=>4
            ];
            $this->db->insert('company_profile',$dataComp);
            $compID=$this->db->limit(1)->order_by('created_at','asc')->get('company_profile')->row()->id;
            
            // create contact
            $dataContact=[
                'id_company'=>$compID,
                'address'=>$this->input->post('address'),
                'latitude'=>$this->input->post('latitude'),
                'longitude'=>$this->input->post('longitude'),
                'id_city'=>$this->input->post('id_city'),
                'id_kec'=>$this->input->post('id_kec'),
                'id_country_province'=>$this->input->post('id_province'),
                'phone'=>$this->input->post('no_hp'),
            ];
            $this->db->insert('company_contact',$dataContact);

            // create bank
            $dataBank=[
                'id_company'=>$compID,
                'owner'=>$this->input->post('name'),
                'bank_name'=>'MANDIRI',
                'no'=>'00000000000000',
            ];
            $this->db->insert('company_finance_bank',$dataBank);

            // create npwp
            $dataNPWP=[
                'id_company'=>$compID,
                'no'=>'00000000000000',
            ];
            $this->db->insert('company_legal_npwp',$dataNPWP);

            // create pic
            $dataPIC=[
                'id_company'=>$compID,
                'name'=>$this->input->post('name'),
                'position'=>'-',
                'mobile_phone'=>$this->input->post('no_hp'),
            ];
            $this->db->insert('company_pic',$dataPIC);

            // create type
            $dataType=[
                'id_company'=>$compID,
                'id_company_type'=>$this->input->post('bidang_usaha'),
            ];
            $this->db->insert('company_type',$dataType);

            // create type
            $dataWorkArea=[
                'id_company'=>$compID,
                'id_city'=>$this->input->post('id_city'),
            ];
            $this->db->insert('company_work_area',$dataWorkArea);

            $this->db->trans_complete();
            if($this->db->trans_status()){
                $this->db->trans_commit();
                $res['success']=true;
                $res['message']='Terima kasih, data Anda akan kami tinjau terlebih dahulu.';
            }
            else{
                $this->db->trans_rollback();
                $res['message']='Terima kasih, data Anda akan kami tinjau terlebih dahulu.';
            }

        }
        else{
            $res['message']=validation_errors();
        }

        echo json_encode($res);
    }

}
