<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
*@category Internal Helpers
*@author Riyan S.I (riyansaputrai007@gmail.com)
*/

if(!function_exists('dashboard_view'))
{
    function dashboard_view($page=null,$data=null)
	{
		$CI = get_instance();
		if($page!=null)
		{
			$data['_contents']=$CI->load->view($page,$data,true);
		}
		else
		{
			$data['_contents']=null;
		}
		return $CI->load->view('templates/dashboard/body',$data);
    }
}

if(!function_exists('template_view'))
{
    function template_view($template,$page,$data=null)
	{
		$CI = get_instance();
		$data['_contents']=$CI->load->view($page,$data,true);
		return $CI->load->view($template,$data);
	}
}
