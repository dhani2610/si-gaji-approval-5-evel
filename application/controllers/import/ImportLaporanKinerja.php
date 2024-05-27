<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class ImportLaporanKinerja extends CI_Controller {

	public function __construct(){
		parent::__construct();
		// $this->load->library(array('excel','session'));
	}

	public function index()
	{
		$this->load->model('ImportModel');
		$data = array(
			'list_data'	=> $this->ImportModel->getData()
		);
		$this->load->view('import_excel.php',$data);
	}

	// public function import_excel($tunjangan_id){
	// 	if (isset($_FILES["fileExcel"]["name"])) {

	// 		$path = $_FILES["fileExcel"]["tmp_name"];
	// 		$obj = PHPExcel_IOFactory::load($path);
	// 		$object = $obj->getSheetByName('Lap Bulanan Pegawai');

	// 		// if not fount $object
	// 		if (!$object) {
	// 			$this->session->set_flashdata('error', 'File tidak valid');
	// 			redirect('pegawai/lapkin');
	// 		}

	// 		$tunjangan = $this->db->where('id', $tunjangan_id)->get('tunjangan')->row();
			
	// 		$highestRow = $object->getHighestRow();
	// 			$highestColumn = $object->getHighestColumn();	
	// 			for($row=15; $row<=$highestRow; $row++)
	// 			{
	// 				if ($object->getCellByColumnAndRow(1, $row)->getValue() != '') {
	// 					$date = $object->getCellByColumnAndRow(1, $row)->getValue();
	// 					$date = PHPExcel_Style_NumberFormat::toFormattedString($date, 'YYYY-MM-DD');
	// 					// parse date
	// 					if ($object->getCellByColumnAndRow(5, $row)->getValue() != '') {
	// 						$jenis_tugas = 'KS';
	// 					} elseif($object->getCellByColumnAndRow(6, $row)->getValue() != '') {
	// 						$jenis_tugas = 'KP';
	// 					} elseif($object->getCellByColumnAndRow(7, $row)->getValue() != '') {
	// 						$jenis_tugas = 'KT';
	// 					}
	// 					$temp_data[] = array(
	// 						'tunjangan_id'		=> $tunjangan->id,
	// 						'tanggal_kegiatan'	=> $date,
	// 						'nama_kegiatan'		=> $object->getCellByColumnAndRow(2, $row)->getValue(),
	// 						'output'			=> $object->getCellByColumnAndRow(3, $row)->getValue(),
	// 						'pengguna'			=> $object->getCellByColumnAndRow(4, $row)->getValue(),
	// 						'jenis_tugas'		=> $jenis_tugas,
	// 					); 	
	// 				}
	// 			}

	// 			// return $temp_data;

	// 		$this->load->model('ImportModel');
	// 		$insert = $this->ImportModel->insertLaporanKinerja($temp_data);
	// 		if($insert){
	// 			// upload file_lapkin
	// 			$config['upload_path'] = './upload/';
	// 			$config['allowed_types'] = 'xls|xlsx';
	// 			$config['max_size'] = '5000';
	// 			$config['file_name'] = $tunjangan->id;
	// 			$this->load->library('upload', $config);

	// 			$this->upload->do_upload('fileExcel');
	// 			$data = array('upload_data' => $this->upload->data());

	// 			$this->db->where('id', $tunjangan_id)->update('tunjangan', array('file_lapkin' => $tunjangan->id.'.xlsx'));
	// 			$this->session->set_flashdata('status', '<span class="glyphicon glyphicon-ok"></span> Data Berhasil di Import ke Database');
	// 			redirect($_SERVER['HTTP_REFERER']);
	// 		}else{
	// 			$this->session->set_flashdata('status', '<span class="glyphicon glyphicon-remove"></span> Terjadi Kesalahan');
	// 			redirect($_SERVER['HTTP_REFERER']);
	// 		}
	// 	}else{
	// 		echo "Tidak ada file yang masuk";
	// 	}
	// }

	public function import_excel($tunjangan_id) {
		if (isset($_FILES["fileExcel"]["name"])) {
            $path = $_FILES["fileExcel"]["tmp_name"];
            try {
                $spreadsheet = IOFactory::load($path);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                echo 'Error loading file: ' . $e->getMessage();
                return;
            }
            $sheet = $spreadsheet->getSheetByName('Lap Bulanan Pegawai');

            if (!$sheet) {
                echo 'File tidak valid';
                return;
            }

            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();

            $data = []; // Initialize the array

            for ($row = 15; $row <= $highestRow; $row++) {
                if ($sheet->getCell('B' . $row)->getValue() != '') { // Column B is column index 1 in PhpSpreadsheet
                    $date = $sheet->getCell('B' . $row)->getValue();
                    $date = Date::excelToDateTimeObject($date)->format('Y-m-d');

                    $jenis_tugas = '';
                    if ($sheet->getCell('F' . $row)->getValue() != '') {
                        $jenis_tugas = 'KS';
                    } elseif ($sheet->getCell('G' . $row)->getValue() != '') {
                        $jenis_tugas = 'KP';
                    } elseif ($sheet->getCell('H' . $row)->getValue() != '') {
                        $jenis_tugas = 'KT';
                    }

                    $data[] = array(
                        'tanggal_kegiatan'  => $date,
                        'nama_kegiatan'     => $sheet->getCell('C' . $row)->getValue(),
                        'output'            => $sheet->getCell('D' . $row)->getValue(),
                        'pengguna'          => $sheet->getCell('E' . $row)->getValue(),
                        'jenis_tugas'       => $jenis_tugas,
                    );
                }
            }

			$this->load->model('ImportModel');
			$insert = $this->ImportModel->insertLaporanKinerja($data);
			if($insert){
				// upload file_lapkin
				$config['upload_path'] = './upload/';
				$config['allowed_types'] = 'xls|xlsx';
				$config['max_size'] = '5000';
				$config['file_name'] = $tunjangan->id;
				$this->load->library('upload', $config);

				$this->upload->do_upload('fileExcel');
				$data = array('upload_data' => $this->upload->data());

				$this->db->where('id', $tunjangan_id)->update('tunjangan', array('file_lapkin' => $tunjangan->id.'.xlsx'));
				$this->session->set_flashdata('status', '<span class="glyphicon glyphicon-ok"></span> Data Berhasil di Import ke Database');
				redirect($_SERVER['HTTP_REFERER']);
			}else{
				$this->session->set_flashdata('status', '<span class="glyphicon glyphicon-remove"></span> Terjadi Kesalahan');
				redirect($_SERVER['HTTP_REFERER']);
			}
        } else {
            echo "Tidak ada file yang masuk";
        }
	}
	
	
	

}