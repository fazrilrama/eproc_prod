 <!-- JQuery -->
 <script src="<?php echo base_url('assets/vendor/jquery/jquery-3.4.1.min.js'); ?>"></script>




 <script>
 	var site_url = '<?php echo site_url(); ?>';
 	var base_url = '<?php echo base_url(); ?>';
 	var fc_path = '<?php echo str_replace('\\', '/', FCPATH); ?>';
 	var domain_url = 'https://<?php echo $_SERVER['HTTP_HOST']; ?>';
 	var user = {
 		id_user: '<?php echo $this->session->userdata('user')['id_user']; ?>',
 		role_id: '<?php echo $this->session->userdata('user')['id_usr_role']; ?>',
 		status_id: '<?php echo $this->session->userdata('user')['id_usr_status']; ?>',
 		role_name: '<?php echo $this->session->userdata('user')['role_name']; ?>',
 		notif_user_id: null
 	};
 </script>

 <script>
 	// Notification
 	var Notification = function() {
 		return {
 			refresh_counter: function() {
 				$.ajax({
 					url: site_url + 'notification/get_count_data',
 					type: 'get',
 					dataType: 'json',
 					success: function(res) {
 						$('.notification_unread_counter').html(res.length);
 						let notifications = '';
 						let badges = [
 							'badge-warning',
 							'badge-success',
 							'badge-info',
 							'badge-primary',
 							'badge-danger'
 						];
 						let badge_counter = 0;
 						if (res.length > 0) {
 							$('#notification_header_dot').attr('class', 'badge badge-dot badge-dot-sm badge-danger');
 							$('#notification_header_anim').attr('class', 'icon text-danger icon-anim-pulse fa fa-bell');
 						} else {
 							$('#notification_header_dot').attr('class', 'badge badge-dot badge-dot-sm');
 							$('#notification_header_anim').attr('class', 'icon text-danger fa fa-bell');
 						}
 						res.data.forEach(function(i) {
 							notifications += `
							 	<div class="vertical-timeline-item vertical-timeline-element">
                                    <div><span class="vertical-timeline-element-icon bounce-in"><i class="badge badge-dot badge-dot-xl ${badges[badge_counter]}"> </i></span>
                                        <div class="vertical-timeline-element-content bounce-in"><h4 class="timeline-title">${i.title}</h4>
                                        <p>${i.description}<span data-id="${i.id}" link="${i.link_on_click}" link-type="${i.link_type}" class="notif_click" style="cursor:pointer;"> <i class="fa fa-link"></i></span></p><span class="vertical-timeline-element-date"></span></div>
                                	</div>
                                </div>`;
 							if (badge_counter >= badges.length - 1) {
 								badge_counter = 0;
 							} else {
 								badge_counter += 1;
 							}
 						});
 						$('#notification_header_container').html(notifications);

 						$('.notif_click').click(function() {
 							let link = $(this).attr('link');
 							let linkType = $(this).attr('link-type');
 							let dataID = $(this).attr('data-id');
 							$.ajax({
 								url: site_url + 'notification/update',
 								type: 'post',
 								dataType: 'json',
 								data: postDataWithCsrf.data({
 									id: dataID
 								}),
 								success: function(res) {
 									if (linkType == 'Internal') {

 										RyLinx.to(link, function() {
 											$('#loading_content').hide();
 											Notification.refresh_counter();
 										});
 									} else {
 										window.location.href = link;
 									}
 								},
 								error: function(xhr, res, err) {
 									alert(err);
 								}
 							});
 						});

 					},
 					error: function(err) {
 						alert(err);
 					}
 				});
 			}
 		}
 	}();

 	var toNotifLink = function(link, linkType, dataID) {
 		$.ajax({
 			url: site_url + 'notification/update',
 			type: 'post',
 			dataType: 'json',
 			data: postDataWithCsrf.data({
 				id: dataID
 			}),
 			success: function(res) {
 				if (linkType == 'Internal') {

 					RyLinx.to(link, function() {
 						$('#loading_content').hide();
 						Notification.refresh_counter();
 					});
 				} else {
 					window.location.href = link;
 				}
 			},
 			error: function(xhr, res, err) {
 				alert(err);
 			}
 		});
 	}

 	var API = {
 		host: "<?php echo $this->config->item('api_host'); ?>",
 		key: "<?php echo $this->config->item('api_key'); ?>"
 	}
 </script>

 <script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.js'); ?>"></script>
 <!-- <script src="<?php echo base_url('assets/vendor/popper/js/popper.min.js'); ?>"></script> -->
 <script src="<?php echo base_url('assets/vendor/websocket/fancywebsocket.js'); ?>"></script>
 <!-- BlockUI -->
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.blockUI/2.70/jquery.blockUI.min.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
    

 <script src="<?php echo base_url('assets/vendor/rylinx/dynamics-load.class.js') ?>"></script>
 <script src="<?php echo base_url('assets/js/App.js?v=1.0.0'); ?>"></script>
 <script src="<?php echo base_url('assets/vendor/ryxstate/ryx-state.js'); ?>"></script>


 <script>
	 $(document).ready(function(){

		$.ajaxSetup({
			beforeSend: function (jqXHR, settings) {
				isNotException = true;
				contentBlockUIException.forEach(function (e) {
				if (isNotException == true && this.url == e) isNotException = false;
				});
				if (isNotException) {
				$.blockUI({
					message:
					'<center class="loader bg-transparent no-shadow p-0">\
										<div class="ball-pulse">\
											<div class="bg-white"></div>\
											<div class="bg-white"></div>\
											<div class="bg-white"></div>\
										</div>\
									</center>',
					css: {
					border: "none",
					backgroundColor: "transparent",
					"-webkit-border-radius": "10px",
					"-moz-border-radius": "10px",
					opacity: 100,
					color: "#fff",
					},
					baseZ: 2000,
				});
				}
				let csrf_token_name = '<?php echo $this->security->get_csrf_token_name(); ?>';
				let csrf_cookie_hash = $.cookie('<?php echo $this->config->item('cookie_prefix') . $this->config->item('csrf_cookie_name'); ?>');
				if (settings != null && settings.type != null && settings.type.toLowerCase() == 'post') {
					if (settings.data instanceof FormData) {
						settings.data.append(csrf_token_name, csrf_cookie_hash);
					} else {
						settings.data = `${csrf_token_name}=${csrf_cookie_hash}` + ((settings.data != null) ? '&' + settings.data : '');
					}
				}
			},
			complete: function (data) {
				$.unblockUI();
				globalEvents.dispatch("onAjaxCallDone", data);
			},
			success: function (data) {
				$.unblockUI();
				globalEvents.dispatch("onAjaxCallDone", data);
			},
		});
	 });
 </script>

 <script>
 	var postDataWithCsrf = function() {
 		var token = '<?php echo $this->security->get_csrf_hash(); ?>';
 		return {
 			setToken: function(tokenRegen) {
 				token = tokenRegen;
 				$('input[name="<?php echo $this->security->get_csrf_token_name(); ?>"]').val(token);
 			},
 			getToken: function() {
 				return token;
 			},
 			data: function(dataAdded) {
 				var value = {
 					'<?php echo $this->security->get_csrf_token_name(); ?>': postDataWithCsrf.getToken()
 				};
 				if (dataAdded != null && Object.keys(dataAdded).length > 0) {
 					$.each(dataAdded, function(key, val) {
 						value[key] = val;
 					});
 				}
 				return value;
 			},
 			getMap: function() {
 				return {
 					name: '<?php echo $this->security->get_csrf_token_name(); ?>',
 					value: token
 				}
 			}
 		};
 	}();
 	SocketService.onReceive('update_notif_counter', function(payload) {
 		if (payload == user.nik) {
 			Notification.refresh_counter();
 			$.notify('You have new mail, please check inbox.', {
 				globalPosition: 'top center',
 				autohide: true,
 				autoHideDelay: 5000,
 				className: 'success',
 				style: "bootstrap",
 				gap: 100
 			});
 		}
 	});
 </script>


 <!-- One Signal -->
 <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
 <script>
 	// var OneSignal = window.OneSignal || [];
 	// OneSignal.push(function() {
 	// 	OneSignal.init({
 	// 		appId: "8cfc4bab-d7ef-4c3a-8110-f653dd1252a6",
 	// 	});

 	// 	OneSignal.on('onSubscribeButtonClicked', function() {
 	// 		alert('subscribed');
 	// 		OneSignal.push(function() {
 	// 			OneSignal.getUserId(function(userId) {
 	// 				$.ajax({
 	// 					url: site_url + 'user/edit',
 	// 					type: 'post',
 	// 					dataType: 'json',
 	// 					data: postDataWithCsrf.data({
 	// 						nik: user.nik,
 	// 						notif_user_id: userId
 	// 					}),
 	// 					success: (res)  {
 	// 						if (res.success) {
 	// 							user.notif_user_id = userId;
 	// 						}
 	// 					},
 	// 					error: (xhr, res, err)  {
 	// 						console.log(xhr);
 	// 					}
 	// 				});
 	// 			});
 	// 		});
 	// 	});

 	// 	OneSignal.on('onUnsubscribeButtonClicked', function() {
 	// 		alert('unsubscribed');
 	// 		var userId = null;
 	// 		$.ajax({
 	// 			url: site_url + 'user/edit',
 	// 			type: 'post',
 	// 			dataType: 'json',
 	// 			data: postDataWithCsrf.data({
 	// 				nik: user.nik,
 	// 				notif_user_id: userId
 	// 			}),
 	// 			success: (res)  {
 	// 				if (res.success) {
 	// 					user.notif_user_id = userId;
 	// 				}
 	// 			},
 	// 			error: (xhr, res, err)  {
 	// 				console.log(xhr);
 	// 			}
 	// 		});
 	// 	});

 	// 	OneSignal.getUserId()
 	// 		.then(function(userId) {
 	// 			$.ajax({
 	// 				url: site_url + 'user/edit',
 	// 				type: 'post',
 	// 				dataType: 'json',
 	// 				data: postDataWithCsrf.data({
 	// 					nik: user.nik,
 	// 					notif_user_id: userId
 	// 				}),
 	// 				success: (res)  {
 	// 					if (res.success) {
 	// 						user.notif_user_id = userId;
 	// 					}
 	// 				},
 	// 				error: (xhr, res, err)  {
 	// 					console.log(xhr);
 	// 				}
 	// 			});
 	// 		});
 	// });
 </script>

 <script>
 	$(document).ready(function() {});
 </script>