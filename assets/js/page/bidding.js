$(document).ready(function () {

	callbacks.onGetEdit = function (selectedID, data) {
		$('[style="color:#ff3333;"]').remove();
		$('[type="submit"]').attr('type', 'button').attr('id', 'bid');
		$('#bidding_container').remove();
		$('#form-label').html(`
        <i style="color:blue;cursor:pointer;" id="back" class="fa fa-arrow-left"></i> ${data[0].name} - ${data[0].contract_no}`);
		$('#back').click(function () {
			$('#cancel').click()
		});
		$('#form .card-body').append(`
            <div id="bidding_container">
            <div class="form-group row">
                <label id="" class="col-sm-2 col-form-label">Harga Penawaran (IDR) <span style="color:red">*</span></label>
                <div class="col-sm-10">
                    <input type="text" id="price" class="form-control" data-inputmask-regex="^[0-9]{1,120}$" placeholder="Penawaran Harga">
                </div>
            </div>
            <div class="form-group row">
                <label id="" class="col-sm-2 col-form-label">Catatan</label>
                <div class="col-sm-10">
                    <textarea type="textarea" id="note" rows="5" class="form-control" placeholder="Catatan"></textarea>
                </div>
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

		getExistingSingle = function () {
			$.ajax({
				url: site_url + 'procurement/get_bidding',
				type: 'get',
				dataType: 'json',
				data: {
					mode: 'single',
					id_project: selectedID,
				},
				success: function (res) {
					if (res.data != null) {
						$('#price').val(res.data.price).trigger('change').trigger('keyup');
						$('#note').val(res.data.note);
					}
				},
				error: function (err) {
					console.log(err);
				}

			});
		};

		getExistingSingle();

		$('#bidding_history').DataTable({
			"responsive": true,
			"select": "single",
			"processing": true,
			"retrieve": true,
			"order": [
				[2, "desc"]
			],
			'ajax': {
				"type": "GET",
				"url": site_url + 'procurement/get_bidding',
				"data": function (d) {
					d.mode = 'list';
					d.id_project = selectedID;
				},
				"dataSrc": "data"
			},
			'columns': [{
					render: function (data, type, full, meta) {
						return full.price.replace(/\B(?=(\d{3})+(?!\d))/g, ".");;
					}
				},
				{
					render: function (data, type, full, meta) {
						return full.note;
					}
				},
				{
					render: function (data, type, full, meta) {
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

		$('#bid').click(function () {
			$.ajax({
				url: site_url + 'procurement/submit_bidding',
				type: 'post',
				dataType: 'json',
				data: postDataWithCsrf.data({
					id_project: selectedID,
					price: $('#price').val().replace(/\./g, ''),
					note: $('#note').val()
				}),
				success: function (res) {
					if (res.success) {
						swal('Submit Data', 'Berhasil submit data!', 'success');
						$('#cancel').click();
						$('#table-data').DataTable().ajax.reload();
					} else {
						swal('Submit Data', 'Gagal submit data!', 'error');
						$('#bidding_history').DataTable().ajax.reload();
					}
				},
				error: function (err) {
					console.log(err);
				}

			});

		});

	}
});
