var RyLinxClass = function (container = "", error_url = "", routes = []) {
	let _routes = routes;
	let _error_url = error_url;
	let _rylinxContainer = container;

	let loader = function (link, data, callback = null) {
		// your container id
		$(_rylinxContainer).html(null);
		try {
			if (callback == null) {
				$(_rylinxContainer).load(link, data, function (data, status) {
					$(_rylinxContainer).load(error_url);
				});
			} else {
				$(_rylinxContainer).load(link, data, function (data, status) {
					if (status == "error") {
						$(_rylinxContainer).load(error_url);
						callback();
					} else {
						callback();
					}
				});
			}
		} catch (err) {
			$(_rylinxContainer).load(error_url);
		}

	}


	let autoload_with_routes = function (routes = [], callback = null) {
		if (routes == null) routes = routes;
		console.log('Not yet implemented!');
		callback();
		// loader(site_url+_config.controller,{rylinx_view:routes[0].view},callback);
	}
	let autoload = function (callback = null) {
		var url_full = window.location.href;
		var url_path = window.location.pathname;
		var url_rylinx = url_full.replace(url_path + '#', '').replace(window.location.origin, '');

		var route_found = false;
		for (var i = 0; i < routes.length; i++) {
			// console.log(url_rylinx);
			var e = routes[i];
			if (url_rylinx.includes(e.link)) {
				route_found = true;
				loader(site_url + url_rylinx, null, callback);
				break;
			}
		}

		if (!route_found) {
			if (url_rylinx == url_path || url_rylinx == '') url_rylinx = '/dashboard';
			loader(site_url + url_rylinx, null, callback);
		}


	}
	let to = function (link, callback = null) {
		// console.log(link);
		var url_rylinx = link.replace('#', '');
		if (url_rylinx != '' && url_rylinx != 'javascript:void(0)') {
			loader(site_url + url_rylinx, null, callback);
		} else {
			callback();
		}
	}
	let to_with_data = function (link, data, callback = null) {
		// console.log(link);
		var url_rylinx = link.replace('#', '');
		if (url_rylinx != '' && url_rylinx != 'javascript:void(0)') {
			loader(site_url + url_rylinx, data, callback);
		} else {
			if (callback != null) {
				callback();
			} else {
				$('#loading_content').hide();
			}
		}
	}

	return {
		loader: loader,
		to: to,
		autoload: autoload,
		autoload_with_routes: autoload_with_routes,
		to_with_data: to_with_data
	}
}
