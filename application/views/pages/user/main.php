<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="main-card mb-3 card" id="table_container">


	<div class="card-body">
		<div class="card-header mb-1">
			<h5 class="card-title">User
				<button title="Edit Data" id="edit-btn" style="margin-left:5px;border-radius:25px;" title="Add/Edit Data" class="btn btn-sm btn-success">
					<i class="fa fa-edit"></i>
				</button>
			</h5>
		</div>
		<div class="table-responsive">
			<table width="100%" id="table_data" class="mb-0 table table-striped responsive table-bordered no-wrap">
				<thead>
					<tr>
						<th>NIK</th>
						<th>Name</th>
						<th>Birthday</th>
						<th>Work Area</th>
						<th>Position</th>
						<th>Role</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
		</div>
	</div>
</div>


<div class="main-card mb-3 card" id="main_container">
	<div class="card-header mb-1">
		<h5 class="card-title" id="form-label">User</h5>
	</div>
	<div class="card-body">
		<div id="form-container">
			<?php echo form_open(null, 'id="form"') ?>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">NIK</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="nik" id="nik" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Nama</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="emp_fullname" id="emp_fullname" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Tgl Lahir</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="emp_dob" id="emp_dob" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Area</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="subarea" id="subarea" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Unit</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="unit" id="unit" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Posisi</label>
				<div class="col-sm-10">
					<input class="form-control" disabled name="emp_position" id="emp_position" type="none">
				</div>
			</div>

			<div class="form-gruop row">
				<label class="col-sm-2 col-form-label" for="">Role User <span style="color:red;">*</span></label>
				<div class="col-sm-10">
					<select class="form-control" name="role_id" id="role_id" required></select>
				</div>
			</div>
			<br>
			<div class="form-gruop row">
				<label class="col-sm-2 col-form-label" for="">Status <span style="color:red;">*</span></label>
				<div class="col-sm-10">
					<select class="form-control" name="id_status" id="id_status" required>
						<option value="1">Active</option>
						<option value="2">Inactive</option>
					</select>
				</div>
			</div>
			<br>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for=""> Password
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="password" name="password_confirmation" pattern="(?=.*[\d,`~!@#$%^&*);'[\]\x22{}.]).{8,16}">
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for=""> Ulangi Password
				</label>
				<div class="col-sm-10" id="retype_password_container">
					<input class="form-control" type="password" data-validation="confirmation" data-validation-error-msg-container="#retype_password_container" data-validation-error-msg="Ulangi password tidak valid" name="password" id="retype_password">
				</div>
			</div>

			<label class="" style="color:#ff3333;">Required (*)</span></label>
			<br>
			Note:
			<br>
			<ul>
				<li>
					<span style="color:grey;">Only fill password if wanna change.</span>
				</li>
				<li>
					<span style="color:grey;">Personal data can be change only in <a target="_blank" href="http://siska.bgrlogistik.id">SISKA System</a>.</span>
				</li>
			</ul>


			<hr />
			<div class="row">
				<div class="col-md-12" style="text-align:right;">
					<button type="button" id="cancel" class="btn btn-danger"> <i class="fa fa-times"></i>
						Cancel</button>
					<button type="submit" id="submit" class="btn btn-primary"> <i class="fa fa-paper-plane"></i>
						Submit</button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>


<!-- <script src="<?php echo base_url('assets/js/page/user_role.js'); ?>" /> -->

<script>
	var isEdit = false;
	var modalLabel = $('#form-label');
	var form = $('#form');
	var formContainer = $('#main_container');
	var tableContainer = $('#table_container');
	var table = $('#table_data');
	var no = 0;
	var selectedID = null;

	$('document').ready(function() {

		var dtTable = table.DataTable({
			"aaSorting": [],
			"initComplete": function(settings, json) {
				no = 0;
				$('#table_data_filter input').unbind();
				$('#table_data_filter input').keyup(function(e) {
					if (e.keyCode == 13) /* if enter is pressed */ {
						dtTable.search($(this).val()).draw();
					}
				});
			},
			"columnDefs": [{
					"orderable": false,
					"targets": 5
				},
				{
					"orderable": false,
					"targets": 6
				}
			],
			"responsive": true,
			"select": "single",
			"processing": true,
			"retrieve": true,
			"serverSide": true,
			'ajax': {
				"type": "GET",
				"url": site_url + 'user/all',
				"data": function(d) {},
				"dataSrc": "data"
			},
			'columns': [{
					render: function(data, type, full, meta) {
						return full.nik;
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full.siska != null) ? full.siska.emp_fullname : '-';
					}
				},
				{
					render: function(data, type, full, meta) {
						return (full.siska != null) ? full.siska.emp_dob : '-';
					}
				},
				{
					render: function(data, type, full, meta) {
						var position = '-';
						if (full.siska.subarea_ket != null) {
							position = full.siska.subarea_ket;
						}
						return position;

					}
				},
				{
					render: function(data, type, full, meta) {
						var position = '-';
						if (full.siska.pos_objnm != null) {
							position = full.siska.pos_objnm;
						}
						return position;

					}
				},
				{
					render: function(data, type, full, meta) {
						return full.role_name;
					}
				},
				{
					render: function(data, type, full, meta) {
						var btnType = (full.id_usr_status != 1) ? 'btn-danger' : 'btn-success';
						var btnIcon = (full.id_usr_status != 1) ? 'fa fa-times' : 'fa fa-check';
						var btnTitle = (full.id_usr_status == 1) ? 'Nonaktifkan user' :
							'Aktifkan user';
						var btnAct = (full.id_usr_status == 1) ? "'Inactivated'" : "'Activated'";
						// var btn='<button onclick="switch_activation(this,'+btnAct+',\''+full.nik+'\')" style="cursor:pointer;" title="'+btnTitle+'" class="btn btn-sm '+btnType+'"><i class="'+btnIcon+'"></i> '+full.status_name+'</button>';
						var btn = '<span class="badge badge-' + ((full.id_usr_status == 1) ?
								'success' : 'danger') + '" style="color:white;">' + full
							.status_name + '</span>';
						return btn;
					}
				}
			]
		});

		//Selectable
		dtTable.on('select', function(e, dt, type, indexes) {
			if (type === 'row') {
				var data = dtTable.rows({
					selected: true
				}).data();
				selectedID = data[0].nik;
			}
		});

		dtTable.on('deselect', function(e, dt, type, indexes) {
			selectedID = null;
		});
		//Selectable

		var FormSetup = function() {
			return {
				do: function() {
					$.validate({
						form: '#form',
						errorMessagePosition: 'top',
						modules: 'location, date, security, file',
						onModulesLoaded: function() {},
						onError: function($form) {
							alert('Validation of form ' + $form.attr('id') + ' failed!');
						},
						onSuccess: function($form) {
							return true; // false Will stop the submission of the form
						}
					});
					//Management Section
					switchView();

					function switchView(animateTime = 0) {
						// dtTable.rows('.selected').deselect();
						if (formContainer.is(':visible')) {
							formContainer.hide(animateTime);
							tableContainer.show(animateTime);
						} else {
							formContainer.show(animateTime);
							tableContainer.hide(animateTime);
						}
					}
					form.on('submit', function(e) {
						var url_form = (isEdit == false) ? site_url + 'user/add' : site_url +
							'user/edit'
						e.preventDefault();
						$.ajax({
							url: url_form,
							type: 'POST',
							dataType: 'json',
							data: form.serialize() + '&nik=' + selectedID,
							success: function(res) {
								if (res.success) {
									$('#table_data').DataTable().ajax.reload();
									swal(
										'Saved!',
										'Successful edit user!',
										'success'
									);
									$('#table_data').DataTable().ajax.reload();
								}

								resetParam();
								selectedID = null;
								switchView();
							}
						});
					});
					$('#cancel').click(function() {
						switchView();
						resetParam();
					});
					$('#edit-btn').click(function() {
						if (selectedID != null) {
							switchView();
							modalLabel.html(
								'<i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Edit User'
							);
							edit_data(selectedID);
							$('#back').click(function() {
								switchView();
								resetParam();
							});
						} else {
							swal({
								title: "Edit Data",
								text: "Please select at least 1 data!",
								icon: "warning",
								button: "OK",
							});
						}
					});

					function edit_data(idData) {
						$('#nik').val(null);
						$('#emp_fullname').val(null);
						$('#emp_dob').val(null);
						$('#emp_position').val(null);
						$('#id_status').val(null);
						$('#subarea').val(null);
						$('#unit').val(null);
						$('#password').val(null);
						$('#retype_password').val(null);

						$.getJSON(site_url + 'user_role/get', function(res) {
							var opt = '';
							res.forEach(function(e) {
								opt += '<option value="' + e.id_usr_role + '">' + e
									.role_name + '</option>';
							});
							$('#role_id').html(opt);


							$.ajax({
								url: site_url + 'user/register_user',
								type: 'POST',
								data: postDataWithCsrf.data({
									id: selectedID
								}),
								dataType: 'json',
								async: true,
								success: function(res) {
									if (res.success) {
										isEdit = true;
										$.get(site_url + 'user/get/' + selectedID, function(
											res) {
											if (res != []) {
												var data = JSON.parse(res);
												data = data != null && data
													.length > 0 ? data[0] :
													null;
												$('#nik').val(data.nik);
												$('#role_id').val(data
													.id_usr_role);
												$('#id_status').val(data
													.id_usr_status);
												$('#emp_fullname').val(data
													.siska.emp_fullname);
												$('#emp_dob').val(data.siska.emp_dob);

												var position = data.siska.pos_objnm != null ?
													data.siska.pos_objnm + " (" +
													data.siska.pos_obj + ")" : '-';
												$('#emp_position').val(position);
												var subarea = data.siska.subarea_ket != null ?
													data.siska.subarea_ket : '-';
												$('#subarea').val(subarea);
												var unit = data.siska.unit_objnm ?
													data.siska.unit_objnm + " (" + data
													.siska.unit_obj + ")" : '-';
												$('#unit').val(unit);
											}
										});
									}
								}
							});
						});
					}
					//Management Section
				}
			}
		}();

		FormSetup.do();
	});

	var resetParam = function() {
		isEdit = false;
		// selectedID=null;
	}
	var switch_activation = function(context, action, idData) {
		// console.log('clicked');
		swal({
				title: "Are you sure?",
				text: "This user data will be " + action + ".",
				icon: "warning",
				buttons: ['Cancel', 'Yes, make ' + action],
				dangerMode: (action != "Activated")
			})
			.then(function(isDelete) {
				if (isDelete) {
					$.ajax({
						url: site_url + "user/switch_activation",
						type: 'POST',
						data: postDataWithCsrf.data({
							id: idData,
							action: action
						}),
						dataType: 'json',
						async: false,
						success: function(data, text) {
							if (data.success) {
								swal(
									'Saved!',
									'Successful ' + action + ' user!',
									'success'
								);
								$('#table_data').DataTable().ajax.reload();
								resetParam();
								selectedID = null;
							}
							postDataWithCsrf.setToken(data.regen_csrf_token);
						},
						error: function(stat, res, err) {
							resetParam();
							selectedID = null;
							alert(err);
							postDataWithCsrf.setToken(data.regen_csrf_token);
						}
					});
				}
			});
	}
</script>