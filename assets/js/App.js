var countRefreshMenu = 0;
var contentBlockUIException = [site_url + "notification/get_notif_counter"];
var isNotException = true;
var globalEvents = {
	dispatch: function (id, data) {
		if (globalEvents.onDone != null) globalEvents.onDone(id, data);
	},
	onDone: null,
};

function formatRupiah(angka, prefix) {
	var number_string = angka
			.toString()
			.replace(/[^,\d]/g, "")
			.toString(),
		split = number_string.split(","),
		sisa = split[0].length % 3,
		rupiah = split[0].substr(0, sisa),
		ribuan = split[0].substr(sisa).match(/\d{3}/gi);

	// tambahkan titik jika yang di input sudah menjadi angka ribuan
	if (ribuan) {
		separator = sisa ? "." : "";
		rupiah += separator + ribuan.join(".");
	}

	rupiah = split[1] != undefined ? rupiah + "," + split[1] : rupiah;
	return prefix == undefined ? rupiah : rupiah ? "Rp. " + rupiah : "";
}

function stripTags(val) {
	return val.replace(/<\/?[^>]+(>|$)/g, "");
}

function kFormatter(num) {
	return Math.abs(num) > 999
		? Math.sign(num) * (Math.abs(num) / 1000).toFixed(1) + "k"
		: Math.sign(num) * Math.abs(num);
}

var Menu = (function () {
	var listMenu = "";
	var child_active = null;
	var parent_active = null;
	return {
		get: function (menuDash) {
			getCurrentRole(function (res) {
				var user = res;
				$.ajax({
					url: site_url + "menu/get",
					type: "GET",
					dataType: "json",
					data: {
						id_role: user != null ? user.role_id : -1,
					},
					async: false,
					success: function (data, text) {
						Notification.refresh_counter();
						countRefreshMenu += 1;
						$("#menu_container").html("");
						listMenu = "";
						if (data.length > 0) Menu.render(data, 0);
						menuDash.html(listMenu);

						$(".rylinx-nav")
							.unbind("click")
							.click(function () {
								$("#loading_content").show();
								RyLinx.to($(this).attr("href"), function () {
									$("#loading_content").hide();
								});
							});

						if (countRefreshMenu > 1) {
							$(".menu_expand").click(function () {
								if ($(this).attr("aria-expanded") == "false") {
									$(this).attr("aria-expanded", "true");
									$(this).parent("li").attr("class", "mm-active");
									$(this)
										.parent("li")
										.children("ul")
										.attr("class", "mm-collapse mm-show");
								} else {
									$(this).attr("aria-expanded", "false");
									$(this).parent("li").attr("class", "");
									$(this)
										.parent("li")
										.children("ul")
										.attr("class", "mm-collapse");
								}
							});
						}
					},
					error: function (stat, res, err) {
						alert(err);
					},
				});
			});
		},
		render: function (menus, parentID) {
			for (var i = 0; i < menus.length; i++) {
				if (menus[i].parent == parentID) {
					if (menus[i].is_head_section == 1) {
						listMenu +=
							'<li class="app-sidebar__heading">' + menus[i].label + "</li>";
						if (menus[i].count_child > 0) {
							Menu.render(menus, menus[i].id_menu);
						}
					} else {
						// var child_open = "";
						// var curr_url = window.location.pathname;
						// var curr_menu = (site_url + '/' + menus[i].link).replace(domain_url, '');
						// if (curr_menu == curr_url) {
						//     child_open = "active";
						//     child_active = menus[i].id_menu;
						// }

						if (menus[i].count_child > 0) {
							if (menus[i].parent_head_section == 1 || menus[i].parent == 0) {
								listMenu +=
									'<li>\
                                <a class="menu_expand" href="' +
									menus[i].link +
									'" aria-expanded="false">\
									<i class="metismenu-icon ' +
									menus[i].icon +
									'"></i>\
									' +
									menus[i].label +
									'\
									<i class="metismenu-state-icon pe-7s-angle-down caret-left"></i>\
                                </a>\
                                <ul class="mm-collapse">';
							} else {
							}

							// listMenu +='<li>\
							// <a class="rylinx-nav" href="'+menus[i].link+'">\
							//     <i class="metismenu-icon"></i>\
							//     '+menus[i].label+'\
							// </a>\
							// </li>';

							Menu.render(menus, menus[i].id_menu);
							listMenu += "</ul>";

							if (menus[i].parent_head_section == 1 || menus[i].parent == 0) {
								listMenu += "</li>";
							}
						} else {
							listMenu +=
								'<li>\
                            <a  class="rylinx-nav" href="' +
								menus[i].link +
								'" class="mm-active">\
                                <i class="metismenu-icon ' +
								menus[i].icon +
								'"></i>\
                                ' +
								menus[i].label +
								"\
                            </a>\
                            </li>";
						}
					}
				}
			}
			return listMenu;
		},
	};
})();

var getCurrentRole = function (callback) {
	$.ajax({
		url: site_url + "auth/current_logged_user",
		type: "GET",
		dataType: "json",
		async: false,
		success: function (res) {
			callback(res);
		},
	});
};

var largeModal = function (
	params = {
		title: null,
		body: null,
		footer: null,
	}
) {
	var _btn = $("#modal-btn-lg");
	var _container = $("#modal-large");
	var modalTitle = $("#modal-title");
	var modalBody = $("#modal-body");
	var modalFooter = $("#modal-footer");
	modalTitle.html(params.title);
	modalBody.html(params.body);
	modalFooter.html(params.footer);

	return {
		close: function () {
			_btn.click();
		},
		show: function (onShow = null) {
			_btn.click();
			if (onShow != null) onShow(_container);
		},
	};
};
var basicModal = function (
	params = {
		title: null,
		body: null,
		footer: null,
	}
) {
	var _btn = $("#modal-btn-bsc");
	var _container = $("#modal-basic");
	var modalTitle = $("#modal-title-basic");
	var modalBody = $("#modal-body-basic");
	var modalFooter = $("#modal-footer-basic");
	modalTitle.html(params.title);
	modalBody.html(params.body);
	modalFooter.html(params.footer);

	return {
		close: function () {
			_btn.click();
		},
		show: function (onShow = null) {
			_btn.click();
			if (onShow != null) onShow(_container);
		},
	};
};

var loadingContent = function (
	container,
	content,
	style = "width:20px;",
	needText = false,
	textLoading = "Loading, please wait..."
) {
	var textLoad = needText ? "<br><span>" + textLoading + "</span>" : "";
	var loading_img =
		'<center class="loaderContentIndicator">\
	<img style="' +
		style +
		'" src="' +
		site_url +
		"assets/img/system/load.gif" +
		'">\
	' +
		textLoad +
		"</center>";
	return {
		load: function () {
			content.hide();
			content.css("display", "none");
			// select2
			content.next(".select2-container").hide();
			container.append(loading_img);
		},
		unload: function () {
			$(".loaderContentIndicator").remove();
			content.css("display", "");
			content.show();
			// select2
			content.next(".select2-container").show();
		},
	};
};

var getRomanNumber = function (latinNum = 0) {
	var romanNumber = [
		"I",
		"II",
		"III",
		"III",
		"IV",
		"V",
		"VI",
		"VII",
		"VIII",
		"IX",
		"X",
		"XI",
		"XII",
	];
	var result = null;
	if (latinNum + 1 <= romanNumber.length) {
		result = romanNumber[latinNum];
	}

	return result;
};

var getCurrentDate = function () {
	var date = new Date();
	var currYear = date.getFullYear();
	var currMonth = date.getMonth() + 1;
	var currDate = date.getDate();

	currDate = currDate < 10 ? "0" + currDate : currDate;
	currMonth = currMonth < 10 ? "0" + currMonth : currMonth;

	return currYear + "-" + currMonth + "-" + currDate;
};

// Currently disabled
var SocketService = (function () {
	// var _Server = new FancyWebSocket('ws://10.66.0.47:9300');
	// _Server.connect();
	return {
		disconnect: function () {
			// _Server.disconnect();
		},
		onReceive: function (eventName, callback) {
			// _Server.bind(eventName, (payload) {
			// 	callback(payload);
			// });
		},
		send: function (eventName, payload) {
			// _Server.send(eventName, payload);
		},
	};
})();

var AttachmentManage = (function () {
	return {
		delete: function (data, folder, file_name, callback = null) {
			var _tempData = [];
			var _isSuccess = false;
			var _msg = "";

			$.ajax({
				url: site_url + "file_manage/delete/mail",
				type: "POST",
				dataType: "json",
				data: postDataWithCsrf.data({
					type: folder,
					filename: file_name,
				}),
				async: false,
				success: function (res) {
					_isSuccess = res.success;
					if (_isSuccess) {
						data.forEach(function (e) {
							if (e.file_name != file_name) {
								_tempData.push(e);
							}
						});
					}
					_msg = res.message;
				},
				error: function (res, stat, err) {
					_isSuccess = false;
					_msg = err;
				},
			});
			if (callback != null) callback(_isSuccess, _msg, _tempData);
			_tempData = [];
		},
		download: function (folder, filename) {
			window.open(base_url + "/upload/mail/" + folder + "/" + filename);
		},
	};
})();

var expandableText = function (
	text,
	max,
	title = "Keterangan",
	defaultText = "-",
	caption = "...Selengkapnya"
) {
	var vText = defaultText;
	if (text != null) {
		if (text.length > 0 && text.length > max) {
			var jsText = text
				.replace(/\\/g, "\\\\")
				.replace(/"/g, '\\"')
				.replace(/[\n\r]/g, "\\n");
			vText =
				text.replace(/<[^>]*>?/gm, "").substring(0, max) +
				'<a style="color:#3385ff;" onclick="popExpandText(\'' +
				jsText +
				"','" +
				title +
				"')\">" +
				caption +
				"</a>";
		} else if (text.length > 0) {
			vText = text.replace(/<[^>]*>?/gm, "");
		}
	}
	return vText;
};

var popExpandText = function (text, title) {
	swal({
		title: title,
		content: {
			element: "div",
			attributes: {
				style:
					"max-height:150px;overflow-y:scroll;text-align:justify;margin:10px;font-size:0.90em;",
				innerHTML: text,
			},
		},
	});
};

var getMasterData = (function () {
	let _ajax = function (
		param = {
			url,
			isAsync: false,
			type: "GET",
			data: {},
			onFinished: null,
		}
	) {
		$.ajax({
			url: site_url + "master/" + param.url,
			type: param.type,
			dataType: "json",
			async: param.isAsync,
			data: param.data,
			success: function (res) {
				if (param.onFinished != null) param.onFinished(true, res);
			},
			error: function (res, stat, err) {
				if (param.onFinished != null) param.onFinished(false, err);
			},
		});
	};

	return {
		getUser: function (onFinished) {
			_ajax({
				url: "/get_user_public",
				isAsync: false,
				type: "GET",
				onFinished: onFinished,
			});
		},
		getCompanyType: function (onFinished) {
			_ajax({
				url: "/get_company_type_public",
				isAsync: false,
				type: "GET",
				onFinished: onFinished,
			});
		},
		getProvince: function (data = {}, onFinished) {
			_ajax({
				url: "/get_data_province",
				isAsync: false,
				type: "GET",
				data: data,
				onFinished: onFinished,
			});
		},
		getCity: function (data = {}, onFinished) {
			_ajax({
				url: "/get_data_city",
				isAsync: false,
				type: "GET",
				data: data,
				onFinished: onFinished,
			});
		},
		getCompanyCompetency: function (data = {}, onFinished) {
			_ajax({
				url: "/get_company_competency",
				isAsync: false,
				type: "GET",
				data: data,
				onFinished: onFinished,
			});
		},
		getCompanySubCompetency: function (data = {}, onFinished) {
			_ajax({
				url: "/get_company_sub_competency",
				isAsync: false,
				type: "GET",
				data: data,
				onFinished: onFinished,
			});
		},
	};
})();

function lookVerifyHitory(data) {
	data = data.split(",");

	let id = data[0];
	let table = data[1].replace(" ", "");

	largeModal({
		title: "Verification History",
		body: `<table width="100%" id="table-verification-history" class="table table-striped table-bordered">
            <thead>
                <th>Verifikator/Pemilik</th>
                <th>Role</th>
                <th>Status</th>
                <th>Note</th>
                <th>Tanggal</th>
            </<thead>
        </table>`,
		footer: `<div>
        <button id="close-dialog" class="btn btn-danger">Tutup</button>
        </div>`,
	}).show(function () {
		$("#table-verification-history").DataTable({
			initComplete: function () {},
			processing: true,
			retrieve: true,
			ajax: {
				type: "GET",
				url: site_url + "verification/get_verification_history",
				data: function (d) {
					d.id = id;
					d.table = table;
				},
				dataSrc: "",
			},
			order: [[4, "desc"]],
			columns: [
				{
					data: "user_name",
				},
				{
					data: "role_name",
				},
				{
					data: "verification_status",
				},
				{
					data: "verification_note",
				},
				{
					data: "created_at",
				},
			],
		});
		$("#close-dialog").click(function () {
			largeModal().close();
		});
	});
}

function viewDetailVendor(id_company, id_user, onClose = null) {
	let ryLinx = RyLinxClass("#rylinx_content1");
	largeModal({
		title: "Detail Vendor",
		body: function () {
			let view = `<div id="rylinx_content1"></div>`;
			return view;
		},
		footer: function () {
			return `<button class="btn btn-sm btn-info" id="close-view-vendor">Tutup</button>`;
		},
	}).show(function () {
		ryLinx.to(
			`#verification/view_data_perusahaan?id_user=${id_user}&viewOnly=true`,
			function () {
				$("#rylinx_content1 input").attr("disabled", 1);
				$("#rylinx_content1 select").attr("disabled", 1);
				$("#rylinx_content1 textarea").attr("disabled", 1);
				$('#rylinx_content1 input[type="file"]').remove();
				$('#rylinx_content1 span[style="color:red"]').remove();
				$("#rylinx_content1 #persetujuan_registrasi").remove();
			}
		);

		$("#close-view-vendor").click(function () {
			largeModal().close();
			if (onClose != null) onClose();
		});
	});
}
