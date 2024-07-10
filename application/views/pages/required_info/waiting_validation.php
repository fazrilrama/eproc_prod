<!-- <div id="rylinx_content1">
</div>

<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>
<script>
    $(document).ready(function() {
        let rylinx = RyLinxClass('#rylinx_content1');
        rylinx.to('#notification', function() {
            $('.table-resposive').attr('style', 'width:98%');
            globalEvents.onDone = (id, data) => {
                if (id == 'onAjaxCallDone') {
                    $('.notif_click').remove();

                }
            }
        });
    });
</script> -->

<link rel="stylesheet" href="<?php echo base_url('assets/vendor/timer/default.css') ?>">
<script src="<?php echo base_url('assets/vendor/timer/jquery.syotimer.min.js') ?>"></script>

<div class="app-page-title">
    <div class="page-title-wrapper">
        <div class="page-title-heading">
            <div class="page-title-icon">
                <i class="lnr-user text-info">
                </i>
            </div>
            <div>Form Informasi Wajib
                <div class="page-title-subheading">Mohon isi dan submit informasi yang wajib Anda isi.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin:10px">
    <div class="card-body">
        <h5><b>Jenis Vendor</b></h5>
        <select name="jenis_vendor" id="jenis_vendor" class="form-control">
            <option value="2">Perusahaan</option>
            <option value="6">Perseorangan</option>
        </select>
    </div>
</div>

<div id="rylinx_content1">
</div>

<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>

<script>
    $(document).ready(function() {
        let ryLinx = RyLinxClass('#rylinx_content1');
        let linkPersonal = '#required_info/registration_personal';
        let linkCompany = '#required_info/registration_company';

        $('#jenis_vendor').change(function() {
            let val = $(this).val();
            if ($(this).val() == 2) {
                ryLinx.to(linkCompany, function() {
                    $('#form_input').append(`<input type="hidden" name="id_usr_role" id="id_usr_role" value="${val}"/>`);
                    $('input').attr('disabled', 1);
                    $('select').attr('disabled', 1);
                    $('textarea').attr('disabled', 1);
                    $('input[type="file"]').remove();
                    $('span[style="color:red"]').remove();
                    $('#persetujuan_registrasi').html(`<div class="card-body">
                        <br/>
                    <span style="color:darkgreen;"><h5>Mohon cek email Anda, untuk menuggu informasi selanjutnya.</h5></span>
                    </div>`);
                });
            } else {
                ryLinx.to(linkPersonal, function() {
                    $('#form_input').append(`<input type="hidden" name="id_usr_role" id="id_usr_role" value="${val}"/>`);
                    $('input').attr('disabled', 1);
                    $('select').attr('disabled', 1);
                    $('textarea').attr('disabled', 1);
                    $('input[type="file"]').remove();
                    $('span[style="color:red"]').remove();
                    $('#persetujuan_registrasi').html(`<div class="card-body">
                        Status Registarasi : <b style="color:red"><?php echo $this->db
                                                                        ->where('id_user', $this->session->userdata('user')['id_user'])
                                                                        ->get(App_Model::TBL_COMPANY_PROFILE)
                                                                        ->row()->verification_status; ?></b>
                        <br/>
                        Catatan Verifikator : <?php $id_company = $this->db
                                                    ->where('id_user', $this->session->userdata('user')['id_user'])
                                                    ->get(App_Model::TBL_COMPANY_PROFILE)
                                                    ->row()->id;

                                                $verif_history = $this->db->where('data_id', $id_company)
                                                    ->order_by('created_at desc')
                                                    ->limit(1)
                                                    ->get('verification_history')
                                                    ->row();
                                                if ($verif_history != null) {
                                                    echo $verif_history->verification_note;
                                                }

                                                ?>
                        <br/>
                        <span style="color:darkgreen;"><h5>Mohon cek email Anda, untuk menuggu informasi selanjutnya.</h5></span>
                    </div>`);
                });
            }
        });

        $('#jenis_vendor').val(user.role_id).trigger('change');
    });
</script>