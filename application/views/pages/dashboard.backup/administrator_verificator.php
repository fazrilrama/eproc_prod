<?php
$id_user = $this->session->userdata('user')['id_user'];
$company = $this->db
	->where('id_user', $id_user)
	->get('company_profile')->row();
$user = $this->db
	->where('id_user', $id_user)
	->get('sys_user')->row();
$unread_notif = $this->db->where('deleted_at is null')
	->where('is_readed=0')
	->get('tbl_notification')->num_rows();

$open_procurement = $this->db->query('select count(*) as total
from project 
where project_type=1
and status=2
and deleted_at is null')->row()->total;

$total_vendor_being_verified = $this->db->query('SELECT count(a.id_user) as total FROM `sys_user` a
								inner join sys_usr_status b on a.id_usr_status=b.id_usr_status
								inner join sys_usr_role c on c.id_usr_role=a.id_usr_role
								where a.deleted_at is null
								and (a.id_usr_role=2 or a.id_usr_role=6 or a.id_usr_role=7)
								and ( a.id_usr_status=1 or a.id_usr_status=5) ')->row()->total;

$total_vendor_verification = $this->db->query('
								SELECT count(a.id_user) as total FROM `sys_user` a
								inner join sys_usr_status b on a.id_usr_status=b.id_usr_status
								inner join sys_usr_role c on c.id_usr_role=a.id_usr_role
								where a.deleted_at is null
								and (a.id_usr_role=2 or a.id_usr_role=6 or a.id_usr_role=7)
									and ( ( a.id_usr_status=6 )
									or ( a.id_usr_status=2 and (
									select company_profile.verification_status from company_profile 
									inner join tbl_sync_sap on tbl_sync_sap.id_company=company_profile.id
									where id_user=a.id_user 
									and company_profile.id in (select id_company from company_legal_npwp) )=\'Pending Verification\' 
									)
								)
								')->row()->total;
$total_vendor_registered = $this->db->query(
	'SELECT count(a.id_user) as total FROM `sys_user` a
								inner join sys_usr_status b on a.id_usr_status=b.id_usr_status
								inner join sys_usr_role c on c.id_usr_role=a.id_usr_role
								inner join company_profile d on d.id_user=a.id_user
								inner join company_legal_npwp e on e.id_company=d.id
								inner join tbl_sync_sap f on f.id_company=d.id
								where a.deleted_at is null
								and (a.id_usr_role=2 or a.id_usr_role=6 or a.id_usr_role=7)
								and a.id_usr_status!=4'
)->row()->total;

$total_Vendor_company = $this->db->query('SELECT count(a.id) as total FROM company_profile a
inner JOIN company_contact b on (a.id=b.id_company and b.deleted_at is null)
inner JOIN company_legal_npwp c on (a.id=c.id_company and c.deleted_at is null)
inner JOIN tbl_sync_sap d on (a.id=d.id_company and d.deleted_at is null)
inner JOIN m_group e on d.id_group=e.id
inner JOIN sys_user f on f.id_user=a.id_user
inner JOIN sys_usr_status g on g.id_usr_status=f.id_usr_status
inner join sys_usr_role rl on rl.id_usr_role=f.id_usr_role
WHERE a.deleted_at is null 
and f.id_usr_role=2
and f.id_usr_status=2')
	->row()->total;

$total_vendor_personal = $this->db->query('SELECT count(a.id) as total FROM company_profile a
inner JOIN company_contact b on (a.id=b.id_company and b.deleted_at is null)
inner JOIN company_legal_npwp c on (a.id=c.id_company and c.deleted_at is null)
inner JOIN tbl_sync_sap d on (a.id=d.id_company and d.deleted_at is null)
inner JOIN m_group e on d.id_group=e.id
inner JOIN sys_user f on f.id_user=a.id_user
inner JOIN sys_usr_status g on g.id_usr_status=f.id_usr_status
inner join sys_usr_role rl on rl.id_usr_role=f.id_usr_role
WHERE a.deleted_at is null 
and f.id_usr_role=6
and f.id_usr_status=2')
	->row()->total;


$total_catalogue = $this->db->query("select count(*) as total
from company_catalogue 
where deleted_at is null")->row()->total;
$total_catalogue_active = $this->db->query("select count(*) as total
from company_catalogue 
where (CURRENT_TIMESTAMP<=active_end_date)
and deleted_at is null")->row()->total;
$total_catalogue_inactive = $this->db->query("select count(*) as total
from company_catalogue 
where (CURRENT_TIMESTAMP>active_end_date)
and deleted_at is null")->row()->total;
$total_order = $this->db->query("select count(*) as total
from shopping_cart where product_id in (
    select a.id from company_catalogue a 
    inner join company_profile b on a.id_company=b.id
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
						echo $role_name . (($company != null) ? ' ' . $company->prefix_name . ' ' . $company->name : ' ' . $user->name);
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
							<h5 class="menu-header-title"><?php echo (($company != null) ? ' ' . $company->prefix_name . ' ' . $company->name : ' ' . $user->name); ?></h5>
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
								<span><b class="text-success"><span class=""><?php echo $total_vendor_verification ?></span></b> Vendor Perlu Diverfikasi</span>
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
								<div class="p-1" id="to_report">
									<button class="btn-icon-vertical btn-transition-text btn-transition btn-transition-alt pt-2 pb-2 btn btn-outline-danger">
										<i class="fa fa-list text-danger opacity-7 btn-icon-wrapper mb-2"> </i>
										Report Data Vendor
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
					<div class="col-sm-6">
						<div class="widget-chart widget-chart-hover">
							<div class="icon-wrapper rounded-circle">
								<div class="icon-wrapper-bg bg-info"></div>
								<i class="fa fa-shopping-bag text-info"></i>
							</div>
							<div class="widget-numbers"><span class="total_vendor"><?php echo $total_vendor_being_verified ?></span></div>
							<div class="widget-subheading">Total Vendor (Melengkapi Data)</div>
							<div class="widget-description text-info">
								<!-- <i class="fa fa-share"></i> -->
								<!-- <span class="pl-1">Lihat Selengkapnya...</span> -->
							</div>
						</div>
					</div>
					<div class="col-sm-6" id="to_vendor_verify">
						<div class="widget-chart widget-chart-hover">
							<div class="icon-wrapper rounded-circle">
								<div class="icon-wrapper-bg bg-danger"></div>
								<i class="fa fa-shopping-cart text-danger"></i>
							</div>
							<div class="widget-numbers"><span class="order"><?php echo $total_vendor_verification ?></span></div>
							<div class="widget-subheading">Total Vendor (Perlu Verifikasi)</div>
							<div class="widget-description text-primary">
								<i class="fa fa-share"></i>
								<span class="pl-1">Lihat Selengkapnya...</span>
							</div>
						</div>
					</div>
					<div class="col-sm-6" id="to_vendor">
						<div class="widget-chart widget-chart-hover br-br">
							<div class="icon-wrapper rounded-circle">
								<div class="icon-wrapper-bg bg-success"></div>
								<i class="fa fa-user"></i>
							</div>
							<div class="widget-numbers" class="procurement"><?php echo $total_vendor_registered ?></div>
							<div class="widget-subheading">Vendor Terdaftar</div>
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
<div class="card">
	<div class="card-header">
		<h5>Statistik</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-4">
				<figure class="highcharts-figure">
					<div id="vendor_stat"></div>
				</figure>
			</div>
			<div class="col-md-4">
				<figure class="highcharts-figure">
					<div id="ecatalogue_stat"></div>
				</figure>
			</div>
			<div class="col-md-4">
				<figure class="highcharts-figure">
					<div id="vendor_compare_stat"></div>
				</figure>
			</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function() {
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
		Highcharts.chart('vendor_stat', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: 'Vendor (DRM)'
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
				name: 'Vendor',
				colorByPoint: true,
				data: [{
					name: 'Vendor Perusahaan',
					y: parseInt('<?php echo $total_Vendor_company ?>'),
					sliced: false,
					selected: true,
					color: '#34cceb'
				}, {
					name: 'Vendor Personal',
					y: parseInt('<?php echo $total_vendor_personal ?>'),
					color: '#e07426'
				}]
			}]
		});

		Highcharts.chart('vendor_compare_stat', {
			chart: {
				plotBackgroundColor: null,
				plotBorderWidth: null,
				plotShadow: false,
				type: 'pie'
			},
			title: {
				text: 'Perbandingan Vendor Terdaftar dan Sedang Diproses'
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
				name: 'Vendor',
				colorByPoint: true,
				data: [{
					name: 'Vendor Terdaftar',
					y: parseInt('<?php echo $total_vendor_registered ?>'),
					sliced: false,
					selected: true,
					color: '#34cceb'
				}, {
					name: 'Vendor Sedang Melengkapi atau Verifikasi Data',
					y: parseInt('<?php echo $total_vendor_being_verified + $total_vendor_verification ?>'),
					color: '#e07426'
				}]
			}]
		});



		$('#account_settings').click(function() {
			RyLinx.to('#user/manage_account', function() {
				$('#loading_content').hide();
				window.history.replaceState('', '', '#user/manage_account');
			});
		});
		$('#to_report').click(function() {
			RyLinx.to('#report/vendor', function() {
				$('#loading_content').hide();
				window.history.replaceState('', '', '#report/vendor');
			});
		});

		$('#to_notif_all').click(function() {
			RyLinx.to('#notification', function() {
				$('#loading_content').hide();
				window.history.replaceState('', '', '#notification');
			});
		});
		$('#to_vendor_verify').click(function() {
			RyLinx.to('#verification/data_perusahaan', function() {
				$('#loading_content').hide();
				window.history.replaceState('', '', '#verification/data_perusahaan');
			});
		});
		$('#to_vendor').click(function() {
			RyLinx.to('#report/vendor', function() {
				$('#loading_content').hide();
				window.history.replaceState('', '', '#report/vendor');
			});
		});

		$('#manual_book').click(function() {
			window.open(site_url + 'assets/file/MANUAL_BOOK.pdf');
		});


	});
</script>