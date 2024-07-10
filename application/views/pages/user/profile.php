<div class="card" id="content">
	<div class="card-header">
		<h5 class="card-title" id="form-label">User Profile</h5>
	</div>
	<div class="card-body">
		<div id="form-container">
			<?php echo form_open(null, 'id="form"') ?>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">NIK</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['nik'] ?>" class="form-control" disabled name="nik" id="nik" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Nama</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['emp_fullname'] ?>" class="form-control" disabled name="emp_fullname" id="emp_fullname" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Tgl Lahir</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['emp_dob'] ?>" class="form-control" disabled name="emp_dob" id="emp_dob" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Area</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['subarea_ket'] ?>" class="form-control" disabled name="subarea" id="subarea" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Unit</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['unit_objnm'] ?>" class="form-control" disabled name="unit" id="unit" type="none">
				</div>
			</div>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for="">Posisi</label>
				<div class="col-sm-10">
					<input value="<?php echo $user['pos_objnm'] ?>" class="form-control" disabled name="emp_position" id="emp_position" type="none">
				</div>
			</div>

			<div class="form-gruop row">
				<label class="col-sm-2 col-form-label" for="">Role User <span style="color:red;">*</span></label>
				<div class="col-sm-10">
					<input value="<?php echo $user['role_name'] ?>" class="form-control" disabled name="emp_position" id="emp_position" type="none">
				</div>
			</div>
			<br>
			<div class="form-gruop row">
				<label class="col-sm-2 col-form-label" for="">Status <span style="color:red;">*</span></label>
				<div class="col-sm-10">
					<input value="Active" class="form-control" disabled name="emp_position" id="emp_position" type="none">
				</div>
			</div>
			<br>
			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for=""> Password
				</label>
				<div class="col-sm-10">
					<input class="form-control" type="password" name="password_confirmation">
				</div>
			</div>

			<div class="form-group row">
				<label class="col-sm-2 col-form-label" for=""> Ulangi Password
				</label>
				<div class="col-sm-10" id="retype_password_container">
					<input class="form-control" type="password" data-validation="confirmation" data-validation-error-msg-container="#retype_password_container" data-validation-error-msg="Ulangi password tidak valid" name="password" id="retype_password">
				</div>
			</div>

			<label class="" style="color:#ff3333;">Required (*)</span></label>
			<br>
			Note:
			<br>
			<ul>
				<li>
					<span style="color:grey;">Only fill password if wanna change.</span>
				</li>
				<li>
					<span style="color:grey;">Personal data can be change only in <a target="_blank" href="http://siska.bgrlogistik.id">SISKA System</a>.</span>
				</li>
			</ul>


			<hr />
			<div class="row">
				<div class="col-md-12" style="text-align:right;">
					<button type="button" id="cancel" class="btn btn-danger"> <i class="fa fa-times"></i>
						Cancel</button>
					<button type="submit" id="submit" class="btn btn-primary"> <i class="fa fa-paper-plane"></i>
						Submit</button>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$.validate({
			modules: 'location, date, security, file',
			form: '#form',
			onSuccess: function(form) {
				event.preventDefault();
				$.ajax({
					url: site_url + 'user/change_password',
					type: 'post',
					dataType: 'json',
					data: postDataWithCsrf.data({
						password_confirmation: $('#retype_password').val()
					}),
					success: function(res) {
						if (res.success) {
							swal('Change Password', 'Password has been changed!', 'success');
						} else {

							swal('Change Password', 'Password failed to change!', 'error');
						}
					},
					error: function(xhr, res, err) {
						alert(err);
					}
				})
			}
		});

		$('#cancel').click(function() {
			window.location.href = site_url;
		});
	});
</script>