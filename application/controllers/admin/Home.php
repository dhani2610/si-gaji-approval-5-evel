<?php
defined('BASEPATH') or exit('No direct script access allowed');



class Home extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        if ($this->session->userdata('id_role') != "1" && $this->session->userdata('id_role') != "5" && $this->session->userdata('id_role') != "6") {
            redirect('', 'refresh');
        }
    }

    public function index()
    {
        $data = konfigurasi('Dashboard');
        $this->template->load('layouts/template', 'admin/dashboard', $data);
        // $this->template->load('layouts/new','admin/dashboard', $data);
    }
}
