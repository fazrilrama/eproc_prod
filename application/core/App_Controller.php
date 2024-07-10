<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_Controller extends CI_Controller
{

	const PATH_UPLOAD_COMPANY_LOGO = FCPATH . '/upload/company/logo/';
	const PATH_UPLOAD_COMPANY_FILE = FCPATH . '/upload/company/file/';
	const PATH_UPLOAD_SHOPPING_FILE = FCPATH . '/upload/shopping/file/';
	const PATH_UPLOAD_PROCUREMENT = FCPATH . '/upload/procurement/file/';

	var $log_data = [
		'type' => 'raw',
		'data' => null
	];


	const ADMIN_ROLES = [1, 5];

	function __construct($params = ['exclude_login' => null, 'exclude_menu_check' => null, 'exclude_dashboard_redirect' => null])
	{
		parent::__construct();

		// Security Checks
		// if (isset($params['exclude_login'])) $this->excludes = $params['exclude_login'];

		if (!isset($params['exclude_login'])) $params['exclude_login'] = [];
		if (!isset($params['exclude_menu_check'])) $params['exclude_menu_check'] = [];
		if (!isset($params['exclude_dashboard_redirect'])) $params['exclude_dashboard_redirect'] = [];
		$this->_security_validation($params);
		// Security Checks

		$this->_set_settings();
		$this->_log_activity();

		$app_info = $this->config->item('app_info');
		$global_data = [
			'app_identity' => $app_info['identity'],
			'app_version' => $app_info['version']
		];
		$this->load->vars($global_data);
	}

	public function set_page_title($icon = null, $label = null, $breadcumb = [])
	{
		$global_data = [
			'page_title_icon' => (($icon == null) ? 'pe-7s-home' : $icon),
			'page_title_label' => (($label == null) ? 'Dashboard' : $label),
			'page_title_breadcumb' => $breadcumb
		];

		$this->load->vars($global_data);
	}


	public function setLogData($log_data = null)
	{
		if ($log_data != null) {
			if (isset($log_data['type'])) $this->log_data['type'] = $log_data['type'];
			if (isset($log_data['type'])) $this->log_data['type'] = $log_data['data'];
		}
	}

	public function get_csrf_token()
	{
		return [
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->$this->security->get_csrf_hash()
		];
	}

	public function response_json_with_csrf($params = [])
	{
		echo json_encode($this->get_csrf_token());
	}

	static public function get_client_data($context)
	{
		return [
			'ip' => $context->input->ip_address(),
			'browser' => $context->agent->browser(),
			'browser_version' => $context->agent->version(),
			'platform' => $context->agent->platform(),
			'user_agent' => $_SERVER['HTTP_USER_AGENT']
		];
	}

	static public function secure_input($input, $settings = [])
	{
		$input = xss_clean($input, (isset($settings['is_image']) ? $settings['is_image'] : false));
		if (isset($settings['sanitize_filename']) && $settings['sanitize_filename']['enabled']) {
			if (isset($settings['sanitize_filename']) && $settings['sanitize_filename']['allow_path']) {
				$input = sanitize_filename($input, true);
			} else {
				$input = sanitize_filename($input);
			}
		}

		return $input;
	}

	public function upload_company_attachment($isRequiredUpload = false, $field_name = 'attachment', $upload_config = null)
	{
		$file_data = null;
		$error_msg = null;
		$is_success = false;

		if (empty($_FILES[$field_name]['name']) && $isRequiredUpload == true) {
			$is_success = false;
			$error_msg = "You have to upload at least 1 file!";
			$file_data = ['file_name' => null];
		} else if (empty($_FILES[$field_name]['name']) && $isRequiredUpload == false) {
			$is_success = true;
			$error_msg = null;
			$file_data = ['file_name' => null];
		} else {
			if ($upload_config == null) {
				$config['upload_path']          = self::PATH_UPLOAD_COMPANY_FILE;
				$config['allowed_types']        = 'pdf|png|jpg|jpeg|rar|zip';
				$config['max_size']             = 51200;
				$config['remove_spaces']        = true;
				$config['encrypt_name']         = true;
			} else {
				$config = $upload_config;
			}
			$this->load->library('upload', $config);
			if (!$this->upload->do_upload($field_name)) {
				$is_success = false;
				$error_msg = $this->upload->display_errors();
			} else {
				$is_success = true;
				$file_data = $this->upload->data();
			}
		}

		return ['success' => $is_success, 'file_data' => $file_data, 'error' => $error_msg];
	}

	private function _security_validation($params)
	{
		$is_already_logged_in = false;
		if ($this->router->fetch_class() == 'auth' && $this->router->fetch_method() != 'logout') {
			if ($this->_login_check(false)) {
				$is_already_logged_in = true;
			}
		}


		if (!$is_already_logged_in) {
			$need_login_check = true;
			for ($i = 0; $i < count($params['exclude_login']); $i++) {
				if ($this->router->fetch_method() == $params['exclude_login'][$i]) {
					$need_login_check = false;
					break;
				}
			}
			if ($need_login_check) {
				if ($this->_login_check()) {
					$controller = array(
						'requested' => array('class' => $this->router->fetch_class(), 'method' => $this->router->fetch_method()), 'avoid' => isset($params['exclude_menu_check']) ? $params['exclude_menu_check'] : []
					);
					$this->_authorize_controller($controller);
				}
			}
		} else {
			if ($this->router->fetch_method() != 'current_logged_user') {
				redirect(site_url('app'));
			}
		}
	}

	private function _login_check($need_alert = true)
	{
		if ($this->session->userdata('is_logged_in') == null || ($this->session->userdata('is_logged_in') != null && !$this->session->userdata('is_logged_in'))) {
			if ($need_alert) {
				echo '<script>
                window.location.href="' . site_url('auth/login') . '"
                </script>';
			}
			return false;
		} else {
			return true;
		}
	}


	private function _authorize_controller($controller = array('requested' => array('class' => null, 'method' => null), 'avoid' => array()))
	{
		$CI = get_instance();
		$CI->load->model('Menu_model', 'menu');
		$id_role = $CI->session->userdata('user')['id_usr_role'];
		$menus = $CI->menu->get_menu_privilege($id_role);
		$authorized = false;
		$is_core = false;
		$requested_uri = $controller['requested']['class'] . '/' . $controller['requested']['method'];


		//avoid checking for dev only
		$core_controllers = array(
			array('class' => 'app'),
			array('class' => 'app_old'),
			// array('class' => 'dashboard'),
			// array('class' => 'menu', 'method' => 'get'),
			// array('class' => 'user', 'method' => 'profile'),
			// array('id_role' => 1, 'class' => 'menu'),
			// array('id_role' => 1, 'class' => 'user_role'),
		);

		for ($i = 0; $i < count($core_controllers); $i++) {
			if (isset($core_controllers[$i]['id_role'])) {
				if (
					$core_controllers[$i]['id_role'] == $id_role
					&& $core_controllers[$i]['class'] == $controller['requested']['class']
				) {
					$authorized = true;
					break;
				}
			} else if (isset($core_controllers[$i]['method'])) {
				if ($core_controllers[$i]['method'] == $controller['requested']['method']) {
					$authorized = true;
					break;
				}
			} else {
				if ($core_controllers[$i]['class'] == $controller['requested']['class']) {
					$authorized = true;
					break;
				}
			}
		}

		if (!$is_core) {
			foreach ($menus as $menu) {

				if ($menu['link'] != '' && $menu['link'] != null) {
					$menu_link = $menu['link'];

					if (strpos($menu_link, '#') !== false) {
						$menu_link = str_replace('#', '', $menu_link);
					}

					if ($menu_link != "") {

						// if (strpos($requested_uri, $menu_link) !== false) {
						// 	$authorized = true;
						// 	break;
						// }


						if (strpos($requested_uri, '/') !== FALSE && strpos($menu_link, '/') === FALSE) {

							$paths = explode('/', $requested_uri);
							for ($m = 0; $m < count($paths); $m++) {
								if ($paths[$m] == $menu_link) {
									$authorized = true;
									break;
								}
							}
							if ($authorized) break;
						} else {
							if ($requested_uri == $menu_link) {
								$authorized = true;
								break;
							}
						}
					}

					if (isset($controller['avoid'])) {

						// $authorized = false;

						for ($i = 0; $i < count($controller['avoid']); $i++) {

							$authorized = false;

							$avoid_schemas = $controller['avoid'][$i];

							$match_role = true;
							$match_class = true;
							$match_method = true;

							if (isset($avoid_schemas['class'])) {
								$match_class = false;
								if ($avoid_schemas['class'] == $controller['requested']['class']) {
									$match_class = true;
								}
							}

							if (isset($avoid_schemas['method'])) {
								$match_method = false;
								if ($avoid_schemas['method'] == $controller['requested']['method']) {
									$match_method = true;
								}
							}
							if (isset($avoid_schemas['id_role'])) {
								$match_role = false;
								foreach ($avoid_schemas['id_role'] as $v) {
									if ($v == $id_role) {
										$match_role = true;
										break;
									}
								}
							}

							$authorized = ($match_class && $match_method && $match_role);
							if ($authorized) break;
						}
					}
				}
			}
		}

		if (!$authorized) {
			echo '<script>
			document.location.href="' . base_url('#dashboard/unauthorized') . '";
			</script>';
			exit;
		}
	}


	private function _set_settings()
	{
		date_default_timezone_set(get_settings()->timezone);
	}

	private function _log_activity()
	{
		$client = $this->get_client_data($this);

		if ($this->session->userdata('is_logged_in') != null && get_settings()->activity_log_enabled) {
			$data = [
				'id_user' => $this->session->userdata('id_user') != null ? $this->session->userdata('id_user') : 'anonim',
				'controller' => $this->router->fetch_class(),
				'method' => $this->router->fetch_method(),
				'request_method' => $this->input->server('REQUEST_METHOD'),
				'url' => current_url(),
				'ip' => get_client_ip_env(),
				'data' => $this->log_data['data'],
				'data_type' => $this->log_data['type'],
				'ip' => $client['ip'],
				'browser' => $client['browser'],
				'browser_version' => $client['browser_version'],
				'platform' => $client['platform'],
				'user_agent' => $client['user_agent']
			];

			$this->db->insert('sys_activity_log', $data);
		}
	}

	public function is_as_admin()
	{
		$is_as_admin = false;
		foreach (self::ADMIN_ROLES as $role) {
			if ($this->session->userdata('user')['id_usr_role'] == $role) {
				$is_as_admin = true;
				break;
			}
		}
		return $is_as_admin;
	}
}
