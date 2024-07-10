$(document).ready(function () {
	callbacks.onSwitchView = function (isFormVisible) {
		if (isFormVisible) {
			$('.uploaded-attachment').remove();
			if ($('#attachment').attr('is-mandatory') == "true") {
				$('#attachment').attr('data-validation', 'required mime size');
			} else {
				$('#attachment').attr('data-validation', 'mime size');
			}
			$('#attachment').val('');

			if (isForVerification) {
				setSelectedID(null);
				$('input').removeAttr('disabled');
				$('textarea').removeAttr('disabled');
				$('select').removeAttr('disabled');
			}
		}
	}
	callbacks.onGetEdit = function (selectedID, data) {
		$('.uploaded-attachment').remove();
		if (data != null && data[0].attachment != null) {
			let listAttachment = '';
			listAttachment = '<div class="uploaded-attachment">\
            <a target="_blank" href="' + base_url + 'upload/company/file/' + data[0].attachment + '">\
            <i class="fa fa-download"></i> Uploaded File</a>\
            </div>';
			$('#attachment').parent('div').append(listAttachment);

			$('#attachment').attr('data-validation', 'mime size');
			$('#attachment').val('');

			if (isForVerification) {
				$('input').attr('disabled', 1);
				$('textarea').attr('disabled', 1);
				$('select').attr('disabled', 1);
				$('#verification_note').removeAttr('disabled');
				$('#csrf_token').removeAttr('disabled');
				$('#_table').removeAttr('disabled');
				$('#' + dataKey).removeAttr('disabled');

				$('#logo').parent('div').find('img').hide();
				$('#logo').parent('div').append('<img width="200px" alt="Company Logo" src="' + site_url + 'upload/company/logo/' + $('#logo').val() + '"/>');
				$('#logo').parent('div').find('textarea').hide();

				$('#company_profile').parent('div').find('a').remove();
				$('#company_profile').parent('div').append('<a target="_blank" href="' + site_url + 'upload/company/file/' + $('#company_profile').val() + '"><i class="fa fa-download"></i> Download File</a>');
				$('#company_profile').parent('div').find('textarea').hide();

				$('#attachment').attr('hidden', 1);

				$('#verification_status').removeAttr('disabled');
			}
		}
	}
});
