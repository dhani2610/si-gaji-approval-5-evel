<?php 
defined('BASEPATH') or exit('No direct script access allowed');

/*
 * |==============================================================|
 * | Please DO NOT modify this information :                      |
 * |--------------------------------------------------------------|
 * | Author          : Susantokun
 * | Email           : admin@susantokun.com
 * | Filename        : Auth.php
 * | Instagram       : @susantokun
 * | Blog            : http://www.susantokun.com
 * | Info            : http://info.susantokun.com
 * | Demo            : http://demo.susantokun.com
 * | Youtube         : http://youtube.com/susantokun
 * | File Created    : Thursday, 12th March 2020 10:34:33 am
 * | Last Modified   : Thursday, 12th March 2020 10:57:22 am
 * |==============================================================|
 */

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('Auth_model');
    }

    public function profile()
    {
        $data = konfigurasi('Profile', 'Kelola Profile');
        $this->template->load('layouts/template', 'authentication/profile', $data);
    }

    public function updateProfile()
    {
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|max_length[15]');
        $this->form_validation->set_rules('first_name', 'Nama Depan', 'trim|required|min_length[2]|max_length[15]');
        $this->form_validation->set_rules('last_name', 'Nama Belakang', 'trim|required|min_length[2]|max_length[15]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[8]|max_length[50]');
        $this->form_validation->set_rules('phone', 'Telp', 'trim|required|min_length[11]|max_length[12]');

        $id = $this->session->userdata('id');
        $data = array(
            'username' => $this->input->post('username'),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'email' => $this->input->post('email'),
            'phone' => $this->input->post('phone'),
        );
        if ($this->form_validation->run() == true) {
            if (!empty($_FILES['photo']['name'])) {
                $upload = $this->_do_upload();  

                //delete file
                $user = $this->Auth_model->get_by_id($this->session->userdata('id'));
                if (file_exists('assets/uploads/images/foto_profil/'.$user->photo) && $user->photo) {
                    unlink('assets/uploads/images/foto_profil/'.$user->photo);
                }

                $data['photo'] = $upload;
            }
            $result = $this->Auth_model->update($data, $id);
            if ($result > 0) {
                $this->updateProfil();
                $this->session->set_flashdata('success', 'Data Profil Berhasil diubah');
                redirect('auth/profile');
            } else {
                $this->session->set_flashdata('error', 'Data Profile Gagal diubah');
                redirect('auth/profile');
            }
        } else {
            $this->session->set_flashdata('error', show_err_msg(validation_errors()));
            redirect('auth/profile');
        }
    }

    public function updatePassword()
    {
        $this->form_validation->set_rules('passLama', 'Password Lama', 'trim|required|min_length[5]|max_length[25]');
        $this->form_validation->set_rules('passBaru', 'Password Baru', 'trim|required|min_length[5]|max_length[25]');
        $this->form_validation->set_rules('passKonf', 'Password Konfirmasi', 'trim|required|min_length[5]|max_length[25]');

        $id = $this->session->userdata('id');
        if ($this->form_validation->run() == true) {
            if (password_verify($this->input->post('passLama'), $this->session->userdata('password'))) {
                if ($this->input->post('passBaru') != $this->input->post('passKonf')) {
                    $this->session->set_flashdata('msg', show_err_msg('Password Baru dan Konfirmasi Password harus sama'));
                    redirect('auth/profile');
                } else {
                    $data = ['password' => get_hash($this->input->post('passBaru'))];
                    $result = $this->Auth_model->update($data, $id);
                    if ($result > 0) {
                        $this->updateProfil();
                        $this->session->set_flashdata('success', 'Password Berhasil diubah');
                        redirect('auth/profile');
                    } else {
                        $this->session->set_flashdata('error', 'Password Gagal diubah');
                        redirect('auth/profile');
                    }
                }
            } else {
                $this->session->set_flashdata('error', 'Password Salah');
                redirect('auth/profile');
            }
        } else {
            $this->session->set_flashdata('error', 'Terjadi Kesalahan Input. Mohon pastikan data sudah benar');
            redirect('auth/profile');
        }
    }

    public function updateDataDiri()
    {
        $id = $this->session->userdata('id');
        $data = array(
            'agama' => $this->input->post('agama'),
            'alamat' => $this->input->post('alamat'),
            'ttl' => $this->input->post('ttl'),
            'jk' => $this->input->post('jk'),
            'pendidikan' => $this->input->post('pendidikan'),
        );

        $result = $this->Auth_model->update($data, $id);
        if ($result > 0) {
            $this->updateProfil();
            $this->session->set_flashdata('success', 'Data Diri Berhasil diubah');
            redirect('auth/profile');
        } else {
            $this->session->set_flashdata('error', 'Data Diri Gagal diubah');
            redirect('auth/profile');
        }
        
    }

    private function _do_upload()
    {
        $config['upload_path']          = 'assets/uploads/images/foto_profil/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100; //set max size allowed in Kilobyte
        $config['max_width']            = 1000; // set max width image allowed
        $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000);
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('photo')) {
            $this->session->set_flashdata('msg', $this->upload->display_errors('', ''));
            redirect('auth/profile');
        }
        return $this->upload->data('file_name');
    }

    public function register()
    {
        $data = konfigurasi('Register');
        $this->template->load('authentication/layouts/template', 'authentication/register', $data);
    }

    public function check_register()
    {
        $data = konfigurasi('Register');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[50]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[20]');
        if ($this->form_validation->run() == false) {
            $this->register();
        } else {
            $this->Auth_model->reg();
            $this->session->set_flashdata('alert', '<p class="box-msg">
              <div class="info-box alert-success">
              <div class="info-box-icon">
              <i class="fa fa-check-circle"></i>
              </div>
              <div class="info-box-content" style="font-size:14">
              <b style="font-size: 20px">SUKSES</b><br>Pendaftaran berhasil, silakan login.</div>
              </div>
              </p>
            ');
            redirect('auth/login', 'refresh', $data);
        }
    }

    public function check_account()
    {
        //validasi login
        $email      = $this->input->post('email');
        $password   = $this->input->post('password');

        //ambil data dari database untuk validasi login
        $query = $this->Auth_model->check_account($email, $password);

        if ($query === 1) {
            $this->session->set_flashdata('error', 'Akun tidak ditemukan. Silahkan Coba Lagi');
        } elseif ($query === 2) {
            $this->session->set_flashdata('error', 'Akun tidak aktif, silakan hubungi Administrator.');

        } elseif ($query === 3) {
            $this->session->set_flashdata('error', 'Password Salah. Silahkan Coba Lagi.');

        } else {
            //membuat session dengan nama userData yang artinya nanti data ini bisa di ambil sesuai dengan data yang login
            $userdata = array(
              'is_login'    => true,
              'id'          => $query->id,
              'password'    => $query->password,
              'id_role'     => $query->id_role,
              'username'    => $query->username,
              'first_name'  => $query->first_name,
              'last_name'   => $query->last_name,
              'email'       => $query->email,
              'phone'       => $query->phone,
              'photo'       => $query->photo,
              'last_login'  => $query->last_login,
              'cabang_id'  => $query->cabang_id,
              'jabatan_id'  => $query->jabatan_id,
              'created_at'  => $query->created_at,
              'updated_at'  => $query->updated_at,
            );
            $this->session->set_userdata($userdata);
            return true;
        }
    }
    public function login()
    {
        $data = konfigurasi('Login');
        //melakukan pengalihan halaman sesuai dengan levelnya
        if ($this->session->userdata('id_role') == "1") {
            redirect('admin/home');
        }
        if ($this->session->userdata('id_role') == "2") {
            redirect('kepala/home');
        }
        if ($this->session->userdata('id_role') == "3") {
            redirect('petugas/home');
        }
        if ($this->session->userdata('id_role') == "4") {
            redirect('pegawai/home');
        }
        if ($this->session->userdata('id_role') == "5") {
            redirect('admin/home');
        }
        if ($this->session->userdata('id_role') == "6") {
            redirect('petugas/home');
        }

        //proses login dan validasi nya
        if ($this->input->post('submit')) {
            $this->form_validation->set_rules('email', 'Email', 'trim|required|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[22]');
            $error = $this->check_account();

            if ($this->form_validation->run() && $error === true) {
                $data = $this->Auth_model->check_account($this->input->post('email'), $this->input->post('password'));

                // if query return 2
                //jika bernilai TRUE maka alihkan halaman sesuai dengan level nya
                $this->session->set_flashdata('success', 'Anda Berhasil Login');

                if ($data->id_role == '1') {
                    redirect('admin/home');
                } elseif ($data->id_role == '2') {
                    redirect('kepala/home');
                } elseif ($data->id_role == '3') {
                    redirect('petugas/home');
                } elseif ($data->id_role == '4') {
                    redirect('pegawai/home');
                }elseif ($data->id_role == '5') {
                    redirect('admin/home');
                }elseif ($data->id_role == '6') {
                    redirect('petugas/home');
                }
            } else {
                $this->template->load('authentication/layouts/template', 'authentication/login', $data);
            }
        } else {
            $this->template->load('authentication/layouts/template', 'authentication/login', $data);
        }
    }
    public function logout()
    {
        date_default_timezone_set('ASIA/JAKARTA');
        $date = array('last_login' => date('Y-m-d H:i:s'));
        $id = $this->session->userdata('id');
		$this->Auth_model->logout($date, $id);
		$user_data = $this->session->userdata();
		foreach ($user_data as $key => $value) {
			if ($key!='__ci_last_regenerate' && $key != '__ci_vars')
			$this->session->unset_userdata($key);
		}
        $this->session->set_flashdata('success', 'Anda Telah Logout');
        redirect('auth/login');
    }
}
