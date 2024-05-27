<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Cabang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->check_login();
        if ($this->session->userdata('id_role') != "5") {
            redirect('', 'refresh');
        }
    }

    public function index()
    {
        $data = konfigurasi('Cabang');
        $data['cabangs'] = $this->db->get('cabang')->result();
        $this->template->load('layouts/template', 'admin/cabang/index', $data);
        // $this->template->load('layouts/new','admin/dashboard', $data);
    }

    public function store()
    {
        $this->form_validation->set_rules('cabang', 'Nama Cabang', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat Cabang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Gagal menambahkan cabang');
            redirect('admin/cabang', 'refresh');
        } else {
            $data = [
                'cabang' => $this->input->post('cabang'),
                'alamat' => $this->input->post('alamat'),
            ];
            $this->db->insert('cabang', $data);
            $this->session->set_flashdata('success', 'Berhasil menambahkan cabang');
            redirect('admin/cabang', 'refresh');

        }
    }

    public function update()
    {
        $this->form_validation->set_rules('cabang', 'Nama Cabang', 'required');
        $this->form_validation->set_rules('alamat', 'Alamat Cabang', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', 'Gagal mengubah cabang');
            redirect('admin/cabang', 'refresh');
        } else {
            $data = [
                'cabang' => $this->input->post('cabang'),
                'alamat' => $this->input->post('alamat'),
            ];
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('cabang', $data);
            $this->session->set_flashdata('success', 'Berhasil mengubah cabang');
            redirect('admin/cabang', 'refresh');
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('cabang');
        $this->session->set_flashdata('success', 'Berhasil menghapus cabang');
        redirect('admin/cabang', 'refresh');
    }
}
