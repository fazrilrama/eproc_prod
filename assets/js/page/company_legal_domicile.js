$(document).ready(function () {
	$('#id_country').change(function () {
		let value = $(this).val();
		getMasterData.getProvince({
				id_country: (value != '' ? value : -1)
			},
			function (stat, data) {
				if (stat) {
					let opt = '<option value="">Pilih</option>';
					data.forEach(function (i) {
						opt += '<option value="' + i.id + '">' + i.name + '</option>';
					});
					$('#id_country_province').html(opt);
				}
			});
	});
});
