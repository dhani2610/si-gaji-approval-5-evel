<?php
defined('BASEPATH') or exit('No direct script access allowed');

// // USE spreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Tunjangan extends MY_Controller
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
        $data = konfigurasi('Tunjangan');

        $data['periode'] = $this->db->get('periode_tunjangan')->result();
        // get periode and count validasi

        $this->template->load('layouts/template', 'admin/tunjangan/periode', $data);
        // $this->template->load('layouts/new','admin/dashboard', $data);
    }
    public function laporan()
    {
        $data = konfigurasi('Laporan');

        // Ambil nilai filter dari input GET
        $periode = $this->input->get('periode');
        // Ubah format periode dari YYYY-MM menjadi MM-YYYY
        $periode_formatted = date('m-Y', strtotime($periode));
        $status = $this->input->get('status');
        $verifikator = $this->input->get('verifikator');

        // Mulai query
        $this->db->select('*');
        $this->db->from('periode_tunjangan');

        // Tambahkan kondisi filter jika ada
        if (!empty($periode)) {
            $this->db->where('tanggal', $periode_formatted);
        }
        if (!empty($status)) {
            $this->db->where('status_verifikasi_' . $verifikator, $status);
        }
        if (!empty($verifikator)) {
            $this->db->where('status_verifikasi_' . $verifikator . ' IS NOT NULL');
        }

        // Eksekusi query dan ambil hasil
        $data['periode'] = $this->db->get()->result();

        // Load view dengan data yang sudah di-filter
        $this->template->load('layouts/template', 'admin/tunjangan/laporan', $data);
    }


    public function show($periode)
    {
        $id_role = $this->session->userdata('id_role');
        $cabang_id = $this->session->userdata('cabang_id');
        
        $data = konfigurasi('Tunjangan');
        $data['periode'] = $this->db->get_where('periode_tunjangan', ['tanggal' => $periode])->row();

        if ($id_role != 5) {
            $data['tunjangans'] = $this->db->where('periode', $periode)
            ->select('tunjangan.*, first_name, last_name, username')
            ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
            ->where('tbl_user.cabang_id', $cabang_id)
            ->get('tunjangan')->result();
        }else{
            $data['tunjangans'] = $this->db->where('periode', $periode)
            ->select('tunjangan.*, first_name, last_name, username')
            ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
            ->get('tunjangan')->result();
        }

        $this->template->load('layouts/template', 'admin/tunjangan/show', $data);
    }

    public function request_verifikasi($tanggal, $verifikator) {
        $status_column = "status_verifikasi_$verifikator";
        $this->db->set($status_column, 2);
        $this->db->where('tanggal', $tanggal);
        $this->db->update('periode_tunjangan');
        if ($this->session->userdata('id_role') == "1") {
            $path = 'admin';
        }else{
            $path = 'petugas';
        }
        redirect($path.'/tunjangan');
    }

    public function approve_verifikasi($tanggal, $verifikator) {
        $status_column = "status_verifikasi_$verifikator";
        $this->db->set($status_column, 4);
        $this->db->where('tanggal', $tanggal);
        $this->db->update('periode_tunjangan');
        if ($this->session->userdata('id_role') == "1") {
            $path = 'admin';
        }else{
            $path = 'petugas';
        }
        redirect($path.'/tunjangan');
    }

    public function reject_verifikasi($tanggal, $verifikator) {
        $status_column = "status_verifikasi_$verifikator";
        $pesan_column = "pesan_verifikasi_$verifikator";
        $pesan = $this->input->post('pesan');
        $this->db->set($status_column, 3);
        $this->db->set($pesan_column, $pesan);
        $this->db->where('tanggal', $tanggal);
        $this->db->update('periode_tunjangan');
        if ($this->session->userdata('id_role') == "1") {
            $path = 'admin';
        }else{
            $path = 'petugas';
        }
        redirect($path.'/tunjangan');
    }

    public function verify_with_signature($tanggal, $verifikator) {
        // Check role and permissions
        $config['upload_path']          = 'assets/uploads/images/signatures/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100; //set max size allowed in Kilobyte
        $config['max_width']            = 1000; // set max width image allowed
        $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000);
        $this->load->library('upload', $config);
        $upload = $this->_do_upload();
        // Simpan data ke database
        $this->db->set('status_verifikasi_5', 4);
        $this->db->set('foto_file_ttd_verifikator_5', $upload);
        $this->db->where('tanggal', $tanggal);
        $this->db->update('periode_tunjangan');

        if ($this->session->userdata('id_role') == "1") {
            $path = 'admin';
        } else {
            $path = 'petugas';
        }

        redirect(base_url($path . '/tunjangan'));
           
    }
    
    private function _do_upload()
    {
        $config['upload_path']          = 'assets/uploads/signatures/';
        $config['allowed_types']        = 'gif|jpg|png';
        $config['max_size']             = 100; //set max size allowed in Kilobyte
        $config['max_width']            = 1000; // set max width image allowed
        $config['max_height']           = 1000; // set max height allowed
        $config['file_name']            = round(microtime(true) * 1000);
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('signature_file')) {
            $this->session->set_flashdata('msg', $this->upload->display_errors('', ''));
            if ($this->session->userdata('id_role') == "1") {
                $path = 'admin';
            } else {
                $path = 'petugas';
            }

            redirect(base_url($path . '/tunjangan'));
        }
        return $this->upload->data('file_name');
    }
    

    public function add($per)
    {
        $periode = $this->db->get_where('periode_tunjangan', ['tanggal' => $per])->row();
        if ($periode->verifikasi == '1') {
            $this->session->set_flashdata('error', 'Gagal menambahkan Tunjangan. Periode telah dikonfirmasi');
            redirect('admin/tunjangan', 'refresh');
        } else {
            $kehadiran = $this->db->where('periode', $per)
            ->select('kehadiran.*, tunjangan')
            ->join('tbl_user', 'tbl_user.id = kehadiran.user_id')
            ->join('jabatan', 'jabatan.id = tbl_user.jabatan_id')
            ->where('kehadiran.validasi', 1)
            ->get('kehadiran')->result();
            // join user with jabatan

            $key = 0;
            foreach ($kehadiran as $value) {

                $tunjangan = $this->db->where('kehadiran_id', $value->id)->get('tunjangan')->row();
                $total_tunjangan = $value->tunjangan - ($value->tunjangan * ($value->potongan / 100));
                    $data = [
                        'kehadiran_id'  => $value->id,
                        'user_id'       => $value->user_id,
                        'periode'       => $per,
                        'tunjangan'     => $value->tunjangan,
                        'total_potongan'=> $value->potongan,
                        'total_tunjangan'    => $total_tunjangan,
                    ];
                if (!@$tunjangan) {
                    $this->db->insert('tunjangan', $data);
                } else {
                    $this->db->where('kehadiran_id', $value->id)->where('validasi', '!= 1')->update('tunjangan', $data);
                }

                $key++;
            }

            if ($key > 0) {
                $this->session->set_flashdata('success', 'Berhasil menambahkan Tunjangan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan Tunjangan. Tidak ada data yang ditambahkan');
            }
            redirect('admin/tunjangan/show/'.$per, 'refresh');
        }
    }

    // validasi
    public function validasi($id)
    {
        $tunjangan = $this->db->get_where('tunjangan', ['id' => $id])->row();
        if ($tunjangan->validasi == '1') {
            $this->session->set_flashdata('error', 'Gagal memvalidasi Tunjangan. Tunjangan telah divalidasi');
            redirect('admin/tunjangan/show/'.$tunjangan->periode, 'refresh');
        } else {
            $this->db->where('id', $id)->update('tunjangan', ['validasi' => '1']);
            $this->session->set_flashdata('success', 'Berhasil memvalidasi Tunjangan');
            redirect('admin/tunjangan/show/'.$tunjangan->periode, 'refresh');
        }
    }

    public function terima($id)
    {
        $tunjangan = $this->db->get_where('tunjangan', ['id' => $id])->row();
        if (@$tunjangan->tanggal_terima) {
            $this->session->set_flashdata('error', 'Gagal menerima Tunjangan. Tunjangan telah diterima');
            redirect('admin/tunjangan/show/'.$tunjangan->periode, 'refresh');
        } else {
            if ($tunjangan->validasi == '1') {
                $this->db->where('id', $id)->update('tunjangan', ['tanggal_terima' => date('Y-m-d')]);
                $this->session->set_flashdata('success', 'Berhasil menerima Tunjangan');
            } else {
                $this->session->set_flashdata('error', 'Gagal menerima Tunjangan. Tunjangan belum divalidasi');
            }
            redirect('admin/tunjangan/show/'.$tunjangan->periode, 'refresh');
        }
    }

    public function verifikasi($tanggal)
    {
        $periode = $this->db->get_where('periode_tunjangan', ['tanggal' => $tanggal])->row();
        if ($periode->verifikasi == '1') {
            $this->session->set_flashdata('error', 'Gagal mengkonfirmasi Tunjangan. Periode telah dikonfirmasi');
            redirect('admin/tunjangan', 'refresh');
        } else {

            $tunjangan = $this->db->get_where('tunjangan', ['periode' => $tanggal])->result();
            foreach ($tunjangan as $value) {
                if ($value->validasi != '1') {
                    $this->session->set_flashdata('error', 'Terdapat data tunjangan yang belum divalidasi');
                    redirect('admin/tunjangan/show/'.$periode->tanggal, 'refresh');
                }
            }

            $this->db->where('tanggal', $tanggal)->update('periode_tunjangan', ['verifikasi' => date('Y-m-d')]);
            $this->session->set_flashdata('success', 'Berhasil mengkonfirmasi Tunjangan');
            redirect('admin/tunjangan', 'refresh');
        }
    }

    public function nilai($id)
    {
        // update penilaian
        $tunjangan = $this->db->get_where('tunjangan', ['id' => $id])->row();
        $this->db->where('id', $id)->update('tunjangan', ['penilaian' => $this->input->post('penilaian')]);
        $this->session->set_flashdata('success', 'Berhasil mengubah nilai');
        redirect('admin/tunjangan/show/'.$tunjangan->periode, 'refresh');

    }

    // public function rekap($periode)
    // {
    //     // phpspreadsheet print

    //     $this->load->library('phpspreadsheet');
    //     $this->excel->setActiveSheetIndex(0);
    //     $this->excel->getActiveSheet()->setTitle('Rekap Tunjangan');

    //     $this->excel->getActiveSheet()->setCellValue('A1', 'No');
    //     $this->excel->getActiveSheet()->setCellValue('B1', 'Nama');
    //     $this->excel->getActiveSheet()->setCellValue('C1', 'NIP');
    //     $this->excel->getActiveSheet()->setCellValue('D1', 'Jabatan');
    //     $this->excel->getActiveSheet()->setCellValue('E1', 'Golongan');
    //     $this->excel->getActiveSheet()->setCellValue('F1', 'Tunjangan');
    //     $this->excel->getActiveSheet()->setCellValue('G1', 'Potongan');
    //     $this->excel->getActiveSheet()->setCellValue('H1', 'Total');

    //     $tunjangan = $this->db->where('periode' , $periode)
    //     ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
    //     ->join('jabatan', 'jabatan.id = tbl_user.jabatan_id')
    //     ->select('tunjangan.*, tbl_user.first_name, tbl_user.last_name, tbl_user.username, jabatan.jabatan as jabatan, jabatan.kelas')
    //     ->get('tunjangan')->result();

    //     $i = 2;

    //     foreach ($tunjangan as $value) {
            
    //         $this->excel->getActiveSheet()->setCellValue('A'.$i, $i-1);
    //         $this->excel->getActiveSheet()->setCellValue('B'.$i, $value->first_name . ' ' . $value->last_name);
    //         $this->excel->getActiveSheet()->setCellValue('C'.$i, $value->username);
    //         $this->excel->getActiveSheet()->setCellValue('D'.$i, $value->jabatan);
    //         $this->excel->getActiveSheet()->setCellValue('E'.$i, $value->kelas);
    //         $this->excel->getActiveSheet()->setCellValue('F'.$i, $value->tunjangan);
    //         $this->excel->getActiveSheet()->setCellValue('G'.$i, $value->total_potongan);
    //         $this->excel->getActiveSheet()->setCellValue('H'.$i, $value->total_tunjangan);
    //         $i++;
    //     }

    //     $filename = 'Rekap Tunjangan '.$periode.'.xls';
    //     header('Content-Type: application/vnd.ms-excel');
    //     header('Content-Disposition: attachment;filename="'.$filename.'"');
    //     header('Cache-Control: max-age=0');
    //     $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->excel, 'Xls');
    //     $writer->save('php://output');


    // }

    public function rekap_excel($periode){
        // php spreadsheet
        

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Admin')
        ->setLastModifiedBy('Admin')
        ->setTitle('Rekap Tunjangan')
        ->setSubject('Rekap Tunjangan')
        ->setDescription('Rekap Tunjangan')
        ->setKeywords('Rekap Tunjangan')
        ->setCategory('Rekap Tunjangan');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama');
        $sheet->setCellValue('C1', 'NIP');
        $sheet->setCellValue('D1', 'Jabatan');
        $sheet->setCellValue('E1', 'Golongan');
        $sheet->setCellValue('F1', 'Tunjangan');
        $sheet->setCellValue('G1', 'Potongan');
        $sheet->setCellValue('H1', 'Total');

        $tunjangan = $this->db->where('periode' , $periode)
        ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
        ->join('jabatan', 'jabatan.id = tbl_user.jabatan_id')
        ->select('tunjangan.*, tbl_user.first_name, tbl_user.last_name, tbl_user.username, jabatan.jabatan as jabatan, jabatan.kelas')
        ->get('tunjangan')->result();

        $i = 2;

        foreach ($tunjangan as $value) {
            
            $sheet->setCellValue('A'.$i, $i-1);
            $sheet->setCellValue('B'.$i, $value->first_name . ' ' . $value->last_name);
            $sheet->setCellValue('C'.$i, $value->username);
            $sheet->setCellValue('D'.$i, $value->jabatan);
            $sheet->setCellValue('E'.$i, $value->kelas);
            $sheet->setCellValue('F'.$i, $value->tunjangan);
            $sheet->setCellValue('G'.$i, $value->total_potongan);
            $sheet->setCellValue('H'.$i, $value->total_tunjangan);
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Rekap Tunjangan '.$periode.'.xlsx';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }


    public function penilaian($id)
    {
        $tunjangan = $this->db->where('tunjangan.id' , $id)
        ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
        ->join('jabatan', 'jabatan.id = tbl_user.jabatan_id')
        ->select('tunjangan.*, tbl_user.first_name, tbl_user.last_name, tbl_user.username, jabatan.jabatan as jabatan, jabatan.kelas')
        ->get('tunjangan')->row();


        
        $data = konfigurasi('Tunjangan');

        $data['periode'] = $this->db->where('tanggal', $tunjangan->periode)->get('periode_tunjangan')->row();
        $data['tunjangan'] = $tunjangan;
        
        // get periode and count validasi

        $this->template->load('layouts/template', 'admin/tunjangan/penilaian', $data);

        // $this->load->view('admin/layout/wrapper', $data, FALSE);

    }

    public function submit_penilaian($id)
    {
        $tunjangan = $this->db->where('tunjangan.id' , $id)->get('tunjangan')->row();
        // check if penilaian exist
        $penilaian = $this->db->where('tunjangan_id' , $id)->get('penilaian')->row();

        if ($penilaian) {
            $this->session->set_flashdata('error', 'Data Penilaian Sudah Ada');
            redirect('admin/tunjangan/show/'.$tunjangan->periode);
        } else {
            $data = [
                'tunjangan_id' => $tunjangan->id,
                'kualitas_a' => $this->input->post('kualitas_a'),
                'kualitas_b' => $this->input->post('kualitas_b'),
                'kualitas_c' => $this->input->post('kualitas_c'),
                'kualitas_d' => $this->input->post('kualitas_d'),
                'ketepatan_a' => $this->input->post('ketepatan_a'),
                'ketepatan_b' => $this->input->post('ketepatan_b'),
                'kuantitas_a' => $this->input->post('kuantitas_a'),
                'kuantitas_b' => $this->input->post('kuantitas_b'),

                'pelayanan' => $this->input->post('pelayanan'),
                'integritas' => $this->input->post('integritas'),
                'komitmen' => $this->input->post('komitmen'),
                'disiplin' => $this->input->post('disiplin'),
                'kerjasama' => $this->input->post('kerjasama'),

                'tanggal_penilaian' => date('Y-m-d'),

            ];
    
            $this->db->insert('penilaian', $data);

            $kualitas = ($this->input->post('kualitas_a') + $this->input->post('kualitas_b') + $this->input->post('kualitas_c') + $this->input->post('kualitas_d')) / 4;

            $ketepatan = ($this->input->post('ketepatan_a') + $this->input->post('ketepatan_b')) / 2;
            $kuantitas = ($this->input->post('kuantitas_a') + $this->input->post('kuantitas_b')) / 2;
            $hasil_kerja = ($kualitas * 0.5) + ($ketepatan * 0.3) + ($kuantitas * 0.2);


            $perilaku = ($this->input->post('pelayanan') * 0.2) + ($this->input->post('integritas') * 0.2) + ($this->input->post('komitmen') * 0.2) + ($this->input->post('disiplin') * 0.2) + ($this->input->post('kerjasama') * 0.2);

            $nilai = ($hasil_kerja * 0.7) + ($perilaku * 0.3);

            // update penilaian at tunjangan
            $this->db->where('id' , $id)->update('tunjangan', ['penilaian' => $nilai]);
            $this->session->set_flashdata('success', 'Data Penilaian Berhasil Ditambahkan');
            redirect('admin/tunjangan/show/'.$tunjangan->periode);
        }
        
    }

    public function show_penilaian($id)
    {
        $tunjangan = $this->db->where('tunjangan.id' , $id)
        ->join('tbl_user', 'tbl_user.id = tunjangan.user_id')
        ->join('jabatan', 'jabatan.id = tbl_user.jabatan_id')
        ->select('tunjangan.*, tbl_user.first_name, tbl_user.last_name, tbl_user.username, jabatan.jabatan as jabatan, jabatan.kelas')
        ->get('tunjangan')->row();

        $penilaian = $this->db->where('tunjangan_id' , $id)->get('penilaian')->row();
        
        $data = konfigurasi('Tunjangan');

        $data['periode'] = $this->db->where('tanggal', $tunjangan->periode)->get('periode_tunjangan')->row();
        $data['tunjangan'] = $tunjangan;
        $data['penilaian'] = $penilaian;
        
        // get periode and count validasi

        $this->template->load('layouts/template', 'admin/tunjangan/show-penilaian', $data);
    }
        

        
    
    

}
