<?php $this->load->view('templates/dashboard/content-title'); ?>
<style>
	.filter-card {
		margin-left: 16em;
		margin-right: 16em;
		margin-bottom: 3em;
		padding: 10px;
		background-color: #f0f0f0;
	}
</style>

<div class="card">
	<div class="card-body">
		<div style="margin:10px">
			<h5>Filter</h5>
			<?php echo form_open('#', ['id' => 'filter_form']) ?>
			<div class="row">

				<div class="col-md-6">
					<b>Status Pengadaan</b>
					<select class="form-control select2-non-modal" name="f_status" id="f_status">
						<option value="2">Terpublish</option>
						<option value="4">Dibatalkan</option>
						<option value="3">Selesai (Dengan Pemenang)</option>
						<option value="5">Selesai (Tanpa Pemenang)</option>
					</select>
					<b>Metode Pengadaan</b>
					<select class="form-control select2-non-modal" name="f_project_type" id="f_project_type">
						<option value="">Semua</option>
						<?php
						$types = $this->db->where('deleted_at is null')
							->get('m_project_type')->result();

						foreach ($types as $t) {
							echo '<option value="' . $t->id . '">' . $t->name . '</option>';
						}
						?>
					</select>
				</div>
				<div class="col-md-6">
					<b>Waktu</b>
					<div class="row">
						<div class="col-md-6">
							<input class="form-control" type="datetime-local" name="f_start_date" id="f_start_date">
						</div>
						<div class="col-md-6">
							<input class="form-control" type="datetime-local" name="f_end_date" id="f_end_date">
						</div>
					</div>
				</div>

			</div>
			<br>
			<button type="submit" id="filter_submit" class="btn btn-success btn-lg"> <i class="fa fa-filter"></i>
				Filter</button>
			<button type="reset" id="filter_reset" class="btn btn-danger btn-lg"><i class="fa fa-retweet"></i>
				Reset</button>

			<?php echo form_close() ?>
		</div>
		<hr />

		<div style="margin:10px;">
			<span style="font-size:16pt;">Data Pengadaan</span>
			<span id="action_btn_container">
				<button title="Lihat Pengadaan" id="view-btn" class="btn btn-sm btn-success">
					<i class="fa fa-edit"></i>
				</button>
				<button title="Lihat Pemenang" id="look-btn" class="btn btn-sm btn-success">
					<i class="fa fa-info"></i>
				</button>
			</span>

		</div>
		<div class="table-responsive">
			<table id="table-data" class="table table-striped table-hover nowrap">
				<thead>
					<tr>
						<th>Pemilik</th>
						<th>No.PR</th>
						<th>Tipe</th>
						<th>Nama</th>
						<th>Waktu</th>
						<th>Status</th>
						<th>Bidang usaha</th>
						<th>Kompetensi</th>
						<th>Area Kerja</th>
						<th>Lampiran</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery-mask-number@1.0.1/jquery.masknumber.min.js"></script>
<script>
	$(document).ready(function() {

		let formFilterID = "filter_form";
		let filterStatus = $('#f_status');
		let filterStartMonth = $('#f_start_date');
		let filterEndMonth = $('#f_end_date');
		let filterReset = $('#filter_reset');
		let formFilter = $(`#${formFilterID}`);
		let saveURL = '<?php echo $save_url ?>';
		let publishURL = '<?php echo $publish_url ?>';
		let getURL = '<?php echo $get_url ?>';
		let updateURL = '<?php echo $update_url ?>';
		let deleteURL = '<?php echo $delete_url ?>';
		let selectedData = null;

		let addBtn = $('#add-btn');
		let editBtn = $('#edit-btn');
		let delBtn = $('#delete-btn');

		let isVendor = (user.role_id == 2 || user.role_id == 6 || user.role_id == 7);

		let tableData = $('#table-data').DataTable({
			"aaSorting": [],
			"initComplete": function(settings, json) {
				no = 0;
			},
			dom: 'Bfrtip',
			buttons: [
				'pageLength',
				{
					extend: 'excel',
				},
			],
			"responsive": false,
			"select": "single",
			"processing": true,
			"retrieve": true,
			"serverSide": true,
			'ajax': {
				"type": "GET",
				"url": site_url + getURL,
				"data": function(d) {
					d.f_status = filterStatus.val();
					d.f_start_date = filterStartMonth.val();
					d.f_end_date = filterEndMonth.val();
					d.f_id_user = (user.role_id == 3) ? user.id_user : null;
				},
				"dataSrc": "data"
			},
			'columns': [{
					render: function(data, type, full, meta) {
						return full[19];
					}
				}, {
					render: function(data, type, full, meta) {
						return full[1];
					}
				},
				{
					render: function(data, type, full, meta) {
						let status = '-';
						let badgeCondition = "info";
						switch (full[2]) {
							case '1':
								badgeCondition = "success";
								break;
							case '2':
								badgeCondition = "primary";
								break;
							case '3':
								badgeCondition = "info";
								break;
							case '4':
								badgeCondition = "danger";
								break;
							default:
								badgeCondition = "success";
								break;
						}

						return `<badge class="badge badge-${badgeCondition}">${full[3]}</badge>`;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full[4];
					}
				},
				{
					render: function(data, type, full, meta) {
						let now = new Date().getTime();
						let distance = (new Date(full[6]).getTime()) - now;
						let days = Math.floor(distance / (1000 * 60 * 60 * 24));
						let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						let seconds = Math.floor((distance % (1000 * 60)) / 1000);

						let isActive = distance > 0;

						let timeLeft = `<badge class="badge badge-${isActive?"success":"danger"}">${isActive?`Sisa Waktu ${days} hari ${hours} jam ${minutes} menit`:`Sisa Waktu 0 hari 0 jam 0 menit`}</badge>`;

						return `${moment(full[5]).format('D MMM Y HH:mm')} s/d ${moment(full[6]).format('D MMM Y HH:mm')}<br>${timeLeft}`;
					}
				},
				{
					render: function(data, type, full, meta) {
						let status = '-';
						let badgeCondition = "info";
						switch (full[8]) {
							case '1':
								status = 'Tersimpan';
								badgeCondition = "info";
								break;
							case '2':
								status = 'Terpublish';
								badgeCondition = "warning";
								break;
							case '3':
								status = 'Selesai (Dengan Pemenang)';
								badgeCondition = "success";
								break;
							case '4':
								status = 'Dibatalkan';
								badgeCondition = "danger";
								break;
							case '5':
								status = 'Selesai (Tanpa Pemenang)';
								badgeCondition = "danger";
								break;
							default:
								status = 'Tersimpan';
								badgeCondition = "info";
								break;
						}

						return `<badge class="badge badge-${badgeCondition}">${status}</badge>`;
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[21] != null) ? full[21] : 'Semua';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[12] != null) ? full[12] : 'Semua';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[23] != null) ? full[23] : 'Semua';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[9] != null) ?
							`<a target="_blank" href="${base_url}upload/procurement/file/${full[9]}"><i class="fa fa-download"></i>Unduh</a>` :
							'-';
					}
				},

			],
			"drawCallback": function(settings) {}
		});

		tableData.on('select', function(e, dt, type, indexes) {
			if (type === 'row') {
				var data = tableData.rows({
					selected: true
				}).data();
				selectedData = data[0];
				//console.log(selectedData);
			}
		});
		tableData.on('deselect', function(e, dt, type, indexes) {
			selectedData = null;
		});
		let setSelectedData = function(data) {
			this.selectedData = data;
			if (tableData != null) tableData.rows('.selected').deselect();
		}.bind(this);

		let showForm = function(titleForm = "Tambah Pengadaan", loadedData = null) {
			let bodyForm = `
			<?php echo form_open('#', ['id' => 'form-data']) ?>
				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Pembuat Pengadaan<span style="color:red">*</span> </label>
					</div>
					<div class="col-md-9">
						<select style="width:100%" type="select" id="id_user" name="id_user" class="form-control select2" 
							data-validation="required">
							<option value="">Pilih</option>
							<?php
							$data = $this->db->where('id_usr_status', App_Model::STAT_ACCOUNT_ACTIVE)
								->where('id_usr_role', App_Model::ROLE_CUSTOMER)
								->get(App_Model::TBL_USER)
								->result();
							foreach ($data as $o) {
								echo '<option value="' . $o->id_user . '">' . $o->name . ' - ' . $o->email . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
					<label class="form-label">Metode Pengadaan<span style="color:red">*</span> </label>
					</div>
					<div class="col-md-9">
					
					<select style="width:100%" class="form-control select2" id="project_type" name="project_type" 
					data-validation="required">
						<option value="">Pilih</option>
						<?php
						$data = $this->db->where('deleted_at is null')
							->get('m_project_type')->result();
						foreach ($data as $d) {
							echo '<option value="' . $d->id . '">' . $d->name . '</option>';
						}
						?>
					</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
					<label class="form-label">Bidang Usaha<span style="color:red">*</span> </label>
					</div>
					<div class="col-md-9">
					<select style="width:100%"  multiple id="id_company_type" name="id_company_type[]" class="form-control select2" 
					data-validation="required" >
						<option value="">Pilih</option>
						<?php
						$data = $this->db
							->where('deleted_at is null')
							->get('m_company_type')
							->result();
						foreach ($data as $o) {
							echo '<option value="' . $o->id . '">' . $o->name . '</option>';
						}
						?>
					</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
					<label class="form-label">Kompetensi</label>
					</div>
					<div class="col-md-9">
					<select style="width:100%"  type="select" id="id_company_competency" name="id_company_competency" 
						class="form-control select2">
						<option value="">Pilih</option>
						<?php
						$data = $this->db
							->where('deleted_at is null')
							->get(App_Model::TBL_COMPANY_COMPETENCY)
							->result();
						foreach ($data as $o) {
							echo '<option value="' . $o->id . '">' . $o->name . '</option>';
						}
						?>
					</select>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Area Kerja<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<select style="width:100%" type="select" multiple id="work_area" name="work_area[]" 
							class="form-control select2">
							<option value="">Pilih</option>
							<?php
							$data = $this->db
								->where('deleted_at is null')
								->get('m_city')
								->result();
							foreach ($data as $o) {
								echo '<option value="' . $o->id . '">' . $o->name . '</option>';
							}
							?>
						</select>
					</div>
				</div>
				
				<div class="form-group row">
					<div class="col-md-3">
					<label class="form-label">Target Vendor<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">
					<select style="width:100%" type="select" multiple id="target_vendors" name="target_vendors[]" 
						class="form-control select2" data-validation="required" >
					</select>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">No PR<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<input type="text" maxLength="50" id="contract_no" name="contract_no" class="form-control"/>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Nama Proyek<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<input type="text" maxLength="255" id="name" name="name" class="form-control"/>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Waktu Proyek<span style="color:red">*</span></label>
					</div>
					<div class="col-md-4">	
						<input type="datetime-local" id="start_date" name="start_date" class="form-control"/>
					</div>
					<div class="col-md-1">
						<center><h6>s/d</h6></center>
					</div>
					<div class="col-md-4">	
						<input type="datetime-local" id="end_date" name="end_date" class="form-control"/>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Lampiran (optional)</label>
					</div>
					<div class="col-md-9">	
						<span id="file_attachment_proc"></span>
					</div>
				</div>

				<div class="form-group">
					<label class="form-label">Deskripsi</label>
					<div id="description" 
					name="description" ></div>
				</div>

				
				<div class="form-group row">
					<div class="col-md-12">	
						<input type="text" class="form-control" id="vendor_value" name="vendor_value"/>
						<input type="text" class="form-control" id="vendor_value_name" name="vendor_value_name"/>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">	
						<input type="text" class="form-control" id="field_value" name="field_value"/>
						<input type="text" class="form-control" id="field_value_name" name="field_value_name"/>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-12">	
						<input type="text" class="form-control" id="work_area_value" name="work_area_value"/>
						<input type="text" class="form-control" id="work_area_name" name="work_area_name"/>
					</div>
				</div>

				<button hidden id="btn-submit" type="submit">Submit</button>
			<?php echo form_close() ?>
			`;
			largeModal({
				title: titleForm,
				body: bodyForm,
				footer: `
				<button style="margin:3px;" id="cancel" class="btn btn-danger btn-lg">
					<i class="fa fa-arrow-left"></i> Kembali
				</button>`
			}).show(function(modal) {
				let mode = "save";

				let initiate = function() {
					$('.select2').select2({
						dropdownParent: $('#modal-large'),
						theme: "default"
					});

					$.validate({
						form: '#form-data',
						validateOnBlur: false, // disable validation when input looses focus
						errorMessagePosition: 'top', // Instead of 'inline' which is default
						scrollToTopOnError: true, // Set this property to true on longer forms
						modules: 'location, date, security, file',
						onModulesLoaded: function() {},
						onError: function($form) {
							event.preventDefault();
						},
						onSuccess: function($form) {
							event.preventDefault();
							formSubmit($form);
							return true;
						}
					});

					$('#vendor_value').parent('div').parent('div').hide();
					$('#field_value').parent('div').parent('div').hide();
					$('#work_area_value').parent('div').parent('div').hide();

					$('#id_company_type').parent('div').parent('div').hide();
					$('#id_company_type').removeAttr('data-validation');
					$('#id_company_competency').parent('div').parent('div').hide();
					$('#file_attachment_proc').html(`<span>Tidak Ada Lampiran</span>`).show();

					let getVendors = function() {
						$.ajax({
							url: site_url + 'procurement/search_vendor_by',
							type: 'get',
							async: false,
							dataType: 'json',
							data: {
								withBlacklisted: 1,
								field: ($('#id_company_type').val() != "" ? $('#id_company_type').val() : []),
								competency: ($('#id_company_competency').val() != "" ? $('#id_company_competency').val() : null)
							},
							success: function(res) {
								let opt = '';
								if (res.data != null) {
									res.data.forEach(function(item, index) {
										opt += `<option value="${item.id}">${item.id_usr_role == 2 ? `${item.name},${item.prefix_name}` : `${item.prefix_name} ${item.name}`}</option>`;
									});
								}
								$('#target_vendors').html(opt);
							},
							error: function(xhr, res, stat) {
								alert('sorry cant get vendors');
							}
						});
					}

					$('#work_area').change(function() {
						let val = $(this).val();
						$('#work_area_value').val(val);

						let i = 0;
						let seletedText = '';
						$('#work_area option:selected').each(function() {
							if (i != 0) seletedText += ',';
							seletedText += $(this).text();
							i++;
						});
						$('#work_area_name').val(seletedText);

						getVendors();
					});

					$('#id_company_type').change(function() {
						let val = $(this).val();
						$('#field_value').val(val);

						let i = 0;
						let seletedText = '';
						$('#id_company_type option:selected').each(function() {
							if (i != 0) seletedText += ',';
							seletedText += $(this).text();
							i++;
						});
						$('#field_value_name').val(seletedText);

						getVendors();
					});

					$('#id_company_competency').change(function() {
						let val = $(this).val();
						getVendors();
					});

					$('#target_vendors').change(function() {
						let val = $(this).val();

						let i = 0;
						let seletedText = '';
						$('#target_vendors option:selected').each(function() {
							if (i != 0) seletedText += ' | ';
							seletedText += $(this).text();
							i++;
						});
						$('#vendor_value_name').val(seletedText);

						$('#vendor_value').val(val);
					});

					let hideForm = function(params = {
						field: true,
						competency: true,
						target_vendors: true,
						work_area: true
					}) {
						if (params.field == null) params.field = true;
						if (params.competency == null) params.competency = true;
						if (params.target_vendors == null) params.target_vendors = true;
						if (params.work_area == null) params.work_area = true;
						//console.log(params);

						if (params.field) {
							$('#id_company_type').parent('div').parent('div').hide();
							$('#id_company_type').removeAttr('data-validation');
							$('#id_company_type').val([]).trigger('change');
						} else {
							$('#id_company_type').parent('div').parent('div').show();
							$('#id_company_type').attr('data-validation', 'required');
						}

						if (params.competency) {
							$('#id_company_competency').parent('div').parent('div').hide();
						} else {
							$('#id_company_competency').parent('div').parent('div').show();
						}

						if (params.target_vendors) {
							$('#target_vendors').val([]).trigger('change');
							$('#target_vendors').attr('multiple', 1);
							$('#target_vendors').parent('div').parent('div').hide();
							$('#target_vendors').removeAttr('data-validation');
						} else {
							$('#target_vendors').parent('div').parent('div').show();
							$('#target_vendors').attr('data-validation', 'required');
						}

						if (params.work_area) {
							$('#work_area').val([]).trigger('change');
							$('#work_area').parent('div').parent('div').hide();
							$('#work_area').removeAttr('data-validation');
						} else {
							$('#work_area').parent('div').parent('div').show();
							$('#work_area').attr('data-validation', 'required');
						}
					}

					$('#project_type').change(function() {
						let val = $(this).val();

						switch (val) {
							case '1': {
								hideForm();
								break;
							}
							case '2': {
								hideForm({
									field: false,
									work_area: false
								});
								break;
							}
							case '3': {
								hideForm({
									field: false,
									target_vendors: false
								});
								break;
							}
							case '4': {
								hideForm({
									field: false,
									target_vendors: false
								});
								break;
							}
							default: {
								hideForm();
								break;
							}
						}

					});


				}

				if (loadedData != null) $('#description').html(loadedData[7]).trigger('change');
				initiate();
				if (user.role_id != 1) {
					$('#id_user').val(user.id_user).trigger('change');
					$('#id_user').attr('disabled', 1);
				}
				if (loadedData == null) {
					$('#project_type').val(1).trigger('change');
					$('#start_date').val(moment().format('YYYY-MM-DDTHH:mm'));
					$('#end_date').val(moment().format('YYYY-MM-DDTHH:mm'));
				} else {
					$('#id_user').val(loadedData[10]).trigger('change');
					$('#project_type').val(loadedData[2]).trigger('change');
					let field = loadedData[11];
					if (field != null) {
						if (field.toString().includes(',')) {
							field = field.split(',');
						}
					} else {
						field = [];
					}
					$('#id_company_type').val(field).trigger('change');

					field = loadedData[15];
					if (field != null) {
						if (field.toString().includes(',')) {
							field = field.split(',');
						}
					} else {
						field = [];
					}
					$('#work_area').val(field).trigger('change');
					field = [];

					$('#contract_no').val(loadedData[1]).trigger('change');
					$('#name').val(loadedData[4]).trigger('change');
					if (loadedData[9] != null) {
						$('#file_attachment_proc')
							.html(`<a target="_blank" href="${base_url}upload/procurement/file/${loadedData[9]}"><i class="fa fa-download"></i>Unduh</a>`)
							.show();
					}
					$('#start_date').val(moment(loadedData[5]).format('YYYY-MM-DDTHH:mm')).trigger('change');
					$('#end_date').val(moment(loadedData[6]).format('YYYY-MM-DDTHH:mm')).trigger('change');


					field = loadedData[14];
					if (field != null) {
						if (field.toString().includes(',')) {
							field = field.split(',');
						}
					} else {
						field = [];
					}
					$('#target_vendors').val(field).trigger('change');

				}

				$('#save').click(function() {
					mode = "save";
					$('#btn-submit').click();
				});

				$('#publish').click(function() {
					mode = "publish";
					let isNotExpired = moment().diff($('#end_date').val(), 'minute') < 0;
					if (isNotExpired) {
						$('#btn-submit').click();
					} else {
						swal('Informasi', 'Tidak bisa publish pengadaan dengan tanggal kadaluarsa!', 'error');
					}
				});

				$('#cancel').click(function() {
					largeModal().close();
				});

				$('#form-data input').attr('disabled', 1);
				$('#form-data select').attr('disabled', 1);
				$('#form-data span[style="color:red"]').remove();
				$('#form-data input[type="file"]').remove();
				$('#form-data input[type="file"]').remove();

			});
		}

		$('#crud_btn_container').hide();
		$('#action_btn_container').hide();
		$('#copy-btn').hide();
		$('#cancel-btn').hide();
		formFilter.on('submit', function(e) {
			e.preventDefault();
			$('#crud_btn_container').hide();
			$('#action_btn_container').hide();
			$('#copy-btn').hide();
			$('#cancel-btn').hide();
			$('#look-btn').hide();
			$('#view-btn').hide();
			if (filterStatus.val() == 2) {
				$('#action_btn_container').show();
				$('#view-btn').show();
			} else {
				$('#action_btn_container').show();
				$('#look-btn').show();
			}
			$(`#table-data`).DataTable().ajax.reload();
		});


		filterReset.click(function() {
			formFilter.get(0).reset();
		});

		$('#action_btn_container').hide();

		if (isVendor) {
			$('#cancel-btn').remove();
			$('#f_status').val(2);
			$('#filter_submit').click();
		}

		$('#view-btn').click(function() {
			if (selectedData != null) {
				showForm(`Pengadaan ${selectedData[1]}`, selectedData);
				showBidding(selectedData);
			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});
		$('#look-btn').click(function() {
			if (selectedData != null) {
				showForm(`Pengadaan ${selectedData[1]}`, selectedData);
				showBiddingDone(selectedData);

			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		let showBiddingDone = function(selectedData) {
			let data = {
				name: selectedData[4],
				contract_no: selectedData[1],
				id: selectedData[0]
			};
			$('#bidding_container').remove();
			$('#form-data').append(`
				<div id="bidding_container">
				<hr/>
				<span style="font-size:16pt">Daftar Penawaran Vendor</span>
				<span>
					<button title="Jadikan Pemenang" id="winner-btn" style="margin-left:5px;border-radius:25px;"
						class="btn btn-sm btn-success">
						<i class="fa fa-trophy"></i>
					</button>
				</span>
				<div class="table-responsive">
				<table class="table table-striped nowrap" id="bidding_history">
					<thead>
						<tr>
							<th>Vendor</th>
							<th>Harga Penawaran (IDR)</th>
							<th>Catatan</th>
							<th>Waktu Update</th>
							<th>Status</th>
						</tr>
					</thead>
				</table>
				</div>
				</div>
				
			`);

			if (filterStatus.val() != 2) {
				$('#winner-btn').remove();
			}
			$('#winner-btn').click(function(e) {
				e.preventDefault();
				if (choosenWinner != null) {
					swal({
							title: "Are you sure?",
							text: "Choose vendor as winner!",
							icon: "warning",
							buttons: ['Cancel', 'Yes.'],
							dangerMode: true
						})
						.then(function(isDelete) {
							if (isDelete) {
								$.ajax({
									url: site_url + 'procurement/choose_winner',
									type: 'POST',
									data: postDataWithCsrf.data({
										id: selectedData[0],
										id_company: choosenWinner.id_company
									}),
									dataType: 'json',
									async: false,
									success: function(data) {
										if (data.success) {
											swal(
												'Saved!',
												'Successful choose winner!',
												'success'
											);
											$('#table-data').DataTable().ajax.reload();
											$('#bidding_history').DataTable().ajax.reload();
											choosenWinner = null;
										} else {
											swal(
												'Failed!',
												'Failed choose winner!',
												'error'
											);
										}
									},
									error: function(stat, res, err) {
										$('#table-data').DataTable().ajax.reload();
										$('#bidding_history').DataTable().ajax.reload();
										//console.log(stat);
									}
								});
							}
						});

				} else {
					swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
				}
			});

			let choosenWinner = null;
			let biddingTable = $('#bidding_history').DataTable({
				"responsive": false,
				"processing": true,
				"retrieve": true,
				"select": "single",
				"order": [
					[3, "desc"]
				],
				'ajax': {
					"type": "GET",
					"url": site_url + 'procurement/get_bidding_list',
					"data": function(d) {
						d.id_project = data.id;
					},
					"dataSrc": "data"
				},
				initComplete: function() {

					// $('.view_detail').click(function () {
					//     let id = $(this).attr('data-id');
					//     let id_user = $(this).attr('id-user');
					//     viewDetailVendor(id, id_user);
					// });
				},
				drawCallback: function(setting) {
					$('.view_detail').click(function() {
						let id = $(this).attr('data-id');
						let id_user = $(this).attr('id-user');
						//viewDetailVendor(id, id_user);
					});
				},
				'columns': [{
						render: function(data, type, full, meta) {
							let view = `<span id-user="${full.id_user}" data-id='${full.id_company}' class="view_detail" >${full.vendor_name}</span>`;
							return view;

						}
					},
					{
						render: function(data, type, full, meta) {
							return full.last_price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
						}
					},
					{
						render: function(data, type, full, meta) {
							return expandableText(full.last_note, 100);
						}
					},
					{
						render: function(data, type, full, meta) {
							return full.last_update;
						}
					},
					{
						render: function(data, type, full, meta) {
							return `<badge class="badge badge-${full.is_winner=='1'?"success":"danger"}">${full.is_winner=='1'?"Pemenang":"Partisipan"}</badge>`;
						}
					},
				],
			});

			biddingTable.on('select', function(e, dt, type, indexes) {
				if (type === 'row') {
					var data = biddingTable.rows({
						selected: true
					}).data();
					choosenWinner = data[0];
				}
			});
			biddingTable.on('deselect', function(e, dt, type, indexes) {
				choosenWinner = null;
			});
		}

		let showBidding = function(selectedData) {
			let data = {
				name: selectedData[4],
				contract_no: selectedData[1],
				id: selectedData[0]
			};
			$('#bidding_container').remove();
			$('#form-data').append(`
				<hr/>
				<h5>Penawaran</h5>
				<div id="bidding_container">
				<div class="form-group row">
					<label id="" class="col-sm-3 col-form-label">Harga Penawaran (IDR) <span style="color:red">*</span></label>
					<div class="col-sm-9">
						<input type="text" id="price" class="form-control" data-inputmask-regex="^[0-9]{1,120}$" placeholder="Penawaran Harga">
					</div>
				</div>
				<div class="form-group row">
					<label id="" class="col-sm-3 col-form-label">Catatan</label>
					<div class="col-sm-9">
						<textarea type="textarea" id="note" rows="5" class="form-control" placeholder="Catatan"></textarea>
					</div>
				</div>
				<div style="text-align:right;">
					<button id="bid" class="btn btn-lg btn-success"><i class="fa fa-save"></i> Simpan</button>
				</div>
				<hr/>
				<h6>Riwayat Perubahan Penawaran</h6>
				<div class="table-responsive">
					<table class="table table-striped nowrap" id="bidding_history">
						<thead>
							<tr>
								<th>Harga (IDR)</th>
								<th>Catatan</th>
								<th>Waktu Update</th>
							</tr>
						</thead>
					</table>
				</div>
				</div>
				
			`);

			getExistingSingle = function() {
				$.ajax({
					url: site_url + 'procurement/get_bidding',
					type: 'get',
					dataType: 'json',
					data: {
						mode: 'single',
						id_project: data.id,
					},
					success: function(res) {
						if (res.data != null) {
							$('#price').val(res.data.price).trigger('change').trigger('keyup');
							$('#note').val(res.data.note);
						}
					},
					error: function(err) {
						//console.log(err);
					}

				});
			};

			getExistingSingle();

			$('#bidding_history').DataTable({
				"responsive": false,
				"select": "single",
				"processing": true,
				"retrieve": true,
				"order": [
					[2, "desc"]
				],
				'ajax': {
					"type": "GET",
					"url": site_url + 'procurement/get_bidding',
					"data": function(d) {
						d.mode = 'list';
						d.id_project = data.id;
					},
					"dataSrc": "data"
				},
				'columns': [{
						render: function(data, type, full, meta) {
							return full.price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");;
						}
					},
					{
						render: function(data, type, full, meta) {
							return expandableText(full.note, 100);
						}
					},
					{
						render: function(data, type, full, meta) {
							return full.created_at;
						}
					},
				],
			});

			// $('#price').inputmask();
			$('#price').maskNumber({
				thousands: '.',
				integer: true
			});

			$('#bid').click(function() {
				$.ajax({
					url: site_url + 'procurement/submit_bidding',
					type: 'post',
					dataType: 'json',
					data: postDataWithCsrf.data({
						id_project: data.id,
						price: $('#price').val().replace(/\./g, ''),
						note: $('#note').val()
					}),
					success: function(res) {
						if (res.success) {
							swal('Submit Data', 'Berhasil submit data!', 'success');
							$('#cancel').click();
							$('#table-data').DataTable().ajax.reload();
						} else {
							swal('Submit Data', 'Gagal submit data!', 'error');
							$('#bidding_history').DataTable().ajax.reload();
						}
					},
					error: function(err) {
						//console.log(err);
					}

				});

			});
		}

	});
</script>