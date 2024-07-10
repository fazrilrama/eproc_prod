<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . "/third_party/CI_QRCode/Ciqrcode.php";

class CI_qrcode extends Ciqrcode
{
    public function __contruct()
    {
        parent::__construct();
    }
}
