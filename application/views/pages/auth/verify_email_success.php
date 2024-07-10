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
            <div class="wrap-login100 p-l-20 p-r-20 p-t-35 p-b-24" style="width:580px !important;">
                <center>
                    <?php if (!$success) { ?>
                        <h1 style="color:red;"><?php echo $result; ?></h1>
                        <br>
                        <a href="<?php echo site_url('auth') ?>" class="btn btn-lg btn-default">Kembali Ke Login</a>
                    <?php } else { ?>
                        <h1 style="color:green;"><?php echo $result; ?></h1>
                        <br>
                        <a href="<?php echo site_url('auth') ?>" class="btn btn-lg btn-success">Kembali Ke Login</a>
                    <?php } ?>
                </center>
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

</body>

</html>