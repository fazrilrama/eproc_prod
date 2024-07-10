$(document).ready(function () {

	let get_competency = function (value) {
		$.ajax({
			url: site_url + 'catalogue_manage/get_competencies_by_company',
			type: 'GET',
			async: false,
			data: {
				id_company: value
			},
			dataType: 'json',
			success: function (res) {
				let opt = `<option value="">Pilih</option>`;
				res.map(function (item, i) {
					opt += `<option parent_competency="${item.id_competency}" value="${item.id_company_sub_competency}">${item.competency_name} - ${item.sub_competency_name}</option>`;
				});
				$('#id_sub_competencies').html(opt);
			}
		})
	};

	$('#id_company').change(function () {
		let value = $(this).val();
		get_competency(value);
	});


	$('#id_sub_competencies').change(function () {
		let value = $(this).val();
		$.ajax({
			url: site_url + 'catalogue_manage/check_is_electronic',
			type: 'GET',
			async: false,
			data: {
				id_sub_competency: value
			},
			dataType: 'json',
			success: function (res) {
				if (res.is_electronic) {
					$('#guarantee_file').attr('data-validation', 'mime size required');
				} else {
					$('#guarantee_file').attr('data-validation', 'mime size');
				}
			}
		})
	});

	callbacks.onSwitchView = function (isFormVisible) {
		if (isFormVisible) {

			$('.uploaded-attachment').remove();

			if ($('#picture1').attr('is-mandatory') == "true") {
				$('#picture1').attr('data-validation', 'required mime size');
			} else {
				$('#picture1').attr('data-validation', 'mime size');
			}
			$('#picture1').val('');

			if ($('#picture2').attr('is-mandatory') == "true") {
				$('#picture2').attr('data-validation', 'required mime size');
			} else {
				$('#picture2').attr('data-validation', 'mime size');
			}
			$('#picture2').val('');

			if ($('#picture3').attr('is-mandatory') == "true") {
				$('#picture3').attr('data-validation', 'required mime size');
			} else {
				$('#picture3').attr('data-validation', 'mime size');
			}
			$('#picture3').val('');

			if ($('#picture4').attr('is-mandatory') == "true") {
				$('#picture4').attr('data-validation', 'required mime size');
			} else {
				$('#picture4').attr('data-validation', 'mime size');
			}
			$('#picture4').val('');

			if ($('#picture5').attr('is-mandatory') == "true") {
				$('#picture5').attr('data-validation', 'required mime size');
			} else {
				$('#picture5').attr('data-validation', 'mime size');
			}
			$('#picture5').val('');

		}


		$('#is_negotiable').parent('div').parent('div').hide();
		$('#price_after_discount').parent('div').parent('div').hide();
		$('#final_price').parent('div').parent('div').hide();
		$('#is_negotiable').val(0).trigger('change');
		$('#main_price').change(function () {
			$('#price_after_discount').val($(this).val());
			$('#final_price').val($(this).val());
		});
	}

	callbacks.onGetEdit = function (selectedID, data) {
		$('.uploaded-attachment').remove();
		if (data != null) {
			if (data[0].picture1 != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <img style="height:150px;width:auto;" target="_blank" src="' + base_url + 'upload/company/file/' + data[0].picture1 + '"/>\
                </div>';
				$('#picture1').parent('div').append(listAttachment);
				$('#picture1').attr('data-validation', 'mime size');
				$('#picture1').val('');
			}
			if (data[0].picture2 != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <img style="height:150px;width:auto;" target="_blank" src="' + base_url + 'upload/company/file/' + data[0].picture2 + '"/>\
                </div>';
				$('#picture2').parent('div').append(listAttachment);
				$('#picture2').attr('data-validation', 'mime size');
				$('#picture2').val('');
			}
			if (data[0].picture3 != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <img style="height:150px;width:auto;" target="_blank" src="' + base_url + 'upload/company/file/' + data[0].picture3 + '"/>\
                </div>';
				$('#picture3').parent('div').append(listAttachment);
				$('#picture3').attr('data-validation', 'mime size');
				$('#picture3').val('');
			}
			if (data[0].picture4 != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <img style="height:150px;width:auto;" target="_blank" src="' + base_url + 'upload/company/file/' + data[0].picture4 + '"/>\
                </div>';
				$('#picture4').parent('div').append(listAttachment);
				$('#picture4').attr('data-validation', 'mime size');
				$('#picture4').val('');
			}
			if (data[0].picture5 != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <img style="height:150px;width:auto;" target="_blank" src="' + base_url + 'upload/company/file/' + data[0].picture5 + '"/>\
                </div>';
				$('#picture5').parent('div').append(listAttachment);
				$('#picture5').attr('data-validation', 'mime size');
				$('#picture5').val('');
			}
			if (data[0].guarantee_file != null) {
				let listAttachment = '';
				listAttachment = '<div class="uploaded-attachment">\
                <a style="height:150px;width:auto;" target="_blank" href="' + base_url + 'upload/company/file/' + data[0].guarantee_file + '"><i class="fa fa-download"></i></a>\
                </div>';
				$('#guarantee_file').parent('div').append(listAttachment);
				$('#guarantee_file').attr('data-validation', 'mime size');
				$('#guarantee_file').val('');
			}
		}
	}
});
