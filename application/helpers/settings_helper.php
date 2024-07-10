<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 *@category Internal Helpers
 *@author Riyan S.I (riyansaputrai007@gmail.com)
 */
function ssp_default_db(){
    $CI=&get_instance();
    return array(
            'user' => $CI->db->username,
            'pass' => $CI->db->password,
            'db'   => $CI->db->database,
            'host' => $CI->db->hostname,
	    'port' => $CI->db->port
        );
}

if (!function_exists('get_settings')) {
    function get_settings($id = null)
    {
        $context = &get_instance();
        if ($id != null) $context->db->where('id', $id);

        return $context->db->where('deleted_at is null')
            ->order_by('created_at desc')
            ->limit(1)
            ->get('sys_settings')->row();
    }
}
if (!function_exists('check_required_form_validation')) {
    function check_required_form_validation($id_user, $is_must_verified = false, $verification_condition = "='Verified'")
    {
        $context = &get_instance();
        $result = (object) ['detail' => [], 'form_completed' => 0, 'form_incomplete' => 0, 'form_required_total' => 0, 'percentage' => 0];
        $tbl_usr_role = 'sys_usr_role';
        $tbl_user = 'sys_user';
        $tbl_required_form = 'tbl_required_form';
        $tbl_company_profile = 'company_profile';

        $user_role = $context->db->where('id_user', $id_user)
            ->join($tbl_usr_role . ' b', 'a.id_usr_role=b.id_usr_role')
            ->get($tbl_user . ' a')->row();

        $required_form = $context->db
            ->where('id_usr_role', $user_role->id_usr_role)
            ->where('deleted_at is null')
            ->get($tbl_required_form)
            ->result();

        $result->form_required_total = count($required_form);

        $id_company = null;
        $result->form_completed = 0;
        $result->form_incomplete = $result->form_required_total;

        foreach ($required_form as $f) {
            $result->detail[$f->tbl_name] = ['is_valid' => false, 'form_detail' => $f];
            if ($f->tbl_name == $tbl_company_profile) {

                if ($is_must_verified) {
                    $company_data = $context->db
                        ->where('id_user', $id_user)
                        ->where('deleted_at is null')
                        ->get($f->tbl_name)->list_fields();
                    $verification_field_exist = false;
                    foreach ($company_data as $field) {
                        if ($field == 'verification_status') {
                            $verification_field_exist = true;
                            break;
                        }
                    }

                    if ($verification_field_exist) $context->db->where('verification_status ' . $verification_condition);
                }
                $company_profile = $context->db
                    ->where('id_user', $id_user)
                    ->where('deleted_at is null')
                    ->get($f->tbl_name);
                if ($company_profile->num_rows() >= $f->minimum) {
                    $result->detail[$f->tbl_name] = ['is_valid' => true, 'form_detail' => $f];
                    $company_profile = $company_profile->row();
                    $id_company = $company_profile->id;

                    $result->form_completed += 1;
                    $result->form_incomplete -= 1;
                }
            } else {
                $company_profile = $context->db
                    ->where('id_user', $id_user)
                    ->where('deleted_at is null')
                    ->get($tbl_company_profile)
                    ->row();

                $id_company = $company_profile != null ? $company_profile->id : null;

                if ($is_must_verified) {
                    $company_data = $context->db
                        ->where('id_company', $id_company)
                        ->where('deleted_at is null')
                        ->get($f->tbl_name)->list_fields();
                    $verification_field_exist = false;
                    foreach ($company_data as $field) {
                        if ($field == 'verification_status') {
                            $verification_field_exist = true;
                            break;
                        }
                    }

                    if ($verification_field_exist) $context->db->where('verification_status ' . $verification_condition);
                }
                $company_data = $context->db
                    ->where('id_company', $id_company)
                    ->where('deleted_at is null')
                    ->get($f->tbl_name);

                if ($company_data->num_rows() >= $f->minimum) {
                    $result->detail[$f->tbl_name] = ['is_valid' => true, 'form_detail' => $f];

                    $result->form_completed += 1;
                    $result->form_incomplete -= 1;
                }
            }
        }

        $result->percentage = ($result->form_completed <= 0) ? 0 : ($result->form_completed / $result->form_required_total) * 100;

        return $result;
    }
}

define('REGREX_COLLETION',['STRENGTH_PASS'=>'^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$']);