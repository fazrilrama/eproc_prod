/* 
Dynamics Load no need to refresh, template model for CI
Author : Riyan S.I
Github : github.om/riyanirawan007
*/
// set if u have spesific link for any route
var _routes = [];
var _error_url = site_url + 'dashboard/not_found';
var _rylinxContainer = "#rylinx_content";

var RyLinx = function () {
	return {
		loader: function (link, data, callback = null) {
			// your container id
			$(_rylinxContainer).html(null);
			try {
				if (callback == null) {
					$(_rylinxContainer).load(link, data, function (data, status) {
						$(_rylinxContainer).load(_error_url);
					});
				} else {
					$(_rylinxContainer).load(link, data, function (data, status) {
						if (status == "error") {
							$(_rylinxContainer).load(_error_url);
							callback();
						} else {
							callback();
						}
					});
				}
			} catch (err) {
				$(_rylinxContainer).load(_error_url);
			}

		},
		autoload_with_routes(routes = [], callback = null) {
			if (routes == null) routes = _routes;
			console.log('Not yet implemented!');
			callback();
			// RyLinx.loader(site_url+_config.controller,{rylinx_view:routes[0].view},callback);
		},
		autoload(callback = null) {
			var url_full = window.location.href;
			var url_path = window.location.pathname;
			var url_rylinx = url_full.replace(url_path + '#', '').replace(window.location.origin, '');

			var route_found = false;
			for (var i = 0; i < _routes.length; i++) {
				// console.log(url_rylinx);
				var e = _routes[i];
				if (url_rylinx.includes(e.link)) {
					route_found = true;
					RyLinx.loader(site_url + url_rylinx, null, callback);
					break;
				}
			}

			if (!route_found) {
				if (url_rylinx == url_path || url_rylinx == '') url_rylinx = '/dashboard';
				RyLinx.loader(site_url + url_rylinx, null, callback);
			}


		},
		to(link, callback = null) {
			// console.log(link);
			var url_rylinx = link.replace('#', '');
			if (url_rylinx != '' && url_rylinx != 'javascript:void(0)') {
				RyLinx.loader(site_url + url_rylinx, null, callback);
			} else {
				callback();
			}
		},
		to_with_data(link, data, callback = null) {
			// console.log(link);
			var url_rylinx = link.replace('#', '');
			if (url_rylinx != '' && url_rylinx != 'javascript:void(0)') {
				RyLinx.loader(site_url + url_rylinx, data, callback);
			} else {
				if (callback != null) {
					callback();
				} else {
					$('#loading_content').hide();
				}
			}
		},
		setContainer: function (_container) {
			_rylinxContainer = _container;
		}
	}
}();
