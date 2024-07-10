<!-- ***** About Us Area Start ***** -->
<br>
<br>
<style>
    .form-control{
        padding: 1.3rem .75rem;
        border-radius: 35px;
    }
    .input-group-text{
        border-radius: 25px;
        background-color: white;
    }
</style>

    <!-- ***** Welcome Area End ***** -->
    <style>
        .card {
    /* Add shadows to create the "card" effect */
    /* box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); */
    /* transition: 0.3s; */
    border-radius: 25px;
    }

    /* On mouse-over, add a deeper shadow */
    .card:hover {
    /* box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2); */
    }
    </style>
<section class="uza-about-us-area">
    <div class="container">
        <div class="row align-items-center">
            <!-- About Thumbnail -->
            <div class="col-12 col-md-6">
                <div class="about-us-thumbnail mb-80">
                    <img style="height:400px;width:600px; object-fit:cover; border-radius:10px;" src="<?= base_url('assets/img/system/kerjasama.jpg')?>" alt="" data-pagespeed-url-hash="4062730999"
                        onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
                    <h4 style="color: grey;text-align:left;font-size:16pt;">Kami menyediakan pengadaan barang/jasa yang cocok untuk Bisnis Anda</h4>
                    <!-- Video Area -->
                    <div class="uza-video-area hi-icon-effect-8">
                        <a href="https://www.youtube.com/watch?v=HzZU_owM0LI" class="hi-icon video-play-btn"><i
                                class="fa fa-play" aria-hidden="true"></i></a>
                    </div>
                </div>
            </div>
            <!-- About Us Content -->
            <div class="col-12 col-md-6">
                <div class="about-us-content mb-80">
                    <h4 style="text-align: center;color:grey;">Untuk Menjadi Rekanan/Vendor Kami<br>Silahkan Mendaftar</h4>
                    <div class="card">
                        <div class="card-body">
                        <?php echo form_open(null, 'id="login_form" class="login100-form validate-form"'); ?>
                        <div id="login_alert_container"></div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1"><i class="fa fa-envelope"></i></span>
                            </div>
                            <input class="form-control" type="email" name="identity" id="identity"
                                placeholder="Ketik email Anda">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">
                                    <i toggle="#password" class="fa fa-eye toggle-password"></i>
                                </span>
                            </div>
                            <input class="form-control" id="password" type="password" name="password" id="password"
                                placeholder="Ketik kata sandi Anda">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div hidden class="text-left p-t-8 p-b-5" style="font-size:0.9em;">
                                    <input type="checkbox" class="" name="" id=""> Mengingat login
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-right p-t-8 p-b-5" >
                                    <a style="color:grey;" href="<?php echo base_url('auth/forgot_password'); ?>">
                                        Lupa kata sandi?
                                    </a>
                                </div>
                            </div>
                        </div>
                        <br>

                        <div>
                            <div class="row">
                                <div class="col-md-10">
                                    <div id="img_captcha"></div>
                                </div>
                                <div class="col-md-2 text-left">
                                    <i title="Refresh Captcha" class="fa fa-refresh" style="cursor:pointer; font-size:24px;"
                                        id="captcha_refresh"></i>
                                </div>
                                <div class="p-t-5 col-md-12" style="margin-top: 10px;">
                                    <input type="text" placeholder="Captcha Words" name="captcha_words" id="captcha_words"
                                        class="form-control" maxlength="100">
                                </div>
                            </div>
                            <center><label style="color:red;" id="captcha_warn">Captcha words not match, please try
                                    again!.</label></center>
                        </div>
                        <hr>
                        <button id="submit" type="submit" class="btn uza-btn btn-3 mt-15 btn-block btn-primary">
                            Login
                        </button>

                        <br>
                        <div style="text-align:center;">
                            Tidak punya akun?, <a style="font-size:16px;" href="<?php echo site_url('auth/register') ?>">Daftar
                                Disini.</a>
                        </div>

                        <hr />
                        <div style="text-align:center;"><a target="_blank"
                                href="<?php echo site_url('assets/file/MANUAL_BOOK.pdf');?>">Tutorial Penggunaan
                                Aplikasi</a></div>
                        </form>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- About Background Pattern -->
    <div class="about-bg-pattern">
        <img src="https://preview.colorlib.com/theme/uza/img/core-img/curve-2.png" alt="" data-pagespeed-url-hash="1264212511"
            onload="pagespeed.CriticalImages.checkImageForCriticality(this);">
    </div>
</section>
<!-- ***** About Us Area End ***** -->

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

			recaptcha.click(function(e) {
				e.preventDefault();
				loadCaptcha();
			});

			captchaWords.click(function() {
				captchaWarn.hide();
			});

			$('#login_form').submit(function(e) {
				e.preventDefault();
				checkCaptcha();
			});

			function checkCaptcha() {
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
							doLogin();
						} else {
							$('#login_alert_container').html(null);
							$alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
						<span id="login_msg"></span>\
						</div>';

							$('#login_alert_container').html($alert);
							$('#login_alert').show();
							$('#login_msg').html("Captcha not valid, please try again!");
							$('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");
						}

					}
				});
			}

			function doLogin() {
				if ($('#identity').val().length > 0 && $('#password').val().length > 0) {
					$('#login_alert_container').html(null);
					$alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
					<span id="login_msg"></span>\
					</div>';

					$('#login_alert_container').html($alert);
					$('#login_alert').show();
					$('#login_msg').html("Please wait, validating login...");
					$('#login_alert').attr("class", "alert alert-primary alert-dismissible fade show");

					$('#submit').attr('disabled', 'disabled');
					$('#submit').css('background-color', 'grey');

					$.ajax({
						url: '<?php echo site_url(); ?>auth/login',
						type: 'POST',
						dataType: 'json',
						data: $('#login_form').serialize(),
						success: function(res) {
							$alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
						<span id="login_msg"></span>\
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">\
								<span aria-hidden="true">&times;</span>\
						</button>\
					</div>';

							$('#login_alert_container').html($alert);

							if (res.success) {
								$('#login_alert_container').html();
								$('#login_alert').show();
								$('#login_msg').html(res.result);
								$('#login_alert').attr("class", "alert alert-success alert-dismissible fade show");
								var n = 1;
								var timeoutID = window.setInterval(function() {
									// this callback will execute every second until we call
									// the clearInterval method

									// $('#login_msg').html('Redirecting in '+n+'s');
									n--;
									if (n === 0) {
										// TODO: go ahead and really log the dude out
										window.location.href = '<?php echo site_url(); ?>app';
									}
								}, 1000);
							} else {
								$('#login_alert').show();
								$('#login_msg').html(res.result);
								$('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");

								$('#submit').removeAttr('disabled');
								$('#submit').css('background-color', '');
								recaptcha.click();
							}


							$('#unblock_link_alert').click(function() {
								window.location.href = '<?php echo site_url() ?>' + $('#login_form').attr('href');
							});

						},
						error: function(xhr, res, err) {
							$('#submit').removeAttr('disabled');
							$('#submit').css('background-color', '');
							$('#login_alert').show();
							$('#login_msg').html(err);
							$('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");
						}
					});
				}

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