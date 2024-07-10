<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Maintance extends CI_Controller
{

    public function index()
    {
        $this->load->view('pages/maintance/maintance');
    }
}
