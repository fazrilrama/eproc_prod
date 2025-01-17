<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $this->config->item('app_info')['identity']['name'] ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/img/icon/fav_icon.png" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/bootstrap/css/bootstrap.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/fonts/iconic/css/material-design-iconic-font.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/animate/animate.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/css-hamburgers/hamburgers.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/animsition/css/animsition.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/select2/select2.min.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/vendor/daterangepicker/daterangepicker.css">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/css/util.css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets/templates/login/css/main.css">
    <!--===============================================================================================-->

    <style>
        .field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -32px;
            position: relative;
            z-index: 2;
            width: 35px;
            height: auto;
        }

        .container {
            padding-top: 50px;
            margin: auto;
        }

        .input100 {
            font-size: 14px !important;
            height: 40px !important;
        }

        .card {
            border-radius: 5px;
            -webkit-box-shadow: 10px 10px 20px 10px rgba(69, 90, 100, 0.1);
            box-shadow: 10px 10px 20px 10px rgba(69, 90, 100, 0.1);
            border: none;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>

    <div class="limiter">
        <div class="container-login100" style="background-image: url('<?php echo base_url(); ?>assets/templates/login/images/bg-01.jpg');">
            <div class="wrap-login100 p-l-20 p-r-20 p-t-35 p-b-24" style="width:90% !important;">
                <div style="text-align:center;">
                    <img style="margin-bottom:5px;" width="150px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/company_logo.png" alt="">
                </div>
                <div style="text-align:center;">
                    <img width="250px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/app_logo.png" alt="">
                </div>
                <span class="login100-form-title p-b-10">
                    <!-- <p style="margin-top:10px;">Please identify yourself</p> -->
                </span>
                <hr>
                <iframe src="<?php echo site_url('assets/tnc.html') ?>" height="1000px" width="100%"></iframe>
                <hr>
                <center>
                    <h4>Apakah anda setuju dengan syarat dan ketentuan di atas ?</h4>
                    <br>
                    <button data-backdrop="static" data-keyboard="false" data-toggle="modal" data-target="#exampleModalCenter" class="btn btn-lg btn-primary" type="button">Ya</button>
                    <a href="<?php echo site_url('auth') ?>" class="btn btn-lg btn-danger" type="button">Tidak</a>
                </center>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Form Pendaftaran</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div id="login_alert_container"></div>
                        <?php echo form_open(null, 'id="register_form" class="login100-form validate-form"'); ?>
                        <center hidden style="margin-bottom:5px;">
                            <h5 id="register_as_title">Sebagai Customer</h5>
                        </center>
                        <div hidden style="padding-top:20px;">
                            <div class="row">
                                <div class="col-md-2">

                                </div>
                                <div hidden class="col-md-4">
                                    <center id="as_customer" class="card register_as_change">
                                        <img style="padding:1px; margin-bottom:0px !important;" src="http://stylelinefurniture.net/wp-content/uploads/2017/02/newcustomer-282x300.png" width="100px;" height="100px;" alt="">
                                        <div><b>User</b></div>
                                    </center>
                                </div>
                                <div class="col-md-4">
                                    <center id="as_vendor" class="card register_as_change">
                                        <img style="padding:1px;  margin-bottom:0px !important;" src="<?php echo base_url('assets/img/icon/vendor_company.png') ?>" width="100px;" height="100px;" alt="">
                                        <div><b>Perusahaan</b></div>
                                    </center>
                                </div>
                                <div class="col-md-4">
                                    <center id="as_vendor_personal" class="card register_as_change">
                                        <img style="padding:1px;  margin-bottom:0px !important;" src="<?php echo base_url('assets/img/icon/vendor_personal.png') ?>" width="100px;" height="100px;" alt="">
                                        <div><b>Personal</b></div>
                                    </center>
                                </div>
                                <!-- <div class="col-md-2">

                            </div> -->
                            </div>
                        </div>

                        <div id="inner-form">
                            <input hidden id="register_as" name="register_as" type="text" class="form-control">
                            <div style="margin-bottom:10px;">
                                <div class="wrap-input100">
                                    <span class="label-input100">Email</span>
                                    <input data-validation="required email" data-validation-error-msg-container="#email_error" class="input100" type="email" name="email" id="email" placeholder="Type your email">
                                    <span class="focus-input100" data-symbol="&#xF206;"></span>
                                </div>
                                <div style="color:red;" class="label-input100" id="email_error"></div>
                            </div>
                            <div style="margin-bottom:10px;">
                                <div class="wrap-input100">
                                    <span class="label-input100">Password</span>
                                    <input data-validation="required length strength" data-validation-strength="2" data-validation-length="min8" data-validation-error-msg-container="#password_error" class="input100" type="password" name="password_confirmation" id="password_confirmation" placeholder="Type your password">
                                    <span toggle="#password_confirmation" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                                </div>
                                <div style="color:red;" class="label-input100" id="password_error"></div>
                                <span style="font-size:0.8em"> Kekuatan Password :</span> <input type="password" hidden disabled id="password_strength">
                            </div>

                            <div class="wrap-input100">
                                <span class="label-input100">Konfirmasi Password</span>
                                <input data-validation="required confirmation" data-validation-error-msg-container="#password_confirm_error" class="input100" id="password" type="password" name="password" placeholder="Confirm your password">
                                <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                <span class="focus-input100" data-symbol="&#xf190;"></span>
                            </div>
                            <div style="color:red;margin-bottom:5px;" class="label-input100" id="password_confirm_error"></div>

                            <br>
                            <div class="p-b-20">
                                <div class="row">
                                    <div class="col-md-10">
                                        <div id="img_captcha"></div>
                                    </div>
                                    <div class="col-md-2 text-left">
                                        <i title="Refresh Captcha" class="fa fa-refresh" style="margin-left:10px;cursor:pointer; font-size:24px;" id="captcha_refresh"></i>
                                    </div>
                                    <div class="p-t-5 col-md-12">
                                        <input type="text" placeholder="Captcha Words" name="captcha_words" id="captcha_words" style="padding:3px; width:100%; border-radius:5px; height:100%; border: 1px solid lightgrey !important;" maxlength="100">
                                    </div>
                                </div>
                                <center><label style="color:red;" id="captcha_warn">Captcha words not match, please try again!.</label></center>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="close" type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button id="submit" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>


        <div id="dropDownSelect1"></div>

        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/jquery/jquery-3.2.1.min.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/animsition/js/animsition.min.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/bootstrap/js/popper.js"></script>
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/bootstrap/js/bootstrap.min.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/select2/select2.min.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/daterangepicker/moment.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/daterangepicker/daterangepicker.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/vendor/countdowntime/countdowntime.js"></script>
        <!--===============================================================================================-->
        <script src="<?php echo base_url(); ?>assets/templates/login/js/main.js"></script>


        <!-- Validator -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>


        <script>
            $(document).ready(function() {

                var captchaField = $('#img_captcha');
                var captchaWords = $('#captcha_words');
                var captchaWarn = $('#captcha_warn');
                var recaptcha = $('#captcha_refresh');
                var validate_url = '<?php echo site_url() . "auth/captcha_validate"; ?>';
                captchaWarn.hide();
                loadCaptcha();

                function loadCaptcha() {
                    $.ajax({
                        async: false,
                        url: '<?php echo site_url() . "auth/generate_captcha"; ?>',
                        type: 'GET',
                        dataType: 'html',
                        success: function(res) {
                            captchaField.html(res);
                        }
                    });
                }

                $('#password_confirmation').on('keyup', function() {
                    $('#password_strength').val($(this).val()).trigger('change').trigger('keyup');
                    $('.strength-meter').css('background', 'transparent');
                });

                recaptcha.click(function(e) {
                    e.preventDefault();
                    loadCaptcha();
                });

                captchaWords.click(function() {
                    captchaWarn.hide();
                });


                function checkCaptcha(onSuccess) {
                    $.ajax({
                        async: false,
                        url: validate_url,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            csrf_token: $('input[name="csrf_token"]').val(),
                            captcha_words: captchaWords.val()
                        },
                        success: function(res) {
                            if (res.is_valid) {
                                onSuccess();
                            } else {
                                captchaWarn.css('color', 'red');
                                captchaWarn.html('Captcha words not match, please try again!.');
                                captchaWarn.show();
                            }

                        }
                    });
                }


                $.validate({
                    form: '#register_form',
                    modules: 'location, date, security, file',
                    onModulesLoaded: function() {
                        var optionalConfig = {
                            fontSize: '10pt',
                            padding: '4px',
                            bad: 'Sangat Lemah',
                            weak: 'Lemah',
                            good: 'Cukup Kuat',
                            strong: 'Kuat'
                        };

                        $('#password_strength').displayPasswordStrength(optionalConfig);
                    },
                    onError: function($form) {},
                    onSuccess: function($form) {
                        event.preventDefault();
                        checkCaptcha(function() {
                            doSubmit($form);
                        });
                        return true;
                    }
                });

                $('#exampleModalCenter').on('show.bs.modal', function(e) {
                    $('#register_form')[0].reset();
                    captchaWarn.css('color', 'red');
                    captchaWarn.html('Captcha words not match, please try again!.');
                    captchaWarn.hide();
                    recaptcha.click();
                    switchRegisterAs('as_vendor');
                });

                function switchRegisterAs(id) {
                    if (id == 'as_customer') {
                        $('#register_as').val('3');
                        $('#register_as_title').html('Sebagai User');
                        $('#as_customer').attr('style', 'border:2px solid darkblue !important;');
                        $('#as_vendor').removeAttr('style');
                        $('#as_vendor_personal').removeAttr('style');
                    } else if (id == 'as_vendor') {
                        $('#register_as').val('2');
                        $('#register_as_title').html('Sebagai Vendor Perusahaan');
                        $('#as_vendor_personal').removeAttr('style');
                        $('#as_customer').removeAttr('style');
                        $('#as_vendor').attr('style', 'border:2px solid darkblue !important;');
                    } else if (id == 'as_vendor_personal') {
                        $('#register_as').val('6');
                        $('#register_as_title').html('Sebagai Vendor Personal');
                        $('#as_customer').removeAttr('style');
                        $('#as_vendor').removeAttr('style');
                        $('#as_vendor_personal').attr('style', 'border:2px solid darkblue !important;');
                    }
                }

                $('.register_as_change').click(function() {
                    let id = $(this).attr('id');
                    switchRegisterAs(id);
                });


                function doSubmit(form) {
                    $alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
                                <span id="login_msg"></span>\
                                </div>';

                    $('#login_alert_container').html($alert);
                    $('#login_alert').show();
                    $('#login_msg').html("Please wait, sending verification email...");
                    $('#login_alert').attr("class", "alert alert-primary alert-dismissible fade show");

                    $('#submit').attr('disabled', 'disabled');
                    $('#submit').css('background-color', 'grey');
                    $.ajax({
                        url: '<?php echo site_url('auth/do_register') ?>',
                        type: 'post',
                        dataType: 'json',
                        data: form.serialize(),
                        success: function(res) {
                            if (res.success) {
                                $alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
                                <span id="login_msg"></span>\
                                </div>';

                                $('#login_alert_container').html($alert);
                                $('#login_alert').show();
                                $('#login_msg').html(res.result);
                                $('#login_alert').attr("class", "alert alert-success alert-dismissible fade show");

                                $('#submit').remove();
                                $('#inner-form').remove();
                                $('#close').attr('class', 'btn btn-success');
                                $('#close').html('Registrasi berhasil, Klik Untuk Kembali');
                                $('#close').click(function() {
                                    window.location.href = '<?php echo site_url('auth') ?>';
                                });

                            } else {
                                $alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
                                <span id="login_msg"></span>\
                                </div>';

                                $('#login_alert_container').html($alert);
                                $('#login_alert').show();
                                $('#login_msg').html(res.result);
                                $('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");

                                $('#submit').removeAttr('disabled');
                                $('#submit').css('background-color', '');
                            }
                        },
                        error: function(err) {
                            $alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
                                <span id="login_msg"></span>\
                                </div>';

                            $('#login_alert_container').html($alert);
                            $('#login_alert').show();
                            $('#login_msg').html(err);
                            $('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");

                            $('#submit').removeAttr('disabled');
                            $('#submit').css('background-color', '');
                        }
                    })
                }

            });

            $(".toggle-password").click(function() {
                $(this).toggleClass("fa-eye fa-eye-slash");
                var input = $($(this).attr("toggle"));
                if (input.attr("type") == "password") {
                    input.attr("type", "text");
                } else {
                    input.attr("type", "password");
                }
            });
        </script>

</body>

</html>