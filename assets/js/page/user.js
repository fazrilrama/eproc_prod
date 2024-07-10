var unblockUser=function(id){
	$.ajax({
		url:site_url+'user/unblockUser',
		type:'post',
		data:{
			idUser:id
		},
		dataType:'json',
		success:function(res){
			alert(res.message);
			if(res.success){
				$('#table-data').DataTable().ajax.reload();
			}
		},
		error:function(xhr,stat,err){
			alert(err);
		}
	});
}

$(document).ready(function () {
	
	callbacks.onGetEdit = function (selectedID, data) {
		if (selectedID != null) {
			$('#password_confirmation').attr('data-validation', '');
			$('#password').attr('data-validation', '');
		} else {
			$('#password_confirmation').attr('data-validation', 'required');
			$('#password').attr('data-validation', 'required');
		}
	}
});
