<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    public $table = 'tbl_user';
    public $id    = 'tbl_user.id';

    public function get_by_id($id)
    {
        $this->db->select('tbl_user.*, jabatan.jabatan');
        $this->db->from($this->table);
        // join table user
        $this->db->join('jabatan', 'jabatan.id = tbl_user.jabatan_id');
        $this->db->join('cabang', 'cabang.id = tbl_user.cabang_id');
        $this->db->where($this->id, $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all()
    {
        $id_role = $this->session->userdata('id_role');
        $cabang_id = $this->session->userdata('cabang_id');
    
        $this->db->select('jabatan.jabatan,cabang.cabang, tbl_user.*, tbl_role.name');
        $this->db->from('tbl_user');
        // join table user
        $this->db->join('jabatan', 'jabatan.id = tbl_user.jabatan_id');
        $this->db->join('cabang', 'cabang.id = tbl_user.cabang_id');
        $this->db->join('tbl_role', 'tbl_user.id_role = tbl_role.id');
        // Tambahkan kondisi WHERE jika id_role bukan 5
        if ($id_role != 5) {
            $this->db->where('tbl_user.cabang_id', $cabang_id);
        }
        $this->db->order_by('tbl_user.id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($where)
    {
        date_default_timezone_set('ASIA/JAKARTA');
        $user = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'username' => $this->input->post('username'),
            'phone' => $this->input->post('phone'),
            'email' => $this->input->post('email'),
            'id_role' => $this->input->post('id_role'),
            'created_at' => date('Y-m-d H:i:s'),
            'password' => get_hash($this->input->post('password')),
            'cabang_id' => $this->input->post('cabang_id'),
            'jabatan_id' => $this->input->post('jabatan_id'),
            'alamat' => $this->input->post('alamat'),
            'jk' => $this->input->post('jk'),
            'ttl' => $this->input->post('ttl'),
            'status_kepegawaian' => $this->input->post('status_kepegawaian'),
            'agama' => $this->input->post('agama'),
            'pendidikan' => $this->input->post('pendidikan'),
        );

        $this->db->where('id', $where);
        $this->db->update('tbl_user', $user);

        return $this->db->affected_rows();

        // $this->db->update($this->table, $data, $where);
        // return $this->db->affected_rows();
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tbl_user');
        return $this->db->affected_rows();

    }

    public function get_jabatan()
    {
        $this->db->select('*');
        $this->db->from('jabatan');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    public function get_cabang()
    {
        $this->db->select('*');
        $this->db->from('cabang');
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    public function register()
    {
        date_default_timezone_set('ASIA/JAKARTA');
        $data = array(
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'username' => $this->input->post('username'),
            'phone' => $this->input->post('phone'),
            'email' => $this->input->post('email'),
            'id_role' => $this->input->post('id_role'),
            'created_at' => date('Y-m-d H:i:s'),
            'password' => get_hash($this->input->post('password')),

            'cabang_id' => $this->input->post('cabang_id'),
            'jabatan_id' => $this->input->post('jabatan_id'),
            'alamat' => $this->input->post('alamat'),
            'jk' => $this->input->post('jk'),
            'ttl' => $this->input->post('ttl'),
            'status_kepegawaian' => $this->input->post('status_kepegawaian'),
            'agama' => $this->input->post('agama'),
            'pendidikan' => $this->input->post('pendidikan'),

            'activated' => 1
        );
        $user = $this->db->insert('tbl_user', $data);

    }


}

/* End of file Person_model.php */
