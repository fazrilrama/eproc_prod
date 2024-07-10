$(document).ready(function () {

	callbacks.onGetEdit = function (selectedID, data) {
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

		$('#verification_status').removeAttr('disabled');
		// $('#id_group').removeAttr('disabled');

	};

	callbacks.onSwitchView = function (isFormVisible) {
		if (isFormVisible) {
			setSelectedID(null);
			$('input').removeAttr('disabled');
			$('textarea').removeAttr('disabled');
			$('select').removeAttr('disabled');
		}
	};

	callbacks.onTableRedraw = function (settings) {}

});
