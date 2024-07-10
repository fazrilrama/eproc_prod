<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-user text-info">
                </i>
            </div>
            <div>Verifikasi Data
                <div class="page-title-subheading">Verifikasi Data Vendor
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" id="table_container">
    <div class="card-body">
        <label for="">Filter</label>
        <div class="row">
            <div class="col-md-4" style="margin-bottom: 5px;">
                <b>Pemilik Vendor</b>
                <select class="form-control filter" name="f_company_owner" id="f_company_owner">
                        <?php if( $this->session->userdata('user')['id_usr_role']==1 ){ echo '<option value="">Semua</option>';} ?>
                        <?php $data = $this->db->where('deleted_at is null')->get('m_company')->result();
                        foreach ($data as $d) {
                            if( $this->session->userdata('user')['id_usr_role']==1 ){
                                echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                            }
                            else{
                                if( $this->session->userdata('user')['id_company_owner']==$d->id){
                                    echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                                }
                            }
                        } ?>
                </select>
            </div>
            <div class="col-md-4">
                <b>Status</b>
                <select name="f_status" id="f_status" class="form-control">
                    <option value="">Non Rejected</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-4">
                <b>Jenis Vendor</b>
                <select name="jenis_vendor" id="jenis_vendor" class="form-control">
                    <option value="2">Perusahaan</option>
                    <option value="6">Perseorangan</option>
                </select>
            </div>
        </div>
        <br/>
        <div class="table-responsive">
            <table class="table table-striped table-hover nowrap" id="dTable">
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
    <div class="main-card mb-3 card">
        <div class="card-header"><i class="header-icon lnr-license icon-gradient bg-plum-plate"> </i>Verifikasi Data
            <div class="btn-actions-pane-right">
                <div class="nav">
                    <?php if($this->session->userdata('user')['id_usr_role']!=8):?>
                    <a data-toggle="tab" href="#tab-eg2-0" class="btn-pill btn-wide btn btn-outline-alternate btn-sm show <?php echo ($this->session->userdata('user')['id_usr_role']!=8)?'active':''?>">Form Verifikasi</a>
                    <?php endif;?>
                    <a data-toggle="tab" href="#tab-eg2-1" class="btn-pill btn-wide mr-1 ml-1 btn btn-outline-alternate btn-sm show <?php echo ($this->session->userdata('user')['id_usr_role']!=8)?'':'active'?>">Riwayat Verifikasi</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content">
               <?php if($this->session->userdata('user')['id_usr_role']!=8):?>
                <div class="tab-pane show <?php echo ($this->session->userdata('user')['id_usr_role']!=8)?'active':''?>" id="tab-eg2-0" role="tabpanel">
                    <form action="" id="form-verification">
                        <div class="form-group">
                            <label for="" class="form-label">Status Verifikasi<span style="color:red">*</span> </label>
                            <select name="verification_status" id="verification_status" class="form-control">
                                <option value="Pending Verification">Pending Verification</option>
                                <option value="Verified">Verified</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="" class="form-label">Catatan Verifikasi<span style="color:red">*</span> </label>
                            <span style="color:red">Wajib diisi ketika status verifikasi rejected</span>
                            <textarea name="verification_note" id="verification_note" cols="10" rows="3" class="form-control"></textarea>
                        </div>

                        <button type="button" id="btn-back" class="btn btn-default btn-lg"><i class="fa fa-arrow-left"></i> Kembali</button>
                        <button type="button" id="btn-submit" class="btn btn-success btn-lg"><i class="fa fa-paper-plane"></i> Submit</button>
                    </form>
                </div>
                <?php endif;?>
                <div class="tab-pane show <?php echo ($this->session->userdata('user')['id_usr_role']!=8)?'':'active'?>" id="tab-eg2-1" role="tabpanel">
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
            dom: 'Bfrtip',
            buttons: ['excel', 'pageLength'],
            'ajax': {
                "type": "GET",
                "url": site_url + 'verification/get_data_perusahaan',
                "data": function(d) {
                    d.jenis_vendor = $('#jenis_vendor').val();
                    d.f_status = $('#f_status').val();
                    d.f_company_owner=$('#f_company_owner').val();
                },
                "dataSrc": ""
            },
            'columns': [{
                    render: function(data, type, full, meta) {
                        return `${full.name}${full.prefix_name!=null?`,${full.prefix_name}`:''}`;
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
                        return `<button data-verification-status="${full.verification_status}" data-verification-note="${full.verification_note}" data-id="${full.id_user}" data-id-company="${full.id}" class="btn btn-info btn-edit"><i class="fa <?php echo ($this->session->userdata('user')['id_usr_role']!=8)?'fa fa-edit':'fa fa-eye'?>"></i></button>`;
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
                    ryLinx.to(`#verification/view_data_perusahaan?id_user=${id}&viewOnly=false`, function() {
                        $('#rylinx_content1 #form_input').append(`<input type="hidden" name="id_usr_role" id="id_usr_role" value="${$('#jenis_vendor').val()}"/>`);
                        $('#rylinx_content1 input').attr('disabled', 1);
                        $('#rylinx_content1 select').attr('disabled', 1);
                        $('#rylinx_content1 textarea').attr('disabled', 1);
                        $('#rylinx_content1 input[type="file"]').remove();
                        $('#rylinx_content1 span[style="color:red"]').remove();
                        $('#rylinx_content1 #persetujuan_registrasi').remove();
                    });
                });
            }
        });

        $('#jenis_vendor').change(function() {
            $('#dTable').DataTable().ajax.reload();
        });
        $('#f_status').change(function() {
            $('#dTable').DataTable().ajax.reload();
        });
        $('#f_company_owner').change(function() {
            $('#dTable').DataTable().ajax.reload();
        });

        $('#btn-back').click(function() {
            tableContainer.show();
            verificationContainer.hide();
            id_company = null;
            $('#dTable').DataTable().ajax.reload();
        });

        let doSubmit = function() {

            $.ajax({
                url: site_url + 'verification/verify_data_perusahaan',
                type: 'POST',
                data: postDataWithCsrf.data({
                    id_company: id_company,
                    verification_status: $('#verification_status').val(),
                    verification_note: $('#verification_note').val(),
                }),
                dataType: 'json',
                success: function(res) {
                    //console.log(res);
                    if (res.success) {
                        swal(
                            'Sukses Simpan!',
                            res.result,
                            'success'
                        ).then(function(value) {
                            tableContainer.show();
                            verificationContainer.hide();
                            id_company = null;
                            $('#dTable').DataTable().ajax.reload();
                        });
                    } else {
                        swal(
                            'Gagal Simpan!',
                            res.result,
                            (res.res_sap != "" && res.res_sap != null && res.res_sap.Data.Response.Type == 'D') ? 'warning' : 'error'
                        ).then(function() {
                            if (res.res_sap != "" && res.res_sap != null && res.res_sap.Data.Response.Type == 'D') {
                                let noVendor = res.res_sap.Data.Partner;
                                swal({
                                        title: "Konfirmasi Sinkronisasi",
                                        text: `Data di SAP sudah ada dengan nama ${res.res_sap.Data.Name}, Apakah Anda ingin lanjut sinkronisasi ?`,
                                        icon: "warning",
                                        buttons: true,
                                        dangerMode: false,
                                    })
                                    .then(function(isSync) {
                                        if (isSync) {
                                            $.ajax({
                                                url: site_url + 'verification/update_sap',
                                                type: 'POST',
                                                data: postDataWithCsrf.data({
                                                    id_company: id_company,
                                                    verification_status: $('#verification_status').val(),
                                                    verification_note: $('#verification_note').val(),
                                                    no_vendor: noVendor
                                                }),
                                                dataType: 'json',
                                                success: function(res) {
                                                    //console.log(res);
                                                    if (res.success) {
                                                        swal(
                                                            'Sukses Simpan!',
                                                            res.result,
                                                            'success'
                                                        ).then(function(value) {
                                                            tableContainer.show();
                                                            verificationContainer.hide();
                                                            id_company = null;
                                                            $('#dTable').DataTable().ajax.reload();
                                                        });
                                                    } else {
                                                        swal(
                                                            'Gagal Simpan!',
                                                            res.result,
                                                            'error'
                                                        );
                                                    }
                                                },
                                                error: function(err) {
                                                    alert(err);
                                                }
                                            });
                                        }
                                    });
                            }
                        });
                    }
                },
                error: function(err) {
                    alert(err);
                }
            });
        }
        $('#btn-submit').click(function() {
            if (id_company != null && $('#verification_status').val() != 'Pending Verification') {

                if ($('#verification_status').val() == 'Rejected') {
                    if ($('#verification_note').val().length > 0) {
                        doSubmit();
                    } else {
                        swal(
                            'Tidak Dapat Kirim!',
                            'Anda wajib melampirkan alasan/catatan!',
                            'warning'
                        );
                    }
                } else {
                    doSubmit();
                }
            }
        });


        $('#verification_status').change(function() {
            let val = $(this).val();
            if (val != 'Pending Verification') {
                $('#btn-submit').removeAttr('disabled');
            } else {
                $('#btn-submit').attr('disabled', 1);
            }
        });


    });
</script>