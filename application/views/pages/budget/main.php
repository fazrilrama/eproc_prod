<?php $this->load->view('templates/dashboard/content-title'); ?>

<div class="card">
    <div class="card-header">
        Data Budget
    </div>
    <div class="card-body">
        <h5>Filter</h5>
        <div class="row">
            <div class="col-md-4">
                Area Kerja
                <select name="f_branch" id="f_branch" class="form-control select2">
                    <option value="">Semua</option>
                    <?php
                    $data = $this->db->where('deleted_at is null')
                        ->get('m_branch_code')->result();
                    foreach ($data as $d) {
                        if($this->session->userdata('user')['id_usr_role']==3){
                            if($d->id_company_owner==$this->session->userdata('user')['id_company_owner']){
                                echo '<option value="' . $d->no_fund_center . '">' . $d->name . '</option>';
                            }
                        }
                        else{
                            echo '<option value="' . $d->no_fund_center . '">' . $d->name . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
            <div hidden class="col-md-4">
                Tipe
                <select name="f_type" id="f_type" class="form-control select2">
                    <!-- <option value="">Semua</option> -->
                    <!-- <option value="1">Operasional</option> -->
                    <option value="2">Non-Operasional</option>
                </select>
            </div>
            <div class="col-md-4">
                Tahun
                <select name="f_year" id="f_year" class="form-control select2">
                    <?php
                    $start = 2020;
                    $end = date('Y')+20;
                    for ($i = $start; $i <= $end; $i++) {
                        echo '<option '.(date('Y')==$i?'selected':'') .' value="' . $i . '">' . $i . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-4">
                Kepemilikan Budget
                <select name="f_company_owner" id="f_company_owner" class="form-control select2">
                    <?php if($this->session->userdata('user')['id_usr_role']==1){
                        echo '<option value="">Semua</option>';
                    }?>
                    <?php
                    foreach( $this->db->where('deleted_at is null')->get('m_company')->result()  as $d){
                        if($this->session->userdata('user')['id_usr_role']==1){
                            echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                        }
                        else{
                            if($d->id==$this->session->userdata('user')['id_company_owner']){
                                echo '<option value="' . $d->id . '">' . $d->codename . '</option>';
                            }
                        }
                    }?>
                </select>
            </div>

        </div>
        <hr>
        <button id="add-budget-op" class=" btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Budget</button>
        <button id="edit-budget-op" class=" btn btn-sm btn-info"><i class="fa fa-edit"></i> Edit Budget</button>
        <hr>
        <div class="table-responsive">
            <table id="table_data" class="table table-hover table-borderless table-striped">
                <thead>
                    <tr>
                        <th>Area Kerja</th>
                        <th>No FundsCenter</th>
                        <th>Tahun</th>
                        <th>Tipe</th>
                        <th>Biaya Tersedia</th>
                        <th>Waktu Update</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        var getURL = '<?php echo $get_url ?>';
        var updateURL = '<?php echo $update_url ?>';
        var selectedData = null;
        var dtTable = $('#table_data').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {},
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
            "serverSide": true,
            'ajax': {
                "type": "GET",
                "url": site_url + getURL,
                "data": function(d) {
                    d.f_branch = $('#f_branch').val();
                    d.f_type = $('#f_type').val();
                    d.f_year = $('#f_year').val();
                    d.f_company_owner=$('#f_company_owner').val();
                },
                "dataSrc": "data"
            },
            'columns': [{
                render: function(data, type, full, meta) {
                    return full[11];
                }
            }, {
                render: function(data, type, full, meta) {
                    return full[2];
                }
            }, {
                render: function(data, type, full, meta) {
                    return full[7];
                }
            }, {
                render: function(data, type, full, meta) {
                    return full[13];
                }
            }, {
                render: function(data, type, full, meta) {
                    var budget = `<b>Rp ${full[5].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}</b>`;
                    var action = '';
                    return budget + action;
                }
            }, {
                render: function(data, type, full, meta) {
                    var updateTime = (full[9] != null) ? full[9] : full[8];
                    return moment(updateTime).format('d MMM Y HH:mm:ss');
                }
            }]
        });

        var btnSynSAP = $('#sync-to-sap');
        var btnEditBudgetOP = $('#edit-budget-op');

        $('#f_branch').change(function() {
            $('#table_data').DataTable().ajax.reload();
        });
        $('#f_year').change(function() {
            $('#table_data').DataTable().ajax.reload();
        });
        $('#f_type').change(function() {
            $('#table_data').DataTable().ajax.reload();
        });

        $('#f_company_owner').change(function() {
            $('#table_data').DataTable().ajax.reload();
        });

        $('#f_type').trigger('change');

        var modalSAP = function(data, type) {
            largeModal({
                title: `Sync SAP Budget Non-Operasional ${data[11]}`,
                body: `<div>
                Budget Pada Sistem<br>
                <h3 id="budget_system">Rp ${data[5].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}</h3>
                Budget Pada SAP<br>
                <h3 id="budget_sap"></h3>
                Budget To Sync<br>
                <input style="font-size:16pt" class="form-control" type="number" min="1" id="budget_custom"></input>
                
                <hr>
                <h5>Data Transaksi Belum Selesai</h5>
                <div class="table-reponsive">
                    <table style="width:100%" id="table_data_trans" class="table table-striped nowrap">
                        <thead>
                            <tr>
                                <th>TRX ID</th>
                                <th>Vendor</th>
                                <th>Produk</th>
                                <th>Harga</th>
                                <th>Kuantitas</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                </div>`,
                footer: `<button id="sync" class="btn btn-lg btn-success"><i class="fa fa-sync"></i> Sync Budget</button>
                <button id="close" class="btn btn-md btn-danger"><i class="fa fa-times"></i> Batal</button>`
            }).show(function(modal) {
                if (type == 2) {

                    $.ajax({
                        url: site_url + 'budget/get_sap_data',
                        type: 'post',
                        dataType: 'json',
                        data: postDataWithCsrf.data({
                            year: data[7],
                            fund_id: data[2]
                        }),
                        success: function(res) {
                            var budgetSAP = `Rp `;
                            if (res.success) {
                                budgetSAP += res.data.available.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                            } else {
                                budgetSAP += '0';
                            }
                            $('#budget_sap').html(budgetSAP);
                            $('#budget_custom').val(res.data.available);
                        },
                        error: function(xhr, res, stat) {
                            //console.log(stat);
                            alert('cant get sap data!');
                        }
                    });
                } else {
                    $('#budget_sap').html('Data tidak ditemukan.');
                    $('#budget_custom').val(data[5]);
                }
                $('#close').click(function() {
                    largeModal().close();
                });
                $('#sync').click(function() {
                    if ($('#budget_custom').val() != null && $('#budget_custom').val().length > 0) {

                        $.ajax({
                            url: site_url + 'budget/edit_data',
                            type: 'post',
                            dataType: 'json',
                            data: postDataWithCsrf.data({
                                id: data[0],
                                available: $('#budget_custom').val()
                            }),
                            success: function(res) {
                                if (res.success) {
                                    swal('Data Saved!', 'Data budget has been saved.', 'success').then(function() {
                                        $('#table_data').DataTable().ajax.reload();
                                        largeModal().close();
                                    });
                                } else {
                                    swal('Data Failed To Save!', 'Data budget has been failed to save.', 'error');
                                }
                            },
                            error: function(xhr, res, stat) {

                            }
                        });
                    } else {
                        swal('Info', 'Data budget tidak valid!', 'warning');
                    }
                });
                $('#table_data_trans').DataTable({
                    "aaSorting": [],
                    "initComplete": function(settings, json) {},
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
                    "serverSide": true,
                    'ajax': {
                        "type": "GET",
                        "url": site_url + 'budget/get_undone_transaction',
                        "data": function(d) {
                            d.fund_id = data[2];
                        },
                        "dataSrc": "data"
                    },
                    'columns': [{
                            render: function(data, type, full, meta) {
                                return full[6];
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                return full[20] + ' ' + full[19];
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                return full[18];
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                
                                return 'Rp ' + full[17].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                return full[3];
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                return 'Rp ' + full[16].toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.");
                            }
                        },
                        {
                            render: function(data, type, full, meta) {
                                var status = full[4];
                                switch (status) {
                                    case '1':
                                        status = `<span class="badge badge-warning" style="color:white;">Menunggu Persetujuan</span>`;
                                        break;
                                    case '2':
                                        status = `<span class="badge badge-info" style="color:white;">Pemesanan Diterima</span>`;
                                        break;
                                    case '3':
                                        status = `<span class="badge badge-danger" style="color:white;">Ditolak</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '4':
                                        status = `<span class="badge badge-primary" style="color:white;">Diproses Vendor</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '5':
                                        status = `<span class="badge badge-success" style="color:white;">Diterima Pemesan</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '6':
                                        status = `<span class="badge badge-danger" style="color:white;">Ditolak Vendor</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '7':
                                        status = `<span class="badge badge-success" style="color:white;">Pemesanan Selesai</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '8':
                                        status = `<span class="badge badge-danger" style="color:white;">Ditolak Pemesan</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                    case '9':
                                        status = `<span class="badge badge-danger" style="color:white;">GR Ditolak GA/Proc Kantor Pusat</span><br>Catatan:<br>${expandableText(full[4],15,'Catatan Approval','-','...selengkapnya')}`;
                                        break;
                                }

                                return status;
                            }
                        }
                    ]
                });
            });
        };

        $('#add-budget-op').click(function(){
            basicModal({
                title:'Add Budget',
                body:`<form id="formBudget">
                    <div>
                        <label>No Fund Center</label>
                        <select style="width:100%" required class="form-control" id="no_fund_center" name="no_fund_center">
                            <?php foreach($this->db->where('deleted_at is null')->get('m_branch_code')->result() as $d):?>
                                <option value="<?=$d->no_fund_center?>"><?=$d->name?> (<?=$d->no_fund_center?>)</option>
                            <?php endforeach;?>
                        </select>
                    </div>
                    <div>
                        <label>Budget Available</label>
                        <input type="number" required id="available" name="available" class="form-control"></input>
                    </div>
                    <div>
                        <label>Tahun</label>
                        <input type="number" maxlength="4" required id="time" name="time" class="form-control"></input>
                    </div>
                    <hr>
                    <button class="btn btn-md btn-success"><i class="fa fa-save"></i> Submit</button>
                </form>`,
                footer:``,
            }).show(function(modal){
                $('#no_fund_center').select2({
                    dropdownParent:modal
                });

                $('#formBudget').submit(function(e){
                    e.preventDefault();
                    var fd=new FormData($(this)[0]);
                    $.ajax({
                        url:site_url+'budget/addBudget',
                        type:'post',
                        data:fd,
                        dataType:'json',
                        processData:false,
                        contentType:false,
                        success:function(res){
                            swal('Informasi', res.message, res.success?'success':'error');
                            basicModal().close();
                            if(res.success) $('#table_data').DataTable().ajax.reload();
                        },
                        error:function(xhr,stat,err){
                            swal('Informasi', 'Terjadi kesalahan sistem, silahkan coba kembali.', 'error');
                            basicModal().close();
                        }
                    });
                });
            });
        });

        btnEditBudgetOP.click(function() {
            if (selectedData != null) {
                modalSAP(selectedData, selectedData[1]);
            } else {
                swal('Informasi', 'Mohon pilih data budget terlebih dahulu.', 'warning');
            }
        });

        dtTable.on('select', function(e, dt, type, indexes) {
            if (type === 'row') {
                var data = dtTable.rows({
                    selected: true
                }).data();
                selectedData = data[0];
                //console.log(selectedData);
            }
        });
        dtTable.on('deselect', function(e, dt, type, indexes) {
            selectedData = null;
        });
    });
</script>