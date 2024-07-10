<script>
	logout = function() {
		window.location.href = site_url + 'auth/logout';
	}

	window.onhashchange=function(e){
		var link=window.location.hash;
		if(link==null || link=="") link='#dashboard';
		RyLinx.to(link, function() {
			$('#loading_content').hide();
			Notification.refresh_counter();
		});
	};

	$(document).ready(function() {
		$('#loading_content').hide();
		RyLinx.autoload(function() {
			$('#loading_content').hide();

			$('.rylinx-nav').unbind('click').click(function() {
				$('#loading_content').show();
				RyLinx.to($(this).attr('href'), function() {
					$('#loading_content').hide();
					Notification.refresh_counter();
				});
			});

		});

		$(".input-mask-trigger").inputmask();

		$('.changelog').click(function() {
			basicModal({
				title: 'Riwayat Versi Aplikasi',
				body: `<iframe style="border:0px;width:100%;height:500px;" src="<?php echo base_url('assets/file/changelog.html'); ?>"></iframe>`,
				footer: ``
			}).show(function(modal) {});
		});
	});
</script>

<!-- Validator -->
<!-- <script src="<?php echo base_url('assets/vendor/jquery/jquery.form-validator.min.js'); ?>"></script> -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.3.26/jquery.form-validator.min.js"></script>


<!-- Pooper -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>

<!-- Bootstrap -->
<!-- <script src="<?php echo base_url('assets/vendor/bootstrap/js/bootstrap.min.js'); ?>"></script> -->
<!-- SweetAlert -->
<script src="<?php echo base_url('assets/vendor/sweetalert/sweetalert.min.js'); ?>"></script>
<!-- Datatables -->
<script src="<?php echo base_url('assets/vendor/datatables/js/pdfmake.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/vfs_fonts.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/datatables.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datatables/js/select.datatables.min.js'); ?>"></script>

<!-- Select2 -->
<script src="<?php echo base_url('assets/vendor/select2/js/select2.min.js'); ?>"></script>
<!-- Custom -->
<script type="text/javascript" src="<?php echo base_url('assets/templates/admin/scripts/main.js'); ?>"></script>

<!-- Datepicker -->
<script src="<?php echo base_url('assets/vendor/moment/moment-with-locales.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/datetimepicker/js/bootstrap-datetimepicker.min.js'); ?>"></script>

<!-- CKEditor -->
<!-- <script src="<?php echo base_url('assets/vendor/ckeditor/js/ckeditor-full.js'); ?>"></script> -->
<script src="https://cdn.ckeditor.com/4.12.1/full/ckeditor.js"></script>


<!-- fileuploader -->
<script src="https://hayageek.github.io/jQuery-Upload-File/4.0.11/jquery.uploadfile.min.js"></script>

<!-- Notify -->
<script src="<?php echo base_url('assets/vendor/notify/notify.min.js'); ?>"></script>

<!-- Vue JS -->
<script src="<?php echo base_url('assets/vendor/vuejs/vue.min.js'); ?>"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/vue-resource/1.5.1/vue-resource.min.js"></script> -->

<!-- Axios -->
<!-- <script src="<?php echo base_url('assets/vendor/axios/axios.min.js'); ?>"></script> -->

<!-- PrintThis -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js"></script>

<!-- TinyMCE -->
<script src="<?php echo base_url('assets/vendor/tinymce/tinymce.min.js'); ?>"></script>


<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"></script>

<script src="<?php echo base_url('assets/vendor/jquery/jqDownloader.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/jquery/qrcode.min.js'); ?>"></script>

<!-- Input Mask -->
<script src="<?php echo base_url('assets/vendor/inputmask/jquery.inputmask.js'); ?>"></script>
<script src="<?php echo base_url('assets/vendor/inputmask/bindings/inputmask.binding.js'); ?>"></script>

<!--Start of Tawk.to Script-->
<!-- <script type="text/javascript">
	var Tawk_API = Tawk_API || {},
		Tawk_LoadStart = new Date();
	(function() {
		var s1 = document.createElement("script"),
			s0 = document.getElementsByTagName("script")[0];
		s1.async = true;
		s1.src = 'https://embed.tawk.to/5e2fd9a28e78b86ed8ab5f35/default';
		s1.charset = 'UTF-8';
		s1.setAttribute('crossorigin', '*');
		s0.parentNode.insertBefore(s1, s0);
	})();
</script> -->
<!--End of Tawk.to Script-->

<!-- HighChart -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js"></script>