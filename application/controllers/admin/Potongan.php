<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Potongan extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        if ($this->session->userdata('id_role') != "1" && $this->session->userdata('id_role') != "5") {
            redirect('', 'refresh');
        }
    }

    public function index()
    {
        $data = konfigurasi('Potongan');
        $data['potongans'] = $this->db->get('potongan')->result();
        $this->template->load('layouts/template', 'admin/potongan/index', $data);
        // $this->template->load('layouts/new','admin/dashboard', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('kode', 'Kode', 'required');
        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('potongan', 'Potongan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Gagal menambahkan potongan');
            redirect('admin/potongan', 'refresh');
        } else {
            $data = [
                'kode' => $this->input->post('kode'),
                'name' => $this->input->post('name'),
                'potongan' => $this->input->post('potongan'),
            ];
            $this->db->insert('potongan', $data);
            $this->session->set_flashdata('success', 'Berhasil menambahkan potongan');
            redirect('admin/potongan', 'refresh');

        }
    }

    public function update()
    {
        $this->form_validation->set_rules('name', 'Nama', 'required');
        $this->form_validation->set_rules('potongan', 'Potongan', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Gagal mengubah potongan');
            redirect('admin/potongan', 'refresh');
        } else {
            $data = [
                'potongan' => $this->input->post('potongan'),
                'name' => $this->input->post('name'),
            ];
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('potongan', $data);
            $this->session->set_flashdata('success', 'Berhasil mengubah potongan');
            redirect('admin/potongan', 'refresh');
        }
    }
}
