$(document).ready(function () {


	$('#vendor_value').parent('div').parent('div').hide();
	$('#field_value').parent('div').parent('div').hide();
	$('#id_company_type').parent('div').parent('div').hide();
	$('#id_company_type').removeAttr('data-validation');
	$('#id_company_competency').parent('div').parent('div').hide();

	let getVendors = function () {
		$.ajax({
			url: site_url + 'procurement/search_vendor_by',
			type: 'get',
			async: false,
			dataType: 'json',
			data: {
				field: ($('#id_company_type').val() != "" ? $('#id_company_type').val() : []),
				competency: ($('#id_company_competency').val() != "" ? $('#id_company_competency').val() : null)
			},
			success: function (res) {
				let opt = '';
				if (res.data != null) {
					res.data.forEach(function (item, index) {
						opt += `<option value="${item.id}">${item.id_usr_role == 2 ? `${item.name},${item.prefix_name}` : `${item.prefix_name} ${item.name}`}</option>`;
					});
				}
				$('#target_vendors').html(opt);
			},
			error: function (xhr, res, stat) {
				alert('sorry cant get vendors');
			}
		});
	}

	$('#work_area').change(function () {
		let val = $(this).val();
		$('#project_type').trigger('change');
		$('#work_area_value').val(val);
		getVendors();
	});

	$('#id_company_type').change(function () {
		let val = $(this).val();
		$('#project_type').trigger('change');
		$('#field_value').val(val);
		getVendors();
	});

	$('#id_company_competency').change(function () {
		let val = $(this).val();
		$('#project_type').trigger('change');
		getVendors();
	});

	$('#project_type').change(function () {
		let val = $(this).val();

		if (val != 1 && val != '') {
			$('#id_company_type').attr('data-validation', 'required');
			$('#id_company_type').parent('div').parent('div').show();
			$('#id_company_competency').parent('div').parent('div').show();
		} else {
			$('#id_company_type').parent('div').parent('div').hide();
			$('#id_company_type').removeAttr('data-validation');
			$('#id_company_competency').parent('div').parent('div').hide();
			$('#target_vendors').val([]).trigger('change');
			$('#target_vendors').attr('multiple', 1);
			$('#target_vendors').parent('div').parent('div').hide();
		}

		//Company Type
		if ($('#id_company_type').val() != "") {

			if (val != 1 && val != 4 && val != '') {
				$('#target_vendors').val([]).trigger('change');
				$('#target_vendors').attr('multiple', 1);
				$('#target_vendors').parent('div').parent('div').show();
			} else if (val == 4) {
				$('#target_vendors').val([]).trigger('change');
				$('#target_vendors').removeAttr('multiple');
				$('#target_vendors').parent('div').parent('div').show();
			} else {
				$('#target_vendors').val([]).trigger('change');
				$('#target_vendors').attr('multiple', 1);
				$('#target_vendors').parent('div').parent('div').hide();
			}
		} else {
			$('#target_vendors').val([]).trigger('change');
			$('#target_vendors').attr('multiple', 1);
			$('#target_vendors').parent('div').parent('div').hide();
		}
	});

	$('#target_vendors').change(function () {
		let val = $(this).val();
		$('#vendor_value').val(val);
	});


	$('#file_attachment_proc').hide();
	callbacks.onGetEdit = function (selectedID, data) {
		// let splitComType = (data[0].id_company_type.includes(',')) ? data[0].id_company_type.split(',') : [data[0].id_company_type];
		// let valueComType = (data[0].)

		$('#id_company_type').val([data[0].id_company_type]).trigger('change');
		$('#target_vendors').val(data[0].target_vendors).trigger('change');

		$('#file_attachment_proc').hide();
		$('#file_attachment_proc').html('');
		if (data[0].attachment != null) {
			$('#file_attachment_proc').show();
			let file_upload = `<a href="${site_url + 'upload/procurement/file/' + data[0].attachment}" target="_blank">Unduh File <i class="fa fa-download"></i></a>`;
			$('#file_attachment_proc').html(file_upload);
		}

		$('#bidding_container').remove();
		$('#form-label').html(`
        <i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> ${data[0].name} - ${data[0].contract_no}`);
		$('#back').click(function () {
			$('#cancel').click()
		});
		$('#form .card-body').append(`
            <div id="bidding_container">
            <hr/>
            <h6>Daftar Penawaran Vendor</h6>
            <div class="table-responsive">
            <table class="table table-striped nowrap" id="bidding_history">
                <thead>
                    <tr>
                        <th>Vendor</th>
                        <th>Harga Penawaran (IDR)</th>
                        <th>Catatan</th>
                        <th>Waktu Update</th>
                    </tr>
                </thead>
            </table>
            </div>
            </div>
            
        `);


		$('#bidding_history').DataTable({
			"responsive": true,
			"processing": true,
			"retrieve": true,
			"order": [
				[3, "desc"]
			],
			'ajax': {
				"type": "GET",
				"url": site_url + 'procurement/get_bidding_list',
				"data": function (d) {
					d.id_project = selectedID;
				},
				"dataSrc": "data"
			},
			initComplete: function () {

				// $('.view_detail').click(function () {
				//     let id = $(this).attr('data-id');
				//     let id_user = $(this).attr('id-user');
				//     viewDetailVendor(id, id_user);
				// });
			},
			drawCallback: function (setting) {
				$('.view_detail').click(function () {
					let id = $(this).attr('data-id');
					let id_user = $(this).attr('id-user');
					viewDetailVendor(id, id_user);
				});
			},
			'columns': [{
					render: function (data, type, full, meta) {
						let view = `<a href="javascript:void()" id-user="${full.id_user}" data-id='${full.id_company}' class="view_detail" >${full.vendor_name}</a>`;
						return view;

					}
				},
				{
					render: function (data, type, full, meta) {
						return full.last_price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
					}
				},
				{
					render: function (data, type, full, meta) {
						return full.last_note;
					}
				},
				{
					render: function (data, type, full, meta) {
						return full.last_update;
					}
				},
			],
		});

	}
});
