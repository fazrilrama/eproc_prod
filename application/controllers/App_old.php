<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_old extends App_Controller
{

	public function index()
	{
		$check_complete_profile = check_required_form_validation($this->session->userdata('user')['id_user']);
		$user_role = $this->session->userdata('user')['id_usr_role'];
		$user_status = $this->session->userdata('user')['id_usr_status'];
		if (
			($check_complete_profile->percentage == 100 || ($user_role != App_Model::ROLE_VENDOR
				&& $user_role != App_Model::ROLE_VENDOR_PERSONAL
				&& $user_role != App_Model::ROLE_VENDOR_GROUP))
			&& $user_status == App_Model::STAT_ACCOUNT_ACTIVE
		) {
			return dashboard_view('');
		} else if ($user_status == App_Model::STAT_ACCOUNT_WAITING_VALIDATING_PROFILE) {
			$data['include_topbar'] = true;
			$data['include_sidebar'] = false;
			return template_view('templates/dashboard/body_custom', 'pages/required_info/waiting_validation', $data);
		} else {
			$data['include_topbar'] = true;
			$data['include_sidebar'] = false;
			return template_view('templates/dashboard/body_custom', 'pages/required_info/registration', $data);
			// return template_view('templates/dashboard/body_custom', 'pages/required_info/registration_company', $data);
		}
	}
}
