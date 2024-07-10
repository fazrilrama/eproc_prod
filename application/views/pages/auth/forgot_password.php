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
				<?php echo form_open(null, 'id="forgot_pass_form" class="login100-form validate-form"'); ?>
				<div style="text-align:center;">
					<img style="margin-bottom:5px;" width="150px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/company_logo.png" alt="">
				</div>
				<div style="text-align:center;">
					<img width="200px" height="auto" src="<?php echo base_url(); ?>assets/img/logo/app_logo.png" alt="">
				</div>
				<span class="login100-form-title p-b-10">
					<p style="margin-top:10px;">Password confirmation will be send to your email.</p>
				</span>
				<div id="login_alert_container">

				</div>

				<div class="wrap-input100 validate-input m-b-23" data-validate="Email is required">
					<span class="label-input100">Email</span>
					<input class="input100" type="email" name="identity" id="identity" placeholder="Type your Email">
					<span class="focus-input100" data-symbol="&#xf206;"></span>
				</div>

				<div class="container-login100-form-btn">
					<div class="wrap-login100-form-btn">
						<div class="login100-form-bgbtn"></div>
						<button id="submit" type="submit" class="login100-form-btn">
							Submit
						</button>
					</div>
					<div style="margin-top:10px;">
						<a href="<?php echo site_url('auth') ?>">Back to Login</a>
					</div>
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

	<script>
		$(document).ready(function() {

			$('#forgot_pass_form').on('submit', function(e) {
				e.preventDefault();
				if ($('#identity').val().length > 0) {
					$('#login_alert_container').html(null);
					$alert = '<div id="login_alert" class="alert alert-warning alert-dismissible fade show" role="alert">\
				<span id="login_msg"></span>\
				</div>';

					$('#login_alert_container').html($alert);
					$('#login_alert').show();
					$('#login_msg').html("Please wait, sending email...");
					$('#login_alert').attr("class", "alert alert-primary alert-dismissible fade show");

					$('#submit').attr('disabled', 'disabled');
					$('#submit').css('background-color', 'grey');

					$.ajax({
						url: '<?php echo site_url(); ?>auth/request_forgot_password',
						type: 'POST',
						dataType: 'json',
						data: $(this).serialize(),
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
								$('#submit').remove();
							} else {
								$('#login_alert').show();
								$('#login_msg').html(res.result);
								$('#login_alert').attr("class", "alert alert-danger alert-dismissible fade show");

								$('#submit').removeAttr('disabled');
								$('#submit').css('background-color', '');
							}

							$('#unblock_link_alert').click(function() {
								window.location.href = '<?php echo site_url() ?>' + $(this).attr('href');
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

			});
		});

		$(".toggle-password").click(function() {
			$(this).toggleClass("fa-eye fa-eye-slash");
			var ele = $('#password');
			(ele.attr('type') == "password") ? ele.attr('type', 'text'): ele.attr('type', 'password');
		});
	</script>

</body>

</html>