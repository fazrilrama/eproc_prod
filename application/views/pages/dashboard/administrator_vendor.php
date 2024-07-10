<?php
$id_user = $this->session->userdata('user')['id_user'];
$company = $this->db
    ->where('id_user', $id_user)
    ->get('company_profile')->row();
$unread_notif = $this->db->where('deleted_at is null')
    ->where('is_readed=0')
    ->where('to_user', $id_user)
    ->get('tbl_notification')->num_rows();

$open_procurement = $this->db->query('select count(*) as total
from project 
where project_type=1
and status=2
and deleted_at is null')->row()->total;

$total_catalogue = $this->db->query("select count(*) as total
from company_catalogue 
where id_company='" . $company->id . "'
and deleted_at is null")->row()->total;
$total_catalogue_active = $this->db->query("select count(*) as total
from company_catalogue 
where id_company='" . $company->id . "'
and (CURRENT_TIMESTAMP<=active_end_date)
and deleted_at is null")->row()->total;
$total_catalogue_inactive = $this->db->query("select count(*) as total
from company_catalogue 
where (CURRENT_TIMESTAMP>active_end_date)
and id_company='" . $company->id . "'
and deleted_at is null")->row()->total;

$total_order = $this->db->query("select count(*) as total
from shopping_cart where product_id in (
    select a.id from company_catalogue a 
    inner join company_profile b on a.id_company=b.id
    where b.id='" . $company->id . "'
)
and deleted_at is null
and (status=2 or status=8)")->row()->total;


?>

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-graph icon-gradient bg-ripe-malin">
                </i>
            </div>
            <div>Dashboard
                <div class="page-title-subheading">Selamat Datang di E-Procurement PT. BGR Logistik Indonesia, <b>
                        <?php
                        $role_name = $this->session->userdata('user')['role_name'];
                        echo $role_name . ' ' . $company->prefix_name . ' ' . $company->name;
                        ?></b>.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12 col-xl-6">
        <div class="mb-3 profile-responsive card">
            <div class="dropdown-menu-header">
                <div class="dropdown-menu-header-inner bg-dark">
                    <div class="menu-header-image opacity-1" style="background-image: url('assets/images/dropdown-header/abstract3.jpg');"></div>
                    <div class="menu-header-content btn-pane-right">
                        <div class="avatar-icon-wrapper mr-3 avatar-icon-xl btn-hover-shine">
                            <div class="avatar-icon rounded">
                                <img src="<?php echo site_url('assets/img/icon/default_user.png') ?>" alt="Avatar 5"></div>
                        </div>
                        <div>
                            <h5 class="menu-header-title"><?php echo $company->prefix_name . ' ' . $company->name ?></h5>
                            <h6 class="menu-header-subtitle"><?php echo $role_name ?></h6>
                        </div>
                        <!-- <div class="menu-header-btn-pane">
							<button class="btn btn-success">View Profile</button>
						</div> -->
                    </div>
                </div>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <div class="widget-content pt-4 pb-4 pr-1 pl-1">
                        <div class="text-center">
                            <h5 class="mb-0">
                                <span class="pr-1">
                                    <b class="text-danger"><span class=""><?php echo $unread_notif ?></span></b> Notifikasi,
                                </span>
                                <span><b class="text-success"><span class=""><?php echo $open_procurement ?></span></b> Pengadaan Terbuka Tersedia</span>
                            </h5>
                        </div>
                    </div>
                </li>
                <li class="p-0 list-group-item">
                    <div class="grid-menu grid-menu-2col">
                        <div class="no-gutters row">
                            <div class="col-sm-6">
                                <div class="p-1" id="account_settings">
                                    <button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-dark">
                                        <i class="pe-7s-user text-dark opacity-7 btn-icon-wrapper mb-2"> </i>
                                        Pengaturan Akun
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-1" id="to_competency">
                                    <button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-danger">
                                        <i class="fa fa-list text-danger opacity-7 btn-icon-wrapper mb-2"> </i>
                                        Kompetensi Perusahaan
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-1" id="manual_book">
                                    <button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-success">
                                        <i class="pe-7s-info text-success opacity-7 btn-icon-wrapper mb-2"> </i>
                                        Petunjuk Penggunaan
                                    </button>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-1" onclick="logout()">
                                    <button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-focus">
                                        <i class="pe-7s-power text-focus opacity-7 btn-icon-wrapper mb-2"> </i>
                                        Logout
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-lg-12 col-xl-6">

        <div class="card">

            <div class="grid-menu grid-menu-2col">
                <div class="no-gutters row">
                    <div class="col-sm-6" id="to_notif_all">
                        <div class="widget-chart widget-chart-hover">
                            <div class="icon-wrapper rounded-circle">
                                <div class="icon-wrapper-bg bg-primary"></div>
                                <i class="fa fa-bell text-primary"></i>
                            </div>
                            <div class="widget-numbers"><span class="notif_all"><?php echo $unread_notif ?></span></div>
                            <div class="widget-subheading">Notifikasi</div>
                            <div class="widget-description text-success">
                                <i class="fa fa-share"></i>
                                <span class="pl-1">Lihat Selengkapnya...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="to_ecatalogue">
                        <div class="widget-chart widget-chart-hover">
                            <div class="icon-wrapper rounded-circle">
                                <div class="icon-wrapper-bg bg-info"></div>
                                <i class="fa fa-shopping-bag text-info"></i>
                            </div>
                            <div class="widget-numbers"><span class="ecatalogue"><?php echo $total_catalogue ?></span></div>
                            <div class="widget-subheading">Total E-Katalog</div>
                            <div class="widget-description text-info">
                                <i class="fa fa-share"></i>
                                <span class="pl-1">Lihat Selengkapnya...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="to_order">
                        <div class="widget-chart widget-chart-hover">
                            <div class="icon-wrapper rounded-circle">
                                <div class="icon-wrapper-bg bg-danger"></div>
                                <i class="fa fa-shopping-cart text-danger"></i>
                            </div>
                            <div class="widget-numbers"><span class="order"><?php echo $total_order ?></span></div>
                            <div class="widget-subheading">Total Pemesanan</div>
                            <div class="widget-description text-primary">
                                <i class="fa fa-share"></i>
                                <span class="pl-1">Lihat Selengkapnya...</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6" id="to_procurement">
                        <div class="widget-chart widget-chart-hover br-br">
                            <div class="icon-wrapper rounded-circle">
                                <div class="icon-wrapper-bg bg-success"></div>
                                <i class="fa fa-user"></i>
                            </div>
                            <div class="widget-numbers" class="procurement"><?php echo $open_procurement ?></div>
                            <div class="widget-subheading">Pengadaan Terbuka</div>
                            <div class="widget-description text-warning">
                                <i class="fa fa-share"></i>
                                <span class="pl-1">Lihat Selengkapnya...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Pengadaan Terbuka</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 table-responsive">
                        <table style="width:'100%' !important;" id="list_pengadaan_table" class="table table-borderless">
                            <thead>
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Statistik</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <figure class="highcharts-figure">
                            <div id="ecatalogue_stat"></div>
                        </figure>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        var tableData = $('#list_pengadaan_table').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {
                no = 0;
            },
            "responsive": false,
            "select": false,
            "processing": true,
            "retrieve": true,
            "serverSide": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'procurement/get_data',
                "data": function(d) {
                    d.f_status = 2;
                    d.f_project_type = 1;
                },
                "dataSrc": "data"
            },
            'columns': [{
                render: function(data, type, full, meta) {


                    return `
                        <div class="single-blog-post card" style="">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p style="text-align:left;font-size:14pt;font-weight:bold;">${full[4].length>34?full[4].substring(0,34)+'...':full[4]}</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="timer" time="${full[6]}" style="text-align:right; font-size:15pt; color:#EE7B1D"></p>
                                            </div>
                                        </div>
                                        <div>
                                            <div id="strip_html" hidden>
                                            ${full[7]}
                                            </div>
                                            <div class="clean_html">
                                            </div>
                                        </div>
                                        <br/>
                                        <b>Waktu Pengadaan</b>: ${moment(full[5]).format('D MMM Y HH:mm')} s/d ${moment(full[6]).format('D MMM Y HH:mm')}
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer" style="text-align:right;">
                                <a href="<?php echo site_url('home/pengadaan/') ?>${full[0]}" class="btn btn-info">Selengkapnya <i class="arrow_carrot-2right"></i></a>
                            </div>
                        </div>`;
                }
            }],
            "drawCallback": function(settings) {

                var timers = [];
                var i = 0;
                $('.timer').each(function() {
                    var dataDate = $(this).attr('time');
                    timers[i] = setInterval(function() {
                        var now = new Date().getTime();
                        var distance = (new Date(dataDate).getTime()) - now;
                        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        var isActive = distance > 0;
                        var timeLeft = `<span class="">${isActive?`${days} hari ${hours} jam ${minutes} menit ${seconds} detik`:`Sisa Waktu 0 hari 0 jam 0 menit`}</span>`;
                        var textLeftTime = `${timeLeft}`;

                        $(this).html(textLeftTime);
                    }, 1000);

                    i++;
                });

                $('.clean_html').each(function() {
                    var plainText = $(this).parent('div').find('[id="strip_html"]').text();
                    $(this).html(plainText.length > 150 ? plainText.substring(0, 150) + '...' : plainText);
                });


            }
        });



        Highcharts.chart('ecatalogue_stat', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'E-Katalog'
            },
            tooltip: {
                pointFormat: '<b>{point.y}</b> {series.name}'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Katalog',
                colorByPoint: true,
                data: [{
                    name: 'Katalog Aktif',
                    y: parseInt('<?php echo $total_catalogue_active ?>'),
                    sliced: false,
                    selected: true,
                    color: '#4fc740'
                }, {
                    name: 'Katalog Tidak Aktif',
                    y: parseInt('<?php echo $total_catalogue_inactive ?>'),
                    color: '#eb4034'
                }]
            }]
        });



        $('#account_settings').click(function() {
            RyLinx.to('#required_info/data_perusahaan', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#required_info/data_perusahaan');
            });
        });
        $('#to_competency').click(function() {
            RyLinx.to('#profile/company_competencies', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#profile/company_competencies');
            });
        });

        $('#to_notif_all').click(function() {
            RyLinx.to('#notification', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#notification');
            });
        });
        $('#to_ecatalogue').click(function() {
            RyLinx.to('#catalogue_manage', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#catalogue_manage');
            });
        });
        $('#to_order').click(function() {
            RyLinx.to('#shopping/order_list', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#shopping/order_list');
            });
        });

        $('#to_procurement').click(function() {
            RyLinx.to('#procurement/procurement_list', function() {
                $('#loading_content').hide();
                window.history.replaceState('', '', '#procurement/procurement_list');
            });
        });

        $('#manual_book').click(function() {
            window.open(site_url + 'assets/file/MANUAL_BOOK.pdf');
        });


    });
</script>