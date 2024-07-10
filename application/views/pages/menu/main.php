<!-- Treeview css -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendor/jstree/themes/default/style.css'); ?>">
<link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendor/nestable/nestable.css'); ?>">

<style>
	.jstree-default .jstree-anchor {
		line-height: 24px;
		height: 24px;
		color: #353c4e;
		font-size: 14.5px;
	}
</style>
<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="main-card mb-3 card" id="table_container">
	<div class="card-body">
		<div class="card-header">
			Menu Management & Privilege
			<div class="btn-actions-pane-right">
				<div class="nav">
					<a data-toggle="tab" href="#menu_privilege" class="btn-pill btn-wide btn btn-outline-alternate btn-sm show active">Role Privilege</a>
					<a data-toggle="tab" href="#menu_positioning" class="btn-pill btn-wide mr-1 ml-1 btn btn-outline-alternate btn-sm show">Management &
						Positioning</a>
				</div>
			</div>
		</div>
		<div class="tab-content">
			<div class="tab-pane show active" id="menu_privilege" role="tabpanel">
				<div id="role_table_page" style="margin-top:15px;" class="table-responsive">
					<button id="change_menu_privilege" style="margin-bottom:10px;" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i> Change Menu Privilege</button>
					<div class="dt-responsive table-responsive">
						<table id="table_user_role" class="table table-striped table-bordered nowrap">
							<thead>
								<tr>
									<th>No</th>
									<th>Role Name</th>
									<th>Role Desc</th>
									<th>Created Date</th>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
					</div>
				</div>
				<div id="role_check_page">
					<label for="" id="check_label"></label>
					<text id="id_role" hidden></text>
					<!-- Checkbox Tree card start -->
					<div class="card-block tree-view">
						<div id="checkTree">
						</div>
					</div>
					<!-- Checkbox Tree card end -->

					<hr />
					<div style="text-align:right;">
						<button type="button" id="cancel-privilege" class="btn btn-danger"><i class="fa fa-times"></i>
							Close</button>
						<button type="button" id="submit-privilege" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Submit</button>
					</div>
				</div>
			</div>
			<div class="tab-pane show" id="menu_positioning" role="tabpanel">
				<div id="nestable-menu" style="margin-top:10px; margin-bottom:10px;" class="m-b-10">
					<button title="Add New" id="modal-btn-menu" style="margin-left:5px;border-radius:25px;" class="btn btn-sm btn-primary">
						<i class="fa fa-plus"></i>
					</button>
					<button title="Expand All" data-action="expand-all" style="margin-left:5px;border-radius:25px;" class="btn btn-sm btn-success">
						<i class="fa fa-expand"></i>
					</button>
					<button title="Collapse All" data-action="collapse-all" style="margin-left:5px;border-radius:25px; color:white;" class="btn btn-sm btn-warning">
						<i class="fa fa-compress"></i>
					</button>
				</div>

				<div class="row" id="menu-tree-container">
					<div class="col-sm-12">
						<div class="cf nestable-lists">
							<div class="dd" style="max-width:100% !important;" id="nestable2">

							</div>
						</div>
					</div>
				</div>

				<div id="form-container">
					<div style="margin-top:10px;">
						<h5 class="card-title" id="form-label"></h5>
					</div>
					<?php echo form_open(null, 'id="form"'); ?>
					<div>
						<input type="text" name="id" id="id" hidden>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="label">Label<span class="required" style="color:#ff3333;">*</span>
						</label>
						<div class="col-sm-10">
							<input id="label" class="form-control" name="label" placeholder="Menu Label" type="text" required="required">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="link">Link
						</label>
						<div class="col-sm-10">
							<input id="link" class="form-control" name="link" placeholder="Menu Link" type="text">
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" for="icon">Menu Icon
						</label>
						<div class="col-sm-8">
							<input id="icon" class="form-control" name="icon" placeholder="Menu Icon, exp: pe-7s-user" type="text">
						</div>
						<div class="col-sm-2">
							<i id="icon-life" style="font-size:35px;" class="pe-7s-user"></i>
						</div>
					</div>

					<div class="form-group row">
						<input type="text" value="0" name="is_head_section" id="m_head_sec" hidden>
						<!-- <input type="text" value="0" name="have_crud" id="m_have_crud" hidden> -->
						<label class="col-sm-2 col-form-label">Atributes
						</label>
						<div class="col-sm-10">
							<input class="checkbox" type="checkbox" id="is_head_section">
							<label class="label" for="is_head_section">Head Section</label>
						</div>
					</div>

					<div class="form-group row">
						<label class="col-sm-2 col-form-label" style="color:#ff3333;">Required
							(*)</span>
						</label>
					</div>
					<hr />
					<div style="text-align:right;">
						<button type="button" id="cancel" class="btn btn-danger"> <i class="fa fa-times"></i> Cancel</button>
						<button class="btn btn-primary" type="submit" id="sendSubmit"><i class="fa fa-paper-plane"></i> Submit</button>
					</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Tree view js -->
<script type="text/javascript" src="<?php echo base_url('assets/vendor/jstree/jstree.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/vendor/jstree/jstree.checkbox.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('assets/vendor/jstree/jstree.types.js'); ?>"></script>
<!-- <script src="//cdnjs.cloudflare.com/ajax/libs/jstree/3.3.5/jstree.min.js"></script> -->

<script type="text/javascript" src="<?php echo base_url('assets/vendor/nestable/jquery.nestable.js'); ?>"></script>


<script>
	var isEdit = false;
	var menu_data = [{}];
	var list = '';
	var initiateMenu = true;
	$(document).ready(function() {
		menu_get();
		var table = $('#table_user_role');
		var menuLabel = $('#form-label');
		var form = $('#form');
		var map = [];
		var child = [];
		var parent = [];
		var index = 0;

		$('#form-container').hide();
		$('#menu-tree-container').show();

		var nesOneArray = function(menus, parent) {
			for (var i = 0; i < menus.length; i++) {
				map[index] = {
					id: menus[i].id,
					parent: parent
				};
				index++;
				if (menus[i].children != undefined) {
					nesOneArray(menus[i].children, menus[i].id);
				}
			}
		}

		var updateOutput = function(e) {
			var list = e.length ? e : $(e.target),
				output = list.data('output');
			if (window.JSON) {
				var data_output = window.JSON.stringify(list.nestable('serialize'));
				output.val(data_output);
				index = 0;
				map = [];
				nesOneArray(list.nestable('serialize'), 0);
				// console.log(map);s
				if (!initiateMenu) {
					$('#loading_content').show();
					$.ajax({
						url: site_url + 'menu/save_map_menu',
						type: 'POST',
						async: false,
						dataType: 'json',
						data: postDataWithCsrf.data({
							menu_data: map
						}),
						success: function(data, text) {
							Menu.get($('#menu_container'));
							$('#loading_content').hide();

						},
						error: function(stat, res, err) {
							$('#loading_content').hide();
						}
					});
				} else {
					initiateMenu = false;
				}
			} else {
				output.val('JSON browser support required for this demo.');
			}

			initiateMenu = false;
		};

		$('#icon').keyup(function() {
			$('#icon-life').attr('class', $(this).val());
		});


		// activate Nestable for list 2
		$('#nestable2').nestable({
				group: 1
			})
			.on('change', updateOutput);

		// output initial serialised data
		updateOutput($('#nestable2').data('output', $('#nestable2-output')));

		$('#nestable-menu').on('click', function(e) {
			var target = $(e.target),
				action = target.data('action');
			if (action === 'expand-all') {
				$('.dd').nestable('expandAll');
			}
			if (action === 'collapse-all') {
				$('.dd').nestable('collapseAll');

			}
		});


		function menu_get() {
			$.ajax({
				url: site_url + 'menu/get',
				type: 'GET',
				dataType: 'json',
				success: function(data, text) {
					list = '';
					recursive_menu(data, 0);
					$('#nestable2').html(list);
					updateOutput($('#nestable2').data('output', $('#nestable2-output')));
				},
				error: function(stat, res, err) {
					alert(err);
				}
			});
		}


		// Datatable
		var dtTable = table.DataTable({
			"aaSorting": [],
			"initComplete": function(settings, json) {
				no = 0;
			},
			"select": "single",
			"retrieve": true,
			"processing": true,
			'ajax': {
				"type": "GET",
				"url": site_url + 'master/get_data',
				"data": function(d) {
					d._table = 'sys_usr_role';
					no = 0;
				},
				"dataSrc": ""
			},
			'columns': [{
					render: function(data, type, full, meta) {
						no += 1;
						return no;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.role_name;
					}
				},
				{
					render: function(data, type, full, meta) {
						var role_desc = (full.role_desc != null && full.role_desc != '') ? full
							.role_desc : '-';
						return role_desc;
					}
				},
				{
					render: function(data, type, full, meta) {
						return full.created_at;
					}
				}
			]
		});

		$('#have_crud').click(function() {
			var check = $(this).is(':checked') ? 1 : 0;
			$('#m_have_crud').val(check);
		});
		form.submit(function(e) {
			var url_form = (isEdit == false) ? site_url + 'menu/add' : site_url + 'menu/edit';
			e.preventDefault();

			$.ajax({
				url: url_form,
				type: 'post',
				data: $("#form").serialize(),
				dataType: 'json',
				success: function(data, text) {
					var caption = (isEdit == false) ? 'input' : 'edit';
					if (data.success) {
						$('#cancel').click();
						swal({
							title: 'Saved!',
							text: 'Successful ' + caption + ' data!',
							icon: 'success',
							closeOnConfirm: true
						}).then(function() {
							menu_get();
							Menu.get($('#menu_container'));
						});
					} else {
						$('#cancel').click();
						swal({
							title: 'Failed!',
							text: 'Failed ' + caption + ' data!',
							icon: 'error'
						});
					}
					isEdit = false;


				},
				error: function(stat, res, err) {
					alert(err);
					isEdit = false;
				}
			});
		});


		// Modal Section
		$('#modal-btn-menu').click(function() {
			switchView();
			if (!isEdit) {
				form[0].reset();
				$('#icon-life').attr('class', 'pe-7s-user');
				$('#is_head_section').removeAttr('checked');
				$('#m_head_sec').val("false");
				menuLabel.html('<i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Add New Menu');
			} else {
				menuLabel.html('<i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Edit Menu');
			}
			$('#back').click(function() {
				$('#cancel').click();
			});
		});

		$('#is_head_section').change(function() {
			if ($(this).is(':checked')) {
				$(this).val('1');
			} else {
				$(this).val('0');
			}
			$('#m_head_sec').val($(this).val());
		});

		$('#cancel').click(function() {
			switchView();
			isEdit = false;
		});
		//Modal Section

		//Role Privilege Submit
		$('#submit-privilege').click(function() {
			submit_menu_role_privilege();
		});


		menu_edit = function(dataID) {
			$.ajax({
				url: site_url + 'menu/get',
				type: 'get',
				dataType: 'json',
				data: {
					id_menu: dataID
				},
				success: function(data, text) {
					isEdit = true;
					var menu = data[0];
					$('#id').val(menu.id_menu);
					$('#label').val(menu.label);
					$('#link').val(menu.link);
					$('#icon').val(menu.icon);
					$('#icon-life').attr('class', menu.icon);

					if (menu.is_head_section == 1) {
						$('#is_head_section').attr('checked', 'checked');
						$('#m_head_sec').val("1");
					} else {
						$('#is_head_section').removeAttr('checked');
						$('#m_head_sec').val("0");
					}

					if (menu.have_crud == '1') {
						$('#have_crud').prop('checked', true);;
						$('#m_have_crud').val('1');
					} else {
						$('#m_have_crud').val('0');
					}

					$('#modal-btn-menu').click();
				},
				error: function(stat, res, err) {
					alert(err);
				}
			});
		}

		menu_delete = function(dataID) {
			swal({
				title: "Are you sure?",
				text: "This data and its children will be deleted!,",
				icon: "warning",
				dangerMode: true,
				confirmButtonClass: "btn-danger",
				buttons: ['Cancel', 'Yes, delete it!']
			}).then(function(isConfirmed) {
				if (isConfirmed) {
					$.ajax({
						url: site_url + 'menu/delete',
						type: 'post',
						data: postDataWithCsrf.data({
							id: dataID
						}),
						dataType: 'json',
						success: function(data, text) {
							var caption = 'delete';
							if (data.success) {
								swal({
									title: 'Deleted!',
									text: 'Successful ' + caption + ' data!',
									icon: 'success'
								}).then(function() {
									menu_get();
									Menu.get($('#menu_container'));
								});
							} else {
								swal(
									'Failed!',
									'Failed ' + caption + ' data!',
									'error'
								);
							}
							isEdit = false;


						},
						error: function(stat, res, err) {
							alert(err);
							isEdit = false;
						}
					});
				}
			});
		}


		$.jstree.plugins.addHTML = function(options, parent) {
			this.redraw_node = function(obj, deep,
				callback, force_draw) {
				obj = parent.redraw_node.call(
					this, obj, deep, callback, force_draw
				);
				if (obj) {
					var node = this.get_node(jQuery(obj).attr('id'));
					if (node &&
						node.data &&
						("addHTML" in node.data)) {
						jQuery(obj).append(
							"<div style='margin-left: 50px'>" +
							node.data.addHTML +
							"</div>"
						);
					}
				}
				return obj;
			};
		};
		$.jstree.defaults.addHTML = {};

		$('#checkTree').jstree({
			'core': {
				'themes': {
					'responsive': false
				},
				data: []
			},
			'types': {
				"default": {
					"icon": "fa fa-folder"
				},
				"file": {
					"icon": "fa fa-file"
				}
			},
			'checkbox': {
				three_state: false,
				whole_node: false, //Used to check/uncheck node only if clicked on checkbox icon, and not on the whole node including label
				tie_selection: false
			},
			'plugins': ['types', 'checkbox', 'addHTML']
		});

		function menu_privilege(roleID) {
			$('#id_role').html(roleID);
			$('#checkTree').jstree(true).settings.core.data = [];
			$('#checkTree').jstree(true).refresh(true);
			$.ajax({
				url: site_url + 'menu/get_role_privilege',
				type: 'GET',
				dataType: 'json',
				data: {
					id_role: roleID
				},
				success: function(data, text) {
					$('#role_table_page').hide();
					$('#role_check_page').show();
					$('#checkTree').jstree(true).settings.core.data = data;
					$('#checkTree').jstree(true).refresh(true);
					$("#checkTree").jstree(true).load_node('#');
				},
				error: function(stat, res, err) {
					alert(err);
				}
			});
		}

		function submit_menu_role_privilege() {
			menu_data = [{}];
			var id_role = $('#id_role').html();
			var selectedElms = $('#checkTree').jstree("get_checked", true);
			var i = 0;
			$.each(selectedElms, function() {

				menu_data[i] = {
					id_menu: this.id,
					id_role: id_role
					// act_create: $('#' + this.id + '-create').is(':checked') ? 1 : 0,
					// act_update: $('#' + this.id + '-update').is(':checked') ? 1 : 0,
					// act_delete: $('#' + this.id + '-delete').is(':checked') ? 1 : 0
				};
				i++;
			});

			$.ajax({
				url: site_url + 'menu/save_map_menu_role',
				type: 'POST',
				dataType: 'json',
				data: postDataWithCsrf.data({
					id_role: id_role,
					menu_data: menu_data
				}),
				success: function(data, text) {
					//console.log('save menu on role :' + id_role);
					if (data.success) {
						swal({
							title: 'Saved!',
							text: 'Successful saving data!',
							icon: 'success'
						}).then(function() {
							menu_privilege(id_role);
							Menu.get($('#menu_container'));
						});
					} else {
						swal(
							'Failed!',
							'Failed saving data!',
							'error'
						);
					}


				},
				error: function(stat, res, err) {
					alert(err);
				}
			});
		}

		function recursive_menu(items, parent) {
			var child = 0;
			for (var i = 0; i < items.length; i++) {
				var menu = items[i];

				if (menu.parent == parent) {
					child += 1;
					if (child == 1) {
						list += '<ol class="dd-list">';
					}
					list += ' <li class = "dd-item dd3-item" data-id = "' + menu.id_menu + '" >';

					if (menu.count_child > 0) {
						list += '\
				<button data-action="collapse" type="button" style="display: block;">Collapse</button>\
				<button data-action="expand" type="button" style="display: none;">Expand</button>';
					}

					list += '<div class = "dd-handle dd3-handle"></div>\
				<div class = "dd3-content" > ' + menu.label + '\
				<span style="position:absolute;right:5px;">\
					<a style="cursor:pointer;color:black;" title="Edit" onclick="menu_edit(' + menu.id_menu + ')"><i class="fa fa-edit"></i></a>\
					<a style="cursor:pointer;color:black;" title="Delete" onclick="menu_delete(' + menu.id_menu + ')"><i class="fa fa-trash"></i></a>\
				</span>\
				</div>';
					recursive_menu(items, menu.id_menu);
					list += '</li>';
				}
			}
			if (child > 0) {
				list += '</ol>';
			}
		}


		var selectedID = null;
		var roleName = null;

		function resetParam() {
			isEdit = false;
			// selectedID=null;
		}


		function switchViewPrivilege(animateTime = 0) {
			// dtTable.rows('.selected').deselect();
			if ($('#role_table_page').is(':visible')) {
				$('#role_table_page').hide(animateTime);
				$('#role_check_page').show(animateTime);
			} else {
				$('#role_table_page').show(animateTime);
				$('#role_check_page').hide(animateTime);
			}
		}

		function switchView(animateTime = 0) {
			// dtTable.rows('.selected').deselect();
			if ($('#form-container').is(':visible')) {
				$('#form-container').hide(animateTime);
				$('#menu-tree-container').show(animateTime);
				$('#nestable-menu').show(animateTime);
			} else {
				$('#form-container').show(animateTime);
				$('#nestable-menu').hide(animateTime);
				$('#menu-tree-container').hide(animateTime);
			}
		}
		dtTable.on('select', function(e, dt, type, indexes) {
			if (type === 'row') {
				var data = dtTable.rows({
					selected: true
				}).data();
				selectedID = data[0].id_usr_role;
				roleName = data[0].role_name;
			}
		});

		dtTable.on('deselect', function(e, dt, type, indexes) {
			selectedID = null;
			roleName = null;
		});
		$('#role_table_page').show();
		$('#role_check_page').hide();
		// Change Privilege
		$('#change_menu_privilege').click(function() {
			if (selectedID != null && roleName != null) {
				switchViewPrivilege();
				$('#check_label').html(
					'<i style="margin:10px; font-size:16px; color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> Edit Menu Privilege for Role: <b>' +
					roleName + '</b>');
				menu_privilege(selectedID);
				$('#back').click(function() {
					switchViewPrivilege();
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

		$('#cancel-privilege').click(function() {
			switchViewPrivilege();
		});



	});
</script>