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

<div id="timer-countdown"></div>
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
        let user_status = '<?php echo $this->session->userdata('user')['id_usr_status']; ?>';

        if (user_status == '5') {
            let createdDate = `<?php $user = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                                    ->get('sys_user')->row();
                                echo $user->created_at; ?>`;
            let remainingTime = moment(createdDate, 'YYYY-MM-DD hh:mm:ss').add('days', 14);

            $('#timer-countdown').syotimer({
                year: remainingTime.year(),
                month: remainingTime.month() + 1,
                day: remainingTime.date(),
                hour: remainingTime.hour(),
                minute: remainingTime.minute(),
                headTitle: '<h3>Waktu tersisa untuk melengkapi Data Profil.</h3>',
                footTitle: '<i style="color: red;">Mohon lengkapi data profil sebelum waktu habis!</i>',
                afterDeadline: function(timerBlock) {
                    $.post(site_url + 'required_info/block_after_timeout', postDataWithCsrf.data(), function(data) {
                        window.location.href = site_url + 'auth/logout';
                        alert("Maaf akun Anda telah dihapus dari sistem karena tidak mengisi data profil dalam 2x24 jam.");
                    }, 'json');
                }

            });
        }

        $('#jenis_vendor').change(function() {
            let val = $(this).val();
            if (val == 2) {
                ryLinx.to(linkCompany, function() {
                    $('#form_input').append(`<input type="hidden" name="id_usr_role" id="id_usr_role" value="${val}"/>`);
                });
            } else {
                ryLinx.to(linkPersonal, function() {
                    $('#form_input').append(`<input type="hidden" name="id_usr_role" id="id_usr_role" value="${val}"/>`);
                });
            }
        });

        globalEvents.onDone = function(id, data) {
            if (id == 'formSubmit') {
                location.reload();
                $('#timer-countdown').remove();
            }
        };

        $('#jenis_vendor').val(user.role_id).trigger('change');
    });
</script>