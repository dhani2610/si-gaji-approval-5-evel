<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header with-border">
				<h3 class="card-title">Data Tunjangan</h3>
                <!-- nutton back -->
                <div class="text-right">

                    <a href="<?= base_url('admin/tunjangan/show/'.$tunjangan->periode) ?>" class="btn btn-sm btn-warning">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <td>Nama</td>
                                <td>: &nbsp; <?= $tunjangan->first_name .' '. $tunjangan->last_name ?></td>
                            </tr>
                            <tr>
                                <td>NIP</td>
                                <td>: &nbsp; <?= $tunjangan->username ?></td>
                            </tr>
                            <tr>
                                <td>Jabatan</td>
                                <td>: &nbsp; <?= $tunjangan->jabatan ?></td>
                            </tr>
                            <tr>
                                <td>Tunjangan</td>
                                <td>: &nbsp; <?= $periode->periode ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Hasil Penilaian</h4><br><hr>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th rowspan="8">Hasil Kerja</th>
                            <th rowspan="4">Kualitas</th>
                            <td>Output pekerjaan memberikan kepuasan kepada pemangku kepentingan internal dan eksternal</td>
                            <td><?= isset($penilaian) ? $penilaian->kualitas_a : 0 ?></td>
                        </tr>
                        <tr>
                            <td>Kesesuaian pekerjaan dengan jabatan</td>
                            <td><?= isset($penilaian) ? $penilaian->kualitas_b : 0 ?></td>
                        </tr>
                        <tr>
                            <td>Output pekerjaan inovatif ( sesuai perkembangan terbaru)</td>
                            <td><?= isset($penilaian) ? $penilaian->kualitas_c : 0 ?></td>

                        </tr>
                        <tr>
                            <td>Output pekerjaan  sesuai dengan SKP</td>
                            <td><?= isset($penilaian) ? $penilaian->kualitas_d : 0 ?></td>

                        </tr>
                        <tr>
                            <th rowspan="2">Ketepatan</th>
                            <td>Output pekerjaan  sesuai dengan Permintaan</td>
                            <td><?= isset($penilaian) ? $penilaian->ketepatan_a : 0 ?></td>
                        </tr>
                        <tr>
                            <td>Menyelesaikan pekerjaan sesuai dengan Standard Operating Procedure (SOP) atau Standar Operasional dan Tata Kerja (SOTK) instansi</td>
                            <td><?= isset($penilaian) ? $penilaian->ketepatan_b : 0 ?></td>
                        </tr>
                        <tr>
                            <th rowspan="2">Kuantitas</th>
                            <td>Menyelesaikan pekerjaan sesuai dengan jumlah yang diminta</td>
                            <td><?= isset($penilaian) ? $penilaian->kuantitas_a : 0 ?></td>
                        </tr>
                        <tr>
                            <td>Menyelesaikan seluruh pekerjaan secara efisien ( tepat waktu, tepat sumber daya, tepat pembiayaan)</td>
                            <td><?= isset($penilaian) ? $penilaian->kuantitas_b : 0 ?></td>
                        </tr>

                        <tr>
                            <th rowspan="5">Perilaku</th>
                            <th>Orientasi Pelayanan</th>
                            <td>sikap dan prilaku dalam memberikan pelayanan</td>
                            <td><?= isset($penilaian) ? $penilaian->pelayanan : 0 ?></td>
                        </tr>

                        <tr>
                            <th>Integritas</th>
                            <td>Bertindak sesuai nilai, norma dan etika organisasi</td>
                            <td><?= isset($penilaian) ? $penilaian->integritas : 0 ?></td>
                        </tr>
                        <tr>
                            <th>Komitmen</th>
                            <td>kemauan dan kemampuan untuk menyelaraskan sikap dan tindakan untuk tujuan organisasi dengan mengutamakan kepentingan kedinasan dari pada kepentingan diri sendiri, seseorang dan / atau golongan</td>
                            <td><?= isset($penilaian) ? $penilaian->komitmen : 0 ?></td>
                        </tr>
                        <tr>
                            <th>Disiplin</th>
                            <td>Kesanggupan untuk menaati kewajiban dan menghindari larangan yang ditentukan dalam peraturan perundang-undangan dan / atau peraturan kedinasan</td>
                            <td><?= isset($penilaian) ? $penilaian->disiplin : 0 ?></td>
                        </tr>
                        <tr>
                            <th>Kerjasama</th>
                            <td>kemauan dan kemampuan untuk bekerjasama dengan rekan kerja, atasan, bawahan dalam unit kerjanya serta instansi lain.</td>
                            <td><?= isset($penilaian) ? $penilaian->kerjasama : 0 ?></td>
                        </tr>
                        <tr>
                            <th colspan="3">Total Penilaian</th>
                            <td><?= $tunjangan->penilaian ?></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>