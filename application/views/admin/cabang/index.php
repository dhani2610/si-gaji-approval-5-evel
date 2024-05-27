<!-- button tambah jabatan -->
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header with-border">
				<h3 class="card-title">Data Cabang</h3>
				<!-- button add -->
				<div class="pull-right">
					<!-- button modal add jabatan -->
					<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-add-jabatan">
						<i class="fa fa-plus"></i> Tambah
					</button>
					<!-- modal -->
					<!-- modal add jabatan -->
					<!-- Button trigger modal -->
					<?php 
						$kelas = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18'];
					?>
					<div class="modal fade" id="modal-add-jabatan"  tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title">Tambah Cabang</h4>
								</div>
								<div class="modal-body">
									<form action="<?= base_url('admin/cabang/store'); ?>" method="post">

										<div class="form-group">
											<label for="cabang">Cabang</label>
											<input type="text" name="cabang" id="cabang" class="form-control" placeholder="Cabang">
										</div>
										<div class="form-group">
											<label for="alamat">Alamat</label>
											<textarea name="alamat" class="form-control" id=""></textarea>
										</div>

										<div class="modal-footer">
											<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
											<button type="submit" class="btn btn-primary">Simpan</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Cabang</th>
								<th>Alamat</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php $no = 1; ?>
							<?php foreach ($cabangs as $cabang) : ?>
								<tr>
									<td><?= $no; ?></td>
									<td><?= $cabang->cabang; ?></td>
									<td><?= $cabang->alamat; ?></td>
									<td>
										<!-- modal edit jabatan -->
										<button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#modal-edit-jabatan<?= $cabang->id; ?>">
											<i class="fa fa-edit"></i> Edit
										</button>

										<!-- modal -->
										<!-- modal edit jabatan -->
										<!-- Button trigger modal -->
										<div class="modal fade" id="modal-edit-jabatan<?= $cabang->id; ?>"  tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
											<div class="modal-dialog" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h4 class="modal-title">Edit Jabatan</h4>
													</div>
													<div class="modal-body">
														<form action="<?= base_url('admin/cabang/update'); ?>" method="post">
															<input type="hidden" name="id" value="<?= $cabang->id; ?>">

															<div class="form-group">
																<label for="cabang">Cabang</label>
																<input type="text" name="cabang" id="cabang" class="form-control" value="<?= $cabang->cabang; ?>" placeholder="Cabang">
															</div>
															<div class="form-group">
																<label for="alamat">Alamat</label>
																<textarea name="alamat" class="form-control" id=""><?= $cabang->alamat; ?></textarea>
															</div>
														</div>
														<div class="modal-footer">
															<button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
															<button type="submit" class="btn btn-primary">Simpan</button>
														</div>
													</form>
												</div>
											</div>
										</div>
										<!-- end modal -->
										
										<a href="<?= site_url('admin/cabang/delete/'.$cabang->id); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus data ini?');"><i class="fa fa-trash"></i> Hapus</a>
										<!-- delete with param id -->


									</td>
								</tr>
								<?php $no++; ?>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>



