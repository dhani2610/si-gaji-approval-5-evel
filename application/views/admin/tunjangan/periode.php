<!-- button tambah jabatan -->
<!-- script datepicker -->
<!--  -->
 <!-- Load jQuery from CDN -->
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Load jsPDF and html2canvas from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script type="text/javascript">
$(function() {
	$('#datepicker').datepicker({
		format: 'yyyy-mm-dd',
		autoclose: true
	});
});
</script>
<div class="row">
	<div class="col-md-12">
		<div class="alert alert-primary" role="alert">
		  <!-- <h6 class="alert-heading text-bold"></h6> -->
		  	<p class="mb-0">
				<?php if($this->session->userdata('id_role') == 1): ?>
			  	Silahkan buat periode kehadiran dan tunjangan pada Menu 
			  	<a href="<?= base_url('admin/kehadiran/') ?>" class="text-bold text-decoration-none">Kehadiran</a> 
				  <br>
				<?php endif; ?>
				  Pastikan data tunjangan telah divalidasi untuk bisa melakukan verifikasi pada periode yang telah dibuat.
			</p>
		</div>
		<div class="card">
			<div class="card-header with-border">
				<h3 class="card-title">Data Periode Tunjangan</h3>
				<!-- button add -->
				<div class="pull-right">
				</div>
			</div>

			<!-- card body -->
			<div class="card-body" id="content">
				<!-- table -->
				<div class="table-responsive">
					<table id="datatable" class="table table-bordered">
						<thead>
							<tr>
								<th width="10">No</th>
								<!-- <th>Nama Periode</th> -->
								<th class="text-center">Periode</th>
								<th>Tervalidasi</th>
								<!-- <th>Total Tunjangan</th> -->
								<th class="text-center">Verifikasi</th>
								<th class="text-center">Validasi Kepala Balai</th>
								<?php if ($this->session->userdata('jabatan_id') == 6 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<th class="text-center">Verifikasi 1</th>
								<?php endif; ?>

								<?php if ($this->session->userdata('jabatan_id') == 7 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<th class="text-center">Verifikasi 2</th>
								<?php endif; ?>

								<?php if ($this->session->userdata('jabatan_id') == 8 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<th class="text-center">Verifikasi 3</th>
								<?php endif; ?>

								<?php if ($this->session->userdata('jabatan_id') == 9 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<th class="text-center">Verifikasi 4</th>
								<?php endif; ?>

								<?php if ($this->session->userdata('jabatan_id') == 10 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<th class="text-center">Verifikasi 5</th>
								<?php endif; ?>

								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($periode as $value => $per): ?>
							<?php
								$periode = $per->tanggal;
								$id_role = $this->session->userdata('id_role');
								$cabang_id = $this->session->userdata('cabang_id');

								$this->db->from('tunjangan');
								$this->db->join('tbl_user', 'tbl_user.id = tunjangan.user_id');
								$this->db->where('tunjangan.periode', $periode);
								$this->db->where('tunjangan.validasi', 1);

								// Tambahkan kondisi WHERE jika id_role bukan 5
								if ($id_role != 5) {
									$this->db->where('tbl_user.cabang_id', $cabang_id);
								}

								$validasi = $this->db->count_all_results();
							?>
							<tr>
								<td><?= $value+1 ?></td>
								<!-- <td><?= $per->periode; ?></td> -->
								<td class="text-center"><?= $per->tanggal; ?></td>
								<td><?= $validasi ?></td>
								<!-- <td>
								<?php
										$periode = $per->tanggal;
										$id_role = $this->session->userdata('id_role');
										$cabang_id = $this->session->userdata('cabang_id');

										$this->db->select('SUM(total_tunjangan) AS total');
										$this->db->from('tunjangan');
										$this->db->join('tbl_user', 'tbl_user.id = tunjangan.user_id');
										$this->db->where('tunjangan.periode', $periode);

										// Tambahkan kondisi WHERE jika id_role bukan 5
										if ($id_role != 5) {
											$this->db->where('tbl_user.cabang_id', $cabang_id);
										}

										$total = $this->db->get()->row();
										echo "Rp. ".number_format($total->total, 0, ',', '.');
									?>
								</td> -->
								<td class="text-center">
									<?php if ($per->verifikasi == null or $per->verifikasi == '0000-00-00'): ?>
										<span class="badge badge-danger">Belum</span>
										<?php else: ?>
											<p class="mb-0"> <?= tgl_indo($per->verifikasi) ?></p>
									<?php endif ?>
								</td>
								<td class="text-center">
									<?php if ($per->ttd == null or $per->ttd == '0000-00-00'): ?>
										<span class="badge badge-danger">Belum</span>
										<!-- verifikasi periode -->
										<?php if ($this->session->userdata('id_role') == 2): ?>
											<br>
											<a href="<?= base_url('kepala/tunjangan/ttd/'.$per->tanggal) ?>" class="mt-2 btn btn-outline-success btn-sm">
												<i class="fa fa-check"></i> Validasi Kepala Balai

											</a>
										<?php endif ?>
										<?php else: ?>
											<p class="mb-0"> <?= tgl_indo($per->ttd) ?></p>
									<?php endif ?>
								</td>
								<!-- Verifikator 1 -->
								<?php if ($this->session->userdata('jabatan_id') == 6 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<td>
										<?= $this->load->view('admin/tunjangan/verifikasi_buttons', ['verifikator' => 1, 'per' => $per], true) ?>
								</td>
								<?php endif; ?>

								<!-- Verifikator 2 -->
								<?php if ($this->session->userdata('jabatan_id') == 7 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<td>
										<?= $this->load->view('admin/tunjangan/verifikasi_buttons', ['verifikator' => 2, 'per' => $per], true) ?>
								</td>
								<?php endif; ?>

								<!-- Verifikator 3 -->
								<?php if ($this->session->userdata('jabatan_id') == 8 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<td>
										<?= $this->load->view('admin/tunjangan/verifikasi_buttons', ['verifikator' => 3, 'per' => $per], true) ?>
								</td>
								<?php endif; ?>

								<!-- Verifikator 4 -->
								<?php if ($this->session->userdata('jabatan_id') == 9 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<td>
										<?= $this->load->view('admin/tunjangan/verifikasi_buttons', ['verifikator' => 4, 'per' => $per], true) ?>
								</td>
								<?php endif; ?>

								<!-- Verifikator 5 -->
								<?php if ($this->session->userdata('jabatan_id') == 10 && $this->session->userdata('id_role') == 6 || $this->session->userdata('id_role') == 1): ?>
								<td>
										<?= $this->load->view('admin/tunjangan/verifikasi_buttons', ['verifikator' => 5, 'per' => $per], true) ?>
								</td>
								<?php endif; ?>

								<td>
									<!-- input kehadiran -->
									<a href="
										<?php if($this->session->userdata('id_role') == 1): ?>
											<?= base_url('admin/tunjangan/show/'.$per->tanggal) ?>
										<?php elseif($this->session->userdata('id_role') == 3 || $this->session->userdata('id_role') == 6): ?>
											<?= base_url('petugas/tunjangan/show/'.$per->tanggal) ?>
										<?php elseif($this->session->userdata('id_role') == 2): ?>
											<?= base_url('kepala/tunjangan/show/'.$per->tanggal) ?>
										<?php endif; ?>
									" class="btn btn-primary btn-sm">
										<i class="fa fa-list"></i> Detail Tunjangan
									</a>
									<!-- rekap bulanan -->
									<a href="<?= base_url('petugas/tunjangan/pdf/'.$per->tanggal) ?>" class="btn btn-danger btn-sm">
										<i class="fa fa-file-pdf-o"></i> Rekap Bulanan
									</a>
									<!-- <a href="<?= base_url('export/rekap_excel/'.$per->tanggal) ?>" class="btn btn-danger btn-sm">
										<i class="fa fa-file-pdf-o"></i> Rekap Bulanan
									</a> -->

									<!-- verifikasi periode -->
									<?php if ($per->verifikasi == 0 && $this->session->userdata('id_role') == 1): ?>
										<a href="<?= base_url('admin/tunjangan/verifikasi/'.$per->tanggal) ?>" class="btn btn-success btn-sm">
											<i class="fa fa-check"></i> Verifikasi
										</a>
									<?php endif ?>

									

								</td>
							</tr>
							<!-- end modal edit -->
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>

		</div>
	</div>
</div>


<script>
	function generatePDF() {
		const { jsPDF } = window.jspdf;

		html2canvas(document.getElementById('content')).then(canvas => {
			const imgData = canvas.toDataURL('image/png');
			const pdf = new jsPDF();
			pdf.addImage(imgData, 'PNG', 10, 10);
			pdf.save('example.pdf');
		});
	}
</script>


