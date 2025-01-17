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

<div class="main-card mb-3 card">
    <div class="card-body">
        <div class="forms-wizard-vertical sw-main sw-theme-default">
            <ul class="forms-wizard nav nav-tabs step-anchor" id="selection_required_wizard">

            </ul>
            <div class="form-wizard-content sw-container tab-content" style="min-height: 703.2px;">
                <div id="rylinx_content1" class="tab-pane step-content active">
                </div>
                <div id="step-finish" class="tab-pane step-content" style="display: none;">
                    <div id="finsih-term">
                        <h3>Persetujuan Registrasi</h3>
                        <ol style="font-size: 1.2em">
                            <li>Pastikan data Anda benar dan dapat dipertanggung jawabkan.</li>
                            <li>Jika data yang Anda masukan adalah tidak benar secara hukum,
                                maka Penyelengara dalam hal ini <b>PT. BGR Logistik Indonesia (Persero)</b> dapat <span style="color: red;">memblokir/menghapus/melakukan tindakan hukum
                                    menurut Perundang-undangan yang berlaku.</span> </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider"></div>
        <div class="clearfix">
            <!-- <button type="button" id="reset-btn22" class="btn-shadow float-left btn btn-link">Reset</button> -->
            <button type="button" id="next-btn-wizard" class="btn-shadow btn-wide float-right btn-pill btn-hover-shine btn btn-primary"><i class="fa fa-arrow-right"> Selanjutnya</i></button>
            <button type="button" id="prev-btn-wizard" class="btn-shadow float-right btn-wide btn-pill mr-3 btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Sebelumnya</button>
        </div>
    </div>
</div>
<script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>

<script>
    $(document).ready(function() {
        let current_page = 1;
        let prev_page = null;
        let next_page = null;
        let first_page = null;
        let total_form_page = 0;
        let user_status = '<?php echo $this->session->userdata('user')['id_usr_status']; ?>';
        let refreshValidationCheck = function() {
            $.ajax({
                url: site_url + 'required_info/check_required_form_validation',
                type: 'post',
                dataType: 'json',
                data: postDataWithCsrf.data(),
                success: function(res) {

                    $('button[type="submit"]').html('<i class="fa fa-save"></i> Submit');
                    $('#cancel').html('<i class="fa fa-times"></i> Batal');
                    //console.log(res);
                    Object.keys(res.detail).forEach(function(key) {
                        $(`.nav-item[key="${key}"]`).removeClass('done');
                        let {
                            title,
                            description,
                            link
                        } = res.detail[key].form_detail;
                        let {
                            is_valid
                        } = res.detail[key];
                        total_percentage = res.percentage;

                        if (is_valid) {
                            $(`.nav-item[key="${key}"]`).addClass('done');
                        }

                        if (total_percentage == 100) {
                            $(`.nav-item[key="step-finish"]`).addClass('done');
                        } else {
                            $(`.nav-item[key="step-finish"]`).removeClass('done');
                        }

                        $(`.nav-link[key="${key}"]`).attr('is_valid', `${is_valid}`);

                    });
                },
                error: function(xhr, res, err) {
                    console.log(err);
                },
            });
        }

        if (user_status == '5') {
            let createdDate = `<?php $user = $this->db->where('id_user', $this->session->userdata('user')['id_user'])
                                    ->get('sys_user')->row();
                                echo $user->created_at; ?>`;
            let remainingTime = moment(createdDate, 'YYYY-MM-DD hh:mm:ss').add('days', 2);

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


        let ryLinx = RyLinxClass('#rylinx_content1');

        let updatePageCounter = function() {
            prev_page = parseInt(current_page) - 1;
            next_page = parseInt(current_page) + 1;

            $('button[type="submit"]').html('<i class="fa fa-save"></i> Submit');
            $('#cancel').html('<i class="fa fa-times"></i> Batal');
        }
        $.ajax({
            url: site_url + 'required_info/check_required_form_validation',
            type: 'post',
            dataType: 'json',
            async: false,
            data: postDataWithCsrf.data(),
            success: function(res) {
                $('#prev-btn-wizard').css('display', 'none');
                let selectionContainer = $('#selection_required_wizard');
                let menus = '';
                let i = 0;
                let total_percentage = 0;
                Object.keys(res.detail).forEach(function(key) {
                    let {
                        title,
                        description,
                        link
                    } = res.detail[key].form_detail;
                    let {
                        is_valid
                    } = res.detail[key];
                    total_percentage = res.percentage;
                    i += 1;

                    const realLink = link;

                    if (i == 1) first_page = realLink;
                    link = (link != null) ? link.replace('/', '-') : 'step';
                    menus += `
                    <li key="${key}" link="${realLink}" class="nav-item ${is_valid?'done':''} ${i==1?'active':''}">
                        <a href="javascript::void();" key="${key}"  is_valid="${is_valid}" class="nav-link index-${i}" index="${i}">
                            <em>${i}</em><span>${title}</span>
                        </a>
                    </li>`;
                });
                total_form_page = i;

                i++;
                menus += `
                <li key="step-finish" class="nav-item ${total_percentage==100?'done':''}">
                    <a href="javascript::void();" class="nav-link index-${i}"  index="${i}">
                        <em>${i}</em><span>Selesai</span>
                    </a>
                </li>`;

                selectionContainer.html(menus);

                ryLinx.to(first_page, function() {
                    $('#rylinx_content1').find('.app-page-title').remove();
                    $('#rylinx_content1').find('.card').attr('class', '');
                    $('#rylinx_content1').find('button[type="reset"]').remove();
                    window.history.pushState('page2', 'Title', site_url + 'app');
                    current_page = 1;
                    updatePageCounter();
                    onSubmitDone();

                    if ($('#verification_status').val() != 'Rejected') {
                        $('#verification_status').parent('div').parent('div').hide();
                        $('#verification_note').parent('div').parent('div').hide();
                    }
                });

                $('.nav-link').click(function() {
                    let index = $(this).attr('index');
                    (index == 1) ? $('#prev-btn-wizard').css('display', 'none'): $('#prev-btn-wizard').css('display', 'block');;

                    if (parseInt(index) <= total_form_page) {
                        $('#next-btn-wizard').html('<i class="fa fa-arrow-right"></i> Selanjutnya');
                        $('#step-finish').css('display', 'none');
                        $('#rylinx_content1').attr('style', 'display:block');
                        let is_valid = $(this).attr('is_valid');
                        let link = $(this).parent('li').attr('link');
                        let index = $(this).attr('index');

                        if (is_valid == 'true') {
                            ryLinx.to(link, function() {
                                $('#rylinx_content1').find('.app-page-title').remove();
                                $('#rylinx_content1').find('.card').attr('class', '');
                                $('#rylinx_content1').find('button[type="reset"]').remove();
                                window.history.replaceState("", "", site_url + 'app');
                                current_page = index;
                                updatePageCounter();
                                $('.nav-item').each(function(i, ele) {
                                    if ($(this).hasClass('active')) {
                                        $(this).removeClass('active');
                                    }
                                });
                                $(this).parent('li').addClass('active');
                                onSubmitDone();

                                if ($('#verification_status').val() != 'Rejected') {
                                    $('#verification_status').parent('div').parent('div').hide();
                                    $('#verification_note').parent('div').parent('div').hide();
                                }

                            });
                        } else {
                            window.history.replaceState("", "", site_url + 'app');
                        }
                    } else {
                        let is_prev_valid = $('.index-' + total_form_page).attr('is_valid');
                        if (is_prev_valid == 'true') {

                            $('#step-finish').css('display', 'block');
                            $('.nav-item').each(function(i, ele) {
                                if ($(this).hasClass('active')) {
                                    $(this).removeClass('active');
                                }
                            });
                            $(this).parent('li').addClass('active');
                            $('#rylinx_content1').attr('style', 'display:none');
                            current_page = index;
                            updatePageCounter();
                            window.history.replaceState("", "", site_url + 'app');
                            $("html, body").animate({
                                scrollTop: 0
                            }, "slow");
                            $('#next-btn-wizard').html('<i class="fa fa-paper-plane"></i> Setuju & Submit');
                        }
                    }

                });

                $('#next-btn-wizard').click(function() {

                    if ($('#form-container').is(':visible') || current_page == 1) {
                        $('button[type="submit"]').click();
                        refreshValidationCheck();
                    }

                    let index = $('.index-' + next_page).attr('index');
                    $('#prev-btn-wizard').css('display', 'block');
                    if (parseInt(index) <= total_form_page) {

                        $('#next-btn-wizard').html('<i class="fa fa-arrow-right"></i> Selanjutnya');
                        $('#step-finish').css('display', 'none');
                        $('#rylinx_content1').attr('style', 'display:block');

                        let is_valid = $('.index-' + next_page).attr('is_valid');
                        let is_prev_valid = $('.index-' + current_page).attr('is_valid');
                        let link = $('.index-' + next_page).parent('li').attr('link');
                        let index = $('.index-' + next_page).attr('index');
                        if (is_prev_valid == 'true') {
                            ryLinx.to(link, function() {
                                $('#rylinx_content1').find('.app-page-title').remove();
                                $('#rylinx_content1').find('.card').attr('class', '');
                                $('#rylinx_content1').find('button[type="reset"]').remove();
                                window.history.replaceState("", "", site_url + 'app');

                                $('.nav-item').each(function(i, ele) {
                                    if ($(this).hasClass('active')) {
                                        $(this).removeClass('active');
                                    }
                                });
                                $('.index-' + next_page).parent('li').addClass('active');
                                current_page = index;
                                updatePageCounter();
                                onSubmitDone();

                                if ($('#verification_status').val() != 'Rejected') {
                                    $('#verification_status').parent('div').parent('div').hide();
                                    $('#verification_note').parent('div').parent('div').hide();
                                }

                            });
                        } else {
                            window.history.replaceState("", "", site_url + 'app');
                        }
                    } else if (parseInt(index) == total_form_page + 1) {
                        let is_prev_valid = $('.index-' + total_form_page).attr('is_valid');
                        if (is_prev_valid == 'true') {
                            $('#step-finish').css('display', 'block');
                            $('.nav-item').each(function(i, ele) {
                                if ($(this).hasClass('active')) {
                                    $(this).removeClass('active');
                                }
                            });
                            $('.index-' + next_page).parent('li').addClass('active');
                            $('#rylinx_content1').attr('style', 'display:none');
                            current_page = index;
                            updatePageCounter();
                            window.history.replaceState("", "", site_url + 'app');
                            $("html, body").animate({
                                scrollTop: 0
                            }, "slow");
                            $('#next-btn-wizard').html('<i class="fa fa-paper-plane"></i> Setuju & Submit');
                        }
                    } else {
                        $.post(site_url + 'required_info/submit_registration', postDataWithCsrf.data(), function(data) {
                            if (data.success) {
                                swal('Submit Data ' + ((data.success) ? 'Berhasil !' : 'Gagal !'), data.msg, (data.success) ? 'success' : 'danger')
                                    .then(function() {
                                        window.location.href = site_url + 'auth/logout';
                                    });
                            }
                        }, 'json');
                    }

                });

                $('#prev-btn-wizard').click(function() {
                    let index = $('.index-' + prev_page).attr('index');

                    if (parseInt(index) <= total_form_page) {
                        $('#next-btn-wizard').html('<i class="fa fa-arrow-right"></i> Selanjutnya');
                        $('#step-finish').css('display', 'none');
                        $('#rylinx_content1').attr('style', 'display:block');
                        let is_prev_valid = $('.index-' + prev_page).attr('is_valid');

                        let link = $('.index-' + prev_page).parent('li').attr('link');
                        let index = $('.index-' + prev_page).attr('index');
                        if (is_prev_valid == 'true') {
                            ryLinx.to(link, function() {
                                $('#rylinx_content1').find('.app-page-title').remove();
                                $('#rylinx_content1').find('.card').attr('class', '');
                                $('#rylinx_content1').find('button[type="reset"]').remove();
                                window.history.replaceState("", "", site_url + 'app');

                                $('.nav-item').each(function(i, ele) {
                                    if ($(this).hasClass('active')) {
                                        $(this).removeClass('active');
                                    }
                                });
                                $('.index-' + prev_page).parent('li').addClass('active');
                                current_page = index;
                                if (current_page == 1) $('#prev-btn-wizard').css('display', 'none');
                                updatePageCounter();
                                onSubmitDone();

                                if ($('#verification_status').val() != 'Rejected') {
                                    $('#verification_status').parent('div').parent('div').hide();
                                    $('#verification_note').parent('div').parent('div').hide();
                                }

                            });
                        } else {
                            window.history.replaceState("", "", site_url + 'app');
                        }

                    } else {

                        let is_prev_valid = $('.index-' + prev_page).attr('is_valid');
                        if (is_prev_valid == 'true') {

                            $('#step-finish').css('display', 'block');
                            $('.nav-item').each(function(i, ele) {
                                if ($(this).hasClass('active')) {
                                    $(this).removeClass('active');
                                }
                            });
                            $('.index-' + prev_page).parent('li').addClass('active');
                            $('#rylinx_content1').attr('style', 'display:none');
                            current_page = index;
                            updatePageCounter();
                            window.history.replaceState("", "", site_url + 'app');
                            $('#next-btn-wizard').html('<i class="fa fa-paper-plane"></i> Setuju & Submit');
                        }

                    }

                });


                let onSubmitDone = function() {

                    globalEvents.onDone = function(id, data) {
                        if (id == 'formSubmit') {
                            if (current_page == 1) {
                                // let link = $('.index-' + current_page).parent('li').attr('link');
                                // ryLinx.to(link, ()  {
                                //     $('#rylinx_content1').find('.app-page-title').remove();
                                //     $('#rylinx_content1').find('.card').attr('class', '');
                                //     $('#rylinx_content1').find('button[type="reset"]').remove();
                                //     refreshValidationCheck();

                                //     if ($('#verification_status').val() != 'Rejected') {
                                //         $('#verification_status').parent('div').parent('div').hide();
                                //         $('#verification_note').parent('div').parent('div').hide();
                                //     }
                                // });
                            } else {
                                refreshValidationCheck();
                            }


                        }
                    }
                };

            },
            error: function(xhr, res, err) {
                //console.log(err);
            }
        });

    });
</script>