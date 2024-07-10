<?php $this->load->view('templates/dashboard/content-title'); ?>
<style>
	.filter-card {
		padding: 10px;
		background-color: #f0f0f0;
	}
</style>

<div class="card">
	<div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div>
                    <label for="">Perusahaan</label>
                    <select name="f_company" id="f_company" class="form-control filter">
                        <?php foreach($dataCompany as $d):?>
                            <option value="<?=$d->id?>"><?=$d->name?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
        </div>
        <br/>
        <div class="table-responsive">
            <table id="dtTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Company ID</th>
                        <th>Division</th>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Project Status</th>
                        <th>Date</th>
                        <th>Estimate Price</th>
                        <th>Deal Price</th>
                        <th>ID Supplier</th>
                        <th>Supplier Name</th>
                        <th>Update At</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $('#dtTable').DataTable({
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
			"serverSide": false,
			'ajax': {
				"type": "GET",
				"url": site_url + '/rni_api/getDataProcurement',
				"data": function(d) {
					d.f_company = $('#f_company').val();
				},
				"dataSrc": "data"
			},
			'columns': [
            {
				render: function(data, type, full, meta) {
					return full.id;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.company_id;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.division;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.id_project;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.project_name;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.project_status;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.date;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.oe_price;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.deal_price;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.id_supplier;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.supplier_name;
				}
			},
            {
				render: function(data, type, full, meta) {
					return full.updateindb;
				}
			},

            ]
        });
        $('.filter').change(function(){
            $('#dtTable').DataTable().ajax.reload();
        });
    });
</script>