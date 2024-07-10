<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-user text-info">
                </i>
            </div>
            <div>Data Vendor
                <div class="page-title-subheading">Data Vendor
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="table_container">
    <div class="card-body">
        <div class="row">
            <div class="col-md-2">
                <b>Jenis Vendor</b>
            </div>
            <div class="col-md-10">
                <select name="jenis_vendor" id="jenis_vendor" class="form-control">
                    <option value="2">Perusahaan</option>
                    <option value="6">Perseorangan</option>
                </select>
            </div>
        </div>
        <br />
        <div class="table-responsive">
            <table class="table table-striped table-hover nowrap" id="dTable" nowrap>
                <thead>
                    <tr>
                        <th>Nama Vendor</th>
                        <th>Kategori Perusahaan</th>
                        <th>Alamat</th>
                        <th>Email Perusahaan</th>
                        <th>No.Telp Perusahaan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="verification_container" class="card">
    <div id="rylinx_content1">
    </div>
    <div class="card-body">
        <button class="btn btn-info btn-lg" id="btn-submit"><i class="fa fa-save"></i> Simpan</button>
    </div>
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-license icon-gradient bg-plum-plate"> </i>Verifikasi Data
            <div class="btn-actions-pane-right">
                <div class="nav">
                    <a data-toggle="tab" href="#tab-eg2-1" class="btn-pill btn-wide mr-1 ml-1 btn btn-outline-alternate btn-sm show active">Riwayat Verifikasi</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane show active" id="tab-eg2-1" role="tabpanel">
                    <div class="table-responsive">
                        <table style="width: 100%" class="table table-striped table-hover nowrap" id="riwayat_verifikasi">
                            <thead>
                                <tr>
                                    <th>Tgl</th>
                                    <th>Status</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-block text-right card-footer">
        </div>
    </div>
</div>

<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>
<script>
    $(document).ready(function() {

        let tableContainer = $('#table_container');
        let verificationContainer = $('#verification_container');
        let ryLinx = RyLinxClass('#rylinx_content1');
        let id_company = null;

        tableContainer.show();
        verificationContainer.hide();

        let dtTableVerification = $('#riwayat_verifikasi').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {},
            "responsive": false,
            "processing": true,
            "retrieve": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'verification/get_history_verification',
                "data": function(d) {
                    d.id_company = id_company
                },
                "dataSrc": ""
            },
            "order": [
                [0, "desc"]
            ],
            'columns': [{
                render: function(data, type, full, meta) {
                    return `${full.created_at}`;
                }
            }, {
                render: function(data, type, full, meta) {
                    return `${full.verification_status}`;
                },
            }, {
                render: function(data, type, full, meta) {
                    return `${full.verification_note==null?'-':full.verification_note}`;
                }
            }]
        });

        let dtTable = $('#dTable').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {},
            "responsive": false,
            "processing": true,
            "retrieve": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'required_info/get_data_perusahaan',
                "data": function(d) {
                    d.jenis_vendor = (user.role_id == 1 || user.role_id == 3 ) ? $('#jenis_vendor').val() : user.role_id;
                    d.id_user = (user.role_id == 1 || user.role_id == 3) ? null : user.id_user;

                },
                "dataSrc": ""
            },
            'columns': [{
                    render: function(data, type, full, meta) {
                        return `${full.prefix_name?full.prefix_name+' ':''}${full.name}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `${full.group_description.replace('BP ','')}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `${full.address}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `${full.email}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `${full.phone}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `<button data-verification-status="${full.verification_status}" data-verification-note="${full.verification_note}" data-id="${full.id_user}" data-id-company="${full.id}" class="btn btn-info btn-edit"><i class="fa fa-edit"></i></button>`;
                    }
                },
            ],
            "drawCallback": function(settings) {

                $('.btn-edit').click(function() {
                    let id = $(this).attr('data-id');
                    id_company = $(this).attr('data-id-company');
                    let verification_status = $(this).attr('data-verification-status');
                    let verification_note = $(this).attr('data-verification-note');
                    $('#riwayat_verifikasi').DataTable().ajax.reload();

                    $('#verification_status').val(verification_status).trigger('change');
                    // $('#verification_note').val(verification_note == 'null' ? null : verification_note);
                    tableContainer.hide();
                    verificationContainer.show();
                    ryLinx.to(`#required_info/view_data_perusahaan?id_user=${id}`, function() {
                        $('#rylinx_content1 #persetujuan_registrasi').hide();
                    });
                });

                if (user.role_id != 1  && user.role_id != 3) {
                    $('.btn-edit').click();
                }
            }
        });

        $('#jenis_vendor').change(function() {
            $('#dTable').DataTable().ajax.reload();
        });

        $('#btn-submit').click(function() {
            $('#rylinx_content1 button[type="submit"]').click();
        });

        if (user.role_id != 1 && user.role_id != 3) {
            $('#jenis_vendor').parent('div').parent('div').hide();
        }

    });
</script>