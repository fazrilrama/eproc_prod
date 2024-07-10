<?php $this->load->view('templates/dashboard/content-title'); ?>


<div class="card">
    <div class="card-header">
        <h5 style="width:50%;">Daftar Pemesanan</h5>
    </div>
    <div class="card-body">
        <div id="filter_container" style="margin-left:20px;margin-right:20px;">
            <h5>Filter:</h5>
            <div class="row">
                <div id="filter_container_status" class="col-md-6">
                    <span style="font-size:1.3em">Status</span>
                    <select class="form-control select2" id="f_status">

                        <?php $role = $this->session->userdata('user')['id_usr_role'];
                        if ($role == 2 || $role == 6 || $role == 7) {
                            echo '
                            <option value="2">Diterima</option>
                            <option value="6">Ditolak Vendor</option>
                            <option value="4">Diproses Vendor</option>
                            <option value="8">Ditolak Pemesan</option>
                            <option value="5">Diterima Pemesan</option>
                            <option value="7">Pemesanan Selesai</option>';
                        } else {
                            echo '
                            <option value="">Semua</option>
                            <option value="2">Diterima Vendor</option>
                            <option value="6">Ditolak Vendor</option>
                            <option value="4">Diproses Vendor</option>
                            <option value="8">Ditolak Pemesan</option>
                            <option value="5">Diterima Pemesan</option>
                            <option value="7">Pemesanan Selesai</option>';
                        } ?>

                    </select>
                </div>
                <div id="filter_container_branch" class="col-md-6">

                    <span style="font-size:1.3em">Area Kerja</span>
                    <select class="form-control select2" id="f_branch">
                        <?php if( $this->session->userdata('user')['id_usr_role']==1 ){ echo '<option value="">Semua</option>';} ?>
                        <?php
                        $data = $this->db->get('m_branch_code')->result();
                        foreach ($data as $d) {
                            if( $this->session->userdata('user')['id_usr_role']==1 ){
                                echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                            }
                            else{
                                if( $this->session->userdata('user')['id_company_owner']==$d->id_company_owner){
                                    echo '<option value="' . $d->id . '">' . $d->name . '</option>';
                                }
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>
            <hr>
        </div>
        <div>
            <button style="display: none;" id="btn-approval" class="btn btn-sm btn-success"><i class="fa fa-check"></i> Persetujuan</button>
            <button id="btn-history" class="btn btn-sm btn-primary"><i class="fa fa-clock"></i> Riwayat Pemesanan</button>
            <button id="btn-print-po" class="btn btn-sm btn-info"><i class="fa fa-print"></i> Cetak PO</button>
            <button style="display:none;" id="btn-delete" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Hapus TRX</button>
        </div>

        <br>
        <div class="table-responsive">
            <table id="table-data" class="table table-bordered table-striped nowrap">
                <thead>
                    <tr>
                        <th>No</th>
                        <th style="min-width: 70px;">No.PO</th>
                        <th>Vendor</th>
                        <th>Produk</th>
                        <th>Harga</th>
                        <th style="max-width: 55px;">Kuantitas</th>
                        <th>Total</th>
                        <th style="min-width: 70px;">Area</th>
                        <th style="min-width: 70px;">Pemesan</th>
                        <th style="min-width: 70px;">Waktu Pesan</th>
                        <th style="min-width: 70px;">Catatan</th>
                        <th style="min-width: 70px;">File</th>
                        <th>Status</th>
                    </tr>
                </thead>

            </table>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('.select2').select2();

        let btnPrintPO = $('#btn-print-po');
        btnPrintPO.hide();
        let filterOiOi = $('#filter_container');
        let filterOiOiStatus = $('#filter_container_status');
        let filterOiOiBranch = $('#filter_container_branch');
        let btnDelete=$('#btn-delete');

        if (user.role_id == 2 || user.role_id == 6 || user.role_id == 7) {
            filterOiOiStatus.attr('class', 'col-md-12');
            filterOiOiBranch.hide();
        }

        let branch = "<?php echo $this->db->where('id_user', $this->session->userdata('user')['id_user'])->get('sys_user')->row()->branch_code ?>";
        let no = 0;
        if (user.role_id != 1 && user.role_id != 3) {
            $('#f_branch').val(branch).trigger('change');
            $('#f_branch').attr('disabled', 1);
        }
        let tableData = $('#table-data').DataTable({
            "aaSorting": [],
            "initComplete": function(settings, json) {
                no = 0;
            },
            dom: 'Bfrtip',
            buttons: ['excel', 'pageLength'],
            "select": "single",
            "retrieve": true,
            "processing": true,
            "serverSide": true,
            "responsive": false,
            'ajax': {
                "type": "GET",
                "url": site_url + 'shopping/get_order_list',
                "data": function(d) {
                    no = 0;
                    d.branch = (user.role_id == 1 || user.role_id == 3) ? null : branch;
                    d.status = $('#f_status').val();
                    d.branch = $('#f_branch').val();
                },
                "dataSrc": "data"
            },
            "drawCallback": function() {
                $('.buyer-detail').click(function() {
                    let email = $(this).attr('data-email');
                    let telp = $(this).attr('data-telp');
                    let name = $(this).attr('data-name');
                    let fundcenter = $(this).attr('data-fc');
                    $.ajax({
                        url: site_url + 'catalogue/get_budget',
                        type: 'get',
                        data: {
                            owner: fundcenter,
                            interval_time: 1,
                            time: '<?php echo date("Y") ?>'
                        },
                        dataType: 'json',
                        success: function(res) {
                            let budget_ops = (res.ops != null) ? res.ops.available : 0;
                            let budget_nonops = (res.non_ops != null) ? res.non_ops.available : 0;
                            swal(`${name}`, `Detail Kontak\nEmail:${email}\nTelp:${telp}\n
                            Budget Operasional: Rp${budget_ops.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}
                            Budget Non-Operasional: Rp${budget_nonops.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1.")}`, 'info');
                        }
                    });
                });
                if (user.role_id == 1 || user.role_id == 3) {
                    $('.vendor-detail').click(function() {
                        let idUser = $(this).attr('data-iduser');
                        viewDetailVendor(null, idUser);
                    });
                }
            },
            'columns': [{
                    render: function(data, type, full, meta) {
                        no += 1;
                        return no;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        let po = full[19] != null ? full[19] : '...';
                        return po;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `<span style="cursor:pointer;text-decoration:underline;color:blue;" data-iduser="${full[17]}" class="vendor-detail">${full[1]}</span>`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full[2];
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `Rp${full[3].replace(/\B(?=(\d{3})+(?!\d))/g, ".")}/${full[14]}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full[4];
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `Rp${( parseInt(full[4]) * parseInt(full[3]) ).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full[5];
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `<span class="buyer-detail" style="cursor:pointer;text-decoration:underline;color:blue;" data-fc="${full[21]}" data-name="${full[6]}" data-telp="${full[8]}" data-email="${full[7]}">${full[6]}</span>`;

                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return moment(full[15]).format('D MMM Y hh:mm');
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return expandableText(full[20], 20);
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return `Delivery Order(DO) : ${full[22]!=null ? `<a alt="File DO" target="_blank" href="${site_url+'upload/shopping/file/'+full[22]}">Unduh File <i class="fa fa-download"></i></a>`:`-`}<br/>` +
                            `Good Receipt (GR) : ${full[23]!=null ? `<a alt="File DO" target="_blank" href="${site_url+'upload/shopping/file/'+full[23]}">Unduh File <i class="fa fa-download"></i></a>`:`-`}`;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        let status = full[16];
                        switch (status) {
                            case '1':
                                status = `<span class="badge badge-warning" style="color:white;">Menunggu Persetujuan</span>`;
                                break;
                            case '2':
                                status = `<span class="badge badge-info" style="color:white;">Pemesanan Diterima</span>`;
                                break;
                            case '3':
                                status = `<span class="badge badge-danger" style="color:white;">Ditolak</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '4':
                                status = `<span class="badge badge-primary" style="color:white;">Diproses Vendor</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '5':
                                status = `<span class="badge badge-success" style="color:white;">Diterima Pemesan</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '6':
                                status = `<span class="badge badge-danger" style="color:white;">Ditolak Vendor</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '7':
                                status = `<span class="badge badge-success" style="color:white;">Pemesanan Selesai</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '8':
                                status = `<span class="badge badge-danger" style="color:white;">Ditolak Pemesan</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                            case '9':
                                status = `<span class="badge badge-danger" style="color:white;">GR Ditolak GA/Proc Kantor Pusat</span><br>Catatan:<br>${expandableText(full[18],15,'Catatan Approval','-','...selengkapnya')}`;
                                break;
                        }

                        return status;
                    }
                },

            ]
        });

        $('#f_branch').change(function() {
            $('#table-data').DataTable().ajax.reload();
        });


        let selectedData = [];
        if ($('#f_status').val() == 2 && (user.role_id == 2 || user.role_id == 6 || user.role_id == 7)) {
            $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
            $('#btn-approval').show();
        } else {
            $('#btn-approval').hide();
        }
        if((user.role_id == 3 || user.role_id == 1)){
            btnDelete.show();
        }
        $('#f_status').change(function() {
            $('#table-data').DataTable().ajax.reload();
            let val = $(this).val();
            $('#btn-approval').hide();
            if (val == 1 && (user.role_id == 3 || user.role_id == 1)) {
                $('#btn-approval').show();
            } else if (val == 2 && (user.role_id == 2 || user.role_id == 6 || user.role_id == 7)) {
                $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
                $('#btn-approval').show();
            } else if (val == 4 && (user.role_id == 8 || user.role_id == 3 || user.role_id == 1)) {
                $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
                $('#btn-approval').show();
            } else if (val == 5 && (user.role_id == 3 || user.role_id == 1)) {
                $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
                $('#btn-approval').show();
            } else if (val == 8 && (user.role_id == 2 || user.role_id == 6 || user.role_id == 7)) {
                $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
                $('#btn-approval').show();
            } else if (val == 9 && (user.role_id == 8 || user.role_id == 3 || user.role_id == 1)) {
                $('#btn-approval').html('<i class="fa fa-edit"></i> Edit Status');
                $('#btn-approval').show();
            }

            // Btn Delete
            if((user.role_id == 3 || user.role_id == 1)){
                btnDelete.show();
            }


            //Btn Print PO
            if (val == 2) {
                btnPrintPO.show();
            }

            selectedData = [];
        });

        btnDelete.click(function(){
            if (selectedData.length <= 0) {
                swal('Informasi', 'Mohon pilih minimal 1 data!', 'warning');
            } else {
                swal({
                    title: "Apa anda yakin?",
                    text: "Pemesanan yg telah dihapus tidak akan bisa dikembalikan lagi.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        
                        //console.log(selectedData[0]);
                        $.ajax({
                            url:site_url+'/shopping/delete_order',
                            type:'POST',
                            data:{
                                id:selectedData[0][0]
                            },
                            dataType:'json',
                            success:function(res){
                                if(res.success){
                                    swal("Berhasil menghapus transaksi.", {
                                        icon: "success",
                                    });
                                }
                                else{
                                    swal('Gagal hapus', 'Silahkan coba beberapa saat lagi', 'error');
                                }
                                $('#table-data').DataTable().ajax.reload();
                            },
                            error:function(xhr,stat,err){
                             //console.log(err);
                             swal('Gagal hapus', 'Terjadi kesalah pada sistem, silahkan coba beberapa saat lagi', 'error');   
                            }
                        })
                    }
                });   
            }
        });

        btnPrintPO.click(function() {
            if (selectedData.length <= 0) {
                swal('Informasi', 'Mohon pilih minimal 1 data!', 'warning');
            } else {
                let data = selectedData[0];
                let myWindow = window.open('', '', 'width=1000,height=1000');
                let printData = `
                <div style="padding:30px;">
                    <table style="width:100%;padding:10px;">
                        <thead>
                            <tr>
                                <td style="text-align:left;">
                                <?php echo $this->session->userdata('user')['id_company_owner']==1 ?'<img src="" style="width:200px;height:auto;"/>':'  ' ?>
                                </td>
                                <td style="text-align:right;"><img src="data:image/png;base64,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" style="width:150px;height:auto;"/></td>
                            </tr>
                        </thead>
                    </table>
                    <table style="width:100%;border-collapse: collapse;" border="1">
                        <tbody>
                             <tr>
                                <td style="text-align:center">
                                    <center><h2>TRX PO E-KATALOG</h2></center>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table style="width:100%;border-collapse: collapse;" border="1">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>No.TRX</th>
                                <th>Item</th>
                                <th>Harga</th>
                                <th>Jumlah</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                `;

                selectedData.map(function(item, index) {
                    let i = (index + 1);
                    printData += `
                    <tr>
                        <td style="padding:10px;">${i}</td>
                        <td style="padding:10px;">${item[19]}</td>
                        <td style="padding:10px;">${item[2]}</td>
                        <td style="padding:10px;">${`Rp${( parseInt(item[3]) ).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`}</td>
                        <td style="padding:10px;">${item[4]}</td>
                        <td style="padding:10px;">${`Rp${( parseInt(item[4]) * parseInt(item[3]) ).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".")}`}</td>
                    </tr>
                    `;
                });


                printData += `
                <tr>
                    <td style="padding:10px;" colspan=4>
                    *note untuk pembelian Barang include ppn/pph serta ongkos kirim			
                    Pengiriman barang item maksimal 30 hari kerja.			
                    E TRX PO dapat dicetak jika vendor tidak mendapat notif email.			
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    Tanda Tangan
                    <br/>
                    ${data[12]?data[12]+' ':''}${data[13]}
                    </td>
                    <td style="padding:10px;" colspan=2>
                        Tanggal ${moment(data[15]).format('D MMM Y')}
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        <br/>
                        Tanda Tangan
                        <br/>
                        ${data[5]}<br/>${data[6]}
                    </td>
                </tr>
                `;

                printData += `
                         </tbody>
                    </table>
                </div>`;
                myWindow.document.write(printData);
                setTimeout(function(){
                    
                myWindow.document.close();
                myWindow.focus();
                myWindow.print();
                }, 100);
            }
        });

        $('#f_status').val((user.role_id == 2 || user.role_id == 6 || user.role_id == 7) ? 2 : 2).trigger('change');

        tableData.on('select', function(e, dt, type, indexes) {
            if (type === 'row') {
                var data = tableData.rows({
                    selected: true
                }).data();
                selectedData = data;
            }
        });

        tableData.on('deselect', function(e, dt, type, indexes) {
            if (type === 'row') {
                var data = tableData.rows({
                    selected: true
                }).data();
                selectedData = data;
            }
        });

        $('#btn-approval').click(function() {
            let option = '';

            if ((user.role_id == 8 || user.role_id == 3 || user.role_id == 1) && $('#f_status').val() == 4) {
                option = `<option value="5">Diterima Pemesan</option>
                <option value="8">Ditolak Pemesan</option>`;
            } else if ((user.role_id == 8 || user.role_id == 3 || user.role_id == 1) && $('#f_status').val() == 9) {
                option = `<option value="5">Diterima Pemesan</option>`;
            } else if ((user.role_id == 3 || user.role_id == 1) && $('#f_status').val() == 1) {
                option = `<option value="2">Terima</option>
                            <option value="3">Tolak</option>`;
            } else if ((user.role_id == 2 || user.role_id == 6 || user.role_id == 7 || user.role_id == 1) && ($('#f_status').val() == 2 || $('#f_status').val() == 8)) {
                option = `<option value="6">Ditolak Vendor</option>
                        <option value="4">Diproses Vendor</option>`;
            } 
            // else if ((user.role_id == 3 || user.role_id == 1) && $('#f_status').val() == 5) {
            //     option = `<option value="7">Pemesanan Selesai</option>
            //     <option value="9">GR Ditolak GA/Proc Kantor Pusat</option>`;
            // }

            if (selectedData.length <= 0) {
                swal('Informasi', 'Mohon pilih minimal 1 data!', 'warning');
            } else {
                basicModal({
                    title: 'Persetujuan Pemesanan',
                    body: `
                    <form id="form_approval">
                    <label>Status Persetujuan</label>
                    <select class="form-control" id="approval-status">
                    ${option}
                    </select>


                    <br/>
                    <div id="approval_note_container">
                        <label id="approval_note_label">Catatan <span style="color:red;">(wajib diisi ketika melakukan penolakan)</span></label>
                        <textarea class="form-control" rows="5" id="approval-note"></textarea>
                    </div>
                    <br/>
                    <div id="approval_file_do_container">
                        <label id="approval_file_do_label">File Delivery Order(DO) <span style="color:red;">(wajib diisi ketika memperoses)</span></label>
                        <input type="file" class="form-control" rows="5" id="file_do" name="file_do"/>
                        <span style="color:red;">Jenis file pdf, png, jpg, jpeg<br>Maksimal 50MB</span>
                    </div>
                    <br/>
                    <div id="approval_file_gr_container">
                        <label id="approval_file_gr_label">File Good Receipt(GR) <span style="color:red;">(wajib diisi ketika menyelesaikan pemesanan)</span></label>
                        <input type="file" class="form-control" rows="5" id="file_gr" name="file_gr"/>
                        <span style="color:red;">Jenis file pdf, png, jpg, jpeg<br>Maksimal 50MB</span>
                    </div>
                    <br/>
                    <div style="text-align:right;">
                        <button type="submit" id="approval-submit" class="btn btn-sm btn-success"><i class="fa fa-paper-plane"></i> Submit</>
                    </div>
                    </form>
                    `,
                    footer: ``
                }).show(function() {
                    let statusApproval = $('#approval-status');
                    let noteApproval = $('#approval-note');
                    if (user.role_id == 2 || user.role_id == 6 || user.role_id == 7) {
                        // $('#approval_note_label').html('Catatan');
                    } else if ($('#f_status').val() == 4) {
                        // $('#approval_note_label').html('Catatan');
                    }

                    $('#approval-status').change(function() {
                        $('#file_do').removeAttr('data-validation');
                        $('#file_gr').removeAttr('data-validation');
                        $('#approval_note').removeAttr('data-validation');
                        $('#approval_file_do_container').hide();
                        $('#approval_file_gr_container').hide();
                        let val = $(this).val();
                        if (val == '4') {
                            $('#approval_file_do_container').show();
                            $('#approval-note').removeAttr('data-validation');
                            $('#file_do').attr('data-validation', 'required mime size');
                            $('#file_do').attr('data-validation-max-size', '50M');
                            $('#file_do').attr('data-validation-allowing', 'pdf, png, jpg, jpeg');
                        } else if (val == '6' || val == '3') {
                            $('#approval-note').attr('data-validation', 'required');
                        } else if (val == '5') {
                            $('#approval_file_gr_container').show();
                            $('#approval-note').removeAttr('data-validation');
                            $('#file_gr').attr('data-validation', 'required mime size');
                            $('#file_gr').attr('data-validation-max-size', '50M');
                            $('#file_gr').attr('data-validation-allowing', 'pdf, png, jpg, jpeg');
                        } else if (val == '2') {
                            $('#approval-note').removeAttr('data-validation');
                        } else if (val == '8') {
                            $('#approval-note').attr('data-validation', 'required');
                        } else if (val == '9') {
                            $('#approval-note').attr('data-validation', 'required');
                        }
                    });
                    $('#approval-status').trigger('change');

                    $.validate({
                        form: '#form_approval',
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
                            let form = $form;
                            // var formData = new FormData(form[0]);


                            if (statusApproval.val() == '3' && noteApproval.val().length <= 0) {
                                swal('Informasi', 'Mohon isi alasan penolakan!', 'warning');
                            } else {
                                let formData = new FormData();
                                formData.append('<?php echo $this->security->get_csrf_token_name() ?>', '<?php echo $this->security->get_csrf_hash() ?>');
                                for (let i = 0; i < selectedData.length; i++) {
                                    let item = selectedData[i];
                                    formData.append('id[]', item[0]);
                                    formData.append('status[]', statusApproval.val());
                                    formData.append('note[]', noteApproval.val());
                                    if (statusApproval.val() == '4') {
                                        formData.append('file_do', $('input[name="file_do"]')[0].files[0]);
                                    }
                                    if (statusApproval.val() == '5') {
                                        formData.append('file_gr', $('input[name="file_gr"]')[0].files[0]);
                                    }

                                }

                                // for (var pair of formData.entries()) {
                                //     console.log(pair[0] + '=' + pair[1]);
                                // }

                                $.ajax({
                                    url: site_url + '/shopping/submit_approval',
                                    type: 'post',
                                    dataType: 'json',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(res) {
                                        if (res.success) {
                                            swal('Berhasil Disimpan!', 'Data berhasil disimpan.', 'success')
                                                .then(function() {
                                                    $('#table-data').DataTable().ajax.reload();
                                                    basicModal().close();
                                                });
                                        } else {
                                            swal('Gagal Disimpan!', 'Data gagal disimpan, silahkan coba lagi.', 'error')
                                                .then(function() {
                                                    // $('#table-data').DataTable().ajax.reload();
                                                });
                                        }
                                    },
                                    error: function(err) {
                                        alert(err);
                                    }
                                });
                            }


                            return true;
                        }
                    });
                });
            }
        });

        $('#btn-history').click(function() {

            if (selectedData.length <= 0) {
                swal('Informasi', 'Mohon pilih minimal 1 data!', 'warning');
            } else {

                let item = selectedData[0];
                largeModal({
                    title: 'Riwayat Pemesanan ' + item[19],
                    body: `<div class="table-responsive"> 
                    <table width="100%" id="history_order_table" class="table table-hover table-striped nowrap">
                    <thead>
                    <tr>
                       <th>Verifikator</th>
                       <th>Waktu</th>
                       <th>Status</th> 
                       <th>Catatan</th>
                       <th>File</th> 
                    </tr>
                    </thead>
                    
                    </table>
                    </div>`,
                    footer: ``

                }).show(function(modal) {

                    let tableDataHistory = $('#history_order_table').DataTable({
                        "aaSorting": [],
                        "initComplete": function(settings, json) {
                            no = 0;
                        },
                        dom: 'Bfrtip',
                        buttons: ['excel', 'pageLength'],
                        "select": "single",
                        "retrieve": true,
                        "processing": true,
                        "serverSide": false,
                        "responsive": false,
                        'ajax': {
                            "type": "GET",
                            "url": site_url + 'shopping/get_history',
                            "data": function(d) {
                                no = 0;
                                d.id = item[0];
                            },
                            "dataSrc": ""
                        },
                        "drawCallback": function() {

                        },
                        'columns': [{
                                render: function(data, type, full, meta) {
                                    return `${full.role_name}<br><span style="font-size:9pt">(${full.email})</span>`;
                                }
                            },
                            {
                                render: function(data, type, full, meta) {
                                    return moment(full.created_at).format('D MMM Y H:mm:ss');
                                }
                            }, {
                                render: function(data, type, full, meta) {
                                    let status = full.status;
                                    switch (status) {
                                        case '1':
                                            status = `<span class="badge badge-warning" style="color:white;">Menunggu Persetujuan</span>`;
                                            break;
                                        case '2':
                                            status = `<span class="badge badge-info" style="color:white;">Pemesanan Diterima</span>`;
                                            break;
                                        case '3':
                                            status = `<span class="badge badge-danger" style="color:white;">Ditolak</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '4':
                                            status = `<span class="badge badge-primary" style="color:white;">Diproses Vendor</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '5':
                                            status = `<span class="badge badge-success" style="color:white;">Diterima Pemesan</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '6':
                                            status = `<span class="badge badge-danger" style="color:white;">Ditolak Vendor</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '7':
                                            status = `<span class="badge badge-success" style="color:white;">Pemesanan Selesai</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '8':
                                            status = `<span class="badge badge-danger" style="color:white;">Ditolak Pemesan</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                        case '9':
                                            status = `<span class="badge badge-danger" style="color:white;">GR Ditolak GA/Proc Kantor Pusat</span><br>Catatan:<br>${expandableText(full.approval_note,15,'Catatan Approval','-','...selengkapnya')}`;
                                            break;
                                    }

                                    return status;
                                }
                            }, {
                                render: function(data, type, full, meta) {
                                    return expandableText(full.note, 20);
                                }
                            },
                            {
                                render: function(data, type, full, meta) {
                                    return `Delivery Order(DO) : ${full.file_do!=null ? `<a alt="File DO" target="_blank" href="${site_url+'upload/shopping/file/'+full.file_do}">Unduh File <i class="fa fa-download"></i></a>`:`-`}<br/>` +
                                        `Good Receipt (GR) : ${full.file_gr!=null ? `<a alt="File DO" target="_blank" href="${site_url+'upload/shopping/file/'+full.file_gr}">Unduh File <i class="fa fa-download"></i></a>`:`-`}`;
                                }
                            }

                        ]
                    });

                });
            }
        });
    });
</script>