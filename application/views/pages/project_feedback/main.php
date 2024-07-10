<?php $this->load->view('templates/dashboard/content-title');

if($this->session->userdata('user')['id_usr_role']==3){
    $this->db->where('company.id_company_owner', $this->session->userdata('user')['id_company_owner'] );
}

$list_project = $this->db
    ->select("project.*, 
    concat(if(company.prefix_name is null,'',concat(company.prefix_name,' ') ),company.name) as company_name")
    ->where('winner is not null')
    ->where('project.deleted_at is null')
    ->where_in('project.id','(select id_project from tbl_vendor_valuation where deleted_at is null)')
    ->join('company_profile company', 'company.id=project.winner')
    ->get('project')
    ->result();
$existing_feedback = $this->db->where('deleted_at is null')->get('tbl_vendor_valuation')->result();
$existing = array();
if ($existing_feedback != null) {

    foreach ($existing_feedback as $e) {
        $existing[] = $e->id_project;
    }
    if($this->session->userdata('user')['id_usr_role']==3){
        $this->db->where('company.id_company_owner', $this->session->userdata('user')['id_company_owner'] );
    }
    $list_project_listed = $this->db
        ->select('project.*, concat(if(company.prefix_name is null,"",concat(company.prefix_name," ") ),company.name) as company_name')
        ->where('winner is not null')
        ->where('project.deleted_at is null')
        ->where_not_in('project.id', $existing)
        ->join('company_profile company', 'company.id=project.winner')
        ->get('project')
        ->result();
} else {
    if($this->session->userdata('user')['id_usr_role']==3){
        $this->db->where('company.id_company_owner', $this->session->userdata('user')['id_company_owner'] );
    }
    $list_project_listed = $this->db
        ->select('project.*, concat(if(company.prefix_name is null,"",concat(company.prefix_name," ") ),company.name) as company_name')
        ->where('winner is not null')
        ->where('project.deleted_at is null')
        ->join('company_profile company', 'company.id=project.winner')
        ->get('project')
        ->result();
}
$list_vendor = $this->db
    ->select('company.*,if(contact.email is null, user.email, contact.email) as email')
    ->where('winner is not null')
    ->where('project.deleted_at is null')
    ->join('company_profile company', 'company.id=project.winner')
    ->join('sys_user user', 'company.id_user=user.id_user')
    ->join('company_contact contact', 'company.id=contact.id_company')
    ->get('project')
    ->result();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rating/1.5.0/bootstrap-rating.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-rating/1.5.0/bootstrap-rating.min.js"></script>

<style>
    label {
        font-weight: bold;
    }

    th {
        text-align: inherit;
        border: 1px solid #e9ecef;
    }

    td {
        text-align: inherit;
        border: 1px solid #e9ecef;
        min-width: 100px;
    }

    .color-yellow {
        color: #fcd303;
    }

    .modal-dialog-large,
    .modal-content-large {
        height: inherit;
    }
</style>


<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <!-- <div>
                    <label for="">Vendor</label>
                    <select id="f_vendor" class="form-control select2">
                        <option value="">Semua</option>
                        <?php
                        foreach ($list_vendor as $v) {
                            echo '<option value="' . $v->id . '">' . $v->prefix_name . ' ' . $v->name . ' | ' . $v->email . '</option>';
                        }
                        ?>
                    </select>
                </div> -->
            </div>
            <div class="col-md-6">
                <!-- <div>
                    <label for="">Proyek</label>
                    <select id="f_project" class="form-control select2">
                        <option value="">Semua</option>
                        <?php
                        foreach ($list_project as $p) {
                            echo '<option value="' . $p->id . '">' . $p->name . ' | ' . $p->contract_no . ' | '.$p->vendor_value_name.'</option>';
                        }
                        ?>
                    </select>
                </div> -->
            </div>
        </div>
        <hr>
        <div class="table-responsive">
            <div>
                <button title="Add" class="btn-add btn btn-sm btn-success"><i class="fa fa-plus"></i></button>
                <button title="Edit" class="btn-edit btn btn-sm btn-info"><i class="fa fa-edit"></i></button>
                <button title="Delete" class="btn-delete btn btn-sm btn-danger"><i class="fa fa-trash"></i></button>
            </div>
            <br>
            <table id="dt-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th colspan="3">
                            <center>Proyek</center>
                        </th>
                        <th colspan="2">
                            <center>Vendor</center>
                        </th>
                        <th colspan="5">
                            <center>Penilaian</center>
                        </th>
                        <th colspan="3">
                            <center>Kesimpulan</center>
                        </th>
                    </tr>
                    <tr>
                        <th>Nama</th>
                        <th>No.Kontrak</th>
                        <th>Tanggal Penawaran</th>

                        <th>Nama</th>
                        <th>Email</th>

                        <th>Aspek Mutu</th>
                        <th>Aspek Harga</th>
                        <th>Aspek Waktu Pekerjaan</th>
                        <th>Aspek Waktu Pembayaran</th>
                        <th>Aspek K3LL</th>

                        <th>Persentase Nilai</th>
                        <th>Kategori</th>
                        <th>Keterangan</th>

                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2();
        let btnAction = {
            add: $('.btn-add'),
            edit: $('.btn-edit'),
            delete: $('.btn-delete')
        }
        let listProject = JSON.parse(JSON.stringify(<?php echo json_encode($list_project) ?>));
        let listProjectListed = JSON.parse(JSON.stringify(<?php echo json_encode($list_project_listed) ?>));
        let selectedData = null;
        let bobotPenilaian = {
            mutu: 30,
            waktuPekerjaan: 15,
            K3: 10,
            harga: 30,
            pembayaran: 15
        }
        let aspekPenilaian = {
            mutu: ['Kerusakan Diatas 10%', 'Terdapat Kerusakan 6 s/d 10%', 'Terdapat Kerusakan 1 s/d 5%', 'Tidak Ada Kerusakan/Zero Defect'],
            waktuPekerjaan: [
                `Terlambat lebih dari 4 hari`,
                `Terlambat 3 s / d 4 hari`,
                `Terlambat 1 s / d 2 hari`,
                `Pengiriman / Pekerjaan tepat waktu)`
            ],
            K3: [
                `Ada pencemaran dan kecelakaan kerja`,
                `Ada pencemaran atau kecelakaan kerja`,
                `Ada ketentuan peraturan K3LL perusahaan yang dilanggar tetapi tidak ada pencemaran dan kecelakaan kerja`,
                `Memenuhi peraturan K3LL perusahaan dan tidak ada pencemaran maupun kecelakaan kerja`
            ],
            harga: [
                `Diatas 20 % lebih murah dari rekanan pembanding`,
                `10 s / d 15 % lebih murah dari rekanan pembanding`,
                `16 s / d 20 % lebih murah dari rekanan pembanding`,
                `1 s / d 9 % lebih murah dari rekanan pembanding atau sama, dan / atau jika tidak ada pembanding`
            ],
            pembayaran: [
                `Pembayaran dibawah 2 minggu`,
                `Pembayaran 2 s / d 3 minggu`,
                `Pembayaran diatas 3 minggu s / d 4 minggu`,
                `Pembayaran lebih dari 4 minggu`
            ]

        }

        $('#modal-basic').attr('data-backdrop','static');

        let showForm = function(isAdd = true, data = null) {
            basicModal({
                title: '<h5>Tambah Penilaian Terhadap Vendor Per Proyek</h5>',
                body: `
                <form id="main-form">
                    <label>Proyek</label>
                    <select style="width:100%" class="form-control select2-modal" id="id_project" name="id_project">
                    </select>
                    <hr/>
                    <b>Penilaian Vendor</b>
                    <div class="row">
                        <div class="col-md-6">
                            <div>
                                <span>Aspek Mutu</span>
                                <br/>
                                <input type="hidden" class="rating" id="aspek_mutu" data-start="0" data-stop="4" data-step="1" data-filled="fa fa-star fa-2x color-yellow" data-empty="fa fa-star-o fa-2x color-yellow"/>
                                <small style="color:red;">
                                    <ol style="padding-inline-start:15px;">
                                        <li>Kerusakan Diatas 10%</li>
                                        <li>Terdapat Kerusakan 6 s/d 10%</li>
                                        <li>Terdapat Kerusakan 1 s/d 5%</li>
                                        <li>Tidak Ada Kerusakan/Zero Defect</li>
                                    </ol>
                                </small>
                                <span>Aspek Waktu Pekerjaan</span>
                                <br/>
                                <input type="hidden" class="rating" id="aspek_waktu_pekerjaan" data-start="0" data-stop="4" data-step="1" data-filled="fa fa-star fa-2x color-yellow" data-empty="fa fa-star-o fa-2x color-yellow"/>
                                <small style="color:red;">
                                    <ol style="padding-inline-start:15px;">
                                        <li>Terlambat lebih dari 4 hari</li>
                                        <li>Terlambat 3 s/d 4 hari</li>
                                        <li>Terlambat 1 s/d 2 hari</li>
                                        <li>Pengiriman/Pekerjaan tepat waktu</li>
                                    </ol>
                                </small>
                                <span>Aspek K3 & Lingkungan Hidup</span>
                                <br/>
                                <input type="hidden" class="rating" id="aspek_k3ll" data-start="0" data-stop="4" data-step="1" data-filled="fa fa-star fa-2x color-yellow" data-empty="fa fa-star-o fa-2x color-yellow"/>
                                <small style="color:red;">
                                    <ol style="padding-inline-start:15px;">
                                        <li>Ada pencemaran dan kecelakaan kerja</li>
                                        <li>Ada pencemaran atau kecelakaan kerja</li>
                                        <li>Ada ketentuan peraturan K3LL perusahaan yang dilanggar tetapi tidak ada pencemaran dan kecelakaan kerja</li>
                                        <li>Memenuhi peraturan K3LL perusahaan dan tidak ada pencemaran maupun kecelakaan kerja</li>
                                    </ol>
                                </small>
                                
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span>Aspek Harga</span>
                            <br/>
                            <input type="hidden" class="rating" id="aspek_harga" data-start="0" data-stop="4" data-step="1" data-filled="fa fa-star fa-2x color-yellow" data-empty="fa fa-star-o fa-2x color-yellow"/>
                            <small style="color:red;">
                                    <ol style="padding-inline-start:15px;">
                                        <li>Diatas 20% lebih murah dari rekanan pembanding</li>
                                        <li>10 s/d 15% lebih murah dari rekanan pembanding</li>
                                        <li>16 s/d 20% lebih murah dari rekanan pembanding</li>
                                        <li>1 s/d 9% lebih murah dari rekanan pembanding atau sama, dan/atau jika tidak ada pembanding</li>
                                    </ol>
                            </small>

                            <span>Aspek Pembayaran</span>
                            <br/>
                            <input type="hidden" class="rating" id="aspek_pembayaran" data-start="0" data-stop="4" data-step="1" data-filled="fa fa-star fa-2x color-yellow" data-empty="fa fa-star-o fa-2x color-yellow"/>
                            <small style="color:red;">
                                    <ol style="padding-inline-start:15px;">
                                        <li>Pembayaran dibawah 2 minggu</li>
                                        <li>Pembayaran 2 s/d 3 minggu</li>
                                        <li>Pembayaran diatas 3 minggu s/d 4 minggu</li>
                                        <li>Pembayaran lebih dari 4 minggu</li>
                                    </ol>
                                </small>
                        </div>
                    </div>
                    <br>
                </form>`,
                footer: `
                    <div style="text-align:right;">
                        <button id="btn-save" type="button" class="btn btn-md btn-success"><i class="fa fa-save"></i> Simpan</button>
                    </div>`
            }).show(function(container) {
                let selProject = ``;

                if (!isAdd) {

                    listProject.forEach(function(item) {
                        selProject += `<option value="${item.id}">${item.name} Tgl ${moment(item.start_date).format('D MMM Y')} - ${moment(item.end_date).format('D MMM Y')} | ${item.contract_no} | ${item.vendor_value_name}</option>`;
                    });
                    $('#id_project').html(selProject);
                } else {
                    listProjectListed.forEach(function(item) {
                        selProject += `<option value="${item.id}">${item.name} Tgl ${moment(item.start_date).format('D MMM Y')} - ${moment(item.end_date).format('D MMM Y')} | ${item.contract_no} | ${item.vendor_value_name}</option>`;
                    });
                    $('#id_project').html(selProject);
                }
                $('.select2-modal').select2({
                    dropdownParent: $('#modal-basic')
                });
                $('.rating').rating('rate', 1);

                if (!isAdd) {
                    $('#id_project').select2('val', selectedData.id_project);
                    $('#id_project').val(selectedData.id_project).trigger('change');
                    $('#aspek_mutu').rating('rate', selectedData.aspek_mutu);
                    $('#aspek_harga').rating('rate', selectedData.aspek_harga);
                    $('#aspek_waktu_pekerjaan').rating('rate', selectedData.aspek_waktu_pekerjaan);
                    $('#aspek_pembayaran').rating('rate', selectedData.aspek_pembayaran);
                    $('#aspek_k3ll').rating('rate', selectedData.aspek_k3ll);
                }

                $('#btn-save').click(function() {
                    if (isAdd) {

                        if ($('#id_project').val() != null) {
                            $.ajax({
                                url: site_url + 'evaluation/add_data_feedback',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id_user: '<?php echo $this->session->userdata('user')['id_user'] ?>',
                                    id_project: $('#id_project').val(),
                                    aspek_mutu: $('#aspek_mutu').rating('rate'),
                                    aspek_harga: $('#aspek_harga').rating('rate'),
                                    aspek_waktu_pekerjaan: $('#aspek_waktu_pekerjaan').rating('rate'),
                                    aspek_pembayaran: $('#aspek_pembayaran').rating('rate'),
                                    aspek_k3ll: $('#aspek_k3ll').rating('rate')
                                },
                                success: function(res) {
                                    swal('Informasi', res.result, res.success ? 'success' : 'error');
                                    $('#dt-table').DataTable().ajax.reload();
                                    basicModal().close();
                                },
                                error: function(xhr, stat, err) {
                                    swal('Informasi', err, 'error');
                                    basicModal().close();
                                }
                            });
                        } else {
                            swal('Informasi', 'Proyek harus diisi', 'warning');
                        }
                    } else {
                        if ($('#id_project').val() != null) {
                            $.ajax({
                                url: site_url + 'evaluation/edit_data_feedback',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: data.id,
                                    id_user: '<?php echo $this->session->userdata('user')['id_user'] ?>',
                                    id_project: $('#id_project').val(),
                                    aspek_mutu: $('#aspek_mutu').rating('rate'),
                                    aspek_harga: $('#aspek_harga').rating('rate'),
                                    aspek_waktu_pekerjaan: $('#aspek_waktu_pekerjaan').rating('rate'),
                                    aspek_pembayaran: $('#aspek_pembayaran').rating('rate'),
                                    aspek_k3ll: $('#aspek_k3ll').rating('rate')
                                },
                                success: function(res) {
                                    swal('Informasi', res.result, res.success ? 'success' : 'error');
                                    $('#dt-table').DataTable().ajax.reload();
                                    basicModal().close();
                                },
                                error: function(xhr, stat, err) {
                                    swal('Informasi', err, 'error');
                                    basicModal().close();
                                }
                            });
                        } else {
                            swal('Informasi', 'Proyek harus diisi', 'warning');
                        }
                    }
                });
            });
        }

        btnAction.add.click(function() {
            showForm();
        });
        btnAction.edit.click(function() {
            if (selectedData != null) {
                showForm(false, selectedData);
            } else {
                swal('Informasi', 'Harap pilih minimal 1 data terlebih dahulu', 'warning');
            }
        });
        btnAction.delete.click(function() {
            if (selectedData != null) {
                if (confirm('Apa Anda yakin menghapus data ini?')) {
                    $.ajax({
                        url: site_url + 'evaluation/delete_data_feedback',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id: selectedData.id
                        },
                        success: function(res) {
                            if (res.success) {
                                swal('Informasi', 'Berhasil hapus data, silahkan coba lagi.', 'success');
                                $('#dt-table').DataTable().ajax.reload();
                            } else {
                                swal('Informasi', 'Gagal hapus data, silahkan coba lagi.', 'error');
                            }
                        },
                        error: function(xhr, stat, err) {
                            swal('Informasi', err, 'error');
                            basicModal().close();
                        }
                    });
                }
            } else {
                swal('Informasi', 'Harap pilih minimal 1 data terlebih dahulu', 'warning');
            }
        });

        let dtTable = $('#dt-table').DataTable({
            "aaSorting": [],
            "retrieve": true,
            "processing": true,
            'ajax': {
                "type": "GET",
                "url": site_url + 'evaluation/get_feedback',
                "data": function(d) {
                    d.f_company = $('#f_company').val();
                    d.f_start_date = $('#f_start_date').val();
                    d.f_end_date = $('#f_end_date').val();
                },
                "dataSrc": function(res) {
                    selectedData = null;
                    return res;
                }
            },
            "select": "single",
            'columns': [{
                    render: function(data, type, full, meta) {
                        return full.project_name;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return full.project_contract_no;
                    }
                },
                {
                    render: function(data, type, full, meta) {
                        return moment(full.project_start_date).format('D MMM Y') + " s/d " + moment(full.project_end_date).format('D MMM Y');
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return full.vendor_name;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return full.vendor_email;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return aspekPenilaian.mutu[full.aspek_mutu - 1] + ` <b>(${full.aspek_mutu})</b>`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return aspekPenilaian.harga[full.aspek_harga - 1] + ` <b>(${full.aspek_harga})</b>`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return aspekPenilaian.waktuPekerjaan[full.aspek_waktu_pekerjaan - 1] + ` <b>(${full.aspek_waktu_pekerjaan})</b>`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return aspekPenilaian.pembayaran[full.aspek_pembayaran - 1] + ` <b>(${full.aspek_pembayaran})</b>`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        return aspekPenilaian.K3[full.aspek_k3ll - 1] + ` <b>(${full.aspek_k3ll})</b>`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        let total = (4 * bobotPenilaian.mutu) +
                            (4 * bobotPenilaian.harga) +
                            (4 * bobotPenilaian.K3) +
                            (4 * bobotPenilaian.pembayaran) +
                            (4 * bobotPenilaian.waktuPekerjaan);

                        let persentase = (
                            ((full.aspek_mutu * bobotPenilaian.mutu) +
                                (full.aspek_harga * bobotPenilaian.harga) +
                                (full.aspek_waktu_pekerjaan * bobotPenilaian.waktuPekerjaan) +
                                (full.aspek_pembayaran * bobotPenilaian.pembayaran) +
                                (full.aspek_k3ll * bobotPenilaian.K3)) / total) * 100;

                        return persentase + '%';
                    }
                }, {
                    render: function(data, type, full, meta) {
                        let total = (4 * bobotPenilaian.mutu) +
                            (4 * bobotPenilaian.harga) +
                            (4 * bobotPenilaian.K3) +
                            (4 * bobotPenilaian.pembayaran) +
                            (4 * bobotPenilaian.waktuPekerjaan);

                        let persentase = (
                            ((full.aspek_mutu * bobotPenilaian.mutu) +
                                (full.aspek_harga * bobotPenilaian.harga) +
                                (full.aspek_waktu_pekerjaan * bobotPenilaian.waktuPekerjaan) +
                                (full.aspek_pembayaran * bobotPenilaian.pembayaran) +
                                (full.aspek_k3ll * bobotPenilaian.K3)) / total) * 100;
                        let kategori = 'D';
                        if (persentase >= 90 && persentase <= 100) {
                            kategori = 'A';
                        } else if (persentase >= 80 && persentase <= 89) {
                            kategori = 'B';
                        } else if (persentase >= 70 && persentase <= 79) {
                            kategori = 'C';
                        } else {
                            kategori = 'D';
                        }

                        return `${kategori}`;
                    }
                }, {
                    render: function(data, type, full, meta) {
                        let total = (4 * bobotPenilaian.mutu) +
                            (4 * bobotPenilaian.harga) +
                            (4 * bobotPenilaian.K3) +
                            (4 * bobotPenilaian.pembayaran) +
                            (4 * bobotPenilaian.waktuPekerjaan);

                        let persentase = (
                            ((full.aspek_mutu * bobotPenilaian.mutu) +
                                (full.aspek_harga * bobotPenilaian.harga) +
                                (full.aspek_waktu_pekerjaan * bobotPenilaian.waktuPekerjaan) +
                                (full.aspek_pembayaran * bobotPenilaian.pembayaran) +
                                (full.aspek_k3ll * bobotPenilaian.K3)) / total) * 100;
                        let kategori = 'Diperingatkan maks. 2 kali / dikeluarkan dari DRM';
                        if (persentase >= 90 && persentase <= 100) {
                            kategori = 'Memuaskan';
                        } else if (persentase >= 80 && persentase <= 89) {
                            kategori = 'Baik';
                        } else if (persentase >= 70 && persentase <= 79) {
                            kategori = 'Cukup';
                        } else {
                            kategori = 'Diperingatkan maks. 2 kali / dikeluarkan dari DRM';
                        }

                        return `${kategori}`;
                    }
                }
            ],
            dom: 'Bflrtip',
            buttons: [
                'excel'
            ],
            "drawCallback": function() {

            }
        });
        dtTable.on('select', function(e, dt, type, indexes) {
            if (type === 'row') {
                var data = dtTable.rows({
                    selected: true
                }).data();
                selectedData = data[0];
            }
        });
        dtTable.on('deselect', function(e, dt, type, indexes) {
            selectedData = null;
        });
    });
</script>