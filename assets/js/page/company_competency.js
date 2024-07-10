$(document).ready(function () {
	$('#id_competency').change(function () {
		let value = $(this).val();
		getMasterData.getCompanySubCompetency({
				id_company_competency: (value != '' ? value : -1)
			},
			function (stat, data) {
				if (stat) {
					let opt = '<option value="">Pilih</option>';
					data.forEach(function (i) {
						opt += '<option value="' + i.id + '">' + i.name + '</option>';
					});
					$('#id_company_sub_competency').html(opt);
				}
			});
	});
});
