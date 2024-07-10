<?php $this->load->view('templates/dashboard/content-title'); ?>
<style>
	.filter-card {
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
						<!-- <option value="">Semua</option> -->
						<option value="1">Tersimpan</option>
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
						<div class="col-md-12" style="margin-bottom: 5px;">
								<b>Pemilik Vendor</b>
								<select class="form-control filter" name="f_company_owner" id="f_company_owner">
									<?php if( $this->session->userdata('user')['id_usr_role']==1 ){ echo '<option value="">Semua</option>';} ?>
									<?php $data = $this->db->where('deleted_at is null')->get('m_company')->result();
									foreach ($data as $d) {
										if( $this->session->userdata('user')['id_usr_role']==1 ){
											echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
										}
										else{
											if( $this->session->userdata('user')['id_company_owner']==$d->id){
												echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
											}
										}
									} ?>
								</select>
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
			<span id="crud_btn_container">
				<?php if (!isset($action_add) || (isset($action_add) && $action_add == 'enabled')) { ?>
					<button title="Add New" id="add-btn" style="margin-left:5px;" title="Add/Edit Data" class="btn btn-sm btn-primary">
						<i class="fa fa-plus" style="font-size:12pt"></i>
					</button>
				<?php } ?>

				<?php if (!isset($action_edit) || (isset($action_edit) && $action_edit == 'enabled')) { ?>
					<button title="Edit Data" id="edit-btn" style="margin-left:5px;" title="Add/Edit Data" class="btn btn-sm btn-success">
						<i class="fa fa-edit" style="font-size:12pt"></i>
					</button>
				<?php } ?>

				<?php if (!isset($action_delete) || (isset($action_delete) && $action_delete == 'enabled')) { ?>
					<button title="Hapus Data" id="delete-btn" style="margin-left:5px;" title="Add/Edit Data" class="btn btn-sm btn-danger">
						<i class="fa fa-trash" style="font-size:12pt"></i>
					</button>
				<?php } ?>
			</span>
			<span id="action_btn_container">
				<button id="cancel-btn" style="margin-left:5px;" title="Batalkan Pengadaan" class="btn btn-sm btn-danger">
					<i class="fa fa-times" style="font-size:12pt"></i>
				</button>

				<button title="Lihat Pengadaan" id="view-btn" style="margin-left:5px;" class="btn btn-sm btn-success">
					<i class="fa fa-eye" style="font-size:12pt"></i>
				</button>

				<button id="copy-btn" style="margin-left:5px;" title="Recreate Data" class="btn btn-sm btn-info">
					<i class="fa fa-edit" style="font-size:12pt"></i>
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
						<th>Target Vendor</th>
						<th>Area Kerja</th>
						<th>Divisi</th>
						<th>Perkiraan Nilai Proyek(Rp)</th>
						<th>Lampiran</th>
						<th>Showed In Home</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {

		var formFilterID = "filter_form";
		var filterStatus = $('#f_status');
		var filterStartMonth = $('#f_start_date');
		var filterEndMonth = $('#f_end_date');
		var filterProjectType = $('#f_project_type');
		var filterReset = $('#filter_reset');
		var copyBtn = $('#copy-btn');
		var formFilter = $(`#${formFilterID}`);
		var saveURL = '<?php echo $save_url ?>';
		var publishURL = '<?php echo $publish_url ?>';
		var getURL = '<?php echo $get_url ?>';
		var updateURL = '<?php echo $update_url ?>';
		var deleteURL = '<?php echo $delete_url ?>';
		var selectedData = null;

		var addBtn = $('#add-btn');
		var editBtn = $('#edit-btn');
		var delBtn = $('#delete-btn');

		var isVendor = (user.role_id == 2 || user.role_id == 6 || user.role_id == 7);

		var tableData = $('#table-data').DataTable({
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
					d.f_id_user = (user.role_id == 3) ? null : null;
					d.f_project_type = filterProjectType.val();
					d.f_company_owner=$('#f_company_owner').val();
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
						var status = '-';
						var badgeCondition = "info";
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
						var now = new Date().getTime();
						var distance = (new Date(full[6]).getTime()) - now;
						var days = Math.floor(distance / (1000 * 60 * 60 * 24));
						var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
						var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
						var seconds = Math.floor((distance % (1000 * 60)) / 1000);

						var isActive = distance > 0;

						var timeLeft = `<badge class="badge badge-${isActive?"success":"danger"}">${isActive?`Sisa Waktu ${days} hari ${hours} jam ${minutes} menit`:`Sisa Waktu 0 hari 0 jam 0 menit`}</badge>`;

						return `${moment(full[5]).format('D MMM Y HH:mm')} s/d ${moment(full[6]).format('D MMM Y HH:mm')}<br>${timeLeft}`;
					}
				},
				{
					render: function(data, type, full, meta) {
						var status = '-';
						var badgeCondition = "info";
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
						return (full[22] != null) ? full[22] : 'Semua';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[23] != null) ? full[23] : 'Semua';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[26] != null) ? full[26] : '-';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[27] != null) ? Number(full[27]).toLocaleString(
							'id'
						) : '-';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full[9] != null) ?
							`<a target="_blank" href="${base_url}upload/procurement/file/${full[9]}"><i class="fa fa-download"></i>Unduh</a>` :
							'-';
					}
				},
				{
					render: function(data, type, full, meta) {
						var view= (full[25]=='1') ? `Showed`:`Not Showed`;
						if(full[2]==1){
							view+=`<br>
							<button class="btn btn-sm btn-info btn-change-showed" data-state="${full[25]}" data-id="${full[0]}" >Make ${(full[25]=='0') ? `Showed`:`Not Showed`}</button>`;
						}
						return view;
					}
				},

			],
			"drawCallback": function(settings) {
				$('.btn-change-showed').off('click')
				.on('click',function(){
					var dataID=$(this).attr('data-id');
					var dataState=$(this).attr('data-state');

					if(confirm('Are you sure?')){
						$.ajax({
							url:site_url+'/procurement/changedFrontShowed',
							type:'post',
							dataType:'json',
							data:{
								id:dataID,
								showed:(dataState==1?0:1)
							},
							success:function(res){
								alert(res.message);
								$('#table-data').DataTable().ajax.reload();
							}
							,error:function(xhr,stat,err){
								alert(err);
							}
						})
					}
				});
			}
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
		var setSelectedData = function(data) {
			this.selectedData = data;
			if (tableData != null) tableData.rows('.selected').deselect();
		}.bind(this);

		var showForm = function(titleForm = "Tambah Pengadaan", loadedData = null, forCopy = false) {
			var bodyForm = `
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
						<input data-validation="required" type="text" maxLength="50" id="contract_no" name="contract_no" class="form-control"/>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Nama Proyek<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<input data-validation="required" type="text" maxLength="255" id="name" name="name" class="form-control"/>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Waktu Pengadaan<span style="color:red">*</span></label>
					</div>
					<div class="col-md-4">	
						<input data-validation="required" type="datetime-local" id="start_date" name="start_date" class="form-control"/>
					</div>
					<div class="col-md-1">
						<center><h6>s/d</h6></center>
					</div>
					<div class="col-md-4">	
						<input data-validation="required" type="datetime-local" id="end_date" name="end_date" class="form-control"/>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Divisi<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<input placeholder="conth: Procurement" data-validation="required" type="text" maxLength="255" id="division" name="division" class="form-control"/>
					</div>
				</div>
				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Estimasi Nilai Proyek(Rp)<span style="color:red">*</span></label>
					</div>
					<div class="col-md-9">	
						<input data-validation="required" type="number" maxLength="255" id="oe_price" name="oe_price" class="form-control"/>
					</div>
				</div>

				<div class="form-group row">
					<div class="col-md-3">
						<label class="form-label">Lampiran (optional)</label>
					</div>
					<div class="col-md-9">	
						<input is-mandatory="true" type="file" id="attachment" name="attachment" class="form-control" 
						data-validation="mime size"
						data-validation-max-size="50M"
						data-validation-allowing="png, jpeg, jpg, pdf"  />
						<span style="color:red;">File png,jpeg,jpg,pdf Maksimal 50MB</span>
						<br/><span id="file_attachment_proc"></span>
					</div>
				</div>

				<div class="form-group">
					<label class="form-label">Deskripsi</label>
					<textarea type="textarea" id="description" 
					name="description" 
					class="form-control 
					text-editor-master-full" 
					data-validation="required"></textarea>
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
				<button style="margin:3px;" id="save" class="btn btn-info btn-lg">
					<i class="fa fa-save"></i> Simpan
				</button>
				<button style="margin:3px;" id="publish" class="btn btn-success btn-lg">
					<i class="fa fa-paper-plane"></i> Publish
				</button>
				<button style="margin:3px;" id="cancel" class="btn btn-danger btn-lg">
					<i class="fa fa-arrow-left"></i> Kembali
				</button>`
			}).show(function(modal) {
				var mode = "save";

				var initiate = function() {
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

					tinymce.remove();

					tinymce.init({
						selector: '.text-editor-master-full',
						menubar: true,
						setup: function(ed) {
							textEditor = ed;
							ed.on('change', function() {
								ed.save();
							});
						},
						mobile: {
							theme: 'silver'
						},
						plugins: 'print powerpaste preview autolink directionality visualblocks visualchars template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
						toolbar1: 'sizeselect | fontselect |  fontsizeselect | formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
						branding: false,
						height: 400,
						image_advtab: true,
						force_br_newlines: true,
						force_p_newlines: false,
						forced_root_block: '',
						templates: [],
						fontsize_formats: "8pt 9pt 10pt 11pt 12pt 13pt 14pt 15pt 16pt 17pt 18pt 19pt 20pt 24pt 36pt",
						content_css: [base_url + 'assets/vendor/tinymce/skins/content/default/content.min.css', base_url + 'assets/vendor/tinymce/skins/content/adinda/custom-content.css']
					});

					$(document).on('focusin', function(e) {
						if ($(e.target).closest(".mce-window").length ||
							$(e.target).closest(".tox-textfield").length) {
							e.stopImmediatePropagation();
						}
					});

					$('#vendor_value').parent('div').parent('div').hide();
					$('#field_value').parent('div').parent('div').hide();
					$('#work_area_value').parent('div').parent('div').hide();

					$('#id_company_type').parent('div').parent('div').hide();
					$('#id_company_type').removeAttr('data-validation');
					$('#id_company_competency').parent('div').parent('div').hide();
					$('#file_attachment_proc').hide();

					var getVendors = function() {
						$.ajax({
							url: site_url + 'procurement/search_vendor_by',
							type: 'get',
							async: false,
							dataType: 'json',
							data: {
								withBlacklisted: $('#f_status').val()!=1?1:0,
								field: ($('#id_company_type').val() != "" ? $('#id_company_type').val() : [-1]),
								competency: ($('#id_company_competency').val() != "" ? $('#id_company_competency').val() : null)
							},
							success: function(res) {
								var opt = '';
								if (res.data != null) {
									res.data.forEach(function(item, index) {
										opt += `<option value="${item.id}">${item.id_usr_role == 2 ? `${item.name},${item.prefix_name}` : `${item.prefix_name} ${item.name}`}</option>`;
									});
								}
								$('#target_vendors').html(opt);
								$('#target_vendors').trigger('change');
							},
							error: function(xhr, res, stat) {
								alert('sorry cant get vendors');
							}
						});
					}

					$('#work_area').change(function() {
						var val = $(this).val();
						$('#work_area_value').val(val);

						var i = 0;
						var seletedText = '';
						$('#work_area option:selected').each(function() {
							if (i != 0) seletedText += ',';
							seletedText += $(this).text();
							i++;
						});
						$('#work_area_name').val(seletedText);

						getVendors();
					});

					$('#id_company_type').change(function() {
						var val = $(this).val();
						$('#field_value').val(val);

						var i = 0;
						var seletedText = '';
						$('#id_company_type option:selected').each(function() {
							if (i != 0) seletedText += ',';
							seletedText += $(this).text();
							i++;
						});
						$('#field_value_name').val(seletedText);

						getVendors();
					});

					$('#id_company_competency').change(function() {
						var val = $(this).val();
						getVendors();
					});

					$('#target_vendors').change(function() {
						var val = $(this).val();

						var i = 0;
						var seletedText = '';
						$('#target_vendors option:selected').each(function() {
							if (i != 0) seletedText += ' | ';
							seletedText += $(this).text();
							i++;
						});
						$('#vendor_value_name').val(seletedText);

						$('#vendor_value').val(val);
					});

					var hideForm = function(params = {
						field: true,
						competency: true,
						target_vendors: true,
						work_area: true
					}) {
						if (params.field == null) params.field = true;
						if (params.competency == null) params.competency = true;
						if (params.target_vendors == null) params.target_vendors = true;
						if (params.work_area == null) params.work_area = true;


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


						if ($('#project_type').val() == '4') {
							$('#target_vendors').removeAttr('multiple');
						} else {
							$('#target_vendors').attr('multiple', 1);
						}

						if (params.target_vendors) {
							$('#target_vendors').val([]).trigger('change');
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
						var val = $(this).val();

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

				var formSubmit = function(form) {
					var formData = new FormData($('#form-data')[0]);
					if (loadedData != null && !forCopy) formData.append('id', loadedData[0]);
					if (loadedData != null) formData.append('existing_attachment', loadedData[9]);
					if (loadedData != null && forCopy) formData.append('for_copy', 'true');
					// for (var p of formData) {
					// 	console.log(p[0] + ":" + p[1]);
					// }
					var formUrl = (mode == "save") ? site_url + saveURL : site_url + publishURL;
					$.ajax({
						url: formUrl,
						type: 'post',
						data: formData,
						dataType: 'json',
						processData: false,
						contentType: false,
						success: function(data, text) {
							var caption = (loadedData == null) ? 'input' : 'edit';
							var failMsg = 'Failed ' + caption + ' data!';
							if(data.message!=null) failMsg=data.message;
							if (data.success) {
								swal(
									'Saved!',
									'Successful ' + caption + ' data!',
									'success'
								);
								$('#table-data').DataTable().ajax.reload();
								$('#cancel').click();

							} else {
								if (data.result != null) failMsg = data.result;
								swal(
									'Failed!',
									failMsg,
									'error'
								);
								$('#table-data').DataTable().ajax.reload();
								$('#cancel').click();
							}
						},
						error: function(stat, res, err) {
							alert('Cant submit data, please try again.');
							//console.log(err);
						}
					});
				}

				if (loadedData != null) $('#description').val(loadedData[7]).trigger('change');
				initiate();
				if (user.role_id != 1) {
					$('#id_user').val(user.id_user).trigger('change');
					$('#id_user').attr('disabled', 1);
					$('#id_user').attr('id', 'id_user_select');
					$('#id_user_select').parent('div').parent('div').append('<input hidden name="id_user" id="id_user" value="' + user.id_user + '"></input>');
				}
				if (loadedData == null) {
					$('#project_type').val(1).trigger('change');
					$('#start_date').val(moment().format('YYYY-MM-DDTHH:mm'));
					$('#end_date').val(moment().format('YYYY-MM-DDTHH:mm'));
				} else {
					$('#id_user').val(loadedData[10]).trigger('change');
					$('#project_type').val(loadedData[2]).trigger('change');
					var field = loadedData[11];
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
					$('#division').val(loadedData['26']);
					$('#oe_price').val(loadedData['27']);


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
					var isNotExpired = moment().diff($('#end_date').val(), 'minute') < 0;
					if (isNotExpired) {
						$('#btn-submit').click();
					} else {
						swal('Informasi', 'Tidak bisa publish pengadaan dengan tanggal kadaluarsa!', 'error');
					}
				});

				$('#cancel').click(function() {
					largeModal().close();
				});


			});
		}


		$('#crud_btn_container').hide();
		$('#action_btn_container').hide();
		$('#copy-btn').hide();
		formFilter.on('submit', function(e) {
			e.preventDefault();
			$('#crud_btn_container').hide();
			$('#action_btn_container').hide();
			$('#copy-btn').hide();
			$('#cancel-btn').hide();
			if (filterStatus.val() == 1) {
				$('#crud_btn_container').show();
			} else if (filterStatus.val() == 2) {
				$('#cancel-btn').show();
				$('#action_btn_container').show();
			} else if (filterStatus.val() == 5) {
				$('#copy-btn').show();
				$('#action_btn_container').show();
			} else if (filterStatus.val() == "") {
				$('#crud_btn_container').hide();
				$('#action_btn_container').hide();
				$('#copy-btn').hide();
			} else {
				$('#action_btn_container').show();
			}
			$(`#table-data`).DataTable().ajax.reload();
		});

		filterReset.click(function() {
			formFilter.get(0).reset();
		});

		addBtn.click(function() {
			showForm(null);
		});

		delBtn.click(function() {
			if (selectedData != null) {
				swal({
						title: "Are you sure?",
						text: "This data will be deleted!",
						icon: "warning",
						buttons: ['Cancel', 'Yes, delete it.'],
						dangerMode: true
					})
					.then(function(isDelete) {
						if (isDelete) {
							$.ajax({
								url: site_url + deleteURL,
								type: 'POST',
								data: postDataWithCsrf.data({
									id: selectedData[0]
								}),
								dataType: 'json',
								async: false,
								success: function(data) {
									if (data.success) {
										swal(
											'Deleted!',
											'Successful delete data!',
											'success'
										);
										$('#table-data').DataTable().ajax.reload();
										selectedData = null;
									} else {
										swal(
											'Failed!',
											'Failed delete data!',
											'error'
										);
									}
								},
								error: function(stat, res, err) {
									$('#table-data').DataTable().ajax.reload();
									selectedData = null;
									//console.log(stat);
								}
							});
						}
					});

			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		editBtn.click(function() {
			if (selectedData != null) {
				showForm(`Pengadaan ${selectedData[1]}`, selectedData);
			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		$('#cancel-btn').click(function() {
			if (selectedData != null) {
				swal({
						title: "Are you sure?",
						text: "This data will be canceled!",
						icon: "warning",
						buttons: ['Cancel', 'Yes, cancel it.'],
						dangerMode: true
					})
					.then(function(isDelete) {
						if (isDelete) {
							$.ajax({
								url: site_url + 'procurement/cancel_procurement',
								type: 'POST',
								data: postDataWithCsrf.data({
									id: selectedData[0]
								}),
								dataType: 'json',
								async: false,
								success: function(data) {
									if (data.success) {
										swal(
											'Deleted!',
											'Successful cancel data!',
											'success'
										);
										$('#table-data').DataTable().ajax.reload();
										selectedData = null;
									} else {
										swal(
											'Failed!',
											'Failed cancel data!',
											'error'
										);
									}
								},
								error: function(stat, res, err) {
									$('#table-data').DataTable().ajax.reload();
									selectedData = null;
									//console.log(stat);
								}
							});
						}
					});

			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		$('#view-btn').click(function() {
			if (selectedData != null) {
				showForm(`Pengadaan ${selectedData[1]}`, selectedData);
				$('#form-data input').attr('disabled', 1);
				$('#form-data select').attr('disabled', 1);
				$('#form-data span[style="color:red"]').remove();
				$('#form-data input[type="file"]').remove();
				$('#form-data input[type="file"]').remove();
				$('#save').remove();
				$('#publish').remove();
				$('#description').parent('div').html('Deskripsi<br>' + selectedData[7]);
				$('#description').remove();

				if (selectedData[9] != null) {
					$('#file_attachment_proc')
						.parent('div')
						.html(`<a target="_blank" href="${base_url}upload/procurement/file/${selectedData[9]}"><i class="fa fa-download"></i>Unduh</a>`)
						.show();
				} else {
					$('#file_attachment_proc')
						.parent('div')
						.html(`Tidak Ada Lampiran`)
						.show();

				}
				tinymce.remove();

				showBidding(selectedData);
			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		copyBtn.click(function() {
			if (selectedData != null) {
				showForm(`Pengadaan ${selectedData[1]}`, selectedData, true);
			} else {
				swal('Informasi', 'Mohon pilih data terlebih dahulu.', 'warning');
			}
		});

		var showBidding = function(selectedData) {
			var data = {
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
					<button title="Jadikan Pemenang" id="winner-btn" style="margin-left:5px;"
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
							<th>Riwayat Penawaran</th>
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
											$('#cancel').click();
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

			var choosenWinner = null;
			var biddingTable = $('#bidding_history').DataTable({
				"responsive": false,
				"processing": true,
				"retrieve": true,
				"select": "single",
				"order": [
					[3, "desc"]
				],
				dom: 'Bfrtip',
				buttons: [
					'pageLength',
					{
						extend: 'excel',
						title: `[${moment().format('Y-m-d HH.mm')}] List Penawaran No.PR ${data.contract_no}`
					},
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
					//     var id = $(this).attr('data-id');
					//     var id_user = $(this).attr('id-user');
					//     viewDetailVendor(id, id_user);
					// });
				},
				drawCallback: function(setting) {
					$('.view_detail').click(function() {
						var id = $(this).attr('data-id');
						var id_user = $(this).attr('id-user');
						$('#modal-btn-lg').click();
						setTimeout(function() {
							viewDetailVendor(id, id_user, function() {
								setTimeout(function() {
									$('#view-btn').click();
								}, 800);
							});
						}, 800);
					});

					$('.view-history').click(function() {
						var id_project = $(this).attr('data-id-project');
						var id_company = $(this).attr('data-id-company');
						var vendor_name = $(this).attr('data-name');
						basicModal({
							title: `Riwayat Penawaran ${vendor_name}`,
							body: `
								<table style="width:100%;" class="table table-striped" id="view-history-bidding">
									<thead>
										<tr>		
											<th>Harga (IDR)</th>
											<th>Catatan</th>
											<th>Waktu Update</th>
										</tr>
									</thead>
								</table>
							`,
							footer: ``
						}).show(function() {
							var tblHistory = $('#view-history-bidding').DataTable({
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
										d.id_project = id_project;
										d.id_company = id_company;
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
						});
					});
				},
				'columns': [{
						render: function(data, type, full, meta) {
							var view = `<a href="javascript:void()" id-user="${full.id_user}" data-id='${full.id_company}' class="view_detail" >${full.vendor_name}</a>`;
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
					{
						render: function(data, type, full, meta) {
							return `<a href="javascript:void()" class="view-history" 
							data-id-project="${full.id_project}"
							data-id-company="${full.id_company}"
							data-name="${full.vendor_name}">
							<i class="fa fa-history"></i> Riwayat Penawaran
							</a>`;
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

		$('#filter_submit').click();

	});
</script>