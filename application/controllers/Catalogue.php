<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Catalogue extends App_Controller
{

    public function __construct()
    {
        parent::__construct([
            'exclude_menu_check' => [
                ['method' => 'add_data_catalogue', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'edit_data_catalogue', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'delete_data_catalogue', 'id_role' => [1, 2, 3, 6, 7]],
                ['method' => 'get_data_catalogue'],
                ['method' => 'get_data_catalogue_ssp'],
                ['method' => 'search'],
                ['method' => 'get_comptencies_by_company'],
                ['method' => 'get_budget'],
            ]
        ]);

        $this->load->model('Catalogue_model', 'catalogue');
        $this->load->model('Company_model', 'company');
    }


    public function get_budget()
    {
        $owner = $this->input->get('owner');
        $id_user = $this->input->get('id_user');
        $interval = $this->input->get('interval');
        $time = $this->input->get('time');
        $params = [];

        if ($owner != null) $params['owner_code'] = $owner;
        if ($interval != null) $params['interval'] = $interval;
        if ($time != null) $params['time'] = $time;
        if ($id_user != null) $params['owner_code'] = $this->db->where('a.id_user', $id_user)
            ->join('m_branch_code b', 'b.id=a.branch_code')
            ->get('sys_user a')
            ->row()->no_fund_center;
        echo json_encode($this->catalogue->get_budget($params));
    }

    public function get_data_catalogue()
    {
        $id = $this->input->get('id');
        $id_company = $this->input->get('id_company');
        echo json_encode($this->catalogue->get_data($id, $id_company)->get()->result());
    }


    public function get_data_catalogue_ssp()
    {
        $id = $this->input->get('id');
        $id_company = $this->input->get('id_company');
        $sqlWhere = [];
        if ($id != null) array_push($sqlWhere, "id='$id'");
        if ($id_company != null) array_push($sqlWhere, "id_company='$id_company'");
        echo json_encode($this->catalogue->get_data_ssp($sqlWhere));
    }

    public function add_data_catalogue()
    {
        $upload_config = [];
        $upload_config['upload_path']          = self::PATH_UPLOAD_COMPANY_FILE;
        $upload_config['allowed_types']        = 'png|jpg|jpeg';
        $upload_config['max_size']             = 2048;
        $upload_config['remove_spaces']        = true;
        $upload_config['encrypt_name']         = true;
        $picture1 = $this->upload_company_attachment(true, 'picture1', $upload_config);
        $picture2 = $this->upload_company_attachment(false, 'picture2', $upload_config);
        $picture3 = $this->upload_company_attachment(false, 'picture3', $upload_config);
        $picture4 = $this->upload_company_attachment(false, 'picture4', $upload_config);
        $picture5 = $this->upload_company_attachment(false, 'picture5', $upload_config);
        $guarantee_file = $this->upload_company_attachment(false, 'guarantee_file', $upload_config);

        echo json_encode(add_data(null, [
            'picture1' => $picture1['file_data']['file_name'],
            'picture2' => $picture2['file_data']['file_name'],
            'picture3' => $picture3['file_data']['file_name'],
            'picture4' => $picture4['file_data']['file_name'],
            'picture5' => $picture5['file_data']['file_name'],
            'guarantee_file' => $guarantee_file['file_data']['file_name'],
        ]));
    }
    public function edit_data_catalogue()
    {
        $upload_config = [];
        $upload_config['upload_path']          = self::PATH_UPLOAD_COMPANY_FILE;
        $upload_config['allowed_types']        = 'png|jpg|jpeg';
        $upload_config['max_size']             = 2048;
        $upload_config['remove_spaces']        = true;
        $upload_config['encrypt_name']         = true;
        $picture1 = $this->upload_company_attachment(true, 'picture1', $upload_config);
        $picture2 = $this->upload_company_attachment(false, 'picture2', $upload_config);
        $picture3 = $this->upload_company_attachment(false, 'picture3', $upload_config);
        $picture4 = $this->upload_company_attachment(false, 'picture4', $upload_config);
        $picture5 = $this->upload_company_attachment(false, 'picture5', $upload_config);
        $guarantee_file = $this->upload_company_attachment(false, 'guarantee_file', $upload_config);

        echo json_encode(edit_data(null, [
            'picture1' => $picture1['file_data']['file_name'],
            'picture2' => $picture2['file_data']['file_name'],
            'picture3' => $picture3['file_data']['file_name'],
            'picture4' => $picture4['file_data']['file_name'],
            'picture5' => $picture5['file_data']['file_name'],
            'guarantee_file' => $guarantee_file['file_data']['file_name'],
        ]));
    }

    public function delete_data_catalogue()
    {
        $catalogue = $this->db->where('id', $this->input->post('id'))
            ->get(App_Model::TBL_COMPANY_CATALOGUE)->row();
        if ($catalogue != null) {
            if ($catalogue->picture1 != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->picture1);
            if ($catalogue->picture2 != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->picture2);
            if ($catalogue->picture3 != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->picture3);
            if ($catalogue->picture4 != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->picture4);
            if ($catalogue->picture5 != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->picture5);
            if ($catalogue->guarantee_file != null) unlink(self::PATH_UPLOAD_COMPANY_FILE . $catalogue->guarantee_file);
        }
        echo json_encode(delete_data());
    }

    public function get_competencies_by_company()
    {
        $id_company = $this->input->get('id_company');
        $this->db->where('a.id_company', $id_company);

        echo json_encode($this->db->select('a.*,d.name as competency_name,c.name as sub_competency_name')
            ->from(App_Model::TBL_COMPANY_COMPETENCIES . ' a')
            ->join(App_Model::TBL_COMPANY_PROFILE . ' b', 'a.id_company=b.id')
            ->join(App_Model::TBL_COMPANY_SUB_COMPETENCY . ' c', 'a.id_company_sub_competency=c.id')
            ->join(App_Model::TBL_COMPANY_COMPETENCY . ' d', 'c.id_company_competency=d.id')
            ->get()
            ->result());
    }

    public function manage()
    {
        $id_user = $this->session->userdata('user')['id_user'];
        $company = $this->company->get_profile($id_user)
            ->get()->row();
        if ($company != null || $this->is_as_admin()) {

            $table_name = App_Model::TBL_COMPANY_CATALOGUE;
            if ($company != null) {
                $table_fields = $this->catalogue->get_data()->get()->list_fields();
            } else {
                $table_fields = $this->catalogue->get_data()->get()->list_fields();
            }
            $fields_exception = ['unit', 'max_order', 'id', 'dimension_long', 'dimension_width', 'dimension_height', 'active_end_date', 'price_after_discount', 'final_price', 'picture2', 'picture3', 'picture4', 'picture5', 'id_company', 'description', 'id_sub_competencies', 'updated_at', 'deleted_at', 'created_at'];
            $table_header = get_header($table_fields, $fields_exception, [
                'company_name' => [
                    'text' => 'Perusahaan'
                ],
                'product_code' => [
                    'text' => 'Kode'
                ],
                'product_name' => [
                    'text' => 'Nama'
                ],
                'product_brand' => [
                    'text' => 'Merek'
                ],
                'main_price' => [
                    'text' => 'Harga'
                ],
                'is_negotiable' => [
                    'text' => 'Negosiasi'
                ],
                'price_after_discount' => [
                    'text' => 'Harga Setelah Diskon'
                ],
                'final_price' => [
                    'text' => 'Harga Final'
                ],
                'unit' => [
                    'text' => 'Satuan'
                ],
                'min_order' => [
                    'text' => 'Min/Maks Pesanan'
                ],
                'max_order' => [
                    'text' => 'Maks Pesan'
                ],
                'product_weight' => [
                    'text' => 'Dimensi'
                ],
                'dimension_long' => [
                    'text' => 'Panjang (cm)'
                ],
                'dimension_width' => [
                    'text' => 'Lebar (cm)'
                ],
                'dimension_height' => [
                    'text' => 'Tinggi (cm)'
                ],
                'active_start_date' => [
                    'text' => 'Tgl Aktif'
                ],
                'picture1' => [
                    'text' => 'Sampel Gambar'
                ],
            ]);

            $this->set_page_title('pe-7s-user', 'Katalog Perusahaan', [
                [
                    'icon' => '<i class="fa fa-home"></i>',
                    'link' => '#dashboard',
                    'label' => ''
                ],
                [
                    'label' => 'Perusahaan'
                ],
                [
                    'icon' => '',
                    'active' => true,
                    'label' => ' Katalog'
                ]
            ]);

            $dataKey = 'id';
            $data['header_title'] = 'Perusahaan - Katalog';
            $data['table_header'] = $table_header['header_text'];
            $data['table_header_arr'] = implode(',', $table_header['header_id']);
            $data['get_url'] = 'catalogue/get_data_catalogue';
            if ($company != null) $data['get_url'] .= '?id_company=' . $company->id;
            $data['delete_url'] = 'catalogue/delete_data_catalogue';
            $data['update_url'] = 'catalogue/edit_data_catalogue';
            $data['add_url'] = 'catalogue/add_data_catalogue';
            $data['data_key'] = $dataKey;
            $data['data_table'] = $table_name;

            // Form

            $fields_exception = ['id', 'updated_at', 'deleted_at', 'created_at'];

            $form_inflated = [];
            $form = form_builder(
                $table_name,
                $fields_exception,
                [
                    'product_code' => [
                        'data-validation' => 'required length',
                        'data-validation-length' => 'min3',
                        'placeholder' => 'Kode Produk'
                    ],
                    'product_name' => [
                        'data-validation' => 'required length',
                        'data-validation-length' => 'min3',
                        'placeholder' => 'Nama Produk'
                    ],
                    'product_brand' => [
                        'data-validation' => 'required length',
                        'data-validation-length' => 'min3',
                        'placeholder' => 'Merek Produk'
                    ],
                    'main_price' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Harga Utama',
                    ],
                    'final_price' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Harga Final'
                    ],
                    'price_after_discount' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Harga Setelah Diskon'
                    ],
                    'unit' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Satuan Produk'
                    ],
                    'min_order' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Minimal Pesanan'
                    ],
                    'max_order' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Maksimal Pesanan'
                    ],
                    'product_weight' => [
                        'data-validation' => 'required',
                        'placeholder' => 'Berat Produk'
                    ],
                    'dimension_long' => [
                        'placeholder' => 'Dimensi Panjang (cm)'
                    ],
                    'dimension_width' => [
                        'placeholder' => 'Dimensi Lebar (cm)'
                    ],
                    'dimension_height' => [
                        'placeholder' => 'Dimensi Tinggi (cm)'
                    ],
                    'active_start_date' => [
                        'data-validation' => 'required',
                    ],
                    'active_end_date' => [
                        'data-validation' => 'required',
                    ],
                ],
                [
                    'product_code' => [
                        'text' => 'Kode Produk<span style="color:red;">*</span>'
                    ],
                    'product_name' => [
                        'text' => 'Nama Produk<span style="color:red;">*</span>'
                    ],
                    'product_name' => [
                        'text' => 'Nama Produk<span style="color:red;">*</span>'
                    ],
                    'product_brand' => [
                        'text' => 'Merek Produk<span style="color:red;">*</span>'
                    ],
                    'main_price' => [
                        'text' => 'Harga Utama<span style="color:red;">*</span>'
                    ],
                    'price_after_discount' => [
                        'text' => 'Harga Setelah Diskon<span style="color:red;">*</span>'
                    ],
                    'unit' => [
                        'text' => 'Satuan Produk<span style="color:red;">*</span>'
                    ],
                    'min_order' => [
                        'text' => 'Minimal Pesanan<span style="color:red;">*</span>'
                    ],
                    'max_order' => [
                        'text' => 'Maksimal Pesanan<span style="color:red;">*</span>'
                    ],
                    'product_weight' => [
                        'text' => 'Berat Produk<span style="color:red;">*</span>'
                    ],
                    'dimension_long' => [
                        'text' => 'Dimensi Panjang (cm)'
                    ],
                    'dimension_width' => [
                        'text' => 'Dimensi Lebar (cm)'
                    ],
                    'dimension_height' => [
                        'text' => 'Dimensi Tinggi (cm)'
                    ],
                    'active_start_date' => [
                        'text' => 'Tgl Mulai Aktif<span style="color:red;">*</span>',
                    ],
                    'active_end_date' => [
                        'text' => 'Tgl Akhir Aktif<span style="color:red;">*</span>',
                    ],
                    'is_negotiable' => [
                        'text' => 'Dapat Di Negosiasi<span style="color:red;">*</span>',
                    ],
                    'final_price' => [
                        'text' => 'Harga Final<span style="color:red;">*</span>',
                    ],
                    'id_sub_competencies' => [
                        'text' => 'Kategori Kompetensi<span style="color:red;">*</span>',
                    ],
                    'picture1' => [
                        'text' => 'Sampel Gambar 1<span style="color:red;">*</span>',
                    ],
                    'picture2' => [
                        'text' => 'Sampel Gambar 2',
                    ],
                    'picture3' => [
                        'text' => 'Sampel Gambar 3',
                    ],
                    'picture4' => [
                        'text' => 'Sampel Gambar 4',
                    ],
                    'picture5' => [
                        'text' => 'Sampel Gambar 5',
                    ],
                    'id_company' => [
                        'text' => 'Perusahaan'
                    ]
                ],
                [
                    'dimension_long' => '<span style="color:red;">Satuan dalam (cm)</span>',
                    'dimension_width' => '<span style="color:red;">Satuan dalam (cm)</span>',
                    'dimension_height' => '<span style="color:red;">Satuan dalam (cm)</span>',
                    'unit' => '<span style="color:red;">Contoh Hari,Buah,Lusin,dll</span>'
                ]
            );

            $i = 0;
            foreach ($form as $f) {
                if ($f['input_field']['attr']['id'] == 'id_company') {
                    if ($company != null) {
                        $o = $company;
                        $f['input_field']['html'] = '<input hidden id="id_company" name="id_company" class="form-control"
                        value="' . $o->id . '" />
                        <input disabled class="form-control"
                        value="' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '" />';
                    } else {

                        $f['input_field']['html'] = '<select type="select" id="id_company" name="id_company" class="form-control" data-validation="required"
                        data-validation-error-msg="Perusahaan tidak valid!" >';
                        $data_opt = $this->company->get(null, null, [], [
                            '( c.id_usr_role=' . App_Model::ROLE_VENDOR
                                . ' or c.id_usr_role=' . App_Model::ROLE_VENDOR_PERSONAL
                                . ' or c.id_usr_role=' . App_Model::ROLE_VENDOR_GROUP . ' ) '
                        ]);
                        $opt = '<option value="">Pilih</option>';
                        foreach ($data_opt as $o) {
                            $opt .= '<option value="' . $o->id . '">' . $o->prefix_name . ' ' . $o->name. ' | ' . $o->user_email . '</option>';
                        }
                        $f['input_field']['html'] .= $opt . '</select>';
                    }
                }

                if ($f['input_field']['attr']['id'] == 'id_sub_competencies') {
                    $f['input_field']['html'] = '<select type="select" id="id_sub_competencies" name="id_sub_competencies" class="form-control select2" data-validation="required">
                    <option value="">Pilih</option>
                    </select>';
                }

                if ($f['input_field']['attr']['id'] == 'is_negotiable') {
                    $f['input_field']['html'] = '<select type="select" id="is_negotiable" name="is_negotiable" class="form-control" data-validation="required"
                         >';
                    $opt = '<option value="">Pilih</option>
                    <option value="1">Ya</option>
                    <option value="0">Tidak</option>';
                    $f['input_field']['html'] .= $opt . '</select>';
                }

                if ($f['input_field']['attr']['id'] == 'description') {
                    $f['input_field']['html'] = '<textarea type="textarea" id="description" name="description" class="form-control text-editor-master-full" data-validation="required"></textarea>';
                }

                if ($f['input_field']['attr']['id'] == 'picture1') {
                    $f['input_field']['html'] = '<input is-mandatory="true" type="file" id="picture1" name="picture1" class="form-control" 
                    data-validation="required mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="png, jpeg, jpg"  />
                    <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
                }
                if ($f['input_field']['attr']['id'] == 'picture2') {
                    $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="picture2" name="picture2" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="png, jpeg, jpg"  />
                    <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
                }

                if ($f['input_field']['attr']['id'] == 'picture3') {
                    $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="picture3" name="picture3" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="png, jpeg, jpg"  />
                    <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
                }

                if ($f['input_field']['attr']['id'] == 'picture4') {
                    $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="picture4" name="picture4" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="png, jpeg, jpg"  />
                    <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
                }


                if ($f['input_field']['attr']['id'] == 'picture5') {
                    $f['input_field']['html'] = '<input is-mandatory="false" type="file" id="picture5" name="picture5" class="form-control" 
                    data-validation="mime size"
                    data-validation-max-size="2M"
                    data-validation-allowing="png, jpeg, jpg"  />
                    <span style="color:red;">File png,jpeg,jpg, Maksimal 2MB</span>';
                }


                $form_inflated[$i] = $f;
                $i++;
            }

            $data['form'] = $form_inflated;

            $data['add_scripts'] = [
                base_url('assets/js/page/catalogue_manage.js')
            ];

            $data['render_column_modifier'] = '{
                picture1:{
                    render:"<img style=\"border-radius:50px; height:80px;width:80px;\" target=\"_blank\" src=\"' . base_url('/upload/company/file/{val}') . '\" />"
                },
                picture2:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                picture3:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                picture4:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                picture5:{
                    render:"<a target=\"_blank\" href=\"' . base_url('/upload/company/file/{val}') . '\"><i class=\"fa fa-download\"></i></a>"
                },
                is_negotiable:{
                    render:val=>(val.is_negotiable==1?"Ya":"Tidak")
                },
                min_order:{
                    render:val=>"Min : "+val.min_order+" "+val.unit+" , Maks: "+val.max_order+" "+val.unit
                }
                ,main_price:{
                    render:val=>"Rp"+val.main_price.replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")
                }
                ,active_start_date:{
                    render:val=>moment(val.active_start_date,"YYYY-MM-DD").format("D MMM Y")+" s/d "+moment(val.active_end_date,"YYYY-MM-DD").format("D MMM Y")
                }
                ,product_weight:{
                    render:val=>"Berat : "+val.product_weight+"<br/>"+"PxLxT (cm) : "+val.dimension_long+","+val.dimension_width+","+val.dimension_height+"<br/>"
                }
            }';
            $this->load->view('pages/master/master_view', $data);
        } else {
            echo "<script>
            alert('Anda harus membuat profil perusahaan terlebih dahulu!');
            window.location.href='" . site_url('app#profile/company') . "';
            location.reload();
            </script>";
        }
    }

    public function index()
    {
        $this->set_page_title('pe-7s-user', 'Katalog Perusahaan', [
            [
                'icon' => '<i class="fa fa-shopback"></i>',
                'link' => '#dashboard',
                'label' => ''
            ],
            [
                'label' => 'Perusahaan'
            ],
            [
                'icon' => '',
                'active' => true,
                'label' => ' Katalog'
            ]
        ]);
        $this->load->view('pages/catalogue/main');
    }


    public function search()
    {
        $result = [
            'success' => true,
            'total_data' => 0,
            'result' => []
        ];

        $search_term = $this->input->post('search_term');
        $f_group = $this->input->post('group');
        $f_competency = $this->input->post('competency');
        $f_sub_competency = $this->input->post('sub_competency');
        $f_lokasi = $this->input->post('f_lokasi');
        $f_company_owner = $this->input->post('f_company_owner');

        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');

        $data = $this->catalogue->get_search();

        if ($f_group != null) $data->where('c.id_group', $f_group);
        if ($f_competency != null) $data->where('d.id', $f_competency);
        if ($f_sub_competency != null) $data->where('c.id', $f_sub_competency);
        if ($f_company_owner != null) $data->where('b.id_company_owner', $f_company_owner);
        if ($f_lokasi != null) $data->where($f_lokasi . ' in ( select m_city.id from m_city inner join company_work_area on company_work_area.id_city=m_city.id WHERE company_work_area.id_company=a.id_company ) ');

        if ($search_term != null) {
            $search_term = strtolower($search_term);
            $data->where("(
            LOWER(b.prefix_name) like '%$search_term%'
            or LOWER(b.name) like '%$search_term%'
            or LOWER(a.product_code) like '%$search_term%'
            or LOWER(a.product_name) like '%$search_term%'
            or LOWER(a.product_name) like '%$search_term%'
            or LOWER(a.product_brand) like '%$search_term%'
            or LOWER(a.main_price) like '%$search_term%'
            or LOWER(a.price_after_discount) like '%$search_term%'
            or LOWER(a.final_price) like '%$search_term%'
            or LOWER(a.unit) like '%$search_term%'
            or LOWER(b.description) like '%$search_term%'
            or LOWER(c.name) like '%$search_term%'
            or LOWER(c.description) like '%$search_term%'
            or LOWER(d.name) like '%$search_term%'
            or LOWER(d.description) like '%$search_term%'
            or LOWER( (select GROUP_CONCAT(m_city.name) from m_city inner join company_work_area on company_work_area.id_city=m_city.id WHERE company_work_area.id_company=a.id_company) ) like '%$search_term%'
            )");
        }

        $data->where("a.active_start_date <= '" . date('Y-m-d') . "' AND " . "a.active_end_date >= '" . date('Y-m-d') . "'");

        $data = $data->limit($limit)->offset($offset)->get()->result();
        $result['result'] = $data;
        $result['total_data'] = $this->count_search();
        echo json_encode($result);
    }

    private function count_search()
    {
        $search_term = $this->input->post('search_term');
        $f_group = $this->input->post('group');
        $f_competency = $this->input->post('competency');
        $f_sub_competency = $this->input->post('sub_competency');
        $f_company_owner = $this->input->post('f_company_owner');
        $f_lokasi = $this->input->post('f_lokasi');

        $limit = $this->input->post('limit');
        $offset = $this->input->post('offset');

        $data = $this->catalogue->get_search_count();

        if ($f_group != null) $data->where('c.id_group', $f_group);
        if ($f_competency != null) $data->where('d.id', $f_competency);
        if ($f_sub_competency != null) $data->where('c.id', $f_sub_competency);
        if ($f_company_owner != null) $data->where('b.id_company_owner', $f_company_owner);
        if ($f_lokasi != null) $data->where($f_lokasi . ' in ( select m_city.id from m_city inner join company_work_area on company_work_area.id_city=m_city.id WHERE company_work_area.id_company=a.id_company ) ');

        if ($search_term != null) {
            $search_term = strtolower($search_term);
            $data->where("(
                LOWER (b.prefix_name) like '%$search_term%'
                or LOWER(b.name) like '%$search_term%'
                or LOWER(a.product_code) like '%$search_term%'
            or LOWER(a.product_name) like '%$search_term%'
            or LOWER(a.product_name) like '%$search_term%'
            or LOWER(a.product_brand) like '%$search_term%'
            or LOWER(a.main_price) like '%$search_term%'
            or LOWER(a.price_after_discount) like '%$search_term%'
            or LOWER(a.final_price) like '%$search_term%'
            or LOWER(a.unit) like '%$search_term%'
            or LOWER(b.description) like '%$search_term%'
            or LOWER(c.name) like '%$search_term%'
            or LOWER(c.description) like '%$search_term%'
            or LOWER(d.name) like '%$search_term%'
            or LOWER(d.description) like '%$search_term%'
            or LOWER( (select GROUP_CONCAT(m_city.name) from m_city inner join company_work_area on company_work_area.id_city=m_city.id WHERE company_work_area.id_company=a.id_company) ) like '%$search_term%'
            )");
        }

        $data->where("a.active_start_date <= '" . date('Y-m-d') . "' AND " . "a.active_end_date >= '" . date('Y-m-d') . "'");

        return count($data->get()->result());
    }
}
