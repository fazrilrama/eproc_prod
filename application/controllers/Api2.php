<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api2 extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();

		$apikey = '$2y$10$4VKJNBm6wYFBGtYv8T2iGuvvS0bZgt9fCOd4goMuPaHg46ryUpx6u';
		if ($this->input->get_request_header("x-api-key", "") != $apikey) {
			echo json_encode(["result" => "Not Opperation"]);
		}
	}

	public function daily_logins($days = 14)
	{
		header('Content-Type: application/json');
		if (explode(" ", $this->input->get_request_header("authorization", ""))[1] !== '$2t$10$bd35HUBu31HXaZDfkb2rmeb6je8JbfehpuIVGJdIcVJjT0n0S0Shy') {
			echo json_encode(["result" => "Not Opperation"]);
			exit;
		}

		$data = $this->db->select('DATE(created_at) AS date, COUNT(*) as count', false)
			->where("DATE(created_at) BETWEEN CURDATE() - INTERVAL $days DAY AND CURDATE()")
			->group_by('DATE(created_at)', false)
			->get('sys_login_session')
			->result();

		$startDate = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
		$startDate->modify("-$days day");

		$endDate = new DateTime("now", new DateTimeZone('Asia/Jakarta'));
		$endDate->modify("+1 day");

		$interval = new DateInterval("P1D");

		$dateRange = new DatePeriod($startDate, $interval, $endDate);

		$dateCount = [];

		foreach ($dateRange as $date) {
			$dateCount[$date->format("Y-m-d")] = 0;
		}

		foreach ($data as $datum) {
			$dateCount[$datum->date] = (int) $datum->count;
		}

		echo json_encode($dateCount, TRUE);
	}
}