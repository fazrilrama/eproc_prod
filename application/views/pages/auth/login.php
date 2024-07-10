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
	</style>
</head>

<body>

	<div class="limiter">
		<div class="container-login100" style="background-image: url('<?php echo base_url(); ?>assets/templates/login/images/bg-01.jpg');">
			<div class="wrap-login100 p-l-20 p-r-20 p-t-35 p-b-24" style="width:380px !important;">
				<?php echo form_open(null, 'id="login_form" class="login100-form validate-form"'); ?>
				<div style="text-align:center;">
					<img style="margin-bottom:5px;" width="150px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/company_logo.png" alt="">
				</div>
				<div style="text-align:center;">
					<img width="200px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/app_logo.png" alt="">
				</div>
				<span class="login100-form-title p-b-10">
					<!-- <p style="margin-top:10px;">Please identify yourself</p> -->
				</span>
				<div id="login_alert_container">

				</div>

				<div class="wrap-input100 validate-input m-b-23" data-validate="Email is required">
					<span class="label-input100">Email</span>
					<input class="input100" type="email" name="identity" id="identity" placeholder="Ketik email Anda">
					<span class="focus-input100" data-symbol="&#xf206;"></span>
				</div>

				<div class="wrap-input100 validate-input" data-validate="Password is required">
					<span class="label-input100">Kata Sandi</span>
					<input class="input100" id="password" type="password" name="password" id="password" placeholder="Ketik kata sandi Anda">
					<span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
					<span class="focus-input100" data-symbol="&#xf190;"></span>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="text-left p-t-8 p-b-5" style="font-size:0.9em;">
							<input type="checkbox" class="" name="" id=""> Mengingat login
						</div>
					</div>
					<div class="col-md-6">
						<div class="text-right p-t-8 p-b-5">
							<a href="<?php echo base_url('auth/forgot_password'); ?>">
								Lupa kata sandi?
							</a>
						</div>
					</div>
				</div>

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

				<div class="container-login100-form-btn">
					<div class="wrap-login100-form-btn">
						<div class="login100-form-bgbtn"></div>
						<button id="submit" type="submit" class="login100-form-btn">
							Login
						</button>
					</div>
				</div>

				<br>
				<div style="text-align:center;">
					Tidak punya akun?, <a style="font-size:16px;" href="<?php echo site_url('auth/register') ?>">Daftar Disini.</a>
				</div>
				
				<hr/>
				<div style="text-align:center;"><a target="_blank" href="<?php echo site_url('assets/file/MANUAL_BOOK.pdf');?>">Tutorial Penggunaan Aplikasi</a></div>

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

</body>

</html>