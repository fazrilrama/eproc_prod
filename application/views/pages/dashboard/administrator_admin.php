<?php
$id_user = $this->session->userdata('user')['id_user'];
$company = $this->db
	->where('id_user', $id_user)
	->get('company_profile')->row();
$unread_notif = $this->db
	->join('sys_user b', 'a.from=b.id_user')
	->join('sys_user c', 'a.to_user=c.id_user', 'left')
	->join('sys_usr_role d', 'a.to_role=d.id_usr_role', 'left')
	->join('sys_usr_status e', 'b.id_usr_status=e.id_usr_status')
	->where('e.id_usr_status != 5')
	->where('a.deleted_at is null')
	->where('a.is_readed', 0)
	->get('tbl_notification a')
	->num_rows();

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

$total_vendor_verification = $this->db->query("SELECT count(*) as total FROM `sys_user` a
						inner join sys_usr_status b on a.id_usr_status=b.id_usr_status
						inner join sys_usr_role c on c.id_usr_role=a.id_usr_role
						inner join company_profile company on company.id_user=a.id_user
						left join tbl_sync_sap sap on sap.id_company=company.id
						inner join company_legal_npwp npwp on npwp.id_company=company.id
						where a.deleted_at is null
						and (a.id_usr_role=2 or a.id_usr_role=6 or a.id_usr_role=7)

						and ( 
								( a.id_usr_status=6 and company.verification_status !='Rejected')
								or ( a.id_usr_status=2 
									and (select company_profile.verification_status from company_profile 
										inner join tbl_sync_sap on tbl_sync_sap.id_company=company_profile.id
										where id_user=a.id_user 
										and company_profile.id in (select id_company from company_legal_npwp) )='Pending Verification' 
							)
						)")->row()->total;
$total_vendor_registered = $this->db->query(
	'SELECT count(a.id_user) as total FROM `sys_user` a
								inner join sys_usr_status b on a.id_usr_status=b.id_usr_status
								inner join sys_usr_role c on c.id_usr_role=a.id_usr_role
								inner join company_profile d on d.id_user=a.id_user
								inner join company_legal_npwp e on e.id_company=d.id
								inner join tbl_sync_sap f on f.id_company=d.id
								AND b.id_usr_status=2
								and (a.id_usr_role=2 or a.id_usr_role=6 or a.id_usr_role=7)'
)->row()->total;

$total_vendor_aktif = $this->db->select('*')->from('company_cabang_area')->group_by('id_company')->get()->result_array();

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
where (active_end_date >= CURRENT_TIMESTAMP)
and deleted_at is null")->row()->total;
$total_catalogue_inactive = $this->db->query("select count(*) as total
from company_catalogue 
where (active_end_date < CURRENT_TIMESTAMP)
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
						echo $role_name . ' (' . $this->session->userdata('user')['name'] . ')';
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
							<h5 class="menu-header-title"><?php echo  $this->session->userdata('user')['name'] ?></h5>
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
					<!-- <div class="col-sm-6">
						<div class="widget-chart widget-chart-hover">
							<div class="icon-wrapper rounded-circle">
								<div class="icon-wrapper-bg bg-info"></div>
								<i class="fa fa-shopping-bag text-info"></i>
							</div>
							<div class="widget-numbers"><span class="total_vendor"><?= count($total_vendor_aktif) ?></span></div>

							<div class="widget-subheading">Total Vendor (Melengkapi Data)</div>
							<div class="widget-description text-info">
							</div>
						</div>
					</div> -->
					<div class="col-sm-6" id="to_vendor">
						<div class="widget-chart widget-chart-hover br-br">
							<div class="icon-wrapper rounded-circle">
								<div class="icon-wrapper-bg bg-success"></div>
								<i class="fa fa-user"></i>
							</div>
							<div class="widget-numbers" class="procurement"><?= count($total_vendor_aktif) ?></div>

							<div class="widget-subheading">Vendor Terdaftar</div>
							<div class="widget-description text-warning">
								<i class="fa fa-share"></i>
								<span class="pl-1">Lihat Selengkapnya...</span>
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

<div class="card">
	<div class="card-header">
		<h5>Statistik TRX</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-12">
				<center>
					<h5> <span class="fmost_trx_length_txt"></span> Barang dengan TRX Terbanyak</h5>
				</center>
				<div class="row">
					<?php
					$months = [
						['id' => "1", "name" => "Januari"],
						['id' => "2", "name" => "Februari"],
						['id' => "3", "name" => "Maret"],
						['id' => "4", "name" => "April"],
						['id' => "5", "name" => "Mei"],
						['id' => "6", "name" => "Juni"],
						['id' => "7", "name" => "Juli"],
						['id' => "8", "name" => "Agustus"],
						['id' => "9", "name" => "September"],
						['id' => "10", "name" => "Oktober"],
						['id' => "11", "name" => "November"],
						['id' => "12", "name" => "Desember"],
					];
					?>
					<div class="col-md-6">
						<label for=""><b>Divre</b></label>
						<select class="form-control" id="fmost_trx_branch">
							<option value="" selected>Seluruh Divre</option>
							<?php
							$branchCode = $this->db->get('m_branch_code')->result();
							foreach ($branchCode as $b) {
								echo '<option value="' . $b->id . '">' . $b->name . '</option>';
							}
							?>
						</select>
					</div>
					<div class="col-md-6">
						<label for=""><b>Range Maksimal</b></label>
						<select class="form-control" id="fmost_trx_length">
							<option value="10" selected>10</option>
							<option value="15">15</option>
							<option value="20">20</option>
							<option value="25">25</option>
						</select>
					</div>
					<div class="col-md-6">
						<label for=""><b>Bulan</b></label>
						<input type="month" value="<?php echo date('Y') . '-01' ?>" class="form-control" id="fmost_trx_date_start">
					</div>
					<div class="col-md-6">
						<label for=""><b>s.d Bulan</b></label>
						<input type="month" value="<?php echo date('Y-m') ?>" class="form-control" id="fmost_trx_date_end">
					</div>
					<div class="col-md-12">
						<br>
						<table id="fmost_table" class="table table-striped table-hover">
							<thead>
								<tr>
									<th>No</th>
									<th>Kompetensi</th>
									<th>Sub Kompetensi</th>
									<th>Nama Vendor</th>
									<th>Nama Produk</th>
									<th>Banyak TRX</th>
								</tr>
							</thead>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script>
	$(document).ready(function() {

		var filter = {
			fMostTRXLength: {
				id: 'fmost_trx_length',
				ele: $('#fmost_trx_length')
			},
			fMostTRXDateStart: {
				id: 'fmost_trx_date_start',
				ele: $('#fmost_trx_date_start')
			},
			fMostTRXDateEnd: {
				id: 'fmost_trx_date_end',
				ele: $('#fmost_trx_date_end')
			},
			fMostTRXBranch: {
				id: 'fmost_trx_branch',
				ele: $('#fmost_trx_branch')
			},
		};
		var no = 0;

		var fMostTable = $("#fmost_table").DataTable({
			'ajax': {
				"type": "POST",
				"url": site_url + 'dashboard/get_most_trx',
				"data": function(d) {
					Object.keys(filter).forEach(function(key) {
						d[filter[key].id] = filter[key].ele.val();
					});

				},
				"dataSrc": function(res) {
					no = 0;
					return res;
				}
			},
			"columns": [{
					render: function(data, type, full, meta) {
						return no += 1;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.comp;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.sub_comp_name;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.vendor_name;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.product_name;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.count_trx + ' Total TRX';
					}
				},
			]
		});
		var fmostLengtText = $('.fmost_trx_length_txt');

		Object.keys(filter).forEach(function(key) {
			filter[key].ele.change(function() {
				if (filter[key].id == 'fmost_trx_length') {
					fmostLengtText.html($(this).val());
				}
				$("#fmost_table").DataTable().ajax.reload();
			});
		});

		filter.fMostTRXLength.ele.val(10).trigger('change');


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