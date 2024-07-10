<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta http-equiv="Content-Language" content="en">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $this->config->item('app_info')['identity']['name'] ?></title>
	<link rel="icon" type="image/png" href="<?php echo base_url(); ?>assets/img/logo/company_logo.png" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />
	<meta name="description" content="This is an example dashboard created using build-in elements and components.">
	<meta name="msapplication-tap-highlight" content="no">
	<!--
    =========================================================
    * ArchitectUI HTML Theme Dashboard - v1.0.0
    =========================================================
    * Product Page: https://dashboardpack.com
    * Copyright 2019 DashboardPack (https://dashboardpack.com)
    * Licensed under MIT (https://github.com/DashboardPack/architectui-html-theme-free/blob/master/LICENSE)
    =========================================================
    * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
    -->

	<!-- Bootstrap -->
	<!-- <link href="<?php echo base_url('assets/vendor/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet"> -->

	<!-- ArchitectUI -->
	<link href="<?php echo base_url('assets/templates/admin/main.css'); ?>" rel="stylesheet">

	<!-- Datatables -->
	<link href="<?php echo base_url('assets/vendor/datatables/css/datatables.min.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/vendor/datatables/css/select.datatables.min.css'); ?>" rel="stylesheet">

	<!-- Select2 -->
	<link href="<?php echo base_url('assets/vendor/select2/css/select2.min.css'); ?>" rel="stylesheet" />

	<!-- File Uploader -->
	<link href="https://hayageek.github.io/jQuery-Upload-File/4.0.11/uploadfile.css" rel="stylesheet">

	<!-- JqueryUI -->
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.0/themes/smoothness/jquery-ui.css" />

	<!-- Datepicker -->
	<link rel="stylesheet" href="<?php echo base_url('assets/vendor/datetimepicker/css/bootstrap-datetimepicker.min.css'); ?>" rel="stylesheet" />

	<!-- Autonumeric -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/autonumeric/4.1.0/autoNumeric.min.js"></script>

	<!-- Font Awesome -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">

	<!-- Timeline -->
	<style>
		.modal-dialog-large,
		.modal-content-large {
			height: 100% !important;
		}

		ul.timeline {
			list-style-type: none;
			position: relative;
		}

		ul.timeline:before {
			content: ' ';
			background: #d4d9df;
			display: inline-block;
			position: absolute;
			left: 29px;
			width: 2px;
			height: 100%;
			z-index: 400;
		}

		ul.timeline>li {
			margin: 20px 0;
			padding-left: 20px;
		}

		ul.timeline>li:before {
			content: ' ';
			background: white;
			display: inline-block;
			position: absolute;
			border-radius: 50%;
			border: 3px solid #22c0e8;
			left: 20px;
			width: 20px;
			height: 20px;
			z-index: 400;
		}
	</style>

	<style>
		.modal-dialog-large,
		.modal-content-large {
			/* 80% of window height */
			height: 90%;
		}

		.modal-body-large {
			/* 100% = dialog height, 120px = header + footer */
			max-height: calc(100% - 120px);
			overflow-y: scroll;
		}

		.breadcrumb-item a {
			text-decoration: none;
			color: black;
		}

		.active a {
			color: #0066ff;
		}

		.loading {
			background-color: rgba(40, 57, 47, 0.39);
			width: 100%;
			height: 100%;
			top: 0px;
			left: 0px;
			position: fixed;
			display: block;
			z-index: 99
		}

		.loading-image {
			margin-top: 50px;
			width: 7em;
			z-index: 100;
		}

		.loading-label {
			color: white;
			font-size: 24px;
		}
	</style>

	<style>
		.form-error {
			color: red;
		}

		.ajax-upload-dragdrop {
			width: 100% !important;
		}

		.ajax-file-upload {
			text-align: center;
			font-size: 14px !important;
			font-weight: normal !important;
		}

		.ajax-file-upload-statusbar {
			width: 100% !important;
		}

		.ajax-file-upload-progress {
			border: 0px solid white !important;
		}

		.ajax-file-upload-error {
			color: red;
		}
	</style>

	<style>
		.notifyjs-metro-base {
			position: relative;
			min-height: 52px;
			color: #444;
		}

		.notifyjs-metro-base .image {
			display: table;
			position: absolute;
			height: auto;
			width: auto;
			left: 25px;
			top: 50%;

			-moz-transform: translate(-50%, -50%);
			-ms-transform: translate(-50%, -50%);
			-o-transform: translate(-50%, -50%);
			-webkit-transform: translate(-50%, -50%);
			transform: translate(-50%, -50%);
		}

		.notifyjs-metro-base .text-wrapper {
			display: inline-block;
			vertical-align: top;
			text-align: left;
			margin: 10px 10px 10px 52px;
			clear: both;
			font-family: 'Segoe UI';
		}

		.notifyjs-metro-base .title {
			font-size: 15px;
			font-weight: bold;
		}

		.notifyjs-metro-base .text {
			font-size: 12px;
			font-weight: normal;
			vertical-align: middle;
		}
	</style>

	<style>
		.logo-src {
			height: 46px !important;
		}

		.select2-selection {
			-webkit-box-shadow: 0;
			box-shadow: 0;
			background-color: #fff;
			border: 0;
			border-radius: 0;
			color: #555555;
			font-size: 14px;
			outline: 0;
			min-height: 39px;
			text-align: left;
		}

		.select2-selection__rendered {
			margin: 5px;
		}

		.select2-selection__arrow {
			margin: 5px;
		}
	</style>
</head>