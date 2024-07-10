<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="pe-7s-users icon-gradient bg-mean-fruit">
                </i>
            </div>
            <div>Report Penilaian Vendor
                <div class="page-title-subheading">Vendor yang telah terdaftar pada sistem
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form id="form_filter">
            <div>
                <h5>Filter</h5>
                <br />
                <div class="row">
                    <div class="col-md-2">
                        Vendor/Customer
                    </div>
                    <div class="col-md-10">
                        <select data-validation="required" class="form-control" name="f_company" id="f_company">
                            <option value="">Pilih</option>
                            <?php $data = $this->db->where('b.deleted_at is null')
                                ->join(App_Model::TBL_SAP_SYNC . ' b', 'a.id=b.id_company')
                                ->get(App_Model::TBL_COMPANY_PROFILE . ' a')
                                ->result();
                            foreach ($data as $d) {
                                echo '<option value="' . $d->id_company . '">' . $d->prefix_name . ' ' . $d->name . ' ' . $d->postfix_name . '</option>';
                            } ?>
                        </select>
                    </div>
                </div>
                <br />
                <div class="row">
                    <div class="col-md-2">
                        Waktu
                    </div>
                    <div class="col-md-4">
                        <input data-validation="required" class="form-control" name="f_start_date" id="f_start_date" type="month" />
                    </div>
                    <div class="col-md-1">
                        s/d
                    </div>
                    <div class="col-md-4">
                        <input data-validation="required" class="form-control" name="f_end_date" id="f_end_date" type="month" />
                    </div>
                </div>
                <br>
                <button id="btn_filter" type="submit" class="btn btn-lg btn-info"><i class="fa fa-filter"></i> Filter</button>
                <hr>
            </div>
        </form>

        <div class="table-responsive">
            <table id="table-report" class="table table-striped table-hover nowrap">
                <thead>
                    <th>Nama Vendor</th>
                    <th>SAP Partner No</th>
                    <th>Email</th>
                    <th>No.Telp</th>
                    <th>Proyek</th>
                    <th>Waktu Proyek</th>
                    <th>Bulan Penilaian</th>
                    <th>Aspek Mutu</th>
                    <th>Aspek Harga</th>
                    <th>Aspek Waktu</th>
                    <th>Aspek Pembayaran</th>
                    <th>Aspek K3LL</th>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $.validate({
            form: '#form_filter',
            validateOnBlur: false, // disable validation when input looses focus
            errorMessagePosition: 'top', // Instead of 'inline' which is default
            scrollToTopOnError: true, // Set this property to true on longer forms
            modules: 'location, date, security, file',
            onModulesLoaded: function() {},
            onError: function($form) {
                event.preventDefault();
            },
            onSuccess: function($form) {
                event.preventDefault();
                $('#table-report').DataTable().ajax.reload();
                return true;
            }
        });

        let dtTable = $('#table-report').DataTable({
            "aaSorting": [],
            "retrieve": true,
            "processing": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'report/get_report_feedback',
                "data": function(d) {
                    d.f_company = $('#f_company').val();
                    d.f_start_date = $('#f_start_date').val();
                    d.f_end_date = $('#f_end_date').val();
                },
                "dataSrc": ""
            },
            'columns': [{
                    render: function(data, type, full, meta) {
                        return full.company_name;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.id_sap;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.email;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.phone;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.project_name;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return full.project_start_date + ' s/d ' + full.project_end_date;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return moment(full.created_at, 'YYYY-MM-DD').format('MMM');
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return full.aspek_mutu;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.aspek_harga;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.aspek_waktu_pekerjaan;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.aspek_pembayaran;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.aspek_k3ll;
                    }
                },

            ],
            "responsive": true,
            "initComplete": function(settings, json) {},
            dom: 'Bflrtip',
            buttons: [
                'excel'
            ],
        });
    });
</script>